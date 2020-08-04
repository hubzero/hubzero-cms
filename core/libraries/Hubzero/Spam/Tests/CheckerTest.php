<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\Checker;
use Hubzero\Spam\Tests\Mock\Detector;
use Hubzero\Spam\Tests\Mock\DetectorException;
use Hubzero\Spam\StringProcessor\NoneStringProcessor;
use Hubzero\Spam\StringProcessor\NativeStringProcessor;

/**
 * Spam checker tests
 */
class CheckerTest extends Basic
{
	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\Checker::__construct
	 * @covers  \Hubzero\Spam\Checker::setStringProcessor
	 * @covers  \Hubzero\Spam\Checker::getStringProcessor
	 * @return  void
	 **/
	public function testStringProcessor()
	{
		$service = new Checker(new NoneStringProcessor());

		$this->assertInstanceOf('Hubzero\Spam\StringProcessor\NoneStringProcessor', $service->getStringProcessor());

		$service->setStringProcessor(new NativeStringProcessor());

		$this->assertInstanceOf('Hubzero\Spam\StringProcessor\NativeStringProcessor', $service->getStringProcessor());
	}

	/**
	 * Test to make sure a detector is registered properly
	 * and returns $this.
	 *
	 * @covers  \Hubzero\Spam\Checker::registerDetector
	 * @return  void
	 **/
	public function testRegisterDetector()
	{
		$service = new Checker();

		$this->assertInstanceOf('Hubzero\Spam\Checker', $service->registerDetector(new Detector()));

		$this->setExpectedException('RuntimeException');

		$service->registerDetector(new Detector());
	}

	/**
	 * Test to get a registered detector
	 *
	 * @covers  \Hubzero\Spam\Checker::getDetector
	 * @covers  \Hubzero\Spam\Checker::classSimpleName
	 * @return  void
	 **/
	public function testGetDetector()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		$this->assertInstanceOf('Hubzero\Spam\Tests\Mock\Detector', $service->getDetector('Hubzero\Spam\Tests\Mock\Detector'));
		$this->assertFalse($service->getDetector('Hubzero\Spam\Tests\Mock\Example'));
	}

	/**
	 * Test that getDetectors returns an array of detectors
	 *
	 * @covers  \Hubzero\Spam\Checker::getDetectors
	 * @return  void
	 **/
	public function testGetDetectors()
	{
		$d = new Detector();
		$k = get_class($d);

		$data = [];
		$data[$k] = $d;

		$service = new Checker();
		$service->registerDetector($data[$k]);

		$detectors = $service->getDetectors();

		$this->assertTrue(is_array($detectors), 'Getting all detectors should return an array');
		$this->assertCount(1, $detectors, 'Get detectors should have returned one detector');
		$this->assertEquals($detectors, $data);
	}

	/**
	 * Test that getReport() returns an array
	 *
	 * @covers  \Hubzero\Spam\Checker::getReport
	 * @return  void
	 **/
	public function testGetReport()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		$report = $service->getReport();

		$this->assertTrue(is_array($report));
	}

	/**
	 * Test the check() method
	 *
	 * @covers  \Hubzero\Spam\Checker::check
	 * @covers  \Hubzero\Spam\Checker::prepareData
	 * @covers  \Hubzero\Spam\Checker::mark
	 * @return  void
	 **/
	public function testCheck()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		// This should NOT be caught as spam
		$result = $service->check('Maecenas sed diam eget risus varius blandit sit amet non magna.');

		$this->assertInstanceOf('Hubzero\Spam\Result', $result);
		$this->assertFalse($result->isSpam());

		// This should be caught as spam
		$result = $service->check('Maecenas sed diam eget risus varius spam blandit sit amet non magna.');

		$this->assertInstanceOf('Hubzero\Spam\Result', $result);
		$this->assertTrue($result->isSpam());

		$messages = $result->getMessages();
		$this->assertTrue(is_array($messages));
		$this->assertTrue(in_array('Text contained the word "spam".', $messages));

		// Make sure string processors do their job
		$service->setStringProcessor(new NativeStringProcessor());

		$result = $service->check("Maecenas sed diam eget risus varius sp\nam blandit sit amet non magna.");

		$this->assertInstanceOf('Hubzero\Spam\Result', $result);
		$this->assertTrue($result->isSpam());

		// Make sure exceptions are caught and passed as error messages
		$service->registerDetector(new DetectorException());

		$result = $service->check('Maecenas sed diam eget risus varius spam blandit sit amet non magna.');

		$error = $service->getError();

		$this->assertEquals($error, 'I always throw an exception.');
	}
}
