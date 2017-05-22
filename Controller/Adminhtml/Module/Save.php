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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Umc\Base\Api\Data\ModuleInterfaceFactory;

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
     * @var \Umc\Base\Api\Data\ModuleInterfaceFactory
     */
    protected $moduleFactory;

    /**
     * constructor
     *
     * @param ModuleInterfaceFactory $moduleFactory
     * @param Context $context
     */
    public function __construct(
        ModuleInterfaceFactory $moduleFactory,
        Context $context
    ) {
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
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        $module = $this->moduleFactory->create();
        try {
            $module->initFromData($this->getRequest()->getPost()->toArray());
            $this->messageManager->addSuccessMessage(__('Your extension has been created!'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirectBack = true;
        }
        $pageRedirect = $this->resultRedirectFactory->create();
        if ($redirectBack) {
            $pageRedirect->setPath(
                '*/*/edit',
                [
                    'id' => strtr(
                        base64_encode($module->getExtensionName()),
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
