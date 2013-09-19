<?php
/**
 * Test class for the blog comment table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . '/components/com_blog/tables/comment.php';

class BlogTableCommentTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'entry_id', 
		'content', 
		'created', 
		'created_by',
		'anonymous',
		'parent'
	);
	var $mock       = array(
		'id' => 0, 
		'entry_id' => 1, 
		'content' => 'My comment is amazing', 
		'created' => '', 
		'created_by' => 1001,
		'anonymous' => 0,
		'parent' => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new BlogTableComment($db);
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
	 * Test that instance is an instance of BlogTableComment
	 *
	 * @group com_blog
	 */
	function testIsInstanceOfBlogTableComment()
	{
		$this->assertTrue($this->instance instanceof BlogTableComment);
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
	 * Test that record has specified attributes
	 *
	 * @group com_blog
	 */
	function testRecordHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'BlogTableComment');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_blog
	 * @covers BlogTableComment::save
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
	 * @group com_blog
	 * @depends testRecordSave
	 * @covers BlogTableComment::load
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
	 */
	function testRecordDelete($id)
	{
		$result = $this->instance->delete($id);
		$this->assertTrue($result);
	}

	/**
	 * Test record check passes
	 *
	 * @group com_blog
	 * @covers BlogTableComment::check
	 */
	function testRecordCheck()
	{
		$this->instance->answer = 'The answer is 53';

		$this->assertTrue($this->instance->check());
	}

	/**
	 * Test that the tag is a string
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testContentIsString()
	{
		$this->assertTrue(is_string($this->instance->content), "Content is string");
	}

	/**
	 * Test that the tag is a string
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testEntryIdIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->entry_id), "Entry ID is numeric");
	}

	/**
	 * Test that helpful is numeric
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testParentIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->parent), "Parent is numeric");
	}

	/**
	 * Test that anonymous is numeric
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testAnonymousIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->anonymous), "Anonymous is numeric");
	}

	/**
	 * Test that created is set
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testCreatedIsSet()
	{
		$this->assertTrue(($this->instance->created != ''), "Created is set");
	}

	/**
	 * Test that nothelpful is numeric
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testCreatedByIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->created_by), "Created_by is numeric");
	}

	/**
	 * Test that created_by is set
	 *
	 * @group com_blog
	 * @depends testRecordCheck
	 * @covers BlogTableComment::check
	 */
	function testCreatedbyIsSet()
	{
		$this->assertTrue(($this->instance->created_by != 0), "Created_by is set");
	}
}