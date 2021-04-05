<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Exception\ConfigInvalidException;
use JuniWalk\Darwin\Exception\ConfigNotFoundException;
use JuniWalk\Darwin\Tools\Rule;
use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\DI\Config\Loader as ConfigLoader;
use Nette\FileNotFoundException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Schema\Processor as ConfigValidator;
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
		$file = CWD.'/.darwinrc';

		$configLoader = new ConfigLoader;
		$configLoader->addAdapter('darwinrc', NeonAdapter::class);
		$configLoader->setParameters([
			'projectName' => CWD_NAME,
			'presetPath' => DARWIN_HOME_PATH.'/preset',
			'darwinPath' => DARWIN_HOME_PATH,
		]);

		try {
			$config = (new ConfigValidator)->process(
				$this->getConfigSchema(),
				$configLoader->load($file)
			);

		} catch (FileNotFoundException $e) {
			throw new ConfigNotFoundException($e->getMessage(), $e->getCode());

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
	 * @return Schema
	 */
	private function getConfigSchema(): Schema
	{
		return Expect::structure([
			'includes' => Expect::listOf(
				Expect::string()->assert('is_dir')
			),
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
