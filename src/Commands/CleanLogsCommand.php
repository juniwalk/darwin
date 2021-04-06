<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class CleanLogsCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Remove all error logs';
	protected static $defaultName = 'clean:logs';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$config = $this->getConfig();

		if (!$loggingDir = $config->getLoggingDir()) {
			return Command::SUCCESS;
		}

		$finder = (new Finder)->ignoreDotFiles(false)
			->files()->notName('index.*')
			->in($loggingDir);

		if (!$finder->hasResults()) {
			return Command::SUCCESS;
		}

		$progress = new ProgressBar($output, false);
		$progress->execute($finder, function($progress, $file) use ($loggingDir) {
			$progress->setMessage(str_replace($loggingDir, '.', $file->getPathname()));
			$progress->advance();

			unlink($file->getPathname());
		});

		return Command::SUCCESS;
	}
}
