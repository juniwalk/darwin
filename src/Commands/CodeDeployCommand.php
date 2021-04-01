<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeDeployCommand extends AbstractCommand
{
	/** @var string */
	const FILE_LOCK = 'www/lock.phtml';
	const FILE_UNLOCK = 'www/lock.off';


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
		$console = $this->getApplication();


		$output->writeln('');
		$output->writeln('<question>'.str_repeat(' ', 68).'</>');
		$output->writeln('<question>'.str_pad('Updating source code of the application', 68, ' ', STR_PAD_BOTH).'</>');
		$output->writeln('<question>'.str_repeat(' ', 68).'</>');
		$output->writeln('');

		// lock:
		$params = new ArrayInput(['command' => 'web:lock']);
		$params->setInteractive(false);
		$console->doRun($params, $output);


		// source:
		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');
		$this->exec('composer', 'install', '--no-interaction', '--optimize-autoloader', '--prefer-dist', '--no-dev');
		$output->writeln('');
		$this->exec('yarn', 'install');
		$output->writeln('');
		$this->exec('mkdir', '-p', '-m', '0755', 'temp/sessions');
		$this->exec('mkdir', '-p', '-m', '0755', 'www/static');

		// clean.proxies
		$this->exec('rm', '-rf', 'temp/proxies/*');

		// database:
		$this->exec('rm', '-rf', 'temp/cache/*');
		$this->exec('rm', '-rf', 'www/static/*');
		$this->exec('php', 'www/index.php', 'migrations:migrate', '--no-interaction');
		$output->writeln('');
		$this->exec('php', 'www/index.php', 'orm:generate-proxies');
		$output->writeln('');

		// clean:
		$this->exec('rm', '-rf', 'temp/cache/*');
		$this->exec('rm', '-rf', 'www/static/*');
		$this->exec('composer', 'dump-autoload', '--optimize', '--no-dev');
		$this->exec('darwin', 'fix', '--no-interaction');

		// warmup:
		$this->exec('php', 'www/index.php', 'tessa:warm-up', '--quiet');
		$this->exec('darwin', 'fix', '--no-interaction');

		return Command::SUCCESS;
	}
}
