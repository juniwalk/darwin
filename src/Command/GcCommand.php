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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GcCommand extends Command
{
    /**
     * Path to the project.
     *
     * @var string
     */
    protected $dir;

    /**
     * Force the cleanup?
     *
     * @var bool
     */
    protected $force;


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('gc');
        $this->setDescription('Garbage collector for your project');

        // Define arguments and options of this command with default values
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the fix for any directory');
    }


    /**
     * Command's entry point.
     *
     * @param  InputInterface   $input   Input stream
     * @param  OutputInterface  $output  Output stream
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Prepare input/output for this command
        if (!$this->prepare($input, $output)) {
            // Handle rror
        }

        // Chech the input params
        if (!$this->isReady()) {
            return null;
        }

        // Load config and search for garbage
        $this->write('Looking for garbage ...'.PHP_EOL);
    }


    /**
     * Check directory.
     *
     * @return bool
     * @throws ErrorException
     */
    protected function isReady()
    {
        // Output which directory we are trying to fix right now
        $this->write(PHP_EOL.'<info>We will look for garbage in this directory:</info>');
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
}
