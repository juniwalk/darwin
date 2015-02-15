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
        // Set input/output streams into instance
        $this->setInputOutput($input, $output);

        // Gather arguments and options of this command
        $dir = $input->getArgument('dir');
        $owner = $input->getOption('owner');
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

        // Search for files and dirs in the folder
        $search = Finder::find('*')->from($dir);
        $sizeof = iterator_count($search);

        // If there are no contents
        if (empty($sizeof)) {
            throw new ErrorException('No files and/or directories found to fix.');
        }

        // Get new progress bar instance
        $bar = $this->getProgressBar($sizeof);

        // Search for each file and dir in current project and set privileges
        foreach ($search as $path => $file) {
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
        $output->writeln(PHP_EOL);
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
