<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

use JuniWalk\Darwin\Commands;
use JuniWalk\Darwin\CommandLoader;
use Symfony\Component\Console\Application;

if (!@include __DIR__.'/../../../autoload.php') {
	throw new Exception('Composer autoloader not found.');
}

$commandLoader = new CommandLoader([
	// Clean commands
	Commands\CleanBackupCommand::class,
	Commands\CleanCacheCommand::class,
	Commands\CleanLogsCommand::class,
	Commands\CleanSessionsCommand::class,

	// Code commands
	Commands\CodeChangelogCommand::class,
	Commands\CodeDeployCommand::class,
	Commands\CodeInstallCommand::class,
	Commands\CodePullCommand::class,
	Commands\CodeWarmupCommand::class,

	// Image commands
	Commands\ImageShrinkCommand::class,
	Commands\ImageRestoreCommand::class,

	// Make commands
	Commands\MakeCloseCommand::class,
	Commands\MakeLockedCommand::class,
	Commands\MakeOpenCommand::class,
	Commands\MakeUnlockedCommand::class,

	// Schema commands
	Commands\SchemaDiffCommand::class,
	Commands\SchemaDumpCommand::class,
	Commands\SchemaMigrateCommand::class,
]);

$darwin = new Application('Darwin', 'dev-master');
$darwin->setCommandLoader($commandLoader);
$darwin->run();
