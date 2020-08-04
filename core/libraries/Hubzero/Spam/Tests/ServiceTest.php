<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;

/**
 * Spam (abstract) Service tests
 */
class ServiceTest extends Basic
{
	/**
	 * Get the mock object
	 *
	 * @return  object
	 **/
	protected function getStub()
	{
		return $this->getMockForAbstractClass('Hubzero\Spam\Detector\Service');
	}

	/**
	 * Tests for setting and getting a value
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::setValue
	 * @covers  \Hubzero\Spam\Detector\Service::getValue
	 * @return  void
	 **/
	public function testValue()
	{
		$stub = $this->getStub();

		$stub->setValue('foo');

		$this->assertEquals($stub->getValue(), 'foo');
	}

	/**
	 * Tests detect() returns false
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::detect
	 * @return  void
	 **/
	public function testDetect()
	{
		$stub = $this->getStub();

		$this->assertFalse($stub->detect('foo'));
	}

	/**
	 * Tests learn()
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::learn
	 * @return  void
	 **/
	public function testLearn()
	{
		$stub = $this->getStub();

		$isSpam = true;

		$this->assertFalse($stub->learn('', $isSpam));
		$this->assertTrue($stub->learn('foo', $isSpam));
	}

	/**
	 * Tests forget()
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::forget
	 * @return  void
	 **/
	public function testForget()
	{
		$stub = $this->getStub();

		$isSpam = true;

		$this->assertTrue($stub->forget('foo', $isSpam));
	}

	/**
	 * Tests message() returns an empty string
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::message
	 * @return  void
	 **/
	public function testMessage()
	{
		$stub = $this->getStub();

		$this->assertEquals($stub->message(), '');
	}
}
