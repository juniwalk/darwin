<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
		// deploy: lock source clean.proxies database clean warmup

		// lock:
		// test ! -e "$(FILE_UNLOCK)" || mv "$(FILE_UNLOCK)" "$(FILE_LOCK)"

		// source:
		// git pull --ff-only --no-stat
		// echo ""
		// test ! -e "$(IS_COMPOSER)" || composer install --no-interaction --optimize-autoloader --prefer-dist --no-dev
		// echo ""
		// test ! -e "$(IS_YARN)" || yarn install
		// echo ""
		// mkdir -p -m 0755 temp/sessions
		// mkdir -p -m 0755 www/static

		// clean.proxies
		// rm -rf temp/proxies/*

		// database:
		// rm -rf temp/cache/*
		// rm -rf www/static/*
		// php www/index.php migrations:migrate --no-interaction
		// echo ""
		// php www/index.php orm:generate-proxies

		// clean:
		// rm -rf temp/cache/*
		// rm -rf www/static/*
		// test ! -e "$(IS_COMPOSER)" || composer dump-autoload --optimize --no-dev
		// test ! -e "$(IS_DARWIN)" || darwin fix --no-interaction

		// warmup:
		// php www/index.php tessa:warm-up --quiet
		// test ! -e "$(IS_DARWIN)" || darwin fix --no-interaction

		$process = new Process(['composer', 'install', '--no-interaction', '--optimize-autoloader', '--prefer-dist', '--no-dev']);
		$process->run(function($type, $buffer) use ($output) {
			$output->write($buffer);
		});

		return Command::SUCCESS;
	}
}
