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

$darwin->add(new Commands\CleanBackupCommand);
$darwin->add(new Commands\CleanCacheCommand);
$darwin->add(new Commands\CodeDeployCommand);
$darwin->add(new Commands\CodeWarmupCommand);
$darwin->add(new Commands\FilePermissionCommand);
$darwin->add(new Commands\GitChangelogCommand);
$darwin->add(new Commands\ImageShrinkCommand);
$darwin->add(new Commands\ImageRestoreCommand);
$darwin->add(new Commands\WebLockCommand);
$darwin->add(new Commands\WebUnlockCommand);

$darwin->run();
