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
use Symfony\Component\Finder\Finder;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * Input stream instance.
     * @var InputInterface
     */
    private $input;

    /**
     * Output stream instance.
     * @var OutputInterface
     */
    private $output;


    /**
     * Dialog to confirm some action.
     * @param  InputInterface   $input   Input stream
     * @param  OutputInterface  $output  Output stream
     * @return static
     */
    protected function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this;
    }


    /**
     * Write message/s to console output.
     * @param  string  $message  Message to output
     * @return static
     */
    protected function write($message)
    {
        $this->output->writeln($message);
        return $this;
    }


    /**
     * Prepare command arguments.
     * @param  InputInterface   $input   Input stream
     * @param  OutputInterface  $output  Output stream
     * @return bool
     */
    protected function prepare(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        foreach ($input->getOptions() as $key => $value) {
            $this->$key = $value;
        }

        foreach ($input->getArguments() as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }


    /**
     * Check wether command is ready.
     * @param  string  $dir    Containment directory
     * @param  bool    $force  Force the task outside src dir
     * @return bool
     * @throws ErrorException
     */
    protected function isReady($dir, $force)
    {
        $this->write('<info>We will '.strtolower($this->getDescription()).' in directory:</info>');
        $this->write('<comment>'.$dir.'</comment>'.PHP_EOL);

        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return false;
        }

        if (!preg_match(static::CONTAINMENT, $dir) && !$force) {
            throw new ErrorException('Working outside containment directory, use --force flag to override.');
        }

        if (!is_dir($dir)) {
            throw new ErrorException('Directory does not exist.');
        }

        return true;
    }


    /**
     * Dialog to confirm some action.
     * @param  string  $message  Dialog message
     * @param  bool    $default  Default outcome
     * @return bool
     */
    protected function confirm($message, $default = true)
    {
        $question = new ConfirmationQuestion($message, $default);

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }


    /**
     * Iterate over found files and execute method.
     * @param  mixed        $finder  Finder instance
     * @param  callable     $method  Callback method
     * @param  string|null  $root    Root directory
     * @return static
     */
    protected function iterate($finder, callable $method, $root = null)
    {
        $this->write(PHP_EOL.PHP_EOL);

        $bar = $this->getProgressBar(count($finder));
        $msg = '<comment>Task has finished.</comment>';

        foreach ($finder as $path) {
            // Display path to file in the message
            // and advance progress bar to next point
            $bar->setMessage(str_replace($root, '~', $path));
            $bar->advance();

            if (!call_user_func($method, $path)) {
                $msg = '<error>Task has failed.</error>';
                break;
            }
        }

        $bar->setMessage($msg);
        $bar->finish();

        return $this->write(PHP_EOL);
    }


    /**
     * Run analyzis and store found items in property.
     * @param  Finder  $finder  Finder instance
     * @param  array   $items   Output holder
     * @return static
     */
    protected function analyze(Finder $finder, array &$items = null)
    {
        $this->write(PHP_EOL);

        $bar = $this->getProgressBar(0, 50, 1);
        $bar->setMessage('Analyzing directory...');
        $bar->setFormat(implode(PHP_EOL, array(
            ' <info>%message%</info>',
            ' <comment>%current%</comment> items found',
        )));

        foreach ($finder as $item) {
            $items[] = $item;
            $bar->advance();
        }

        $bar->finish();

        if (!empty($items)) {
            $items = array_reverse($items);
        }

        return $this->write(PHP_EOL);
    }


    /**
     * Dialog to confirm some action.
     * @param  int  $steps      Maximum steps
     * @param  int  $width      Width in characters
     * @param  int  $frequency  Stebs before redraw
     * @return ProgressBar
     */
    protected function getProgressBar($steps = 0, $width = 50, $frequency = 50)
    {
        // Prepare task progress bar
        $bar = new ProgressBar($this->output, $steps);
        $bar->setBarCharacter('<comment>-</comment>');
        $bar->setProgressCharacter('<comment>></comment>');
        $bar->setEmptyBarCharacter(' ');
        $bar->setRedrawFrequency($frequency);
        $bar->setBarWidth($width);

        // Set format of the bar
        $bar->setFormat(implode(PHP_EOL, array(
            ' %current%/%max% items processed in %elapsed%',
            ' [%bar%] %percent:3s%%',
            ' %message%',
        )));

        return $bar;
    }
}
