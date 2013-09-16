<?php
/**
 * Test class for the answers component controller
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 */

// Include time component controller
require_once JPATH_BASE . DS . 'components' . DS . 'com_answers' . DS . 'controllers' . DS . 'questions.php';

/**
 * Test class for answers component primary controller
 */
class AnswersControllerTagsTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	/**
	 * Setup
	 */
	function setUp()
	{
		$app =& JFactory::getApplication('site');
		$this->instance = new AnswersControllerQuestions();
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
	 * @group com_answers
 	 * @runInSeparateProcess
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance, 'Controller isn\'t an object');
	}

	/**
	 * Test that instance is an instance of AnswersControllerQuestions
	 *
	 * @group com_answers
	 * @runInSeparateProcess
	 */
	function testIsInstanceOfTimeController()
	{
		$this->assertTrue($this->instance instanceof AnswersControllerQuestions);
	}

	/**
	 * Test that instance extends Hubzero_Controller
	 *
	 * @group com_answers
	 * @runInSeparateProcess
	 */
	function testExtendsHubzeroController()
	{
		$this->assertTrue($this->instance instanceof Hubzero_Controller);
	}
}