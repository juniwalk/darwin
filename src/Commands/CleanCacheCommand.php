<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanCacheCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Clear application cache and fix permissions';
	protected static $defaultName = 'clean:cache';

	/** @var bool */
	private $skipFix;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['clean']);

		$this->addOption('skip-fix', 's', InputOption::VALUE_NONE, 'Skip fixing permissions');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->skipFix = $input->getOption('skip-fix');

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$status = new StatusIndicator($output);
		$config = $this->getConfig();

		if (!$cacheDirs = $config->getCacheDirs()) {
			return Command::SUCCESS;
		}

		$this->writeHeader('Clearing out application cache');

		foreach ($cacheDirs as $dir) {
			$status->setMessage('Clear directory: <comment>'.$dir.'</>');
			$status->execute(function($status) use ($dir) {
				return $this->exec('rm', '-rf', $dir.'*');
			});
		}

		if ($this->skipFix === true) {
			return Command::SUCCESS;
		}

		$params = new ArgvInput;
		$params->setInteractive(false);
		$fixCommand = $this->findCommand('make:close');
		$fixCommand->run($params, $output);

		return Command::SUCCESS;
	}
}
