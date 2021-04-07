<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanSessionsCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Remove all user sessions';
	protected static $defaultName = 'clean:sessions';


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
		$output->writeln('');

		$status->setMessage('Clear user sessions');
		$code = $status->execute(function($status) use ($config) {
			if (!$sessionDir = $config->getSessionDir()) {
				return $status->setStatus($status::SKIPPED);
			}

			return $this->exec('rm', '-rf', $sessionDir.'/*');
		});

		$output->writeln('');
		return $code;
	}
}
