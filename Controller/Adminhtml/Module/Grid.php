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
use Magento\Framework\View\Result\LayoutFactory;

class Grid extends Action
{
    /**
     * layout factory reference
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultLayoutFactory;

    /**
     * constructor
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Context $context
    )
    {
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }

    /**
     * execute the action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultLayoutFactory->create();
    }
}
