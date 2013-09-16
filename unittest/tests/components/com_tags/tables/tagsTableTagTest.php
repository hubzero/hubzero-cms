<?php
/**
 * Test class for the time records table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_tags' . DS . 'tables' . DS . 'tag.php';

class TagsTableTagTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'tag', 
		'raw_tag', 
		'description', 
		'admin'
	);
	var $mock       = array(
		'id'          => null,
		'tag'         => 'evil',
		'raw_tag'     => 'Evil!',
		'description' => 'unittest',
		'admin'       => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new TagsTableTag($db);
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
	 * Test that instance is an instance of TagsTableTag
	 *
	 * @group com_tags
	 */
	function testIsInstanceOfTagsTableTag()
	{
		$this->assertTrue($this->instance instanceof TagsTableTag);
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
	 * @covers TagsTableTag::getCount
	 */
	function testGetCountIsNumeric()
	{
		$result = $this->instance->getCount();
		$this->assertTrue(is_numeric($result), "Tags Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_tags
	 * @covers TagsTableTag::getCount
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters = array(
			'scope_id' => 1000,
			'search'   => 'Evi'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Tags Count: $result");
	}

	/**
	 * Test that getRecords
	 * 
	 * @group com_tags
	 * @covers TagsTableTag::getRecords
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
	 * @group com_tags
	 */
	function testTagHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'TagsTableTag');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_tags
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
	 * Test record read
	 *
	 * @group com_tags
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
	 */
	function testRecordSaveFailsWithNoRawTag()
	{
		$mock = $this->mock;
		$mock['raw_tag'] = '';
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record check passes
	 *
	 * @group com_tags
	 * @covers TagsTableTag::check
	 */
	function testRecordCheck()
	{
		$this->instance->raw_tag = 'Evil!';
		$result = $this->instance->check();

		$this->assertTrue($result);

		return $this->instance;
	}

	/**
	 * Test that the tag is a string
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableTag::check
	 */
	function testTagIsString($instance)
	{
		$this->assertTrue(is_string($instance->tag), "Tag is string");
	}

	/**
	 * Test that the tag is lowercase
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableTag::check
	 */
	function testTagIsLowercase($instance)
	{
		$result = $this->instance->tag;
		$this->assertTrue((strtolower($instance->tag) == $instance->tag), "Tag is lowercase");
	}

	/**
	 * Test that the tag contains no punctuation
	 *
	 * @group com_tags
	 * @depends testRecordCheck
	 * @covers TagsTableTag::check
	 */
	function testTagHasNoPunctuation($instance)
	{
		$res = true;
		if (preg_match('/[^a-zA-Z0-9]/', $instance->tag))
		{
			$res = false;
		}
		$this->assertFalse($res, "Tag does not contain punctuation");
	}
}