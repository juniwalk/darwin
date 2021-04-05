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

define('DARWIN_HOME_PATH', realpath(__DIR__.'/../'));
define('CWD', getcwd());
define('CWD_NAME', basename(CWD));

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
	new Commands\MakeLockedCommand,
	new Commands\MakeOpenCommand,
	new Commands\MakeUnlockedCommand,

	// Schema commands
	new Commands\SchemaDiffCommand,
	new Commands\SchemaDumpCommand,
	new Commands\SchemaMigrateCommand,
]);

$darwin->run();
