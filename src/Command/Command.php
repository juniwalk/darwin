<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    private $input;
    private $output;


    /**
     * Dialog to confirm some action.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    public function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }


    /**
     * Dialog to confirm some action.
     *
     * @param  int  $steps  Maximum steps
     * @return ProgressBar
     */
    public function getProgressBar($steps = 0)
    {
        // Prepare task progress bar
        $bar = new ProgressBar($this->output, $steps);
        $bar->setFormat(PHP_EOL.' %current%/%max% [%bar%] %percent:3s%% %memory:6s%'.PHP_EOL.' %message%');
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
    public function confirm($message, $default = true)
    {
        // Build confirmation question with the given message
        $question = new ConfirmationQuestion($message.PHP_EOL, $default);

        // Ask user for confirmation and then return the outcome user has decided
        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
