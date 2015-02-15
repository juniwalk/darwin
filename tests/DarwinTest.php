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

class DarwinTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create symlinks for needed files.
     */
    protected function setUp()
    {
        symlink(__DIR__.'/../res/composer.lock', '/home/travis/composer.lock');
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
     * Application - Basic application test.
     */
    public function testBasic()
    {
        // Get the Darwin instance
        $darwin = new Darwin();

        // Assert custom added methods of this application
        $this->assertSame('Darwin', $darwin->getName());
        $this->assertSame('dev-master b633e81', $darwin->getVersion());
    }
}
