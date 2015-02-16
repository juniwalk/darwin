<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tests;

use JuniWalk\Darwin\Command\SelfInstallCommand;
use JuniWalk\Darwin\Darwin;
use Symfony\Component\Console\Tester\CommandTester;

class SelfInstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create symlinks for needed files.
     */
    protected function setUp()
    {
        symlink(__DIR__.'/../../res/composer.lock', '/home/travis/composer.lock');
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
     * Command - Install application.
     */
    public function testExecuteAll()
    {
        // Execute install script with custom path
        $tester = static::execute('self:install', [
            'path' => __DIR__.'/../../res'
        ]);

        // Check that the file was successfully created
        $this->assertFileExists(__DIR__.'/../../res/darwin');
    }


    /**
     * Command - Try to install app again.
     *
     * @expectedException \ErrorException
     */
    public function testWhenInstalled()
    {
        // Try to install again without forcing
        $tester = static::execute('self:install', [
            'path' => __DIR__.'/../../res',
            '--force' => false
        ]);
    }


    /**
     * Command - Force installation.
     */
    public function testForceInstall()
    {
        // Try to install application again over already installed one
        $tester = static::execute('self:install', [
            'path' => __DIR__.'/../../res',
            '--force' => true
        ]);

        // Check that the file was successfully created
        $this->assertFileExists(__DIR__.'/../../res/darwin');
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
        $darwin->add(new SelfInstallCommand);

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
