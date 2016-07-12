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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

final class FilePermissionCommand extends \Symfony\Component\Console\Command\Command
{
	protected function configure()
	{
		$this->setDescription('Fix file permissions in given directory');
		$this->setName('file:permission')->setAliases(['fix']);
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
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = (new Finder)
			->in($folder = getcwd())
			->exclude('vendor')
			->exclude('bin');

		if (!$rules = $this->loadRules()) {
			return;
		}

		$progress = new ProgressIterator($output, $finder);
		$progress->onSingleStep[] = function ($bar, $file) use ($rules, $folder) {
			$bar->setMessage(str_replace($folder, '.', $file));

			foreach ($rules as $rule) {
				$rule->apply($file);
			}

			$bar->advance();
		};

		$progress->execute();
	}


	/**
	 * @return Rule[]
	 */
	private function loadRules()
	{
		$config = $this->getHelper('config')
			->load('fix.neon');

		$class = $config['className'];

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
