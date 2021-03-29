<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class GitChangelogCommand extends AbstractCommand
{
	/** @var string */
	private $range;


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription('Create changelog from git log output.');
		$this->setName('git:changelog')->setAliases(['changelog']);

		$this->addArgument('range', InputArgument::OPTIONAL, 'Range of the logs to include', null);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$range = $input->getArgument('range');

		if ($range == null) {
			$this->range = 'origin/HEAD..HEAD';
		}

		if ($range == 'rebuild') {
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
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$finder = (new Finder)->in(getcwd())
			->name('/changelog\.md$/i');

		if (!$finder->hasResults()) {
			// changelog not found
			throw new \Exception;
		}

		$file = $finder->getIterator()->current();

		$commits = $this->exec('git --no-pager log '.$this->range.' --format=\'"%cd","%s"\'');
		$commits = explode(PHP_EOL, $commits);
		$commits = array_filter($commits);

		if (!$commits) {
			// no commits found
			throw new \Exception;
		}

		$changelog = $changes = $lastDate = null;

		if ($file && $this->range != null) {
			$changelog = file_get_contents($file);
		}

		foreach ($commits as $commit) {
			[$date, $message] = str_getcsv($commit);

			if ($lastDate !== $date) {
				$changes .= PHP_EOL.'### '.$date.PHP_EOL;
			}

			$changes .= '- '.$message.PHP_EOL;
			$lastDate = $date;
		}

		$changelog = ltrim($changes).PHP_EOL.$changelog;
		file_put_contents($file, $changelog);


		// give message to check the generated output?

		return Command::SUCCESS;
	}
}
