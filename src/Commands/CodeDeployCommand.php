<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeDeployCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Deploy pending updates to the project';
	protected static $defaultName = 'code:deploy';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['deploy']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$params = new ArgvInput;
		$commands = [
			'make:locked',		// lock access
			'code:pull',		// pull new source code
			'code:install',		// install dependencies
			'clean:cache',		// clear cache
			'schema:migrate',	// migrate database
			'code:warmup',		// warmup cache
		];

		foreach ($commands as $command) {
			$command = $this->findCommand($command);
			$command->run($params, $output);
		}

		return Command::SUCCESS;
	}
}
