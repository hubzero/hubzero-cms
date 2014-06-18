<?php
/**
 * Test class for the time records table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'following.php';

class CollectionsTableFollowingTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'follower_type',
		'follower_id',
		'created',
		'following_type',
		'following_id'
	);
	var $mock       = array(
		'id'             => null,
		'follower_type'  => 'member',
		'follower_id'    => 123,
		'created'        => '2-13=09-30 16:15:17',
		'following_type' => 'collection',
		'following_id'   => 1
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTableFollowing($db);
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
	 * Test that instance is an instance of CollectionsTableFollowing
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTableFollowing()
	{
		$this->assertTrue($this->instance instanceof CollectionsTableFollowing);
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
	 * Test that count() returns number
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::count
	 */
	function testCountIsNumeric()
	{
		$result = $this->instance->count();
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that count() with filters returns number
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::count
	 */
	function testGetCategoriesCountWithFiltersIsNumeric()
	{
		$filters = array(
			'following_type' => 'collection'
		);

		$result = $this->instance->count($filters);
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that find() returns an array
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::find
	 */
	function testFindIsArray()
	{
		$filters = array();
		$filters['start'] = 0;
		$filters['limit'] = 10;
		$result = $this->instance->find($filters);
		$this->assertTrue(is_array($result));
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
			$this->assertClassHasAttribute($a, 'CollectionsTableFollowing');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::save
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
	 * @covers CollectionsTableFollowing::getRecord
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
	 * Test record check fails when no following ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::check
	 */
	function testRecordCheckFailsWithNoFollowingId()
	{
		$mock = $this->mock;
		$mock['following_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no following type is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::check
	 */
	function testRecordCheckFailsWithNoFollowingType()
	{
		$mock = $this->mock;
		$mock['following_type'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no follower ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::check
	 */
	function testRecordCheckFailsWithNoFollowerId()
	{
		$mock = $this->mock;
		$mock['follower_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no follower type is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::check
	 */
	function testRecordCheckFailsWithNoFollowerType()
	{
		$mock = $this->mock;
		$mock['follower_type'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check passes
	 *
	 * @group com_collections
	 * @covers CollectionsTableFollowing::check
	 */
	function testRecordCheck()
	{
		$this->instance->title = 'Evil!';
		$result = $this->instance->check();

		$this->assertTrue($result);

		return $this->instance;
	}

	/**
	 * Test that the following type is a string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableFollowing::check
	 */
	function testFollowingTypeIsString($instance)
	{
		$this->assertTrue(is_string($instance->following_type), "Following type is string");
	}

	/**
	 * Test that the follower type is a string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableFollowing::check
	 */
	function testFollowerTypeIsString($instance)
	{
		$this->assertTrue(is_string($instance->follower_type), "Follower type is string");
	}

	/**
	 * Test that the following ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableFollowing::check
	 */
	function testFollowingIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->following_id), "Following ID is numeric");
	}

	/**
	 * Test that the follower ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableFollowing::check
	 */
	function testFollowerIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->follower_id), "Follower ID is numeric");
	}
}