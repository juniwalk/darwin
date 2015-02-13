<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tests;

use JuniWalk\Darwin\Darwin;
use Symfony\Component\Console\Tester\CommandTester;

class FixCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $darwin = new Darwin();

        $command = $dar->find('fix');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

echo $commandTester->getDisplay();

        //$this->assertRegExp('/.../', );
    }
}
