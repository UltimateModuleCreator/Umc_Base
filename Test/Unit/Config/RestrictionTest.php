<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config;

use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\DataObject;
use Umc\Base\Config\Restriction;

class RestrictionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Restriction
     */
    protected $restrictionConfig;

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
        $this->reader            = $this->getMock(Filesystem::class, ['read'], [], '', false);
        $this->restrictionConfig = new Restriction($this->reader, 'config', []);

        $this->reader->method('read')->willReturn([]);
    }

    /**
     * cleanup after tests
     */
    protected function tearDown()
    {
        $this->restrictionConfig  = null;
        $this->reader             = null;
        parent::tearDown();
    }

    /**
     * @tests Restriction::getMagicRestrictedValues()
     */
    public function testGetMagicRestrictedValues()
    {
        $class = $this->getMock(DataObject::class, [], [], '', false);
        $restrictions = $this->restrictionConfig->getMagicRestrictedValues($class);
        $this->assertContains('data', $restrictions);
        $this->assertContains('data_by_key', $restrictions);
    }
}
