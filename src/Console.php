<?php

namespace LCI\MODX\Console;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class Console
{
    const COMMANDS_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'commands.php';
    const ENV_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'env.php';
    const PACKAGE_COMMANDS_FILE = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'package_commands.php';

    /** @var \modX */
    public static $modx;

    /** @var array  */
    protected static $config = [];

    /** @var bool  */
    protected static $env_loaded = false;

    /** @var array */
    protected $commands = [];

    /** @var string */
    protected static $env_dir_path;

    /** @var array */
    protected $package_commands = [];

    /**
     * Console constructor.
     * @param string $dir ~ directory path where the .env file is located
     */
    public function __construct()
    {
        static::loadEnv();

        $this->loadConsoleCommands();
    }

    /**
     *
     */
    public static function loadEnv()
    {
         if (!static::$env_loaded) {
             $dir = dirname(__DIR__);
             $display_notice = false;

             static::loadCustomEnvDirectory();
             if (!empty(static::$env_dir_path)) {
                 $dir = static::$env_dir_path;
                 $display_notice = true;
             }

             try {
                 /** @var Dotenv $dotenv */
                 $dotenv = new Dotenv($dir);
                 $dotenv->load();
                 $dotenv->getEnvironmentVariableNames();
                 static::$config = $_ENV;

             } catch (InvalidPathException $e) {
                 if ($display_notice) {
                     echo 'Invalid custom .env file '.$e->getMessage(). ' Fix or delete the cache file: ' . static::ENV_DIR.PHP_EOL;exit();
                 }
             }

             if (isset(static::$config['DISPLAY_ERRORS']) && (bool)static::$config['DISPLAY_ERRORS']) {
                 error_reporting(E_ALL);
                 ini_set('display_errors', 1);
             }
         }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return static::$config;
    }

    /**
     * @return array|bool
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return string|null
     */
    public function getEnvDir()
    {
        return $this->env_dir_path;
    }

    /**
     * @return array|bool
     */
    public function getPackageCommands()
    {
        return $this->package_commands;
    }


    /**
     * Loads a new modX instance
     *
     * @throws \RuntimeException
     * @return \modX
     */
    public function loadMODX()
    {
        if (static::$modx) {
            return static::$modx;
        }

        if (isset(static::$config['MODX_CONFIG_PATH']) && file_exists(static::$config['MODX_CONFIG_PATH']) ) {
            require_once static::$config['MODX_CONFIG_PATH'];

        } elseif (defined('MODX_CONFIG_PATH') && file_exists(MODX_CONFIG_PATH)) {
            require_once(MODX_CONFIG_PATH);

        } else {
            $this->findMODX();
            if (defined('MODX_CONFIG_PATH') && file_exists(MODX_CONFIG_PATH) && !defined('MODX_CORE_PATH')) {
                require_once(MODX_CONFIG_PATH);
            }

            if (!defined('MODX_CORE_PATH')) {
                throw new \RuntimeException('There does not seem to be a MODX installation here. ');
            }

        }

        require_once(MODX_CORE_PATH . 'model/modx/modx.class.php');

        /** @var \modX $modx */
        $modx = new \modX();
        $modx->initialize('mgr');
        $modx->getService('error', 'error.modError', '', '');
        $modx->setLogTarget('ECHO');

        static::$modx = $modx;

        return $modx;
    }

