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
namespace Umc\Base\Model;

use Magento\Framework\Archive\Tar;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Core\Settings;
use Umc\Base\Model\Generator\GeneratorInterface;
use Umc\Base\Model\Config\Source as SourceConfig;
use Umc\Base\Model\Core\Module;
use Umc\Base\Model\Writer\WriterInterface;

class Builder extends Umc
{
    /**
     * list of files to write
     *
     * @var array
     */
    protected $files = [];

    /**
     * object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * source config
     *
     * @var Config\Source
     */
    protected $sourceConfig;

    /**
     * builder map
     *
     * @var array
     */
    protected $generatorMap;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * archiver
     *
     * @var \Magento\Framework\Archive\Tar
     */
    protected $archive;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $io;

    /**
     * current module
     *
     * @var Module
     */
    protected $module;

    /**
     * constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param SourceConfig $sourceConfig
     * @param WriterInterface $writer
     * @param Tar $archive
     * @param IoFile $io
     * @param array $generatorMap
     * @param array $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        SourceConfig $sourceConfig,
        WriterInterface $writer,
        Tar $archive,
        IoFile $io,
        array $generatorMap = [],
        array $data = []
    )
    {
        $this->objectManager    = $objectManager;
        $this->sourceConfig     = $sourceConfig;
        $this->writer           = $writer;
        $this->archive          = $archive;
        $this->io               = $io;
        $this->generatorMap     = $generatorMap;
        parent::__construct($data);
    }


    /**
     * @param Module $module
     * @return $this
     */
    public function setModule(Module $module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * build the module.
     * YEAH this is it.
     * All other classes in this module only serve the next 30-ish lines
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        if (!$this->module) {
            throw new \Exception("Module not set for builder");
        }
        foreach ($this->sourceConfig->getConfig('file') as $fileConfig){
            $fileConfig = $this->preProcessFileConfig($fileConfig);
            $scope = $fileConfig['scope'];
            $type  = $fileConfig['type'];
            $files = $this->getGenerator($scope.'.'.$type)
                ->setConfig($fileConfig)
                ->setModule($this->module)
                ->generate();
            foreach ($files as $name => $content) {
                $this->files[$name] = $content;
            }
        }
        $basePath = $this->module->getSettings()->getXmlRootPath().'/'.
            $this->module->getNamespace().'/'.
            $this->module->getModuleName().'/';
        $this->writer->setPath($basePath);
        foreach ($this->files as $name => $file) {
            $destinationFile = $name;
            $this->writer->write($destinationFile, $file);
        }
        //wrap it up
        $this->createArchive();
        //clean it up
        $this->cleanup();
        //write the list of files
        $this->writeLog();
        $this->writeUninstall();
        return $this;
    }

    public function writeUninstall()
    {
        $content = $this->module->getUninstallScript();
        $oldPath = $this->writer->getPath();
        $newPath = $this->module->getSettings()->getXmlRootPath().'/'.
            Settings::MODULES_DIR_NAME.'/';
        $destination = $this->module->getNamespace().'_'.$this->module->getModuleName().'/uninstall.sql';
        $this->writer->setPath($newPath);
        $this->writer->write($destination, $content);
        $this->writer->setPath($oldPath);
        return $this;
    }
    public function writeLog()
    {
        $files = array_keys($this->files);
        asort($files);
        $content = implode("\n", $files)."\n";
        $oldPath = $this->writer->getPath();
        $newPath = $this->module->getSettings()->getXmlRootPath().'/'.
            Settings::MODULES_DIR_NAME.'/';
        $destination = $this->module->getNamespace().'_'.$this->module->getModuleName().'/files.log';
        $this->writer->setPath($newPath);
        $this->writer->write($destination, $content);
        $this->writer->setPath($oldPath);
        return $this;
    }
    public function createArchive()
    {
        $basePath = trim($this->getBaseWritePath(true), '/');
        $destination = $this->module->getSettings()->getXmlRootPath().'/'.
            $this->module->getNamespace().'_'.
            $this->module->getModuleName().'.gz';
        $this->archive->pack($basePath,
            $destination,
            false
        );
        return $this;
    }

    /**
     * @param bool $namespaceOnly
     * @return string
     */
    public function getBaseWritePath($namespaceOnly = false)
    {
        return $this->module->getSettings()->getXmlRootPath().'/'.
            $this->module->getNamespace().'/'.
            ((!$namespaceOnly) ? $this->module->getModuleName().'/' : '');
    }

    /**
     * @return $this
     */
    public function cleanup()
    {
        $basePath = trim($this->getBaseWritePath(true), '/');
        $this->io->rmdir($basePath, true);
        return $this;
    }

    /**
     * preprocess config file
     *
     * @param array $config
     * @return mixed
     */
    protected function preProcessFileConfig(array $config)
    {
        return $this->sourceConfig->preProcessFileConfig($config);
    }

    /**
     * get generator based on type
     *
     * @param $type
     * @return GeneratorInterface
     * @throws \Exception
     */
    protected function getGenerator($type)
    {
        if (!isset($this->generatorMap[$type])) {
            throw new \Exception("Generator not found for type ".$type);
        }
        $generator = $this->generatorMap[$type];
        if (is_string($generator)) {
            $this->generatorMap[$type] = $this->objectManager->create($generator);
            $generator = $this->generatorMap[$type];
        }
        if (!$generator instanceof GeneratorInterface) {
            throw new \Exception(
                get_class($generator).
                    ' must implement \Umc\Base\Model\Generator\GeneratorInterface'
            );
        }
        return $generator;
    }
}
