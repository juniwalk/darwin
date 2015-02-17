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

class GcCommand extends Command
{
    /**
     * Path checking.
     *
     * @var string
     */
    const CONTAINMENT = '/^\/(srv)/i';

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
     * Define list of vcs dirs.
     *
     * @var array
     */
    protected $vcs = [
        '.svn', '_svn', 'CVS', '_darcs', '.arch-params',
        '.monotone', '.bzr', '.git', '.hg', 'examples',
        'tests', 'docs',
    ];


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('gc');
        $this->setDescription('Garbage collect your project');

        // Define arguments and options of this command with default values
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
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

        // Perform check on given directory path
        if (!$this->isReady($this->dir, $this->force)) {
            return null;
        }

        // Create Finder to search for directories in given dir
        $finder = (new Finder)->in($this->dir)
            ->ignoreDotFiles(false) // And include dot files
            ->ignoreVCS(false);     // Include VCS directories

        // Iterate over names of vcs dirs
        foreach ($this->vcs as $dir) {
            // Set vcs dir filter
            $finder->path($dir);
        }

        // Analyue given directory
        $this->analyze($finder, $items);

        // If there are no items
        if (empty($items)) {
            return null;
        }

        // If the user does not wish to continue
        if (!$this->confirm('<info>Proceed with deletion <comment>[Y,n]</comment>?</info>')) {
            return null;
        }

        // Send the items for deletion
        $this->iterate($items, [$this, 'delete'], $this->dir);
    }


    /**
     * Process file or directory.
     *
     * @param  \SplFileInfo  $file  Information about the file
     * @return bool
     * @throws ErrorException
     */
    public function delete(\SplFileInfo $file)
    {
        // Default function
        $function = 'rmdir';

        // If this is a file
        if ($file->isFile()) {
            // Switch function
            $function = 'unlink';
        }

        // Remove the file from disk
        $report = error_reporting(0);
        $status = $function($file);
        $report = error_reporting($report);

        if ($status == false) {
            $error = error_get_last();
            throw new ErrorException($error['message']);
        }

        return true;
    }
}
