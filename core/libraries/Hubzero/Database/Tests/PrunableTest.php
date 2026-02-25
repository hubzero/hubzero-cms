<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\PrunableLog;
use Hubzero\Database\Tests\TestModels\MassPrunableSession;
use Hubzero\Database\Tests\TestModels\PrunableOrder;

/**
 * Prunable traits tests
 *
 * Tests for the Prunable and MassPrunable traits that allow automatic
 * cleanup of old database records.
 */
class PrunableTest extends AbstractDriverTestCase
{
    /**
     * Track pruning events for testing
     *
     * @var array
     */
    protected static $eventLog = [];

    /**
     * Register the Event stub autoloader if needed.
     * Called once before the first test in this class.
     */
    public static function setUpBeforeClass(): void
    {
        // Mock Event facade for tests - MassPrunable trait calls \Hubzero\Facades\Event::trigger()
        // Using spl_autoload_register to intercept Event class loading
        if (!class_exists('Event', false)) {
            spl_autoload_register(function ($class) {
                if ($class === 'Event') {
                    // phpcs:ignore Squiz.PHP.Eval
                    eval(
                        'class Event { '
                        . 'public static function trigger($event, $args = []) '
                        . '{ return []; } }'
                    );
                }
            }, true, true);
        }

        parent::setUpBeforeClass();
    }

