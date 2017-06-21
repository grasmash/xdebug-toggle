<?php

namespace Grasmash\XDebugToggle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseCommand extends Command
{
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
          ->addOption(
            'ini-file',
            null,
            InputOption::VALUE_OPTIONAL,
            'The file path to the php.ini file that should be modified',
            php_ini_loaded_file()
          )
          ->setDescription('Toggles xDebug, enabling or disabling it as needed.')
          ->setHelp('This command will re-write your active php.ini file by either commenting or uncommenting the Zend extension for xDebug.')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function setup(InputInterface $input, OutputInterface $output)
    {
        $this->setFs(new Filesystem());
        $this->setOutput($output);
        $this->setLogger(new ConsoleLogger($output));
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function getFs()
    {
        return $this->fs;
    }

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     */
    public function setFs($fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return \Symfony\Component\Console\Logger\ConsoleLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Symfony\Component\Console\Logger\ConsoleLogger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sets $this->xDebugEnabled.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    public function setXDebugStatus($contents)
    {
        if (preg_match('|;zend_extension=".+\/xdebug.so"|', $contents)) {
            $this->xDebugEnabled = false;
        } elseif (preg_match('|zend_extension=".+\/xdebug.so"|', $contents)) {
            $this->xDebugEnabled = true;
        } else {
            $this->xDebugEnabled = null;
        }
    }

    /**
     * Gets $this->xDebugEnabled.
     *
     * @return mixed
     *   $this->xDebugEnabled.
     */
    public function getXDebugStatus()
    {
        return $this->xDebugEnabled;
    }

    /**
     * Enables xDebug.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    public function enableXDebug($destination_file, $contents)
    {
        $this->logger->notice("Enabling xdebug in $destination_file...");
        $new_contents = preg_replace('|(;)+(zend_extension=".+\/xdebug.so")|', '$2', $contents);
        $this->fs->dumpFile($destination_file, $new_contents);
        $this->output->writeln("<info>xDebug enabled.</info>");
    }

    /**
     * Disables xDebug.
     *
     * @param string $contents
     *   The contents of php.ini.
     */
    public function disableXDebug($destination_file, $contents)
    {
        $this->logger->notice("Disabling xdebug in $destination_file...");
        $new_contents = preg_replace('|(;)*(zend_extension=".+\/xdebug.so")|', ';$2', $contents);
        $this->fs->dumpFile($destination_file, $new_contents);
        $this->output->writeln("<info>xDebug disabled.</info>");
    }
}
