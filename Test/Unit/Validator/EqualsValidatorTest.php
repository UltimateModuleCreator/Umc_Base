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
use Umc\Base\Validator\EqualsValidator;

class EqualsValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mainModel;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mainModel = $this->getMock(ModelInterface::class, [], [], '', false);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->mainModel = null;
        parent::tearDown();
    }

    /**
     * @tests RequireChildValidator::validate()
     */
    public function testValidateOk()
    {
        $this->mainModel->method('getDataUsingMethod')->willReturnMap(
            [
                ['field_one', 1],
                ['field_one', 2],
            ]
        );
        $validator = new EqualsValidator(
            'field_one',
            'field_two',
            'dummy error message'
        );
        $this->assertEquals([], $validator->validate($this->mainModel));
    }

    /**
     * @tests RequireChildValidator::validate()
     */
    public function testValidateNotOk()
    {
        $errorMessage = 'field_one and field_two should not be the same';
        $this->mainModel->method('getDataUsingMethod')->willReturn(1);
        $this->mainModel->method('getValidationErrorKey')->willReturn('field');
        $validator = new EqualsValidator(
            'field_one',
            'field_two',
            '%1 and %2 should not be the same'
        );
        $this->assertEquals(['field' => [$errorMessage]], $validator->validate($this->mainModel));
    }
}
