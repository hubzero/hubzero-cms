<?php
/**
 * Test class for the time records table class
 *
 * @author Sam Wilson <samwilson@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');
require_once JPATH_BASE . DS . 'plugins' . DS . 'time' . DS . 'tables' . DS . 'records.php';

class TimeRecordsTableTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array('id', 'task_id', 'user_id', 'time', 'date', 'description', 'billed');
	var $mock       = array(
		'id'          => null,
		'task_id'     => 1,
		'user_id'     => 1,
		'time'        => 1,
		'date'        => '2012-01-01',
		'description' => 'unittest',
		'billed'      => 0
		);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new TimeRecords($db);
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
	 * Test that instance is an instance of TimeRecords
	 *
	 * @group com_time
	 */
	function testIsInstanceOfTimeRecords()
	{
		$this->assertTrue($this->instance instanceof TimeRecords);
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
	 * @covers TimeRecords::getCount
	 */
	function testGetCountIsNumeric()
	{
		$result = $this->instance->getCount();
		$this->assertType('numeric', $result, "Time Records Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 *
	 * @group com_time
	 * @covers TimeRecords::getCount
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters           = array();
		$filters['user']   = 1000;
		$filters['task']   = 1;
		$filters['search'] = array('test');
		$filters['q']      = array();
		$filters['q'][1]   = array('column' => 'time', 'o' => '=', 'value' => '1.0');

		$result = $this->instance->getCount($filters);
		$this->assertType('numeric', $result, "Time Records Count: $result");
	}

	/**
	 * Test that getSummaryHours returns array
	 *
	 * @group com_time
	 * @covers TimeRecords::getSummaryHours
	 */
	function testGetSummaryHoursIsArray()
	{
		$result = $this->instance->getSummaryHours('10', '1');
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getSummaryHoursByHub returns array
	 *
	 * @group com_time
	 * @covers TimeRecords::getSummaryHoursByHub
	 */
	function testGetSummaryHoursByHubIsArray()
	{
		$result = $this->instance->getSummaryHoursByHub('10', '1');
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getSummaryEntries returns array
	 *
	 * @group com_time
	 * @covers TimeRecords::getSummaryEntries
	 */
	function testGetSummaryEntriesIsArray()
	{
		$date = array('start' => '2000-00-00', 'end' => '2100-00-00');
		$result = $this->instance->getSummaryEntries($date);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getTotalHours returns number
	 *
	 * @group com_time
	 * @covers TimeRecords::getTotalHours
	 */
	function testGetTotalHoursIsNumeric()
	{
		$result = $this->instance->getTotalHours();
		$this->assertTrue(is_numeric($result));
	}

	/**
	 * Test that getTotalHours returns number with filters
	 *
	 * @group com_time
	 * @covers TimeRecords::getTotalHours
	 */
	function testGetTotalHoursWithFiltersIsNumeric()
	{
		$filter1             = array();
		$filter1['user_id']  = '1000';

		$result = $this->instance->getTotalHours($filter1);
		$this->assertTrue(is_numeric($result));

		$filter2             = array();
		$filter2['id_range'] = '1,2,3';

		$result = $this->instance->getTotalHours($filter2);
		$this->assertTrue(is_numeric($result));

		$filters = array_merge($filter1, $filter2);

		$result = $this->instance->getTotalHours($filters);
		$this->assertTrue(is_numeric($result));
	}

	/**
	 * Test that getRecords
	 *
	 * @group com_time
	 * @covers TimeRecords::getRecords
	 */
	function testGetRecords()
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
	 * @group com_time
	 */
	function testRecordsHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'TimeRecords');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_time
	 * @covers TimeRecords::save
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
	 * @group com_time
	 * @depends testRecordSave
	 * @covers TimeRecords::getRecord
	 */
	function testRecordRead($id)
	{
		$result = $this->instance->getRecord($id);
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
	function testRecordSaveFailsWithNoTime()
	{
		$mock = $this->mock;
		$mock['time'] = '0.0';
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no task is provided
	 *
	 * @group com_time
	 */
	function testRecordSaveFailsWithNoTask()
	{
		$mock = $this->mock;
		$mock['task_id'] = null;
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record check passes
	 *
	 * @group com_time
	 */
	function testRecordCheckReturnsTrue()
	{
		$this->instance->task_id = 1;
		$this->instance->time    = "1.0";
		$result = $this->instance->check();

		$this->assertTrue($result);
	}

	/**
	 * Test get record without id creates empty object
	 *
	 * @group com_time
	 */
	function testGetRecordWithoutIdCreatesNew()
	{
		$result = $this->instance->getRecord(0);

		$this->assertTrue(is_object($result));
	}
}