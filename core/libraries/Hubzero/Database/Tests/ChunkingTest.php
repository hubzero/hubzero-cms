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
use Hubzero\Database\Tests\TestModels\TestItem;

/**
 * Chunking methods test
 *
 * Tests chunk(), chunkById(), cursor(), and lazyById() methods for
 * memory-efficient batch processing of large datasets.
 */
class ChunkingTest extends AbstractDriverTestCase
{
    /**
     * Test table name
     *
     * @var string
     */
    private static $testTable = 'test_items';

    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array Table names
     */
    protected static function getTestTables(): array
    {
        return [self::$testTable];
    }

    /**
     * Create test tables and insert seed data for a database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing table
        try {
            $driver->dropTable(self::$testTable, true);
        } catch (\Exception $e) {
            // Table doesn't exist, which is fine
        }

        // Create table using schema builder for database-agnostic DDL
        $schema = $driver->schema();
        $schema->createTable(self::$testTable)
            ->id()
            ->string('name', 255)
            ->string('category', 50)->nullable()
            ->integer('active')->default(1)
            ->execute();
    }

    /**
     * Set up before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear booted models cache
        TestItem::clearBootedModels();

        // Purge query cache
        Query::purgeCache();
    }

    /**
     * Clean up test data before each test
     *
     * Truncate the table to remove all rows and reset auto-increment.
     *
     * @param string $dbName Database name
     * @param Driver $driver Database driver
     * @return void
     */
    private function cleanupData(string $dbName, Driver $driver): void
    {
        try {
            // Truncate table to delete all rows and reset auto-increment
            $driver->truncateTable(self::$testTable);
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    /**
     * Insert test records
     *
     * @param Driver $driver Database driver
     * @param int $count Number of records to insert
     * @return void
     */
    private function insertRecords(Driver $driver, int $count): void
    {
        $rows = [];
        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'name' => "Item {$i}",
                'category' => 'cat' . ($i % 3),
                'active' => $i % 2
            ];
        }

        // Use Query's insertMany for efficiency
        $query = new Query($driver);
        $query->insertMany(self::$testTable, $rows);
    }

    // =========================================================================
    // chunk() Tests
    // =========================================================================

