<?php

namespace LCI\MODX\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomEnvDirectory extends BaseCommand
{
    public $loadMODX = true;

    protected function configure()
    {
        $this
            ->setName('console:env-dir')
            ->setAliases(['console:env'])
            ->setDescription('Define custom .env directory')
            // @see https://symfony.com/doc/current/console/input.html
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'The full directory path in which your .env file exists, ex: /var/www/'
            );
    }

    /**
     * Runs the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');

        $this->console->setCustomEnvDirectory($dir);

        $output->writeln('### ENV directory has been set to '.$dir.' ###');
    }
}
