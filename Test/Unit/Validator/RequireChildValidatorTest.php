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
use Umc\Base\Validator\RequireChildValidator;

class RequireChildValidatorTest extends \PHPUnit_Framework_TestCase
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
        $childModel = $this->getMock(ModelInterface::class, [], [], '', false);
        $this->mainModel->method('getChildModels')->willReturn([$childModel]);
        $validator = new RequireChildValidator('dummy error message');
        $this->assertEquals([], $validator->validate($this->mainModel));
    }

    /**
     * @tests RequireChildValidator::validate()
     */
    public function testValidateNotOk()
    {
        $errorMessage = 'dummy error message';
        $this->mainModel->method('getChildModels')->willReturn([]);
        $validator = new RequireChildValidator($errorMessage);
        $this->assertEquals(['' => [$errorMessage]], $validator->validate($this->mainModel));
    }
}
