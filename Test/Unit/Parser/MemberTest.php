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
namespace Umc\Base\Test\Unit\Parser;

use Magento\Framework\Config\Reader\Filesystem;
use Umc\Base\Api\Data\ModelInterface;
use Umc\Base\Config\ClassConfig;
use Umc\Base\Parser\Member;

class MemberTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        /** @var Filesystem| \PHPUnit_Framework_MockObject_MockObject $reader */
        $reader = $this->getMock(Filesystem::class, [], [], '', false);
        $reader->method('read')->willReturn([
            'root' => []
        ]);
        $classConfig = new ClassConfig($reader, 'root');
        $memberParser = new Member($classConfig);
        /** @var ModelInterface|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getMock(ModelInterface::class, [], [], '', false);
        $model->method('filterContent')->will($this->returnCallback([$this, 'filterContentMock']));
        $member = [
            'constant' => true,
            'type' => 'public',
            'id' => 'member',
            'construct' => 1,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation',
            'default' => 'default',
            'core' => 1
        ];
        $expected = [
            'constant' => true,
            'type' => 'const',
            'id' => 'member_processed',
            'construct' => true,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation_processed',
            'default' => 'default_processed',
            'core' => true
        ];
        $this->assertEquals($expected, $memberParser->parse($model, $member));

        $member = [
            'constant' => false,
            'type' => 'public',
            'id' => 'member',
            'construct' => 1,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation',
            'default' => 'default',
            'core' => 1
        ];
        $expected = [
            'constant' => false,
            'type' => 'public',
            'id' => 'member_processed',
            'construct' => true,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation_processed',
            'default' => 'default_processed',
            'core' => true
        ];
        $this->assertEquals($expected, $memberParser->parse($model, $member));

        $member = [
            'constant' => false,
            'id' => 'member',
            'construct' => 1,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation',
            'default' => 'default',
            'core' => 1
        ];
        $expected = [
            'constant' => false,
            'type' => 'protected',
            'id' => 'member_processed',
            'construct' => true,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation_processed',
            'default' => 'default_processed',
            'core' => true
        ];
        $this->assertEquals($expected, $memberParser->parse($model, $member));

        $member = [
            'constant' => false,
            'id' => 'member',
            'construct' => 1,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation',
            'default' => 'default',
            'core' => 1,
            'var' => 'Some\Class'
        ];
        $expected = [
            'constant' => false,
            'type' => 'protected',
            'id' => 'member_processed',
            'construct' => true,
            'parent' => true,
            'show' => true,
            'skip' => false,
            'doc' => 'documentation_processed',
            'default' => 'default_processed',
            'core' => true,
            'var' => [
                'class' => 'Some\Class_processed',
                'alias' => '_processed'
            ]
        ];
        $this->assertEquals($expected, $memberParser->parse($model, $member));
    }

    /**
     * get the first parameter of the method
     * @return string
     */
    public function filterContentMock()
    {
        $args = func_get_args();
        $value = (isset($args[0])) ? $args[0] : '';
        $value .= '_processed';
        return $value;
    }
}
