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
    public function testBasic()
    {
        // Get the Darwin instance
        $darwin = new Darwin();

        // Assert custom added methods of this application
        $this->assertSame('Darwin', $darwin->getName());
        $this->assertSame('dev-master b633e81', $darwin->getVersion());
        $this->assertSame('juniwalk/darwin', $darwin->getPackage());
    }
}
