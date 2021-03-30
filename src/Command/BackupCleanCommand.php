<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\Tools\ProgressBar;
use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class BackupCleanCommand extends AbstractCommand
{
	/** @var string */
	const DATE_FORMAT = '/(\d{14})/';

	/** @var DateTime */
	private $keepTime;

	/** @var int */
	private $keepCount;

	/** @var bool */
	private $isForced;

	/** @var string */
	private $folder;

	/** @var int */
	private $count = 0;

	/** @var int */
	private $size = 0;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Clear out backups using defined parameters.');
		$this->setName('backup:clean');

		$this->addArgument('folder', InputArgument::OPTIONAL, 'Working directory for backup cleaning.', getcwd());
		$this->addOption('keep-count', 'c', InputOption::VALUE_REQUIRED, 'Minimum number of backups to be kept per project.', 3);
		$this->addOption('keep-time', 't', InputOption::VALUE_REQUIRED, 'Keep backups that are no older than keep-time.', '-7 days');
		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'This command runs in dry-run as default. Use -f to perform deletions.');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$keepTime = strtotime($input->getOption('keep-time'));

		$this->keepTime = DateTime::from($keepTime)->setTime(0, 0, 0);
		$this->keepCount = $input->getOption('keep-count');
		$this->folder = $input->getArgument('folder');
		$this->isForced = $input->getOption('force');

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$folder = $this->folder !== getcwd()
			? $this->folder
			: 'current';

		$this->addQuestion(function($cli) use ($folder) {
			return $cli->confirm('Continue with <info>'.$folder.'</> directory?');
		});

		parent::interact($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int|null
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$files = (new Finder)->files()
			->in($this->folder)
			->name('*.files.tgz')
			->name('*.db.tgz')
			->sortByName()
			->reverseSorting();

		if (!$files->hasResults()) {
			$output->writeln('Error, no files were found.');
			return 0;
		}

		$files = $this->categorize($files);

		$progress = new ProgressBar($output, false);
		$progress->execute($files, function($progress, $backups, $project) {
			$progress->setMessage($project);
			$progress->advance();

			$backups = $this->avoidActiveBackups($backups);
			$this->clearBackups($backups);
		});

		$output->writeln(PHP_EOL.'Success, <info>'.$this->count.' files</> cleared and <comment>'.$this->formatSize($this->size).'</> saved.');
		return Command::SUCCESS;
	}


	/**
	 * @param  string[]  $files
	 * @return string[]
	 */
	private function categorize(iterable $files): iterable
	{
		$backups = [];

		foreach ($files as $file) {
			$path = $file->getPathname();

			if (!preg_match(static::DATE_FORMAT, $path, $matches)) {
				continue;
			}

			$time = DateTime::createFromFormat('YmdHis', $matches[0])->getTimestamp();
			$project = basename(dirname($path));

			$backups[$project][$time][] = $path;
		}

		return $backups;
	}


	/**
	 * @param  string[]  $files
	 * @return string[]
	 */
	private function avoidActiveBackups(iterable $backups): iterable
	{
		$backups = array_slice($backups, $this->keepCount, null, true);

		foreach ($backups as $time => $backup) {
			$date = DateTime::from($time)->setTime(0, 0, 0);

			if ($date <= $this->keepTime) {
				continue;
			}

			unset($backups[$time]);
		}

		return $backups;
	}


	/**
	 * @param  string[]  $files
	 * @return void
	 */
	private function clearBackups(iterable $backups): void
	{
		$files = [];

		foreach ($backups as $backup) {
			$files = array_merge($files, $backup);
		}

		$this->count += sizeof($files);

		foreach ($files as $file) {
			$this->size += filesize($file);

			if (!$this->isForced) {
				continue;
			}

			unlink($file);
		}
	}


	/**
	 * @param  int  $bytes
	 * @param  int  $decimals
	 * @return string
	 */
	private function formatSize(int $bytes, int $decimals = 2): string
	{
		$size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		$factor = floor((strlen((string) $bytes) - 1) / 3);

		if ($factor <= 0) {
			$decimals = 0;
		}

		return sprintf(
			'%.'.$decimals.'f '.$size[$factor],
			$bytes / pow(1024, $factor)
		);
	}
}
