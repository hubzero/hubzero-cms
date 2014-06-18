<?php
/**
 * Test class for the Knowledge Base comment table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'comment.php';

class KbTableCommentTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'entry_id',
		'content',
		'created',
		'created_by',
		'anonymous',
		'parent',
		'asset_id',
		'helpful',
		'nothelpful'
	);
	var $mock       = array(
		'id'         => null,
		'entry_id'   => 1,
		'content'    => 'Nullam quis risus eget urna mollis ornare vel eu leo.',
		'created'    => '2013-05-06 12:13:04',
		'created_by' => 123,
		'anonymous'  => 0,
		'parent'     => 0,
		'asset_id'   => 0,
		'helpful'    => 3,
		'nothelpful' => 1
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new KbTableComment($db);
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
	 * Test that instance is an instance of KbTableComment
	 *
	 * @group com_kb
	 */
	function testIsInstanceOfKbTableComment()
	{
		$this->assertTrue($this->instance instanceof KbTableComment);
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
	 * Test that record has specified attributes
	 *
	 * @group com_kb
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'KbTableComment');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_kb
	 * @covers KbTableComment::save
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
	 * @group com_kb
	 * @covers KbTableComment::check
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
	 * Test that entry ID is numeric
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableComment::check
	 */
	function testEntryIdIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->entry_id), "Entry ID is numeric");
	}

	/**
	 * Test that content is a string
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableComment::check
	 */
	function testContentIsString($instance)
	{
		$this->assertTrue(is_string($instance->content), "Content is string");
	}

	/**
	 * Test that parent ID is numeric
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableComment::check
	 */
	function testParentIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->parent), "Parent is numeric");
	}

	/**
	 * Test record read
	 *
	 * @group com_kb
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
	 * @group com_kb
	 * @depends testRecordSave
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record save fails when content is provided
	 *
	 * @group com_kb
	 * @covers KbTableComment::check
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
	 * Test record save fails when entry ID is provided
	 *
	 * @group com_kb
	 * @covers KbTableComment::check
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