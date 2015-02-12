<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Nette\Utils\Finder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCommand extends Command
{
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
    }


    /**
     * Command's entry point
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Set input/output streams into instance
        $this->setInputOutput($input, $output);

        // Gather arguments and options of this command
        $dir = $input->getArgument('dir');
        $owner = $input->getOption('owner');

        // Output which directory we are trying to fix right now
        $output->writeln(PHP_EOL.'<info>We will fix permissions and set owner to <comment>'.$owner.'</comment> for directory:</info>');
        $output->writeln('<comment>'.$dir.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[y,N]</comment>?</info>', false)) {
            return null;
        }

        // Search for files and dirs in the folder
        $search = Finder::find('*')->from($dir);
        $sizeof = iterator_count($search);

        // If there are no contents
        if (empty($sizeof)) {
            return null;
        }

        // Get new progress bar instance
        $bar = $this->getProgressBar($sizeof);

        // Search for each file and dir in current project and set privileges
        foreach ($search as $path => $file) {
            // Display path to file in the message
            $bar->setMessage($path);

            // If this is not one of locked files
            if (!preg_match(LOCKED_FILES, $file->getFilename())) {
                // Change owner to Apache user
                chown($path, $owner);
            }

            // If this is a directory
            if ($file->isDir()) {
                // Set appropriate mode
                chmod($path, 0755);
            }

            // If this is a file
            if ($file->isFile()) {
                // Set appropriate mode
                chmod($path, 0644);
            }

            // Advance progress bar
            $bar->advance();
        }

        // Task has finished
        $bar->setMessage('<info>Okay, all fixed now.</info>');
        $bar->finish();
    }
}
