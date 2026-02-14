<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;

/**
 * Tests for UNION and UNION ALL queries
 *
 * Tests the union() and unionAll() methods that combine result sets
 * from multiple SELECT queries.
 */
class UnionQueryTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['union_users', 'union_admins', 'union_logs', 'union_archived_logs'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created per-test in setupTables(), so just ensure they don't exist
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    // =========================================================================
    // Basic Union Tests
    // =========================================================================

    /**
     * Test union() method exists and returns self for chaining
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionMethodExistsAndReturnsQuery(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'union'));

        $query->select('name')->from('union_users');
        $result = $query->union(function ($q) {
            $q->select('name')->from('union_admins');
        });

        $this->assertSame($query, $result);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test unionAll() method exists and returns self for chaining
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionAllMethodExistsAndReturnsQuery(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'unionAll'));

        $query->select('name')->from('union_users');
        $result = $query->unionAll(function ($q) {
            $q->select('name')->from('union_admins');
        });

        $this->assertSame($query, $result);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test basic union combines result sets
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function basicUnionCombinesResultSets(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // Get user names unioned with admin names
        $results = $query->select('name')
            ->from('union_users')
            ->union(function ($q) {
                $q->select('name')->from('union_admins');
            })
            ->fetch('rows');

        // Should have 5 distinct names (Alice, Bob, Charlie, David, Eve)
        $this->assertCount(5, $results);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test union with Query object (not closure)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionWithQueryObject(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $adminQuery = new Query($driver);
        $adminQuery->select('name')->from('union_admins');

        $query = new Query($driver);
        $results = $query->select('name')
            ->from('union_users')
            ->union($adminQuery)
            ->fetch('rows');

        $this->assertCount(5, $results);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test union removes duplicate rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionRemovesDuplicates(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // Error logs from both tables - "Error 1" exists in both
        $results = $query->select('message')
            ->from('union_logs')
            ->whereEquals('level', 'error')
            ->union(function ($q) {
                $q->select('message')
                    ->from('union_archived_logs')
                    ->whereEquals('level', 'error');
            })
            ->fetch('rows');

        // Should have 2 distinct messages: "Error 1" and "Old Error"
        $this->assertCount(2, $results);

        // Cleanup
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test unionAll keeps duplicate rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionAllKeepsDuplicates(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // Error logs from both tables - should include duplicate "Error 1"
        $results = $query->select('message')
            ->from('union_logs')
            ->whereEquals('level', 'error')
            ->unionAll(function ($q) {
                $q->select('message')
                    ->from('union_archived_logs')
                    ->whereEquals('level', 'error');
            })
            ->fetch('rows');

        // Should have 3 rows: "Error 1" (twice) and "Old Error"
        $this->assertCount(3, $results);

        // Cleanup
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    // =========================================================================
    // Multiple Union Tests
    // =========================================================================

    /**
     * Test multiple unions in same query
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleUnionsInSameQuery(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        $results = $query->select('message')
            ->from('union_logs')
            ->whereEquals('level', 'error')
            ->union(function ($q) {
                $q->select('message')
                    ->from('union_logs')
                    ->whereEquals('level', 'info');
            })
            ->union(function ($q) {
                $q->select('message')
                    ->from('union_archived_logs');
            })
            ->fetch('rows');

        // Should have distinct messages from all queries
        // "Error 1", "Info 1", "Old Error" = 3 distinct
        $this->assertCount(3, $results);

        // Cleanup
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test mixed union and unionAll in same query
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function mixedUnionAndUnionAll(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // This tests the SQL generation with mixed UNION and UNION ALL
        $results = $query->select('message')
            ->from('union_logs')
            ->whereEquals('level', 'error')
            ->unionAll(function ($q) {
                $q->select('message')
                    ->from('union_archived_logs')
                    ->whereEquals('level', 'error');
            })
            ->fetch('rows');

        // UNION ALL should keep duplicates
        $this->assertCount(3, $results);

        // Cleanup
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    // =========================================================================
    // Where Clause Tests
    // =========================================================================

    /**
     * Test union with where clauses filters correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionWithWhereClausesFiltersCorrectly(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // Active users union with moderators
        $results = $query->select('name', 'type')
            ->from('union_users')
            ->whereEquals('active', 1)
            ->whereEquals('type', 'user')
            ->union(function ($q) {
                $q->select('name', 'type')
                    ->from('union_users')
                    ->whereEquals('type', 'moderator');
            })
            ->fetch('rows');

        // Should have Alice (active user) and Bob (moderator)
        $this->assertCount(2, $results);

        // Cleanup
        $driver->dropTable('union_users', true);
    }

    /**
     * Test union with parameter bindings
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionWithParameterBindings(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        // Test that bindings work correctly with unions
        $results = $query->select('name')
            ->from('union_users')
            ->where('name', 'LIKE', 'A%')
            ->union(function ($q) {
                $q->select('name')
                    ->from('union_admins')
                    ->where('name', 'LIKE', 'D%');
            })
            ->fetch('rows');

        // Should have Alice and David
        $this->assertCount(2, $results);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test union with empty result set in first query
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionWithEmptyFirstResultSet(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        $results = $query->select('name')
            ->from('union_users')
            ->whereEquals('name', 'NonExistent')
            ->union(function ($q) {
                $q->select('name')
                    ->from('union_admins');
            })
            ->fetch('rows');

        // Should have 2 admins (David, Eve)
        $this->assertCount(2, $results);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test union with both empty result sets
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionWithBothEmptyResultSets(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);
        $this->seedTestData($driver);

        $query = new Query($driver);

        $results = $query->select('name')
            ->from('union_users')
            ->whereEquals('name', 'NonExistent')
            ->union(function ($q) {
                $q->select('name')
                    ->from('union_admins')
                    ->whereEquals('name', 'AlsoNonExistent');
            })
            ->fetch('rows');

        $this->assertCount(0, $results);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test toString() generates SQL with UNION keyword
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function toStringGeneratesSqlWithUnion(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);

        $query = new Query($driver);

        $query->select('name')
            ->from('union_users')
            ->union(function ($q) {
                $q->select('name')->from('union_admins');
            });

        $sql = $query->toString();

        // Verify SQL contains UNION keyword
        $this->assertStringContainsString('UNION', $sql);
        $this->assertStringContainsString('union_users', strtolower($sql));
        $this->assertStringContainsString('union_admins', strtolower($sql));

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    /**
     * Test toString() generates SQL with UNION ALL keyword
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function toStringGeneratesSqlWithUnionAll(string $dbName, Driver $driver)
    {
        $this->setupTables($driver);

        $query = new Query($driver);

        $query->select('name')
            ->from('union_users')
            ->unionAll(function ($q) {
                $q->select('name')->from('union_admins');
            });

        $sql = $query->toString();

        // Verify SQL contains UNION ALL keyword
        $this->assertStringContainsString('UNION ALL', $sql);

        // Cleanup - drop all tables created by setupTables()
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test tables
     */
    private function setupTables(Driver $driver): void
    {
        // Drop existing tables
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);

        // Create tables using schema builder
        $schema = $driver->schema();

        $schema->createTable('union_users')
            ->id()
            ->string('name', 100)
            ->string('type', 50)
            ->integer('active')->default(1)
            ->execute();

        $schema->createTable('union_admins')
            ->id()
            ->string('name', 100)
            ->string('level', 50)
            ->execute();

        $schema->createTable('union_logs')
            ->id()
            ->string('message', 255)
            ->string('level', 50)
            ->execute();

        $schema->createTable('union_archived_logs')
            ->id()
            ->string('message', 255)
            ->string('level', 50)
            ->execute();
    }

    /**
     * Seed test data
     */
    private function seedTestData(Driver $driver): void
    {
        // Insert users
        $query = new Query($driver);
        $query->insertMany('union_users', [
            ['name' => 'Alice', 'type' => 'user', 'active' => 1],
            ['name' => 'Bob', 'type' => 'moderator', 'active' => 1],
            ['name' => 'Charlie', 'type' => 'user', 'active' => 0],
        ]);

        // Insert admins
        $query = new Query($driver);
        $query->insertMany('union_admins', [
            ['name' => 'David', 'level' => 'super'],
            ['name' => 'Eve', 'level' => 'standard'],
        ]);

        // Insert logs
        $query = new Query($driver);
        $query->insertMany('union_logs', [
            ['message' => 'Error 1', 'level' => 'error'],
            ['message' => 'Info 1', 'level' => 'info'],
        ]);

        // Insert archived logs (with one duplicate message)
        $query = new Query($driver);
        $query->insertMany('union_archived_logs', [
            ['message' => 'Error 1', 'level' => 'error'],
            ['message' => 'Old Error', 'level' => 'error'],
        ]);
    }
}
