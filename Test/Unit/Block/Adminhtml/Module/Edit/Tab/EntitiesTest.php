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
 * @copyright Copyright (c) 2014
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Test\Unit\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Block\Adminhtml\EntityFactory as EntityBlockFactory;
use Umc\Base\Block\Adminhtml\Module\Edit\Tab\Entities;
use Umc\Base\Block\Adminhtml\Entity;

class EntitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | Context
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | Registry
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | FormFactory
     */
    protected $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | FormConfig
     */
    protected $formConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | EntityBlockFactory
     */
    protected $entityBlockFactory;

    /**
     * @var Entities
     */
    protected $entitiesTab;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->context            = $this->getMock(Context::class, [], [], '', false);
        $this->registry           = $this->getMock(Registry::class, [], [], '', false);
        $this->formFactory        = $this->getMock(FormFactory::class, [], [], '', false);
        $this->formConfig         = $this->getMock(FormConfig::class, [], [], '', false);
        $this->entityBlockFactory = $this->getMock(EntityBlockFactory::class, [], [], '', false);

        $this->entitiesTab        = new Entities(
            $this->context,
            $this->registry,
            $this->formFactory,
            $this->formConfig,
            'umc_entity',
            $this->entityBlockFactory,
            'template.phtml'
        );
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->context            = null;
        $this->registry           = null;
        $this->formFactory        = null;
        $this->formConfig         = null;
        $this->entityBlockFactory = null;
        $this->entitiesTab        = null;
        parent::tearDown();
    }

    /**
     * @tests Entities:getEntityBlock()
     */
    public function testGetEntityBlock()
    {
        $blockMock = $this->getMock(Entity::class, [], [], '', false);
        $blockMock->expects($this->once())->method('setData');
        $blockMock->expects($this->once())->method('setEntity');
        $this->entityBlockFactory->method('create')->willReturn($blockMock);
        $this->entitiesTab->getEntityBlock(1, null);
    }

    /**
     * @tests Entities::getEntities()
     */
    public function testGetEntities()
    {
        $module = $this->getMock(ModuleInterface::class, [], [], '', false);
        $entity = $this->getMock(EntityInterface::class, [], [], '', false);
        $module->method('getEntities')->willReturn([$entity]);
        $this->registry->method('registry')->willReturn($module);
        $this->assertEquals([$entity], $this->entitiesTab->getEntities());
    }
}
