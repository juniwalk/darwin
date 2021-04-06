<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaDiffCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Create new migration from schema differences';
	protected static $defaultName = 'schema:diff';


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
		$code = $this->exec('php', 'www/index.php', 'migrations:diff');
		$output->writeln('');

		return $code;
	}
}
