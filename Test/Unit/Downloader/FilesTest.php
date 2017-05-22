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
namespace Umc\Base\Test\Unit\Downloader;

use Umc\Base\Downloader\Files;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @tests Files::testGetRelativePath()
     * @throws \Exception
     */
    public function testGetRelativePath()
    {
        $files = new Files();
        $this->assertEquals('modules/file/files.log', $files->getRelativePath('file'));
    }
}
