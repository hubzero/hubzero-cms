<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Query;

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
	 * Test to make sure we can run a basic delete statement
	 *
	 * @return  void
	 **/
	public function testBasicRemove()
	{
		$dbo   = $this->getMockDriver();
		$query = new Query($dbo);

		// Try to update an existing row
		$query->remove('users', 'id', 1);

		$this->assertEquals(3, $this->getConnection()->getRowCount('users'), 'Remove did not return the expected row count of 3');
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
}