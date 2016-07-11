<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\Darwin;
use JuniWalk\Darwin\Command\FilePermissionCommand;
use JuniWalk\Darwin\Command\ImageShringCommand;
use JuniWalk\Darwin\Command\ImageRestoreCommand;

if (!@include __DIR__.'/../../../autoload.php') {
	throw new \Exception('Composer autoloader not found.');
}

$darwin = new Darwin('Darwin', 'dev-master');
$darwin->setHome('~/.config/darwin');

$darwin->add(new FilePermissionCommand);
$darwin->add(new ImageShringCommand);
$darwin->add(new ImageRestoreCommand);

$darwin->run();
