<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config\Mapper;

use Umc\Base\Config\Mapper\Sorting;
use Umc\Base\Model\Umc;
use Umc\Base\Model\UmcFactory;

class SortingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UmcFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $umcFactory;

    /**
     * @var Sorting
     */
    protected $sorting;

    /**
     * setup tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->umcFactory = $this->getMock(UmcFactory::class, [], [], '', false);
        $umc = new Umc();
        $this->umcFactory->method('create')->willReturn($umc);
        $this->sorting    = new Sorting($this->umcFactory, ['data' => ['field1']]);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->umcFactory = null;
        $this->sorting    = null;
        parent::tearDown();
    }

    /**
     * @tests Sorting::map()
     */
    public function testMap()
    {
        $array = [
            'data' => [
                [
                    'field1' => [
                        0 => ['sort' => 20],
                        1 => ['sort' => 30],
                        2 => ['sort' => 10],
                        3 => []
                    ],
                ],
            ]
        ];
        $expected =  [
            'data' => [
                [
                    'field1' => [
                        3 => [],
                        2 => ['sort' => 10],
                        0 => ['sort' => 20],
                        1 => ['sort' => 30],
                    ],
                ],
            ]
        ];
        $this->assertEquals($expected, $this->sorting->map($array));
    }
}
