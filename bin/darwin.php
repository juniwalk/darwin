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
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;


// Include vendor autoloader to access projects
include __DIR__.'/../../../autoload.php';


$dispatcher = new EventDispatcher();
$dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
	//$event->getOutput()->writeln(PHP_EOL);
});


$darwin = new Darwin;
$darwin->setDispatcher($dispatcher);
$darwin->add(new FixCommand);
$darwin->run();
