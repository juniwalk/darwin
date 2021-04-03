<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeWarmupCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Warmup the use of the application';
	protected static $defaultName = 'code:warmup';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['warmup']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->writeHeader('Warming up');

		$this->exec('composer', 'dump-autoload', '--optimize', '--no-dev');
		$this->exec('php', 'www/index.php', 'orm:generate-proxies');
		$output->writeln('');
		$this->exec('php', 'www/index.php', 'tessa:warm-up', '--quiet');

		return Command::SUCCESS;
	}
}
