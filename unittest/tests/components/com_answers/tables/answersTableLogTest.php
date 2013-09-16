<?php
/**
 * Test class for the log table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . '/administrator/components/com_answers/tables/log.php';

class AnswersTableLogTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'rid', 
		'ip', 
		'helpful'
	);
	var $mock       = array(
		'id'      => null,
		'rid'     => 1,
		'ip'      => '127.0.0.1',
		'helpful' => 1
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new AnswersTableLog($db);
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
	 * Test that instance is an instance of TagsTableTag
	 *
	 * @group com_time
	 */
	function testIsInstanceOfAnswersTableLog()
	{
		$this->assertTrue($this->instance instanceof AnswersTableLog);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_time
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}
}