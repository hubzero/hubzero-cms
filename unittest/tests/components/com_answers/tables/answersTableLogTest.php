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
	 * @group com_answers
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance);
	}

	/**
	 * Test that instance is an instance of TagsTableTag
	 *
	 * @group com_answers
	 */
	function testIsInstanceOfAnswersTableLog()
	{
		$this->assertTrue($this->instance instanceof AnswersTableLog);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_answers
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test record save
	 *
	 * @group com_answers
	 * @covers AnswersTableLog::save
	 */
	function testRecordSave()
	{
		$this->instance->bind($this->mock);
		$result = $this->instance->save();

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance->id;
	}

	/**
	 * Test record read
	 *
	 * @group com_answers
	 * @depends testRecordSave
	 * @covers AnswersTableLog::load
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($result->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_answers
	 * @depends testRecordSave
	 * @covers AnswersTableLog::delete
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record check
	 *
	 * @group com_answers
	 * @covers AnswersTableLog::check
	 */
	function testRecordCheck()
	{
		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertTrue($result);
	}

	/**
	 * Test record check fails when no rid (response ID)
	 *
	 * @group com_answers
	 * @covers AnswersTableLog::check
	 */
	function testRecordCheckFailsWithNoRid()
	{
		$mock = $this->mock;
		$mock['rid'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails with invalid helpful value
	 *
	 * @group com_answers
	 * @covers AnswersTableLog::check
	 */
	function testRecordCheckFailsWithInvalidHelpful()
	{
		$mock = $this->mock;
		$mock['helpful'] = 5;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails with invalid IP
	 *
	 * @group com_answers
	 * @covers AnswersTableLog::check
	 */
	function testRecordCheckFailsWithInvalidIp()
	{
		$mock = $this->mock;
		$mock['ip'] = 'bad.ip.address';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}