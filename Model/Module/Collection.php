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
namespace Umc\Base\Model\Module;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Filesystem as FilesystemCollection;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\Encoder;
use Umc\Base\Writer\Filesystem as UmcFileSystem;

class Collection extends FilesystemCollection
{
    /**
     * @var string
     */
    const PACKAGE_FIELD     = 'package';

    /**
     * @var string
     */
    const FILENAME_ID_FIELD = 'filename_id';

    /**
     * @var string
     */
    const FOLDER_FIELD      = 'folder';

    /**
     * @var string
     */
    const SAFE_ID_FIELD     = 'safe_id';

    /**
     * @var string
     */
    const XML_REGEX         = '/\.(xml)$/';

    /**
     * Base dir where packages are located
     *
     * @var string
     */
    protected $baseDir = '';

    /**
     * File names regex pre-filter
     *
     * @var string
     */
    protected $_allowedFilesMask = '/^[a-z0-9\.\-\_]+\.(xml)$/i';

    /**
     * file system access
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Url\Encoder
     */
    protected $encoder;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $connectDirectory;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param Filesystem $filesystem
     * @param Encoder $encoder
     * @throws \Exception
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        Filesystem $filesystem,
        Encoder $encoder
    ) {
        $this->filesystem       = $filesystem;
        $this->encoder          = $encoder;
        $this->connectDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->connectDirectory->create(UmcFileSystem::VAR_DIR_NAME);
        $this->addTargetDir($this->connectDirectory->getAbsolutePath(UmcFileSystem::VAR_DIR_NAME));
        $this->setCollectRecursively(false);
        parent::__construct($entityFactory);
    }

    /**
     * generate row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        $row[self::PACKAGE_FIELD] = preg_replace(
            self::XML_REGEX,
            '',
            str_replace(
                $this->connectDirectory->getAbsolutePath(UmcFileSystem::VAR_DIR_NAME.'/'),
                '',
                $filename
            )
        );
        $row[self::FILENAME_ID_FIELD] = $row[self::PACKAGE_FIELD];
        $folder = explode('/', $row[self::PACKAGE_FIELD]);
        array_pop($folder);
        $row[self::FOLDER_FIELD] = '/';
        if (!empty($folder)) {
            $row[self::FOLDER_FIELD] = implode('/', $folder) . '/';
        }
        $row[self::SAFE_ID_FIELD] = $this->encoder->encode($row[self::FILENAME_ID_FIELD]);
        return $row;
    }

    /**
     * Get all folders as options array
     *
     * @return array
     */
    public function collectFolders()
    {
        return ['/' => '/'];
    }

    /**
     * @param string $field
     * @param string $direction
     * @return \Magento\Framework\Data\Collection
     */
    public function addOrder($field, $direction)
    {
        return $this->setOrder($field, $direction);
    }
}
