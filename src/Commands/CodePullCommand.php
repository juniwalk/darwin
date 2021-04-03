<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CodePullCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Pull repository changes';
	protected static $defaultName = 'code:pull';


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
		$status = new StatusIndicator($output);
		$config = $this->getConfig();

		$this->writeHeader('Updating source code of the application');

		$status->setMessage('Create directory for PHP sessions');
		$status->execute(function($status) use ($config) {
			if (!$sessionDir = $config->getSessionDir()) {
				return $status->setStatus($status::SKIPPED);
			}

			return $this->exec('mkdir', '-p', '-m', '0755', $sessionDir);
		});

		$status->setMessage('Create logging directory');
		$status->execute(function($status) use ($config) {
			if (!$loggingDir = $config->getLoggingDir()) {
				return $status->setStatus($status::SKIPPED);
			}

			return $this->exec('mkdir', '-p', '-m', '0755', $loggingDir);
		});

		$status->setMessage('Create cache directories');
		$status->execute(function($status) use ($config) {
			if (!$cacheDirs = $config->getCacheDirs()) {
				return $status->setStatus($status::SKIPPED);
			}

			$codes = [];

			foreach ($cacheDirs as $dir) {
				$code[] = $this->exec('mkdir', '-p', '-m', '0755', $dir);
			}

			return array_sum($code) === 0
				? Command::SUCCESS
				: Command::FAILURE;
		});

		$output->writeln('');
		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');

		return Command::SUCCESS;
	}
}
