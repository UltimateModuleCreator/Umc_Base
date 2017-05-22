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

use Umc\Base\Downloader\Downloader;
use Umc\Base\Downloader\DownloaderInterface;

class DownloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $map;

    /**
     * @var Downloader
     */
    protected $downloader;

    /**
     * @var DownloaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleDownloader;

    /**
     * @var DownloaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dummyDownloader;

    /**
     * @var string
     */
    protected $wrongDownloader;

    /**
     * set up tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->moduleDownloader = $this->getMock(DownloaderInterface::class, [], [], '', false);
        $this->dummyDownloader  = $this->getMock(DownloaderInterface::class, [], [], '', false);
        $this->wrongDownloader  = 'wrong';
        $this->map = [
            'module' => $this->moduleDownloader,
            'dummy'  => $this->dummyDownloader,
            'wrong'  => $this->wrongDownloader,
        ];
        $this->downloader = new Downloader($this->map);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->moduleDownloader = null;
        $this->dummyDownloader  = null;
        $this->wrongDownloader  = null;
        $this->map              = null;
        $this->downloader       = null;
        parent::tearDown();
    }

    /**
     * @tests Downloader::getDownloader()
     * @throws \Exception
     */
    public function testGetDownloader()
    {
        $this->assertEquals($this->moduleDownloader, $this->downloader->getDownloader('module'));
        $this->assertEquals($this->moduleDownloader, $this->downloader->getDownloader('missing'));
        $this->assertEquals($this->dummyDownloader, $this->downloader->getDownloader('dummy'));
        $this->setExpectedException(\Exception::class);
        $this->downloader->getDownloader('wrong');
    }
}
