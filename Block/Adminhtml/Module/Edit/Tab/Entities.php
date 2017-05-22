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
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Block\Adminhtml\EntityFactory as EntityBlockFactory;

/**
 * @api
 */
class Entities extends AbstractTab implements TabInterface
{
    /**
     * @var EntityBlockFactory
     */
    protected $entityBlockFactory;

    /**
     * @var string
     */
    protected $entityBlockTemplate;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FormConfig $formConfig
     * @param string $entityCode
     * @param EntityBlockFactory $entityBlockFactory
     * @param string $entityBlockTemplate
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FormConfig $formConfig,
        $entityCode,
        EntityBlockFactory $entityBlockFactory,
        $entityBlockTemplate,
        array $data = []
    ) {
        $this->entityBlockFactory  = $entityBlockFactory;
        $this->entityBlockTemplate = $entityBlockTemplate;
        parent::__construct($context, $registry, $formFactory, $formConfig, $entityCode, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Entities');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * get the current entities
     *
     * @return \Umc\Base\Api\Data\EntityInterface[]
     */
    public function getEntities()
    {
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        if ($module = $this->_coreRegistry->registry('current_module')) {
            return $module->getEntities();
        }
        return [];
    }

    /**
     * @param string $increment
     * @param null|EntityInterface $entity
     * @return \Umc\Base\Block\Adminhtml\Entity
     */
    public function getEntityBlock($increment, EntityInterface $entity = null)
    {
        $entityBlock = $this->entityBlockFactory->create();
        $entityBlock->setTemplate($this->entityBlockTemplate);
        $entityBlock->setEntity($entity);
        $entityBlock->setData('increment', $increment);
        return $entityBlock;
    }
}
