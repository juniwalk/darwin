<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Exception\CommandFailedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CodeDeployCommand extends AbstractConfigAwareCommand
{
	/** @var string */
	protected static $defaultDescription = 'Deploy pending updates to the project';
	protected static $defaultName = 'code:deploy';

	/** @var string[] */
	private $commandList = [
		'make:locked' => [],
		'code:pull' => [],
		'code:install' => [],
		'schema:migrate' => [],
		'clean:cache' => [
			'--skip-fix' => true
		],
		'code:warmup' => [],
		'make:close' => [],
	];


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['deploy']);

		$this->addOption('skip-migrations', 'm', InputOption::VALUE_NONE, 'Do not execute schema migrations');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		if ($input->getOption('skip-migrations')) {
			unset($this->commandList['schema:migrate']);
		}

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @throws CommandFailedException
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		foreach ($this->commandList as $commandName => $arguments) {
			$arguments = new ArrayInput($arguments);
			$arguments->setInteractive(false);

			$command = $this->findCommand($commandName);
			$code = $command->run($arguments, $output);

			if ($code === Command::FAILURE) {
				throw CommandFailedException::fromName($commandName);
			}
		}

		return Command::SUCCESS;
	}
}
