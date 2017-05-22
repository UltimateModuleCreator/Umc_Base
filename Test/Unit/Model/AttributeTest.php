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
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\EntityInterfaceFactory;
use Umc\Base\Api\Data\Attribute\TypeInterface;
use Umc\Base\Config\SaveAttributes;
use Umc\Base\Model\Attribute;
use Umc\Base\Model\Attribute\Type\Factory as AttributeTypeFactory;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Config\Restriction as RestrictionConfig;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AttributeTypeFactory
     */
    protected $typeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityInterfaceFactory
     */
    protected $entityFactory;

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
     * @var Attribute
     */
    protected $attribute;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->typeFactory = $this->getMock(AttributeTypeFactory::class, [], [], '', false);
        $this->entityFactory = $this->getMock(EntityInterfaceFactory::class, [], [], '', false);
        $this->saveAttributesConfig = $this->getMock(SaveAttributes::class, [], [], '', false);
        $this->formConfig = $this->getMock(FormConfig::class, [], [], '', false);
        $this->restrictionConfig = $this->getMock(RestrictionConfig::class, [], [], '', false);
        $this->escaper = new Escaper();
        $this->attribute = new Attribute(
            $this->saveAttributesConfig,
            $this->formConfig,
            $this->escaper,
            $this->typeFactory,
            []
        );
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->typeFactory          = null;
        $this->entityFactory        = null;
        $this->saveAttributesConfig = null;
        $this->formConfig           = null;
        $this->restrictionConfig    = null;
        $this->escaper              = null;
        $this->attribute            = null;
        parent::tearDown();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityInterface
     */
    protected function setupEntity()
    {
        return $this->getMock(EntityInterface::class, [], [], '', false);
    }

    /**
     * @tests Attribute::setEntity()
     */
    public function testSetEntity()
    {
        $entity = $this->setupEntity();
        $this->attribute->setEntity($entity);
        $this->assertEquals($entity, $this->attribute->getEntity());
    }

    /**
     * @tests Attribute::getAdminGridFilter();
     */
    public function testGetAdminGridFilterForTree()
    {
        $entity = $this->setupEntity();
        $this->attribute->setData(AttributeInterface::ADMIN_GRID_FILTER, 1);
        $entity->method('getIsTree')->willReturn(0);
        $this->attribute->setEntity($entity);
        $this->assertTrue($this->attribute->getAdminGridFilter());
        $this->attribute->setData(AttributeInterface::ADMIN_GRID_FILTER, 0);
        $this->assertFalse($this->attribute->getAdminGridFilter());

    }

    /**
     * @tests Attribute::getAdminGridFilter();
     */
    public function testGetAdminGridFilterNotTree()
    {
        $entity = $this->setupEntity();
        $this->attribute->setData(AttributeInterface::ADMIN_GRID_FILTER, 1);
        $entity->method('getIsTree')->willReturn(1);
        $this->attribute->setEntity($entity);
        $this->assertFalse($this->attribute->getAdminGridFilter());
        $this->attribute->setData(AttributeInterface::ADMIN_GRID_FILTER, 0);
        $this->assertFalse($this->attribute->getAdminGridFilter());
    }

    /**
     * @tests Attribute::getValidationErrorKey()
     */
    public function testGetValidationErrorKey()
    {
        $entity = $this->setupEntity();
        $entity->method("getIndex")->willReturn(2);
        $this->attribute->setEntity($entity);
        $this->attribute->setIndex(1);
        $this->assertEquals('attribute_2_1_field', $this->attribute->getValidationErrorKey('field'));
    }

    /**
     * @tests Attribute::getParent()
     */
    public function testGetParent()
    {
        $entity = $this->setupEntity();
        $this->attribute->setEntity($entity);
        $this->assertEquals($entity, $this->attribute->getParent());
    }

    /**
     * @tests Attribute::getCodeCamelCase()
     */
    public function testGetCodeCamelCase()
    {
        $this->attribute->setCode('some_code');
        $this->assertEquals('someCode', $this->attribute->getCodeCamelCase());
        $this->assertEquals('SomeCode', $this->attribute->getCodeCamelCase(true));
    }

    /**
     * @tests Attribute::getEntityType()
     */
    public function testGetEntityType()
    {
        $entity = $this->setupEntity();
        $entity->expects($this->once())->method('getType');
        $this->attribute->setEntity($entity);
        $this->attribute->getEntityType();
    }

    /**
     * @tests Attribute::getAdminGrid()
     */
    public function testGetAdminGrid()
    {
        $this->attribute->setData(AttributeInterface::ADMIN_GRID, 1);
        $this->attribute->setData(AttributeInterface::IS_NAME, 1);
        $this->assertEquals(0, $this->attribute->getAdminGrid());
        $this->attribute->setData(AttributeInterface::IS_NAME, 0);
        $this->assertEquals(1, $this->attribute->getAdminGrid());
        $this->attribute->setData(AttributeInterface::ADMIN_GRID, 0);
        $this->assertEquals(0, $this->attribute->getAdminGrid());
        $this->attribute->setData(AttributeInterface::IS_NAME, 0);
        $this->assertEquals(0, $this->attribute->getAdminGrid());
    }

    /**
     * @tests Attribute::getAdminGridNotRestricted()
     */
    public function testGetAdminGridNotRestricted()
    {
        $this->attribute->setData(AttributeInterface::ADMIN_GRID, 1);
        $this->attribute->setData(AttributeInterface::IS_NAME, 1);
        $this->assertTrue($this->attribute->getAdminGridNotRestricted());
        $this->attribute->setData(AttributeInterface::IS_NAME, 0);
        $this->assertTrue($this->attribute->getAdminGridNotRestricted());
        $this->attribute->setData(AttributeInterface::ADMIN_GRID, 0);
        $this->assertFalse($this->attribute->getAdminGridNotRestricted());
        $this->attribute->setData(AttributeInterface::IS_NAME, 0);
        $this->assertFalse($this->attribute->getAdminGridNotRestricted());
    }

    /**
     * @tests Attribute::getLabel()
     */
    public function testGetLabel()
    {
        $this->attribute->setLabel('some label');
        $this->assertEquals('Some Label', $this->attribute->getLabel());
    }

    /**
     * @tests Attribute::getColumnSetup()
     */
    public function testGetColumnSetup()
    {
        $type = $this->getMock(TypeInterface::class, [], [], '', false);
        $type->method('getSqlTypeConst')->willReturn('text');
        $type->method('getSetupLength')->willReturn(255);
        $entity = $this->setupEntity();
        $entity->method('getLabelSingular')->willReturn('Entity');
        $this->typeFactory->method('create')->willReturn($type);
        $this->attribute->setData([
            'code' => 'code',
            'label' => 'Label',
        ]);
        $this->attribute->setEntity($entity);
        $expected = "            ->addColumn(
                'code',
                {{class Magento\\Framework\\DB\\Ddl\\Table}}::text,
                255,
                [],
                'Entity Label'
            )\n";
        $this->assertEquals($expected, $this->attribute->getColumnSetup());
    }
}
