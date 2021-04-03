<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CodePullCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Pull repository changes';
	protected static $defaultName = 'code:pull';


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
		$this->writeHeader('Updating source code of the application');

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
		$this->exec('git', 'pull', '--ff-only', '--no-stat');
		$output->writeln('');

		return Command::SUCCESS;
	}
}
