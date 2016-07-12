<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Helpers;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Nette\Neon\Neon;

final class ConfigHelper implements HelperInterface
{
	/** @var Application */
	private $application;

	/** @var HelperSet */
	private $helperSet;


	/**
	 * @param Application  $application
	 */
	public function __construct(Application $application)
	{
		$this->application = $application;
	}


	/**
	 * @param HelperSet|NULL  $helperSet
	 */
	public function setHelperSet(HelperSet $helperSet = NULL)
	{
		$this->helperSet = $helperSet;
	}


	/**
	 * @return HelperSet
	 */
	public function getHelperSet()
	{
		return $this->helperSet;
	}


	/**
	 * @return string
	 */
	public function getHome()
	{
		return $this->application->getHome();
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return 'config';
	}


	/**
	 * @param  string  $fileName
	 * @return array
	 * @throws Exception
	 */
	public function load($fileName)
	{
		$file = $this->getHome().'/'.$fileName;

		if (!file_exists($file) && !touch($file)) {
			throw new \Exception;
		}

		return (array) Neon::decode(file_get_contents($file));
	}
}
