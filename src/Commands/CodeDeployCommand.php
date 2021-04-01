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
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->writeHeader('Updating source code of the application');

		$lockCommand = $this->findCommand('web:lock');
		$lockCommand->run(new ArgvInput, $output);

		$status = new StatusIndicator($output);
		$status->setMessage('Create directory for PHP sessions');
		$status->execute(function($status) {
			return $this->exec('mkdir', '-p', '-m', '0755', 'temp/sessions');
		});

		$status->setMessage('Create directory for assets cache');
		$status->execute(function($status) {
			return $this->exec('mkdir', '-p', '-m', '0755', 'www/static');
		});


		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');

		$installCommand = $this->findCommand('code:install');
		$installCommand->run(new ArgvInput, $output);

		$output->writeln('');


		$cleanCommand = $this->findCommand('clean:cache');
		$cleanCommand->run(new ArgvInput, $output);


		// database:
		$this->exec('php', 'www/index.php', 'migrations:migrate', '--no-interaction');
		$output->writeln('');


		$warmupCommand = $this->findCommand('code:warmup');
		$warmupCommand->run(new ArgvInput, $output);

		return Command::SUCCESS;
	}
}
