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
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Provider\AttributeProvider;

class AttributeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @tests AttributeProvider::getModels()
     */
    public function testGetModels()
    {
        $provider = new AttributeProvider();
        /** @var ModuleInterface|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getMock(ModuleInterface::class, [], [], '', false);
        $childModel = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('getGrandChildModels')->willReturn([$childModel]);
        $this->assertEquals([$childModel], $provider->getModels($model));
    }
}
