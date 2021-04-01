<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class WebUnlockCommand extends AbstractCommand
{
	/** @var string */
	const FILE_LOCK = 'www/lock.phtml';
	const FILE_UNLOCK = 'www/lock.off';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('UNLOCK access into website');
		$this->setName('web:unlock')->setAliases(['unlock']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 * @throws GitNoCommitsException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$isWebLocked = $this->exec('test', '-e', $this::FILE_LOCK);

		if ($isWebLocked !== Command::SUCCESS) {
			return Command::SUCCESS;
		}

		$this->exec('mv', $this::FILE_LOCK, $this::FILE_UNLOCK);

		return Command::SUCCESS;
	}
}
