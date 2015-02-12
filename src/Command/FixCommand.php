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

class FixCommand extends Command
{
    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('fix');
        $this->setDescription('Fix permissions of the files and dirs');

        // Define arguments and options of this command
        $this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project');
        $this->addOption('owner', 'o', InputOption::VALUE_OPTIONAL, 'Define owner for files');
    }


    /**
     * Command's entry point
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Gather arguments and options of this command
        $dir = $input->getArgument('dir') ?: getcwd();
        $owner = $input->getOption('owner') ?: 'www-data';


        $output->writeln("\$ darwin fix {$dir} --owner={$owner}");
    }
}
