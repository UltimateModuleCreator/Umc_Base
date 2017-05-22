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

namespace Umc\Base\Ui\Component\Listing\Columns;

use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Umc\Base\Downloader\Downloader;
use Umc\Base\Writer\Filesystem as UmcFilesystem;

class ModuleActions extends Column
{
    /**
     * @var Downloader
     */
    protected $downloader;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Downloader $downloader
     * @param Filesystem $filesystem
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Downloader $downloader,
        Filesystem $filesystem,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->downloader = $downloader;
        $this->filesystem = $filesystem;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'umc/module/edit',
                        ['id' => $item['safe_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                $rootDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
                foreach ($this->downloader->getDownloaders() as $type => $downloader) {
                    $file = $downloader->getRelativePath($item['filename_id']);
                    $file = $rootDir->getRelativePath(UmcFilesystem::VAR_DIR_NAME . '/' .$file);
                    if ($rootDir->isFile($file) && $rootDir->isReadable($file)) {
                        $url = $this->urlBuilder->getUrl(
                            'umc/module/download',
                            [
                                'id' => $item['safe_id'],
                                'type' => $type
                            ]
                        );
                        $label = $downloader->getLabel();
                    } else {
                        $url = '#';
                        $label = $downloader->getErrorLabel();
                    }
                    $item[$this->getData('name')][$type] = [
                        'href' => $url,
                        'label' => $label,
                        'hidden' => false,
                    ];
                }
            }
        }
        return $dataSource;
    }
}
