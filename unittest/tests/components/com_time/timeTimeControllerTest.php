<?php
/**
 * Test class for the time component controller
 *
 * @author Sam Wilson <samwilson@purdue.edu>
 */

// Include time component controller
require_once JPATH_BASE . DS . 'components' . DS . 'com_time' . DS . 'controllers' . DS . 'time.php';

/**
 * Test class for time component primary controller
 */
class TimeControllerTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	/**
	 * Setup
	 */
	function setUp()
	{
		$app = JFactory::getApplication('site');
		$this->instance = new TimeController();
	}

	/**
	 * Tear down
	 */
	function tearDown()
	{
		$this->instance = null;
	}

	/**
	 * Test if $this->instance is an object
	 *
	 * @group com_time
 	 * @runInSeparateProcess
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance, 'Controller isn\'t an object');
	}

	/**
	 * Test that instance is an instance of TimeController
	 *
	 * @group com_time
	 * @runInSeparateProcess
	 */
	function testIsInstanceOfTimeController()
	{
		$this->assertTrue($this->instance instanceof TimeController);
	}

	/**
	 * Test that instance extends \Hubzero\Component\SiteController
	 *
	 * @group com_time
	 * @runInSeparateProcess
	 */
	function testExtendsHubzeroController()
	{
		$this->assertTrue($this->instance instanceof \Hubzero\Component\SiteController);
	}
}