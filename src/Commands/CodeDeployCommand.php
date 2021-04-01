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
	 * @throws GitNoCommitsException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->writeHeader('Updating source code of the application');

		$lockCommand = $this->findCommand('web:lock');
		$lockCommand->run(new ArgvInput, $output);

		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');
		$this->exec('composer', 'install', '--no-interaction', '--optimize-autoloader', '--prefer-dist', '--no-dev');
		$output->writeln('');
		$this->exec('yarn', 'install');
		$output->writeln('');

		$status = new StatusIndicator($output);
		$status->setMessage('Create directory for PHP sessions');
		$status->execute(function($status) {
			return $this->exec('mkdir', '-p', '-m', '0755', 'temp/sessions');
		});

		$status->setMessage('Create directory for assets cache');
		$status->execute(function($status) {
			return $this->exec('mkdir', '-p', '-m', '0755', 'www/static');
		});

		$output->writeln('');

		// clean.proxies
		$this->exec('rm', '-rf', 'temp/proxies/*');


		// database:
		$this->exec('rm', '-rf', 'temp/cache/*');
		$this->exec('rm', '-rf', 'www/static/*');
		$this->exec('php', 'www/index.php', 'migrations:migrate', '--no-interaction');
		$output->writeln('');


		$cleanCommand = $this->findCommand('clean:cache');
		$cleanCommand->run(new ArgvInput, $output);


		$warmupCommand = $this->findCommand('code:warmup');
		$warmupCommand->run(new ArgvInput, $output);

		return Command::SUCCESS;
	}
}
