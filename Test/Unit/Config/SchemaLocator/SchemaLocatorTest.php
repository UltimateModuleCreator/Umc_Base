<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Umc\Base\Test\Unit\Config\SchemaLocator;

use Magento\Framework\Module\Dir\Reader;
use Umc\Base\Config\SchemaLocator\SchemaLocator;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader |  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleReader;

    /**
     * set up tests
     */
    protected function setUp()
    {
        $this->moduleReader = $this->getMock(Reader::class, [], [], '', false);
        $this->moduleReader->method('getModuleDir')->willReturn('root');
        parent::setUp();
    }

    /**
     * @test SchemaLocator::getPerFileSchema()
     */
    public function testGetPerFileSchema()
    {
        $schemaLocator = new SchemaLocator($this->moduleReader, 'test');
        $this->assertEquals('root/umc/test.xsd', $schemaLocator->getPerFileSchema());
    }

    /**
     * @test SchemaLocator::getSchema()
     */
    public function testGetSchema()
    {
        $schemaLocator = new SchemaLocator($this->moduleReader, 'test');
        $this->assertEquals('root/umc/test.xsd', $schemaLocator->getSchema());

        $schemaLocator = new SchemaLocator($this->moduleReader, 'test_merged');
        $this->assertEquals('root/umc/test_merged.xsd', $schemaLocator->getSchema());
    }
}
