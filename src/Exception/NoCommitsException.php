<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Exception;

final class NoCommitsException extends \RuntimeException
{
	/**
	 * @param  string  $range
	 * @return static
	 */
	public static function fromRange(string $range): self
	{
		return new static('No commits found for range: '.$range, 500);
	}
}