    /**
     * @return bool
     */
    public function isModxInstalled()
    {
        $this->findMODX();
        if (isset(static::$config['MODX_CONFIG_PATH']) && file_exists(static::$config['MODX_CONFIG_PATH']) ) {
            return true;
        }
        if (defined('MODX_CONFIG_PATH') && file_exists(MODX_CONFIG_PATH) ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $class ~ the fully qualified class name and of the Symfony\Component\Console\Command\Command class
     */
    public function cancelRegistrationConsoleCommand($class)
    {
        if (in_array($class, $this->commands)) {
            $commands = $this->commands;
            $this->commands = [];

            foreach ($commands as $command) {
                if ($command != $class) {
                    $this->commands[] = $command;
                }
            }

            static::writeCacheFile(static::COMMANDS_FILE, $this->commands);
        }
    }

    /**
     * @param string $class ~ the fully qualified class name and of the Symfony\Component\Console\Command\Command class
     */
    public function cancelRegistrationPackageCommands($class)
    {
        if (in_array($class, $this->package_commands)) {
            $commands = $this->package_commands;
            $this->package_commands = [];

            foreach ($commands as $command) {
                if ($command != $class) {
                    $this->package_commands[] = $command;
                }
            }

            static::writeCacheFile(static::PACKAGE_COMMANDS_FILE, $this->package_commands);
        }
    }

    /**
     * @param string $class ~ the fully qualified class name and of the Symfony\Component\Console\Command\Command class
     */
    public function registerConsoleCommand($class)
    {
        if (!in_array($class, $this->commands) && is_a($class, 'Symfony\Component\Console\Command\Command', true)) {
            $this->commands[] = $class;

            static::writeCacheFile(static::COMMANDS_FILE, $this->commands);
        }
    }

    /**
     * @param string $class ~ the fully qualified class name of a class that implements the LCI\MODX\Console\Command\PackageCommands interface
     */
    public function registerPackageCommands($class)
    {
        if (!in_array($class, $this->package_commands) && is_a($class, 'LCI\MODX\Console\Command\PackageCommands', true)) {
            $this->package_commands[] = $class;

            static::writeCacheFile(static::PACKAGE_COMMANDS_FILE, $this->package_commands);
        }
    }

    /**
     * @param string $directory ~ the full directory path with the .env file is located
     */
    public static function setCustomEnvDirectory($directory)
    {
        static::writeCacheFile(static::ENV_DIR, ['env_dir' => $directory]);
    }

    /**
     *
     */
    protected function findMODX() {
        if (!defined('MODX_PATH')) {
            $folders = explode('/', __DIR__);

            // if installed inside of MODX:
            for ($x = count($folders); $x > 0; $x--) {
                $dir = implode(DIRECTORY_SEPARATOR, $folders) . DIRECTORY_SEPARATOR;

                if (file_exists($dir . 'core/config/config.inc.php') || $dir = $this->findMODXInAdjacentDirectory($dir)) {
                    define('MODX_PATH', $dir);
                    break;
                }

                array_pop($folders);
            }

        }

        if (!defined('MODX_CONFIG_PATH') && defined('MODX_PATH')) {
            define('MODX_CONFIG_PATH', MODX_PATH.'core/config/config.inc.php');
        }
    }

    /**
     * @param string $dir ~ directory path
     * @return bool|string
     */
    protected function findMODXInAdjacentDirectory($dir)
    {
        $folders = ['html', 'public', 'www'];

        // if installed inside of MODX:
        foreach ($folders as $folder) {
            $temp = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder;

            if (file_exists($dir . 'core/config/config.inc.php')) {
                return $temp;
            }
        }
        return false;
    }

    /**
     * @return $this
     */
    protected function loadConsoleCommands()
    {
        if (file_exists(static::COMMANDS_FILE)) {
            $this->commands = include static::COMMANDS_FILE;
        }

        if (file_exists(static::PACKAGE_COMMANDS_FILE)) {
            $this->package_commands = include static::PACKAGE_COMMANDS_FILE;
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected static function loadCustomEnvDirectory()
    {
        if (file_exists(static::ENV_DIR)) {
            $array = include static::ENV_DIR;
            if (is_array($array) && isset($array['env_dir'])) {
                static::$env_dir_path = $array['env_dir'];
            }
        } else {
            $path = static::findCustomENV();
            if ($path !== false) {
                static::setCustomEnvDirectory($path);
                // now load the file:
                static::loadCustomEnvDirectory();
            }
        }
    }

    /**
     * @return bool|string
     */
    protected static function findCustomENV() {
        $path = false;
        $folders = explode(DIRECTORY_SEPARATOR, __DIR__);

        // if installed inside of MODX:
        for ($x = count($folders); $x > 0; $x--) {
            $dir = implode(DIRECTORY_SEPARATOR, $folders) . DIRECTORY_SEPARATOR;

            if (file_exists($dir . '.env')) {
                $path = $dir;
                break;
            } elseif (file_exists($dir . 'bin' .DIRECTORY_SEPARATOR . '.env')) {
                $path = $dir . DIRECTORY_SEPARATOR . 'bin' .DIRECTORY_SEPARATOR;
                break;
            }
            // remove last folder:
            array_pop($folders);
        }

        return $path;
    }

    /**
     * @param string $file
     * @param array $data
     */
    protected static function writeCacheFile($file, $data)
    {
        $content = '<?php ' . PHP_EOL .
            'return ' . var_export($data, true) . ';';

        file_put_contents($file, $content);
    }
}