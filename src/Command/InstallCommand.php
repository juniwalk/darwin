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

class InstallCommand extends Command
{
    /**
     * Path to default binary.
     *
     * @var string
     */
    const PATH = '/usr/local/bin';

    /**
     * Default file permissions.
     *
     * @var int
     */
    const PERM = 0744;


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install Darwin into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('path', InputArgument::OPTIONAL, 'Override binary path', static::PATH);
        $this->addArgument('perm', InputArgument::OPTIONAL, 'Override file permissions', static::PERM);
    }


    /**
     * Command's entry point.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \RuntimeException('This is not implemented yet');
    }
}
