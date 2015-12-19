<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

use JuniWalk\Darwin\FixCommand;
use Symfony\Component\Console\Application;


// Include vendor autoloader to access projects
include __DIR__.'/../../../autoload.php';


$darwin = new Application('Darwin');
$darwin->add(new FixCommand);
$darwin->run();
