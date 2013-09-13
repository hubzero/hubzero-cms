<?php
/**
 * Test class for the tags API component controller
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 */

// Include time api component controller
require_once JPATH_BASE . DS . 'components' . DS . 'com_tags' . DS . 'controllers' . DS . 'api.php';

/**
 * Test class for time component api controller
 */
class TagsControllerApiTest extends PHPUnit_Framework_TestCase
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
	function testIsInstanceOfTagsControllerApi()
	{
		$this->assertTrue($this->instance instanceof TagsControllerApi);
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