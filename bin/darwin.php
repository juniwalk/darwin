<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Binary;

use JuniWalk\Darwin\Darwin;
use JuniWalk\Darwin\Command\FixCommand;

// Include vendor autoloader to access projects
if (!@include __DIR__.'/../../../autoload.php') {
	throw new \Exception('Composer autoloader not found.');
}

$darwin = new Darwin('Darwin');
$darwin->setHome('~/.config/darwin');

$darwin->add(new FixCommand);

$darwin->run();
