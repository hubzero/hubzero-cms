<?php
/**
 * Test class for the time records table class
 * 
 * @author Sam Wilson <samwilson@purdue.edu>
 * @runTestsInSeparateProcesses
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
		$this->instance = new TagsTableOption($db);
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
	function testIsInstanceOfTagsTableObject()
	{
		$this->assertTrue($this->instance instanceof TagsTableObject);
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

	/**
	 * Test that getCount returns number
	 *
	 * @group com_time
	 * @covers TagsTableTag::getCount
	 */
	function testCountIsNumeric()
	{
		$result = $this->instance->count();
		$this->assertType('numeric', $result, "Tags Object Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_time
	 * @covers TagsTableTag::getCount
	 */
	function testCountWithFiltersIsNumeric()
	{
		$filters = array(
			'tbl' => 'resource'
		);

		$result = $this->instance->count($filters);
		$this->assertType('numeric', $result, "Tags Object Count: $result");
	}

	/**
	 * Test that getRecords
	 * 
	 * @group com_time
	 * @covers TagsTableTag::getRecords
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
	 * @group com_time
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
	 * @group com_time
	 * @covers TagsTableTag::save
	 */
	function testRecordSave()
	{
		$result = $this->instance->save($this->mock);

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance->id;
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_time
	 * @covers TagsTableTag::getCount
	 */
	function testScopeIdIsNumeric()
	{
		$this->assertType('numeric', $this->instance->objectid, "Scope ID is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_time
	 * @covers TagsTableTag::getCount
	 */
	function testScopeIsString()
	{
		$this->assertType('string', $this->instance->tbl, "Scope is string");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_time
	 * @covers TagsTableTag::getCount
	 */
	function testTagIdIsNumeric()
	{
		$this->assertType('numeric', $this->instance->tagid, "Tag ID is numeric");
	}

	/**
	 * Test record read
	 *
	 * @group com_time
	 * @depends testRecordSave
	 * @covers TagsTableTag::getRecord
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($result->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_time
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
	 * @group com_time
	 */
	function testRecordSaveFailsWithNoScope()
	{
		$mock = $this->mock;
		$mock['tbl'] = '';
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_time
	 */
	function testRecordSaveFailsWithNoScopeId()
	{
		$mock = $this->mock;
		$mock['objectid'] = 0;
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_time
	 */
	function testRecordSaveFailsWithNoTagId()
	{
		$mock = $this->mock;
		$mock['tagid'] = 0;
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}
}