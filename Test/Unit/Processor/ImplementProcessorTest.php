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
use Umc\Base\Config\ClassConfig;
use Umc\Base\Generator\Validator\Dependency;
use Umc\Base\Processor\ImplementProcessor;
use Umc\Base\Provider\Processor\ProviderInterface;

class ImplementProcessorTest extends \PHPUnit_Framework_TestCase
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
     * @var ImplementProcessor
     */
    protected $implementProcessor;

    /**
     * @var ClassConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $classConfig;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mainModel           = $this->getMock(ModelInterface::class, [], [], '', false);
        $this->modelProvider       = $this->getMock(ProviderInterface::class, [], [], '', false);
        $this->dependencyValidator = $this->getMock(Dependency::class, [], [], '', false);
        $this->classConfig         = $this->getMock(ClassConfig::class, [], [], '', false);
        $this->implementProcessor  = new ImplementProcessor(
            $this->dependencyValidator,
            $this->modelProvider,
            $this->classConfig
        );

        $this->classConfig->method('getClassData')->willReturn([
            'id' => 'Some\ClassName',
            'class' => 'Some\ClassName',
            'alias' => 'ClassName'
        ]);

        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('filterContent')->will($this->returnCallback([$this, 'filterContentMock']));
        $this->modelProvider->method('getModels')->willReturn([$model]);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->mainModel           = null;
        $this->modelProvider       = null;
        $this->dependencyValidator = null;
        $this->classConfig         = null;
        $this->implementProcessor  = null;
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
     * @tests ImplementProcessor::process()
     */
    public function testProcess()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(true);
        $expected = [
            'Some\ClassName' => [
                'id' => 'Some\ClassName',
                'class' => 'Some\ClassName',
                'alias' => 'ClassName'
            ]
        ];
        $this->assertEquals($expected, $this->implementProcessor->process($this->mainModel, ['id' => 'id'], ''));
    }

    /**
     * @tests ImplementProcessor::process()
     */
    public function testProcessNotValid()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(false);
        $this->assertEquals([], $this->implementProcessor->process($this->mainModel, ['id' => 'id'], ''));
    }
}
