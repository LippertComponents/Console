# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.6pl] - 2019-04-10
### Fix

- Fixed the Console->findMODX() to no longer search /, /paas and /pass/bin to remove error log warnings

## [1.1.5pl] - 2019-03-18
### Fix

- Fixed the Console->findMODX() to to have DIRECTORY_SEPARATOR to make proper paths

## [1.1.4pl] - 2019-03-16
### Fix

- Fixed undefined index, use the correct static::$paths in Console->findMODX() 

## [1.1.3pl] - 2019-03-16
### Fix

- Move Console config files into MODX/core/config directory 
- Deprecated: `Console::COMMANDS_FILE, Console::ENV_DIR and Console::PACKAGE_COMMANDS_FILE`, do not use
Instead use: `$paths = new Console()->getConfigFilePaths();`

## [1.1.2pl] - 2019-03-08
### Fix

- Fix, on MODXCloud symlinks work for command line usage but failed when running through Nginx.
Now will get the full path for the /www/core directory when ran through Nginx. 

## [1.1.1pl] - 2019-03-08
### Fix

- Fixed Accessing static property LCI\MODX\Console\Console::$env_dir_path as non static in src/Console.php on line 99

## [1.1.0pl] - 2019-03-08
### Added

- Added static method LCI\MODX\Console\Console::loadEnv()

## [1.0.7pl] - 2018-10-03
### Removed

- Removed the Application->getDefaultInputDefinition method to allow Symfony defaults

## [1.0.6pl] - 2018-09-25
### Fixed

- Fixed Question prompts to work properly when options are passed to a Command.  
The fix was adding interactive logic on ConsoleUserInteractionHandler for input->setInteractive.