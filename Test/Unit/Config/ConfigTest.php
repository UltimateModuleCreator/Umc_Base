<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config;

use Magento\Framework\Config\Reader\Filesystem;
use Umc\Base\Config\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

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
        $this->reader = $this->getMock(Filesystem::class, ['read'], [], '', false);
        $this->config = new Config($this->reader, 'config', []);
        $this->source = $this->getSourceConfig();
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->config  = null;
        $this->reader = null;
        $this->source = null;
        parent::tearDown();
    }

    /**
     * @tests Config::getConfig()
     * with no path specified and the disabled fields are not ignored
     */
    public function testGetConfigNoPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($source['config'], $this->config->getConfig(null, false, null));
    }

    /**
     * @tests Config::getConfig()
     * with no path specified and the disabled fields are ignored
     */
    public function testGetConfigNoPathIgnoreDisabled()
    {
        $source = $this->source;
        unset($source['config']['form']['umc_module']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_module']['fieldset']['disabled']);
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($source['config'], $this->config->getConfig());
    }

    /**
     * @tests Config::getConfig()
     * with a path specified and the disabled fields are not ignored
     */
    public function testGetConfigValidPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_module';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        unset($source['config']['form']['umc_module']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_module']['fieldset']['disabled']);
        $this->assertEquals($source['config']['form']['umc_module'], $this->config->getConfig($path));
    }

    /**
     * @tests Config::getConfig()
     * with an invalid path specified and the disabled fields are not ignored
     */
    public function testGetConfigNoValidPathNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_module/wrong_path';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals(null, $this->config->getConfig($path));
    }

    /**
     * @tests Config::getConfig()
     * with a valid path specified and the disabled fields are ignored
     */
    public function testGetConfigValidPathIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_module/fieldset/disabled';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals(null, $this->config->getConfig($path));
    }

    /**
     * @tests Config::getConfig()
     * check if the default specified value is returned
     * when disabled elements are not ignored
     */
    public function testGetConfigDefaultNoIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_module/wrong_field';
        $expected = '1234';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($expected, $this->config->getConfig($path, false, $expected));
    }

    /**
     * @tests Config::getConfig()
     * check if the default specified value is returned
     * when disabled elements are ignored
     */
    public function testGetConfigDefaultIgnoreDisabled()
    {
        $source = $this->source;
        $path = 'form/umc_module/fieldset/settings/field/disabled';
        $expected = '1234';
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $this->assertEquals($expected, $this->config->getConfig($path, true, $expected));
    }

    /**
     * @tests Config::getBoolValue()
     */
    public function testGetBoolValue()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        $evaluate = $source['config']['form']['umc_module']['fieldset']['settings'];
        $this->assertEquals(true, $this->config->getBoolValue($evaluate, 'collapsible'));
        $this->assertEquals(false, $this->config->getBoolValue($evaluate, 'not_existent'));
        $disabledField = $source['config']['form']['umc_module']['fieldset']['settings']['field'];
        $this->assertEquals(false, $this->config->getBoolValue($disabledField, 'disabled'));
        $disabledFieldset = $source['config']['form']['umc_module']['fieldset'];
        $this->assertEquals(false, $this->config->getBoolValue($disabledFieldset, 'disabled'));
    }

    /**
     * @tests Config::getEnabledConfig()
     */
    public function testGetEnabledConfig()
    {
        $source = $this->source;
        $this->reader->expects($this->any())->method('read')->willReturn($source);
        unset($source['config']['form']['umc_module']['fieldset']['settings']['field']['disabled']);
        unset($source['config']['form']['umc_module']['fieldset']['disabled']);
        $this->assertEquals($source['config'], $this->config->getEnabledConfig());
    }

    /**
     * source for the tests
     *
     * @return array
     */
    protected function getSourceConfig()
    {
        return [
            'config' => [
                'form' => [
                    'umc_module' => [
                        'id' => 'umc_module',
                        'fieldset' => [
                            'settings' => [
                                'id'    => 'settings',
                                'sort'  => 10,
                                'collapsible' => true,
                                'translate' => 'label',
                                'label' => 'Module settings',
                                'field' => [
                                    'qualified' => [
                                        'id' => 'qualified',
                                        'type' => 'select',
                                        'required' => true,
                                        'sort' => 10,
                                        'system' => true,
                                        'label' => 'Fully qualified class names',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                    'underscore' => [
                                        'id' => 'underscore',
                                        'type' => 'select',
                                        'required' => true,
                                        'sort' => 20,
                                        'system' => true,
                                        'label' => 'Use underscore',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                    'disabled' => [
                                        'id' => 'disabled',
                                        'type' => 'text',
                                        'required' => true,
                                        'sort' => 30,
                                        'system' => true,
                                        'label' => 'Disabled field',
                                        'disabled' => true
                                    ]
                                ]
                            ],
                            'disabled' => [
                                'id'    => 'disabled',
                                'sort'  => 20,
                                'collapsible' => true,
                                'translate' => 'label',
                                'label' => 'Disabled fieldset',
                                'disabled' => 'true',
                                'field' => [
                                    'some_field' => [
                                        'id' => 'some_field',
                                        'type' => 'select',
                                        'required' => true,
                                        'sort' => 10,
                                        'system' => true,
                                        'label' => 'Some field',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
