<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\BackendRegistry;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Expression;
use Hubzero\Database\Exception\UnsupportedSyntaxException;
use Hubzero\Database\Value\Basic;
use Hubzero\Database\Value\Raw;

/**
 * Base query tests
 */
class QueryTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['posts', 'groups', 'users'];
    }

    /**
     * Setup test tables with data
     *
     * @param Driver $driver
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $schema = $driver->schema();
        $query = new Query($driver);

        // Commit any pending transactions before DDL operations
        try {
            $connection = $driver->getConnection();
            if ($connection && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            // Ignore - transaction may already be committed
        }

        // Check if tables exist - if so, clear data instead of drop/recreate
        $usersExists = $driver->tableExists('users');
        $groupsExists = $driver->tableExists('groups');
        $postsExists = $driver->tableExists('posts');

        // If all tables exist, just clear data (no DDL = no blocking)
        if ($usersExists && $groupsExists && $postsExists) {
            // Clear existing data
            try {
                $query->delete('posts')->execute();
                $query->delete('groups')->execute();
                $query->delete('users')->execute();
            } catch (\Exception $e) {
                // If delete fails, fall through to recreate
                $usersExists = false;
            }
        }

        // Create users table if doesn't exist
        if (!$usersExists) {
            try {
                $driver->dropTable('users', true);
            } catch (\Exception $e) {
                // Ignore
            }
            $schema->table('users')->create()
                ->id('id')
                ->string('name', 255)
                ->string('email', 255)
                ->execute();
        }

        // Create groups table if doesn't exist
        if (!$groupsExists) {
            try {
                $driver->dropTable('groups', true);
            } catch (\Exception $e) {
                // Ignore
            }
            $schema->table('groups')->create()
                ->id('id')
                ->string('cn', 255)->nullable()
                ->string('name', 255)->nullable()
                ->integer('created_by')->nullable()
                ->execute();
        }

        // Create posts table if doesn't exist
        if (!$postsExists) {
            try {
                $driver->dropTable('posts', true);
            } catch (\Exception $e) {
                // Ignore
            }
            $schema->table('posts')->create()
                ->id('id')
                ->string('title', 255)->nullable()
                ->integer('user_id')->nullable()
                ->execute();
        }

        // Insert test data (4 default users)
        $query->insertMany('users', [
            ['id' => 1, 'name' => 'Sam Wilson', 'email' => 'sam@example.com'],
            ['id' => 2, 'name' => 'Sally Smith', 'email' => 'sally@example.com'],
            ['id' => 3, 'name' => 'Steve Rogers', 'email' => 'steve@example.com'],
            ['id' => 4, 'name' => 'Sarah Connor', 'email' => 'sarah@example.com'],
        ]);

        // Set auto-increment to 5 so next insert gets ID=5
        $driver->setAutoIncrement('users', 5);
    }

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach (static::getClassDrivers() as $dbName => $driver) {
            // Clear data and re-insert default rows (no DDL, just DML)
            // Only do this if tables exist
            if ($driver->tableExists('users')) {
                // Truncate all tables (commits automatically)
                $driver->truncateTable('posts');
                $driver->truncateTable('groups');
                $driver->truncateTable('users');

                // Re-insert default users
                (new Query($driver))->insertMany('users', [
                    ['id' => 1, 'name' => 'Sam Wilson', 'email' => 'sam@example.com'],
                    ['id' => 2, 'name' => 'Sally Smith', 'email' => 'sally@example.com'],
                    ['id' => 3, 'name' => 'Steve Rogers', 'email' => 'steve@example.com'],
                    ['id' => 4, 'name' => 'Sarah Connor', 'email' => 'sarah@example.com'],
                ]);

                // Set next ID to 5 and commit
                $driver->setAutoIncrement('users', 5);
                try {
                    $connection = $driver->getConnection();
                    if ($connection && $connection->inTransaction()) {
                        $connection->commit();
                    }
                } catch (\Exception $e) {
                    // Ignore
                }
            }
        }
    }

    /**
     * Seed posts table for join tests.
     *
     * @param Driver $driver
     * @param array  $rows
     */
    private function seedPosts(Driver $driver, array $rows): void
    {
        $query = new Query($driver);
        $query->insertMany('posts', $rows);
    }

    /**
     * Seed groups table for join tests.
     *
     * @param Driver $driver
     * @param array  $rows
     */
    private function seedGroups(Driver $driver, array $rows): void
    {
        $query = new Query($driver);
        $query->insertMany('groups', $rows);
    }
    /**
     * Test to make sure we can run a basic select statement
     */
    #[DataProvider('databaseProvider')]
    public function testBasicFetch(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Try to actually fetch some rows
        $rows = $query->select('*')
                      ->from('users')
                      ->whereEquals('id', '1')
                      ->fetch();

        // Basically, as long as we don't get false here, we're good
        $this->assertCount(1, $rows, 'Query should have returned one result');
    }

    /**
     * Test percent expression in a functional query
     */
    #[DataProvider('databaseProvider')]
    public function testPercentExpressionFunctional(string $dbName, Driver $driver)
    {
        $seed = new Query($driver);
        $seed->insertMany('posts', [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $row = $query->select(
            Expression::percent(
                Expression::count('posts.id'),
                Expression::count('posts.id')
            ),
            'score'
        )
            ->from('posts')
            ->first();

        $this->assertNotNull($row);
        $this->assertEquals(100, (int) $row->score);
    }

    /**
     * Test select with array of columns
     */
    #[DataProvider('databaseProvider')]
    public function testSelectArrayColumns(string $dbName, Driver $driver)
    {
        $query = new Query($driver);
        $rows = $query->select(['id', 'email'])
                      ->from('users')
                      ->order('id', 'asc')
                      ->fetch();

        $this->assertCount(4, $rows);
        $this->assertEquals(1, $rows[0]->id);
        $this->assertEquals('sam@example.com', $rows[0]->email);
    }

    /**
     * Test to make sure we can run a basic insert statement
     */
    #[DataProvider('databaseProvider')]
    public function testBasicPush(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Try to add a new row
        $query->push('users', [
            'name'  => 'new user',
            'email' => 'newuser@gmail.com'
        ]);

        // There are 4 default users in the seed data, and adding a new one should a rowcount of 5
        $count = $driver->setQuery("SELECT COUNT(*) FROM users")->loadResult();
        $this->assertEquals(
            5,
            $count,
            'Push did not return the expected row count of 5'
        );
    }

    /**
     * Test to make sure we can run a basic update statement
     */
    #[DataProvider('databaseProvider')]
    public function testBasicAlter(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Try to update an existing row
        $query->alter('users', 'id', 1, [
            'name'  => 'Updated User',
            'email' => 'updateduser@gmail.com'
        ]);

        // Verify the update worked
        $updated = $driver->setQuery("SELECT * FROM users WHERE id = 1")->loadObject();
        $this->assertEquals('Updated User', $updated->name);
        $this->assertEquals('updateduser@gmail.com', $updated->email);

        // Verify other rows were not affected
        $count = $driver->setQuery("SELECT COUNT(*) FROM users")->loadResult();
        $this->assertEquals(4, $count, 'Alter should not change row count');
    }

    /**
     * Test to make sure we can set a basic value on update
     */
    #[DataProvider('databaseProvider')]
    public function testSetWithBasicValue(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Try to update an existing row with Basic value objects
        $query->alter('users', 'id', 1, [
            'name'  => new Basic('Updated User'),
            'email' => new Basic('updateduser@gmail.com')
        ]);

        // Verify the update worked
        $updated = $driver->setQuery("SELECT * FROM users WHERE id = 1")->loadObject();
        $this->assertEquals('Updated User', $updated->name);
        $this->assertEquals('updateduser@gmail.com', $updated->email);
    }

    /**
     * Test to make sure we can run a basic delete statement
     */
    #[DataProvider('databaseProvider')]
    public function testBasicRemove(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $result = $query->remove('users', null, null);
        $this->assertFalse($result);

        // Try to remove an existing row
        $query->remove('users', 'id', 1);

        $count = $driver->setQuery("SELECT COUNT(*) FROM users")->loadResult();
        $this->assertEquals(
            3,
            $count,
            'Remove did not return the expected row count of 3'
        );
    }

    /**
     * Test to make sure we can build a query with aliased from statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithAliasedFrom(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Build query with table alias
        $query->select('*')
              ->from('users', 'u')
              ->whereEquals('u.name', 'Sam Wilson');

        // Execute and verify functional behavior
        $results = $query->fetch();

        $this->assertCount(1, $results, 'Query with aliased FROM should return one result');
        $this->assertEquals('Sam Wilson', $results[0]->name);
        $this->assertEquals('sam@example.com', $results[0]->email);
    }

    /**
     * Test to make sure we can build a query with a raw WHERE statement
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithRawWhere(string $dbName, Driver $driver)
    {
        // Use driver's quoteName to handle database-specific casing
        $nameCol = $driver->quoteName('name');

        // Test whereRaw with lowercase match
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereRaw("LOWER({$nameCol})=?", ['sam wilson']);

        $results = $query->fetch();
        $this->assertCount(1, $results, 'whereRaw should match case-insensitively');
        $this->assertEquals('Sam Wilson', $results[0]->name);

        // Test orWhereRaw
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('name', 'Sally Smith')
              ->orWhereRaw("LOWER({$nameCol})=?", ['sam wilson']);

        $results = $query->fetch();
        $this->assertCount(2, $results, 'orWhereRaw should return two results');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('Sally Smith', $names);
    }

    /**
     * Test to make sure we can build a query with where like statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithWhereLike(string $dbName, Driver $driver)
    {
        // Test whereLike - match users with 'Sam' or 'Sally' in name
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereLike('name', 'Sa');

        $results = $query->fetch();
        $this->assertCount(3, $results, 'whereLike should match Sam, Sally, Sarah');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('Sally Smith', $names);
        $this->assertContains('Sarah Connor', $names);

        // Test orWhereLike - match 'Steve' OR names with 'Sa'
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereLike('name', 'Steve')
              ->orWhereLike('name', 'Sally');

        $results = $query->fetch();
        $this->assertCount(2, $results, 'orWhereLike should match Steve and Sally');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Steve Rogers', $names);
        $this->assertContains('Sally Smith', $names);
    }

    /**
     * Test to make sure we can build a query with where IS NULL statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithWhereIsNull(string $dbName, Driver $driver)
    {
        // Insert a test user with NULL email for testing
        $q = new Query($driver);
        $q->push('users', ['name' => 'No Email User', 'email' => null]);

        // Test whereIsNull
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereIsNull('email');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(1, count($results), 'whereIsNull should find users with NULL email');

        $nullEmailFound = false;
        foreach ($results as $row) {
            if ($row->name === 'No Email User') {
                $this->assertNull($row->email);
                $nullEmailFound = true;
            }
        }
        $this->assertTrue($nullEmailFound, 'Should have found the user with NULL email');

        // Test orWhereIsNull - find users with specific name OR NULL email
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('name', 'Sam Wilson')
              ->orWhereIsNull('email');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(
            2,
            count($results),
            'orWhereIsNull should find Sam Wilson and NULL email users'
        );

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('No Email User', $names);
    }

    /**
     * Test to make sure we can build a query with where IS NOT NULL statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithWhereIsNotNull(string $dbName, Driver $driver)
    {
        // Test whereIsNotNull - all original users have non-null names
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereIsNotNull('name');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(4, count($results), 'whereIsNotNull should find all users with non-null names');

        foreach ($results as $row) {
            $this->assertNotNull($row->name, 'All returned users should have non-null names');
        }

        // Test orWhereIsNotNull
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('id', 1)
              ->orWhereIsNotNull('name');

        $results = $query->fetch();
        // Should get all users with non-null names (which is all of them) plus id=1 (which is also included)
        $this->assertGreaterThanOrEqual(4, count($results), 'orWhereIsNotNull should find many users');
    }

    /**
     * Test to make sure we can build a query with where IN statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithWhereIn(string $dbName, Driver $driver)
    {
        // Test whereIn with actual user names
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereIn('name', ['Sam Wilson', 'Sally Smith', 'Steve Rogers']);

        $results = $query->fetch();
        $this->assertCount(3, $results, 'whereIn should match three users');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('Sally Smith', $names);
        $this->assertContains('Steve Rogers', $names);
        $this->assertNotContains('Sarah Connor', $names);

        // Test orWhereIn
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('name', 'Sarah Connor')
              ->orWhereIn('name', ['Sam Wilson', 'Sally Smith']);

        $results = $query->fetch();
        $this->assertCount(3, $results, 'orWhereIn should match three users');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sarah Connor', $names);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('Sally Smith', $names);

        // Test whereNotIn - should exclude Sam and Sally
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereNotIn('name', ['Sam Wilson', 'Sally Smith']);

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(2, count($results), 'whereNotIn should exclude Sam and Sally');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertNotContains('Sam Wilson', $names);
        $this->assertNotContains('Sally Smith', $names);
        $this->assertContains('Steve Rogers', $names);
        $this->assertContains('Sarah Connor', $names);

        // Test orWhereNotIn
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('id', 1)  // Sam Wilson
              ->orWhereNotIn('name', ['Sally Smith', 'Steve Rogers', 'Sarah Connor']);

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(
            1,
            count($results),
            'orWhereNotIn should include Sam and anyone not in the list'
        );

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
    }

    /**
     * Test to make sure we can build a query with complex nested where statements
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithNestedWheres(string $dbName, Driver $driver)
    {
        // Test nested where: (name = Sam OR name = Sally) AND (email LIKE %example% OR email LIKE %gmail%)
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->whereEquals('name', 'Sam Wilson', 1)
              ->orWhereEquals('name', 'Sally Smith', 1)
              ->resetDepth(0)
              ->whereLike('email', 'example.com', 1)
              ->orWhereLike('email', 'test.com', 1);

        $results = $query->fetch();

        // Should match users whose name is Sam OR Sally, AND whose email contains example.com or test.com
        // Sam Wilson (sam@example.com) - matches both conditions ✓
        // Sally Smith (sally@example.com) - matches both conditions ✓
        $this->assertCount(2, $results, 'Nested where should match Sam and Sally with example.com emails');

        $names = array_map(fn($r) => $r->name, $results);
        $this->assertContains('Sam Wilson', $names);
        $this->assertContains('Sally Smith', $names);
    }

    /**
     * Test to make sure we can build a query with JOIN clauses
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithJoinClause(string $dbName, Driver $driver)
    {
        // Insert test posts for join testing
        $qInsert = new Query($driver);
        $qInsert->insertMany('posts', [
            ['id' => 1, 'title' => 'Post 1', 'user_id' => 1],
            ['id' => 2, 'title' => 'Post 2', 'user_id' => 2],
            ['id' => 3, 'title' => 'Post 3', 'user_id' => 1],
        ]);

        // Test INNER JOIN (alias: join) - let builder handle quoting/casing
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->join('posts', 'users.id', 'posts.user_id');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(3, count($results), 'INNER JOIN should match users with posts');

        // Test explicit innerJoin
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->innerJoin('posts', 'users.id', 'posts.user_id');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(3, count($results), 'innerJoin should match users with posts');

        // Test LEFT JOIN - should include all users even without posts
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->leftJoin('posts', 'users.id', 'posts.user_id');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(4, count($results), 'LEFT JOIN should include all users');

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->rightJoin('posts', 'users.id', 'posts.user_id');

        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(3, count($results), 'RIGHT JOIN should include all posts');
    }

    /**
     * Test FULL JOIN
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithFullJoin(string $dbName, Driver $driver)
    {
        // Insert test posts if not already present
        $qInsert = new Query($driver);
        $qInsert->insertMany('posts', [
            ['id' => 11, 'title' => 'Post 11', 'user_id' => 1],
            ['id' => 12, 'title' => 'Post 12', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->fullJoin('posts', 'users.id', 'posts.user_id');

        // FULL JOIN should include all users and all posts
        $results = $query->fetch();
        $this->assertGreaterThanOrEqual(4, count($results), 'FULL JOIN should include all users and posts');
    }

    /**
     * Test FULL JOIN emulation with multiple joins and edge clauses
     */
    #[DataProvider('databaseProvider')]
    public function testFullJoinEmulationWithMultipleJoins(string $dbName, Driver $driver)
    {
        $this->expectException(UnsupportedSyntaxException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->fullJoin('posts', 'users.id', 'posts.user_id')
              ->fullJoin('groups', 'users.id', 'groups.created_by')
              ->where('users.id', '>=', 1)
              ->order('users.id', 'asc')
              ->limit(10);

        $query->fetch();
    }

    /**
     * Test FULL JOIN with additional LEFT/INNER joins (safe subset)
     */
    #[DataProvider('databaseProvider')]
    public function testFullJoinWithAdditionalJoins(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 31, 'title' => 'Post 31', 'user_id' => 1],
            ['id' => 32, 'title' => 'Post 32', 'user_id' => 2],
        ]);
        $this->seedGroups($driver, [
            ['id' => 11, 'cn' => 'group-11', 'name' => 'Group 11', 'created_by' => 1],
            ['id' => 12, 'cn' => 'group-12', 'name' => 'Group 12', 'created_by' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('*')
              ->from('users')
              ->fullJoin('posts', 'users.id', 'posts.user_id')
              ->leftJoin('groups', 'users.id', 'groups.created_by')
              ->innerJoin('posts p2', 'users.id', 'p2.user_id')
              ->where('users.id', '>=', 1)
              ->fetch();

        $this->assertNotEmpty($rows, 'FULL JOIN with additional LEFT/INNER joins should return rows');
    }

    /**
     * Test FULL JOIN with unqualified keys resolved via schema lookup
     */
    #[DataProvider('databaseProvider')]
    public function testFullJoinWithUnqualifiedKeys(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'hello', 'user_id' => 1],
            ['id' => 2, 'title' => 'world', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('*')
                      ->from('users')
                      ->fullJoin('posts', 'id', 'user_id')
                      ->fetch();

        // 4 rows: 2 matched (users 1,2) + 2 unmatched users (3,4)
        $this->assertCount(4, $rows, "[$dbName]");
    }

    /**
     * Test joinOn with multiple predicates
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnMultiplePredicates(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'steve@example.com', 'user_id' => null],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['or', 'users.email', '=', 'posts.title'],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
        $userIds = array_map(function ($row) {
            return $row->user_id;
        }, $rows);
        sort($userIds);
        $this->assertEquals([1, 3], $userIds);
    }

    /**
     * Test joinBuilder fluent chaining
     */
    #[DataProvider('databaseProvider')]
    public function testJoinBuilderChaining(string $dbName, Driver $driver)
    {
        $seed = new Query($driver);
        $seed->insertMany('posts', [
            ['id' => 1, 'title' => 'active', 'user_id' => 1],
            ['id' => 2, 'title' => 'inactive', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.title')
                      ->from('users')
                      ->joinBuilder('posts', 'left')
                          ->on('users.id', '=', 'posts.user_id')
                          ->andWhere('posts.title', '=', 'active')
                          ->end()
                      ->fetch();

        $this->assertCount(4, $rows);
        $activeTitles = array_filter(array_map(function ($row) {
            return $row->title ?? null;
        }, $rows));
        $this->assertCount(1, $activeTitles);
        $this->assertEquals('active', array_values($activeTitles)[0]);
    }

    /**
     * Test joinBuilder with grouped predicates
     */
    #[DataProvider('databaseProvider')]
    public function testJoinBuilderGroup(string $dbName, Driver $driver)
    {
        $seed = new Query($driver);
        $seed->insertMany('posts', [
            ['id' => 1, 'title' => 'sam@example.com', 'user_id' => 1],
            ['id' => 2, 'title' => 'active', 'user_id' => 2],
            ['id' => 3, 'title' => 'other', 'user_id' => 3],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.title')
                      ->from('users')
                      ->joinBuilder('posts')
                          ->on('users.id', '=', 'posts.user_id')
                          ->group(function ($join) {
                            $join->on('users.email', '=', 'posts.title')
                                 ->orWhere('posts.title', '=', 'active');
                          })
                          ->end()
                      ->fetch();

        $this->assertCount(2, $rows);
        $titles = array_map(function ($row) {
            return $row->title;
        }, $rows);
        sort($titles);
        $this->assertEquals(['active', 'sam@example.com'], $titles);
    }

    /**
     * Test join with a closure (Laravel-style)
     */
    #[DataProvider('databaseProvider')]
    public function testJoinWithClosure(string $dbName, Driver $driver)
    {
        $seed = new Query($driver);
        $seed->insertMany('posts', [
            ['id' => 1, 'title' => 'active', 'user_id' => 1],
            ['id' => 2, 'title' => 'inactive', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.title')
                      ->from('users')
                      ->leftJoin('posts', function ($join) {
                        $join->on('users.id', '=', 'posts.user_id')
                             ->andWhere('posts.title', '=', 'active');
                      })
                      ->fetch();

        $this->assertCount(4, $rows);
        $activeTitles = array_filter(array_map(function ($row) {
            return $row->title ?? null;
        }, $rows));
        $this->assertCount(1, $activeTitles);
        $this->assertEquals('active', array_values($activeTitles)[0]);
    }

    /**
     * Test joinOn with literal predicates (join-where style)
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithLiteralPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.name', 'operator' => '=', 'value' => 'Sam Wilson'],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn with NULL literal predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithNullLiteralPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.email', 'operator' => 'is', 'value' => null],
                      ])
                      ->fetch();

        $this->assertCount(0, $rows);
    }

    /**
     * Test joinOn with left-side literal predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithLeftLiteralPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left_value' => 1, 'operator' => '=', 'right' => 'posts.user_id'],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn with IN predicate list
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithInPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 3],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'in', 'value' => [1, 2]],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn with BETWEEN predicate range
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithBetweenPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
            ['id' => 3, 'title' => 'post-3', 'user_id' => 4],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'between', 'value' => [1, 3]],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
    }

    /**
     * Test joinOn with LIKE predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithLikePredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.name', 'operator' => 'like', 'value' => 'Sam%'],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn with IS DISTINCT FROM predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithIsDistinctFromPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'sam@example.com', 'user_id' => 1],
            ['id' => 2, 'title' => 'different@example.com', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.email', 'operator' => 'is distinct from', 'right' => 'posts.title'],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(2, $rows[0]->user_id);
    }

    /**
     * Test joinOn with EXISTS predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithExistsPredicate(string $dbName, Driver $driver)
    {
        // Some engines may rewrite non-correlated EXISTS predicates from JOIN ON
        // into WHERE while preserving result semantics.

        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['operator' => 'exists', 'value' => 'SELECT 1'],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
    }

    /**
     * Test joinOn with NOT EXISTS predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithNotExistsPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['operator' => 'not exists', 'value' => 'SELECT 1'],
                      ])
                      ->fetch();

        $this->assertCount(0, $rows);
    }

    /**
     * Test joinOn with IS NOT DISTINCT FROM predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithIsNotDistinctFromPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'sam@example.com', 'user_id' => 1],
            ['id' => 2, 'title' => 'different@example.com', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.email', 'operator' => 'is not distinct from', 'right' => 'posts.title'],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn with NOT LIKE predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithNotLikePredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
            ['id' => 3, 'title' => 'post-3', 'user_id' => 3],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.name', 'operator' => 'not like', 'value' => 'Sam%'],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
    }

    /**
     * Test joinOn with NOT IN predicate list
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithNotInPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
            ['id' => 3, 'title' => 'post-3', 'user_id' => 3],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'not in', 'value' => [1, 2]],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(3, $rows[0]->user_id);
    }

    /**
     * Test joinOn with NOT BETWEEN predicate range
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithNotBetweenPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 2],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 4],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'not between', 'value' => [1, 3]],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(4, $rows[0]->user_id);
    }

    /**
     * Test joinOn with IS NOT NULL literal predicate
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithIsNotNullLiteralPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.email', 'operator' => 'is not', 'value' => null],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
    }

    /**
     * Test joinOn with empty IN list
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithEmptyInPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'in', 'value' => []],
                      ])
                      ->fetch();

        $this->assertCount(0, $rows);
    }

    /**
     * Test joinOn with empty NOT IN list
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithEmptyNotInPredicate(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['left' => 'users.id', 'operator' => 'not in', 'value' => []],
                      ])
                      ->fetch();

        $this->assertCount(2, $rows);
    }

    /**
     * Test joinOn with EXISTS subquery built by Query
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnWithExistsSubquery(string $dbName, Driver $driver)
    {
        $this->seedPosts($driver, [
            ['id' => 1, 'title' => 'post-1', 'user_id' => 1],
            ['id' => 2, 'title' => 'post-2', 'user_id' => 2],
        ]);
        $this->seedGroups($driver, [
            ['id' => 1, 'cn' => 'group-1', 'name' => 'Group 1', 'created_by' => 1],
        ]);

        $subquery = new Query($driver);
        $subquery->select('1')
                 ->from('groups')
                 ->whereRaw('groups.created_by = users.id');

        $query = new Query($driver);
        $rows = $query->select('users.id', 'user_id')
                      ->select('posts.id', 'post_id')
                      ->from('users')
                      ->joinOn('posts', [
                        ['users.id', '=', 'posts.user_id'],
                        ['operator' => 'exists', 'value' => $subquery->toSql()],
                      ])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]->user_id);
    }

    /**
     * Test joinOn rejects invalid condition formats
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnInvalidConditionFormatThrows(string $dbName, Driver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->joinOn('posts', [
                ['users.id', '=', 'posts.user_id'],
                ['too', 'many', 'parts', 'in', 'condition'],
              ]);
    }

    /**
     * Test joinOn rejects BETWEEN with incorrect value count
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnBetweenInvalidRangeThrows(string $dbName, Driver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->joinOn('posts', [
                ['users.id', '=', 'posts.user_id'],
                ['left' => 'users.id', 'operator' => 'between', 'value' => [1]],
              ]);

        $query->fetch();
    }

    /**
     * Test joinOn rejects EXISTS without a subquery
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnExistsWithoutSubqueryThrows(string $dbName, Driver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->joinOn('posts', [
                ['users.id', '=', 'posts.user_id'],
                ['operator' => 'exists'],
              ]);
    }

    /**
     * Test joinOn rejects array left-side literal
     */
    #[DataProvider('databaseProvider')]
    public function testJoinOnLeftLiteralArrayThrows(string $dbName, Driver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->joinOn('posts', [
                ['users.id', '=', 'posts.user_id'],
                ['left_value' => [1, 2], 'operator' => '=', 'right' => 'posts.user_id'],
              ]);
    }

    /**
     * Test fullJoinOn rejects multiple predicates
     */
    #[DataProvider('databaseProvider')]
    public function testFullJoinOnMultiplePredicatesThrows(string $dbName, Driver $driver)
    {
        $this->expectException(UnsupportedSyntaxException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->fullJoinOn('posts', [
                ['users.id', '=', 'posts.user_id'],
                ['users.email', '=', 'posts.title'],
              ]);

        $query->fetch();
    }

    /**
     * Test to make sure we can build a query with JOIN and complex conditions
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryWithComplexJoinConditions(string $dbName, Driver $driver)
    {
        // Insert test posts for join testing
        $qInsert = new Query($driver);
        $qInsert->insertMany('posts', [
            ['id' => 21, 'title' => 'Post 21', 'user_id' => 1],
            ['id' => 22, 'title' => 'Post 22', 'user_id' => 2],
            ['id' => 23, 'title' => 'Post 23', 'user_id' => 3],
        ]);

        // Test JOIN with additional WHERE condition
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->join('posts', 'users.id', 'posts.user_id')
              ->where('users.id', '>', 1);

        $results = $query->fetch();

        // Should only match user_id > 1 (users 2 and 3)
        $this->assertGreaterThanOrEqual(2, count($results), 'JOIN with WHERE condition should filter results');

        foreach ($results as $row) {
            $this->assertGreaterThan(1, $row->id, 'All joined users should have id > 1');
        }
    }

    /**
     * Test to make sure we can build an INSERT statement
     */
    #[DataProvider('databaseProvider')]
    public function testBuildInsert(string $dbName, Driver $driver)
    {
        // Test basic INSERT - execute and verify
        $query = new Query($driver);
        $query->insert('users')
            ->values(['name' => 'danger', 'email' => 'danger@test.com'])
            ->execute();

        // Verify the insert worked
        $result = $driver->setQuery("SELECT * FROM users WHERE name = 'danger'")->loadObject();
        $this->assertNotNull($result, 'INSERT should have created a new user');
        $this->assertEquals('danger', $result->name);
        $this->assertEquals('danger@test.com', $result->email);

        // Test INSERT IGNORE (syntax differs by database)
        // Try to insert duplicate - should be ignored if using IGNORE
        $query = new Query($driver);
        $query->insert('users', true)
            ->values(['id' => 1, 'name' => 'should be ignored', 'email' => 'ignored@test.com'])
            ->execute();

        // Verify original record unchanged (INSERT IGNORE should have prevented update)
        $result = $driver->setQuery("SELECT * FROM users WHERE id = 1")->loadObject();
        $this->assertEquals('Sam Wilson', $result->name, 'INSERT IGNORE should not overwrite existing record');
    }

    /**
     * Test to make sure that fetch properly caches a query
     */
    #[DataProvider('databaseProvider')]
    public function testFetchCachesQueriesByDefault(string $dbName, Driver $driver)
    {
        Query::purgeCache();

        // First fetch populates the cache
        $query1 = new Query($driver);
        $result1 = $query1->from('users')->select('*')->fetch('rows');
        $this->assertGreaterThan(0, count($result1), "[$dbName] Should have users");

        // Modify a property on the cached stdClass object
        $original = $result1[0]->name;
        $result1[0]->name = '__CACHE_MARKER__';

        // Second fetch with same SQL should hit static cache (same objects)
        $query2 = new Query($driver);
        $result2 = $query2->from('users')->select('*')->fetch('rows');
        $this->assertEquals(
            '__CACHE_MARKER__',
            $result2[0]->name,
            "[$dbName] Cached fetch should return the same stdClass objects (shared reference)"
        );

        // Restore and clean up
        $result1[0]->name = $original;
        Query::purgeCache();
    }

    /**
     * Test to make sure that fetch does not cache when explicitly disabled
     */
    #[DataProvider('databaseProvider')]
    public function testFetchDoesNotCacheQueries(string $dbName, Driver $driver)
    {
        Query::purgeCache();

        // First fetch with noCache=true
        $query1 = new Query($driver);
        $result1 = $query1->from('users')->select('*')->fetch('rows', true);
        $this->assertGreaterThan(0, count($result1), "[$dbName] Should have users");

        // Modify a property on the returned stdClass object
        $original = $result1[0]->name;
        $result1[0]->name = '__NOCACHE_MARKER__';

        // Second fetch with noCache=true re-executes the query (new objects)
        $query2 = new Query($driver);
        $result2 = $query2->from('users')->select('*')->fetch('rows', true);
        $this->assertEquals(
            $original,
            $result2[0]->name,
            "[$dbName] Non-cached fetch should return fresh objects from the database"
        );

        Query::purgeCache();
    }

    /**
     * Test to make sure we can clear query parts and rebuild
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryClear(string $dbName, Driver $driver)
    {
        // Test clearing multiple parts and rebuilding
        $query = new Query($driver);
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

        // Execute and verify we're now querying groups, not users
        $results = $query->fetch();
        $this->assertIsArray($results, 'Query should execute successfully after clearing');

        // Test deselect and reselect
        $query = new Query($driver);
        $query->select('name')
              ->from('users')
              ->whereIsNotNull('name')
              ->deselect()
              ->select('id');

        $results = $query->fetch();
        $this->assertGreaterThan(0, count($results), 'deselect/reselect should work');
        // Verify we're selecting id, not name
        foreach ($results as $row) {
            $this->assertObjectHasProperty('id', $row);
        }

        // Test clearing INSERT - build query for groups, then clear and switch to users
        $query = new Query($driver);
        $query->insert('groups')
              ->values(['cn' => 'lorem'])
              ->clear('insert')
              ->clear('values')
              ->insert('users')
              ->values(['name' => 'Jimmy', 'email' => 'jimmy@test.com'])
              ->execute();

        // Verify insert went to users table, not groups
        $result = $driver->setQuery("SELECT * FROM users WHERE name = 'Jimmy'")->loadObject();
        $this->assertNotNull($result, 'Cleared INSERT should have inserted into users');

        // Test clearing DELETE
        $query = new Query($driver);
        $query->delete('groups')
              ->whereEquals('cn', 'lorem')
              ->clear('delete')
              ->clear('where')
              ->delete('users')
              ->whereEquals('name', 'Jimmy')
              ->execute();

        // Verify Jimmy was deleted
        $result = $driver->setQuery("SELECT * FROM users WHERE name = 'Jimmy'")->loadObject();
        $this->assertNull($result, 'Cleared DELETE should have deleted from users');

        // Test clearing UPDATE
        $query = new Query($driver);
        $query->update('groups')
              ->set(['cn' => 'lorem'])
              ->whereEquals('cn', 'lorem')
              ->clear('update')
              ->clear('where')
              ->update('users')
              ->set(['name' => 'Frank'])
              ->whereEquals('id', 2)
              ->execute();

        // Verify user 2 was updated
        $result = $driver->setQuery("SELECT * FROM users WHERE id = 2")->loadObject();
        $this->assertEquals('Frank', $result->name, 'Cleared UPDATE should have updated users');
    }

    /**
     * Test to make sure we can build a query with LIMIT and START
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryLimitStart(string $dbName, Driver $driver)
    {
        // Insert some test groups for pagination testing
        $qInsert = new Query($driver);
        $groupData = [];
        for ($i = 1; $i <= 15; $i++) {
            $groupData[] = ['id' => $i, 'cn' => "group{$i}", 'name' => "Group {$i}"];
        }
        $qInsert->insertMany('groups', $groupData);

        // Test LIMIT only - should get first 10 results
        $query = new Query($driver);
        $query->select('*')
              ->from('groups')
              ->order('id', 'asc')
              ->limit(10);

        $results = $query->fetch();
        $this->assertCount(10, $results, 'LIMIT should restrict to 10 results');
        $this->assertEquals(1, $results[0]->id, 'First result should be id=1');

        // Test LIMIT with START - should skip first 5, get next 10
        $query = new Query($driver);
        $query->select('*')
              ->from('groups')
              ->order('id', 'asc')
              ->limit(10)
              ->start(5);

        $results = $query->fetch();
        $this->assertCount(10, $results, 'LIMIT with START should still return 10 results');
        $this->assertEquals(6, $results[0]->id, 'First result should be id=6 (after skipping 5)');
        $this->assertEquals(15, $results[9]->id, 'Last result should be id=15');
    }

    /**
     * Test that START is an integer
     */
    #[DataProvider('databaseProvider')]
    public function testNonNumericStart(string $dbName, Driver $driver)
    {
        // Test that non-numeric start is ignored at Query level (casts to 0)
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->start('beginning');

        // Should execute without START/OFFSET clause (non-numeric becomes 0)
        $results = $query->fetch();
        $this->assertIsArray($results, 'Query with invalid start should still execute');

        // NOTE: We directly test the Syntax class as the `start()` method on
        //       the Query class casts values as integers.
        $syntaxClass = BackendRegistry::resolveSyntaxClassFor($driver->getSyntax());
        $this->assertNotNull($syntaxClass);
        $syntax = new $syntaxClass($driver);

        $this->expectException(\InvalidArgumentException::class);

        $syntax->setStart('beginning');
    }

    /**
     * Test that START is greater than or equal to zero
     */
    #[DataProvider('databaseProvider')]
    public function testNegativeStart(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $this->expectException(\InvalidArgumentException::class);

        $query->select('*')
              ->from('groups')
              ->start(-50);
    }

    /**
     * Test that LIMIT is an integer
     */
    #[DataProvider('databaseProvider')]
    public function testNonNumericLimit(string $dbName, Driver $driver)
    {
        // Test that non-numeric limit is ignored at Query level (casts to 0)
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->limit('all');

        // Should execute without LIMIT clause (non-numeric becomes 0, ignored)
        $results = $query->fetch();
        $this->assertIsArray($results, 'Query with invalid limit should still execute');

        // NOTE: We directly test the Syntax class as the `limit()` method on
        //       the Query class casts values as integers.
        $syntaxClass = BackendRegistry::resolveSyntaxClassFor($driver->getSyntax());
        $this->assertNotNull($syntaxClass);
        $syntax = new $syntaxClass($driver);

        $this->expectException(\InvalidArgumentException::class);

        $syntax->setLimit('all');
    }

    /**
     * Test that LIMIT is greater than or equal to zero
     */
    #[DataProvider('databaseProvider')]
    public function testNegativeLimit(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $this->expectException(\InvalidArgumentException::class);

        $query->select('*')
              ->from('groups')
              ->limit(-50);
    }

    /**
     * Test to make sure we can build a query with ORDER BY
     */
    #[DataProvider('databaseProvider')]
    public function testBuildQueryOrder(string $dbName, Driver $driver)
    {
        // Test ORDER BY ASC - users should be ordered by id ascending
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->order('id', 'asc');

        $results = $query->fetch();
        $this->assertGreaterThan(0, count($results), 'ORDER BY should return results');

        // Verify ascending order
        $prevId = 0;
        foreach ($results as $row) {
            $this->assertGreaterThan($prevId, $row->id, 'Results should be in ascending order');
            $prevId = $row->id;
        }

        // Test unorder and reorder with DESC (new query object)
        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->order('id', 'asc')  // Set initial order
              ->unorder()           // Clear it
              ->order('name', 'desc');  // Set new order

        $results = $query->fetch();
        $this->assertGreaterThan(0, count($results), 'Reordered query should return results');

        // Verify descending order by name
        $prevName = 'ZZZZZ';  // Start with high value
        foreach ($results as $row) {
            $this->assertLessThanOrEqual($prevName, $row->name, 'Results should be in descending order by name');
            $prevName = $row->name;
        }

        // Test that an exception is thrown if order is not asc or desc
        $this->expectException(\InvalidArgumentException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from('users')
              ->order('id', 'foo');
    }

    /**
     * Test convenience helpers: first, value, pluck, keyBy, exists
     */
    #[DataProvider('databaseProvider')]
    public function testConvenienceHelpers(string $dbName, Driver $driver)
    {
        $query = new Query($driver);
        $first = $query->select('*')
            ->from('users')
            ->order('id', 'asc')
            ->first();
        $this->assertNotNull($first, 'first() should return a row');
        $this->assertEquals(1, $first->id, 'first() should return the lowest id');

        $query = new Query($driver);
        $email = $query->from('users')
            ->whereEquals('id', 2)
            ->value('email');
        $this->assertEquals('sally@example.com', $email, 'value() should return a scalar column');

        $query = new Query($driver);
        $names = $query->from('users')
            ->order('id', 'asc')
            ->pluck('name');
        $this->assertEquals(4, count($names), 'pluck() should return all names');
        $this->assertEquals('Sam Wilson', $names[0], 'pluck() should preserve ordering');

        $query = new Query($driver);
        $namesById = $query->from('users')
            ->pluck('name', 'id');
        $this->assertEquals('Sam Wilson', $namesById[1], 'pluck() should support keying');

        $query = new Query($driver);
        $byId = $query->from('users')->keyBy('id');
        $this->assertEquals('sarah@example.com', $byId[4]->email, 'keyBy() should index rows');

        $query = new Query($driver);
        $this->assertTrue(
            $query->from('users')->whereEquals('email', 'sam@example.com')->exists(),
            'exists() should return true for a matching row'
        );

        $query = new Query($driver);
        $this->assertTrue(
            $query->from('users')->whereEquals('email', 'nope@example.com')->doesntExist(),
            'doesntExist() should return true when no rows match'
        );
    }

    /**
     * Test exists()/doesntExist() with scoped selects and conditions
     */
    #[DataProvider('databaseProvider')]
    public function testExistsAndDoesntExist(string $dbName, Driver $driver)
    {
        $query = new Query($driver);
        $this->assertTrue(
            $query->select('id')->from('users')->whereEquals('email', 'sam@example.com')->exists(),
            'exists() should work with a select and where clause'
        );

        $query = new Query($driver);
        $this->assertTrue(
            $query->select('id')->from('users')->whereEquals('email', 'missing@example.com')->doesntExist(),
            'doesntExist() should work with a select and where clause'
        );
    }

    /**
     * Test pluck/keyBy for mapping usage patterns
     */
    #[DataProvider('databaseProvider')]
    public function testPluckAndKeyBy(string $dbName, Driver $driver)
    {
        $query = new Query($driver);
        $map = $query->from('users')->pluck('email', 'id');
        $this->assertEquals('sam@example.com', $map[1], 'pluck() should return keyed map');
        $this->assertEquals('sarah@example.com', $map[4], 'pluck() should include all rows');

        $query = new Query($driver);
        $rows = $query->from('users')->keyBy('id');
        $this->assertEquals('Sally Smith', $rows[2]->name, 'keyBy() should return rows keyed by id');
    }

    /**
     * Test insertOrIgnore and insertManyIgnore insert new rows
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreHelpers(string $dbName, Driver $driver)
    {
        $query = new Query($driver);
        $query->insertOrIgnore('users')
            ->values(['id' => 10, 'name' => 'Tony Stark', 'email' => 'tony@example.com'])
            ->execute();

        $query = new Query($driver);
        $email = $query->from('users')->whereEquals('id', 10)->value('email');
        $this->assertEquals('tony@example.com', $email, 'insertOrIgnore() should insert a new row');

        $query = new Query($driver);
        $inserted = $query->insertManyIgnore('users', [
            ['id' => 11, 'name' => 'Bruce Banner', 'email' => 'bruce@example.com'],
            ['id' => 12, 'name' => 'Natasha Romanoff', 'email' => 'natasha@example.com'],
        ], 0);
        $this->assertEquals(2, $inserted, 'insertManyIgnore() should insert rows');
    }

    /**
     * Test insertUsing (INSERT ... SELECT) helper
     */
    #[DataProvider('databaseProvider')]
    public function testInsertUsing(string $dbName, Driver $driver)
    {
        // Insert groups from users (map id -> id, name -> cn/name)
        $select = (new Query($driver))
            ->select('id', 'group_id')
            ->select('name', 'cn')
            ->select('name', 'group_name')
            ->select('id', 'creator_id')
            ->from('users')
            ->order('id', 'asc');

        $query = new Query($driver);
        $query->insertUsing('groups', $select, ['id', 'cn', 'name', 'created_by'])
            ->execute();

        $rows = (new Query($driver))
            ->from('groups')
            ->order('id', 'asc')
            ->fetch();

        $this->assertEquals(4, count($rows), 'insertUsing() should insert rows from select');
        $this->assertEquals(1, $rows[0]->id, 'insertUsing() should preserve selected id');
        $this->assertEquals('Sam Wilson', $rows[0]->name, 'insertUsing() should map selected values');
    }

    /**
     * Test paginated subquery (SELECT FROM subquery with LIMIT)
     *
     * Verifies that LIMIT/OFFSET works correctly inside a subquery
     * used as a derived table, across all database backends.
     */
    #[DataProvider('databaseProvider')]
    public function testPaginatedSubquery(
        string $dbName,
        Driver $driver
    ) {
        // Insert 15 groups for pagination testing
        $qInsert = new Query($driver);
        $groupData = [];
        for ($i = 1; $i <= 15; $i++) {
            $groupData[] = [
                'id' => $i,
                'cn' => "group{$i}",
                'name' => "Group {$i}",
            ];
        }
        $qInsert->insertMany('groups', $groupData);

        // SELECT * FROM (SELECT ... LIMIT 5) sub
        $query = new Query($driver);
        $query->select('*')
            ->fromSub(function ($sub) {
                $sub->select('*')
                    ->from('groups')
                    ->order('id', 'asc')
                    ->limit(5);
            }, 'limited_groups')
            ->order('id', 'asc');

        $results = $query->fetch();
        $this->assertCount(
            5,
            $results,
            "[$dbName] Subquery LIMIT should restrict to 5 rows"
        );
        $this->assertEquals(
            1,
            $results[0]->id,
            "[$dbName] First row should be id=1"
        );
        $this->assertEquals(
            5,
            $results[4]->id,
            "[$dbName] Last row should be id=5"
        );

        // SELECT * FROM (SELECT ... LIMIT 5 OFFSET 5) sub
        $query2 = new Query($driver);
        $query2->select('*')
            ->fromSub(function ($sub) {
                $sub->select('*')
                    ->from('groups')
                    ->order('id', 'asc')
                    ->limit(5)
                    ->start(5);
            }, 'paged_groups')
            ->order('id', 'asc');

        $results2 = $query2->fetch();
        $this->assertCount(
            5,
            $results2,
            "[$dbName] Subquery LIMIT+OFFSET should return 5 rows"
        );
        $this->assertEquals(
            6,
            $results2[0]->id,
            "[$dbName] First row after offset should be id=6"
        );
        $this->assertEquals(
            10,
            $results2[4]->id,
            "[$dbName] Last row after offset should be id=10"
        );
    }

    /**
     * Test UNION ALL with LIMIT applied to the combined result
     *
     * Verifies that LIMIT/OFFSET works correctly when applied to
     * a UNION of multiple queries, across all database backends.
     */
    #[DataProvider('databaseProvider')]
    public function testUnionAllWithLimit(
        string $dbName,
        Driver $driver
    ) {
        // Insert users (already seeded: 4 users with ids 1-4)
        // Insert groups for the union
        $qInsert = new Query($driver);
        $groupData = [];
        for ($i = 1; $i <= 6; $i++) {
            $groupData[] = [
                'id' => $i,
                'cn' => "group{$i}",
                'name' => "Group {$i}",
            ];
        }
        $qInsert->insertMany('groups', $groupData);

        // UNION ALL: names from users + names from groups, limited
        $unionQuery = new Query($driver);
        $unionQuery->select('name')
            ->from('groups')
            ->order('name', 'asc');

        $query = new Query($driver);
        $query->select('name')
            ->from('users')
            ->unionAll($unionQuery)
            ->limit(5);

        $results = $query->fetch();
        $this->assertCount(
            5,
            $results,
            "[$dbName] UNION ALL with LIMIT should return 5 rows"
        );
    }

    /**
     * Test UNION (distinct) with LIMIT
     */
    #[DataProvider('databaseProvider')]
    public function testUnionWithLimit(
        string $dbName,
        Driver $driver
    ) {
        // Insert groups with some names matching users
        $qInsert = new Query($driver);
        $qInsert->insertMany('groups', [
            ['id' => 1, 'cn' => 'g1', 'name' => 'Sam Wilson'],
            ['id' => 2, 'cn' => 'g2', 'name' => 'Unique Group'],
            ['id' => 3, 'cn' => 'g3', 'name' => 'Another Group'],
        ]);

        // UNION (distinct) removes duplicates like 'Sam Wilson'
        $unionQuery = new Query($driver);
        $unionQuery->select('name')
            ->from('groups');

        $query = new Query($driver);
        $query->select('name')
            ->from('users')
            ->union($unionQuery)
            ->limit(3);

        $results = $query->fetch();
        $this->assertCount(
            3,
            $results,
            "[$dbName] UNION with LIMIT should return 3 rows"
        );
    }

    /**
     * Test fluent pattern:
     * FROM (subquery with UNION) AS alias, then outer WHERE/ORDER/LIMIT
     *
     * Mirrors the support-activity module pattern in a backend-agnostic way.
     */
    #[DataProvider('databaseProvider')]
    public function testFromSubUnionPatternWithOuterFilterOrderAndLimit(
        string $dbName,
        Driver $driver
    ) {
        // Add group rows with high IDs for deterministic ordering.
        $this->seedGroups($driver, [
            ['id' => 100, 'cn' => 'group-100', 'name' => 'Group 100'],
            ['id' => 101, 'cn' => 'group-101', 'name' => 'Group 101'],
        ]);

        $userSource = $driver->quote('user');
        $groupSource = $driver->quote('group');

        $query = (new Query($driver))
            ->select('a.id')
            ->select('a.label')
            ->select('a.source')
            ->fromSub(function ($sub) use ($userSource, $groupSource) {
                $sub->select('id')
                    ->select('name', 'label')
                    ->select($userSource, 'source')
                    ->from('users')
                    ->union(function ($union) use ($groupSource) {
                        $union->select('id')
                            ->select('name', 'label')
                            ->select($groupSource, 'source')
                            ->from('groups');
                    });
            }, 'a')
            ->where('a.id', '>', 2)
            ->order('a.id', 'desc')
            ->limit(3);

        $results = $query->fetch();

        $this->assertCount(
            3,
            $results,
            "[$dbName] Pattern should return 3 rows after outer limit"
        );
        $this->assertEquals(
            101,
            (int) $results[0]->id,
            "[$dbName] First row should be highest id from unioned derived table"
        );
        $this->assertEquals(
            100,
            (int) $results[1]->id,
            "[$dbName] Second row should be next highest id"
        );
        $this->assertEquals(
            4,
            (int) $results[2]->id,
            "[$dbName] Third row should be top matching users id"
        );
    }

    /**
     * Test fluent pattern with optional outer "start" style filtering.
     *
     * This mirrors the support-activity flow where the outer query applies
     * an optional created-date filter after the unioned derived table exists.
     */
    #[DataProvider('databaseProvider')]
    public function testFromSubUnionPatternWithOptionalOuterStartFilter(
        string $dbName,
        Driver $driver
    ) {
        $this->seedGroups($driver, [
            ['id' => 100, 'cn' => 'group-100', 'name' => 'Group 100'],
            ['id' => 101, 'cn' => 'group-101', 'name' => 'Group 101'],
        ]);

        $buildActivityPattern = function (?int $start = null) use ($driver): Query {
            $userSource = $driver->quote('user');
            $groupSource = $driver->quote('group');

            $query = (new Query($driver))
                ->select('a.id')
                ->select('a.ticket')
                ->select('a.created')
                ->select('a.category')
                ->fromSub(function ($sub) use ($userSource, $groupSource) {
                    $sub->select('id')
                        ->select('id', 'ticket')
                        ->select('id', 'created')
                        ->select($userSource, 'category')
                        ->from('users')
                        ->union(function ($union) use ($groupSource) {
                            $union->select('id')
                                ->select('id', 'ticket')
                                ->select('id', 'created')
                                ->select($groupSource, 'category')
                                ->from('groups');
                        });
                }, 'a')
                ->order('a.created', 'desc')
                ->limit(4);

            if ($start !== null) {
                $query->where('a.created', '>', $start);
            }

            return $query;
        };

        $withoutStart = $buildActivityPattern()->fetch();
        $withStart = $buildActivityPattern(100)->fetch();

        $this->assertCount(
            4,
            $withoutStart,
            "[$dbName] Without start filter, outer limit should control row count"
        );
        $this->assertEquals(
            101,
            (int) $withoutStart[0]->created,
            "[$dbName] Highest created should appear first without start filter"
        );

        $this->assertCount(
            1,
            $withStart,
            "[$dbName] Start filter should be applied at outer query level"
        );
        $this->assertEquals(
            101,
            (int) $withStart[0]->created,
            "[$dbName] Filtered result should only contain rows above the start threshold"
        );
        $this->assertEquals(
            'group',
            $withStart[0]->category,
            "[$dbName] Category alias from unioned subquery should be preserved"
        );
    }

    /**
     * Test joinRaw with aliased tables and standard fluent filters.
     *
     * Mirrors module patterns that use explicit join conditions with aliases.
     */
    #[DataProvider('databaseProvider')]
    public function testJoinRawWithAliasedTables(string $dbName, Driver $driver)
    {
        $this->seedGroups($driver, [
            ['id' => 1, 'cn' => 'g1', 'name' => 'Group 1', 'created_by' => 1],
            ['id' => 2, 'cn' => 'g2', 'name' => 'Group 2', 'created_by' => 2],
        ]);

        $rows = (new Query($driver))
            ->select('g.id')
            ->select('u.name', 'creator')
            ->from('groups', 'g')
            ->joinRaw('users AS u', 'u.id = g.created_by', 'inner')
            ->whereEquals('u.id', 1)
            ->limit(1)
            ->fetch();

        $this->assertCount(1, $rows, "[$dbName] joinRaw should return matching joined rows");
        $this->assertEquals(1, (int) $rows[0]->id);
        $this->assertEquals('Sam Wilson', $rows[0]->creator);
    }

    /**
     * Test expression grouping with sqlSubstringIndex().
     *
     * Mirrors module patterns that group by computed domain expressions.
     */
    #[DataProvider('databaseProvider')]
    public function testGroupByExpressionUsingSqlSubstringIndex(string $dbName, Driver $driver)
    {
        (new Query($driver))->insert('users')->values([
            'id'    => 5,
            'name'  => 'Tony Stark',
            'email' => 'tony@starkindustries.com',
        ])->execute();

        $domainExpr = $driver->sqlSubstringIndex('email', '@', -1);

        $rows = (new Query($driver))
            ->select($domainExpr, 'domain')
            ->select('COUNT(*)', 'email_count')
            ->from('users')
            ->group($domainExpr)
            ->order('email_count', 'desc')
            ->fetch();

        $this->assertNotEmpty($rows, "[$dbName] Grouped domain query should return rows");

        $countsByDomain = [];
        foreach ($rows as $row) {
            $countsByDomain[(string) $row->domain] = (int) $row->email_count;
        }

        $this->assertArrayHasKey(
            'example.com',
            $countsByDomain,
            "[$dbName] Expected default seeded domain to be present"
        );
        $this->assertSame(
            4,
            $countsByDomain['example.com'],
            "[$dbName] Expected 4 seeded users in example.com"
        );
    }

    /**
     * Test sqlDateSub() expression inside whereRaw().
     *
     * Mirrors module patterns that inject portable relative-date expressions.
     */
    #[DataProvider('databaseProvider')]
    public function testSqlDateSubExpressionInWhereRaw(string $dbName, Driver $driver)
    {
        $quotedNow = $driver->quote('2026-02-16 00:00:00');
        $dateSubExpr = $driver->sqlDateSub($quotedNow, 30, 'DAY');

        $rows = (new Query($driver))
            ->select('id')
            ->from('users')
            ->whereRaw($dateSubExpr . ' IS NOT NULL')
            ->limit(1)
            ->fetch();

        $this->assertNotEmpty(
            $rows,
            "[$dbName] sqlDateSub expression should compile and execute in whereRaw"
        );
    }

    /**
     * Test sqlRand() ordering with limit/value retrieval.
     *
     * Mirrors featured-member style lookup:
     * ->order($driver->sqlRand(), 'asc')->limit(1)->value('id')
     */
    #[DataProvider('databaseProvider')]
    public function testSqlRandOrderWithLimitAndValue(string $dbName, Driver $driver)
    {
        if ($dbName === 'mock') {
            $this->assertStringContainsStringIgnoringCase(
                'RANDOM',
                $driver->sqlRand(),
                '[mock] sqlRand() should produce a RANDOM-like SQL token'
            );
            return;
        }

        $userId = (int) (new Query($driver))
            ->select('id')
            ->from('users')
            ->order(Expression::randomOrder(), 'asc')
            ->limit(1)
            ->value('id');

        $this->assertContains(
            $userId,
            [1, 2, 3, 4],
            "[$dbName] sqlRand order + value should return one of seeded user IDs"
        );
    }
}
