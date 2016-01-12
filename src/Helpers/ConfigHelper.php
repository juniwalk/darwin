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
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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
     * @param HelperSet  $helperSet
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
	public function getName()
	{
		return 'config';
	}


	/**
	 * @param  string  $fileName
	 * @return array
	 */
	public function load($fileName)
	{
		$config = $this->application->getHome() .'/'. $fileName;

		if (!file_exists($config) && !touch($config)) {
			throw new \Exception;
		}

		$config = file_get_contents($config);

		try {
			return (array) Yaml::parse($config);

		} catch (ParseException $e) {
			throw $e; // for now
		}
	}


	public function save($fileName, array $data)
	{

	}


	/**
	 *
	 */
	private function createFolder()
	{
		mkdir($this->application->getHome());
	}
}
