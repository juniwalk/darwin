#!/usr/bin/env php
<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

// Register phars manifests
Phar::mapPhar('darwin.phar');

// Include bootstrap file of the Darwin application
include 'phar://darwin.phar/juniwalk/darwin/bin/darwin.php';

__HALT_COMPILER();
