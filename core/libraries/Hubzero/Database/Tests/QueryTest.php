<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Query;
use Hubzero\Database\Value\Basic;
use Hubzero\Database\Value\Raw;

/**
 * Base query tests
 */
class QueryTest extends Database
{
	/**
	 * Test to make sure we can run a basic select statement
	 *
	 * @return  void
	 **/
	public function testBasicFetch()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to actually fetch some rows
		$rows = $query->select('*')
		              ->from('users')
		              ->whereEquals('id', '1')
		              ->fetch();

		// Basically, as long as we don't get false here, we're good
		$this->assertCount(1, $rows, 'Query should have returned one result');
	}

	/**
	 * Test to make sure we can run a basic insert statement
	 *
	 * @return  void
	 **/
	public function testBasicPush()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to add a new row
		$query->push('users', [
			'name'  => 'new user',
			'email' => 'newuser@gmail.com'
		]);

		// There are 4 default users in the seed data, and adding a new one should a rowcount of 5
		$this->assertEquals(5, $this->getConnection()->getRowCount('users'), 'Push did not return the expected row count of 5');
	}

	/**
	 * Test to make sure we can run a basic update statement
	 *
	 * @return  void
	 **/
	public function testBasicAlter()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->alter('users', 'id', 1, [
			'name'  => 'Updated User',
			'email' => 'updateduser@gmail.com'
		]);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'users', 'SELECT * FROM users'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedUsers.xml')
		                      ->getTable('users');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Test to make sure we can set a basic value on update
	 *
	 * @return  void
	 **/
	public function testSetWithBasicValue()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->alter('users', 'id', 1, [
			'name'  => new Basic('Updated User'),
			'email' => new Basic('updateduser@gmail.com')
		]);

		// Get the current state of the database
		$queryTable = $this->getConnection()->createQueryTable(
		    'users', 'SELECT * FROM users'
		);

		// Get our expected state of the database
		$expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . DS . 'Fixtures' . DS . 'updatedUsers.xml')
		                      ->getTable('users');

		// Now assert that updated and expected are the same
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	/**
	 * Test to make sure we can run a basic delete statement
	 *
	 * @return  void
	 **/
	public function testBasicRemove()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$result = $query->remove('users', null, null);
		$this->assertFalse($result);

		// Try to update an existing row
		$query->remove('users', 'id', 1);

		$this->assertEquals(3, $this->getConnection()->getRowCount('users'), 'Remove did not return the expected row count of 3');
	}

	/**
	 * Test to make sure we can build a query with aliased from statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithAliasedFrom()
	{
		// Here's the query we're trying to write...
		$expected = "SELECT * FROM `users` AS `u` WHERE `u`.`name` = 'awesome'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users', 'u')
		      ->whereEquals('u.name', 'awesome');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with a raw WHERE statement
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithRawWhere()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE LOWER(`name`)='awesome'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereRaw('LOWER(`name`)=?', ['awesome']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` = 'foo' OR LOWER(`name`)='awesome'";

		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'foo')
		      ->orWhereRaw('LOWER(`name`)=?', ['awesome']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where like statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereLike()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` LIKE '%awesome%'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereLike('name', 'awesome');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` LIKE '%awesome%' OR `name` LIKE '%amazing%'";

		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereLike('name', 'awesome')
		      ->orWhereLike('name', 'amazing');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereIsNull()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` IS NULL";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIsNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` = '' OR `name` IS NULL";

		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', '')
		      ->orWhereIsNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereIsNotNull()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` IS NOT NULL";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIsNotNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` = 'bar' OR `name` IS NOT NULL";

		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'bar')
		      ->orWhereIsNotNull('name');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithWhereIn()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE `name` IN ('one','two','three')";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereIn('name', ['one', 'two', 'three']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` = 'amazing' OR `name` IN ('one','two','three')";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'amazing')
		      ->orWhereIn('name', ['one', 'two', 'three']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` NOT IN ('one','two','three')";

		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereNotIn('name', ['one', 'two', 'three']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$expected = "SELECT * FROM `users` WHERE `name` = 'amazing' OR `name` NOT IN ('one','two','three')";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'amazing')
		      ->orWhereNotIn('name', ['one', 'two', 'three']);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with complex nested where statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithNestedWheres()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` WHERE (`name` = 'a' OR `name` = 'b' ) AND (`email` = 'c' OR `email` = 'd' )";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->whereEquals('name', 'a', 1)
		      ->orWhereEquals('name', 'b', 1)
		      ->resetDepth(0)
		      ->whereEquals('email', 'c', 1)
		      ->orWhereEquals('email', 'd', 1);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with a raw JOIN statement
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithJoinClause()
	{
		$dbo = $this->getMockDriver();

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` INNER JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->join('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'join Query did not build the expected result');

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->innerJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'innerJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` LEFT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->leftJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'leftJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` RIGHT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);

		if ($dbo->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
		{
			$this->setExpectedException('\Hubzero\Database\Exception\UnsupportedSyntaxException');

			$query->select('*')
			      ->from('users')
			      ->rightJoin('posts', '`users`.id', '`posts`.user_id');
		}
		else
		{
			$query->select('*')
			      ->from('users')
			      ->rightJoin('posts', '`users`.id', '`posts`.user_id');

			$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'rightJoin Query did not build the expected result');
		}

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` RIGHT JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);
		$query->select('*')
		      ->from('users')
		      ->rightJoin('posts', '`users`.id', '`posts`.user_id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'rightJoin Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` FULL JOIN posts ON `users`.id = `posts`.user_id";

		$query = new Query($dbo);

		if ($dbo->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
		{
			$this->setExpectedException('\Hubzero\Database\Exception\UnsupportedSyntaxException');

			$query->select('*')
			      ->from('users')
			      ->fullJoin('posts', '`users`.id', '`posts`.user_id');
		}
		else
		{
			$query->select('*')
			      ->from('users')
			      ->fullJoin('posts', '`users`.id', '`posts`.user_id');

			$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'fullJoin Query did not build the expected result');
		}
	}

	/**
	 * Test to make sure we can build a query with a raw JOIN statement
	 *
	 * @return  void
	 **/
	public function testBuildQueryWithRawJoinClause()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `users` INNER JOIN posts ON `users`.id = `posts`.user_id AND `users`.id > 1";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->joinRaw('posts', '`users`.id = `posts`.user_id AND `users`.id > 1');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build an INSERT statement
	 *
	 * @return  void
	 **/
	public function testBuildInsert()
	{
		// Here's the query we're trying to write...
		$expected = "INSERT INTO `users` (`name`) VALUES ('danger')";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->insert('users')
			->values(array(
				'name' => 'danger'
			));

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		if ($dbo->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
		{
			$expected = "INSERT OR IGNORE INTO `users` (`name`) VALUES ('awesome')";
		}
		else
		{
			$expected = "INSERT IGNORE INTO `users` (`name`) VALUES ('awesome')";
		}

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->insert('users', true)
			->values(array(
				'name' => 'awesome'
			));

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure that fetch properly caches a query
	 *
	 * @return  void
	 **/
	public function testFetchCachesQueriesByDefault()
	{
		// Mock a database driver
		$dbo = $this->getMockDriver();

		// Mock the query builder and tell it we only want to override the query method
		$query = $this->getMockBuilder('Hubzero\Database\Query')
		              ->setConstructorArgs([$dbo])
		              ->setMethods(['query'])
		              ->getMock();

		// Now set that we should only be calling the query method one time
		// We also tell it to return something from the query method, otherwise
		// the cache will fail.
		$query->expects($this->once())
		      ->method('query')
		      ->willReturn('foo');

		// The query itself here is irrelavent, we just need to make sure
		// that calling the same query twice doesn't hit the driver twice
		$query->fetch();
		$query->fetch();
	}

	/**
	 * Test to make sure that fetch properly caches a query
	 *
	 * @return  void
	 **/
	public function testFetchDoesNotCacheQueries()
	{
		// Mock a database driver
		$dbo = $this->getMockDriver();

		// Mock the query builder and tell it we only want to override the query method
		$query = $this->getMockBuilder('Hubzero\Database\Query')
		              ->setConstructorArgs([$dbo])
		              ->setMethods(['query'])
		              ->getMock();

		// Now set that we should be calling the query exactly 2 times
		// We also tell it to return something from the query method, otherwise
		// the cache will fail and we could get a false positive.
		$query->expects($this->exactly(2))
		      ->method('query')
		      ->willReturn('foo');

		// The query itself here is irrelavent, we just need to make sure
		// that calling fetch results in a call to the query method.
		// We call it twice to ensure that the result is in the cache.
		// If the result were not in the cache, we could get a false positive.
		$query->fetch('rows', true);
		$query->fetch('rows', true);
	}

	/**
	 * Test to make sure we can build a query with where IS NULL statements
	 *
	 * @return  void
	 **/
	public function testBuildQueryClear()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `groups`";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('users')
		      ->join('groups', 'created_by', 'id', 'inner')
		      ->whereIsNotNull('name')
		      ->group('cn', 'id')
		      ->having('foo', '=', 3)
		      ->clear('from')
		      ->clear('where')
		      ->clear('join')
		      ->clear('group')
		      ->clear('having')
		      ->from('groups');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "SELECT id FROM `users` WHERE `name` IS NOT NULL";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('name')
		      ->from('users')
		      ->whereIsNotNull('name')
		      ->deselect()
		      ->select('id');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "INSERT INTO `users` (`name`) VALUES ('Jimmy')";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->insert('groups')
		      ->values(array('cn' => 'lorem'))
		      ->clear('insert')
		      ->clear('values')
		      ->insert('users')
		      ->values(array('name' => 'Jimmy'));

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "DELETE FROM `users` WHERE `name` = 'Frank'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->delete('groups')
		      ->whereEquals('cn', 'lorem')
		      ->clear('delete')
		      ->clear('where')
		      ->delete('users')
		      ->whereEquals('name', 'Frank');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// Here's the query we're try to write...
		$expected = "UPDATE `users` SET `name` = 'Frank' WHERE `id` = '2'";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->update('groups')
		      ->set(array('cn' => 'lorem'))
		      ->whereEquals('cn', 'lorem')
		      ->clear('update')
		      ->clear('where')
		      ->update('users')
		      ->set(array('name' => 'Frank'))
		      ->whereEquals('id', 2);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test to make sure we can build a query with LIMIT
	 *
	 * @return  void
	 **/
	public function testBuildQueryLimitStart()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `groups` LIMIT 10";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('groups')
		      ->limit(10);

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$query->start(5);

		$expected = "SELECT * FROM `groups` LIMIT 5,10";

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');
	}

	/**
	 * Test that START is an integer
	 *
	 * @return  void
	 **/
	public function testNonNumericStart()
	{
		$dbo = $this->getMockDriver();

		$query = new Query($dbo);
		$query->select('*')
		      ->from('groups')
		      ->start('beginning');

		$expected = "SELECT * FROM `groups`";

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// NOTE: We directly test the Syntax class as the `start()` method on
		//       the Query class casts values as integers.
		$syntax = '\\Hubzero\\Database\\Syntax\\' . ucfirst($dbo->getSyntax());
		$syntax = new $syntax($dbo);

		$this->setExpectedException('InvalidArgumentException');

		$syntax->setStart('beginning');
	}

	/**
	 * Test that START is greater than or equal to zero
	 *
	 * @return  void
	 **/
	public function testNegativeStart()
	{
		$dbo = $this->getMockDriver();

		$query = new Query($dbo);

		$this->setExpectedException('InvalidArgumentException');

		$query->select('*')
		      ->from('groups')
		      ->start(-50);

		/*
		$syntax = '\\Hubzero\\Database\\Syntax\\' . ucfirst($dbo->getSyntax());
		$syntax = new $syntax($dbo);

		$this->setExpectedException('InvalidArgumentException');

		$syntax->setStart(-50);
		*/
	}

	/**
	 * Test that LIMIT is an integer
	 *
	 * @return  void
	 **/
	public function testNonNumericLimit()
	{
		$dbo   = $this->getMockDriver();

		$query = new Query($dbo);
		$query->select('*')
		      ->from('groups')
		      ->limit('all');

		$expected = "SELECT * FROM `groups`";

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// NOTE: We directly test the Syntax class as the `limit()` method on
		//       the Query class casts values as integers.
		$syntax = '\\Hubzero\\Database\\Syntax\\' . ucfirst($dbo->getSyntax());
		$syntax = new $syntax($dbo);

		$this->setExpectedException('InvalidArgumentException');

		$syntax->setLimit('all');
	}

	/**
	 * Test that LIMIT is greater than or equal to zero
	 *
	 * @return  void
	 **/
	public function testNegativeLimit()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$this->setExpectedException('InvalidArgumentException');

		$query->select('*')
		      ->from('groups')
		      ->limit(-50);
	}

	/**
	 * Test to make sure we can build a query with ORDER BY
	 *
	 * @return  void
	 **/
	public function testBuildQueryOrder()
	{
		// Here's the query we're try to write...
		$expected = "SELECT * FROM `groups` ORDER BY `id` ASC";

		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		$query->select('*')
		      ->from('groups')
		      ->order('id', 'asc');

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		$query->unorder()
			->order('name', 'desc');

		$expected = "SELECT * FROM `groups` ORDER BY `name` DESC";

		$this->assertEquals($expected, str_replace("\n", ' ', $query->toString()), 'Query did not build the expected result');

		// Test that an exception is thrown if order is not asc or desc
		$this->setExpectedException('InvalidArgumentException');

		$query = new Query($dbo);
		$query->select('*')
		      ->from('groups')
		      ->order('id', 'foo');
	}
}
