<?php
/**
 * Test class for the Knowledge Base category table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'tag.php';

class KbTableCategoryTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'title',
		'alias',
		'description',
		'section',
		'state',
		'access',
		'asset_id'
	);
	var $mock       = array(
		'id'           => null,
		'title'        => 'Hubzero Help',
		'alias'        => '',
		'description'  => 'Help for HUBzero installs.',
		'section'      => 0,
		'state'        => 1,
		'access'       => 0,
		'asset_id'     => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new KbTableCategory($db);
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
	 * @group com_kb
	 */
	function testInstanceIsObject()
	{
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of KbTableCategory
	 *
	 * @group com_kb
	 */
	function testIsInstanceOfKbTableCategory()
	{
		$this->assertTrue($this->instance instanceof KbTableCategory);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_kb
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test that getCategoriesCount returns number
	 *
	 * @group com_kb
	 * @covers KbTableCategory::getCategoriesCount
	 */
	function testGetCategoriesCountIsNumeric()
	{
		$result = $this->instance->getCategoriesCount();
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getCategoriesCount with filters returns number
	 *
	 * @group com_kb
	 * @covers KbTableCategory::getCategoriesCount
	 */
	function testGetCategoriesCountWithFiltersIsNumeric()
	{
		$filters = array(
			'section' => 0,
			'search'  => 'hub'
		);

		$result = $this->instance->getCategoriesCount($filters);
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getCategoriesAll
	 *
	 * @group com_kb
	 * @covers KbTableCategory::getCategoriesAll
	 */
	function testGetCategoriesAllIsArray()
	{
		$filters = array();
		$filters['start'] = 0;
		$filters['limit'] = 10;
		$result = $this->instance->getCategoriesAll($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that record has specified attributes
	 *
	 * @group com_kb
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'KbTableCategory');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_kb
	 * @covers KbTableCategory::save
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
	 * @group com_kb
	 * @depends testRecordSave
	 * @covers KbTableCategory::getRecord
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($this->instance->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_kb
	 * @depends testRecordSave
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record check fails when no time is provided
	 *
	 * @group com_kb
	 * @covers KbTableCategory::check
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
	 * Test record check passes
	 *
	 * @group com_kb
	 * @covers KbTableCategory::check
	 */
	function testRecordCheck()
	{
		$this->instance->title = 'Evil!';
		$result = $this->instance->check();

		$this->assertTrue($result);

		return $this->instance;
	}

	/**
	 * Test that the alias is a string
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableCategory::check
	 */
	function testAliasIsString($instance)
	{
		$this->assertTrue(is_string($instance->alias), "Alias is string");
	}

	/**
	 * Test that the alias is lowercase
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableCategory::check
	 */
	function testAliasIsLowercase($instance)
	{
		$this->assertTrue((strtolower($this->instance->alias) === $instance->alias), "Alias is lowercase");
	}

	/**
	 * Test that the alias contains no punctuation
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableCategory::check
	 */
	function testAliasHasNoPunctuation($instance)
	{
		$res = true;
		if (preg_match('/[^a-zA-Z0-9]/', $instance->alias))
		{
			$res = false;
		}
		$this->assertFalse($res, "Alias does not contain punctuation");
	}
}