<?php

namespace Grasmash\XDebugToggle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class XDebugToggleCommand extends BaseCommand
{

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this->setName('toggle');
    }

    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The CLI input.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The CLI output.
     *
     * @return null
     *   This method does not return anything.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setup($input, $output);
        $ini_file = $input->getOption('ini-file');
        $contents = file_get_contents($ini_file);
        $this->setXDebugStatus($contents);

        if ($this->xDebugEnabled === false) {
            $this->enableXDebug($ini_file, $contents);
        } elseif ($this->xDebugEnabled == true) {
            $this->disableXDebug($ini_file, $contents);
        } else {
            $this->logger->error("Could not find xdebug zend extension in $ini_file!");
            return 1;
        }

        return 0;
    }
}
