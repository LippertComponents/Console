<?php

namespace LCI\MODX\Console\Command;

use LCI\MODX\Console\Console;

interface PackageCommands
{
    public function __construct(Console $console);

    /**
     * @return array ~ of Fully qualified names of all command class
     */
    public function getAllCommands();

    /**
     * @return array ~ of Fully qualified names of active command classes. This could differ from all if package creator
     *      has different commands based on the state like the DB. Example has Install and Uninstall, only one would
     *      be active/available depending on the state
     */
    public function getActiveCommands();

    /**
     * @param \LCI\MODX\Console\Command\Application $application
     * @return \LCI\MODX\Console\Command\Application
     */
    public function loadActiveCommands(Application $application);
}