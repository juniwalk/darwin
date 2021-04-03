<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Exception\ConfigInvalidException;
use JuniWalk\Darwin\Exception\ConfigNotFoundException;
use JuniWalk\Darwin\Tools\Rule;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

final class Configuration
{
	/** @var string */
	private $sessionDir;

	/** @var string */
	private $loggingDir;

	/** @var string[] */
	private $cacheDirs;

	/** @var string[] */
	private $lockingFiles;

	/** @var string[] */
	private $excludePaths;

	/** @var Rule[] */
	private $rules;


	/**
	 * @throws ConfigNotFoundException
	 * @throws ConfigInvalidException
	 */
	public function __construct()
	{
		try {
			$config = (new Processor)->process(
				$this->getConfigSchema(),
				$this->getConfigData()
			);

		} catch (ValidationException $e) {
			throw new ConfigInvalidException($e->getMessage(), $e->getCode());
		}

		foreach ($config as $key => $value) {
			$this->{$key} = $value;
		}

		foreach ($this->rules as $pattern => $params) {
			$this->rules[$pattern] = new Rule($pattern, 'any', $params->owner, $params->mode);
		}
	}


	/**
	 * @return string|null
	 */
	public function getSessionDir(): ?string
	{
		return $this->sessionDir;
	}


	/**
	 * @return string|null
	 */
	public function getLoggingDir(): ?string
	{
		return $this->loggingDir;
	}


	/**
	 * @return string[]
	 */
	public function getCacheDirs(): iterable
	{
		return $this->cacheDirs;
	}


	/**
	 * @return string
	 */
	public function getLockFile(): string
	{
		return $this->lockingFiles->lock;
	}


	/**
	 * @return string
	 */
	public function getUnlockFile(): string
	{
		return $this->lockingFiles->unlock;
	}


	/**
	 * @return string[]
	 */
	public function getExcludeFolders(): iterable
	{
		return $this->excludePaths;
	}


	/**
	 * @return Rule[]
	 */
	public function getRules(): iterable
	{
		return $this->rules;
	}


	/**
	 * @return string[]
	 * @throws ConfigNotFoundException
	 */
	private function getConfigData(): iterable
	{
		$file = getcwd().'/.darwinrc';

		if (!is_file($file) || !$content = file_get_contents($file)) {
			throw new ConfigNotFoundException;
		}

		return Neon::decode($content);
	}


	/**
	 * @return Schema
	 */
	private function getConfigSchema(): Schema
	{
		return Expect::structure([
			'sessionDir' => Expect::string()->assert('is_dir'),
			'loggingDir' => Expect::string()->assert('is_dir'),
			'cacheDirs' => Expect::listOf(
				Expect::string()->assert('is_dir')
			),
			'lockingFiles' => Expect::structure([
				'lock' => Expect::string('www/lock.phtml'),
				'unlock' => Expect::string('www/lock.off'),
			]),
			'excludePaths' => Expect::listOf(
				Expect::string()->assert('is_dir')
			),
			'rules' => Expect::arrayOf(
				Expect::structure([
					'owner' => Expect::string(),
					'mode' => Expect::listOf('int|null')->min(2)->max(2),
				])
			)
		]);
	}
}
