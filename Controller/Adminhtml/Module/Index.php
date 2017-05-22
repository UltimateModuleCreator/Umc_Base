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
namespace Umc\Base\Controller\Adminhtml\Module;

use Umc\Base\Controller\Adminhtml\Module;

class Index extends Module
{
    /**
     * run index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getConfig()->getTitle()->set(__('Ultimate Module Creator'));
        $pageResult->getConfig()->getTitle()->prepend(__('Module List'));
        $this->_setActiveMenu('Umc_Base::umc')
            ->_addBreadcrumb(
                __('Ultimate Module Creator'),
                __('Ultimate Module Creator')
            )->_addBreadcrumb(
                __('Module List'),
                __('Module List')
            );
        return $pageResult;
    }
}
