<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\ProgressBar;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Nette\Utils\Image;
use Nette\Utils\ImageException;

final class ImageShrinkCommand extends AbstractCommand
{
	/** @var string */
	const IMAGES = '/\.(jpe?g|png|gif)$/i';

	/** @var int|null */
	private $quality;

	/** @var int */
	private $size;

	/** @var bool */
	private $backup;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Shrink all images that ale larger than given size');
		$this->setName('image:shrink')->setAliases(['shrink']);

		$this->addOption('size', null, InputOption::VALUE_REQUIRED, 'Size to which the image will be fitted', null);
		$this->addOption('quality', null, InputOption::VALUE_REQUIRED, 'Quality of resulting image', 75);
		$this->addOption('backup', null, InputOption::VALUE_NONE, 'Backup image before resizing');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->quality = (int) $input->getOption('quality') ?: null;
		$this->size = (int) $input->getOption('size');
		$this->backup = (bool) $input->getOption('backup');

		parent::initialize($input, $output);
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

			$this->resizeImage($file);
		});

		// TODO: Print table with failed images
		// limit the list to X entries

		return Command::SUCCESS;
	}


	/**
	 * @param  SplFileInfo  $file
	 * @return void
	 */
	private function resizeImage(SplFileInfo $file): void
	{
		$path = $file->getPathname();
		$image = Image::fromFile($path, $format);

		if ($this->size && ($image->getWidth() > $this->size || $image->getHeight() > $this->size)) {
			$image->resize($this->size, $this->size, $image::FIT | $image::SHRINK_ONLY);
		}

		if ($this->backup == true) {
			copy($path, $path.'.backup');
		}

		$image->save($path, $this->quality, $format);
	}
}
