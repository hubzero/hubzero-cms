<?php
/**
 * Test class for the tags component controller
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 */

// Include time component controller
require_once JPATH_BASE . DS . 'components' . DS . 'com_tags' . DS . 'controllers' . DS . 'tags.php';

/**
 * Test class for time component primary controller
 */
class TagsControllerTagsTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	/**
	 * Setup
	 */
	function setUp()
	{
		$app =& JFactory::getApplication('site');
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
	 * Test that instance is an instance of TagsControllerTags
	 *
	 * @group com_time
	 * @runInSeparateProcess
	 */
	function testIsInstanceOfTimeController()
	{
		$this->assertTrue($this->instance instanceof TagsControllerTags);
	}

	/**
	 * Test that instance extends Hubzero_Controller
	 *
	 * @group com_time
	 * @runInSeparateProcess
	 */
	function testExtendsHubzeroController()
	{
		$this->assertTrue($this->instance instanceof Hubzero_Controller);
	}
}