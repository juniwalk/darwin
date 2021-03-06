<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Exception\ConfigInvalidException;
use JuniWalk\Darwin\Exception\ConfigNotFoundException;
use Nette\DI\Config;
use Nette\DI\Config\Adapters\NeonAdapter;
use Nette\DI\Helpers;
use Nette\FileNotFoundException;
use Nette\Schema;
use Nette\Schema\Expect;
use Nette\Schema\ValidationException;

final class Configuration
{
	/** @var string */
	private $sessionDir;

	/** @var string */
	private $loggingDir;

	/** @var string */
	private $assetsDir;

	/** @var string[] */
	private $cacheDirs;

	/** @var string[] */
	private $lockingFiles;

	/** @var string[] */
	private $deployCommands;

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
			$config = (new Schema\Processor)->process(
				$this->getConfigSchema(),
				$this->getConfigData()
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
			$this->rules[$pattern] = new Rule($pattern, $params->owner, $params->mode);
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
	 * @return string|null
	 */
	public function getAssetsDir(): ?string
	{
		return $this->assetsDir;
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
	public function getDeployCommands(): iterable
	{
		return $this->deployCommands;
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
	 */
	private function getConfigData(): iterable
	{
		$loader = new Config\Loader;
		$loader->addAdapter(CONFIG_NAME, NeonAdapter::class);
		$loader->setParameters($params = [
			'projectName' => basename(WORKING_DIR),
			'presetPath' => DARWIN_PATH.'/preset',
			'darwinPath' => DARWIN_PATH,
			'basePath' => WORKING_DIR,
		]);

		$data = $loader->load(CONFIG_FILE);
		$data = Helpers::expand($data, $params, true);

		return $data;
	}


	/**
	 * @return Schema\Schema
	 */
	private function getConfigSchema(): Schema\Schema
	{
		return Expect::structure([
			'sessionDir' => Expect::string(),
			'loggingDir' => Expect::string(),
			'assetsDir' => Expect::string(),
			'cacheDirs' => Expect::listOf(
				Expect::string(),
			),
			'lockingFiles' => Expect::structure([
				'lock' => Expect::string('www/lock.phtml'),
				'unlock' => Expect::string('www/lock.off'),
			]),
			'deployCommands' => Expect::arrayOf(
				Expect::arrayOf('string|int|bool')
			),
			'excludePaths' => Expect::listOf(
				Expect::string()
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
