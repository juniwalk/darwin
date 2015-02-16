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
use Nette\Utils\Finder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCommand extends Command
{
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

        // Chech the input params
        if (!$this->isReady()) {
            return null;
        }

        // Get the files and count them
        $files = $this->getFiles();
        $count = iterator_count($files);

        // If there are no files
        if (empty($count)) {
            throw new ErrorException('No files and/or directories found to fix.');
        }

        // Get new progress bar instance
        $bar = $this->getProgressBar($count);

        // Iterate over found files and fix them
        foreach ($files as $path => $file) {
            // Display path to file in the message
            // and advance progress bar to ne unit
            $bar->setMessage($path);
            $bar->advance();

            // Set appropriate mode to the file / dir
            // and change owner to web server user
            $this->setMode($path, $file->isFile());
            $this->setOwner($path, $owner);

            // If this is one of the files to be locked from access
            if (preg_match(static::LOCKED_FILES, $file->getFilename())) {
                // Change owner to root user
                $this->setOwner($path, 'root');
            }
        }

        // Task has finished
        $bar->setMessage('<info>All is fixed now.</info>');
        $bar->finish();

        // Move pointer to new line
        $this->write(PHP_EOL);
    }


    /**
     * Check wether command is ready.
     *
     * @return bool
     * @throws ErrorException
     */
    protected function isReady()
    {
        // Output which directory we are trying to fix right now
        $this->write(PHP_EOL.'<info>We will fix permissions and set owner to <comment>'.$this->owner.'</comment> for directory:</info>');
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


    /**
     * Get the list of found fles and directories.
     *
     * @return IteratorAggregate
     */
    protected function getFiles()
    {
        // Return list of files and directories to fix
        return Finder::find('*')->from($this->dir);
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
     * @param  string      $path   Path to file or dir
     * @param  int|string  $owner  New owner name/id
     * @return bool
     */
    protected function setOwner($path, $owner)
    {
        // Set selected owner
        return chown($path, $owner);
    }
}
