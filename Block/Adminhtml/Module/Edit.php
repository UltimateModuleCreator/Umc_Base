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
namespace Umc\Base\Block\Adminhtml\Module;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Block\Adminhtml\Module\Edit\Tab\AbstractTab;
use Umc\Base\Config\Form as FormConfig;

/**
 * @api
 */
class Edit extends Container
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Umc\Base\Config\Form
     */
    protected $formConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormConfig $formConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormConfig $formConfig,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->formConfig   = $formConfig;
        parent::__construct($context, $data);
    }

    /**
     * set form data and buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Umc_Base';
        $this->_controller = 'adminhtml_module';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Module'));
        $this->buttonList->remove('delete');
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label'     => __('Save and Continue Edit'),
                'class'     => 'save',
                'data_attribute'  => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ],
            ],
            -100
        );
        $this->buttonList->add(
            'add-entity',
            [
                'label'     => __('Add Entity'),
                'class'     => 'save add-entity',
            ],
            -150
        );
        $this->buttonList->add(
            'add-relation',
            [
                'label'     => __('Add Relation'),
                'class'     => 'save add-relation',
            ],
            -200
        );
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {

        $module = $this->getModule();
        if ($module->getNamespace() && $module->getModuleName()) {
            $title = __('Edit Module "%1"', $module->getExtensionName());
        } else {
            $title = __('Create module');
        }
        return $title;
    }

    /**
     * @return \Umc\Base\Api\Data\ModuleInterface $module
     */
    public function getModule()
    {
        return $this->coreRegistry->registry('current_module');
    }

    /**
     * get url for validating the module
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->_urlBuilder->getUrl('umc/module/validate');
    }

    /**
     * get entity dependencies as json
     *
     * @return string
     */
    public function getModuleDepends()
    {
        return $this->formConfig->getDepends(ModuleInterface::ENTITY_CODE);
    }

    /**
     * get entity dependencies as json
     *
     * @return string
     */
    public function getEntityDepends()
    {
        return $this->formConfig->getDepends(EntityInterface::ENTITY_CODE);
    }

    /**
     * get attribute dependencies as json
     *
     * @return string
     */
    public function getAttributeDepends()
    {
        return $this->formConfig->getDepends(AttributeInterface::ENTITY_CODE);
    }

    /**
     * @return string
     */
    public function getRelationDepends()
    {
        return $this->formConfig->getDepends(RelationInterface::ENTITY_CODE);
    }

    /**
     * @return string
     */
    public function getNameAttributes()
    {
        return $this->getModule()->getNameAttributes();
    }

    /**
     * tooltips type
     *
     * @return string
     */
    public function getTooltipType()
    {
        return $this->_scopeConfig->getValue(AbstractTab::XML_TOOLTIP_TYPE_PATH);
    }

    /**
     * @return string
     */
    public function getRelationsAsJson()
    {
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        $module = $this->coreRegistry->registry('current_module');
        if ($module) {
            return $module->getRelationsAsJson();
        }
        return '{}';
    }
}
