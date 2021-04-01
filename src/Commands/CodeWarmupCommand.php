<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeWarmupCommand extends AbstractCommand
{
	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Warmup the use of the application.');
		$this->setName('code:warmup')->setAliases(['warmup']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 * @throws GitNoCommitsException
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
