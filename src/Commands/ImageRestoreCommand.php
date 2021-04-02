<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class ImageRestoreCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Restore all backed up images';
	protected static $defaultName = 'image:restore';

	/** @var string */
	const IMAGES = '/\.(jpe?g|png|gif)\.backup$/i';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['restore']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$files = (new Finder)->in($folder = getcwd())
			->files()->name($this::IMAGES);

		$progress = new ProgressBar($output, false);
		$progress->execute($files, function($progress, $file) use ($folder) {
			$progress->setMessage(str_replace($folder, '.', $file));
			$progress->advance();

			$path = $file->getPathname();

			if (!rename($path, rtrim($path, '.backup'))) {
				// Restore has failed
			}
		});

		return Command::SUCCESS;
	}
}
