<?php

namespace Grasmash\XDebugToggle\Tests;

use Grasmash\XDebugToggle\Command\XDebugToggleCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class xDebugToggleTest.
 *
 * @package Grasmash\XDebugToggle\Tests
 */
class xDebugToggleCommandTest extends TestCase {

    /**
     * @var XDebugToggleCommand;
     */
    protected $command;

    /**
     * Shared setup method for all tests in this class.
     */
    public function setup() {
        $this->command = new XDebugToggleCommand();
        $output = new ConsoleOutput();
        $this->command->setFs(new Filesystem());
        $this->command->setLogger(new ConsoleLogger($output));
        $this->command->setOutput($output);
    }

    /**
     * Tests setXDebugStatus().
     */
    public function testSetXDebugStatus() {
        $ini_disabled = file_get_contents(__DIR__ . '/fixtures/xdebug-disabled.ini');
        $this->command->setXDebugStatus($ini_disabled);
        $this->assertEquals($this->command->getXDebugStatus(), FALSE);

        $ini_enabled = file_get_contents(__DIR__ . '/fixtures/xdebug-enabled.ini');
        $this->command->setXDebugStatus($ini_enabled);
        $this->assertEquals($this->command->getXDebugStatus(), TRUE);

        $ini_missing = file_get_contents(__DIR__ . '/fixtures/xdebug-missing.ini');
        $this->command->setXDebugStatus($ini_missing);
        $this->assertEquals($this->command->getXDebugStatus(), NULL);
    }

    /**
     * Tests enableXDebug().
     */
    public function testEnableXDebug() {
        $ini_disabled = file_get_contents(__DIR__ . '/fixtures/xdebug-disabled.ini');
        $tmp_file = __DIR__ . '/../tmp/testEnableXDebug.ini';
        $this->command->getFs()->remove($tmp_file);
        $this->command->enableXDebug($tmp_file, $ini_disabled);
        $this->assertFileExists($tmp_file);
        $new_contents = file_get_contents($tmp_file);
        $this->assertContains('zend_extension="/Applications/MAMP/bin/php/php7.0.13/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"', $new_contents);
    }

    /**
     * Tests disableXDebug().
     */
    public function testDisableXDebug() {
        $ini_disabled = file_get_contents(__DIR__ . '/fixtures/xdebug-enabled.ini');
        $tmp_file = __DIR__ . '/../tmp/testDisableXDebug.ini';
        $this->command->getFs()->remove($tmp_file);
        $this->command->disableXDebug($tmp_file, $ini_disabled);
        $this->assertFileExists($tmp_file);
        $new_contents = file_get_contents($tmp_file);
        $this->assertContains(';zend_extension="/Applications/MAMP/bin/php/php7.0.13/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"', $new_contents);
    }

    /**
     * Tests execute().
     *
     * @dataProvider providerTestExecute
     */
    public function testExecute($ini_file, $command, $prefix) {
        $tmp_file = __DIR__ . '/../tmp/testExecute.ini';
        $this->command->getFs()->remove($tmp_file);
        copy($ini_file, $tmp_file);
        $output = shell_exec(__DIR__ . "/../bin/xdebug $command --ini-file=$tmp_file -v");
        $this->assertContains("$prefix xdebug in $tmp_file...", $output);
    }

    /**
     * Data provider for testExecute().
     *
     * @return array
     */
    public function providerTestExecute()
    {
        return [
          [__DIR__ . '/fixtures/xdebug-disabled.ini', 'enable', 'Enabling'],
          [__DIR__ . '/fixtures/xdebug-disabled.ini', 'toggle', 'Enabling'],
          [__DIR__ . '/fixtures/xdebug-enabled.ini', 'disable', 'Disabling'],
          [__DIR__ . '/fixtures/xdebug-enabled.ini', 'toggle', 'Disabling'],
        ];
    }

    /**
     * Tests status command.
     *
     * @dataProvider providerTestStatus
     */
    public function testStatus($ini_file, $status) {
        $output = shell_exec(__DIR__ . "/../bin/xdebug status --ini-file=$ini_file -v");
        $this->assertContains("xDebug is $status.", $output);
    }

    /**
     * Data provider for testExecute().
     *
     * @return array
     */
    public function providerTestStatus()
    {
        return [
          [__DIR__ . '/fixtures/xdebug-disabled.ini', 'disabled'],
          [__DIR__ . '/fixtures/xdebug-enabled.ini', 'enabled'],
        ];
    }

}