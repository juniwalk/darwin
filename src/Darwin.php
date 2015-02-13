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
use JuniWalk\Darwin\Command\InstallCommand;

class Darwin extends \Symfony\Component\Console\Application
{
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
        // If there is no app name
        if (!isset($this->name)) {
            // Get the name from this class without namespaces
            $this->name = basename(strtr(__CLASS__, '\\', '/'));
        }

        return $this->name;
    }


    /**
     * Current version.
     *
     * @return string
     */
    public function getVersion()
    {
        // If the version is unknown
        if (!isset($this->version)) {
            // For now return this, in future we will
            // parse verison out of composer.lock file
            $this->version = 'v0.9';
        }

        return $this->version;
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
        $cmds[] = new InstallCommand();

        // Return commands
        return $cmds;
    }
}
