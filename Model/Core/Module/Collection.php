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
namespace Umc\Base\Model\Core\Module;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\Filesystem as FilesystemCollection;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\Encoder;
use Umc\Base\Model\Core\Settings;

class Collection extends FilesystemCollection
{

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
    protected $_allowedFilesMask    = '/^[a-z0-9\.\-\_]+\.(xml)$/i';

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
     * constructor
     *
     * @param Filesystem $filesystem
     * @param Encoder $encoder
     */
    public function __construct(
        Filesystem $filesystem,
        Encoder $encoder
    )
    {
        $this->filesystem       = $filesystem;
        $this->encoder          = $encoder;
        $this->connectDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->connectDirectory->create(Settings::VAR_DIR_NAME);
        $this->addTargetDir($this->connectDirectory->getAbsolutePath(Settings::VAR_DIR_NAME));
        $this->setCollectRecursively(false);
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
        $row['package'] = preg_replace(
            '/\.(xml)$/',
            '',
            str_replace(
                $this->connectDirectory->getAbsolutePath(Settings::VAR_DIR_NAME.'/'),
                '',
                $filename
            )
        );
        $row['filename_id'] = $row['package'];
        $folder = explode('/', $row['package']);
        array_pop($folder);
        $row['folder'] = '/';
        if (!empty($folder)) {
            $row['folder'] = implode('/', $folder) . '/';
        }
        $row['safe_id'] = $this->encoder->encode($row['filename_id']);
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
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * //TODO: remove this when Magento fixes the 'like' filter in the parent collection
     */
    public function filterCallbackLike($field, $filterValue, $row)
    {
        $filterValueRegex = str_replace(['%', "'"], ['(.*?)',''], preg_quote($filterValue, '/'));
        return (bool)preg_match("/^{$filterValueRegex}\$/i", $row[$field]);

    }
}
