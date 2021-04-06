<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Nette\DI\Config;
use Nette\DI\Config\Adapters\NeonAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MakeConfigCommand extends AbstractCommand
{
	/** @var string */
	protected static $defaultDescription = 'Create darwin configuration file';
	protected static $defaultName = 'make:config';

	/** @var string */
	private $type;

	/** @var string[] */
	private $types = [];


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);

		$this->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type of the config to be created');
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$this->type = $input->getOption('type');
		$this->types = glob(DARWIN_PATH.'/preset/*');
		$this->types = array_map('basename', $this->types);

		if ($this->type && in_array($this->type, $this->types)) {
			$input->setInteractive(false);
		}

		parent::initialize($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function interact(InputInterface $input, OutputInterface $output): void
	{
		$this->addQuestion(function($cli) {
			$this->type = $cli->choose('Which config would you like to use?', $this->types);
		});

		parent::interact($input, $output);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$status = new StatusIndicator($output);
		$output->writeln('');
		$config = [
			'includes' => [
				'%presetPath%/'.$this->type,
			],
		];

		$status->setMessage('Writing <comment>.'.CONFIG_NAME.'</> file');
		$status->execute(function($status) use ($config) {
			$loader = new Config\Loader;
			$loader->addAdapter(CONFIG_NAME, NeonAdapter::class);
			$loader->save($config, CONFIG_FILE);
		});

		$output->writeln('');
		return Command::SUCCESS;
	}
}
