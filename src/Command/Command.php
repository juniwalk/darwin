<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Symfony\Component\Console\Input\InputInterface;

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
     * @param  string  $message  Dialog message
     * @param  bool    $default  Default outcome
     * @return bool
     */
    public function getDialog()
    {
        // Create DialogHelper instance
        $dialog = new DialogHelper;
        $dialog->setInput($this->input);

        return $dialog;
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
        // Ask user for confirmation and then return the outcome user has decided
        return $this->getDialog()->askConfirmation($this->output, $message.PHP_EOL, $default);
    }
}
