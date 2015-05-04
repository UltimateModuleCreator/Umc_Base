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
namespace Umc\Base\Block\Adminhtml\Module\Grid\Column;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Object;
use Magento\Framework\Url\Decoder;
use Umc\Base\Model\Core\Settings;
use Umc\Base\Model\Downloader;

class Download extends AbstractRenderer implements CollectionDataSourceInterface
{
    /**
     * settings instance
     *
     * @var \Umc\Base\Model\Core\Settings
     */
    protected $settings;

    /**
     * file system reference
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * decoder
     *
     * @var \Magento\Framework\Url\Decoder
     */
    protected $decoder;

    /**
     * @var \Umc\Base\Model\Downloader
     */
    protected $downloader;

    /**
     * constructor
     *
     * @param Settings $settings
     * @param Filesystem $filesystem
     * @param Decoder $decoder
     * @param Downloader $downloader
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Settings $settings,
        Filesystem $filesystem,
        Decoder $decoder,
        Downloader $downloader,
        Context $context,
        array $data = []
    )
    {
        $this->settings  = $settings;
        $this->filesystem = $filesystem;
        $this->decoder = $decoder;
        $this->downloader = $downloader;
        parent::__construct($context, $data);
    }
    /**
     * check if column should be displayed
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return true;
    }

    /**
     * check if column is grouped
     *
     * @return bool
     */
    public function isGrouped()
    {
        return false;
    }

    /**
     * @param Object $row
     * @return string
     */
    public function render(Object $row)
    {
        $download = $this->getColumn()->getData('download');
        $id =  $row->getSafeId();
        $packageName = $this->decoder->decode($id);
        $downloader = $this->getDownloader($download);
        $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $file = $downloader->getRelativePath($packageName);
        $file = $rootDir->getRelativePath(Settings::VAR_DIR_NAME . '/' .$file);
        if ($rootDir->isFile($file) && $rootDir->isReadable($file)) {
            return '<a href="'.
                $this->getUrl('umc/module/download', array('type'=>$download, 'id'=>$id)).'">'.$this->getLabel().
                '</a>';
        }
        return '<span style="color:red;">'.
            __('File does not exist or is not readable').
            '</span>';
    }

    /**
     * get label for column
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getLabel()
    {
        if ($label = $this->getColumn()->getData('label')) {
            return __($label);
        }
        if ($label = $this->getColumn()->getData('header')) {
            return __($label);
        }
        return __('Download');
    }

    /**
     * get downloader
     *
     * @param $type
     * @return Downloader\DownloaderInterface
     */
    protected function getDownloader($type)
    {
        return $this->downloader->getDownloader($type);
    }
}
