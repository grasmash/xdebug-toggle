<?php

namespace Grasmash\XDebugToggle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XDebugStatusCommand extends BaseCommand
{

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this->setName('status');
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
            $output->writeln("<info>xDebug is disabled.</info>");
        } elseif ($this->xDebugEnabled == true) {
            $output->writeln("<info>xDebug is enabled.</info>");
        } else {
            $output->writeln("<error>xDebug is missing.</error>");
            return 1;
        }

        return 0;
    }
}
