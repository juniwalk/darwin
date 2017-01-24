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
use Nette\Utils\Image;
use Nette\Utils\ImageException;

final class ImageShrinkCommand extends \Symfony\Component\Console\Command\Command
{
	/** @var string */
	const IMAGES = '/\.(jpe?g|png|gif)$/i';


	protected function configure()
	{
		$this->setDescription('Shrink all images that ale larger than given size');
		$this->setName('image:shrink')->setAliases(['shrink']);

		$this->addOption('size', NULL, InputOption::VALUE_REQUIRED, 'Size to which the image will be fitted', 1024);
		$this->addOption('quality', NULL, InputOption::VALUE_REQUIRED, 'Quality of resulting image', 75);
		$this->addOption('backup', NULL, InputOption::VALUE_NONE, 'Backup image before resizing');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return integer|NULL
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = (new Finder)->files()
			->name($this::IMAGES)
			->in($folder = getcwd());

		$progress = new ProgressIterator($output, $finder);
		$progress->onSingleStep[] = function ($bar, $file) use ($input, $folder) {
			$bar->setMessage(str_replace($folder, '.', $file));

			try {
				$status = $this->resizeImage($input, $file);

			} catch (ImageException $e) {
				$status = FALSE;
			}

			// TODO: Store failed image

			$bar->advance();
		};

		$progress->execute();

		// TODO: Print table with failed images
		// limit the list to X entries
	}


	/**
	 * @param  InputInterface  $input
	 * @param  \SplFileInfo    $file
	 * @return bool
	 */
	private function resizeImage(InputInterface $input, \SplFileInfo $file)
	{
		$size = $input->getOption('size');
		$path = $file->getPathname();

		$image = Image::fromFile($path, $format);

		if ($image->getWidth() <= $size && $image->getHeight() <= $size) {
			return TRUE;
		}

		$image->resize($size, $size, $image::FIT | $image::SHRINK_ONLY);

		if ($input->getOption('backup')) {
			copy($path, $path.'.backup');
		}

		return $image->save($path, $input->getOption('quality'), $format);
	}
}
