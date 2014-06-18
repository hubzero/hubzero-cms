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
		$this->instance = new TagsControllerApi();
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
	 * @group com_tags
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of TagsControllerApi
	 *
	 * @group com_tags
	 */
	function testIsInstanceOfTagsControllerApi()
	{
		$this->assertTrue($this->instance instanceof TagsControllerApi);
	}

	/**
	 * Test that instance extends \Hubzero\Component\ApiController
	 *
	 * @group com_tags
	 */
	function testExtendsHubzeroApiController()
	{
		$this->assertTrue($this->instance instanceof \Hubzero\Component\ApiController);
	}
}