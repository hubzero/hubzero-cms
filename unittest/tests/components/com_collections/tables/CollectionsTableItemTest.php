<?php
/**
 * Test class for the time records table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'comment.php';

class CollectionsTableItemTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'title',
		'description',
		'url',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'state',
		'access',
		'positive',
		'negative',
		'type',
		'object_id'
	);
	var $mock       = array(
		'id'          => null,
		'title'       => 'Nullam quis risus',
		'description' => 'Nullam quis risus eget urna mollis ornare vel eu leo.',
		'url'         => '',
		'created'     => '2013-05-06 12:13:04',
		'created_by'  => 123,
		'modified'    => '',
		'modified_by' => 0,
		'state'       => 1,
		'access'      => 0,
		'positive'    => 3,
		'negative'    => 1,
		'type'        => 'file',
		'object_id'   => 1
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTableItem($db);
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
	 * Test that instance is an instance of CollectionsTableItem
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTableItem()
	{
		$this->assertTrue($this->instance instanceof CollectionsTableItem);
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
			$this->assertClassHasAttribute($a, 'CollectionsTableItem');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTableItem::save
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
	 * @covers CollectionsTableItem::check
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
	 * @covers CollectionsTableItem::check
	 */
	function testObjectIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->object_id), "Object ID is numeric");
	}

	/**
	 * Test that description is a string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableItem::check
	 */
	function testDescriptionIsString($instance)
	{
		$this->assertTrue(is_string($instance->description), "Description is string");
	}

	/**
	 * Test that type is string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableItem::check
	 */
	function testTypeIsString($instance)
	{
		$this->assertTrue(is_string($instance->type), "Type is string");
	}

	/**
	 * Test record read
	 *
	 * @group com_collections
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
	 * @group com_collections
	 * @depends testRecordSave
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableItem::check
	 */
	function testRecordCheckFailsWithNoContent()
	{
		$mock = $this->mock;
		$mock['content'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableItem::check
	 */
	function testRecordCheckFailsWithNoEntryId()
	{
		$mock = $this->mock;
		$mock['entry_id'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}