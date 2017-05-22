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

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

class Module extends GridContainer
{
    /**
     * set grid attributes
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup      = 'Umc_Base';
        $this->_controller      = 'adminhtml_module';
        $this->_headerText      = __('Modules');
        $this->_addButtonLabel  = __('Create Module');
        parent::_construct();
    }
}
