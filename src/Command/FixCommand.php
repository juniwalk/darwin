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
use Symfony\Component\Finder\Finder;

class FixCommand extends Command
{
    /**
     * Path checking.
     *
     * @var string
     */
    const CONTAINMENT = '/^\/(srv)/i';

    /**
     * Define names of files that should be locked out from Apache user.
     *
     * @var string
     */
    const LOCKED_FILES = '/(index|config|htaccess|composer)/is';

    /**
     * Path to the project.
     *
     * @var string
     */
    protected $dir;

    /**
     * Owner name for unlocked files.
     *
     * @var string
     */
    protected $owner;

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
        $this->setName('fix');
        $this->setDescription('Fix permissions of the files and dirs');

        // Define arguments and options of this command with default values
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
        $this->addOption('owner', 'o', InputOption::VALUE_REQUIRED, 'Define owner for files', 'www-data');
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
        $this->prepare($input, $output);

        // Perform check on given directory path
        if (!$this->isReady($this->dir, $this->force)) {
            return null;
        }

        // Parse files in directory
        $this->iterate(
            (new Finder)->in($this->dir),   // Find files
            [ $this, 'setPermissions' ],    // Execute method
            $this->dir                      // Strip root path
        );
    }


    /**
     * Process file or directory.
     *
     * @param  \SplFileInfo  $file  Information about the file
     * @return bool
     * @throws ErrorException
     */
    public function setPermissions(\SplFileInfo $file)
    {
        // Set appropriate mode to the file / dir
        // and change owner to web server user
        $this->setMode($file, $file->isFile());
        $this->setOwner($file, $this->owner);

        // If this is one of the files to be locked from access
        if ($file->isFile() && preg_match(static::LOCKED_FILES, $file->getFilename())) {
            // Change owner to root user
            $this->setOwner($file, 'root');
        }

        return true;
    }


    /**
     * Set new permissions mode.
     *
     * @param  string  $path    Path to file or dir
     * @param  bool    $isFile  Is this file?
     * @return bool
     */
    protected function setMode($path, $isFile = true)
    {
        // Directory mode
        $mode = 0755;

        // If this is file
        if ($isFile == true) {
            // Use file mode
            $mode = 0644;
        }

        // Set appropriate mode
        return chmod($path, $mode);
    }


    /**
     * Set new permissions mode.
     *
     * @param  string  $path   Path to file or dir
     * @param  string  $owner  New owner name/id
     * @return bool
     */
    protected function setOwner($path, $owner)
    {
        // Set selected owner
        return chown($path, $owner);
    }
}
