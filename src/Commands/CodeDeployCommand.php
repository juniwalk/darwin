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


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['deploy']);

		$this->addOption('unlock', 'u', InputOption::VALUE_NONE, 'Unlock application afterwards');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @throws CommandFailedException
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$config = $this->getConfig();

		if (!$commandList = $config->getDeployCommands()) {
			throw CommandFailedException::fromName(static::$defaultName);
		}

		if ($input->getOption('unlock')) {
			$commandList[MakeUnlockedCommand::$defaultName] = [];
		}

		foreach ($commandList as $commandName => $arguments) {
			$arguments = new ArrayInput($arguments);
			$arguments->setInteractive(false);

			$command = $this->getCommand($commandName);
			$code = $command->run($arguments, $output);

			if ($code === Command::FAILURE) {
				throw CommandFailedException::fromName($commandName);
			}
		}

		return Command::SUCCESS;
	}
}
