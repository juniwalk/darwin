<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\Exception\InvalidArgumentException;
use JuniWalk\Darwin\Exception\TerminateException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class FixCommand extends \Symfony\Component\Console\Command\Command
{
    /**
	 * Default working directory.
	 * @var string
	 */
    const CONTAINMENT = '/^\/(srv)/i';


	/** @var string */
	private $dir;


	protected function configure()
	{
		$this->setDescription('Fix website permissions in given directory');
		$this->setName('fix');

		// Define arguments and options of this command with default values
		$this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'Bypass container directory');
		$this->addOption('owner', 'o', InputOption::VALUE_REQUIRED, 'Define owner for files', 'www-data');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @throws InvalidArgumentException
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->dir = $dir = $input->getArgument('dir');
		$force = (bool) $input->getOption('force');

		$output->writeln('<info>Changed current directory to <comment>'.$dir.'</comment></info>');

		if (!$dir || !is_dir($dir)) {
			throw new InvalidArgumentException('Unable to fix permissions in given directory');
		}

        if (!$force && !preg_match(static::CONTAINMENT, $dir)) {
            throw new InvalidArgumentException('Directory containment breach, use --force flag to override');
        }
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @throws TerminateException
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		$question = new ConfirmationQuestion('Continue with this directory <comment>[Y,n]</comment>? ', true);

		if (!$this->getHelper('question')->ask($input, $output, $question)) {
			throw new TerminateException;
		}

		$output->writeln(PHP_EOL);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = (new Finder)->in($this->dir)->exclude('vendor')->exclude('bin');

		$bar = new ProgressBar($output, sizeof($finder));
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%%\n %message%");
		$bar->setMessage('<info>Preparing...</info>');
		$bar->start();

		foreach ($finder as $file) {
			$bar->setMessage(str_replace($this->dir, '.', $file));

			if (!$this->processPath($file)) {
				break;
			}

			$bar->advance();
			usleep(250);
		}

		$bar->setMessage('<info>Permissions were fixed</info>');
		$bar->finish();
	}


	/**
	 * @param  SplFileInfo  $file
	 * @return bool
	 */
	private function processPath(\SplFileInfo $file)
	{
		chmod($file, $file->isFile() ? 0644 : 0755);
		chown($file, 'www-data');

		if ($file->isFile() && preg_match('/(index|config|htaccess|composer)/i', $file->getFilename())) {
			chown($file, 'root');
		}

		return true;
	}
}
