<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressIterator extends \Nette\Object
{
	/** @var OutputInterface */
	private $output;

	/** @var \Traversable */
	private $values;


	/** @var callable[] */
	public $onBeforeStart = [];

	/** @var callable[] */
	public $onBeforeFinish = [];

	/** @var callable[] */
	public $onSingleStep = [];


	/**
	 * @param OutputInterface  $output
	 * @param Traversable      $values
	 */
	public function __construct(OutputInterface $output, \Traversable $values)
	{
		$this->output = $output;
		$this->values = $values;

		$this->onBeforeStart[] = function ($bar) use ($output) {
			$bar->setMessage('<info>Preparing...</info>');
			$output->write(PHP_EOL);
		};

		$this->onBeforeFinish[] = function ($bar) {
			$bar->setMessage('<info>Process has finished</info>');
		};
	}


	/**
	 * @return \Traversable
	 */
	public function getValues()
	{
		return $this->values;
	}


	public function execute()
	{
		$bar = new ProgressBar($this->output, iterator_count($this->values));
		$bar->setFormat(" %current%/%max% [%bar%] %percent:3s%%\n %message%");
		$bar->setRedrawFrequency(100);

		$this->onBeforeStart($bar);
		$bar->start();

		foreach ($this->values as $value) {
			$this->onSingleStep($bar, $value);
			usleep(250);
		}

		$this->onBeforeFinish($bar);
		$bar->finish();

		$this->output->writeln(PHP_EOL);
	}
}
