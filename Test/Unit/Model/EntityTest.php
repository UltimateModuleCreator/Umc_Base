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

use Umc\Base\Model\Entity;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;
use Umc\Base\Model\Entity\Type\Factory as EntityTypeFactory;
use Umc\Base\Api\Data\AttributeInterfaceFactory;
use Umc\Base\Config\SaveAttributes;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\Restriction as RestrictionConfig;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\ModuleInterface;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityTypeFactory
     */
    protected $typeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AttributeInterfaceFactory
     */
    protected $attributeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SaveAttributes
     */
    protected $saveAttributesConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormConfig
     */
    protected $formConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RestrictionConfig
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
        $this->typeFactory = $this->getMock(EntityTypeFactory::class, [], [], '', false);
        $this->attributeFactory = $this->getMock(AttributeInterfaceFactory::class, [], [], '', false);
        $this->saveAttributesConfig = $this->getMock(SaveAttributes::class, [], [], '', false);
        $this->formConfig = $this->getMock(FormConfig::class, [], [], '', false);
        $this->restrictionConfig = $this->getMock(RestrictionConfig::class, [], [], '', false);
        $this->escaper = new Escaper();

        $this->formConfigSource = $this->getFormConfig();
        $this->formConfig->method('getConfig')
            ->willReturn($this->formConfigSource['config']['form']['umc_entity']);

        $this->restrictionConfigSource = $this->getRestrictionConfig();
        $this->restrictionConfig->method('getRestrictions')
            ->willReturn($this->restrictionConfigSource['config']['entity']['umc_entity']['restriction']);

        $this->saveAttributesConfig->method('getAttributes')->willReturn(['name_singular', 'name_plural']);

        $this->entity = new Entity(
            $this->saveAttributesConfig,
            $this->formConfig,
            $this->escaper,
            $this->typeFactory,
            $this->attributeFactory,
            []
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->typeFactory          = null;
        $this->attributeFactory     = null;
        $this->saveAttributesConfig = null;
        $this->formConfig           = null;
        $this->restrictionConfig    = null;
        $this->escaper              = null;
        $this->entity               = null;

        parent::tearDown();
    }

    /**
     * @return ModuleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function setupModule()
    {
        /** @var ModuleInterface */
        $module = $this->getMock(ModuleInterface::class, [], [], '', false);
        return $module;
    }

    /**
     * @return AttributeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function setupAttribute()
    {
        $attribute = $this->getMock(AttributeInterface::class, [], [], '', false);
        $attribute->method('validate')->willReturn([]);
        return $attribute;
    }

    /**
     * get restriction config
     * @return array
     */
    protected function getRestrictionConfig()
    {
        return [
            'config' => [
                'entity' => [
                    'umc_entity' => [
                        'id' =>' umc_entity',
                        'restriction' => [
                            'name_singular' => [
                                'id' => 'name_singular',
                                'val' => [
                                    'resource' => [
                                        'id' => 'resource',
                                        'translate' => true,
                                        'real_val' => 'resource',
                                        'message' => "You cannot use this value here. '.
                                        'It will conflict with the Magento folder structure."
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * get form config
     * @return array
     */
    protected function getFormConfig()
    {
        return [
            'config' => [
                'form' => [
                    'umc_entity' => [
                        'id' => 'umc_entity',
                        'fieldset' => [
                            'name_settings' => [
                                'id'    => 'name_settings',
                                'sort'  => 10,
                                'collapsible' => true,
                                'label' => 'Name Settings',
                                'field' => [
                                    'label_singular' => [
                                        'id' => 'label_singular',
                                        'type' => 'text',
                                        'required' => true,
                                        'sort' => 10,
                                        'system' => true,
                                        'label' => 'Entity Label Singular',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                    'label_plural' => [
                                        'id' => 'label_plural',
                                        'type' => 'text',
                                        'required' => true,
                                        'sort' => 20,
                                        'system' => true,
                                        'label' => 'Entity Label Plural',
                                        'tooltip' => 'tooltip goes here',
                                    ],
                                    'name_singular' => [
                                        'id' => 'name_singular',
                                        'type' => 'text',
                                        'required' => true,
                                        'sort' => 20,
                                        'system' => true,
                                        'label' => 'Entity Name Singular',
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
     * @tests Entity::setModule()
     */
    public function testSetModule()
    {
        $module = $this->setupModule();
        $this->assertEquals(null, $this->entity->getModule());
        $this->entity->setModule($module);
        $this->assertEquals($module, $this->entity->getModule());
    }

    /**
     * @tests Entity::getType()
     */
    public function testGetType()
    {
        $type = 'flat';
        $this->entity->setData('type', $type);
        $this->assertEquals($type, $this->entity->getType());
    }

    /**
     * @tests Entity::getLabelSingular()
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
     * @tests Entity::getLabelPlural()
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
     * @tests Entity::addAttribute()
     */
    public function testAddAttribute()
    {
        $this->assertEquals(0, count($this->entity->getAttributes()));
        $this->entity->addAttribute($this->setupAttribute());
        $this->assertEquals(1, count($this->entity->getAttributes()));
    }

    /**
     * @tests Entity::getAttributes()
     */
    public function testGetAttributes()
    {
        $attribute = $this->setupAttribute();
        $this->entity->addAttribute($attribute);
        $attributes = $this->entity->getAttributes();
        $this->assertEquals($attribute, $attributes[0]);
    }

    /**
     * @tests Entity::toXml()
     */
    public function testToXml()
    {
        $openTag =  '<umc_entity>'."\n";
        $closeTag =  '</umc_entity>'."\n";
        $nameSingular = '<name_singular>entity</name_singular>'."\n";
        $namePlural = '<name_plural>entities</name_plural>'."\n";
        $attributes = '<attributes>Attributes XML here</attributes>';
        $attribute = $this->setupAttribute();
        $attribute->method('toXml')->willReturn('Attributes XML here');
        $this->entity->addAttribute($attribute);
        $this->entity->setData([
            'name_singular' => 'entity',
            'name_plural' => 'entities',
            'code' => 'some code'
        ]);
        $expected = $openTag.$nameSingular.$namePlural.$attributes.$closeTag;
        $this->assertEquals($expected, $this->entity->toXml([], null, false, false));
        $expected = '<entity>'."\n".$nameSingular.$namePlural.$attributes.'</entity>'."\n";
        $this->assertEquals($expected, $this->entity->toXml([], 'entity', false, false));
        $expected = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
            $openTag.$nameSingular.$namePlural.$attributes.$closeTag;
        $this->assertEquals($expected, $this->entity->toXml([], null, true, false));
    }

    /**
     * @tests Entity::getNameAttribute
     */
    public function testGetNameAttribute()
    {
        $this->assertNull($this->entity->getNameAttribute());
        $attribute = $this->setupAttribute();
        $attribute->method('getIsName')->willReturn(true);
        $this->entity->addAttribute($attribute);
        $this->assertEquals($attribute, $this->entity->getNameAttribute());
    }

    /**
     * @tests Entity::getValidationErrorKey
     */
    public function testGetValidationErrorKey()
    {
        $this->entity->setIndex(0);
        $field = 'test';
        $this->assertEquals('entity_0_'.$field, $this->entity->getValidationErrorKey($field));
    }

    /**
     * @tests Entity::getNameSingular()
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
     * @tests Entity::getNamePlural()
     */
    public function testGetNamePlural()
    {
        $this->entity->setData([
            'name_plural' => 'entities'
        ]);
        $this->assertEquals('entities', $this->entity->getNamePlural());
        $this->assertEquals('Entities', $this->entity->getNamePlural(true));
    }

    /**
     * @tests Entity::getParent()
     */
    public function testGetParent()
    {
        $module = $this->setupModule();
        $this->entity->setModule($module);
        $this->assertEquals($module, $this->entity->getParent());
    }

    /**
     * @test Entity:getDateAttributeCodes()
     */
    public function testGetDateAttributeCodes()
    {
        $this->assertEquals('[]', $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->method('getType')->willReturn('date');
        $attribute->method('getCode')->willReturn('name');
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name']", $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->method('getType')->willReturn('date');
        $attribute->method('getCode')->willReturn('some_date');
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name', 'some_date']", $this->entity->getDateAttributeCodes());
        $attribute = $this->setupAttribute();
        $attribute->setCode('something');
        $attribute->method('getType')->willReturn('text');
        $this->entity->addAttribute($attribute);
        $this->assertEquals("['name', 'some_date']", $this->entity->getDateAttributeCodes());
    }
}
