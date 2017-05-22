<?php

namespace Grasmash\XDebugToggle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class XDebugToggleCommand extends Command
{
    /**
     * @var
     */
    protected $iniFile;

    /**
     * @var Filesystem;
     */
    protected $fs;

    /**
     * @var ConsoleLogger;
     */
    protected $logger;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var
     */
    protected $xDebugEnabled;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
          ->setName('toggle')
          ->setDescription('Toggles xDebug, enabling or disabling it as needed.')
          ->setHelp('This command will re-write your active php.ini file by either commenting or uncommenting the Zend extension for xDebug.')
        ;
    }

    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The CLI input.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *   The CLI output.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->fs = new Filesystem();
        $this->logger = new ConsoleLogger($output);
        $this->iniFile = php_ini_loaded_file();
        $contents = file_get_contents($this->iniFile);
        $this->setXDebugStatus($contents);

        if ($this->xDebugEnabled === FALSE) {
            $this->enableXDebug($contents);
        }
        elseif ($this->xDebugEnabled == TRUE) {
            $this->disableXDebug($contents);
        }
        else {
            $this->logger->error("Could not find xdebug zend extension in $this->iniFile!");
        }
    }

    /**
     * Sets $this->xDebugEnabled.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    protected function setXDebugStatus($contents) {
        if (preg_match('|;zend_extension=".+\/xdebug.so"|', $contents)) {
            $this->xDebugEnabled = FALSE;
        }
        elseif (preg_match('|zend_extension=".+\/xdebug.so"|', $contents)) {
            $this->xDebugEnabled = TRUE;
        }
        else {
            $this->xDebugEnabled = NULL;
        }
    }

    /**
     * Enables xDebug.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    protected function enableXDebug($contents)
    {
        $this->logger->notice("Enabling xdebug in $this->iniFile...");
        $new_contents = preg_replace('|(;)(zend_extension=".+\/xdebug.so")|', '$2', $contents);
        $this->fs->dumpFile($this->iniFile, $new_contents);
        $this->output->writeln("<info>xDebug enabled.</info>");
    }

    /**
     * Disables xDebug.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    protected function disableXDebug($contents)
    {
        $this->logger->notice("Disabling xdebug in $this->iniFile...");
        $new_contents = preg_replace('|(zend_extension=".+\/xdebug.so")|', ';$1', $contents);
        $this->fs->dumpFile($this->iniFile, $new_contents);
        $this->output->writeln("<info>xDebug disabled.</info>");
    }
}
