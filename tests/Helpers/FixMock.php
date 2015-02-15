<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Tests\Helpers;

use JuniWalk\Darwin\Command\FixCommand;

class FixMock extends FixCommand
{
    /**
     * Set new permissions mode.
     *
     * @param  string      $path   Path to file or dir
     * @param  int|string  $owner  New owner name/id
     * @return bool
     */
    protected function setOwner($path, $owner)
    {
        return true;
    }
}
