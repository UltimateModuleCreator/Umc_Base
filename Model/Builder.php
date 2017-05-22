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
namespace Umc\Base\Model;

use Magento\Framework\Archive\Tar;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Generator\GeneratorInterface;
use Umc\Base\Config\Source as SourceConfig;
use Umc\Base\Writer\Filesystem;
use Umc\Base\Writer\WriterInterface;

class Builder extends Umc
{
    /**
     * list of files to write
     *
     * @var array
     */
    protected $files = [];

    /**
     * source config
     *
     * @var SourceConfig
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param SourceConfig $sourceConfig
     * @param WriterInterface $writer
     * @param Tar $archive
     * @param IoFile $io
     * @param Filesystem $filesystem
     * @param array $generatorMap
     * @param array $data
     */
    public function __construct(
        SourceConfig $sourceConfig,
        WriterInterface $writer,
        Tar $archive,
        IoFile $io,
        Filesystem $filesystem,
        array $generatorMap = [],
        array $data = []
    ) {
        $this->sourceConfig     = $sourceConfig;
        $this->writer           = $writer;
        $this->archive          = $archive;
        $this->io               = $io;
        $this->generatorMap     = $generatorMap;
        $this->filesystem       = $filesystem;
        parent::__construct($data);
    }

    /**
     * build the module.
     * YEAH this is it.
     * All other classes in this module only serve the next 30-ish lines
     *
     * @param ModuleInterface $module
     * @return $this
     * @throws \Exception
     */
    public function build(ModuleInterface $module)
    {
        foreach ($this->sourceConfig->getConfig('file') as $fileConfig) {
            $fileConfig = $this->preProcessFileConfig($fileConfig);
            $scope = $fileConfig['scope'];
            $type  = $fileConfig['type'];
            $generator = $this->getGenerator($scope.'.'.$type);
            $files = $generator
                ->setConfig($fileConfig)
                ->generate($module);
            foreach ($files as $name => $content) {
                $this->files[$name] = $content;
            }
        }
        $basePath = $this->filesystem->getXmlRootPath().'/'.
            $module->getNamespace().'/'.
            $module->getModuleName().'/';
        $this->writer->setPath($basePath);
        foreach ($this->files as $name => $file) {
            $destinationFile = $name;
            $this->writer->write($destinationFile, $file);
        }
        //wrap it up
        $this->createArchive($module);
        //clean it up
        $this->cleanup($module);
        //write the list of files
        $this->writeLog($module);
        return $this;
    }

    /**
     * write generated files log
     *
     * @param ModuleInterface $module
     * @return $this
     */
    protected function writeLog(ModuleInterface $module)
    {
        $files = array_keys($this->files);
        asort($files);
        $content = implode("\n", $files)."\n";
        $oldPath = $this->writer->getPath();
        $newPath = $this->filesystem->getXmlRootPath().'/'.Filesystem::MODULES_DIR_NAME.'/';
        $destination = $module->getNamespace().'_'.$module->getModuleName().'/files.log';
        $this->writer->setPath($newPath);
        $this->writer->write($destination, $content);
        $this->writer->setPath($oldPath);
        return $this;
    }

    /**
     * create the archive
     *
     * @param ModuleInterface $module
     * @return $this
     */
    protected function createArchive(ModuleInterface $module)
    {
        $basePath = rtrim($this->getBaseWritePath($module), '/');
        $destination = $this->filesystem->getXmlRootPath().'/'.
            $module->getExtensionName().'.tar.gz';
        $this->archive->pack(
            $basePath,
            $destination,
            true
        );
        return $this;
    }

    /**
     * @param ModuleInterface $module
     * @param bool $namespaceOnly
     * @return string
     */
    protected function getBaseWritePath(ModuleInterface $module, $namespaceOnly = false)
    {
        return $this->filesystem->getXmlRootPath().'/'.
            $module->getNamespace().'/'.
            ((!$namespaceOnly) ? $module->getModuleName().'/' : '');
    }

    /**
     * @param ModuleInterface $module
     * @return $this
     */
    protected function cleanup(ModuleInterface $module)
    {
        $basePath = rtrim($this->getBaseWritePath($module, true), '/');
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
     * @param string $type
     * @return GeneratorInterface
     * @throws \Exception
     */
    protected function getGenerator($type)
    {
        if (!isset($this->generatorMap[$type])) {
            throw new \Exception("Generator not found for type ".$type);
        }
        $generator = $this->generatorMap[$type];
        if (!$generator instanceof GeneratorInterface) {
            throw new \Exception(
                'Generator for type ' . $type.
                ' must implement '.GeneratorInterface::class
            );
        }
        return $generator;
    }
}
