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

final class CodeDeployCommand extends AbstractCommand
{
	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Deploy pending updates to the project.');
		$this->setName('code:deploy')->setAliases(['deploy']);
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
			'web:lock',			// lock access
			'code:source',		// download new source code
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
