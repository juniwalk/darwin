<?php

/**
 * TEST: Darwin application functonality.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tests\Cases;

use JuniWalk\Darwin\Darwin;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

final class DarwinTest extends \Tester\TestCase
{
	public function testHome()
	{
		$darwin = $this->createDarwin();
		$darwin->setHome('~/darwin');

		Assert::same('/darwin', $darwin->getHome());
	}


	/**
	 * @return Darwin
	 */
	private function createDarwin()
	{
		return new Darwin('Darwin', 'dev-master');
	}
}

(new DarwinTest)->run();
