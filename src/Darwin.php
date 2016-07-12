<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Helpers\ConfigHelper;

final class Darwin extends \Symfony\Component\Console\Application
{
	/** @var string */
	private $home;


	/**
	 * @param string  $home
	 */
	public function setHome($home)
	{
		$serverHome = NULL;

		if (isset($_SERVER['HOME'])) {
			$serverHome = $_SERVER['HOME'];
		}

		$this->home = str_replace('~', $serverHome, $home);
	}


	/**
	 * @return string
	 */
	public function getHome()
	{
		return $this->home;
	}


	/**
	 * @inheritDoc
	 */
	protected function getDefaultHelperSet()
	{
		$helpers = parent::getDefaultHelperSet();
		$helpers->set(new ConfigHelper($this));

		return $helpers;
	}
}
