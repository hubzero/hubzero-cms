<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Tests;

use Hubzero\Test\Basic;
use Hubzero\Debug\Profiler;

/**
 * Profiler tests
 */
class ProfilerTest extends Basic
{
	/**
	 * Hubzero\Debug\Profiler
	 *
	 * @var  object
	 */
	private $instance;

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new Profiler('test');
	}

	/**
	 * Tests the __constructor.
	 *
	 * @covers  \Hubzero\Debug\Profiler::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$instance = new Profiler();

		$this->assertGreaterThan(0, $instance->started());
		$this->assertGreaterThan(0, $instance->memory());
		$this->assertEquals(count($instance->marks()), 0);
		$this->assertEquals($instance->label(), '');

		$instance = new Profiler('test');

		$this->assertEquals($instance->label(), 'test');
	}

	/**
	 * Tests the marks() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::marks
	 * @return  void
	 **/
	public function testMarks()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		// Assert the first point has a time and memory = 0
		$marks = $this->instance->marks();

		$this->assertTrue(is_array($marks), 'marks() should return an array');
		$this->assertEquals(count($marks), 3);
	}

	/**
	 * Tests the mark() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::mark
	 * @return  void
	 **/
	public function testMark()
	{
		$started = $this->instance->started();

		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		// Assert the first point has a time and memory = 0
		$marks = $this->instance->marks();

		$first = $marks[0];

		$this->assertEquals($first->label(), 'one');
		$this->assertEquals($first->started(), $started);

		// Assert the other points have a time and memory
		$second = $marks[1];

		$this->assertEquals($second->label(), 'two');
		$this->assertGreaterThan(0, $second->duration());
		$this->assertGreaterThan(0, $second->memory());

		$third = $marks[2];

		$this->assertEquals($third->label(), 'three');
		$this->assertGreaterThan(0, $third->duration());
		$this->assertGreaterThan(0, $third->memory());

		// Assert the third point has greater values than the other points
		$this->assertGreaterThan($second->ended(), $third->ended());
		$this->assertGreaterThanOrEqual($second->memory(), $third->memory());
	}

	/**
	 * Tests the duration() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::duration
	 * @return  void
	 **/
	public function testDuration()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		$this->assertGreaterThan(0, $this->instance->duration());
	}

	/**
	 * Tests the label() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::label
	 * @return  void
	 **/
	public function testLabel()
	{
		$this->assertEquals($this->instance->label(), 'test');
	}

	/**
	 * Tests the now() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::now
	 * @return  void
	 **/
	public function testNow()
	{
		$this->assertGreaterThanOrEqual(microtime(true), $this->instance->now());
	}

	/**
	 * Tests the reset() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::reset
	 * @return  void
	 **/
	public function testReset()
	{
		$instance = new Profiler('test');

		$instance->mark('one');
		$instance->mark('two');
		$instance->mark('three');

		$instance->reset();

		$marks = $instance->marks();

		$this->assertTrue(empty($marks));
		$this->assertEquals($instance->label(), '');
	}

	/**
	 * Tests the started() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::started
	 * @return  void
	 **/
	public function testStarted()
	{
		$instance = new Profiler('test');

		$this->assertTrue($instance->started() >= time());
	}

	/**
	 * Tests the ended() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::ended
	 * @return  void
	 **/
	public function testEnded()
	{
		$instance = new Profiler('test');

		$started = $instance->started();

		sleep(0.1);

		$this->assertEquals($instance->ended(), $started);

		$instance->mark('one');
		$instance->mark('two');
		$instance->mark('three');

		$this->assertNotEquals($instance->ended(), $started);
		$this->assertTrue($instance->ended() > $started);
	}

	/**
	 * Tests the memory() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::memory
	 * @return  void
	 **/
	public function testMemory()
	{
		$instance = $this->instance;

		$memory1 = $instance->memory();

		$instance->mark('foo');

		$data = array();
		for ($i = 0; $i < 900; $i++)
		{
			$jnk = new \stdClass;
			$jnk->bar = array_fill(0, 100, str_repeat('bar', 10));

			$data[] = $jnk;
		}

		$instance->mark('bar');

		unset($data);

		$memory2 = $instance->memory();

		$this->assertTrue($memory2 >= $memory1);
	}

	/**
	 * Tests the summary() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::summary
	 * @return  void
	 **/
	public function testSummary()
	{
		$summary = $this->instance->summary();

		$this->assertTrue(is_array($summary));
		$this->assertTrue(array_key_exists('start', $summary));
		$this->assertTrue(array_key_exists('end', $summary));
		$this->assertTrue(array_key_exists('total', $summary));
		$this->assertTrue(array_key_exists('memory', $summary));
	}
}
