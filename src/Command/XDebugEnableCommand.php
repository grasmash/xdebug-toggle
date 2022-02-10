<?php

namespace Grasmash\XDebugToggle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XDebugEnableCommand extends BaseCommand
{

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this->setName('enable');
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
        $this->enableXDebug($ini_file, $contents);

        return 0;
    }
}
