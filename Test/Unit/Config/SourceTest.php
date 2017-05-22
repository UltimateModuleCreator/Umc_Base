<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config;

use Magento\Framework\Config\Reader\Filesystem;
use Umc\Base\Config\Source;

class SourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Source
     */
    protected $sourceConfig;

    /**
     * @var Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->reader       = $this->getMock(Filesystem::class, ['read'], [], '', false);
        $this->sourceConfig = new Source($this->reader, 'config', []);

        $this->reader->method('read')->willReturn([]);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->sourceConfig  = null;
        $this->reader        = null;
        parent::tearDown();
    }

    /**
     * @tests Source::preProcessFileConfig()
     */
    public function testPreProcessFileConfig()
    {
        $config = [
            'id' => '{{some_id}}'
        ];
        $expected = [
            'id' => '{{some_id}}',
            'destination' => '{{some_id}}',
            'source' => 'some_id',
            'scope' => 'umc_module',
            'abstract' => false,
            'interface' => false,
            'api' => false,
        ];
        $this->assertEquals($expected, $this->sourceConfig->preProcessFileConfig($config));
    }
}
