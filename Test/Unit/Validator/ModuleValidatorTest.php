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
use Umc\Base\Api\Data\ModuleInterface;
use Umc\Base\Validator\ModuleValidator;
use Umc\Base\Validator\ValidatorPool;

class ModuleValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @tests ModuleValidator::validate()
     */
    public function testValidate()
    {
        $moduleValidatorPool = $this->getMock(ValidatorPool::class, [], [], '', false);
        $moduleValidatorPool->method('validate')->willReturn(['module error']);

        $entityValidatorPool = $this->getMock(ValidatorPool::class, [], [], '', false);
        $entityValidatorPool->method('validate')->willReturn(['entity error']);

        $attributeValidatorPool = $this->getMock(ValidatorPool::class, [], [], '', false);
        $attributeValidatorPool->method('validate')->willReturn(['attribute error']);

        /** @var ModuleInterface|\PHPUnit_Framework_MockObject_MockObject $module */
        $module = $this->getMock(ModuleInterface::class, [], [], '', false);
        $entity = $this->getMockForAbstractClass(ModelInterface::class, [], '', true, true, true, ['getAttributes']);
        $attribute = $this->getMockForAbstractClass(ModelInterface::class);

        $entity->method('getAttributes')->willReturn([$attribute, $attribute]);

        $module->method('getEntities')->willReturn([$entity]);

        $moduleValidator = new ModuleValidator([
            'umc_module' => $moduleValidatorPool,
            'umc_entity' => $entityValidatorPool,
            'umc_attribute' => $attributeValidatorPool,
        ]);

        $expected = [
            'module error',
            'entity error',
            'attribute error',
            'attribute error'
        ];
        $this->assertEquals($expected, $moduleValidator->validate($module));
    }
}
