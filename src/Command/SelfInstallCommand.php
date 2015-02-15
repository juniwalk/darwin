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

class SelfInstallCommand extends Command
{
    /**
     * Path to default binary.
     *
     * @var string
     */
    const PATH = '/usr/local/bin';


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('self:install');
        $this->setDescription('Install into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('path', InputArgument::OPTIONAL, 'Select custom path', static::PATH);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force installation, overwrite existing files');
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
        $name = $this->getApplication()->getName();

        // Gather arguments and options of this command
        $path = $input->getArgument('path');
        $path = $path.'/'.strtolower($name);
        $force = $input->getOption('force');

        // Output which directory we are trying to fix right now
        $output->writeln(PHP_EOL.'<info>'.$name.' will be installed into:</info>');
        $output->writeln('<comment>'.$path.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return null;
        }

        // Get the path to the application executable
        $link = realpath(__DIR__.'/../../bin/'.strtolower($name));

        // If destination file exists and
        // installation was not forced
        if ($this->linkExists($path, $force)) {
            throw new ErrorException($name.' is already installed.');
        }

        // Create link to app executable
        if (!symlink($link, $path)) {
            throw new ErrorException('Failed to install '.$name.'.');
        }

        $output->writeln(PHP_EOL.'<info>'.$name.' has been successfuly installed.</info>');
    }


    /**
     * Does the link already exist?
     *
     * @param  string  $path   Path to link
     * @param  bool    $force  Force the overwrite?
     * @return bool
     */
    protected function linkExists($path, $force = false)
    {
        // If there is no such file
        if (!file_exists($path)) {
            return false;
        }

        // Link does already exist
        if ($force == false) {
            return true;
        }

        // Remove existing link
        return !unlink($path);
    }
}
