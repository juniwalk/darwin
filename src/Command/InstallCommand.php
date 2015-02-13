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
     * Default file permissions.
     *
     * @var int
     */
    const MODE = 0744;


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install Darwin into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('path', InputArgument::OPTIONAL, 'Override binary path', static::PATH);
        $this->addArgument('mode', InputArgument::OPTIONAL, 'Override file permissions', static::MODE);
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
        $mode = $input->getArgument('mode');

        // Output which directory we are trying to fix right now
        $output->writeln(PHP_EOL.'<info>Darwin will be installed into:</info>');
        $output->writeln('<comment>'.$path.'</comment>'.PHP_EOL);

        // If the user does not wish to continue
        if (!$this->confirm('<info>Is this correct path <comment>[Y,n]</comment>?</info>')) {
            return null;
        }

        // Get the path to the darwin executable
        $link = $this->getHome().'/bin/darwin';

        //  Create symbolic link to Darwin
        if (!symlink($path, $link)) {
            throw new \RuntimeException('Failed to write the phar.');
        }

        // Set the file mode
        chmod($path, $mode);

        $output->writeln(PHP_EOL.'<info>Darwin has been successfuly installed.</info>');
    }


    /**
     * Path to home directory.
     *
     * @return string
     */
    protected function getHome()
    {
        // Get the path to /vendor directory
        return realpath(__DIR__.'/../../../..');
    }
}
