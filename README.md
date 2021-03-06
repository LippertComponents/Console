# Console

An extendable Composer driven CLI console for MODX. Creating a single point for running 
CLI commands.

## Install steps

If you have not install composer do so now before proceeding

- [Install Composer on MODX Cloud](https://support.modx.com/hc/en-us/articles/221296007-Composer)
- [Install Composer](https://getcomposer.org/doc/00-intro.md)

**Option one behind the public root**

Not recommended for MODX Cloud as only the www directory gets copied for snapshots and backups

1. Example SSH into MODX Cloud in the home/ directory
2. ```mkdir Console``` or name that you prefer
3. Then cd into the directory

**Option two in traditional MODX components path**

1. Go to MODX/core/components
2. ```mkdir console```
3. Then cd into the directory

**Remain steps are the same in the created directory**

1. Run ```composer require lci/console```

## Config for Packages

As of 1.1.3, dependant packages classes will be stored in MODX/core/config/lci_console*. 

## Custom .env

By default if you want an .env file place it in the /core directory.

Optional set your own custom path, via command line. Example within /core $ `php vendor/bin/console  console:env-dir /var/path/`

Note on MODXCloud you need to provide the realpath not the symlink path if you set a custom path. Like /paas/cXXXX/www/core/path/.env

