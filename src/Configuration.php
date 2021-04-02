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
	private $params;


	/**
	 * @throws ConfigNotFoundException
	 * @throws ConfigInvalidException
	 */
	public function __construct()
	{
		$schema = $this->getConfigSchema();
		$config = $this->getConfigData();

		try {
			$this->params = (new Processor)->process($schema, $config);

		} catch (ValidationException $e) {
			throw new ConfigInvalidException($e->getMessage(), $e->getCode());
		}
	}


	/**
	 * @return string[]
	 */
	public function getExcludeFolders(): iterable
	{
		return $this->params->permissions->excludePaths;
	}


	/**
	 * @return Rule[]
	 */
	public function getRules(): iterable
	{
		$rules = [];

		foreach ($this->params->permissions->rules as $pattern => $params) {
			$rules[] = new Rule($pattern, 'any', $params->owner, $params->mode);
		}

		return $rules;
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

		return (array) Neon::decode($content);
	}


	/**
	 * @return Schema
	 */
	private function getConfigSchema(): Schema
	{
		return Expect::structure([
			'paths' => Expect::structure([
				'sessionDir' => Expect::string()->assert('is_dir'),
				'logDir' => Expect::string()->assert('is_dir'),
				'cacheDirs' => Expect::listOf(
					Expect::string()->assert('is_dir')
				),
			]),
			'security' => Expect::structure([
				'lock' => Expect::string(),
				'unlock' => Expect::string(),
			]),
			'permissions' => Expect::structure([
				'excludePaths' => Expect::listOf(
					Expect::string()->assert('is_dir')
				),
				'rules' => Expect::arrayOf(
					Expect::structure([
						'owner' => Expect::string(),
						'mode' => Expect::listOf('int|null')->min(2)->max(2),
					])
				)
			]),
		]);
	}
}
