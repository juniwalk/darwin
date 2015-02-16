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
        // Set input/output streams into instance
        $this->setInputOutput($input, $output);

        // Gather arguments and options of this command
        $dir = $input->getArgument('dir');
        $force = $input->getOption('force');

        // Output which directory we are trying to fix right now
        $output->writeln(PHP_EOL.'<info>We will fix permissions and set owner to <comment>'.$owner.'</comment> for directory:</info>');
        $output->writeln('<comment>'.$dir.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return null;
        }

        // If this is not server directory and fix is not forced
        if (!preg_match('/^\/(srv)/i', $dir) && !$force) {
            throw new ErrorException('You are not in http server directory.');
        }

        // No such directory
        if (!is_dir($dir)) {
            throw new ErrorException('Directory does not exist.');
        }

        // Load config and search for garbage
    }
}
