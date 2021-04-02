<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeInstallCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Install application dependencies';
	protected static $defaultName = 'code:install';


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
		$this->exec('composer', 'install', '--no-interaction', '--prefer-dist', '--no-dev');
		$output->writeln('');
		$this->exec('yarn', 'install');
		$output->writeln('');

		return Command::SUCCESS;
	}
}
