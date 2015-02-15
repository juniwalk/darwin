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
    /**
     * Create symlinks for needed files.
     */
    protected function setUp()
    {
        symlink(__DIR__.'/../Resources/composer.lock', '/home/travis/composer.lock');
    }


    /**
     * Delete all created symlinks.
     */
    protected function tearDown()
    {
        // Clear the garbahe after tests
        unlink('/home/travis/composer.lock');
    }


    /**
     * Command - Test command with all arguments.
     */
    public function testExecuteAll()
    {
        // Execute Fix test with all possible arguments
        $tester = static::execute('fix', [
            'dir' => realpath(__DIR__.'/../../res'),
            '--owner' => 'root',
            '--force' => true
        ]);

        // Print the output to check
        //echo $tester->getDisplay();

        //$this->assertRegExp('/.../', );
    }


    /**
     * Command - Test command without forcing.
     *
     * @expectedException \ErrorException
     */
    public function testNoForce()
    {
        // Execute Fix test without forcing it
        $tester = static::execute('fix', [
            'dir' => realpath(__DIR__.'/../../res'),
            '--owner' => 'root',
            '--force' => false
        ]);
    }

    /**
     * Execute command in controlled enviroment.
     *
     * @param  string  $name   Command name
     * @param  array   $input  Input arguments
     * @return CommandTester
     */
    protected static function execute($name, array $input = [])
    {
        // Create new Darwin instance
        $darwin = new Darwin();

        // Try to find desired command in Darwin
        $command = $darwin->find($name);

        // If there is no such command
        if (empty($command)) {
            throw new \ErrorException('Command '.$name.' was not found.');
        }

        // Create Command Tester and execute command
        $tester = new CommandTester($command);
        $tester->execute($input, ['interactive' => false]);

        // Return tester instance
        // for additional work
        return $tester;
    }
}
