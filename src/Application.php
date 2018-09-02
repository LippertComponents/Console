<?php

namespace LCI\MODX\Console;

use LCI\MODX\Console\Command\RefreshCache;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var \modX
     */
    protected static $modx;

    protected static $logo = 'art/console.txt';

    protected static $name = 'Console Console';

    protected static $version = '1.0.0 pl';

    /** @var Console */
    protected $console;

    public function __construct(Console $console)
    {
        $this->console = $console;

        parent::__construct(self::$name, self::$version);
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return file_get_contents(__DIR__ . '/' .static::$logo). parent::getHelp();
    }

    /**
     *
     */
    public function loadCommands()
    {
        /** @var LCI\MODX\Console\Command\RefreshCache $refresh */
        $refresh = new RefreshCache();
        $this->add($refresh->setConsole($this->console));

        $commands = $this->console->getCommands();

        foreach ($commands as $command) {
            $class = new $command;
            if (is_object($class) && method_exists($class, 'setConsole')) {
                $class->setConsole($this->console);
            }
            $this->add($class);
        }

        $package_commands = $this->console->getPackageCommands();

        foreach ($package_commands as $package_command) {
            /** @var LCI\MODX\Console\Command\PackageCommands $class */
            try {
                $class = new $package_command($this->console);

                if ($class instanceof PackageCommands) {
                    $class->loadActiveCommands($this);
                }
            } catch (\Exception $exception) {
                // @TODO log error
            }
        }
    }

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputOption('--help',           '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            //new InputOption('--verbose',        '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.'),
            new InputOption('--version',        '-V', InputOption::VALUE_NONE, 'Display the LCI MODX Console version.'),
        ));
    }
}