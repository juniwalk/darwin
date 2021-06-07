<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Commands;

use JuniWalk\Darwin\Tools\StatusIndicator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MakeYarnrcCommand extends AbstractCommand
{
	/** @var string */
	const VENDOR_PATH = 'www/vendor';
	const YARNRC_FILE = '.yarnrc';

	/** @var string */
	protected static $defaultDescription = 'Create .yarnrc file from current working directory';
	protected static $defaultName = 'make:yarnrc';


	/**
	 * @return void
	 */
	protected function configure(): void
	{
		$this->setDescription(static::$defaultDescription);
		$this->setName(static::$defaultName);
		$this->setAliases(['yarn']);
	}


	/**
	 * @param  InputInterface  $input
	 * @param  OutputInterface  $output
	 * @return void
	 */
	protected function initialize(InputInterface $input, OutputInterface $output): void
	{
		$hasYarnrcFile = $this->exec('test', '-e', self::YARNRC_FILE);

		if ($hasYarnrcFile === Command::FAILURE) {
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
			return $cli->confirm('File <comment>'.self::YARNRC_FILE.'</> already exists, overwrite?');
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

		$status->setMessage('Creating <comment>'.self::YARNRC_FILE.'</> file');
		$code = $status->execute(function($status) {
			$content = $this->createYarnFile(self::VENDOR_PATH);
			$file = getcwd().'/'.self::YARNRC_FILE;

			return $this->exec('echo', '"'.$content.'"', '>', $file);
		});

		$output->writeln('');
		return $code;
	}


	/**
	 * @param  string  $assetsDir
	 * @return string
	 */
	public function createYarnFile(string $assetsDir): string
	{
		return implode(PHP_EOL, [
			'# ./'.self::YARNRC_FILE,
			'--modules-folder '.getcwd().'/'.$assetsDir,
			'env:',
			'  NODE_PATH "'.getcwd().'/'.$assetsDir.'"',
		]);
	}
}
