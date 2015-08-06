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

class InstallCommand extends Command
{
    /**
     * Path checking.
     * @var string
     */
    const CONTAINMENT = '/\/(bin)$/i';

    /**
     * Installation directory.
     * @var string
     */
    protected $dir = '/usr/local/bin';

    /**
     * Force the cleanup?
     * @var bool
     */
    protected $force;


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Custom installation path', $this->dir);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force installation over existing files');
    }


    /**
     * Command's entry point.
     * @param  InputInterface   $input   Input stream
     * @param  OutputInterface  $output  Output stream
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepare($input, $output);

        // Get application name and build install path
        $home = $this->getApplication()->getHome();
        $name = $this->getApplication()->getName();
        $path = $this->dir.'/'.strtolower($name);

        if (!$this->isReady($this->dir, $this->force)) {
            return null;
        }

        // Get the path to the application executable
        $link = realpath($home.'/bin/'.strtolower($name));

        if ($this->linkExists($path)) {
            throw new ErrorException($name.' is already installed.');
        }

        if (!symlink($link, $path)) {
            throw new ErrorException('Failed to install '.$name.'.');
        }

        $this->write(PHP_EOL.'<info>'.$name.' has been successfuly installed.</info>');
    }


    /**
     * Does the link already exist?
     * @param  string  $path  Path to link
     * @return bool
     */
    protected function linkExists($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        if ($this->force == false) {
            return true;
        }

        return !unlink($path);
    }
}
