<?php
/**
 * Test class for the questions table class
 * 
 * @author Shawn Rice <zooley@purdue.edu>
 * @runTestsInSeparateProcesses
 */

// Include time records table
jimport('joomla.database.table');

require_once JPATH_BASE . '/administrator/components/com_answers/tables/question.php';

class AnswersTableQuestionTest extends PHPUnit_Framework_TestCase
{
	var $instance   = null;
	var $attributes = array(
		'id', 
		'subject', 
		'question', 
		'created', 
		'created_by',
		'state',
		'anonymous',
		'email',
		'helpful',
		'reward'
	);
	var $mock       = array(
		'id'         => null,
		'subject'    => 'What is the meaning of life?',
		'question'   => 'What is the meaning of life?',
		'created'    => '2013-05-06 12:13:04',
		'created_by' => 'admin',
		'state'      => 0,
		'anonymous'  => 0,
		'email'      => 0,
		'helpful'    => 3,
		'reward'     => 100
	);

	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::siteSetup();
		$db = PHPUnitTestHelper::getDBO();
		$this->instance = new AnswersTableQuestion($db);
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
		$this->assertTrue(is_object($this->instance));
	}

	/**
	 * Test that instance is an instance of AnswersTableQuestion
	 *
	 * @group com_answers
	 */
	function testIsInstanceOfTagsTableObject()
	{
		$this->assertTrue($this->instance instanceof AnswersTableQuestion);
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
	 * @covers AnswersTableQuestion::getCount
	 */
	function testCountIsNumeric()
	{
		$result = $this->instance->count();
		$this->assertTrue(is_numeric($result), "Question Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getCount
	 */
	function testCountWithFilterbyMineIsNumeric()
	{
		$filters = array(
			'filterby' => 'mine'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Question Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getCount
	 */
	function testCountWithFilterbyAllIsNumeric()
	{
		$filters = array(
			'filterby' => 'all'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Question Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getCount
	 */
	function testCountWithFilterbyOpenIsNumeric()
	{
		$filters = array(
			'filterby' => 'open'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Question Count: $result");
	}

	/**
	 * Test that getCount with filters returns number
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getCount
	 */
	function testCountWithFilterbyClosedIsNumeric()
	{
		$filters = array(
			'filterby' => 'closed'
		);

		$result = $this->instance->getCount($filters);
		$this->assertTrue(is_numeric($result), "Question Count: $result");
	}

	/**
	 * Test that getRecords
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getResults
	 */
	function testGetResultsIsArray()
	{
		$filters = array(
			'start' => 0,
			'limit' => 10
		);

		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getRecords filtered by 'all' is an array
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getResults
	 */
	function testGetResultsWithFilterbyAllIsArray()
	{
		$filters = array(
			'start' => 0,
			'limit' => 10,
			'filterby' => 'all'
		);

		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getRecords filtered by 'mine' is an array
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getResults
	 */
	function testGetResultsWithFilterbyMineIsArray()
	{
		$filters = array(
			'start' => 0,
			'limit' => 10,
			'filterby' => 'mine'
		);

		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getRecords filtered by 'open' is an array
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getResults
	 */
	function testGetResultsWithFilterbyOpenIsArray()
	{
		$filters = array(
			'start' => 0,
			'limit' => 10,
			'filterby' => 'open'
		);

		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that getRecords filtered by 'closed' is an array
	 * 
	 * @group com_answers
	 * @covers AnswersTableQuestion::getResults
	 */
	function testGetResultsWithFilterbyClosedIsArray()
	{
		$filters = array(
			'start' => 0,
			'limit' => 10,
			'filterby' => 'closed'
		);

		$result = $this->instance->getResults($filters);
		$this->assertTrue(is_array($result));
	}

	/**
	 * Test that record has specified attributes
	 *
	 * @group com_answers
	 */
	function testObjectHasAttributes()
	{
		foreach ($this->attributes as $a)
		{
			$this->assertClassHasAttribute($a, 'AnswersTableQuestion');
		}
	}

	/**
	 * Test record save
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::save
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
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testQuestionIsString()
	{
		$this->assertTrue(is_string($this->instance->question), "Question is string");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testStateIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->state), "State is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testAnonymousIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->anonymous), "Anonymous is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testHelpfulIsNumeric()
	{
		$this->assertTrue(is_numeric($this->instance->helpful), "Helpful is numeric");
	}

	/**
	 * Test record read
	 *
	 * @group com_answers
	 * @depends testRecordSave
	 * @covers AnswersTableQuestion::load
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
	 * Test record check
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testRecordCheck()
	{
		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertTrue($result);
	}

	/**
	 * Test record check fails when no subject
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testRecordCheckFailsWithNoSubject()
	{
		$mock = $this->mock;
		$mock['subject'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertFalse($result);
	}

	/**
	 * Test record check sets created_by
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testRecordCheckSetsCreatedby()
	{
		$mock = $this->mock;
		$mock['created_by'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertTrue(($this->instance->created_by != ''));
	}

	/**
	 * Test record check sets created
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testRecordCheckSetsCreated()
	{
		$mock = $this->mock;
		$mock['created'] = '';

		$this->instance->bind($mock);
		$result = $this->instance->check();

		$this->assertTrue(($this->instance->created != ''));
	}
}