    protected static function getTestTables(): array
    {
        return ['prunable_orders', 'prunable_sessions', 'prunable_logs'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Create test table for prunable records
        $driver->dropTable('prunable_logs', true);
        $driver->createTable('prunable_logs')
            ->id()
            ->string('message', 255)
            ->string('level', 50)->default('info')
            ->string('created', 50)
            ->execute();

        // Create test table for mass prunable records
        $driver->dropTable('prunable_sessions', true);
        $driver->createTable('prunable_sessions')
            ->id()
            ->integer('user_id')
            ->string('token', 100)
            ->string('expires_at', 50)
            ->execute();

        // Create test table for prunable with cleanup
        $driver->dropTable('prunable_orders', true);
        $driver->createTable('prunable_orders')
            ->id()
            ->string('order_number', 50)
            ->string('status', 50)->default('pending')
            ->decimal('total', 10, 2)
            ->string('created', 50)
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        PrunableLog::clearBootedModels();
        MassPrunableSession::clearBootedModels();
        PrunableOrder::clearBootedModels();
        Query::purgeCache();

        self::$eventLog = [];

        $driver->truncateTable('prunable_logs');
        $driver->truncateTable('prunable_sessions');
        $driver->truncateTable('prunable_orders');
    }

    /**
     * Log event for testing
     */
    public static function logEvent($event, $model)
    {
        self::$eventLog[] = [
            'event' => $event,
            'model_id' => $model->get('id'),
        ];
    }

    // =========================================================================
    // Prunable Trait Tests
    // =========================================================================

    /**
     * Test that Prunable trait exists and can be used
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPrunableTraitExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $this->assertTrue(trait_exists(\Hubzero\Database\Traits\Prunable::class));
    }

    /**
     * Test pruneAll() deletes old records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPruneAllDeletesOldRecords(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert old logs (should be pruned - older than 30 days)
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Old log 1', 'level' => 'info', 'created' => '2020-01-01 00:00:00'],
            ['message' => 'Old log 2', 'level' => 'info', 'created' => '2020-01-02 00:00:00'],
            ['message' => 'Old log 3', 'level' => 'info', 'created' => '2020-01-03 00:00:00'],
        ]);

        // Insert recent logs (should NOT be pruned)
        $recentDate = date('Y-m-d H:i:s', strtotime('-1 day'));
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Recent log 1', 'level' => 'info', 'created' => $recentDate],
            ['message' => 'Recent log 2', 'level' => 'info', 'created' => $recentDate],
        ]);

        // Before pruning: 5 total records
        $query = new Query($driver);
        $count = $query->from('prunable_logs')->count();
        $this->assertEquals(5, $count);

        // Prune old records
        $pruned = PrunableLog::pruneAll();

        // Should have pruned 3 old records
        $this->assertEquals(3, $pruned);

        // After pruning: 2 recent records remain
        $query = new Query($driver);
        $count = $query->from('prunable_logs')->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test pruneAll() returns zero when no records match
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPruneAllReturnsZeroWhenNoMatches(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert only recent logs
        $recentDate = date('Y-m-d H:i:s', strtotime('-1 day'));
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Recent log 1', 'level' => 'info', 'created' => $recentDate],
            ['message' => 'Recent log 2', 'level' => 'info', 'created' => $recentDate],
        ]);

        $pruned = PrunableLog::pruneAll();

        $this->assertEquals(0, $pruned);

        // All records should still exist
        $query = new Query($driver);
        $count = $query->from('prunable_logs')->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test Prunable works with chunk size parameter
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPruneAllWithCustomChunkSize(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert many old logs
        $logs = [];
        for ($i = 0; $i < 50; $i++) {
            $logs[] = [
                'message' => "Old log $i",
                'level' => 'info',
                'created' => '2020-01-01 00:00:00',
            ];
        }
        $query = new Query($driver);
        $query->insertMany('prunable_logs', $logs);

        // Prune with small chunk size
        $pruned = PrunableLog::pruneAll(10);

        $this->assertEquals(50, $pruned);

        // All old records should be deleted
        $query = new Query($driver);
        $count = $query->from('prunable_logs')->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Test prunable() method is abstract and must be implemented
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPrunableMethodMustBeImplemented(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // The prunable() method is protected, so we verify via reflection
        $reflection = new \ReflectionClass(PrunableLog::class);
        $method = $reflection->getMethod('prunable');

        // Verify it exists and is protected (to avoid relationship introspection)
        $this->assertTrue($method->isProtected());

        // Verify it returns a query via calling pruneAll (which uses prunable internally)
        // Insert a record that won't be pruned (recent)
        $recentDate = date('Y-m-d H:i:s');
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Test log', 'level' => 'info', 'created' => $recentDate],
        ]);

        // This should work without errors, proving prunable() is properly implemented
        $pruned = PrunableLog::pruneAll();
        $this->assertEquals(0, $pruned);
    }

    // =========================================================================
    // MassPrunable Trait Tests
    // =========================================================================

    /**
     * Test that MassPrunable trait exists and can be used
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMassPrunableTraitExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $this->assertTrue(trait_exists(\Hubzero\Database\Traits\MassPrunable::class));
    }

    /**
     * Test MassPrunable pruneAll() deletes records in bulk
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMassPrunablePruneAllDeletesInBulk(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert expired sessions (should be pruned)
        $query = new Query($driver);
        $query->insertMany('prunable_sessions', [
            ['user_id' => 1, 'token' => md5('token1'), 'expires_at' => '2020-01-01 00:00:00'],
            ['user_id' => 2, 'token' => md5('token2'), 'expires_at' => '2020-01-02 00:00:00'],
            ['user_id' => 3, 'token' => md5('token3'), 'expires_at' => '2020-01-03 00:00:00'],
        ]);

        // Insert valid sessions (should NOT be pruned)
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));
        $query = new Query($driver);
        $query->insertMany('prunable_sessions', [
            ['user_id' => 4, 'token' => md5('token4'), 'expires_at' => $futureDate],
            ['user_id' => 5, 'token' => md5('token5'), 'expires_at' => $futureDate],
        ]);

        // Before pruning: 5 total records
        $query = new Query($driver);
        $count = $query->from('prunable_sessions')->count();
        $this->assertEquals(5, $count);

        // Prune expired sessions
        $pruned = MassPrunableSession::pruneAll();

        // Should have pruned 3 expired sessions
        $this->assertEquals(3, $pruned);

        // After pruning: 2 valid sessions remain
        $query = new Query($driver);
        $count = $query->from('prunable_sessions')->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test MassPrunable handles large datasets efficiently
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMassPrunableHandlesLargeDatasets(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert many expired sessions
        $sessions = [];
        for ($i = 0; $i < 100; $i++) {
            $sessions[] = [
                'user_id' => $i,
                'token' => md5("token$i"),
                'expires_at' => '2020-01-01 00:00:00',
            ];
        }
        $query = new Query($driver);
        $query->insertMany('prunable_sessions', $sessions);

        // This should be a single DELETE query, not 100 separate deletes
        $pruned = MassPrunableSession::pruneAll();

        $this->assertEquals(100, $pruned);

        // All records should be deleted
        $query = new Query($driver);
        $count = $query->from('prunable_sessions')->count();
        $this->assertEquals(0, $count);
    }

    // =========================================================================
    // Prunable with Additional Conditions Tests
    // =========================================================================

    /**
     * Test Prunable with complex query conditions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPrunableWithComplexConditions(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $now = date('Y-m-d H:i:s');

        // Insert orders with different statuses and dates
        $query = new Query($driver);
        $query->insertMany('prunable_orders', [
            [
                'order_number' => 'ORD-001',
                'status' => 'completed',
                'total' => 100.00,
                'created' => '2020-01-01 00:00:00',
            ],
            [
                'order_number' => 'ORD-002',
                'status' => 'completed',
                'total' => 200.00,
                'created' => '2020-01-02 00:00:00',
            ],
            [
                'order_number' => 'ORD-003',
                'status' => 'pending',
                'total' => 300.00,
                'created' => '2020-01-03 00:00:00',
            ],
            [
                'order_number' => 'ORD-004',
                'status' => 'completed',
                'total' => 400.00,
                'created' => $now,
            ],
        ]);

        // PrunableOrder only prunes completed orders older than 30 days
        $pruned = PrunableOrder::pruneAll();

        $this->assertEquals(2, $pruned);

        // 2 records should remain (1 pending old, 1 completed recent)
        $query = new Query($driver);
        $count = $query->from('prunable_orders')->count();
        $this->assertEquals(2, $count);
    }

    // =========================================================================
    // Event Tests
    // =========================================================================

    /**
     * Test Prunable fires standard deleting/deleted events
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPrunableFiresStandardEvents(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Register event callbacks for standard events
        PrunableLog::deleting(function ($model) {
            PrunableTest::logEvent('deleting', $model);
        });

        PrunableLog::deleted(function ($model) {
            PrunableTest::logEvent('deleted', $model);
        });

        // Insert and prune a log
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Test log', 'level' => 'info', 'created' => '2020-01-01 00:00:00'],
        ]);

        $pruned = PrunableLog::pruneAll();

        $this->assertEquals(1, $pruned);

        // Check standard events were fired
        $this->assertCount(2, self::$eventLog);
        $this->assertEquals('deleting', self::$eventLog[0]['event']);
        $this->assertEquals(1, self::$eventLog[0]['model_id']);
        $this->assertEquals('deleted', self::$eventLog[1]['event']);
        $this->assertEquals(1, self::$eventLog[1]['model_id']);
    }

    /**
     * Test deleting event can cancel pruning
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeletingEventCanCancelPrune(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Register event that cancels deletion for specific records
        PrunableLog::deleting(function ($model) {
            // Cancel if level is 'error'
            if ($model->get('level') === 'error') {
                return false;
            }
        });

        // Insert logs with different levels
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Info log', 'level' => 'info', 'created' => '2020-01-01 00:00:00'],
            ['message' => 'Error log', 'level' => 'error', 'created' => '2020-01-01 00:00:00'],
        ]);

        $pruned = PrunableLog::pruneAll();

        // Only info log should be pruned
        $this->assertEquals(1, $pruned);

        // Error log should still exist
        $query = new Query($driver);
        $count = $query->from('prunable_logs')->where('level', '=', 'error')->count();
        $this->assertEquals(1, $count);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test pruneAll on empty table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPruneAllOnEmptyTable(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $pruned = PrunableLog::pruneAll();
        $this->assertEquals(0, $pruned);
    }

    /**
     * Test pruning does not affect records outside the query
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPruningDoesNotAffectOtherRecords(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert logs
        $query = new Query($driver);
        $query->insertMany('prunable_logs', [
            ['message' => 'Old info', 'level' => 'info', 'created' => '2020-01-01 00:00:00'],
            ['message' => 'Recent info', 'level' => 'info', 'created' => date('Y-m-d H:i:s')],
        ]);

        // Insert sessions (different table)
        $query = new Query($driver);
        $query->insertMany('prunable_sessions', [
            ['user_id' => 1, 'token' => md5('token1'), 'expires_at' => '2020-01-01 00:00:00'],
        ]);

        // Prune logs
        PrunableLog::pruneAll();

        // Sessions should be unaffected
        $query = new Query($driver);
        $count = $query->from('prunable_sessions')->count();
        $this->assertEquals(1, $count);
    }
}
