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
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Parser\Member;
use Umc\Base\Processor\MemberProcessor;
use Umc\Base\Provider\Processor\ProviderInterface;

class MemberProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mainModel;

    /**
     * @var ProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelProvider;

    /**
     * @var Dependency|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependencyValidator;

    /**
     * @var MemberProcessor
     */
    protected $memberProcessor;

    /**
     * @var Member|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $memberParser;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mainModel           = $this->getMock(ModelInterface::class, [], [], '', false);
        $this->modelProvider       = $this->getMock(ProviderInterface::class, [], [], '', false);
        $this->dependencyValidator = $this->getMock(Dependency::class, [], [], '', false);
        $this->memberParser        = $this->getMock(Member::class, [], [], '', false);
        $this->memberProcessor     = new MemberProcessor(
            $this->dependencyValidator,
            $this->modelProvider,
            $this->memberParser
        );

        $this->memberParser->method('parse')->will($this->onConsecutiveCalls(
            [
                'id' => 'member1',
                'name' => 'Name 1',
            ],
            [
                'id' => 'member2',
                'name' => 'Name 2',
            ]
        ));

        $model1 = $this->getMock(ModelInterface::class, [], [], '', false);
        $model1->method('filterContent')->will($this->returnCallback([$this, 'filterContentMock']));
        $model2 = $this->getMock(ModelInterface::class, [], [], '', false);
        $model2->method('filterContent')->will($this->returnCallback([$this, 'filterContentMock']));
        $this->modelProvider->method('getModels')->willReturn([$model1, $model2]);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->mainModel           = null;
        $this->modelProvider       = null;
        $this->dependencyValidator = null;
        $this->memberProcessor     = null;
        $this->memberParser        = null;
    }

    /**
     * get the first parameter of the method
     * @return string
     */
    public function filterContentMock()
    {
        $args = func_get_args();
        return (isset($args[0])) ? $args[0] : '';
    }

    /**
     * @tests MemberProcessor::process()
     */
    public function testProcess()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(true);
        $expected = [
            'member1' => [
                'id' => 'member1',
                'name' => 'Name 1',
            ],
            'member2' => [
                'id' => 'member2',
                'name' => 'Name 2',
            ]
        ];
        $this->assertEquals($expected, $this->memberProcessor->process($this->mainModel, ['id' => 'id'], ''));
    }

    /**
     * @tests MemberProcessor::process()
     */
    public function testProcessNotValid()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(false);
        $this->assertFalse($this->memberProcessor->process($this->mainModel, ['dummy'], ''));
    }
}
