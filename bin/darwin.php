<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\Darwin;
use JuniWalk\Darwin\Commands;

if (!@include __DIR__.'/../../../autoload.php') {
	throw new Exception('Composer autoloader not found.');
}

$darwin = new Darwin('Darwin', 'dev-master');
$darwin->setHome('~/.config/darwin');

// Clean commands
$darwin->add(new Commands\CleanBackupCommand);
$darwin->add(new Commands\CleanCacheCommand);
$darwin->add(new Commands\CleanLogsCommand);
$darwin->add(new Commands\CleanSessionsCommand);

// Code commands
$darwin->add(new Commands\CodeChangelogCommand);
$darwin->add(new Commands\CodeDeployCommand);
$darwin->add(new Commands\CodeInstallCommand);
$darwin->add(new Commands\CodePullCommand);
$darwin->add(new Commands\CodeWarmupCommand);

$darwin->add(new Commands\FilePermissionCommand);

// Image commands
$darwin->add(new Commands\ImageShrinkCommand);
$darwin->add(new Commands\ImageRestoreCommand);

// Schema commands
$darwin->add(new Commands\SchemaDiffCommand);
$darwin->add(new Commands\SchemaDumpCommand);
$darwin->add(new Commands\SchemaMigrateCommand);

// Web commands
$darwin->add(new Commands\WebLockCommand);
$darwin->add(new Commands\WebUnlockCommand);

$darwin->run();
