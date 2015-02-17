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
     * @param  string  $dir    Containment directory
     * @param  bool    $force  Force the task outside src dir
     * @return bool
     * @throws ErrorException
     */
    protected function isReady($dir, $force)
    {
        // Output which directory we are trying to fix right now
        $this->write('<info>We will '.strtolower($this->getDescription()).' in directory:</info>');
        $this->write('<comment>'.$dir.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return false;
        }

        // If this is not server directory and fix is not forced
        if (!preg_match(static::CONTAINMENT, $dir) && !$force) {
            throw new ErrorException('Working outside containment directory, use --force flag to override.');
        }

        // No such directory
        if (!is_dir($dir)) {
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
        // Define bar format
        $format = array(
            ' %current%/%max% in %elapsed:6s%',
            ' [%bar%] %percent:3s%%',
            ' %message%',
        );

        // Prepare task progress bar
        $bar = new ProgressBar($this->output, $steps);
        $bar->setFormat(implode(PHP_EOL, $format));
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
     * Iterate over found files and execute method.
     *
     * @param  \Traversable  $files   Files to iterate over
     * @param  callable      $method  Callback method
     * @param  string|null   $dir     Root directory
     */
    protected function iterate(\Traversable $files, callable $method, $dir = null)
    {
        // New line character
        $this->write(PHP_EOL);

        // Get new progress bar instance with count of files
        $bar = $this->getProgressBar(iterator_count($files));

        // Iterate over found files and fix them
        foreach ($files as $path => $file) {
            // Display path to file in the message
            // and advance progress bar to next point
            $bar->setMessage(strtr($path, $dir, null));
            $bar->advance();

            // Process current path
            $method($path, $file);
        }

        // The progress has finished
        $bar->setMessage('<comment>Task finished.</comment>');
        $bar->finish();

        // New line character
        $this->write(PHP_EOL);
    }
}
