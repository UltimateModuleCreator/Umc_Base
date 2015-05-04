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
 * @copyright 2015 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Block\Adminhtml\Module;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Umc\Base\Model\Config\Form as FormConfig;
use Umc\Base\Model\Core\Attribute;
use Umc\Base\Model\Core\Entity;

class Edit extends Container
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Umc\Base\Model\Config\Form
     */
    protected $formConfig;

    /**
     * @var \Umc\Base\Model\Core\Entity
     */
    protected $entity;

    /**
     * @var \Umc\Base\Model\Core\Attribute
     */
    protected $attribute;

    /**
     * constructor
     *
     * @param Registry $registry
     * @param FormConfig $formConfig
     * @param Entity $entity
     * @param Attribute $attribute
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        FormConfig $formConfig,
        Entity $entity,
        Attribute $attribute,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->formConfig   = $formConfig;
        $this->entity       = $entity;
        $this->attribute    = $attribute;
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
            $title = __('Edit Module "%1"', $module->getNamespace().'_'.$module->getModuleName());
        } else {
            $title = __('Create module');
        }
        return $title;
    }

    /**
     * @return \Umc\Base\Model\Core\Module $module
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
    public function getEntityDepends()
    {
        return $this->formConfig->getDepends($this->entity->getEntityCode());
    }

    /**
     * get attribute dependencies as json
     *
     * @return string
     */
    public function getAttributeDepends()
    {
        return $this->formConfig->getDepends($this->attribute->getEntityCode());
    }

    public function getNameAttributes()
    {
        return $this->getModule()->getNameAttributes();
    }
}
