<?php
/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Test\Unit\Model;

use Magento\Framework\Escaper;
use Magento\Framework\Composer\ComposerInformation;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\AttributeInterfaceFactory;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\EntityInterfaceFactory;
use Umc\Base\Api\Data\FactoryInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\Restriction;
use Umc\Base\Config\SaveAttributes;
use Umc\Base\Model\Composer;
use Umc\Base\Model\ModelInterfaceFactory;
use Umc\Base\Model\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    const XML_HEADER = '<?xml version="1.0" encoding="UTF-8"?>';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Api\Data\EntityInterfaceFactory
     */
    protected $entityFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Api\Data\AttributeInterfaceFactory
     */
    protected $attributeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Config\SaveAttributes
     */
    protected $saveAttributesConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Config\Form
     */
    protected $formConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Umc\Base\Config\Restriction
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
     * @var ComposerInformation | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $composerInformation;

    /**
     * @var Composer | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $composer;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entityFactory = $this->getMock(ModelInterfaceFactory::class, [], [], '', false);
        $this->attributeFactory = $this->getMock(ModelInterfaceFactory::class, [], [], '', false);
        $this->saveAttributesConfig = $this->getMock(SaveAttributes::class, [], [], '', false);
        $this->formConfig = $this->getMock(FormConfig::class, [], [], '', false);
        $this->restrictionConfig = $this->getMock(Restriction::class, [], [], '', false);
        $this->composerInformation = $this->getMock(ComposerInformation::class, [], [], '', false);
        $this->escaper = new Escaper();
        $this->composer = $this->getMock(Composer::class, [], [], '', false);

        $this->formConfigSource = $this->getFormConfig();
        $this->formConfig->method('getConfig')
            ->willReturn($this->formConfigSource['config']['form']['umc_module']);

        $this->saveAttributesConfig->method('getAttributes')->willReturn(['namespace', 'module_name']);

        $entity = $this->setupEntity();
        $this->entityFactory->method('create')->willReturn($entity);

        $this->module = new Module(
            $this->saveAttributesConfig,
            $this->formConfig,
            $this->escaper,
            $this->composerInformation,
            $this->composer,
            [
                FactoryInterface::ATTRIBUTE_FACTORY_KEY => $this->attributeFactory,
                FactoryInterface::ENTITY_FACTORY_KEY => $this->entityFactory,
            ],
            [
                'Magento_Backend' => 10
            ],
            [],
            []
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->entityFactory        = null;
        $this->attributeFactory     = null;
        $this->saveAttributesConfig = null;
        $this->formConfig           = null;
        $this->restrictionConfig    = null;
        $this->escaper              = null;
        $this->module               = null;
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
     * Tests Module::getExtensionName()
     */
    public function testGetExtensionNameWrong()
    {
        $this->assertEquals('', $this->module->getExtensionName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityInterface
     */
    protected function setupEntity()
    {
        $entity = $this->getMock(EntityInterface::class, [], [], '', false);
        $entity->method('validate')->willReturn([]);
        return $entity;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AttributeInterface
     */
    protected function setupAttribute()
    {
        $attribute = $this->getMock(AttributeInterface::class, [], [], '', false);
        $attribute->method('validate')->willReturn([]);
        return $attribute;
    }

    public function getFormConfig()
    {
        return [
            'config' => [
                'form' => [
                    'umc_module' => [
                        'id' => 'umc_module',
                        'fieldset' => [
                            'general' => [
                                'id'    => 'general',
                                'sort'  => 10,
                                'collapsible' => true,
                                'label' => 'Module',
                                'field' => [
                                    'namespace' => [
                                        'id' => 'namespace',
                                        'type' => 'text',
                                        'required' => true,
                                        'sort' => 10,
                                        'system' => true,
                                        'label' => 'Module namespace',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @tests Module::toXml()
     */
    public function testToXml()
    {
        $openTag =  '<umc_module>'."\n";
        $closeTag =  '</umc_module>'."\n";
        $namespace = '<namespace>demo</namespace>'."\n";
        $moduleName = '<module_name>test</module_name>'."\n";
        $entities = '<entities>Entities XML here</entities>';
        $entity = $this->setupEntity();
        $entity->method('toXml')->willReturn('Entities XML here');
        $this->module->addEntity($entity);
        $this->module->setData([
            'namespace' => 'demo',
            'module_name' => 'test',
            'dummy' => 'dummy'
        ]);
        $xml = $this->module->toXml([], null, false, false);
        $this->assertContains($openTag, $xml);
        $this->assertContains($closeTag, $xml);
        $this->assertContains($namespace, $xml);
        $this->assertContains($moduleName, $xml);
        $this->assertContains($entities, $xml);
        $this->assertNotContains('<dummy>dummy</dummy>', $xml);
        $this->assertNotContains(self::XML_HEADER, $xml);

        $xml = $this->module->toXml([], 'module', false, false);
        $this->assertNotContains($openTag, $xml);
        $this->assertNotContains($closeTag, $xml);
        $this->assertNotContains(self::XML_HEADER, $xml);

        $xml = $this->module->toXml([], null, true, false);
        $this->assertContains(self::XML_HEADER, $xml);
    }

    /**
     * @tests Module::validationErrorKey()
     * validation should pass
     */
    public function testGetValidationErrorKey()
    {
        $field = 'test';
        $this->assertEquals('umc_moduletest', $this->module->getValidationErrorKey($field));
    }

    /**
     * @tests Module::initFromData
     */
    public function testInitFromData()
    {
        $data = [
            'umc_module' => [
                'namespace' => 'Demo',
                'module_name' => 'Test',
                'version' => '1.0.0'
            ],
            'entity' => [
                0 => [
                    'dummy' => 'dummy',
                ]
            ]
        ];
        $this->module->initFromData($data);
        $this->assertEquals('Demo', $this->module->getNamespace());
        $this->assertEquals('Test', $this->module->getModuleName());
        $this->assertEquals('1.0.0', $this->module->getVersion());
        $this->assertEquals(1, count($this->module->getEntities()));
    }

    /**
     * @tests Module::addEntity()
     */
    public function testAddEntity()
    {
        $entity = $this->setupEntity();
        $entity->method('getNameSingular')->willReturn('entity1');
        $this->assertEquals(0, count($this->module->getEntities()));
        $this->module->addEntity($entity);
        $this->assertEquals(1, count($this->module->getEntities()));
        $entity1 = $this->setupEntity();
        $entity1->method('getNameSingular')->willReturn('entity1');
        $this->setExpectedException(\Exception::class, "You cannot have 2 entities with the code entity1");
        $this->module->addEntity($entity1);
    }

    /**
     * @tests Module::getPlaceholders()
     */
    public function testGetPlaceholders()
    {
        $this->module->setData([
            'module_name' => 'Module',
            'namespace' => 'Namespace',
            'version' => '1.0.0',
            'dummy' => 'dummy'
        ]);
        $placeholders = $this->module->getPlaceholders();
        $this->assertArrayHasKey('{{Namespace}}', $placeholders);
        $this->assertArrayHasKey('{{Module}}', $placeholders);
        $this->assertArrayHasKey('{{version}}', $placeholders);
        $this->assertArrayNotHasKey('{{dummy}}', $placeholders);
    }

    /**
     * @tests Module::getParentMenuValue()
     */
    public function testGetParentMenuValue()
    {
        $this->module->setMenuParent('Parent_Menu');
        $this->assertEquals(' parent="Parent_Menu"', $this->module->getParentMenuValue());
        $this->module->setMenuParent('');
        $this->assertEquals('', $this->module->getParentMenuValue());
    }

    /**
     * @tests Module::getEntityFlag()
     */
    public function testGetEntityFlag()
    {
        $entity = $this->setupEntity();
        $entity->method('getDataUsingMethod')->willReturnMap([
            ['is_tree', true],
            ['dummy', false]
        ]);
        $this->assertFalse($this->module->getEntityFlag('is_tree'));
        $this->module->addEntity($entity);
        $this->assertTrue($this->module->getEntityFlag('is_tree'));
        $this->assertFalse($this->module->getEntityFlag('dummy'));
    }

    /**
     * @tests Module::getChildModels()
     */
    public function testGetChildModels()
    {
        $entity = $this->setupEntity();
        $this->assertEquals([], $this->module->getChildModels());
        $entity->method('getNameSingular')->willReturn('name');
        $this->module->addEntity($entity);
        $children = $this->module->getChildModels();
        $this->assertEquals($entity, $children['name']);
    }

    /**
     * @tests Module::getGrandChildModels()
     */
    public function testGetGrandChildEntities()
    {
        $entity = $this->setupEntity();
        $attribute = $this->setupAttribute();
        $this->assertEquals([], $this->module->getGrandChildModels());
        $entity->method('getAttributes')->willReturn([$attribute]);
        $this->module->addEntity($entity);
        $children = $this->module->getGrandChildModels();
        $this->assertEquals($attribute, $children[0]);
    }

    /**
     * @tests Module::getNameAttributes()
     */
    public function testGetNameAttributes()
    {
        $attribute = $this->setupAttribute();
        $attribute->method('getIndex')->willReturn(0);
        $entity = $this->setupEntity();
        $entity->method('getNameSingular')->willReturn('entity1');
        $entity->method('getNameAttribute')->willReturn($attribute);
        $entity->method('getIndex')->willReturn(0);
        $this->module->addEntity($entity);

        $attribute = $this->setupAttribute();
        $attribute->method('getIndex')->willReturn(1);
        $entity = $this->setupEntity();
        $entity->method('getNameAttribute')->willReturn($attribute);
        $entity->method('getNameSingular')->willReturn('entity2');
        $entity->method('getIndex')->willReturn(1);
        $this->module->addEntity($entity);

        $expected = '[0,1]';
        $this->assertEquals($expected, $this->module->getNameAttributes());
    }
}
