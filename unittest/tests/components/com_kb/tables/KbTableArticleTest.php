<?php
/**
 * Test class for the Knowledge Base article table class
 *
 * @author Shawn Rice <zooley@purdue.edu>
 * @runInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . DS . 'components' . DS . 'com_kb' . DS . 'tables' . DS . 'article.php';

class KbTableArticleTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id',
		'title',
		'alias',
		'params',
		'fulltxt',
		'created',
		'created_by',
		'modified',
		'modified_by',
		'checked_out',
		'checked_out_time',
		'state',
		'access',
		'hits',
		'version',
		'section',
		'category',
		'helpful',
		'nothelpful'
	);
	var $mock       = array(
		'id'          => null,
		'title'       => 'Maecenas sed diam eget risus varius blandit sit amet non magna.',
		'alias'       => '',
		'params'      => '',
		'fulltxt'     => '<p>Nullam quis risus eget urna mollis ornare vel eu leo. Nullam id dolor id nibh ultricies vehicula ut id elit. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Maecenas sed diam eget risus varius blandit sit amet non magna. Cras mattis consectetur purus sit amet fermentum. Cras mattis consectetur purus sit amet fermentum. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

<p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Sed posuere consectetur est at lobortis. Cras mattis consectetur purus sit amet fermentum. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>',
		'created'     => '2013-05-06 12:13:04',
		'created_by'  => 123,
		'modified'    => '',
		'modified_by' => 0,
		'checked_out' => 0,
		'checked_out_time' => '',
		'state'       => 1,
		'access'      => 0,
		'hits'        => 0,
		'version'     => 0,
		'section'     => 1,
		'category'    => 0,
		'helpful'     => 0,
		'nothelpful'  => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new KbTableArticle($db);
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
	 * Test that instance is an instance of KbTableArticle
	 *
	 * @group com_kb
	 */
	function testIsInstanceOfKbTableArticle()
	{
		$this->assertTrue($this->instance instanceof KbTableArticle);
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
	 * Test that getCount returns number
	 *
	 * @group com_kb
	 * @covers KbTableArticle::getCount
	 */
	function testGetCountIsNumeric()
	{
		$result = $this->instance->getCount();
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 *
	 * @group com_kb
	 * @covers KbTableArticle::count
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters = array(
			'search' => 'fusce'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Record count: $result");
	}

	/**
	 * Test that getRecords returns an array
	 *
	 * @group com_kb
	 * @covers KbTableArticle::find
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
	 * @group com_kb
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'KbTableArticle');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_kb
	 * @covers KbTableArticle::save
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
	 * @covers KbTableArticle::check
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
	 * Test that section is numeric
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableArticle::check
	 */
	function testSectionIsNumeric($instance)
	{
		$this->assertTrue(is_numeric($instance->section), "Section is numeric");
	}

	/**
	 * Test that the title is a string
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableArticle::check
	 */
	function testTitleIsString($instance)
	{
		$this->assertTrue(is_string($instance->title), "Title is string");
	}

	/**
	 * Test that the alias is a string
	 *
	 * @group com_kb
	 * @depends testRecordCheck
	 * @covers KbTableArticle::check
	 */
	function testAliasIsString($instance)
	{
		$this->assertTrue(is_string($instance->alias), "Alias is string");
	}

	/**
	 * Test record read
	 *
	 * @group com_kb
	 * @depends testRecordSave
	 * @covers KbTableArticle::load
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
	 * Test record check fails when no title is provided
	 *
	 * @group com_kb
	 * @covers KbTableArticle::check
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
	 * Test record check fails when no section is provided
	 *
	 * @group com_kb
	 * @covers KbTableArticle::check
	 */
	function testRecordCheckFailsWithNoSection()
	{
		$mock = $this->mock;
		$mock['section'] = 0;

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}
}