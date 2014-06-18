<?php
/**
 * Test class for the collections vote table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'vote.php';

class CollectionsTableVoteTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'user_id',
		'item_id',
		'voted'
	);
	var $mock       = array(
		'id'           => null,
		'user_id'      => 123,
		'item_id'      => 1,
		'voted'        => '2013-09-29 14:16:39'
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTableVote($db);
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
	 * @group com_collections
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of CollectionsTableVote
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTableVote()
	{
		$this->assertTrue($this->instance instanceof CollectionsTableVote);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_collections
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test that record has specified attributes
	 *
	 * @group com_collections
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'CollectionsTableVote');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTableVote::save
	 */
	function testRecordSave()
	{
		$result = $this->instance->save($this->mock);

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance->id;
	}

	/**
	 * Test record read
	 *
	 * @group com_collections
	 * @depends testRecordSave
	 * @covers CollectionsTableVote::getRecord
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($this->instance->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_collections
	 * @depends testRecordSave
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record check fails when no user ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableVote::check
	 */
	function testRecordCheckFailsWithNoUserId()
	{
		$mock = $this->mock;
		$mock['user_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no item ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableVote::check
	 */
	function testRecordCheckFailsWithNoItemId()
	{
		$mock = $this->mock;
		$mock['item_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check passes
	 *
	 * @group com_collections
	 * @covers CollectionsTableVote::check
	 */
	function testRecordCheck()
	{
		$this->instance->title = 'Evil!';
		$result = $this->instance->check();

		$this->assertTrue($result);

		return $this->instance;
	}

	/**
	 * Test that the user ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableVote::check
	 */
	function testUserIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->user_id), "User ID is numeric");
	}

	/**
	 * Test that the item ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableVote::check
	 */
	function testItemIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->item_id), "Item ID is numeric");
	}
}