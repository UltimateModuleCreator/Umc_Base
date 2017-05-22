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
namespace Umc\Base\Test\Unit\Validator;

use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Validator\ValidatorInterface;
use Umc\Base\Validator\ValidatorPool;

class ValidatorPoolTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $validatorOne = $this->getMock(ValidatorInterface::class, [], [], '', false);
        $validatorTwo = $this->getMock(ValidatorInterface::class, [], [], '', false);
        $validatorThree = $this->getMock(ValidatorInterface::class, [], [], '', false);
        $validatorOne->method('validate')->willReturn([
            'error 1',
            'error 2'
        ]);
        $validatorTwo->method('validate')->willReturn([]);
        $validatorThree->method('validate')->willReturn([
            'error 3',
        ]);
        $validatorPool = new ValidatorPool([
            $validatorOne,
            $validatorTwo,
            $validatorThree
        ]);
        /** @var \PHPUnit_Framework_MockObject_MockObject|ModelInterface $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $expected = [
            'error 1',
            'error 2',
            'error 3',
        ];
        $this->assertEquals($expected, $validatorPool->validate($model));

    }
}
