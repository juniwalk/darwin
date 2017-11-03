<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Helpers;

use JuniWalk\Darwin\Exception\ConfigInvalidException;
use JuniWalk\Darwin\Exception\ConfigNotFoundException;
use JuniWalk\Darwin\Tools\Rule;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Nette\Neon\Neon;

final class ConfigHelper implements HelperInterface
{
	/**
	 * @var Application
	 */
	private $application;

	/**
	 * @var HelperSet
	 */
	private $helperSet;

	/**
	 * @var string[]
	 */
	private $exclude = [];

	/**
	 * @var Rule[]
	 */
	private $rules = [];


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
	 * @return string[]
	 */
	public function getExcludeFolders()
	{
		return $this->exclude;
	}


	/**
	 * @return Rule[]
	 */
	public function getRules()
	{
		return $this->rules;
	}


	/**
	 * @param  string  $fileName
	 * @return bool
	 * @throws ConfigInvalidException
	 * @throws ConfigNotFoundException
	 */
	public function load($fileName)
	{
		$file = $this->getHome().'/'.$fileName.'.neon';

		if (!is_file($file) || !$content = file_get_contents($file)) {
			throw ConfigNotFoundException::fromFileName($fileName);
		}

		$config = (array) Neon::decode($content);

		if (!isset($config['excludeFolders']) || !isset($config['rules'])) {
			throw ConfigInvalidException::fromFileName($fileName);
		}

		$this->exclude = $config['excludeFolders'];

		foreach ($config['rules'] as $rule => $data) {
			$this->rules[$rule] = new Rule(
				$data['pattern'],
				$data['type'],
				$data['owner'],
				$data['mode']
			);
		}

		return TRUE;
	}
}
