<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\IO;

class Json extends \Nette\Utils\Json
{
    public static function decodeFile($path, $options = 0)
    {
        return static::decode(file_get_contents($path), $options);
    }
    public static function encodeFile($path, $value, $options = 0)
    {
        file_put_contents(static::encode($value, $options));
    }
}
