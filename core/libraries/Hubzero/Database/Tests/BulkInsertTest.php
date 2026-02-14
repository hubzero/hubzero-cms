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
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\BulkTestUser;

/**
 * Bulk Insert (insertMany) tests
 *
 * Tests for the insertMany() functionality that allows inserting multiple
 * rows in a single query for improved performance.
 */
class BulkInsertTest extends AbstractDriverTestCase
{
    /**
     * Test table name
     *
     * @var string
     */
    private static $testTable = 'bulk_test_users';

    protected static function getTestTables(): array
    {
        return ['bulk_test_users'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('bulk_test_users', true);

        $driver->createTable('bulk_test_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)->nullable()
            ->integer('status')->default(1)
            ->datetime('created')->nullable()
            ->execute();
    }

    protected function setUp(): void
    {
        parent::setUp();

        BulkTestUser::clearBootedModels();
        Query::purgeCache();
    }

    /**
     * Clean up test data before each test
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function cleanupData(Driver $driver): void
    {
        try {
            $driver->exec("DELETE FROM " . $driver->quoteName(self::$testTable));

            $connection = $driver->getConnection();
            if ($connection && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    // =========================================================================
    // Query::insertMany() Tests
    // =========================================================================

    /**
     * Test basic bulk insert via Query class
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyBasic(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'status' => 1],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'status' => 1],
            ['name' => 'Charlie', 'email' => 'charlie@example.com', 'status' => 0],
        ];

        $count = $query->insertMany(self::$testTable, $rows);

        $this->assertEquals(3, $count);

        // Verify data was inserted
        $result = $driver->setQuery('SELECT COUNT(*) as cnt FROM ' . $driver->quoteName(self::$testTable))
            ->loadObject();
        $this->assertEquals(3, $result->cnt);
    }

    /**
     * Test bulk insert with empty array returns 0
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyEmptyArray(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $count = $query->insertMany(self::$testTable, []);

        $this->assertEquals(0, $count);
    }

    /**
     * Test bulk insert validates row structure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyValidatesRowStructure(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'different_column' => 'value'], // Different structure
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row 1 has different columns');

        $query->insertMany(self::$testTable, $rows);
    }

    /**
     * Test bulk insert with chunking
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyWithChunking(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        // Create 25 rows
        $rows = [];
        for ($i = 1; $i <= 25; $i++) {
            $rows[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'status' => 1
            ];
        }

        // Insert with chunk size of 10 (should result in 3 batches)
        $count = $query->insertMany(self::$testTable, $rows, false, 10);

        $this->assertEquals(25, $count);

        // Verify all data was inserted
        $result = $driver->setQuery('SELECT COUNT(*) as cnt FROM ' . $driver->quoteName(self::$testTable))
            ->loadObject();
        $this->assertEquals(25, $result->cnt);
    }

    /**
     * Test bulk insert preserves data correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyPreservesData(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'status' => 1],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'status' => 0],
        ];

        $query->insertMany(self::$testTable, $rows);

        // Verify Alice
        $alice = $driver->setQuery(
            'SELECT * FROM ' . $driver->quoteName(self::$testTable)
            . ' WHERE email = ' . $driver->quote('alice@example.com')
        )
            ->loadObject();
        $this->assertEquals('Alice', $alice->name);
        $this->assertEquals(1, $alice->status);

        // Verify Bob
        $bob = $driver->setQuery(
            'SELECT * FROM ' . $driver->quoteName(self::$testTable)
            . ' WHERE email = ' . $driver->quote('bob@example.com')
        )
            ->loadObject();
        $this->assertEquals('Bob', $bob->name);
        $this->assertEquals(0, $bob->status);
    }

    /**
     * Test bulk insert with null values
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManyWithNullValues(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $rows = [
            ['name' => 'Alice', 'email' => null, 'status' => 1],
        ];

        $count = $query->insertMany(self::$testTable, $rows);

        $this->assertEquals(1, $count);

        // Verify null was preserved
        $alice = $driver->setQuery(
            'SELECT * FROM ' . $driver->quoteName(self::$testTable)
            . ' WHERE name = ' . $driver->quote('Alice')
        )
            ->loadObject();
        $this->assertNull($alice->email);
    }

    /**
     * Test bulk insert with single row
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryInsertManySingleRow(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);

        $rows = [
            ['name' => 'Solo', 'email' => 'solo@example.com', 'status' => 1],
        ];

        $count = $query->insertMany(self::$testTable, $rows);

        $this->assertEquals(1, $count);
    }

    // =========================================================================
    // Relational::insertMany() Tests
    // =========================================================================

    /**
     * Test static insertMany on model class
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationalInsertMany(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'status' => 1],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'status' => 1],
        ];

        $count = BulkTestUser::insertMany($rows);

        $this->assertEquals(2, $count);

        // Verify via model
        $users = BulkTestUser::all()->rows();
        $this->assertCount(2, $users);
    }

    /**
     * Test static insertMany with empty array
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationalInsertManyEmpty(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $count = BulkTestUser::insertMany([]);

        $this->assertEquals(0, $count);
    }

    /**
     * Test static insertMany with chunking
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationalInsertManyWithChunking(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $rows = [];
        for ($i = 1; $i <= 15; $i++) {
            $rows[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'status' => 1
            ];
        }

        $count = BulkTestUser::insertMany($rows, false, 5);

        $this->assertEquals(15, $count);
    }

    /**
     * Test static insertMany validates structure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationalInsertManyValidatesStructure(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $rows = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'other' => 'different'],
        ];

        $this->expectException(\InvalidArgumentException::class);

        BulkTestUser::insertMany($rows);
    }
}
