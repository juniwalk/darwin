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
        $this->setDescription('Install into /bin directory');

        // Define arguments and options of this command with default values
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Custom installation path', static::PATH);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the collection');
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
        $this->prepare($input, $output);

        // Get application name and build install path
        $name = $this->getApplication()->getName();
        $path = $this>dir.'/'.strtolower($name);

        // Perform check on given directory path
        if (!$this->isReady($name, $this>dir, $this->force)) {
            return null;
        }

        // Get the path to the application executable
        $link = realpath(__DIR__.'/../../bin/'.strtolower($name));

        // If destination symlink exists
        if ($this->linkExists($path)) {
            throw new ErrorException($name.' is already installed.');
        }

        // Create link to app executable
        if (!symlink($link, $path)) {
            throw new ErrorException('Failed to install '.$name.'.');
        }

        $this->write(PHP_EOL.'<info>'.$name.' has been successfuly installed.</info>');
    }


    /**
     * Does the link already exist?
     *
     * @param  string  $path   Path to link
     * @param  bool    $force  Force the overwrite?
     * @return bool
     */
    protected function linkExists($path)
    {
        // If there is no such file
        if (!file_exists($path)) {
            return false;
        }

        // Link does already exist
        if ($this->force == false) {
            return true;
        }

        // Remove existing link
        return !unlink($path);
    }


    /**
     * Check wether command is ready.
     *
     * @param  string  $name   Application name
     * @param  string  $dir    Installation directory
     * @param  bool    $force  Force the task outside src dir
     * @return bool
     * @throws ErrorException
     */
    protected function isReady($name, $dir, $force)
    {
        // Output which directory we are trying to fix right now
        $this->write(PHP_EOL.'<info>We will install '.$name.' into directory:</info>');
        $this->write('<comment>'.$dir.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return false;
        }

        // If endpoint directory is not /bin directory
        if (!preg_match('/\/(bin)$/i', $dir) && !$force) {
            throw new ErrorException('Endpoint directory is not ~/bin, use --force flag to override.');
        }

        // No such directory
        if (!is_dir($dir)) {
            throw new ErrorException('Directory does not exist.');
        }

        return true;
    }
}
