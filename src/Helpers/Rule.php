<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Helpers;

final class Rule
{
	private $pattern;
	private $type;
	private $owner;
	private $modes;


	/**
	 * @param string  $pattern
	 * @param string  $tyoe
	 * @param string  $owner
	 * @param array   $modes
	 */
	public function __construct($pattern, $type, $owner, array $modes)
	{
		$this->pattern = $pattern;
		$this->type = $type;
		$this->owner = $owner;
		$this->modes = $modes;
	}


	/**
	 * @param  SplFileInfo  $file
	 * @return bool
	 */
	public function apply(\SplFileInfo $file)
	{
		if (!$this->checkType($file)) {
			return false;
		}

		if (!preg_match($this->pattern, $file->getFilename())) {
			return false;
		}

		chmod($file, $file->isFile() ? $this->modes[0] : $this->modes[1]);
		chown($file, $this->owner);

		return true;
	}


	/**
	 * @param  SplFileInfo  $file
	 * @return bool
	 */
	private checkType(\SplFileInfo $file)
	{
		if ($this->type == 'any') {
			return true;
		}

		if ($file->isFile() && $this->type == 'file') {
			return true;
		}

		if ($file->isDir() && $this->type == 'dir') {
			return true;
		}

		return false;
	}
}
