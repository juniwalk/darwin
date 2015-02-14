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
    public function __construct()
    {
        symlink(__DIR__.'/../Resources/composer.lock', '/home/travis/composer.lock');
    }


    public function __destruct()
    {
        // Clear the garbahe after tests
        unlink('/home/travis/composer.lock');
    }


    public function testExecute()
    {

        $darwin = new Darwin();

        $command = $darwin->find('fix');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            ['dir' => __DIR__, '--owner' => 'root'],
            ['interactive' => true]
        );

echo $commandTester->getDisplay();

        //$this->assertRegExp('/.../', );
    }
}
