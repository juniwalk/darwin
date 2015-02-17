<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\Command\FixCommand;
use JuniWalk\Darwin\Command\GcCommand;
use JuniWalk\Darwin\Command\InstallCommand;
use JuniWalk\Darwin\Darwin;


// Include vendor autoloader to access projects
include __DIR__.'/../../../autoload.php';


// Initialize Darwin
$darwin = new Darwin(__DIR__);

// Insert available commands
$darwin->add(new FixCommand);
$darwin->add(new GcCommand);
$darwin->add(new InstallCommand);

// Run Darwin Application
$darwin->run();
