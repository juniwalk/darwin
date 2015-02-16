<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use ErrorException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * Input stream.
     *
     * @var InputInterface
     */
    private $input;

    /**
     * Output stream.
     *
     * @var OutputInterface
     */
    private $output;


    /**
     * Called when command finishes.
     */
    public function __destruct()
    {
        // Move pointer to new line
        $this->write(PHP_EOL);
    }


    /**
     * Dialog to confirm some action.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }


    /**
     * Prepare command arguments.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function prepare(InputInterface $input, OutputInterface $output)
    {
        // Assign Input/Output streams
        $this->input = $input;
        $this->output = $output;

        // Iterate over the list of provided options
        foreach ($input->getOptions() as $key => $value) {
            // Assign value to property
            $this->$key = $value;
        }

        // Iterate over the list of provided arguments
        foreach ($input->getArguments() as $key => $value) {
            // Assign value to property
            $this->$key = $value;
        }

        return true;
    }


    /**
     * Check wether command is ready.
     *
     * @return bool
     * @throws ErrorException
     */
    protected function isReady()
    {
        // Output which directory we are trying to fix right now
        $this->write(PHP_EOL.'<info>We will '.strtolower($this->getDescription()).' in directory:</info>');
        $this->write('<comment>'.$this->dir.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return false;
        }

        // If this is not server directory and fix is not forced
        if (!preg_match('/^\/(srv)/i', $this->dir) && !$this->force) {
            throw new ErrorException('Working outside srv directory, use --force flag to override.');
        }

        // No such directory
        if (!is_dir($this->dir)) {
            throw new ErrorException('Directory does not exist.');
        }

        return true;
    }


    /**
     * Write message/s to console output.
     */
    protected function write($message)
    {
        $this->output->writeln($message);
    }


    /**
     * Dialog to confirm some action.
     *
     * @param  int  $steps  Maximum steps
     * @return ProgressBar
     */
    protected function getProgressBar($steps = 0)
    {
        // Prepare task progress bar
        $bar = new ProgressBar($this->output, $steps);
        $bar->setFormat(PHP_EOL.' %current%/%max% [%bar%] %percent:3s%% %memory:6s%'.PHP_EOL.' %message%'.PHP_EOL);
        $bar->setBarCharacter('<comment>-</comment>');
        $bar->setProgressCharacter('<comment>></comment>');
        $bar->setEmptyBarCharacter(' ');
        $bar->setRedrawFrequency(50);

        return $bar;
    }


    /**
     * Dialog to confirm some action.
     *
     * @param  string  $message  Dialog message
     * @param  bool    $default  Default outcome
     * @return bool
     */
    protected function confirm($message, $default = true)
    {
        // Build confirmation question with the given message
        $question = new ConfirmationQuestion($message, $default);

        // Ask user for confirmation and then return the outcome user has decided
        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }


    /**
     * Iterate over found filess and execute method.
     *
     * @param  \Traversable  $files   Files to iterate over
     * @param  callable      $method  Callback method
     * @return ProgressBar
     * @throws ErrorException
     */
    protected function iterate(\Traversable $files, callable $method)
    {
        // Get new progress bar instance with count of files
        $bar = $this->getProgressBar(iterator_count($files));

        // Iterate over found files and fix them
        foreach ($files as $path => $file) {
            // Display path to file in the message
            // and advance progress bar to ne unit
            $bar->setMessage($path);
            $bar->advance();

            // Process current path
            if (!$method($path, $file)) {
                throw new ErrorException('Failed to process file or directory.');
            }
        }

        // The progress has finished
        $bar->finish();

        // Return progress bar
        return $bar;
    }
}
