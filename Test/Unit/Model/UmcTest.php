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
namespace Umc\Base\Test\Unit\Model;

use PHPUnit_Framework_TestCase;
use Umc\Base\Model\Umc;

class UmcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Umc
     */
    protected $umc;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->umc = new Umc();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->umc = null;
        parent::tearDown();
    }

    /**
     * @tests Umc::setPadding()
     */
    public function testSetPadding()
    {
        $this->umc->setPadding("    ");
        $this->assertEquals('    ', $this->umc->getPadding());
    }

    /**
     * @tests Umc::getPadding()
     */
    public function testGetPadding()
    {
        $this->umc->setPadding("    ");
        $this->assertEquals('    ', $this->umc->getPadding());
        $this->assertEquals('        ', $this->umc->getPadding(2));
    }

    /**
     * @tests Umc::setEol()
     */
    public function testSetEol()
    {
        $this->umc->setEol("\n\r");
        $this->assertEquals("\n\r", $this->umc->getEol());
    }

    /**
     * @tests Umc::getEol()
     */
    public function testGetEol()
    {
        $this->umc->setEol("\n");
        $this->assertEquals("\n", $this->umc->getEol());
    }

    /**
     * @tests Umc::setDataByPath()
     */
    public function testSetDataByPath()
    {
        $data = [
            'key1' => [
                'key2' => [
                    'value' => 'val'
                ]
            ]
        ];
        $this->umc->setDataByPath('key1/key2/value', "val");
        $this->assertEquals($data, $this->umc->getData());

        $data = $data = [
            'key1' => [
                'key2' => 5
            ]
        ];
        $this->umc->setDataByPath('key1', ['key2' => 5]);
        $this->assertEquals($data, $this->umc->getData());

        $data = $data = [
            'key1' => [
                'key2' => 5,
                'key3' => 'three'
            ]
        ];

        $this->umc->setDataByPath('key1/key3', 'three');
        $this->assertEquals($data, $this->umc->getData());
    }
}
