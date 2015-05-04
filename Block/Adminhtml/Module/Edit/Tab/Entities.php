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

class Entities extends AbstractTab implements TabInterface
{
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
     * @return \Umc\Base\Model\Core\Entity[]
     */
    public function getEntities()
    {
        /** @var \Umc\Base\Model\Core\Module $module */
        if ($module = $this->_coreRegistry->registry('current_module')) {
            return $module->getEntities();
        }
        return [];
    }
}
