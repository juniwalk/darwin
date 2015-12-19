<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Exception;

use Symfony\Component\Console\Exception\ExceptionInterface;

final class TerminateException extends \RuntimeException implements ExceptionInterface
{
}
