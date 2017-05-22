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
namespace Umc\Base\Downloader;

use Magento\Framework\DataObject;

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
     * constructor
     *
     * @param array $downloaderMap
     * @param array $data
     */
    public function __construct(
        array $downloaderMap = [],
        array $data = []
    ) {
        $this->downloaderMap = $downloaderMap;
        parent::__construct($data);
    }

    /**
     * get downloader type
     *
     * @param string $type
     * @return DownloaderInterface
     * @throws \Exception
     */
    public function getDownloader($type)
    {
        if (!isset($this->downloaderMap[$type])) {
            $type = self::DEFAULT_DOWNLOAD;
        }
        $download = $this->downloaderMap[$type];
        if (!$download instanceof DownloaderInterface) {
            throw new \Exception(
                'Downloader with type '. $type.
                ' must implement '.DownloaderInterface::class
            );
        }
        return $download;
    }

    /**
     * @return DownloaderInterface[]
     */
    public function getDownloaders()
    {
        return $this->downloaderMap;
    }
}
