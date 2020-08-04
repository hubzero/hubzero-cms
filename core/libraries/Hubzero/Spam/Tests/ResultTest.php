<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\Result;

/**
 * Spam result test
 */
class ResultTest extends Basic
{
	/**
	 * Tests isSpam() returns correct value
	 *
	 * @covers  \Hubzero\Spam\Result::__construct
	 * @covers  \Hubzero\Spam\Result::isSpam
	 * @return  void
	 */
	public function testIsSpam()
	{
		$result = new Result(true);

		$this->assertTrue($result->isSpam());

		$result = new Result(false);

		$this->assertFalse($result->isSpam());
	}

	/**
	 * Tests passed() returns correct value depending on if spam or not
	 *
	 * @covers  \Hubzero\Spam\Result::__construct
	 * @covers  \Hubzero\Spam\Result::passed
	 * @return  void
	 */
	public function testPassed()
	{
		$result = new Result(false);

		$this->assertTrue($result->passed());

		$result = new Result(true);

		$this->assertFalse($result->passed());
	}

	/**
	 * Tests failed() returns correct value depending on if spam or not
	 *
	 * @covers  \Hubzero\Spam\Result::__construct
	 * @covers  \Hubzero\Spam\Result::failed
	 * @return  void
	 */
	public function testFailed()
	{
		$result = new Result(true);

		$this->assertTrue($result->failed());

		$result = new Result(false);

		$this->assertFalse($result->failed());
	}

	/**
	 * Tests getMessages() returns the list of messages passed in the constructor
	 *
	 * @covers  \Hubzero\Spam\Result::__construct
	 * @covers  \Hubzero\Spam\Result::getMessages
	 * @return  void
	 */
	public function testGetMessages()
	{
		$messages = [
			'Message one',
			'Message two'
		];

		$result = new Result(true, $messages);

		$this->assertEquals($messages, $result->getMessages());
	}
}
