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

final class WebLockCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'LOCK access into website';
	protected static $defaultName = 'web:lock';

	/** @var string */
	const FILE_LOCK = 'www/lock.phtml';
	const FILE_UNLOCK = 'www/lock.off';


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
		$status->setMessage('Locking access to the web page');
		$output->writeln('');

		$code = $status->execute(function($status) {
			$isWebLocked = $this->exec('test', '-e', $this::FILE_LOCK);

			if ($isWebLocked === Command::SUCCESS) {
				return $status->setStatus($status::SKIPPED);
			}

			return $this->exec('mv', $this::FILE_UNLOCK, $this::FILE_LOCK);
		});

		$output->writeln('');
		return $code;
	}
}
