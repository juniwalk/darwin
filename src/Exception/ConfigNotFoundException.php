<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Exception;

final class ConfigNotFoundException extends \RuntimeException
{
	/**
	 * @param  string  $fileName
	 * @return static
	 */
	public static function fromFileName($fileName): self
	{
		return new static('Unable to find configuration with name '.$fileName, 500);
	}
}
