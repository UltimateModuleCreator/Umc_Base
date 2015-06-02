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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url\Decoder;
use Magento\Framework\Xml\Parser;
use Umc\Base\Controller\Adminhtml\Module;
use Umc\Base\Model\Core\Settings;
use Umc\Base\Model\Core\ModuleFactory;

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
     * @var \Umc\Base\Model\Core\ModuleFactory
     */
    protected $moduleFactory;

    /**
     * settings
     *
     * @var \Umc\Base\Model\Core\Settings
     */
    protected $settings;

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
     * page redirect factory
     *
     * @var RedirectFactory
     */
    protected $pageRedirectFactory;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param ScopeConfigInterface $scopeConfig
     * @param Parser $xmlParser
     * @param RedirectFactory $pageRedirectFactory
     * @param ModuleFactory $moduleFactory
     * @param Settings $settings
     * @param Decoder $decoder
     * @param Filesystem $filesystem
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        Registry $coreRegistry,
        ScopeConfigInterface $scopeConfig,
        Parser $xmlParser,
        RedirectFactory $pageRedirectFactory,
        ModuleFactory $moduleFactory,
        Settings $settings,
        Decoder $decoder,
        Filesystem $filesystem,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->coreRegistry         = $coreRegistry;
        $this->scopeConfig          = $scopeConfig;
        $this->xmlParser            = $xmlParser;
        $this->pageRedirectFactory  = $pageRedirectFactory;
        $this->moduleFactory        = $moduleFactory;
        $this->settings             = $settings;
        $this->decoder              = $decoder;
        $this->filesystem           = $filesystem;
        parent::__construct($resultPageFactory, $context);
    }

    /**
     * run action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getConfig()->getTitle()->set(__('Ultimate Module Creator'));
        $id = $this->getRequest()->getParam('id');
        $module = $this->moduleFactory->create();
        if ($id) {
            try {
                $moduleName = $this->decoder->decode($id);
                $path = $this->settings->getXmlRootPath();
                $moduleName = basename($moduleName);
                $xmlFile = $moduleName . '.xml';
                $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
                $file = $rootDir->getRelativePath(Settings::VAR_DIR_NAME . '/' .$xmlFile);
                if ($rootDir->isFile($file) && $rootDir->isReadable($file)) {
                    $this->xmlParser->load($path.'/'.$xmlFile);
                    $data = $this->xmlParser->xmlToArray();
                    $data = $data[$module->getEntityCode()];
                    $entityIdsByCode = [];
                    if (isset($data['entities']['entity'][0])) {
                        $entities = $data['entities']['entity'];
                    } else {
                        $entities = [$data['entities']['entity']];
                    }
                    if (isset($data[$this->settings->getEntityCode()])) {
                        $settings = $data[$this->settings->getEntityCode()];
                        unset($data[$this->settings->getEntityCode()]);
                    } else {
                        $settings = [];
                    }

                    foreach ($entities as $key => $entity) {
                        if (isset($entity['attributes']['attribute'])) {
                            if (isset($entity['attributes']['attribute'][0])) {
                                $entities[$key]['attributes'] = $entity['attributes']['attribute'];
                            } else {
                                $entities[$key]['attributes'] = [$entity['attributes']['attribute']];
                            }
                        }
                        $entityIdsByCode[$entity['name_singular']] = $key;
                    }
                    unset($data['entities']);
                    $relationsByCode = [];
                    if (isset($data['relations'])) {
                        if (isset($data['relations']['relation'][0])) {
                            foreach ($data['relations']['relation'] as $relationArray) {
                                $relationsByCode = array_merge($relationsByCode, $relationArray);
                            }
                        } else {
                            $relationsByCode = $data['relations']['relation'];
                        }

                    }

                    $relations = [];
                    foreach ($relationsByCode as $code => $value) {
                        $parts = explode('_', $code);
                        if (count($parts) != 2) {
                            continue;
                        }
                        if (isset($entityIdsByCode[$parts[0]]) && isset($entityIdsByCode[$parts[1]])) {
                            $entity0 = $entityIdsByCode[$parts[0]];
                            $entity1 = $entityIdsByCode[$parts[1]];
                            $relations[$entity0][$entity1] = $value;
                        }
                    }
                    unset($data['relations']);
                    $data = [
                        $module->getEntityCode()         => $data,
                        'entity'                         => $entities,
                        $this->settings->getEntityCode() => $settings,
                        'relation'                       => $relations
                    ];
                } else {
                    $data = [];
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $pageRedirect = $this->pageRedirectFactory->create();
                $pageRedirect->setPath('umc/*/index');
                return $pageRedirect;
            }
        } else {
            $data = [];
        }
        $module->initFromData($data);
        $this->coreRegistry->register('current_module', $module);
        if ($id) {
            $title = __('Edit Module "%1"', $module->getNamespace().'_'.$module->getModuleName());
        } else {
            $title = __('Create module');
        }
        $pageResult->getConfig()->getTitle()->append($title);
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
}
