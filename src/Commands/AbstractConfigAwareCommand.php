<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractConfigAwareCommand extends AbstractCommand
{
	/** @var Configuration */
	private $config;


	/**
	 * @return Configuration
	 */
	public function getConfig(): Configuration
	{
		return $this->config;
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->config = new Configuration;
		parent::initialize($input, $output);
	}
}
