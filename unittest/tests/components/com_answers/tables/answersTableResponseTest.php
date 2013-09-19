<?php
/**
 * Test class for the question response table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . '/administrator/components/com_answers/tables/response.php';

class AnswersTableResponseTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'qid', 
		'answer', 
		'created', 
		'created_by',
		'helpful',
		'nothelpful',
		'state',
		'anonymous'
	);
	var $mock       = array(
		'id'         => null, 
		'qid'        => 1, 
		'answer'     => 'The anser is 42', 
		'created'    => '2013-09-21 12:56:41', 
		'created_by' => 'admin',
		'helpful'    => 0,
		'nothelpful' => 0,
		'state'      => 0,
		'anonymous'  => 0
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new AnswersTableResponse($db);
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
	 * @group com_answers
	 */
	function testInstanceIsObject()
	{
		$this->assertType('object', $this->instance);
	}

	/**
	 * Test that instance is an instance of AnswersTableResponse
	 *
	 * @group com_answers
	 */
	function testIsInstanceOfAnswersTableResponse()
	{
		$this->assertTrue($this->instance instanceof AnswersTableResponse);
	}

	/**
	 * Test that instance extends JTable
	 *
	 * @group com_answers
	 */
	function testExtendsJTable()
	{
		$this->assertTrue($this->instance instanceof JTable);
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableResponse::getCount
	 */
	function testGetCountIsNumeric()
	{
		$result = $this->instance->getCount();
		$this->assertTrue(is_numeric($result), "Response Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_answers
	 * @covers AnswersTableResponse::getCount
	 */
	function testGetCountWithFiltersIsNumeric()
	{
		$filters = array(
			'qid' => 1
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Response Count: $result");
	}

	/**
	 * Test that getRecords
	 * 
	 * @group com_answers
	 * @covers AnswersTableResponse::getResults
	 */
	function testGetResultsIsArray()
	{
		$filters = array();
		$filters['start'] = 0;
		$filters['limit'] = 10;
		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that record has specified attributes
	 *
	 * @group com_answers
	 */
	function testRecordHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'AnswersTableResponse');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_answers
	 * @covers AnswersTableResponse::save
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
	 * @group com_answers
	 * @depends testRecordSave
	 * @covers AnswersTableResponse::load
	 */
	function testRecordLoad($id)
	{
		$result = $this->instance->load($id);
		$this->assertTrue(is_numeric($result->id));
	}

	/**
	 * Test record delete
	 *
	 * @group com_answers
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
	 * @group com_answers
	 * @covers AnswersTableResponse::check
	 */
	function testRecordCheck()
	{
		$this->instance->answer = 'The answer is 53';

		$this->assertTrue($this->instance->check());
	}

	/**
	 * Test that the tag is a string
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testAnswerIsString()
	{
		$this->assertTrue(is_string($this->instance->answer), "Answer is string");
	}

	/**
	 * Test that the tag is a string
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testQidIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->qid), "QID is numeric");
	}

	/**
	 * Test that helpful is numeric
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testHelpfulIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->helpful), "Helpful is numeric");
	}

	/**
	 * Test that nothelpful is numeric
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testNothelpfulIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->helpful), "Helpful is numeric");
	}

	/**
	 * Test that anonymous is numeric
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testAnonymousIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->anonymous), "Anonymous is numeric");
	}

	/**
	 * Test that created is set
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testCreatedIsSet()
	{
		$this->assertTrue(($this->instance->created != ''), "Created is set");
	}

	/**
	 * Test that created_by is set
	 *
	 * @group com_answers
	 * @depends testRecordCheck
	 * @covers AnswersTableResponse::check
	 */
	function testCreatedbyIsSet()
	{
		$this->assertTrue(($this->instance->created_by != 0), "Created_by is set");
	}
}