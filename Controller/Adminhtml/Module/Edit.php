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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url\Decoder;
use Magento\Framework\Xml\Parser;
use Umc\Base\Api\Data\AttributeInterface;
use Umc\Base\Api\Data\EntityInterface;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Api\Data\RelationInterface;
use Umc\Base\Controller\Adminhtml\Module;
use Umc\Base\Api\Data\ModuleInterfaceFactory;
use Umc\Base\Writer\Filesystem as UmcFilesystem;

class Edit extends Module
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * xml parser
     *
     * @var Parser
     */
    protected $xmlParser;

    /**
     * module factory
     *
     * @var ModuleInterfaceFactory
     */
    protected $moduleFactory;

    /**
     * url decoder
     *
     * @var \Magento\Framework\Url\Decoder
     */
    protected $decoder;

    /**
     * file system access
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * UMC filesystem
     *
     * @var RedirectFactory
     */
    protected $umcFilesystem;

    /**
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ScopeConfigInterface $scopeConfig
     * @param Parser $xmlParser
     * @param ModuleInterfaceFactory $moduleFactory
     * @param Decoder $decoder
     * @param Filesystem $filesystem
     * @param UmcFilesystem $umcFilesystem
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        ScopeConfigInterface $scopeConfig,
        Parser $xmlParser,
        ModuleInterfaceFactory $moduleFactory,
        Decoder $decoder,
        Filesystem $filesystem,
        UmcFilesystem $umcFilesystem
    ) {
        $this->coreRegistry         = $coreRegistry;
        $this->scopeConfig          = $scopeConfig;
        $this->xmlParser            = $xmlParser;
        $this->moduleFactory        = $moduleFactory;
        $this->decoder              = $decoder;
        $this->filesystem           = $filesystem;
        $this->umcFilesystem        = $umcFilesystem;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * run action
     *
     * @return \Magento\Framework\View\Result\Page | \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getConfig()->getTitle()->set(__('Ultimate Module Creator'));
        $id = $this->getRequest()->getParam('id');
        /** @var \Umc\Base\Api\Data\ModuleInterface $module */
        $module = $this->moduleFactory->create();
        if ($id) {
            try {
                $data = $this->initModuleData($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $pageRedirect = $this->resultRedirectFactory->create();
                $pageRedirect->setPath('umc/*/index');
                return $pageRedirect;
            }
        } else {
            $data = [];
        }
        $module->initFromData($data);
        $this->coreRegistry->register('current_module', $module);
        if ($id) {
            $title = __('Edit Module "%1"', $module->getExtensionName());
        } else {
            $title = __('Create module');
        }
        $pageResult->getConfig()->getTitle()->prepend($title);
        $this->_setActiveMenu('Umc_Base::umc')
            ->_addBreadcrumb(
                __('Ultimate Module Creator'),
                __('Ultimate Module Creator')
            )->_addBreadcrumb(
                $title,
                $title
            );
        return $pageResult;
    }

    /**
     * @param string $id
     * @return array
     */
    protected function initModuleData($id)
    {
        $data = [];
        $moduleName = $this->decoder->decode($id);
        $path = $this->umcFilesystem->getXmlRootPath();
        $moduleName = $this->sanitizeModuleFileName($moduleName);
        $xmlFile = $moduleName . '.xml';
        $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $file = $rootDir->getRelativePath(UmcFilesystem::VAR_DIR_NAME . '/' .$xmlFile);
        if ($rootDir->isFile($file) && $rootDir->isReadable($file)) {
            $this->xmlParser->load($path.'/'.$xmlFile);
            $data = $this->xmlParser->xmlToArray();
            $data = $data[ModuleInterface::ENTITY_CODE];
            if (isset($data['entities'][EntityInterface::ENTITY_CODE][0])) {
                $entities = $data['entities'][EntityInterface::ENTITY_CODE];
            } else {
                $entities = [$data['entities'][EntityInterface::ENTITY_CODE]];
            }
            foreach ($entities as $key => $entity) {
                if (isset($entity['attributes'][AttributeInterface::ENTITY_CODE])) {
                    if (isset($entity['attributes'][AttributeInterface::ENTITY_CODE][0])) {
                        $entities[$key]['attributes'] = $entity['attributes'][AttributeInterface::ENTITY_CODE];
                    } else {
                        $entities[$key]['attributes'] = [$entity['attributes'][AttributeInterface::ENTITY_CODE]];
                    }
                }
            }
            unset($data['entities']);
            $relations = [];
            if (isset($data['relations'])) {
                if (isset($data['relations'][RelationInterface::ENTITY_CODE][0])) {
                    $relations = $data['relations'][RelationInterface::ENTITY_CODE];
                } else {
                    $relations = [$data['relations'][RelationInterface::ENTITY_CODE]];
                }

            }
            unset($data['relations']);
            $data = [
                ModuleInterface::ENTITY_CODE     => $data,
                'entity'                         => $entities,
                'relation'                       => $relations
            ];
        }
        return $data;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function sanitizeModuleFileName($file)
    {
        $parts = explode('/', str_replace('\\', '/', $file));
        return $parts[count($parts) - 1];
    }
}
