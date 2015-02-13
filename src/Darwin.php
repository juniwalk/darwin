<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin;

use JuniWalk\Darwin\Command\FixCommand;
use JuniWalk\Darwin\Command\SelfInstallCommand;
use JuniWalk\Darwin\Command\SelfUpdateCommand;

class Darwin extends \Symfony\Component\Console\Application
{
    /**
     * Package name information.
     *
     * @return string
     */
    protected $package;


    /**
     * Initialize Darwin application.
     */
    public function __construct()
    {
        // Set the name of application
        parent::__construct($this->getName(), $this->getVersion());
    }


    /**
     * Name of this application.
     *
     * @return string
     */
    public function getName()
    {
        // Get the name from parent
        $name = parent::getName();

        // If there is no app name
        if (!isset($name)) {
            // Get the name from this class without namespaces
            $name = basename(strtr(__CLASS__, '\\', '/'));
            $this->setName($name);
        }

        return $name;
    }


    /**
     * Current version.
     *
     * @return string
     */
    public function getVersion()
    {
        // Get the version from parent
        $version = parent::getVersion();

        // If the version is unknown
        if (!isset($version)) {
            // Load real version from the composer.lock
            $version = $this->getRealVersion();
            $this->setVersion($version);
        }

        return $version;
    }


    /**
     * Get package information.
     *
     * @return stdClass
     */
    public function getPackage()
    {
        return $this->package;
    }


    /**
     * Gets the default commands that should always be available.
     *
     * @return array
     */
    protected function getDefaultCommands()
    {
        // Get default parent commands and add new
        $cmds = parent::getDefaultCommands();
        $cmds[] = new FixCommand();

        // Self commands
        $cmds[] = new SelfInstallCommand();
        $cmds[] = new SelfUpdateCommand();

        // Return commands
        return $cmds;
    }


    /**
     * Parse version of the package from composer.lock.
     *
     * @return string
     */
    protected function getRealVersion()
    {
        // Load composer.lock and composer.json contents and parse them
        $json = $this->loadJsonFile(__DIR__.'/../composer.json');
        $lock = $this->loadJsonFile($this->getHome().'/composer.lock');

        // Filter packages, get just the one with name of this package
        $package = array_filter($lock->packages, function($v) use ($json) {
            // Compare the name of packages
            return $v->name == $json->name;
        });

        // Get the version of the package
        // and store package name in holder
        $this->package = $package[0]->name;
        $version = $package[0]->version;

        // If the version is one of dev-[branchname]
        if (preg_match('/dev-(\w+)/i', $version)) {
            // Add first 7 characters of the commit this build references
            $version .= ' '.substr($package[0]->source->reference, 0, 7);
        }

        return $version;
    }


    /**
     * Get path to home directory.
     *
     * @return string
     */
    protected function getHome()
    {
        // Return path to home directory which
        // should be /root/.composer project
        return realpath(__DIR__.'/../../../..');
    }


    /**
     * Decode JSON file.
     *
     * @param  string  $file   Path to JSON file
     * @param  bool    $assoc  Return array?
     * @return stdClass|array
     */
    protected function loadJsonFile($file, $assoc = false)
    {
        // Load contents of the JSON file and decode them
        return json_decode(file_get_contents($file), $assoc);
    }
}
