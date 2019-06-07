<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Tests;

use Hubzero\Test\Database;
use Components\Blog\Models\Entry;

require_once dirname(__DIR__) . '/models/entry.php';

/**
 * Entry model test
 */
class EntryTest extends Database
{
	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 */
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());
	}

	/**
	 * Tests object construction and variable initialization
	 *
	 * @covers  Components\Blog\Models\Entry::blank
	 * @return  void
	 **/
	public function testConstruct()
	{
		$model = Entry::blank();

		$this->assertInstanceOf('\Hubzero\Database\Relational', $model, 'Model is not an instance of \Hubzero\Database\Relational');
		$this->assertEquals($model->getModelName(), 'Entry', 'Model should have a model name of "Entry"');
	}

	/**
	 * Tests to make sure that a result can be retrieved
	 *
	 * @covers  Components\Blog\Models\Entry::one
	 * @return  void
	 **/
	public function testOneReturnsResult()
	{
		$this->assertEquals(1, Entry::one(1)->id, 'Model should have returned an instance with ID of 1');
	}

	/**
	 * Tests that a call for a non-existent row via oneOrFail method throws an exception
	 *
	 * @covers  Components\Blog\Models\Entry::oneOrFail
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testOneOrFailThrowsException()
	{
		Entry::oneOrFail(0);
	}

	/**
	 * Tests that a request for a non-existent row via oneOrNew method returns new model
	 *
	 * @covers  Components\Blog\Models\Entry::oneOrNew
	 * @return  void
	 **/
	public function testOneOrNewCreatesNew()
	{
		$this->assertTrue(Entry::oneOrNew(0)->isNew(), 'Model should have stated that it was new');
	}

	/**
	 * Tests that a oneToMany relationship properly grabs the many side of the relationship
	 *
	 * @covers  Components\Blog\Models\Entry::comments
	 * @return  void
	 **/
	public function testCommentsRelationship()
	{
		$this->assertCount(2, Entry::oneOrFail(1)->comments, 'Model should have returned a count of 2 comments for entry 1');
	}

	/**
	 * Test that scoped entries are returned correctly
	 *
	 * @covers  Components\Blog\Models\Entry::oneByScope
	 * @return  void
	 */
	public function testGetScopedEntry()
	{
		$scopes = array(
			array(
				'alias' => 'bibendum',
				'scope' => 'site',
				'scope_id' => 0
			),
			array(
				'alias' => 'commodo',
				'scope' => 'group',
				'scope_id' => 1001
			),
			array(
				'alias' => 'fringilla',
				'scope' => 'member',
				'scope_id' => 1001
			),
		);

		foreach ($scopes as $item)
		{
			$entry = Entry::oneByScope($item['alias'], $item['scope'], $item['scope_id']);

			$this->assertTrue(is_object($entry));
			$this->assertEquals($entry->get('alias'), $item['alias']);
			$this->assertEquals($entry->get('scope'), $item['scope']);
			$this->assertEquals($entry->get('scope_id'), $item['scope_id']);
		}
	}

	/**
	 * Test that aliases are built correctly
	 *
	 * @covers  Components\Blog\Models\Entry::automaticAlias
	 * @return  void
	 */
	public function testAutomaticAlias()
	{
		$entry = Entry::blank();

		$title = 'Sed posuere consectetur est at lobortis!';
		$alias = 'sed-posuere-consectetur-est-at-lobortis';

		$data = array('title' => $title);
		$data['alias'] = $entry->automaticAlias($data);

		$this->assertEquals($alias, $data['alias']);

		$title = 'Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Vestibulum id ligula porta felis euismod semper. Curabitur blandit tempus porttitor.';
		//$alias = 'aenean-eu-leo-quam-pellentesque-ornare-sem-lacinia-quam-venenatis-vestibulum-vestibulum-id-ligula-porta';

		$data = array('title' => $title);
		$data['alias'] = $entry->automaticAlias($data);

		$this->assertTrue((strlen($data['alias']) <= 100), 'Alias should not be longer than 100 characters');
		$this->assertFalse(strstr($data['alias'], '.'), 'Alias should not contain any punctuation other than dashes.');
		$this->assertFalse((substr($data['alias'], 0, 1) === 'A'), 'Alias should be all lowercase.');

		$data = array('alias' => $title);
		$data['alias'] = $entry->automaticAlias($data);

		$this->assertTrue((strlen($data['alias']) <= 100), 'Alias should not be longer than 100 characters');
		$this->assertFalse(strstr($data['alias'], '.'), 'Alias should not contain any punctuation other than dashes.');
		$this->assertFalse((substr($data['alias'], 0, 1) === 'A'), 'Alias should be all lowercase.');
	}

	/**
	 * Test publish_up timestamp is set correctly
	 *
	 * @covers  Components\Blog\Models\Entry::automaticPublishUp
	 * @return  void
	 */
	public function testAutomaticPublishUp()
	{
		// Test that publish_up is set to 'now' if using a non-existing record
		$entry = Entry::blank();

		$dt = new \Hubzero\Utility\Date('now');
		$now = $dt->toSql();

		$data = array('id' => 0);
		$data['publish_up'] = $entry->automaticPublishUp($data);

		$this->assertNotEquals('0000-00-00 00:00:00', $data['publish_up']);
		$this->assertNotEquals(null, $data['publish_up']);
		$this->assertTrue($data['publish_up'] >= $now);

		// Test that publish_up is set to created date if using an existing record
		$entry = Entry::one(1);

		$data = array('id' => $entry->get('id'));
		$data['publish_up'] = $entry->automaticPublishUp($data);

		$this->assertEquals($entry->get('created'), $data['publish_up']);

		// Test that given set values are left untouched
		$data = array(
			'id' => $entry->get('id'),
			'publish_up' => '2017-03-13 15:11:00'
		);

		$result = $entry->automaticPublishUp($data);

		$this->assertEquals($data['publish_up'], $result);
	}
}
