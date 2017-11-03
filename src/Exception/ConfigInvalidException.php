<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Exception;

final class ConfigInvalidException extends DarwinException
{
	/**
	 * @param  string  $fileName
	 * @return static
	 */
	public static function fromFileName($fileName)
	{
		return new static('Invalid configuration structure in file '.$fileName, 500);
	}
}
