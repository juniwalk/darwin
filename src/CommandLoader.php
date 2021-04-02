<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

final class CommandLoader implements CommandLoaderInterface
{
	/** @var string[] */
	private $commands;


	/**
	 * @param string[]  $commands
	 */
	public function __construct(iterable $commands)
	{
		foreach ($commands as $command) {
			$name = $command::getDefaultName();
			$this->commands[$name] = $command;
		}
	}


	/**
	 * @param  string  $name
     * @return bool
	 */
	public function has(string $name): bool
	{
		return isset($this->commands[$name]);
	}


	/**
	 * @param  string  $name
     * @return Command
	 * @throws CommandNotFoundException
	 */
	public function get(string $name): Command
	{
		if (!$this->has($name)) {
			throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
		}

		return $this->commands[$name]();
	}


	/**
	 * @return string[]
	 */
	public function getNames(): iterable
	{
		return array_keys($this->commands);
	}
}
