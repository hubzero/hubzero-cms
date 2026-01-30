<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\Processor;

/**
 * Processor tests
 */
class ProcessorTest extends Basic
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
        foreach (array('ini', 'yaml', 'json', 'php', 'xml') as $type) {
            $result = Processor::instance($type);

            $this->assertInstanceOf(Processor::class, $result);
        }

        $this->expectException(\Hubzero\Error\Exception\InvalidArgumentException::class);

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

        $this->assertIsArray($extensions);
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

        $this->assertIsObject($result);
        $this->assertEquals('bar', $result->foo);
    }
}
