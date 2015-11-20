<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Model\Core;

use Umc\Base\Model\Core\Module;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Phrase;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Core\RelationFactory
     */
    protected $relationFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Core\SettingsFactory
     */
    protected $settingsFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Model\Core\EntityFactory
     */
    protected $entityFactory;
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
     * @var Module
     */
    protected $module;
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
        $this->relationFactory = $this->getMock(
            '\Umc\Base\Model\Core\RelationFactory',
            [],
            [],
            '',
            false
        );
        $this->settingsFactory = $this->getMock(
            '\Umc\Base\Model\Core\SettingsFactory',
            [],
            [],
            '',
            false
        );
        $this->entityFactory = $this->getMock(
            '\Umc\Base\Model\Core\EntityFactory',
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

        $this->module = new Module(
            $this->relationFactory,
            $this->settingsFactory,
            $this->entityFactory,
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
        $this->relationFactory = null;
        $this->settingsFactory = null;
        $this->entityFactory = null;
        $this->attributeFactory = null;
        $this->eventManager = null;
        $this->saveAttributesConfig = null;
        $this->formConfig = null;
        $this->restrictionConfig = null;
        $this->escaper = null;
        $this->module = null;
        $this->objectManagerHelper = null;
        parent::tearDown();
    }

    /**
     * Tests Module->getNamespace()
     */
    public function testGetNamespace()
    {
        $this->module->setData(
            [
                'namespace' => 'Test',
            ]
        );
        $this->assertEquals('Test', $this->module->getNamespace());
        $this->assertEquals('test', $this->module->getNamespace(true));
    }

    /**
     * Tests Module->getModuleName()
     */
    public function testGetModuleName()
    {
        $this->module->setData(
            [
                'module_name' => 'Module',
            ]
        );
        $this->assertEquals('Module', $this->module->getModuleName());
        $this->assertEquals('module', $this->module->getModuleName(true));
    }

    /**
     * Tests Module->getExtensionName()
     */
    public function testGetExtensionName()
    {
        $this->module->setData(
            [
                'namespace' => 'Namespace',
                'module_name' => 'Module',
            ]
        );
        $this->assertEquals('Namespace_Module', $this->module->getExtensionName());
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
     * @param bool|false $withAttribute
     * @return object|\Umc\Base\Model\Core\Entity
     */
    protected function setupEntity($withAttribute = false)
    {
        /** @var \Umc\Base\Model\Core\Entity | object $entity */
        $entity = $this->objectManagerHelper->getObject('\Umc\Base\Model\Core\Entity');
        $entity->setData([
            'label_singular' => 'Test',
            'label_plural' => 'Tests',
            'name_singular' => 'test',
            'name_plural' => 'tests',
            'type' => 'flat',
            'is_tree' => 0,
            'add_created_to_grid' => 1,
            'add_updated_to_grid' => 1,
            'search' => 1,
            'index' => 0
        ]);
        if ($withAttribute) {
            $attribute = $this->setupAttribute();
            $entity->addAttribute($attribute);
        }
        return $entity;
    }

    /**
     * @param bool|false $withAttribute
     * @return object|\Umc\Base\Model\Core\Relation
     */
    protected function setupRelation($withAttribute = false)
    {
        $entityOne = $this->setupEntity($withAttribute);
        $entityTwo = $this->setupEntity($withAttribute);
        $entityTwo->setIndex(1);
        $entityTwo->setNamseSingular('demo');
        /** @var \Umc\Base\Model\Core\Relation | object $relation */
        $relation = $this->objectManagerHelper->getObject('\Umc\Base\Model\Core\Relation');
        $relation->setEntities($entityOne, $entityTwo, 'parent');
        return $relation;
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
        $this->formConfigSource = include $this->filePath . 'form_config.php';
        $this->formConfig->expects($this->any())->method('getConfig')->willReturn($this->formConfigSource['config']['form']['umc_module']);

        $this->restrictionConfigSource = include $this->filePath . 'restriction.php';
        $this->restrictionConfig->expects($this->any())->method('getRestrictions')->willReturn($this->restrictionConfigSource['config']['entity']['umc_module']['restriction']);


        /** @var \Umc\Base\Model\Core\Settings | object $settings */
        $settings = $this->setupSettings();
        $this->module->setSettings($settings);
        $this->module->setData([
            'module_name' => 'Module',
            'namespace' => 'Namespace'
        ]);
        $entity = $this->setupEntity(true);
        $this->module->addEntity($entity);
    }


    //TODO: test toXMl

    /**
     * tests Module->validate()
     * validation should pass
     */
    public function testValidateOk()
    {
        $this->setupValidationTest();
        $this->assertEquals([], $this->module->validate());
    }

    /**
     * tests Module->validate()
     * validation should fail due to incorrect namespace
     */
    public function testValidateNamespaceRestriction()
    {
        $this->setupValidationTest();
        $this->module->setNamespace('Magento');
        $message = $this->restrictionConfigSource['config']['entity']['umc_module']['restriction']['namespace']['val']['Magento']['message'];
        $expected = [
            'modulenamespace' => [
                new Phrase($message)
            ]
        ];
        $this->assertEquals($expected, $this->module->validate());
    }

    /**
     * tests Module->validate()
     * validation should fail due to required field
     */
    public function testValidateRequired()
    {
        $this->setupValidationTest();
        $this->module->setNamespace('');
        $map = [
            array($this->formConfigSource['config']['form']['umc_module']['fieldset']['general']['field']['namespace'], true),
        ];
        $this->formConfig->method('getBoolValue')->will($this->returnValue($map));
        $expected = [
            'modulenamespace' => [
                new Phrase('Field %1 is required', ['Module namespace'])
            ]
        ];
        $this->assertEquals($expected, $this->module->validate());
    }

    /**
     * tests Module->validationErrorKey()
     * validation should pass
     */
    public function testGetValidationErrorKey()
    {
        $field = 'test';
        $this->assertEquals('moduletest', $this->module->getValidationErrorKey($field));
    }
    //TODO: test initFromData

    /**
     * tests Module->addEntity
     */
    public function testAddEntity()
    {
        $entity = $this->setupEntity();
        $this->assertEquals(0, count($this->module->getEntities()));
        $this->module->addEntity($entity);
        $this->assertEquals(1, count($this->module->getEntities()));
    }

    /**
     * tests Module->getPlaceholders()
     */
    public function testGetPlaceholders()
    {
        $settings = $this->setupSettings();
        $this->module->setSettings($settings);
        $this->module->setData([
            'module_name' => 'Module',
            'namespace' => 'Namespace',
            'version' => '1.0.0'
        ]);
        $expected = [
            '{{Namespace}}'         => 'Namespace',
            '{{Module}}'            => 'Module',
            '{{version}}'           => '1.0.0',
            '{{sequence}}'          => '
        <sequence>
            <module name="Magento_Backend" />
        </sequence>',
            '{{module}}'            => 'module',
            '{{menuText}}'          => null,
            '{{menuSortOrder}}'     => null,
            '{{namespace}}'         => 'namespace',
            '{{menuParentValue}}' => '',
            '{{requireJsDialogs}}'  => '',
        ];
        $expected = array_merge($expected, $settings->getPlaceholders());
        $this->assertEquals($expected, $this->module->getPlaceholders());
    }

    /**
     * tests Module->getParentMenuValue()
     */
    public function testGetParentMenuValue()
    {
        $this->module->setMenuParent('Parent_Menu');
        $this->assertEquals(' parent="Parent_Menu"', $this->module->getParentMenuValue());
        $this->module->setMenuParent('');
        $this->assertEquals('', $this->module->getParentMenuValue());
    }

    /**
     * tests Module->getEntityFlag()
     */
    public function testGetEntityFlag()
    {
        $entity = $this->setupEntity();
        $entity->setIsTree(true);
        $this->assertEquals(false, $this->module->getEntityFlag('is_tree'));
        $this->module->addEntity($entity);
        $this->assertEquals(true, $this->module->getEntityFlag('is_tree'));
    }

    //TODO: test getHasFlatUpload

    /**
     * tests Module->getChildEntities()
     */
    public function testGetChildEntities()
    {
        $entity = $this->setupEntity();
        $this->assertEquals([], $this->module->getChildModels());
        $this->module->addEntity($entity);
        $children = $this->module->getChildModels();
        $this->assertEquals($entity, $children[0]);
    }

    /**
     * tests Module->getGrandChildEntities()
     */
    public function testGetGrandChildEntities()
    {
        $entity = $this->setupEntity();
        $attribute = $this->setupAttribute();
        $this->assertEquals([], $this->module->getGrandChildModels());
        $entity->addAttribute($attribute);
        $this->module->addEntity($entity);
        $children = $this->module->getGrandChildModels();
        $this->assertEquals($attribute, $children[0]);
    }

    /**
     * tests Module->getNameAttributes()
     */
    public function testGetNameAttributes()
    {
        $entity = $this->setupEntity(true);
        $this->module->addEntity($entity);
        $entity = $this->setupEntity(true);
        $entity->setIndex(1);
        $attribute = $this->setupAttribute();
        $attribute->setIsName(false)->setIndex(1);
        $entity->addAttribute($attribute);
        $this->module->addEntity($entity);
        $expected = '[0,0]';
        $this->assertEquals($expected, $this->module->getNameAttributes());
    }

    /**
     * tests Module->addRelation()
     */
    public function testAddRelation()
    {
        $relation = $this->setupRelation();
        $this->assertEquals(0, count($this->module->getRelations()));
        $this->module->addRelation($relation);
        $this->assertEquals(1, count($this->module->getRelations()));
    }

    /**
     * tests Module->getRelations()
     */
    public function testGetRelations()
    {
        $relation = $this->setupRelation();
        $this->module->addRelation($relation);
        $relations = $this->module->getRelations();
        $this->assertEquals($relation, $relations[0]);
    }

    /**
     * tests Module->getRelationsAsJson()
     */
    public function testGetRelationsAsJson()
    {
        $relation = $this->setupRelation();
        $this->module->addRelation($relation);
        $expected = '{"0_1":"parent"}';
        $this->assertEquals($expected, $this->module->getRelationsAsJson());
        $relation = $this->setupRelation();
        $entities = $relation->getEntities();
        $entities[0]->setIndex(2);
        $entities[1]->setIndex(3);
        $this->module->addRelation($relation);
        $expected = '{"0_1":"parent","2_3":"parent"}';
        $this->assertEquals($expected, $this->module->getRelationsAsJson());
    }
}
