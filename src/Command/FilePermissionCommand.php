<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\Tools\ProgressBar;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class FilePermissionCommand extends AbstractCommand
{
	/** @var string */
	private $folder;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Fix file permissions in given directory');
		$this->setName('file:permission')->setAliases(['fix']);

		$this->addArgument('folder', InputArgument::OPTIONAL, 'Working directory for permission fixer');
		$this->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Name of configuration file', 'default');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->getHelper('config')->load($input->getOption('config'));
		$this->folder = $input->getArgument('folder') ?: getcwd();

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$folder = $this->folder !== getcwd()
			? $this->folder
			: 'current';

		$this->addQuestion(function($cli) use ($folder) {
			return $cli->confirm('Continue with <info>'.$folder.'</> directory?');
		});

		parent::interact($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$config = $this->getHelper('config');
		$config->loadCurrent($this->folder);

		$folder = new SplFileInfo($this->folder);
		$finder = (new Finder)->ignoreDotFiles(false)
			->exclude($config->getExcludeFolders())
			->in($folder->getPathname());

		foreach ($config->getRules() as $rule) {
			$rule->apply($folder);
		}

		$progress = new ProgressBar($output, false);
		$progress->execute($finder, function($progress, $file) use ($config) {
			$progress->setMessage(str_replace($this->folder, '.', $file->getPathname()));
			$progress->advance();

			foreach ($config->getRules() as $rule) {
				$rule->apply($file);
			}
		});

		return Command::SUCCESS;
	}
}
