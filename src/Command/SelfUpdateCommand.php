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
use Symfony\Component\Process\Process;

class SelfUpdateCommand extends Command
{
    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('self:update');
        $this->setDescription('Search for updates');

        $this->addOption('optimize', 'o', InputOption::VALUE_NONE, 'Optimize generated autoloader');
    }


    /**
     * Command's entry point.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the name of the application for outputing
        $name = $this->getApplication()->getName();
        $pkgn = $this->getApplication()->getPackage()->name;

        // Prepare command to run update on package
        $cmd = 'composer global update '.$pkgn;

        // If the autoloader should be optimized
        if ($input->getOption('optimize')) {
            // Add option into the command
            $cmd .= ' --optimize-autoloader';
        }

        // Inform that we are using composer for updates
        $output->writeln(PHP_EOL.'<info>Executing update using composer.</info>');
        $output->writeln(' <comment>$ '.$cmd.'</comment>'.PHP_EOL);

        // Create new process and execute it
        $process = new Process($cmd);
        $process->run(function ($type, $data) use ($output)
        {
            // Output any changes in real-time
            $output->write($data);
        });

        // Inform that the update has finished
        $output->writeln(PHP_EOL.'<info>Update finished.</info>'.PHP_EOL);
    }
}
