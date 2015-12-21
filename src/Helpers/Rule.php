<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Helpers;

final class Darwin
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
}
