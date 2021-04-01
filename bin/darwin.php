<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\Darwin;
use JuniWalk\Darwin\Command\CodeDeployCommand;
use JuniWalk\Darwin\Command\BackupCleanCommand;
use JuniWalk\Darwin\Command\FilePermissionCommand;
use JuniWalk\Darwin\Command\GitChangelogCommand;
use JuniWalk\Darwin\Command\ImageShrinkCommand;
use JuniWalk\Darwin\Command\ImageRestoreCommand;

if (!@include __DIR__.'/../../../autoload.php') {
	throw new Exception('Composer autoloader not found.');
}

$darwin = new Darwin('Darwin', 'dev-master');
$darwin->setHome('~/.config/darwin');

$darwin->add(new CodeDeployCommand);
$darwin->add(new BackupCleanCommand);
$darwin->add(new FilePermissionCommand);
$darwin->add(new GitChangelogCommand);
$darwin->add(new ImageShrinkCommand);
$darwin->add(new ImageRestoreCommand);

$darwin->run();
