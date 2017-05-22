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
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * @api
 */
class Module extends AbstractTab implements TabInterface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Module');
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
     * prepare the form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setData('html_id_prefix', $this->entityCode);
        $this->getForm()->addFieldNameSuffix($this->entityCode);
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        $module = $this->_coreRegistry->registry('current_module');
        if ($module && count($module->getData())) {
            $this->getForm()->addValues($module->getData());
        } else {
            $this->getForm()->addValues($this->_scopeConfig->getValue('umc/'.$this->entityCode));
        }
        return $this;
    }
}
