<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Exception;

final class ConfigNotFoundException extends DarwinException
{
	/**
	 * @param  string  $fileName
	 * @return static
	 */
	public static function fromFileName(string $fileName) : self
	{
		return new static('Unable to find configuration with name '.$fileName, 500);
	}
}
