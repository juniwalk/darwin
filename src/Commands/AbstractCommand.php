<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

abstract class AbstractCommand extends Command
{
	/** @var Configuration */
	private $config;

	/** @var InputInterface */
	private $input;

	/** @var OutputInterface */
	private $output;

	/** @var callable[] */
	private $questions = [];


	/**
	 * @return Configuration
	 */
	public function getConfig(): Configuration
	{
		return $this->config;
	}


	/**
	 * @param  string  $name
	 * @return Command
	 */
	public function findCommand(string $name): Command
	{
		return $this->getApplication()->find($name);
	}


	/**
	 * @param  callable  $question
	 * @return void
	 */
	public function addQuestion(callable $question): void
	{
		$this->questions[] = $question;
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->config = new Configuration;
		$this->input = $input;
		$this->output = $output;
	}


	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		if (empty($this->questions)) {
			return;
		}

		foreach ($this->questions as $question) {
			$answer = $question($this);
			$output->writeln('');

			if ($answer === false) {
				$this->terminate();
				break;
			}
		}
	}


	/**
	 * @return void
	 */
	protected function terminate(): void
	{
		$this->setCode(function(): int {
			return Command::SUCCESS;
		});
	}


	/**
	 * @param  string  $message
	 * @param  int  $width
	 * @return int
	 */
	protected function writeHeader(string $message, int $width = 68): void
	{
		$message = str_pad($message, $width, ' ', STR_PAD_BOTH);

		$this->output->writeln('');
		$this->output->writeln('<fg=black;bg=#00cdcd>'.str_repeat(' ', $width).'</>');
		$this->output->writeln('<fg=black;bg=#00cdcd>'.$message.'</>');
		$this->output->writeln('<fg=black;bg=#00cdcd>'.str_repeat(' ', $width).'</>');
		$this->output->writeln('');
	}


	/**
	 * @param  string[]  $command  ...
	 * @return int
	 */
	protected function exec(string ... $command): int
	{
		$process = new Process($command);
		$process->setTty(Process::isTtySupported());

		return $process->run(function($type, $buffer) {
			$this->output->write($buffer);
		});
	}


	/**
	 * @param  Question  $question
	 * @return mixed
	 */
	protected function ask(Question $question)
	{
		return $this->getHelper('question')->ask($this->input, $this->output, $question);
	}


	/**
	 * @param  string  $message
	 * @param  bool  $default
	 * @return bool
	 */
	protected function confirm(string $message, bool $default = true): bool
	{
		return $this->ask(new ConfirmationQuestion(
			$message.' <comment>[Y,n]</comment> ',
			$default
		));
	}


	/**
	 * @param  string  $message
	 * @param  string[]  $choices
	 * @param  mixed|null  $default
	 * @return mixed
	 */
	protected function choose(string $message, iterable $choices, $default = null)
	{
		$default = $default ?? array_keys($choices)[0];

		if (sizeof($choices) == 1) {
			return $choices[$default];
		}

		return $this->ask(new ChoiceQuestion(
			$message.' <comment>['.$choices[$default].']</comment> ',
			$choices,
			$default
		));
	}
}
