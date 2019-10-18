<?php declare(strict_types=1);

/**
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
	/** @var Application */
	private $application;

	/** @var HelperSet */
	private $helperSet;

	/** @var string[] */
	private $exclude = [];

	/** @var Rule[] */
	private $rules = [];


	/**
	 * @param Application  $application
	 */
	public function __construct(Application $application)
	{
		$this->application = $application;
	}


	/**
	 * @param  HelperSet|null  $helperSet
	 * @return void
	 */
	public function setHelperSet(HelperSet $helperSet = null): void
	{
		$this->helperSet = $helperSet;
	}


	/**
	 * @return HelperSet
	 */
	public function getHelperSet(): HelperSet
	{
		return $this->helperSet;
	}


	/**
	 * @return string
	 */
	public function getHome(): string
	{
		return $this->application->getHome();
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'config';
	}


	/**
	 * @return string[]
	 */
	public function getExcludeFolders(): iterable
	{
		return $this->exclude;
	}


	/**
	 * @return Rule[]
	 */
	public function getRules(): iterable
	{
		return $this->rules;
	}


	/**
	 * @param  string  $fileName
	 * @return bool
	 * @throws ConfigInvalidException
	 * @throws ConfigNotFoundException
	 */
	public function load(string $fileName): bool
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

		return true;
	}
}
