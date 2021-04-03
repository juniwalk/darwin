<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class CodePermissionCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Fix file permissions in given directory';
	protected static $defaultName = 'code:permission';

	/** @var string */
	private $folder;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['fix']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->folder = getcwd();

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->addQuestion(function($cli) {
			return $cli->confirm('Continue with <info>current</> directory?');
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
		$config = $this->getConfig();

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