    /**
     * Test chunk processes all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkProcessesAllRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 25);

        $processed = [];
        TestItem::all()->chunk(10, function ($items) use (&$processed) {
            foreach ($items as $item) {
                $processed[] = $item->id;
            }
        });

        $this->assertCount(25, $processed);
        sort($processed);
        $this->assertEquals(range(1, 25), $processed);
    }

    /**
     * Test chunk can stop early by returning false
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkStopsOnFalse(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 50);

        $processed = 0;
        $result = TestItem::all()->chunk(10, function ($items) use (&$processed) {
            $processed += count($items);
            if ($processed >= 20) {
                return false;
            }
        });

        $this->assertFalse($result);
        $this->assertEquals(20, $processed);
    }

    /**
     * Test chunk returns true when all processed
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkReturnsTrueWhenComplete(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 15);

        $result = TestItem::all()->chunk(10, function ($items) {
            // Do nothing
        });

        $this->assertTrue($result);
    }

    /**
     * Test chunk validates chunk size
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkValidatesChunkSize(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $this->expectException(\InvalidArgumentException::class);
        TestItem::all()->chunk(0, function ($items) {
        });
    }

    /**
     * Test chunk works with query constraints
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkWithQueryConstraints(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 20);

        $processed = [];
        TestItem::all()->whereEquals('active', 1)->chunk(5, function ($items) use (&$processed) {
            foreach ($items as $item) {
                $processed[] = $item->id;
            }
        });

        // Only odd IDs are active (active = id % 2)
        $this->assertCount(10, $processed);
        foreach ($processed as $id) {
            $this->assertEquals(1, $id % 2, "ID $id should be odd (active)");
        }
    }

    // =========================================================================
    // chunkById() Tests
    // =========================================================================

    /**
     * Test chunkById processes all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkByIdProcessesAllRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 25);

        $processed = [];
        TestItem::all()->chunkById(10, function ($items) use (&$processed) {
            foreach ($items as $item) {
                $processed[] = $item->id;
            }
        });

        $this->assertCount(25, $processed);
        $this->assertEquals(range(1, 25), $processed);
    }

    /**
     * Test chunkById stops on false
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkByIdStopsOnFalse(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 50);

        $processed = 0;
        $result = TestItem::all()->chunkById(10, function ($items) use (&$processed) {
            $processed += count($items);
            if ($processed >= 20) {
                return false;
            }
        });

        $this->assertFalse($result);
        $this->assertEquals(20, $processed);
    }

    /**
     * Test chunkById with custom column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkByIdWithCustomColumn(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 15);

        $processed = [];
        TestItem::all()->chunkById(5, function ($items) use (&$processed) {
            foreach ($items as $item) {
                $processed[] = $item->id;
            }
        }, 'id');

        $this->assertCount(15, $processed);
        $this->assertEquals(range(1, 15), $processed);
    }

    /**
     * Test chunkById validates chunk size
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChunkByIdValidatesChunkSize(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $this->expectException(\InvalidArgumentException::class);
        TestItem::all()->chunkById(0, function ($items) {
        });
    }

    // =========================================================================
    // cursor() Tests (generator-based iteration)
    // =========================================================================

    /**
     * Test cursor returns a Generator
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCursorReturnsGenerator(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 5);

        $result = TestItem::all()->cursor();

        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * Test cursor iterates all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCursorIteratesAllRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 25);

        $processed = [];
        foreach (TestItem::all()->cursor(10) as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(25, $processed);
        // Verify all IDs are unique
        $this->assertCount(25, array_unique($processed));
        // Verify IDs are in ascending order
        $sorted = $processed;
        sort($sorted);
        $this->assertEquals($sorted, $processed);
    }

    /**
     * Test cursor with query constraints
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCursorWithQueryConstraints(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 20);

        $processed = [];
        foreach (TestItem::all()->whereEquals('active', 1)->cursor(5) as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(10, $processed);
    }

    /**
     * Test cursor with empty results
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCursorWithEmptyResults(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $count = 0;
        foreach (TestItem::all()->cursor() as $item) {
            $count++;
        }

        $this->assertEquals(0, $count);
    }

    /**
     * Test cursor validates chunk size
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCursorValidatesChunkSize(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $this->expectException(\InvalidArgumentException::class);
        iterator_to_array(TestItem::all()->cursor(0));
    }

    // =========================================================================
    // lazy() Tests (alias for cursor with different default chunk size)
    // =========================================================================

    /**
     * Test lazy returns a Generator
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyReturnsGenerator(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 5);

        $result = TestItem::all()->lazy();

        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * Test lazy iterates all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyIteratesAllRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 25);

        $processed = [];
        foreach (TestItem::all()->lazy(10) as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(25, $processed);
        // Verify all IDs are unique
        $this->assertCount(25, array_unique($processed));
        // Verify IDs are in ascending order
        $sorted = $processed;
        sort($sorted);
        $this->assertEquals($sorted, $processed);
    }

    /**
     * Test lazy with query constraints
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyWithQueryConstraints(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 20);

        $processed = [];
        foreach (TestItem::all()->whereEquals('active', 1)->lazy(5) as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(10, $processed);
    }

    /**
     * Test lazy with empty results
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyWithEmptyResults(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $count = 0;
        foreach (TestItem::all()->lazy() as $item) {
            $count++;
        }

        $this->assertEquals(0, $count);
    }

    /**
     * Test lazy validates chunk size
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyValidatesChunkSize(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $this->expectException(\InvalidArgumentException::class);
        iterator_to_array(TestItem::all()->lazy(0));
    }

    // =========================================================================
    // lazyById() Tests
    // =========================================================================

    /**
     * Test lazyById returns a Generator
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyByIdReturnsGenerator(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 5);

        $result = TestItem::all()->lazyById();

        $this->assertInstanceOf(\Generator::class, $result);
    }

    /**
     * Test lazyById iterates all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyByIdIteratesAllRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 25);

        $processed = [];
        foreach (TestItem::all()->lazyById(10) as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(25, $processed);
        // Verify all IDs are unique
        $this->assertCount(25, array_unique($processed));
        // Verify IDs are in ascending order
        $sorted = $processed;
        sort($sorted);
        $this->assertEquals($sorted, $processed);
    }

    /**
     * Test lazyById with custom column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyByIdWithCustomColumn(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertRecords($driver, 15);

        $processed = [];
        foreach (TestItem::all()->lazyById(5, 'id') as $item) {
            $processed[] = $item->id;
        }

        $this->assertCount(15, $processed);
        $this->assertEquals(range(1, 15), $processed);
    }

    /**
     * Test lazyById validates chunk size
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLazyByIdValidatesChunkSize(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $this->expectException(\InvalidArgumentException::class);
        iterator_to_array(TestItem::all()->lazyById(0));
    }
}
