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
use Umc\Base\Model\Builder;
use Umc\Base\Api\Data\ModuleInterfaceFactory;
use Umc\Base\Model\UmcFactory;
use Umc\Base\Validator\ModuleValidator;
use Umc\Base\Writer\Filesystem;
use Umc\Base\Writer\WriterInterface;

class Validate extends Action
{
    /**
     * @var string
     */
    const ERROR_GLUE  = '####';

    /**
     * model factory
     *
     * @var \Umc\Base\Model\UmcFactory
     */
    protected $umcFactory;

    /**
     * module factory
     *
     * @var ModuleInterfaceFactory
     */
    protected $moduleFactory;

    /**
     * file writer
     *
     * @var \Umc\Base\Writer\WriterInterface
     */
    protected $writer;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ModuleValidator
     */
    protected $moduleValidator;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UmcFactory $umcFactory
     * @param ModuleInterfaceFactory $moduleFactory
     * @param WriterInterface $writer
     * @param Builder $builder
     * @param ModuleValidator $moduleValidator
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UmcFactory $umcFactory,
        ModuleInterfaceFactory $moduleFactory,
        WriterInterface $writer,
        Builder $builder,
        ModuleValidator $moduleValidator
    ) {
        $this->filesystem           = $filesystem;
        $this->umcFactory           = $umcFactory;
        $this->moduleFactory        = $moduleFactory;
        $this->writer               = $writer;
        $this->builder              = $builder;
        $this->moduleValidator      = $moduleValidator;
        parent::__construct($context);
    }

    /**
     * validate and save module
     *
     * @return void
     */
    public function execute()
    {
        $response = $this->umcFactory->create();
        $response->setData('glue', self::ERROR_GLUE);
        try {
            /** @var \Zend\Stdlib\Parameters $data */
            $data = $this->getRequest()->getPost();
            /** @var \Umc\Base\Api\Data\ModuleInterface $module */
            $module = $this->moduleFactory->create();
            $module->initFromData($data->toArray());
            $errors = $this->moduleValidator->validate($module);
            if (count($errors) == 0) {
                $xml = $module->toXml([], $module->getEntityCode(), true, true);
                $this->writer->setPath($this->filesystem->getXmlRootPath());
                $this->writer->write($module->getExtensionName().'.xml', $xml);
                $this->builder->build($module);
                $response->setData('error', false);
                $response->setData('message', __('Done!!! Check you var folder'));
            } else {
                if (isset($errors[''])) {
                    $response->setData('message', implode(self::ERROR_GLUE, $errors['']));
                    unset($errors['']);
                }
                $response->setData('error', true);
                $response->setData('attributes', $errors);
            }
        } catch (\Exception $e) {
            $response->setData('error', true);
            $response->setData('message', $e->getMessage());
        }
        $this->getResponse()->setBody($response->toJson());
    }
}
