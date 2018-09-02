<?php
ini_set('display_errors', 1);

use LCI\MODX\Console\Application;
use LCI\MODX\Console\Console;

$bootstrap_possible_paths = [
    // if cloned from git:
    dirname(__DIR__).'/src/bootstrap.php',
    // if installed via composer:
    dirname(dirname(dirname(__DIR__))).'/lci/console/src/bootstrap.php',
];
foreach ($bootstrap_possible_paths as $bootstrap_path) {
    if (file_exists($bootstrap_path)) {
        require_once $bootstrap_path;
        break;
    }
}

// allow to override
if (isset($env_directory) && file_exists($env_directory)) {

} else {
    $env_directory = dirname(__DIR__) . '/bin/';
    $env_possible_paths = [
        // if placed in the bin directory
        dirname(__DIR__).'/bin/.env',
        // if cloned from git:
        dirname(__DIR__).'/.env',
        // if installed via composer:
        dirname(dirname(dirname(__DIR__))).'/lci/console/.env',
    ];
    foreach ($env_possible_paths as $env_path) {
        if (file_exists($env_path)) {
            $env_directory = substr($env_path, 0, - 4);
            break;
        }
    }
}

/**
 * Ensure the timezone is set;
 */
if (version_compare(phpversion(),'5.3.0') >= 0) {
    $tz = @ini_get('date.timezone');
    if (empty($tz)) {
        date_default_timezone_set(@date_default_timezone_get());
    }
}

/** @var LCI\MODX\Console\Console $console */
$console = new Console($env_directory);

/** @var Application $application */
$application = new Application($console);
$application->loadCommands();
$application->run();