<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\Command\FixCommand;
use JuniWalk\Darwin\Darwin;


// Include vendor autoloader to access projects
include __DIR__.'/../../../autoload.php';


$darwin = new Darwin;
$darwin->add(new FixCommand);
$darwin->run();
