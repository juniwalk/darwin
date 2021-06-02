<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2021
 * @license   MIT License
 */

use JuniWalk\Darwin\Commands;
use Symfony\Component\Console\Application;

if (!@include __DIR__.'/../../../autoload.php') {
	throw new Exception('Composer autoloader not found.');
}

define('WORKING_DIR', getcwd());
define('DARWIN_PATH', realpath(__DIR__.'/../'));
define('CONFIG_NAME', 'darwinrc');
define('CONFIG_FILE', WORKING_DIR.'/.'.CONFIG_NAME);

$darwin = new Application('Darwin', 'dev-master');
$darwin->addCommands([
	// Clean commands
	new Commands\CleanBackupCommand,
	new Commands\CleanCacheCommand,
	new Commands\CleanLogsCommand,
	new Commands\CleanSessionsCommand,

	// Code commands
	new Commands\CodeChangelogCommand,
	new Commands\CodeDeployCommand,
	new Commands\CodeInstallCommand,
	new Commands\CodePullCommand,
	new Commands\CodeWarmupCommand,

	// Image commands
	new Commands\ImageShrinkCommand,
	new Commands\ImageRestoreCommand,

	// Make commands
	new Commands\MakeCloseCommand,
	new Commands\MakeConfigCommand,
	new Commands\MakeLockedCommand,
	new Commands\MakeOpenCommand,
	new Commands\MakeUnlockedCommand,
	new Commands\MakeYarnrcCommand,

	// Schema commands
	new Commands\SchemaDiffCommand,
	new Commands\SchemaDumpCommand,
	new Commands\SchemaMigrateCommand,
]);

$darwin->run();
