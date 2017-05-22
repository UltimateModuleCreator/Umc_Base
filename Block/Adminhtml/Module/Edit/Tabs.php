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
namespace Umc\Base\Block\Adminhtml\Module\Edit;

use Magento\Backend\Block\Widget\Tabs as TabsWidget;

/**
 * @api
 */
class Tabs extends TabsWidget
{
    /**
     * set form data
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('id', 'umc_base_module_tabs');
        $this->setDestElementId('edit_form');
        $this->setData('title', __('Module Creator'));
    }
}
