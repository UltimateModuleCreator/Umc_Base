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
namespace Umc\Base\Controller\Adminhtml\Module;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Umc\Base\Model\Core\ModuleFactory;

class Save extends Action
{
    /**
     * page redirect factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageRedirectFactory;

    /**
     * module factory
     *
     * @var \Umc\Base\Model\Core\ModuleFactory
     */
    protected $moduleFactory;

    /**
     * constructor
     *
     * @param RedirectFactory $pageRedirectFactory
     * @param ModuleFactory $moduleFactory
     * @param Context $context
     */
    public function __construct(
        RedirectFactory $pageRedirectFactory,
        ModuleFactory $moduleFactory,
        Context $context
    ) {
        $this->pageRedirectFactory  = $pageRedirectFactory;
        $this->moduleFactory        = $moduleFactory;
        parent::__construct($context);
    }

    /**
     * no save is actually done here
     * This is used just for redirect
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $module = $this->moduleFactory->create();
        try {
            $module->initFromData($this->getRequest()->getPost()->toArray());
            $this->messageManager->addSuccess(__('Your extension has been created!'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $redirectBack = true;
        }
        $pageRedirect = $this->pageRedirectFactory->create();
        if ($redirectBack) {
            $pageRedirect->setPath(
                '*/*/edit',
                [
                    'id'       => strtr(
                        base64_encode($module->getNamespace(). '_'. $module->getModuleName()),
                        '+/=',
                        '-_,'
                    ),
                    '_current' => true
                ]
            );
        } else {
            $pageRedirect->setPath('*/*');
        }
        return $pageRedirect;
    }
}
