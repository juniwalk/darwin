<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

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
	protected function configure()
	{
		$this->setDescription('Fix permissions of the files and dirs');
		$this->setName('fix');

		// Define arguments and options of this command with default values
		$this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
		$this->addOption('owner', 'o', InputOption::VALUE_REQUIRED, 'Define owner for files', 'www-data');
		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the fix for any directory');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @throws TerminateException
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		$dir = $input->getArgument('dir');

		if (!is_dir($dir)) {
			throw new \LogicException('Invalid directory: '.$dir);
		}

		$output->writeln('<info>Changin working directory to: <comment>'.$dir.'</comment></info>');
		$question = new ConfirmationQuestion('Continue with this action <comment>[Y,n]</comment>? ', true);
		$helper = $this->getHelper('question');

		if (!$helper->ask($input, $output, $question)) {
			throw new TerminateException;
		}
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$dir = $input->getArgument('dir');

		$finder = (new Finder)->in($dir)->exclude('vendor')->exclude('bin');

		$bar = new ProgressBar($output, sizeof($finder));
		$bar->start();

		foreach ($finder as $file) {
			$bar->setMessage(str_replace($dir, '.', $file));
			$bar->advance();

			if (!$this->processPath($file)) {
				break;
			}
		}

		$bar->finish();

		return 1;
	}


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
