<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

final class Darwin extends \Symfony\Component\Console\Application
{
    /**
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'Darwin', $version = 'UNKNOWN')
    {
    	parent::__construct($name, $version);
    }
}
