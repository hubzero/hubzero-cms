<?php
/**
 * Test class for the time records table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'vote.php';

class KbTableVoteTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'object_id',
		'ip',
		'vote',
		'user_id',
		'type'
	);
	var $mock       = array(
		'id'          => null,
		'object_id'   => 123,
		'ip'          => '127.0.0.1',
		'vote'        => 'like',
		'user_id'     => 1000,
		'type'        => 'comment',
		'created'     => '2013-05-06 12:13:04'
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new KbTableVote($db);
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
	 * @group com_kb
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of KbTableVote
	 *
	 * @group com_kb
	 */
	function testIsInstanceOfKbTableVote()
	{
		$this->assertTrue($this->instance instanceof KbTableVote);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_kb
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test that record has specified attributes
	 *
	 * @group com_kb
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'KbTableVote');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_kb
	 * @covers KbTableVote::save
	 */
	function testRecordSave()
	{
		$result = $this->instance->save($this->mock);

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance->id;
	}

	/**
	 * Test record save
	 *
	 * @group com_kb
	 * @covers KbTableVote::check
	 */
	function testRecordCheck()
	{
		$this->instance->bind($this->mock);
		$result = $this->instance->check();

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance;
	}

	/**
	 * Test that type is a string
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableVote::check
	 */
	function testTypeIsString($instance)
	{
		$this->assertTrue(is_string($instance->type), "Scope is string");
	}

	/**
	 * Test that object ID is numeric
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableVote::check
	 */
	function testObjectIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->object_id), "Object ID is numeric");
	}

	/**
	 * Test record read
	 *
	 * @group com_kb
	 * @depends testRecordSave
	 * @covers TagsTableTag::load
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($this->instance->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_kb
	 * @depends testRecordSave
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record save fails when no type is provided
	 *
	 * @group com_kb
	 * @covers KbTableVote::check
	 */
	function testRecordCheckFailsWithNoType()
	{
		$mock = $this->mock;
		$mock['type'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no object ID is provided
	 *
	 * @group com_kb
	 * @covers KbTableVote::check
	 */
	function testRecordCheckFailsWithNoObjectId()
	{
		$mock = $this->mock;
		$mock['object_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no vote is provided
	 *
	 * @group com_kb
	 * @covers KbTableVote::check
	 */
	function testRecordCheckFailsWithNoVote()
	{
		$mock = $this->mock;
		$mock['vote'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}