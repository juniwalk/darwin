<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\Exception\InvalidArgumentException;
use JuniWalk\Darwin\Tools\ProgressIterator;
use JuniWalk\Darwin\Tools\Rule;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class FilePermissionCommand extends \Symfony\Component\Console\Command\Command
{
	/** @var string */
	const CONTAINMENT = '/^\/(srv)/i';


	/** @var string */
	private $dir;

	/** @var Rule[] */
	private $rules = [];


	protected function configure()
	{
		$this->setDescription('Fix file permissions in given directory');
		$this->setName('file:permission')->setAliases(['fix']);

		$this->addArgument('dir', InputArgument::OPTIONAL, 'Path to the project', getcwd());
		$this->addOption('force', 'f', InputOption::VALUE_NONE, 'Bypass container directory');
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @throws InvalidArgumentException
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->dir = $dir = $input->getArgument('dir');
		$force = (bool) $input->getOption('force');

		$output->writeln('<info>Changed current directory to <comment>'.$dir.'</comment></info>');

		if (!$dir || !is_dir($dir)) {
			throw new InvalidArgumentException('Unable to fix permissions in given directory');
		}

		if (!$force && !preg_match(static::CONTAINMENT, $dir)) {
			throw new InvalidArgumentException('Directory containment breach, use --force flag to override');
		}

		$this->loadRules();
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$question = new ConfirmationQuestion('Continue with this directory <comment>[Y,n]</comment>? ');

		if (!$this->getHelper('question')->ask($input, $output, $question)) {
			return;
		}

		$finder = (new Finder)
			->in($this->dir)
			->exclude('vendor')
			->exclude('bin');

		$progress = new ProgressIterator($output, $finder);
		$progress->onSingleStep[] = function ($bar, $file) {
			$bar->setMessage(str_replace($this->dir, '.', $file));

			foreach ($this->rules as $rule) {
				$rule->apply($file);
			}

			$bar->advance();
		};

		$progress->execute();
	}


	private function loadRules()
	{
		$config = $this->getHelper('config');
		$rules = $config->load('fix.neon');

		$class = $rules['className'];

		foreach ($rules['rules'] as $i => $rule) {
			$this->rules[$i] = new $class(
				$rule['pattern'],
				$rule['type'],
				$rule['owner'],
				$rule['mode']
			);
		}
	}
}
