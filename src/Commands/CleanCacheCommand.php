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

final class CleanCacheCommand extends AbstractCommand
{
	/** @var bool */
	private $skipFix;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Clear application cache and fix permissions');
		$this->setName('clean:cache')->setAliases(['clean']);

		$this->addOption('skip-fix', 's', InputOption::VALUE_NONE, 'Skip fixing permissions');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @throws ProjectNotFoundException
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->skipFix = $input->getOption('skip-fix');

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->writeHeader('Clearing out application cache');

		$status = new StatusIndicator($output);
		$status->setMessage('Clear application cache');
		$status->execute(function($status) {
			return $this->exec('rm', '-rf', 'temp/cache/*');
		});

		$status->setMessage('Clear compiled assets');
		$status->execute(function($status) {
			return $this->exec('rm', '-rf', 'www/static/*');
		});

		$status->setMessage('Clear doctrine proxies');
		$status->execute(function($status) {
			return $this->exec('rm', '-rf', 'temp/proxies/*');
		});

		if ($this->skipFix === true) {
			return Command::SUCCESS;
		}

		$params = new ArgvInput;
		$params->setInteractive(false);
		$fixCommand = $this->findCommand('file:permission');
		$fixCommand->run($params, $output);

		return Command::SUCCESS;
	}
}
