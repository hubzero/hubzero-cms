<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Tests;

use Hubzero\Test\Basic;
use Hubzero\Debug\Profile\Mark;

/**
 * Profiler mark tests
 */
class MarkTest extends Basic
{
	/**
	 * Tests that data passed in constructor is set to correct properties
	 *
	 * @covers  \Hubzero\Debug\Profile\Mark::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$mark = new Mark('test1');

		$this->assertEquals($mark->label(), 'test1');
		$this->assertEquals($mark->started(), 0.0);
		$this->assertEquals($mark->ended(), 0.0);
		$this->assertEquals($mark->memory(), 0);

		$mark = new Mark('test2', 1.5, 3.5, 1048576);

		$this->assertEquals($mark->label(), 'test2');
		$this->assertEquals($mark->started(), 1.5);
		$this->assertEquals($mark->ended(), 3.5);
		$this->assertEquals($mark->memory(), 1048576);
	}

	/**
	 * Tests the label() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::label
	 */
	public function testLabel()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->label(), 'test');
	}

	/**
	 * Tests the started() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::started
	 */
	public function testStarted()
	{
		$mark = new Mark('test', 0, 0, 0);
		$this->assertEquals($mark->started(), 0);

		$mark = new Mark('test', 1.5, 3.5, 0);
		$this->assertEquals($mark->started(), 1.5);
	}

	/**
	 * Tests the ended() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::ended
	 */
	public function testEnded()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->ended(), 1.5);

		$mark = new Mark('test', 1.5, 3.5, 0);
		$this->assertEquals($mark->ended(), 3.5);
	}

	/**
	 * Tests the duration() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::duration
	 */
	public function testDuration()
	{
		$mark = new Mark('test', 0, 0, 0);
		$this->assertEquals($mark->duration(), 0);

		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->duration(), 1.5);
	}

	/**
	 * Tests the memory() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::memory
	 */
	public function testMemory()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->memory(), 0);

		$mark = new Mark('test', 0, 1.5, 1048576);
		$this->assertEquals($mark->memory(), 1048576);
	}

	/**
	 * Tests the toString() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::toString
	 * @covers  \Hubzero\Debug\Profile\Mark::__toString
	 */
	public function testToString()
	{
		$mark = new Mark('test', 0, 1.5, 1048576);

		$result = sprintf('%s: %.2F MiB - %d ms', 'test', 1048576 / 1024 / 1024, 1.5);

		$this->assertEquals($mark->toString(), $result);
		$this->assertEquals($mark->__toString(), $result);
		$this->assertEquals((string)$mark, $result);
	}

	/**
	 * Tests the toArray() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::toArray
	 */
	public function testToArray()
	{
		$mark = new Mark('test', 0, 1.5, 1048576);

		$result = array(
			'label'  => 'test',
			'start'  => 0.0,
			'end'    => 1.5,
			'memory' => 1048576
		);

		$this->assertEquals($mark->toArray(), $result);
	}
}
