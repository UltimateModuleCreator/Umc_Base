<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config;

use Magento\Framework\Config\Reader\Filesystem;
use Umc\Base\Config\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $formConfig;

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
        $this->reader     = $this->getMock(Filesystem::class, ['read'], [], '', false);
        $this->formConfig = new Form($this->reader, 'config', []);
        $this->source     = $this->getSourceConfig();

        $this->reader->method('read')->willReturn($this->source);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->formConfig  = null;
        $this->reader      = null;
        $this->source      = null;
        parent::tearDown();
    }

    /**
     * @tests Form::getFieldType()
     */
    public function testGetFieldType()
    {
        $this->assertEquals('select', $this->formConfig->getFieldType([
            'type' => 'select',
            'dummy' => 'dummy'
        ]));
        $this->assertEquals('text', $this->formConfig->getFieldType([]));
    }

    /**
     * @tests Form::getTexareaRow()
     */
    public function testGetTexareaRow()
    {
        $this->assertEquals(10, $this->formConfig->getTextareaRows([
            'rows' => 10,
            'dummy' => 'dummy'
        ]));
        $this->assertEquals(5, $this->formConfig->getTextareaRows([
            'rows' => 0,
            'dummy' => 'dummy'
        ]));
        $this->assertEquals(5, $this->formConfig->getTextareaRows([]));
    }

    /**
     * @tests Form::getFieldLabelByCode()
     */
    public function testGetFieldLabelByCode()
    {
        $this->assertEquals('Use underscore', $this->formConfig->getFieldLabelByCode('umc_module', 'underscore'));
        $this->assertEquals('', $this->formConfig->getFieldLabelByCode('umc_module', 'dummy'));
        $this->assertEquals('default', $this->formConfig->getFieldLabelByCode('umc_module', 'dummy', 'default'));
    }

    /**
     * @tests Form::getFieldLabelByCode()
     */
    public function testGetDepends()
    {
        $expected = [
            'underscore' => [
                0 => [
                    'type' => [
                        'self' => ["1"]
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $this->formConfig->getDepends('umc_module', false));
        $this->assertEquals(json_encode($expected), $this->formConfig->getDepends('umc_module'));
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
                                        'depends' => [
                                            'type' => [
                                                'depend' => [
                                                    'type' => [
                                                        'id' => 'type',
                                                        'type' => 'self',
                                                        'val' => [
                                                            1 => [
                                                                'id' => 1,
                                                                'value' => 1,
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
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
