<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\ConsoleEvents;

final class Darwin extends \Symfony\Component\Console\Application
{
    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'Darwin', $version = 'UNKNOWN')
    {
		$dispatcher = new EventDispatcher;
		$dispatcher->addListener(ConsoleEvents::TERMINATE, [$this, 'onTerminate']);

    	parent::__construct($name, $version);
		$this->setDispatcher($dispatcher);
    }


	/**
	 * @param ConsoleTerminateEvent $event
	 */
	public function onTerminate(ConsoleTerminateEvent $event)
	{
		$event->getOutput()->writeln(PHP_EOL);
	}
}
