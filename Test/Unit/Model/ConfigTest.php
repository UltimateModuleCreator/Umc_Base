<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Model;

use Umc\Base\Model\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Umc\Base\Model\Config
     */
    protected $model;

    /**
     * @var \Magento\Framework\Config\Reader\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array|null
     */
    protected $source;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->reader = $this->getMock(
            '\Magento\Framework\Config\Reader\Filesystem',
            ['read'],
            [],
            '',
            false
        );

        $this->model = new Config($this->reader, []);
        $this->filePath = realpath(__DIR__) . '/_files/';
        $this->source = include $this->filePath . 'config.php';
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->model = null;
        $this->reader = null;
        parent::tearDown();
    }

    /**
     * Tests Config->getConfig()
     * with no path specified and the disabled fields are not ignored
     */
    public function testGetConfigNoPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($source['config'], $this->model->getConfig(null, false, null));
    }

    /**
     * Tests Config->getConfig()
     * with no path specified and the disabled fields are ignored
     */
    public function testGetConfigNoPathIgnoreDisabled()
    {
        $source = $this->source;
        unset($source['config']['form']['umc_settings']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_settings']['fieldset']['disabled']);
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($source['config'], $this->model->getConfig());
    }

    public function testGetConfigValidPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_settings';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        unset($source['config']['form']['umc_settings']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_settings']['fieldset']['disabled']);
        $this->assertEquals($source['config']['form']['umc_settings'], $this->model->getConfig($path));
    }

    /**
     * Tests Config->getConfig()
     * with an invalid path specified and the disabled fields are not ignored
     */
    public function testGetConfigNoValidPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_settings/wrong_path';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals(null, $this->model->getConfig($path));
    }

    /**
     * Tests Config->getConfig()
     * with a valid path specified and the disabled fields are ignored
     */
    public function testGetConfigValidPathIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_settings/fieldset/disabled';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals(null, $this->model->getConfig($path));
    }

    /**
     * Tests Config->getConfig()
     * check if the default specified value is returned
     * when disabled elements are not ignored
     */
    public function testGetConfigDefaultNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_settings/wrong_field';
        $expected = '1234';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($expected, $this->model->getConfig($path, false, $expected));
    }

    /**
     * Tests Config->getConfig()
     * check if the default specified value is returned
     * when disabled elements are ignored
     */
    public function testGetConfigDefaultIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_settings/fieldset/settings/field/disabled';
        $expected = '1234';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($expected, $this->model->getConfig($path, true, $expected));
    }

    /**
     * Tests Config->getBoolValue()
     */
    public function testGetBoolValue()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $evaluate = $source['config']['form']['umc_settings']['fieldset']['settings'];
        $this->assertEquals(true, $this->model->getBoolValue($evaluate, 'collapsible'));
        $this->assertEquals(false, $this->model->getBoolValue($evaluate, 'not_existent'));
        $disabledField = $source['config']['form']['umc_settings']['fieldset']['settings']['field'];
        $this->assertEquals(false, $this->model->getBoolValue($disabledField, 'disabled'));
        $disabledFieldset = $source['config']['form']['umc_settings']['fieldset'];
        $this->assertEquals(false, $this->model->getBoolValue($disabledFieldset, 'disabled'));
    }

    /**
     * Tests Config->getEnabledConfig()
     */
    public function testGetEnabledConfig()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        unset($source['config']['form']['umc_settings']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_settings']['fieldset']['disabled']);
        $this->assertEquals($source['config'], $this->model->getEnabledConfig());
    }
}
