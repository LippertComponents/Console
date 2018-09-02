<?php

namespace LCI\MODX\Console\Command;

use LCI\MODX\Console\Console;
use LCI\MODX\Console\Helpers\ConsoleUserInteractionHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCommand
 * @package LCI\MODX\Console\Console
 */
abstract class BaseCommand extends Command
{
    /** @var Console */
    protected $console;

    /** @var ConsoleUserInteractionHandler */
    protected $consoleUserInteractionHandler;

    /** \Symfony\Component\Console\Input\InputInterface $input */
    protected $input;

    /** \Symfony\Component\Console\Output\OutputInterface $output */
    protected $output;

    protected $startTime;

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->startTime = microtime(true);
        $this->input = $input;
        $this->output = $output;

        $this->consoleUserInteractionHandler = new ConsoleUserInteractionHandler($input, $output);
        $this->consoleUserInteractionHandler->setCommandObject($this);
    }

    /**
     * @return string
     */
    public function getRunStats()
    {
        $curTime = microtime(true);
        $duration = $curTime - $this->startTime;

        $output = 'Time: ' . number_format($duration * 1000, 0) . 'ms | ';
        $output .= 'Memory Usage: ' . $this->convertBytes(memory_get_usage(false)) . ' | ';
        $output .= 'Peak Memory Usage: ' . $this->convertBytes(memory_get_peak_usage(false));
        return $output;
    }

    /**
     * @param Console $console
     * @return $this
     */
    public function setConsole($console)
    {
        $this->console = $console;
        return $this;
    }

    /**
     * @param $bytes
     * @return string
     */
    protected function convertBytes($bytes)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2).' '.$unit[$i];
    }
}
