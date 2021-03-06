<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force removal of email logs too');
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

		$finder = (new Finder)->ignoreDotFiles(true)
			->files()->notName('index.*')
			->in($loggingDir);

		if (!$input->getOption('force')) {
			$finder->exclude('mails')->notName('*.eml');
			$finder->exclude('prod');
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
