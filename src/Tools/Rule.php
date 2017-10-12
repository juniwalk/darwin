<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

use SplFileInfo as File;

final class Rule
{
	/**
	 * @var string
	 */
	private $pattern;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $owner;

	/**
	 * @var int[]
	 */
	private $modes;


	/**
	 * @param string  $pattern
	 * @param string  $type
	 * @param string  $owner
	 * @param int[]  $modes
	 */
	public function __construct($pattern, $type, $owner, array $modes)
	{
		$this->pattern = $pattern;
		$this->type = $type;
		$this->owner = $owner;
		$this->modes = $modes;
	}


	/**
	 * @param  File  $file
	 * @return bool
	 */
	private function isDesiredType(File $file)
	{
		if ($this->type == 'any') {
			return TRUE;
		}

		if ($file->isFile() && $this->type == 'file') {
			return TRUE;
		}

		if ($file->isDir() && $this->type == 'dir') {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param  File  $file
	 * @return bool
	 */
	public function apply(File $file)
	{
		if (!$this->isDesiredType($file) || !preg_match($this->pattern, $file->getPathname())) {
			return FALSE;
		}

		chown($file, $this->owner);

		if ($file->isFile()) {
			chmod($file, octdec($this->modes[0]));
		}

		if ($file->isDir()) {
			chmod($file, octdec($this->modes[1]));
		}

		return TRUE;
	}
}
