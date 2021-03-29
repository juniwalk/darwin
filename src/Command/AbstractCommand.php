<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends Command
{
	/** @var callable[] */
	private $questions = [];

	/** @var InputInterface */
	private $input;

	/** @var OutputInterface */
	private $output;


	/**
	 * @param  callable  $question
	 * @return void
	 */
	public function addQuestion(callable $question): void
	{
		$this->questions[] = $question;
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
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
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
