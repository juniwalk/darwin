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

class InstallCommand extends Command
{
    /**
     * Path to default binary.
     *
     * @var string
     */
    const PATH = '/usr/local/bin/darwin';


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install Darwin into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('path', InputArgument::OPTIONAL, 'Select custom path', static::PATH);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force installation, overwrite existing files');
    }


    /**
     * Command's entry point.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Set input/output streams into instance
        $this->setInputOutput($input, $output);

        // Gather arguments and options of this command
        $path = $input->getArgument('path');
        $force = $input->getOption('force');

        // Output which directory we are trying to fix right now
        $output->writeln(PHP_EOL.'<info>Darwin will be installed into:</info>');
        $output->writeln('<comment>'.$path.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return null;
        }

        // Get the path to the darwin executable
        $link = realpath(__DIR__.'/../../bin/darwin');

        // If destination file exists and
        // installation was not forced
        if (!$force && file_exists($path)) {
            throw new \RuntimeException('Darwin is already installed.');
        }

        // Get process helper instance
        $process = $this->getHelper('process');

        //  Just create symbolic link to Darwin executable file
        if (!$process->run($output, 'ln -s '.$link.' '.$path)) {
            throw new \RuntimeException('Failed to install Darwin.');
        }

        $output->writeln(PHP_EOL.'<info>Darwin has been successfuly installed.</info>');
    }
}
