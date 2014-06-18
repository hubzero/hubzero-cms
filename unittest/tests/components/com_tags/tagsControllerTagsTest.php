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
		$this->instance = new TagsControllerTags();
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
 	 * @runInSeparateProcess
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of TagsControllerTags
	 *
	 * @group com_tags
	 * @runInSeparateProcess
	 */
	function testIsInstanceOfTimeController()
	{
		$this->assertTrue($this->instance instanceof TagsControllerTags);
	}

	/**
	 * Test that instance extends \Hubzero\Component\SiteController
	 *
	 * @group com_tags
	 * @runInSeparateProcess
	 */
	function testExtendsHubzeroController()
	{
		$this->assertTrue($this->instance instanceof \Hubzero\Component\SiteController);
	}
}