<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\Tools\ProgressIterator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class ImageRestoreCommand extends \Symfony\Component\Console\Command\Command
{
	/** @var string */
	const IMAGES = '/\.(jpe?g|png|gif)\.backup$/i';


	protected function configure()
	{
		$this->setDescription('Restore all backed up images');
		$this->setName('image:restore')->setAliases(['restore']);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = (new Finder)->files()
			->name($this::IMAGES)
			->in($folder = getcwd());

		$progress = new ProgressIterator($output, $finder);
		$progress->onSingleStep[] = function ($bar, $file) use ($input, $folder) {
			$bar->setMessage(str_replace($folder, '.', $file));

			$path = $file->getPathname();

			if (!rename($path, rtrim($path, '.backup'))) {
				// Restore has failed
			}

			$bar->advance();
		};

		$progress->execute();
	}
}
