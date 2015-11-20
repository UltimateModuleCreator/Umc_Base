<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Model\Core;

use Umc\Base\Model\Core\Entity;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Phrase;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Core\Entity\Type\Factory
     */
    protected $typeFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Core\AttributeFactory
     */
    protected $attributeFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Config\SaveAttributes
     */
    protected $saveAttributesConfig;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Config\Form
     */
    protected $formConfig;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Config\Restriction
     */
    protected $restrictionConfig;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Escaper
     */
    protected $escaper;
    /**
     * @var Entity
     */
    protected $entity;
    /**
     * @var string
     */
    protected $filePath;
    /**
     * @var array
     */
    protected $formConfigSource;
    /**
     * @var array
     */
    protected $restrictionConfigSource;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->typeFactory = $this->getMock(
            '\Umc\Base\Model\Core\Entity\Type\Factory',
            [],
            [],
            '',
            false
        );
        $this->attributeFactory = $this->getMock(
            '\Umc\Base\Model\Core\AttributeFactory',
            [],
            [],
            '',
            false
        );
        $this->eventManager = $this->getMock(
            '\Magento\Framework\Event\ManagerInterface',
            [],
            [],
            '',
            false
        );
        $this->saveAttributesConfig = $this->getMock(
            '\Umc\Base\Model\Config\SaveAttributes',
            [],
            [],
            '',
            false
        );
        $this->formConfig = $this->getMock(
            '\Umc\Base\Model\Config\Form',
            [],
            [],
            '',
            false
        );
        $this->restrictionConfig = $this->getMock(
            '\Umc\Base\Model\Config\Restriction',
            [],
            [],
            '',
            false
        );
        $this->escaper = new Escaper();

        $this->entity = new Entity(
            $this->typeFactory,
            $this->attributeFactory,
            $this->eventManager,
            $this->saveAttributesConfig,
            $this->formConfig,
            $this->restrictionConfig,
            $this->escaper,
            []
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->typeFactory = null;
        $this->attributeFactory = null;
        $this->eventManager = null;
        $this->saveAttributesConfig = null;
        $this->formConfig = null;
        $this->restrictionConfig = null;
        $this->escaper = null;
        $this->entity = null;
        $this->objectManagerHelper = null;
        parent::tearDown();
    }

    /**
     * @return \Umc\Base\Model\Core\Settings|object
     */
    protected function setupSettings()
    {
        $settings = $this->objectManagerHelper->getObject('\Umc\Base\Model\Core\Settings');
        $settings->setData([
                'license' => 'License here',
                'underscore' => 1,
                'annotations' => 1,
                'qualified' => 0
            ]
        );
        return $settings;
    }

    /**
     * @return \Umc\Base\Model\Core\Module|object
     */
    protected function setupModule()
    {
        /** @var \Umc\Base\Model\Core\Module | object $module */
        $module = $this->objectManagerHelper->getObject('\Umc\Base\Model\Core\Module');
        $module->setData(
            [
                'namespace' => 'Namespace',
                'module_name' => 'Module',
            ]
        );
        $module->setSettings($this->setupSettings());
        return $module;
    }

    /**
     * @return object|\Umc\Base\Model\Core\Attribute
     */
    protected function setupAttribute()
    {
        /** @var \Umc\Base\Model\Core\Attribute | object $attribute */
        $attribute = $this->objectManagerHelper->getObject('\Umc\Base\Model\Core\Attribute');
        $attribute->setData([
            'code' => 'name',
            'label' => 'Name',
            'is_name' => 1,
            'required' => 1,
            'position' => 10,
            'admin_grid' => 1,
            'default_value' => 'default',
            'index' => 0
        ]);
        return $attribute;
    }

    /**
     * setup validation test
     */
    protected function setupValidationTest()
    {
        $this->filePath = dirname(realpath(__DIR__)) . '/_files/';
        $this->formConfigSource = include $this->filePath . 'form_config_entity.php';
        $this->formConfig->expects($this->any())->method('getConfig')->willReturn($this->formConfigSource['config']['form']['umc_entity']);

        $this->restrictionConfigSource = include $this->filePath . 'restriction.php';
        $this->restrictionConfig->expects($this->any())->method('getRestrictions')->willReturn($this->restrictionConfigSource['config']['entity']['umc_entity']['restriction']);
    }

    /**
     * tests Entity->setModule()
     */
    public function testSetModule()
    {
        $module = $this->setupModule();
        $this->assertEquals(null, $this->entity->getModule());
        $this->entity->setModule($module);
        $this->assertEquals($module, $this->entity->getModule());
    }

    /**
     * tests Entity->getType()
     */
    public function testGetType()
    {
        $type = 'flat';
        $this->entity->setData('type', $type);
        $this->assertEquals($type, $this->entity->getType());
    }

    /**
     * tests Entity->getLabelSingular()
     */
    public function testGetLabelSingular()
    {
        $this->entity->setData([
            'label_singular' => 'entity'
        ]);
        $this->assertEquals('entity', $this->entity->getLabelSingular());
        $this->assertEquals('Entity', $this->entity->getLabelSingular(true));
    }

    /**
     * tests Entity->getLabelPlural()
     */
    public function testGetLabelPlural()
    {
        $this->entity->setData([
            'label_plural' => 'entities'
        ]);
        $this->assertEquals('entities', $this->entity->getLabelPlural());
        $this->assertEquals('Entities', $this->entity->getLabelPlural(true));
    }

    /**
     * tests Entity->addAttribute()
     */
    public function testAddAttribute()
    {
        $this->assertEquals(0, count($this->entity->getAttributes()));
        $this->entity->addAttribute($this->setupAttribute());
        $this->assertEquals(1, count($this->entity->getAttributes()));
    }

    /**
     * tests Entity->getAttributes()
     */
    public function testGetAttributes()
    {
        $attribute = $this->setupAttribute();
        $this->entity->addAttribute($attribute);
        $attributes = $this->entity->getAttributes();
        $this->assertEquals($attribute, $attributes[0]);
    }

    //TODO: test toXML
    //TODO: test getTypeInstance

    public function testValidateOk()
    {
        $attribute = $this->setupAttribute();
        $this->entity->addAttribute($attribute);
        $this->entity->setData([
            'name_singular' => 'entity',
            'name_plural' => 'entities'
        ]);
        $this->assertEquals([], $this->entity->validate());
    }

    public function testValidateNoNameAttribute()
    {
        $attribute = $this->setupAttribute();
        $attribute->setIsName(false);
        $this->entity->addAttribute($attribute);
        $this->entity->setData([
            'name_singular' => 'entity',
            'name_plural' => 'entities',
            'label_singular' => 'Entity'
        ]);
        $expected = [
            '' => [
                new Phrase('Entity "%1" does not have an attribute that behaves as name', ['Entity'])
            ]
        ];
        $this->assertEquals($expected, $this->entity->validate());
    }

    /**
     * tests Module->validate()
     * validation should fail due to incorrect name singular
     */
    public function testValidateNameSingularRestriction()
    {
        $this->setupValidationTest();
        $this->entity->setData([
            'name_singular' => 'resource',
            'name_plural' => 'resources',
            'label_singular' => 'Resource',
            'label_plural' => 'Resources',
            'index' => 0
        ]);
        $this->entity->addAttribute($this->setupAttribute());
        $message = $this->restrictionConfigSource['config']['entity']['umc_entity']['restriction']['name_singular']['val']['resource']['message'];
        $expected = [
            'entity_0_name_singular' => [
                new Phrase($message)
            ]
        ];
        $this->assertEquals($expected, $this->entity->validate());
    }

    public function testGetNameAttribute()
    {
        $this->assertNull($this->entity->getNameAttribute());
        $attribute = $this->setupAttribute();
        $this->entity->addAttribute($attribute);
        $this->assertEquals($attribute, $this->entity->getNameAttribute());
    }

    public function testGetValidationErrorKey()
    {
        $this->entity->setIndex(0);
        $field = 'test';
        $this->assertEquals('entity_0_'.$field, $this->entity->getValidationErrorKey($field));
    }

    //TODO: test getHasAttributeType & getHasAttributeTypeRequired
    //TODO: test getHasEditor && getHasEditorRequired
    //TODO: test getHasMulti && getColumnsSetup
    //TODO: test getEditFormFields && getEditFormFieldsAsNew

    /**
     * tests Entity->getNameSingular()
     */
    public function testGetNameSingular()
    {
        $this->entity->setData([
            'name_singular' => 'entity'
        ]);
        $this->assertEquals('entity', $this->entity->getNameSingular());
        $this->assertEquals('Entity', $this->entity->getNameSingular(true));
    }

    /**
     * tests Entity->getNamePlural()
     */
    public function testGetNamePlural()
    {
        $this->entity->setData([
            'name_plural' => 'entities'
        ]);
        $this->assertEquals('entities', $this->entity->getNamePlural());
        $this->assertEquals('Entities', $this->entity->getNamePlural(true));
    }

    public function testGetParent()
    {
        $module = $this->setupModule();
        $this->entity->setModule($module);
        $this->assertEquals($module, $this->entity->getParent());
    }

    public function testGetDateAttributeCodes()
    {
        $this->assertEquals('[]', $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->setType('date');
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name']", $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->setCode('some_date');
        $attribute->setType('date');
        $attribute->setIsName(false);
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name', 'some_date']", $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->setCode('something');
        $attribute->setIsName(false);
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name', 'some_date']", $this->entity->getDateAttributeCodes());
    }

}
