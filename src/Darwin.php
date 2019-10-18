<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Helpers\ConfigHelper;
use Symfony\Component\Console\Application;

final class Darwin extends Application
{
	/** @var string */
	private $home;


	/**
	 * @param  string  $home
	 * @return void
	 */
	public function setHome(string $home): void
	{
		$serverHome = null;

		if (isset($_SERVER['HOME'])) {
			$serverHome = $_SERVER['HOME'];
		}

		$this->home = str_replace('~', $serverHome, $home);
	}


	/**
	 * @return string
	 */
	public function getHome(): string
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
