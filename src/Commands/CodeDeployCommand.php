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
		$status = new StatusIndicator($output);


		// title.source:
		$output->writeln('');
		$this->printHeader('Updating source code of the application');


		// lock:
		$lockCommand = $this->findCommand('web:lock');
		$lockCommand->run(new ArgvInput, $output);


		// source:
		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');
		$this->exec('composer', 'install', '--no-interaction', '--optimize-autoloader', '--prefer-dist', '--no-dev');
		$output->writeln('');
		$this->exec('yarn', 'install');
		$output->writeln('');

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


		// clean:
		$this->exec('rm', '-rf', 'temp/cache/*');
		$this->exec('rm', '-rf', 'www/static/*');
		$this->exec('darwin', 'fix', '--no-interaction');


		// warmup:
		$this->exec('composer', 'dump-autoload', '--optimize', '--no-dev');
		$this->exec('php', 'www/index.php', 'orm:generate-proxies');
		$output->writeln('');
		$this->exec('php', 'www/index.php', 'tessa:warm-up', '--quiet');
		$this->exec('darwin', 'fix', '--no-interaction');

		return Command::SUCCESS;
	}
}
