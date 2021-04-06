<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Exception\NoCommitsException;
use JuniWalk\Darwin\Tools\ProgressBar;
use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class CodeChangelogCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Create changelog from git log output';
	protected static $defaultName = 'code:changelog';

	/** @var string */
	const CHANGELOG_FILE = 'CHANGELOG.md';
	const CHANGELOG_MSG = '#changelog';

	/** @var string */
	private $range;

	/** @var string */
	private $filter;

	/** @var bool */
	private $autoCommit;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['changelog']);

		$this->addArgument('range', InputArgument::OPTIONAL, 'Range of the logs to include', null);
		$this->addOption('branch', 'b', InputOption::VALUE_REQUIRED, 'Name of working branch', 'master');
		$this->addOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter pattern for the git log command', $this::CHANGELOG_MSG);
		$this->addOption('auto-commit', 'a', InputOption::VALUE_NONE, 'Automaticaly commit generated changelog');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->autoCommit = $input->getOption('auto-commit');
		$this->range = $input->getArgument('range');
		$this->filter = $input->getOption('filter');
		$branch = $input->getOption('branch');

		if ($this->range == null && $branch) {
			$this->range = "origin/{$branch}..{$branch}";
		}

		if ($this->range == 'create') {
			$this->range = null;
		}

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->addQuestion(function($cli) {
			return $cli->confirm('Generate changelog file?');
		});

		parent::interact($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 * @throws GitNoCommitsException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$command = 'git --no-pager log '.$this->range.' --format=\'"%cd","%s"\' --date=short';

		if ($this->filter) {
			$command .= ' --grep='.escapeshellarg($this->filter).' --invert-grep --regexp-ignore-case';
		}

		if (!exec($command, $commits) || !$commits) {
			throw NoCommitsException::fromRange($this->range);
		}

		$files = (new Finder)->in(getcwd())->depth('== 0')->name('/'.$this::CHANGELOG_FILE.'$/i');
		$file = current(iterator_to_array($files->getIterator()));
		$changelog = $changes = $lastDate = null;

		if ($file && $this->range != null) {
			$changelog = $file->getContents();
		}

		$progress = new ProgressBar($output, false);
		$progress->execute($commits, function($progress, $commit) use (&$changes, &$lastDate) {
			[$date, $message] = str_getcsv($commit);

			$progress->setMessage('Processing <comment>'.$message.'</>.');
			$progress->advance();

			if ($lastDate !== $date) {
				$changes .= PHP_EOL.'### '.(DateTime::from($date)->format('d.m.Y')).PHP_EOL;
			}

			$changes .= '- '.$message.PHP_EOL;
			$lastDate = $date;
		});

		$filename = $file ? $file->getPathname() : './'.$this::CHANGELOG_FILE;
		file_put_contents($filename, ltrim($changes).PHP_EOL.$changelog);

		if ($this->autoCommit) {
			exec('git add '.$this::CHANGELOG_FILE);
			exec('git commit --message="'.$this::CHANGELOG_MSG.'"');
		}

		$output->writeln(PHP_EOL.'Changelog generated from '.sizeof($commits).' commits.');
		return Command::SUCCESS;
	}
}
