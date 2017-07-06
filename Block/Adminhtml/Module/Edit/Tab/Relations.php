<?php
/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE_UMC2.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Config\Form as FormConfig;
use Umc\Base\Block\Adminhtml\RelationFactory as RelationBlockFactory;

/**
 * @api
 */
class Relations extends AbstractTab implements TabInterface
{
    /**
     * @var RelationBlockFactory
     */
    protected $relationBlockFactory;

    /**
     * @var string
     */
    protected $relationBlockTemplate;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FormConfig $formConfig
     * @param string $entityCode
     * @param RelationBlockFactory $relationBlockFactory
     * @param string $relationBlockTemplate
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FormConfig $formConfig,
        $entityCode,
        RelationBlockFactory $relationBlockFactory,
        $relationBlockTemplate,
        array $data = []
    ) {
        $this->relationBlockFactory  = $relationBlockFactory;
        $this->relationBlockTemplate = $relationBlockTemplate;
        parent::__construct($context, $registry, $formFactory, $formConfig, $entityCode, $data);
    }

    /**
     * get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Entity Relations');
    }

    /**
     * get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Not important if it's hidden or not
     * The JS will take care of that
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * get the current entities
     *
     * @return \Umc\Base\Api\Data\RelationInterface[]
     */
    public function getRelations()
    {
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        if ($module = $this->_coreRegistry->registry('current_module')) {
            return $module->getRelations();
        }
        return [];
    }

    /**
     * @param int $increment
     * @param RelationInterface|null $relation
     * @return \Umc\Base\Block\Adminhtml\Relation
     */
    public function getRelationBlock($increment, RelationInterface $relation = null)
    {
        /** @var \Umc\Base\Block\Adminhtml\Relation $entityBlock */
        $entityBlock = $this->relationBlockFactory->create();
        $entityBlock->setTemplate($this->relationBlockTemplate);
        $entityBlock->setRelation($relation);
        $entityBlock->setData('increment', $increment);
        return $entityBlock;
    }
}
