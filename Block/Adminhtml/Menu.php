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
namespace Umc\Base\Block\Adminhtml;

use Magento\Backend\Block\Menu as BackendMenu;
use Magento\Backend\Model\Menu as MenuModel;
use Magento\Backend\Model\Menu\Item as MenuItem;

/**
 * @api
 */
class Menu extends BackendMenu
{
    /**
     * draw the menu
     *
     * @param MenuModel $menu
     * @param string $parentId
     * @param int $level
     * @return string
     */
    public function renderUmcMenu(MenuModel $menu, $parentId = '', $level = 0)
    {
        $output = '<ul ' . (0 == $level ? 'id="umc-nav"' : '') . ' >';
        $output .= $this->renderSelector($parentId, 0);
        foreach ($this->_getMenuIterator($menu) as $key => $menuItem) {
            /** @var \Magento\Backend\Model\Menu\Item $menuItem */
            $output .= '<li '
                . ' class="' . $this->_renderItemCssClass($menuItem, $level) . '"'
                . $this->getUiId($menuItem->getId()) . '>';
            $output .= $this->renderAnchorWithoutLink($menuItem);
            $output .= $this->renderUmcMenu($menuItem->getChildren(), $menuItem->getId(), $level + 1);
            $output .='</li>';
            $output .= $this->renderSelector($parentId, $key);
        }
        $output .= '</ul>';
        return $output;
    }

    /**
     * render the selection link
     *
     * @param string $parentId
     * @param string $sortOrder
     * @return string
     */
    protected function renderSelector($parentId, $sortOrder)
    {
        return '<li class="umc-menu-selector">'
            . '<a class="insert-menu" href="#"'
            . ' id="'.$parentId.'___'.$sortOrder.'">'.__('Insert here').'</a></li>';
    }

    /**
     * render menu title
     *
     * @param MenuItem $menuItem
     * @return string
     */
    protected function renderAnchorWithoutLink($menuItem)
    {
        return '<span>' . $this->_getAnchorLabel($menuItem) . '</span>';
    }
}
