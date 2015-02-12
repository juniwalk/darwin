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

class Darwin extends \Symfony\Component\Console\Application
{
    /**
     * Initialize Darwin application.
     */
    public function __construct()
    {
        // Set the name of application
        parent::__construct('Darwin', 'v0.9');

        // Set description of this CLI application
        $this->definition = 'Set of tools to help you with your project.';
    }


    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[] An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Get default parent commands and add new
        $cmds = parent::getDefaultCommands();
        $cmds[] = new FixCommand();

        // Return commands
        return $cmds;
    }
}
