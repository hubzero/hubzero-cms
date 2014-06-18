<?php
/**
 * Test class for the collections table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'collection.php';

class CollectionsTableCollectionTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'title',
		'alias',
		'object_id',
		'object_type',
		'created',
		'created_by',
		'state',
		'access',
		'is_default',
		'description',
		'positive',
		'negative'
	);
	var $mock       = array(
		'id'          => null,
		'title'       => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
		'alias'       => '',
		'object_id'   => 0,
		'object_type' => '',
		'created'     => '2013-05-06 12:13:04',
		'created_by'  => 123,
		'state'       => 1,
		'access'      => 0,
		'is_default'  => 0,
		'description' => 'Nullam quis risus eget urna mollis ornare vel eu leo. Nullam id dolor id nibh ultricies vehicula ut id elit. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
		'positive'    => 0,
		'negative'    => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTableCollection($db);
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
	 * Test that instance is an instance of CollectionsTableCollection
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTableCollection()
	{
		$this->assertTrue($this->instance instanceof CollectionsTableCollection);
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
	 * @covers CollectionsTableCollection::getCount
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
	 * @covers CollectionsTableCollection::count
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters = array(
			'search' => 'fusce'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getRecords
	 *
	 * @group com_collections
	 * @covers CollectionsTableCollection::find
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
			$this->assertClassHasAttribute($a, 'CollectionsTableCollection');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTableCollection::save
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
	 * @group com_collections
	 * @covers CollectionsTableCollection::check
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
	 * Test that object ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableCollection::check
	 */
	function testObjectIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->object_id), "Object ID is numeric");
	}

	/**
	 * Test that object type is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableCollection::check
	 */
	function testObjectTypeIsNumeric($instance)
	{
		$this->assertTrue(is_string($instance->object_type), "Object Type is string");
	}

	/**
	 * Test that the title is a string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableCollection::check
	 */
	function testTitleIsString($instance)
	{
		$this->assertTrue(is_string($instance->title), "Title is string");
	}

	/**
	 * Test that alias is string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableCollection::check
	 */
	function testAliasIsString($instance)
	{
		$this->assertTrue(is_string($instance->alias), "Alias is string");
	}

	/**
	 * Test record read
	 *
	 * @group com_collections
	 * @depends testRecordSave
	 * @covers CollectionsTableCollection::load
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
	 * Test record check fails when no title is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableCollection::check
	 */
	function testRecordCheckFailsWithNoTitle()
	{
		$mock = $this->mock;
		$mock['title'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check fails when no object ID is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableCollection::check
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
	 * Test record check fails when no object type is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableCollection::check
	 */
	function testRecordCheckFailsWithNoObjectType()
	{
		$mock = $this->mock;
		$mock['object_type'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}