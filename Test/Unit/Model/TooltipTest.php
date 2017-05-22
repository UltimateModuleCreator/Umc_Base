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
use Umc\Base\Model\Tooltip;
use \Magento\Framework\Escaper;

class TooltipTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Tooltip
     */
    protected $tooltip;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->escaper = new Escaper();
        $this->tooltip = new Tooltip($this->escaper);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->escaper = null;
        $this->tooltip = null;
        parent::tearDown();
    }

    /**
     * @tests Tooltip::getTitle()
     */
    public function testGetTitle()
    {
        $title = "This is a <strong>title</strong>";
        $this->tooltip->setTitle($title);
        $this->assertEquals($title, $this->tooltip->getTitle(false));
        $this->assertEquals("This is a &lt;strong&gt;title&lt;/strong&gt;", $this->tooltip->getTitle());
    }
}
