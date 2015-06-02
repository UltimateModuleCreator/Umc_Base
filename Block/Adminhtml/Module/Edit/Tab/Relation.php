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
 * @copyright 2015 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
namespace Umc\Base\Block\Adminhtml\Module\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Relation extends AbstractTab implements TabInterface
{
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
} 