<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tools;

final class Rule
{
	private $pattern;
	private $type;
	private $owner;
	private $modes;


	/**
	 * @param string  $pattern
	 * @param string  $type
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
	 * @return array
	 */
	public function toArray()
	{
		return get_object_vars($this);
	}


	/**
	 * @param  \SplFileInfo  $file
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

		chmod($file, $this->getMode($file));
		chown($file, $this->getOwner());

		return true;
	}


	/**
	 * @return string
	 */
	public function getOwner()
	{
		return $this->owner;
	}


	/**
	 * @param  \SplFileInfo  $file
	 * @return integer
	 */
	public function getMode(\SplFileInfo $file)
	{
		$mode = $this->modes[1];

		if ($file->isFile()) {
			$mode = $this->modes[0];
		}

		return octdec($mode);
	}


	/**
	 * @param  \SplFileInfo  $file
	 * @return bool
	 */
	private function checkType(\SplFileInfo $file)
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
}
