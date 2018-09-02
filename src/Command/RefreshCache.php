<?php

namespace LCI\MODX\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCache extends BaseCommand
{
    public $loadMODX = true;

    protected function configure()
    {
        $this
            ->setName('console:refresh-cache')
            ->setAliases(['console:clear'])
            ->setDescription('Refresh MODX Cache');
        // @TODO options for cache partitions: https://docs.modx.com/xpdo/2.x/advanced-features/caching

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
        $this->modx = $this->console->loadMODX();
        $this->modx->cacheManager->refresh();
        $output->writeln('### Cache has been refreshed(cleared) ###');
    }
}
