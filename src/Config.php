<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class Config extends \ArrayObject
{
	/**
	 * Path to the configuration file.
	 * @var string
	 */
	private $file;


	/**
	 * Load contents of the config file.
	 * @param string  $file  Path to yml file
	 */
	public function __construct($file)
	{
		$this->setFlags($this::ARRAY_AS_PROPS | $this::STD_PROP_LIST);
		$this->file = $file;

		if (!$content = file_get_contents($file)) {
			return;
			//throw new ParseException('File not found');
		}

		try {
			$this->exchangeArray(Yaml::parse($content));

		} catch (ParseException $e) {
			throw $e; // for now
		}
	}


	/**
	 * Return path to config file.
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}


	/**
	 * Convert config instance into array.
	 * @return array
	 */
	public function toArray()
	{
		return $this->getArrayCopy();
	}


	/**
	 * Save changes made to the config.
	 */
	public function save()
	{
		// save changes to the config
	}
}
