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

final class MakeLockedCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'LOCK access into website';
	protected static $defaultName = 'make:locked';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['lock']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$status = new StatusIndicator($output);
		$output->writeln('');

		$config = $this->getConfig();
		$lockFile = $config->getLockFile();
		$unlockFile = $config->getUnlockFile();

		$status->setMessage('Locking access to the web page');
		$code = $status->execute(function($status) use ($lockFile, $unlockFile) {
			$hasLockFile = $this->exec('test', '-e', $lockFile);
			$hasUnlockFile = $this->exec('test', '-e', $unlockFile);

			if ($hasLockFile === Command::SUCCESS) {
				return $status->setStatus($status::SKIPPED);
			}

			if ($hasUnlockFile === Command::FAILURE) {
				return $status->setStatus($status::SKIPPED);
			}

			return $this->exec('mv', $unlockFile, $lockFile);
		});

		$output->writeln('');
		return $code;
	}
}
