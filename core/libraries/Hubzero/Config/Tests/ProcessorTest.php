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
	 * @covers  \Hubzero\Config\Processor::all
	 * @return  void
	 **/
	public function testAll()
	{
		$instances = Processor::all();

		$this->assertCount(5, $instances);

		foreach ($instances as $instance)
		{
			$this->assertInstanceOf(Processor::class, $instance);
		}
	}

	/**
	 * Tests the instance() method
	 *
	 * @covers  \Hubzero\Config\Processor::instance
	 * @return  void
	 **/
	public function testInstance()
	{
		foreach (array('ini', 'yaml', 'json', 'php', 'xml') as $type)
		{
			$result = Processor::instance($type);

			$this->assertInstanceOf(Processor::class, $result);
		}

		$this->setExpectedException('Hubzero\\Error\\Exception\\InvalidArgumentException');

		$result = Processor::instance('py');
	}

	/**
	 * Tests getSupportedExtensions()
	 *
	 * @covers  \Hubzero\Config\Processor::getSupportedExtensions
	 * @return  void
	 **/
	public function testGetSupportedExtensions()
	{
		$stub = $this->getMockForAbstractClass('Hubzero\Config\Processor');
		$stub->expects($this->any())
			->method('getSupportedExtensions')
			->will($this->returnValue(array()));

		$this->assertEquals(array(), $stub->getSupportedExtensions());
	}

	/**
	 * Tests parse()
	 *
	 * @covers  \Hubzero\Config\Processor::parse
	 * @return  void
	 **/
	public function testParse()
	{
		$stub = $this->getMockForAbstractClass('Hubzero\Config\Processor');
		$stub->expects($this->any())
			->method('parse')
			->with($this->isType('string'))
			->will($this->returnValue(array()));

		$this->assertEquals(array(), $stub->parse(__DIR__ . '/Tests/Files/test.json'));
	}
}
