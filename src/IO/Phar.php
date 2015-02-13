<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\IO;

class Phar extends \Phar
{
    /**
     * Path to opened file.
     *
     * @var string
     */
    protected $file;


    /**
     * Initialize new Phar archive.
     *
     * @param string  $file   Path to Phar file
     * @param int     $flags  Option flags
     * @param string  $alias  Alias of the phar file
     */
    public function __construct($file, $flags = null, $alias = null)
    {
        // Try to unlink any old phar file
        @unlink($file);

        // If there are no flags
        if (empty($flags)) {
            // Set default flags of the FilesystemIterator class
            $flags = static::KEY_AS_PATHNAME | static::CURRENT_AS_FILEINFO;
        }

        // Store file in property
        $this->file = $file;

        // If there is no alias
        if (empty($alias)) {
            // Get the name of destination file
            $alias = basename($this->file);
        }

        // Make sure that we have called parent constructor
        parent::__construct($this->file, $flags, $alias);

        // Set default algorithm and enable buffering
        $this->setSignatureAlgorithm(static::SHA1);
        $this->startBuffering();
    }


    /**
     * Garbage collect the archive.
     */
    public function __destruct()
    {
        // Disable buffering
        $this->stopBuffering();
    }


    /**
     * Get the name of source file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * Add new file into archive.
     *
     * @param SplFileInfo  $file  File to store
     * @param string       $name  Local file name
     */
    public function add(\SplFileInfo $file, $name = null)
    {
        // If there is no local name
        if (empty($name)) {
            // Get the local name from the real path of the file
            $root = dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR;
            $name = str_replace($root, '', $file->getRealPath());
            $name = strtr($name, '\\', '/');
        }

        // Get the file constents and remove whitespace
        $content = file_get_contents($file);
        $content = $this->stripWhitespace($content);

        // Insert the file into Phar as s string
        return parent::addFromString($name, $content);
    }
    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }
}
