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
namespace Umc\Base\Test\Unit\Processor;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Provider\GlobalProvider;

class GlobalProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @tests GlobalProvider::getModels()
     */
    public function testGetModels()
    {
        $provider = new GlobalProvider();
        /** @var ModelInterface|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $this->assertEquals([$model], $provider->getModels($model));
    }
}
