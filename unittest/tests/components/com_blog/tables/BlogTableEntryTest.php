<?php
/**
 * Test class for the blog entry table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . '/components/com_blog/tables/entry.php';

class BlogTableEntryTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'title', 
		'alias', 
		'content',
		'created',
		'created_by',
		'state',
		'publish_up',
		'publish_down',
		'params',
		'group_id',
		'hits',
		'allow_comments',
		'scope'
	);
	var $mock       = array(
		'id' => 0, 
		'title' => 'My blog post', 
		'alias' => '', 
		'content' => 'My blog post is amazing!',
		'created' => '',
		'created_by' => 1000,
		'state' => 0,
		'publish_up' => '',
		'publish_down' => '',
		'params' => '',
		'group_id' => 0,
		'hits' => 0,
		'allow_comments' => 0,
		'scope' => 'site'
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new BlogTableEntry($db);
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
	 * @group com_blog
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance);
	}

	/**
	 * Test that instance is an instance of BlogTableEntry
	 *
	 * @group com_blog
	 */
	function testIsInstanceOfBlogTableEntry()
	{
		$this->assertTrue($this->instance instanceof BlogTableEntry);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_blog
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test record save
	 *
	 * @group com_blog
	 * @covers BlogTableEntry::save
	 */
	function testRecordSave()
	{
		$this->instance->bind($this->mock);
		$result = $this->instance->save();

		$this->assertTrue($result);

		// Save the object id for deletion later
		return $this->instance->id;
	}

	/**
	 * Test record read
	 *
	 * @group com_blog
	 * @depends testRecordSave
	 * @covers BlogTableEntry::load
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($result->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_blog
	 * @depends testRecordSave
	 * @covers BlogTableEntry::delete
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record check
	 *
	 * @group com_blog
	 * @covers BlogTableEntry::check
	 */
	function testRecordCheck()
	{
		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertTrue($result);
	}

	/**
	 * Test record check fails when no rid (response ID)
	 *
	 * @group com_blog
	 * @covers BlogTableEntry::check
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
	 * Test record check fails with invalid helpful value
	 *
	 * @group com_blog
	 * @covers BlogTableEntry::check
	 */
	function testRecordCheckFailsWithNoScope()
	{
		$mock = $this->mock;
		$mock['scope'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}