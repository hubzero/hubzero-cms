<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @return void
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
	 * @return void
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

		// There are two default users in the seed data, and adding a new one should a rowcount of 3
		$this->assertEquals(5, $this->getConnection()->getRowCount('users'), 'Push did not return the expected row count of 5');
	}

	/**
	 * Test to make sure we can run a basic update statement
	 *
	 * @return void
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
	 * @return void
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
	 * Test to make sure that fetch properly caches a query
	 *
	 * @return void
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
	 * @return void
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