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
namespace Umc\Base\Model\Generator;

use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Module\Dir\Reader as ModuleReader;
use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Core\AbstractModel;
use Umc\Base\Model\Core\Module;
use Umc\Base\Model\Processor\ProcessorInterface;
use Umc\Base\Model\Umc;

class AbstractGenerator extends Umc implements GeneratorInterface
{
    /**
     * prefix for part processors
     *
     * @var string
     */
    const PART_PROCESSOR_KEY = 'part';

    /**
     * prefix for member processors
     *
     * @var string
     */
    const MEMBER_PROCESSOR_KEY = 'member';

    /**
     * prefix for annotation processors
     *
     * @var string
     */
    const ANNOTATION_PROCESSOR_KEY = 'annotation';

    /**
     * prefix for implement processors
     *
     * @var string
     */
    const IMPLEMENT_PROCESSOR_KEY = 'implement';

    /**
     * prefix for construct processors
     *
     * @var string
     */
    const CONSTRUCT_PROCESSOR_KEY = 'construct';

    /**
     * default scope
     *
     * @var string
     */
    protected $defaultScope = 'global';

    /**
     * config to parse
     *
     * @var array
     */
    protected $config;

    /**
     * current module
     *
     * @var Module
     */
    protected $module;

    /**
     * part processors list
     *
     * @var array
     */
    protected $processors;

    /**
     * object manager reference
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * model in scope
     *
     * @var AbstractModel
     */
    protected $scopeModel;

    /**
     * io reference
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $io;

    /**
     * cosntructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param ModuleReader $moduleReader
     * @param IoFile $io
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleReader $moduleReader,
        IoFile $io,
        array $processors = []
    )
    {
        $this->objectManager    = $objectManager;
        $this->moduleReader     = $moduleReader;
        $this->io               = $io;
        $this->processors       = $processors;
    }

    /**
     * set current module
     *
     * @param Module $module
     * @return $this
     */
    public function setModule(Module $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * set the config
     *
     * @param array $config
     * @return $this|mixed
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * get scope model
     *
     * @return AbstractModel
     */
    public function getScopeModel()
    {
        return $this->scopeModel;
    }

    /**
     * get processor
     *
     * @param $type
     * @param $key
     * @return ProcessorInterface
     * @throws \Exception
     */
    protected function getProcessor($key, $type)
    {
        $processorKey = $key.'.'.$type;
        if (!isset($this->processors[$processorKey])) {
            throw new \Exception('Part processor not found for type ' . $processorKey . ' in '. get_class($this));
        }
        $processor = $this->processors[$processorKey];
        if (is_string($processor)) {
            $this->processors[$processorKey] = $this->objectManager->create($processor);
            $processor = $this->processors[$processorKey];
        }
        if (!$processor instanceof ProcessorInterface) {
            throw new \Exception(
                get_class($processor).
                    ' must implement \Umc\Base\Model\ProcessorInterface'
            );
        }
        return $processor;
    }

    /**
     * get processor for parts
     * @param $type
     * @return ProcessorInterface
     * @throws \Exception
     */
    protected function getPartProcessor($type)
    {
        return $this->getProcessor(self::PART_PROCESSOR_KEY, $type);
    }
    /**
     * pre-process file part
     *
     * @param $part
     * @return mixed
     */
    protected function preProcessPart($part)
    {
        if (!isset($part['scope'])) {
            $part['scope'] = $this->config['scope'];
        }
        return $part;
    }

    /**
     * get default scope
     *
     * @return string
     */
    protected function getDefaultScope()
    {
        return $this->defaultScope;
    }

    /**
     * get the source file for the part
     *
     * @param $folder
     * @param $file
     * @return string
     */
    protected function getSourceFile($folder, $file)
    {
        $parts = explode(self::FILE_SEPARATOR, $file);
        if (!isset($parts[1])) {
            array_unshift($parts, self::DEFAULT_MODULE);
        }
        $fileName = $this->moduleReader->getModuleDir(
            'etc',
            $parts[0]
        )
            .'/'.self::SOURCE_FOLDER.'/'.$folder.'/'.$parts[1];
        return $fileName;
    }

    /**
     * @param $file
     * @return bool|string
     * @throws \Exception
     */
    protected function readSourceFile($file)
    {
        if (!is_file($file)) {
            throw new \Exception("Trying to load file '$file' that does not exist.");
        }
        return $this->io->read($file);
    }

    /**
     * generate content
     *
     * @return array
     */
    public function generate()
    {
        $this->scopeModel = $this->module;
        $files = [];
        foreach ($this->getModelsForProcessor() as $model)
        {
            if ($model->validateDepend($this->config)) {
                $content = $this->buildContent($model);
                if ($content) {
                    $destination = $model->filterContent($this->config['destination']);
                    $files[$destination] = $this->postProcess($content);
                }
            }
        }
        return $files;
    }

    /**
     * build content
     *
     * @param AbstractModel $model
     * @return string
     */
    public function buildContent(AbstractModel $model)
    {
        return $this->parseParts($model);
    }

    /**
     * parse file parts from source config
     *
     * @param AbstractModel $model
     * @return string
     */
    public function parseParts(AbstractModel $model)
    {
        $content = '';
        if (isset($this->config['code']['part'])) {
            foreach ($this->config['code']['part'] as $part) {
                $part = $this->preProcessPart($part);
                /** @var ProcessorInterface $processor */
                $processor = $this->getPartProcessor($part['scope']);
                $fileContents = $this->readSourceFile(
                    $this->getSourceFile($this->config['source'], $part['file'])
                );
                $processor->setModel($model);
                $content .= $processor->process($part, $fileContents);
            }
        }
        return $content;
    }

    /**
     * process generated content
     *
     * @param string $content
     * @return string
     */
    public function postProcess($content)
    {
        return $content;
    }

    /**
     * @return AbstractModel[]
     */
    protected function getModelsForProcessor()
    {
        return [
            $this->module
        ];
    }
}
