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
use Umc\Base\Model\Builder;
use Umc\Base\Model\Core\ModuleFactory;
use Umc\Base\Model\UmcFactory;
use Umc\Base\Model\Writer\WriterInterface;
use Magento\Framework\App\RequestInterface;

class Validate extends Action
{
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
     * @var \Umc\Base\Model\Core\ModuleFactory
     */
    protected $moduleFactory;

    /**
     * file writer
     *
     * @var \Umc\Base\Model\Writer\WriterInterface
     */
    protected $writer;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * constructor
     * 
     * @param UmcFactory $umcFactory
     * @param ModuleFactory $moduleFactory
     * @param WriterInterface $writer
     * @param Builder $builder
     * @param Context $context
     */
    public function __construct( 
        UmcFactory $umcFactory,
        ModuleFactory $moduleFactory,
        WriterInterface $writer,
        Builder $builder,
        Context $context
    ) { 
        $this->umcFactory           = $umcFactory;
        $this->moduleFactory        = $moduleFactory;
        $this->writer               = $writer;
        $this->builder              = $builder;
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
        $response->setGlue(self::ERROR_GLUE);
        try {
            /** @var \Zend\Stdlib\Parameters $data */
            $data = $this->getRequest()->getPost();
            $module = $this->moduleFactory->create();
            $module->initFromData($data->toArray());
            $errors = $module->validate();
            if (count($errors) == 0) {
                $xml = $module->toXml([], $module->getEntityCode(), true, true);
                $this->writer->setPath($module->getSettings()->getXmlRootPath());
                $this->writer->write($module->getExtensionName().'.xml', $xml);
                $this->builder->setModule($module)->build();
                $response->setError(false);
                $response->setMessage(__('Done!!! Check you var folder'));
            } else {
                if (isset($errors[''])) {
                    $response->setMessage(implode(self::ERROR_GLUE, $errors['']));
                    unset($errors['']);
                }
                $response->setError(true);
                $response->setAttributes($errors);
            }
        } catch (\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        }
        $this->getResponse()->setBody($response->toJson());
    }
}
