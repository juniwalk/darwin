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

final class WebUnlockCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'UNLOCK access into website';
	protected static $defaultName = 'web:unlock';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['unlock']);
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

		$status->setMessage('Opening access to the web page');
		$code = $status->execute(function($status) use ($config) {
			$isWebLocked = $this->exec('test', '-e', $config->getLockFile());
	
			if ($isWebLocked !== Command::SUCCESS) {
				return $status->setStatus($status::SKIPPED);
			}
	
			return $this->exec('mv', $config->getLockFile(), $config->getUnlockFile());
		});

		$output->writeln('');
		return $code;
	}
}
