<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class MakeCloseCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Set file permissions as strictly closed';
	protected static $defaultName = 'make:close';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['fix']);

		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force permission fix even on excluded folders');
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
		$folder = new SplFileInfo(WORKING_DIR);
		$config = $this->getConfig();

		$finder = (new Finder)->ignoreDotFiles(false)
			->in($folder->getPathname());

		if (!$input->getOption('force')) {
			$finder->exclude($config->getExcludeFolders());
		}

		foreach ($config->getRules() as $rule) {
			$rule->apply($folder);
		}

		$progress = new ProgressBar($output, false);
		$progress->execute($finder, function($progress, $file) use ($config) {
			$progress->setMessage(str_replace(WORKING_DIR, '.', $file->getPathname()));
			$progress->advance();

			foreach ($config->getRules() as $rule) {
				$rule->apply($file);
			}
		});

		return Command::SUCCESS;
	}
}
