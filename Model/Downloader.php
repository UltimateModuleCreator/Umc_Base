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

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Umc\Base\Model\Downloader\DownloaderInterface;

class Downloader extends DataObject
{
    /**
     * default download item
     *
     * @var string
     */
    const DEFAULT_DOWNLOAD = 'module';

    /**
     * map to downloader type classes
     * @var array
     */
    protected $downloaderMap;

    /**
     * object manager reference
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $downloaderMap
     * @param array $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $downloaderMap = [],
        array $data = []
    )
    {
        $this->objectManager = $objectManager;
        $this->downloaderMap = $downloaderMap;
        parent::__construct($data);
    }

    /**
     * get downloader type
     *
     * @param $type
     * @return DownloaderInterface
     * @throws \Exception
     */
    public function getDownloader($type)
    {
        if (!isset($this->downloaderMap[$type])) {
            $type = self::DEFAULT_DOWNLOAD;
        }
        $download = $this->downloaderMap[$type];
        if (is_string($download)) {
            $this->downloaderMap[$type] = $this->objectManager->create($download);
            $download = $this->downloaderMap[$type];
        }
        if (!$download instanceof DownloaderInterface) {
            throw new \Exception(
                get_class($download).
                    ' must implement \Umc\Base\Model\Downloader\DownloadInterface'
            );
        }
        return $download;
    }

    /**
     * @return array
     */
    public function getDownloaderTypes()
    {
        return array_keys($this->downloaderMap);
    }
}
