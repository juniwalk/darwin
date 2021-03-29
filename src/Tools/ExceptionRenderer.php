<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

use Nette\Utils\Strings;
use Throwable;
use Tracy\Debugger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

final class ExceptionRenderer
{
	/** @var OutputInterface */
	private $output;

	/** @var Terminal */
	private $terminal;

	/** @var bool */
	private $logExceptions = true;

	/** @var callable */
	private $onBeforeRender;

	/** @var callable */
	private $onAfterRender;


	/**
	 * @param OutputInterface  $output
	 */
	public function __construct(OutputInterface $output)
	{
		$this->terminal = new Terminal;
		$this->output = $output;
	}


	/**
	 * @param  bool  $logExceptions
	 * @return void
	 */
	public function setLogExceptions(bool $logExceptions = false): void
	{
		$this->logExceptions = $logExceptions;
	}


	/**
	 * @param  callable|null  $beforeRender
	 * @return void
	 */
	public function setBeforeRender(?callable $beforeRender): void
	{
		$this->onBeforeRender = $beforeRender;
	}


	/**
	 * @param  callable|null  $afterRender
	 * @return void
	 */
	public function setAfterRender(?callable $afterRender): void
	{
		$this->onAfterRender = $afterRender;
	}


	/**
	 * @param  Throwable  $e
	 * @return void
	 */
	public function render(Throwable $e): void
	{
		$output = $this->output->getErrorOutput();

		if (isset($this->onBeforeRender)) {
			call_user_func($this->onBeforeRender, $e);
		}

		$title = sprintf('  [%s]  ', get_class($e));
		$len = Strings::length($title);
		$width = $this->terminal->getWidth() ? $this->terminal->getWidth() - 1 : PHP_INT_MAX;
		$formatter = $output->getFormatter();
		$lines = [];

		foreach (preg_split('/\r?\n/', $e->getMessage()) as $line) {
			foreach ($this->splitStringByWidth($line, $width - 4) as $line) {
				// pre-format lines to get the right string length
				$lineLength = Strings::length(preg_replace('/\[[^m]*m/', '', $formatter->format($line))) + 4;
				$lines[] = array($line, $lineLength);
				$len = max($lineLength, $len);
			}
		}

		$messages = [];
		$messages[] = $emptyLine = $formatter->format(sprintf('<error>%s</error>', str_repeat(' ', $len)));
		$messages[] = $formatter->format(sprintf('<error>%s%s</error>', $title, str_repeat(' ', max(0, $len - Strings::length($title)))));

		foreach ($lines as $line) {
			$messages[] = $formatter->format(sprintf('<error>  %s  %s</error>', $line[0], str_repeat(' ', $len - $line[1])));
		}

		$messages[] = $emptyLine;
		$messages[] = PHP_EOL;

		$output->writeln($messages);

		if ($this->logExceptions) {
			Debugger::log($e);
		}

		if (isset($this->onAfterRender)) {
			call_user_func($this->onAfterRender, $e);
		}
	}


	/**
	 * @param  string
	 * @param  int  $width
	 * @return string[]
	 */
	private function splitStringByWidth(string $string, int $width): iterable
	{
		// str_split is not suitable for multi-byte characters, we should use preg_split to get char array properly.
		// additionally, array_slice() is not enough as some character has doubled width.
		// we need a function to split string not by character count but by string width
		if (false === $encoding = mb_detect_encoding($string, null, true)) {
			return str_split($string, $width);
		}

		$utf8String = mb_convert_encoding($string, 'utf8', $encoding);
		$lines = [];
		$line = '';

		foreach (preg_split('//u', $utf8String) as $char) {
			// test if $char could be appended to current line
			if (mb_strwidth($line.$char, 'utf8') <= $width) {
				$line .= $char;
				continue;
			}

			// if not, push current line to array and make new line
			$lines[] = str_pad($line, $width);
			$line = $char;
		}

		if ($line !== '') {
			$lines[] = count($lines) ? str_pad($line, $width) : $line;
		}

		mb_convert_variables($encoding, 'utf8', $lines);

		return $lines;
	}
}
