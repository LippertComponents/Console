<?php

namespace LCI\MODX\Console;

use LCI\MODX\Console\Command\CustomEnvDirectory;
use LCI\MODX\Console\Command\PackageCommands;
use LCI\MODX\Console\Command\RefreshCache;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var \modX
     */
    protected static $modx;

    // @see http://patorjk.com/software/taag/#p=display&f=Slant&t=Console
    protected static $logo = 'art/console.txt';

    protected static $name = 'Console Console';

    protected static $version = '1.1.5 pl';

    /** @var Console */
    protected $console;

    public function __construct(Console $console)
    {
        $this->console = $console;

        parent::__construct(static::$name, static::$version);
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->getArt() . parent::getHelp();
    }

    /**
     *
     */
    public function loadCommands()
    {
        /** @var \LCI\MODX\Console\Command\CustomEnvDirectory $env */
        $env = new CustomEnvDirectory();
        $this->add($env->setConsole($this->console));

        /** @var \LCI\MODX\Console\Command\RefreshCache $refresh */
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
            /** @var \LCI\MODX\Console\Command\PackageCommands $class */
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
     * @return bool|string
     */
    protected function getArt()
    {
        if (file_exists(static::$logo)) {
            return file_get_contents(static::$logo);
        }

        return '';
    }
}