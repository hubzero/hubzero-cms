<?php
/**
 * Test class for the collections asset table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php';

class CollectionsTableAssetTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'item_id',
		'created',
		'created_by',
		'filename',
		'description',
		'state',
		'type',
		'ordering'
	);
	var $mock       = array(
		'id'          => null,
		'item_id'     => 123,
		'created'     => '2013-05-06 12:13:04',
		'created_by'  => 1000,
		'filename'    => '',
		'description' => '',
		'state'       => 1,
		'type'        => '',
		'ordering'    => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new CollectionsTableAsset($db);
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
	 * Test that instance is an instance of CollectionsTableAsset
	 *
	 * @group com_collections
	 */
	function testIsInstanceOfCollectionsTableAsset()
	{
		$this->assertTrue($this->instance instanceof CollectionsTableAsset);
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
			$this->assertClassHasAttribute($a, 'CollectionsTableAsset');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_collections
	 * @covers CollectionsTableAsset::save
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
	 * @covers CollectionsTableAsset::check
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
	 * Test that the type is a string
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableAsset::check
	 */
	function testTypeIsString($instance)
	{
		$this->assertTrue(is_string($instance->type), "Type is string");
	}

	/**
	 * Test that the Item ID is numeric
	 *
	 * @group com_collections
	 * @depends testRecordCheck
	 * @covers CollectionsTableAsset::check
	 */
	function testItemIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->object_id), "Item ID is numeric");
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
	 * Test record save fails when no type is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableAsset::check
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
	 * @group com_collections
	 * @covers CollectionsTableAsset::check
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
	 * Test record save fails when no vote is provided
	 *
	 * @group com_collections
	 * @covers CollectionsTableAsset::check
	 */
	function testRecordCheckFailsWithNoFilename()
	{
		$mock = $this->mock;
		$mock['filename'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}