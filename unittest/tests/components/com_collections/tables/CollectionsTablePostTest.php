<?php
/**
 * Test class for the collections post table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php';

class CollectionsTablePostTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'created',
		'created_by',
		'collection_id',
		'item_id',
		'description',
		'original'
	);
	var $mock       = array(
		'id'            => null,
		'created'       => '2013-09-17 12:31:45',
		'created_by'    => 123,
		'collection_id' => 1,
		'item_id'       => 1,
		'description'   => 'Neat thing here.',
		'original'      => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTablePost($db);
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
	 * Test that instance is an instance of CollectionsTablePost
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTablePost()
	{
		$this->assertTrue($this->instance instanceof CollectionsTablePost);
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
	 * Test that getCount returns number
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::getCount
	 */
	function testGetCountIsNumeric()
	{
		$result = $this->instance->getCount();
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::getCount
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters = array(
			'section' => 0,
			'search'  => 'hub'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getRecords returns an array
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::getRecords
	 */
	function testGetRecordsIsArray()
	{
		$filters = array();
		$filters['start'] = 0;
		$filters['limit'] = 10;
		$result = $this->instance->getRecords($filters);
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
			$this->assertClassHasAttribute($a, 'CollectionsTablePost');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::save
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
	 * @covers CollectionsTablePost::getRecord
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
	 * Test record check fails when no collection ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::check
	 */
	function testRecordCheckFailsWithNoCollectionId()
	{
		$mock = $this->mock;
		$mock['collection_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no item ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTablePost::check
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
	 * @covers CollectionsTablePost::check
	 */
	function testRecordCheck()
	{
		$this->instance->title = 'Evil!';
		$result = $this->instance->check();

		$this->assertTrue($result);

		return $this->instance;
	}

	/**
	 * Test that collection ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTablePost::check
	 */
	function testCollectionIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->collection_id), "Collection ID is numeric");
	}

	/**
	 * Test that item ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTablePost::check
	 */
	function testItemIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->item_id), "Item ID is numeric");
	}
}