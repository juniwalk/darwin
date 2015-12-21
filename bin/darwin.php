<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

// Include vendor autoloader to access projects
include __DIR__.'/../../../autoload.php';

$darwin = new JuniWalk\Darwin\Darwin;
$darwin->add(new JuniWalk\Darwin\Command\FixCommand);
$darwin->run();
