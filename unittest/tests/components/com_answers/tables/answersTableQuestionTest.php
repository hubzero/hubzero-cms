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
		'created_by' => 1000,
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
		$this->assertType('object', $this->instance);
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
		$this->assertType('numeric', $result, "Question Count: $result");
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
		$this->assertType('numeric', $result, "Question Count: $result");
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
		$this->assertType('numeric', $result, "Question Count: $result");
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
		$this->assertType('numeric', $result, "Question Count: $result");
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
		$this->assertType('numeric', $result, "Question Count: $result");
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
		$result = $this->instance->save($this->mock);

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
		$this->assertType('string', $this->instance->question, "Question is string");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testStateIsNumeric()
	{
		$this->assertType('numeric', $this->instance->state, "State is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testAnonymousIsNumeric()
	{
		$this->assertType('numeric', $this->instance->anonymous, "Anonymous is numeric");
	}

	/**
	 * Test that getCount returns number
	 *
	 * @group com_answers
	 * @covers AnswersTableQuestion::check
	 */
	function testHelpfulIsNumeric()
	{
		$this->assertType('numeric', $this->instance->helpful, "Helpful is numeric");
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
	 * Test record save fails when no time is provided
	 *
	 * @group com_answers
	 */
	function testRecordSaveFailsWithNoQuestion()
	{
		$mock = $this->mock;
		$mock['question'] = '';
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}

	/**
	 * Test record save fails when no time is provided
	 *
	 * @group com_answers
	 */
	function testRecordSaveFailsWithNoCreatedby()
	{
		$mock = $this->mock;
		$mock['created_by'] = 0;
		$result = $this->instance->save($mock);

		$this->assertFalse($result);
	}
}