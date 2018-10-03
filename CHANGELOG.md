# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.7pl] - 2018-10-03
### Removed

- Removed the Application->getDefaultInputDefinition method to allow Symfony defaults

## [1.0.6pl] - 2018-09-25
### Fixed

- Fixed Question prompts to work properly when options are passed to a Command.  
The fix was adding interactive logic on ConsoleUserInteractionHandler for input->setInteractive.