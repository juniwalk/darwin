<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaDumpCommand extends AbstractCommand
{
	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Dump SQLs of pending schema structure update');
		$this->setName('schema:dump');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->exec('php', 'www/index.php', 'orm:schema-tool:update', '--dump-sql');
		$output->writeln('');

		return Command::SUCCESS;
	}
}
