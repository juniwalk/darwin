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
use Symfony\Component\Console\Application;

class Darwin extends Application
{
    /**
     * Initialize Darwin application.
     */
    public function __construct()
    {
        // Set the name of application
        parent::__construct('Darwin');
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
