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
use Umc\Base\Processor\AnnotationProcessor;
use Umc\Base\Provider\Processor\ProviderInterface;

class AnnotationProcessorTest extends \PHPUnit_Framework_TestCase
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
     * @var AnnotationProcessor
     */
    protected $annotationProcessor;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mainModel           = $this->getMock(ModelInterface::class, [], [], '', false);
        $this->modelProvider       = $this->getMock(ProviderInterface::class, [], [], '', false);
        $this->dependencyValidator = $this->getMock(Dependency::class, [], [], '', false);
        $this->annotationProcessor = new AnnotationProcessor($this->dependencyValidator, $this->modelProvider);

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
        $this->annotationProcessor = null;
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
     * @tests AnnotationProcessor::process()
     */
    public function testProcess()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(true);
        $method = [
            'id' => 'getSomething',
            'params' => 'array $param1, $param2',
            'return' => '\Some\Class'

        ];
        $expected = ['getSomething' => ' * @method \Some\Class getSomething(array $param1, $param2)'];
        $this->assertEquals($expected, $this->annotationProcessor->process($this->mainModel, $method, ''));
    }

    /**
     * @tests AnnotationProcessor::process()
     */
    public function testProcessNotValid()
    {
        $this->dependencyValidator->method('validateDepend')->willReturn(false);
        $method = [
            'id' => 'getSomething',
            'params' => 'array $param1, $param2',
            'return' => '\Some\Class'

        ];
        $this->assertEquals([], $this->annotationProcessor->process($this->mainModel, $method, ''));
    }
}
