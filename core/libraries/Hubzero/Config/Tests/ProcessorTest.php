<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Config\Processor;

/**
 * Processor tests
 */
class ProcessorTest extends TestCase
{
    /**
     * Tests all()
     *
     * @return  void
     **/
    public function testAll()
    {
        $instances = Processor::all();

        $this->assertCount(5, $instances);

        foreach ($instances as $instance) {
            $this->assertInstanceOf(Processor::class, $instance);
        }
    }

    /**
     * Tests the instance() method
     *
     * @return  void
     **/
    public function testInstance()
    {
        foreach (['ini', 'yaml', 'json', 'php', 'xml'] as $type) {
            $result = Processor::instance($type);

            $this->assertInstanceOf(Processor::class, $result);
        }

        $this->expectException(\InvalidArgumentException::class);

        $result = Processor::instance('py');
    }

    /**
     * Tests getSupportedExtensions() through a concrete implementation
     *
     * @return  void
     **/
    public function testGetSupportedExtensions()
    {
        $processor = Processor::instance('json');
        $extensions = $processor->getSupportedExtensions();

        $this->assertContains('json', $extensions);
    }

    /**
     * Tests stringToObject() through a concrete implementation
     *
     * @return  void
     **/
    public function testParse()
    {
        $processor = Processor::instance('json');
        $result = $processor->stringToObject('{"foo": "bar"}');

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals('bar', $result->foo);
    }
}
