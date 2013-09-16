<?php
/**
 * Test class for the time records table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'object.php';

class TagsTableObjectTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'objectid', 
		'tagid', 
		'strength', 
		'taggerid',
		'taggedon',
		'tbl',
		'label'
	);
	var $mock       = array(
		'id'          => null,
		'objectid'    => 123,
		'tagid'       => 456,
		'strength'    => 1,
		'taggerid'    => 1000,
		'taggedon'    => '2013-05-06 12:13:04',
		'tbl'         => 'resource',
		'label'       => ''
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new TagsTableObject($db);
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
	 * @group com_tags
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of TagsTableObject
	 *
	 * @group com_tags
	 */
	function testIsInstanceOfTagsTableObject()
	{
		$this->assertTrue($this->instance instanceof TagsTableObject);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_tags
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_tags
	 * @covers TagsTableObject::getCount
	 */
	function testCountIsNumeric()
	{
		$result = $this->instance->count();
		$this->assertTrue(is_numeric($result), "Tags Object Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_tags
	 * @covers TagsTableObject::count
	 */
	function testCountWithFiltersIsNumeric()
	{
		$filters = array(
			'tbl' => 'resource'
		);

		$result = $this->instance->count($filters);
		$this->assertTrue(is_numeric($result), "Tags Object Count: $result");
	}

	/**
	 * Test that getRecords
	 * 
	 * @group com_tags
	 * @covers TagsTableObject::find
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
	 * @group com_tags
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'TagsTableObject');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_tags
	 * @covers TagsTableObject::save
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
	 * @group com_tags
	 * @covers TagsTableObject::check
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
	 * Test that getCount returns number
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableObject::check
	 */
	function testScopeIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->objectid), "Scope ID is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableObject::check
	 */
	function testScopeIsString($instance)
	{
		$this->assertTrue(is_string($instance->tbl), "Scope is string");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableObject::check
	 */
	function testTagIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->tagid), "Tag ID is numeric");
	}

	/**
	 * Test record read
	 *
	 * @group com_tags
	 * @depends testRecordSave
	 * @covers TagsTableTag::load
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($result->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_tags
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
	 * @group com_tags
	 * @covers TagsTableObject::check
	 */
	function testRecordCheckFailsWithNoScope()
	{
		$mock = $this->mock;
		$mock['tbl'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_tags
	 * @covers TagsTableObject::check
	 */
	function testRecordCheckFailsWithNoScopeId()
	{
		$mock = $this->mock;
		$mock['objectid'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_tags
	 * @covers TagsTableObject::check
	 */
	function testRecordCheckFailsWithNoTagId()
	{
		$mock = $this->mock;
		$mock['tagid'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}