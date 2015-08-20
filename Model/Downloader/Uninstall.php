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
namespace Umc\Base\Model\Downloader;

use Umc\Base\Model\Core\Settings;

class Uninstall implements DownloaderInterface
{
    /**
     * get relative path to the file
     *
     * @param $file
     * @return string
     */
    public function getRelativePath($file)
    {
        return Settings::MODULES_DIR_NAME . '/' . $file . '/uninstall.sql';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Download Module Uninstall Script');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getErrorLabel()
    {
        return __('Module uninstall script is not available');
    }
}
