<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;

/**
 * Query builder integration tests across multiple database backends
 */
class QueryBuilderIntegrationTest extends AbstractDriverTestCase
{
    /**
     * Test table name
     *
     * @var string
     */
    private static $testTable = 'qb_test';

    /**
     * Related test table name for JOIN tests
     *
     * @var string
     */
    private static $relatedTable = 'qb_test_comments';

    protected static function getTestTables(): array
    {
        return [self::$relatedTable, self::$testTable];
    }

    /**
     * Set up before each test - clean the test data
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clean all test tables before each test to ensure a fresh start
        foreach (static::getClassDrivers() as $dbName => $driver) {
            try {
                (new Query($driver))->delete(self::$relatedTable)->execute();
                (new Query($driver))->delete(self::$testTable)->execute();
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }

    /**
     * Set up test tables using the schema builder (database-agnostic)
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing tables (order matters for foreign key dependencies)
        try {
            $driver->dropTable(self::$relatedTable);
            $driver->dropTable(self::$testTable);
        } catch (\Exception $e) {
            // Tables don't exist, which is fine
        }

        // Create main table
        $driver->createTable(self::$testTable)
            ->integer('id')->autoIncrement()->primaryKey('id')
            ->string('name', 255)->nullable()
            ->string('email', 255)->nullable()
            ->integer('age')->nullable()
            ->execute();

        // Create related table for JOIN tests
        $driver->createTable(self::$relatedTable)
            ->integer('id')->autoIncrement()->primaryKey('id')
            ->integer('user_id')->nullable()
            ->string('comment', 1000)->nullable()
            ->execute();
    }

    // =========================================================================
    // SELECT Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBasicSelect(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);

        // Test basic select
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->fetch();

        $this->assertCount(1, $results, "[$dbName] Should return 1 row");
        $this->assertEquals('Alice', $results[0]->name);
        $this->assertEquals('alice@example.com', $results[0]->email);
        $this->assertEquals(30, $results[0]->age);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithWhere(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 25]);

        // Test WHERE clause
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->whereEquals('name', 'Bob')
            ->fetch();

        $this->assertCount(1, $results, "[$dbName] Should return 1 row");
        $this->assertEquals('Bob', $results[0]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithOrderBy(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Charlie', 'email' => 'charlie@example.com', 'age' => 35]);
        $query->push(self::$testTable, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 25]);

        // Test ORDER BY ASC
        $query = new Query($driver);
        $results = $query->select('name')
            ->from(self::$testTable)
            ->order('name', 'ASC')
            ->fetch();

        $this->assertCount(3, $results, "[$dbName] Should return 3 rows");
        $this->assertEquals('Alice', $results[0]->name);
        $this->assertEquals('Bob', $results[1]->name);
        $this->assertEquals('Charlie', $results[2]->name);

        // Test ORDER BY DESC
        $query = new Query($driver);
        $results = $query->select('name')
            ->from(self::$testTable)
            ->order('age', 'DESC')
            ->fetch();

        $this->assertEquals('Charlie', $results[0]->name);
        $this->assertEquals('Alice', $results[1]->name);
        $this->assertEquals('Bob', $results[2]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithLimit(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        for ($i = 1; $i <= 5; $i++) {
            $query->push(self::$testTable, ['name' => "User$i", 'email' => "user$i@example.com", 'age' => 20 + $i]);
        }

        // Test LIMIT
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->limit(3)
            ->fetch();

        $this->assertCount(3, $results, "[$dbName] Should return 3 rows");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithOffset(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        for ($i = 1; $i <= 5; $i++) {
            $query->push(self::$testTable, ['name' => "User$i", 'email' => "user$i@example.com", 'age' => 20 + $i]);
        }

        // Test OFFSET
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->order('name', 'ASC')
            ->start(2)
            ->limit(2)
            ->fetch();

        $this->assertCount(2, $results, "[$dbName] Should return 2 rows");
        $this->assertEquals('User3', $results[0]->name);
        $this->assertEquals('User4', $results[1]->name);
    }

    // =========================================================================
    // INSERT Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testInsert(string $dbName, Driver $driver): void
    {
        // Test insert using Query builder (push is a convenience method)
        $query = new Query($driver);
        $result = $query->push(self::$testTable, [
            'name' => 'Dave',
            'email' => 'dave@example.com',
            'age' => 28
        ]);

        $this->assertTrue($result > 0, "[$dbName] Insert should return insert ID");

        // Verify inserted data
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->whereEquals('name', 'Dave')
            ->fetch();

        $this->assertCount(1, $results);
        $this->assertEquals('Dave', $results[0]->name);
    }

    // =========================================================================
    // UPDATE Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdate(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Eve', 'email' => 'eve@example.com', 'age' => 22]);

        // Test update
        $query = new Query($driver);
        $result = $query->update(self::$testTable)
            ->set(['age' => 23])
            ->whereEquals('name', 'Eve')
            ->execute();

        $this->assertTrue($result > 0, "[$dbName] Update should return affected rows");

        // Verify updated data
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->whereEquals('name', 'Eve')
            ->fetch();

        $this->assertEquals(23, $results[0]->age);
    }

    // =========================================================================
    // DELETE Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDelete(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $insertId = $query->push(self::$testTable, [
            'name' => 'Frank',
            'email' => 'frank@example.com',
            'age' => 40
        ]);

        $this->assertTrue($insertId > 0, "[$dbName] Insert should return insert ID");

        // Verify it exists
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->whereEquals('name', 'Frank')
            ->fetch();
        $this->assertCount(1, $results);

        // Test delete
        $query = new Query($driver);
        $result = $query->delete(self::$testTable)
            ->whereEquals('name', 'Frank')
            ->execute();

        $this->assertTrue($result > 0, "[$dbName] Delete should return affected rows");

        // Verify deleted
        $query = new Query($driver);
        $results = $query->select('*')
            ->from(self::$testTable)
            ->whereEquals('name', 'Frank')
            ->fetch();
        $this->assertCount(0, $results);
    }

    // =========================================================================
    // Complex Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testJoin(string $dbName, Driver $driver): void
    {
        $table = self::$testTable;
        $relatedTable = self::$relatedTable;

        // Insert test users
        $query = new Query($driver);
        $aliceId = $query->push($table, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);
        $bobId = $query->push($table, ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 25]);

        // Insert comments for users
        $query = new Query($driver);
        $query->push($relatedTable, ['user_id' => $aliceId, 'comment' => 'Great post!']);
        $query->push($relatedTable, ['user_id' => $aliceId, 'comment' => 'Thanks for sharing']);
        $query->push($relatedTable, ['user_id' => $bobId, 'comment' => 'Interesting read']);

        // Test INNER JOIN
        $query = new Query($driver);
        $results = $query->select($table . '.name, ' . $relatedTable . '.comment')
            ->from($table)
            ->join($relatedTable, $table . '.id', $relatedTable . '.user_id')
            ->order($table . '.name', 'ASC')
            ->order($relatedTable . '.id', 'ASC')
            ->fetch();

        $this->assertCount(3, $results, "[$dbName] Should return 3 joined rows");
        $this->assertEquals('Alice', $results[0]->name);
        $this->assertEquals('Great post!', $results[0]->comment);
        $this->assertEquals('Alice', $results[1]->name);
        $this->assertEquals('Thanks for sharing', $results[1]->comment);
        $this->assertEquals('Bob', $results[2]->name);
        $this->assertEquals('Interesting read', $results[2]->comment);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGroupBy(string $dbName, Driver $driver): void
    {
        // Insert test data with duplicate ages
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Charlie', 'email' => 'charlie@example.com', 'age' => 25]);

        // Test GROUP BY with COUNT (use 'cnt' to avoid reserved words)
        $query = new Query($driver);
        $results = $query->select('age, COUNT(*) as cnt')
            ->from(self::$testTable)
            ->group('age')
            ->order('age', 'ASC')
            ->fetch();

        $this->assertCount(2, $results, "[$dbName] Should return 2 age groups");
        $this->assertEquals(25, $results[0]->age);
        $this->assertEquals(1, $results[0]->cnt);
        $this->assertEquals(30, $results[1]->age);
        $this->assertEquals(2, $results[1]->cnt);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHaving(string $dbName, Driver $driver): void
    {
        // Insert test data
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 30]);
        $query->push(self::$testTable, ['name' => 'Charlie', 'email' => 'charlie@example.com', 'age' => 25]);

        // Test HAVING with GROUP BY (use 'cnt' to avoid reserved words)
        $query = new Query($driver);
        $results = $query->select('age, COUNT(*) as cnt')
            ->from(self::$testTable)
            ->group('age')
            ->havingRaw('COUNT(*) > ?', [1])
            ->fetch();

        $this->assertCount(1, $results, "[$dbName] Should return only age group with count > 1");
        $this->assertEquals(30, $results[0]->age);
        $this->assertEquals(2, $results[0]->cnt);
    }
}
