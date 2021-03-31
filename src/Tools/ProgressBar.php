<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

use Throwable;
use Symfony\Component\Console\Helper;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressBar
{
	/** @var OutputInterface */
	private $output;

	/** @var bool */
	private $isHideOnFinish;

	/** @var bool */
	private $throwExceptions;


	/**
	 * @param OutputInterface  $output
	 * @param bool  $isHideOnFinish
	 * @param bool  $throwExceptions
	 */
	public function __construct(
		OutputInterface $output,
		bool $isHideOnFinish = true,
		bool $throwExceptions = false
	) {
		$this->isHideOnFinish = $isHideOnFinish;
		$this->throwExceptions = $throwExceptions;
		$this->output = $output;
	}


	/**
	 * @param  bool  $throwExceptions
	 * @return void
	 */
	public function setThrowExceptions(bool $throwExceptions = false): void
	{
		$this->throwExceptions = $throwExceptions;
	}


	/**
	 * @param  bool  $hideOnFinish
	 * @return void
	 */
	public function setHideOnFinish(bool $hideOnFinish = true): void
	{
		$this->isHideOnFinish = $hideOnFinish;
	}


	/**
	 * @param  iterable  $values
	 * @param  callable  $callback
	 * @return void
	 */
	public function execute(iterable $values, callable $callback): void
	{
		// Make sure there is right amount of padding
		// before progress bar is shown
		$this->output->writeln('');

		$progress = new Helper\ProgressBar($this->output, sizeof($values));
		$progress->setFormat(" %current%/%max% [%bar%] %percent:3s%%\n %message%");
		$progress->setMessage('<info>Preparing...</info>');
		$progress->start();

		$renderer = new ExceptionRenderer($this->output);
		$renderer->setBeforeRender([$progress, 'clear']);
		$renderer->setAfterRender([$progress, 'display']);

		foreach ($values as $key => $value) {
			try {
				$callback($progress, $value, $key);

			} catch (Throwable $e) {
				if ($this->throwExceptions) {
					throw $e;
				}

				$renderer->render($e);
			}
		}

		$progress->setMessage('<info>Process has finished</info>');
		$progress->finish();

		if ($this->isHideOnFinish) {
			$progress->clear();
			return;
		}

		// Make sure there is right amount of padding
		// after progress bar if it is left shown
		$this->output->writeln(PHP_EOL);
	}
}
