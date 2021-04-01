<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaDiffCommand extends AbstractCommand
{
	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Create new migration from schema differences.');
		$this->setName('schema:diff');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->exec('php', 'www/index.php', 'migrations:diff');
		$output->writeln('');

		return Command::SUCCESS;
	}
}
