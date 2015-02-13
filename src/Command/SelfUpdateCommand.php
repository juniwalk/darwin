<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('self:update');
        $this->setDescription('Search for updates');

        $this->addOption('optimize', 'o', InputOption::VALUE_NONE, 'Optimize generated autoloader');
    }


    /**
     * Command's entry point.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the name of the application for outputing
        $name = $this->getApplication()->getName();
        $pkgn = $this->getApplication()->getPackage()->name;

        // Prepare command to run update on package
        $cmd = 'composer global update '.$pkgn;

        // If the autoloader should be optimized
        if ($input->getOption('optimize')) {
            // Add option into the command
            $cmd .= ' --optimize-autoloader';
        }

        // Execute given update command in the process helper
        $this->getHelper('process')->run($output, $cmd);
    }
}
