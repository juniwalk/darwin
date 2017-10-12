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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class FilePermissionCommand extends \Symfony\Component\Console\Command\Command
{
	/**
	 * Working directory for the permission fixing.
	 * @var string
	 */
	private $folder;

	/**
	 * List of rules for permission fixer.
	 * @var Rule[]
	 */
	private $rules;


	/**
	 * @return string
	 */
	public function getFolder() : string
	{
		return $this->folder ?: getcwd();
	}


	protected function configure()
	{
		$this->setDescription('Fix file permissions in given directory');
		$this->setName('file:permission')->setAliases(['fix']);

		$this->addArgument('folder', InputArgument::OPTIONAL, 'Working directory for permission fixer');
		$this->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Name of configuration file', 'default');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->rules = $this->loadRules($input->getOption('config'));
		$this->folder = $input->getArgument('folder');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		$question = new ConfirmationQuestion('Continue with current directory <comment>[Y,n]</comment>? ');

		if ($this->getHelper('question')->ask($input, $output, $question)) {
			return;
		}

		$this->setCode(function () {
			return 0;
		});
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return integer|NULL
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = (new Finder)->in($this->getFolder())
			->exclude('vendor')
			->exclude('bin');

		if (!$rules = $this->loadRules()) {
			return 0;
		}

		$progress = new ProgressIterator($output, $finder);
		$progress->onSingleStep[] = function ($bar, $file) use ($rules) {
			$bar->setMessage(str_replace($this->getFolder(), '.', $file));

			foreach ($rules as $rule) {
				$rule->apply($file);
			}

			$bar->advance();
		};

		$progress->execute();
	}


	/**
	 * @param  string  $file
	 * @return Rule[]
	 */
	private function loadRules(string $file)
	{
		$config = $this->getHelper('config')
			->load($file.'.neon');

		$class = $config['className'];
		$rules = [];

		foreach ($config['rules'] as $i => $rule) {
			$rules[$i] = new $class(
				$rule['pattern'],
				$rule['type'],
				$rule['owner'],
				$rule['mode']
			);
		}

		return $rules;
	}
}
