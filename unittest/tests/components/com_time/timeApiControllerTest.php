<?php
/**
 * Test class for the time api component controller
 * 
 * @author Sam Wilson <samwilson@purdue.edu>
 */

// Include time api component controller
require_once JPATH_BASE . DS . 'components' . DS . 'com_time' . DS . 'controllers' . DS . 'api.php';

/**
 * Test class for time component api controller
 */
class TimeApiControllerTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	/**
	 * Setup
	 */
	function setUp()
	{
		$this->instance = new TimeApiController();
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
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance);
	}

	/**
	 * Test that instance is an instance of TimeController
	 *
	 * @group com_time
	 */
	function testIsInstanceOfTimeApiController()
	{
		$this->assertTrue($this->instance instanceof TimeApiController);
	}

	/**
	 * Test that instance extends Hubzero_Controller
	 *
	 * @group com_time
	 */
	function testExtendsHubzeroApiController()
	{
		$this->assertTrue($this->instance instanceof Hubzero_Api_Controller);
	}
}