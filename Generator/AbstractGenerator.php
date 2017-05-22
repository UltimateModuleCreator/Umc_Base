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
namespace Umc\Base\Generator;

use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Module\Dir\Reader as ModuleReader;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\Source;
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Model\AbstractModel;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Processor\ProcessorInterface;
use Umc\Base\Provider\ProviderInterface;
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
    protected $defaultScope;

    /**
     * config to parse
     *
     * @var array
     */
    protected $config;

    /**
     * current module
     *
     * @var ModuleInterface
     */
    protected $module;

    /**
     * part processors list
     *
     * @var array
     */
    protected $processors;

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
     * @var ProviderInterface
     */
    protected $modelProvider;

    /**
     * @var Dependency
     */
    protected $dependencyValidator;

    /**
     * @var ModuleReader
     */
    protected $moduleReader;

    /**
     * @param ModuleReader $moduleReader
     * @param IoFile $io
     * @param ProviderInterface $modelProvider
     * @param Dependency $dependencyValidator
     * @param string $defaultScope
     * @param array $processors
     * @param array $data
     */
    public function __construct(
        ModuleReader $moduleReader,
        IoFile $io,
        ProviderInterface $modelProvider,
        Dependency $dependencyValidator,
        $defaultScope = Source::MODULE_SCOPE,
        array $processors = [],
        array $data = []
    ) {
        $this->moduleReader        = $moduleReader;
        $this->io                  = $io;
        $this->processors          = $processors;
        $this->defaultScope        = $defaultScope;
        $this->modelProvider       = $modelProvider;
        $this->dependencyValidator = $dependencyValidator;
        parent::__construct($data);
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
    protected function getScopeModel()
    {
        return $this->scopeModel;
    }

    /**
     * get processor
     *
     * @param string $type
     * @param string $key
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
        if (!$processor instanceof ProcessorInterface) {
            throw new \Exception(
                get_class($processor).
                ' must implement '.ProcessorInterface::class
            );
        }
        return $processor;
    }

    /**
     * get processor for parts
     * @param string $type
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
     * @param array $part
     * @return array
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
     * @param string $folder
     * @param string $file
     * @return string
     */
    protected function getSourceFile($folder, $file)
    {
        $parts = explode(self::FILE_SEPARATOR, $file);
        if (!isset($parts[1])) {
            array_unshift($parts, self::DEFAULT_MODULE);
        }
        $fileName = $this->moduleReader->getModuleDir('etc', $parts[0]).
            '/'.self::SOURCE_FOLDER.'/'.$folder.'/'.$parts[1];
        return $fileName;
    }

    /**
     * @param string $file
     * @return bool|string
     * @throws \Exception
     */
    protected function readSourceFile($file)
    {
        if (!$this->io->fileExists($file, true)) {
            throw new \Exception("Trying to load file '$file' that does not exist.");
        }
        return $this->io->read($file);
    }

    /**
     * @param ModuleInterface $module
     * @return array
     */
    public function generate(ModuleInterface $module)
    {
        $this->module = $module;
        $this->scopeModel = $module;
        $files = [];
        foreach ($this->getModelsForProcessor($module) as $model) {
            if ($this->dependencyValidator->validateDepend($model, $this->config)) {
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
     * @param ModelInterface $model
     * @return string
     */
    public function buildContent(ModelInterface $model)
    {
        return $this->parseParts($model);
    }

    /**
     * @param ModelInterface $model
     * @return string
     * @throws \Exception
     */
    public function parseParts(ModelInterface $model)
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
                $content .= $processor->process($model, $part, $fileContents);
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
    protected function postProcess($content)
    {
        return $content;
    }

    /**
     * @param ModelInterface $model
     * @return \Umc\Base\Api\Data\ModelInterface[]
     */
    protected function getModelsForProcessor(ModelInterface $model)
    {
        return $this->modelProvider->getModels($model);
    }
}
