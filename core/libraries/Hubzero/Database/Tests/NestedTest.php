<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Tests\Mock\Discussion;

/**
 * Nested relational model tests
 */
class NestedTest extends Database
{
	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 **/
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());
	}

	/**
	 * Tests object construction and variable initialization
	 *
	 * @return  void
	 **/
	public function testConstruct()
	{
		$model = Discussion::blank();

		$this->assertInstanceOf('\Hubzero\Database\Nested', $model, 'Model is not an instance of \Hubzero\Database\Nested');
		$this->assertEquals($model->getModelName(), 'Discussion', 'Model should have a model name of "Discussion"');
	}

	/**
	 * Tests to make sure we can add a new generic child node
	 *
	 * @return  void
	 **/
	public function testCanAddNewChildNodeFromParentModel()
	{
		$parent = Discussion::oneOrFail(1);

		$new = Discussion::blank()->set([
			'user_id' => 3,
			'title'   => 'Testing Stuff',
			'content' => 'This is a test additional child node'
		]);

		$new->saveAsChildOf($parent);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsWithNewChildNode.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Tests to make sure we can add a new generic child node
	 *
	 * @return  void
	 **/
	public function testCanAddNewChildNodeFromParentId()
	{
		$new = Discussion::blank()->set([
			'user_id' => 3,
			'title'   => 'More Testing',
			'content' => 'This is a node added by parent id'
		]);

		$new->saveAsChildOf(3);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsWithNewChildNodeByParentId.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Tests to make sure we can add a new first child node
	 *
	 * @return  void
	 **/
	public function testCanAddNewFirstChildNode()
	{
		$new = Discussion::blank()->set([
			'user_id' => 3,
			'title'   => 'Left node',
			'content' => 'This should be located as the first child of the parent'
		]);

		$new->saveAsFirstChildOf(1);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsWithNewFirstChild.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Tests to see if we can add a new root node
	 *
	 * @return  void
	 **/
	public function testCanAddNewRootNode()
	{
		Discussion::blank()->set([
			'user_id'  => 3,
			'title'    => 'This is a new discussion',
			'content'  => 'Tell me about life',
			'scope'    => 'group',
			'scope_id' => 2
		])->saveAsRoot();

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsWithNewRootNode.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Tests to see if we can get the children of a given parent
	 *
	 * @return  void
	 **/
	public function testCanGetChildren()
	{
		// Clear that cache here from our earlier request for the same model
		\Hubzero\Database\Query::purgeCache();

		$discussion = Discussion::oneOrFail(1);
		$children   = $discussion->getChildren()->raw();

		$this->assertCount(3, $children, 'Discussion 1 should have had 3 children');

		foreach ([2, 3, 5] as $expected)
		{
			$this->assertArrayHasKey($expected, $children, "Expected a discussion with id {$expected}");
		}
	}

	/**
	 * Tests to see if we can get all the descendants of a given parent
	 *
	 * @return  void
	 **/
	public function testCanGetDescendants()
	{
		$discussion  = Discussion::oneOrFail(1);
		$descendants = $discussion->getDescendants()->raw();

		$this->assertCount(4, $descendants, 'Discussion 1 should have had 4 descendants');

		foreach ([2, 3, 4, 5] as $expected)
		{
			$this->assertArrayHasKey($expected, $descendants, "Expected a discussion with id {$expected}");
		}
	}

	/**
	 * Tests to see if we can get a limited set of the descendants of a given parent
	 *
	 * @return  void
	 **/
	public function testCanGetLimitedDescendants()
	{
		$discussion  = Discussion::oneOrFail(1);
		$descendants = $discussion->descendants()->limit(2)->rows()->raw();

		foreach ([2, 5] as $expected)
		{
			$this->assertArrayHasKey($expected, $descendants, "Expected a discussion with id {$expected}");
		}
	}

	/**
	 * Tests to make sure we can delete a low/bottom level child node (i.e. with no children)
	 *
	 * @return  void
	 **/
	public function testCanDeleteLeafNode()
	{
		$discussion = Discussion::oneOrFail(5)->destroy();

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsAfterDelete.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Tests to make sure we can delete a parent node and cascade the delete as appropriate
	 *
	 * @return  void
	 **/
	public function testCanDeleteParentNode()
	{
		$discussion = Discussion::oneOrFail(3)->destroy();

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'discussions', 'SELECT * FROM discussions'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedDiscussionsAfterDeleteParent.xml')
		                      ->getTable('discussions');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}
}
