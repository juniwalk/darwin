<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanLogsCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Remove all error logs';
	protected static $defaultName = 'clean:logs';


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
		return $this->exec('find', 'log/*', '-not', '-name', '\'.gitignore\'', '-print0', '|', 'xargs', '-0', 'rm', '-rf', '--');
	}
}
