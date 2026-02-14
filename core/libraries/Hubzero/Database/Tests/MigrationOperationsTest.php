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
 * Migration Operations Tests - Safe Patterns
 *
 * Tests database migration patterns that work safely across all databases,
 * including DB2. These tests avoid operations that trigger REORG pending state.
 *
 * Best practices tested:
 * - Creating tables with id column from the start
 * - Idempotent migration patterns
 * - Conditional checks before operations
 * - Composite primary keys
 */
class MigrationOperationsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [
            'temp_idempotent', 'temp_migrate_pattern', 'temp_pk_change',
            'temp_complex_migrate', 'temp_resource_assoc', 'temp_conditional',
        ];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created/dropped per-test
    }

    // =========================================================================
    // Idempotent Operations
    // =========================================================================

    /**
     * Test addAutoIncrementPrimaryKey is idempotent
     *
     * Safe pattern: Create table WITH id, call addAutoIncrementPrimaryKey twice.
     * Should succeed both times (no error when column already exists).
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addAutoIncrementPrimaryKeyIdempotent(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_idempotent', true);

        // Create table WITH id from start (safe on all databases)
        $driver->createTable('temp_idempotent')
            ->id()
            ->string('name', 50)
            ->execute();

        // Both calls should succeed (column already exists)
        $result1 = $driver->addAutoIncrementPrimaryKey('temp_idempotent', 'id', true);
        $this->assertTrue($result1, 'First call should succeed');

        $result2 = $driver->addAutoIncrementPrimaryKey('temp_idempotent', 'id', true);
        $this->assertTrue($result2, 'Second call should also succeed (idempotent)');

        // Clean up
        $driver->dropTable('temp_idempotent', true);
    }

    /**
     * Test addAutoIncrementPrimaryKey on non-existent table returns false
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addAutoIncrementPrimaryKeyNonExistentTable(string $dbName, Driver $driver)
    {
        $result = $driver->addAutoIncrementPrimaryKey('nonexistent_table_xyz', 'id', true);
        $this->assertFalse($result, 'Should return false for non-existent table');
    }

    /**
     * Test common migration pattern: Check column exists before adding
     *
     * Safe pattern: Create WITH id, check if exists, skip adding if present.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function migrationPatternCheckBeforeAdd(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_migrate_pattern', true);

        // Create WITH id from start (safe pattern)
        $driver->createTable('temp_migrate_pattern')
            ->id()
            ->string('name', 50)
            ->string('email', 100)
            ->execute();

        // Migration pattern - check before adding
        if (!$driver->tableHasField('temp_migrate_pattern', 'id')) {
            $driver->addAutoIncrementPrimaryKey('temp_migrate_pattern', 'id', true);
        }

        $this->assertTrue($driver->tableHasField('temp_migrate_pattern', 'id'));

        // Run the same migration pattern again - should not error
        if (!$driver->tableHasField('temp_migrate_pattern', 'id')) {
            $driver->addAutoIncrementPrimaryKey('temp_migrate_pattern', 'id', true);
        }

        $this->assertTrue($driver->tableHasField('temp_migrate_pattern', 'id'));

        // Clean up
        $driver->dropTable('temp_migrate_pattern', true);
    }

    // =========================================================================
    // Primary Key Operations - Safe Patterns
    // =========================================================================

    /**
     * Test creating table with auto-increment primary key from start
     *
     * Recommended pattern: Always create tables with id column.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableWithPrimaryKeyFromStart(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_pk_safe', true);

        // Best practice: Create table WITH primary key from the beginning
        $driver->createTable('temp_pk_safe')
            ->id()  // Auto-increment primary key
            ->string('gid_number', 20)
            ->string('uid_number', 20)
            ->string('role', 50)
            ->execute();

        // Verify id column was created
        $this->assertTrue($driver->tableHasField('temp_pk_safe', 'id'));

        // Insert test data
        $query = new Query($driver);
        $query->insert('temp_pk_safe')
            ->values(['gid_number' => '1', 'uid_number' => '100', 'role' => 'member'])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_pk_safe')
            ->values(['gid_number' => '1', 'uid_number' => '101', 'role' => 'admin'])
            ->execute();

        // Verify auto-increment works
        $query = new Query($driver);
        $rows = $query->select('*')
            ->from('temp_pk_safe')
            ->order('id', 'asc')
            ->fetch();

        $this->assertCount(2, $rows);
        $this->assertNotEmpty($rows[0]->id);
        $this->assertNotEmpty($rows[1]->id);
        $this->assertEquals('1', $rows[0]->gid_number);

        // Clean up
        $driver->dropTable('temp_pk_safe', true);
    }

    /**
     * Test creating table with composite primary key
     *
     * Safe on all databases.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableWithCompositePrimaryKey(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_pk_change', true);

        // Create table with composite primary key
        $driver->createTable('temp_pk_change')
            ->string('gid_number', 20)
            ->string('uid_number', 20)
            ->string('role', 50)
            ->primaryKey(['gid_number', 'uid_number'])
            ->execute();

        // Verify table was created
        $this->assertTrue($driver->tableExists('temp_pk_change'));
        $this->assertTrue($driver->tableHasField('temp_pk_change', 'gid_number'));
        $this->assertTrue($driver->tableHasField('temp_pk_change', 'uid_number'));

        // Insert test data - composite key should enforce uniqueness
        $query = new Query($driver);
        $query->insert('temp_pk_change')
            ->values(['gid_number' => '1', 'uid_number' => '100', 'role' => 'member'])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_pk_change')
            ->values(['gid_number' => '1', 'uid_number' => '101', 'role' => 'admin'])
            ->execute();

        // Verify data
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('temp_pk_change')
            ->fetch('row');
        $this->assertEquals(2, $count->cnt);

        // Clean up
        $driver->dropTable('temp_pk_change', true);
    }

    // =========================================================================
    // Complex Migration Scenarios - Safe Patterns
    // =========================================================================

    /**
     * Test complex migration combining multiple operations
     *
     * Safe pattern: Create WITH id, add other modifications.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function complexMigrationScenario(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_complex_migrate', true);

        // Best practice: Create WITH id from start
        $driver->createTable('temp_complex_migrate')
            ->id()
            ->integer('author_id')
            ->integer('resource_id')
            ->integer('ordering')
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('temp_complex_migrate')
            ->values(['author_id' => 1, 'resource_id' => 100, 'ordering' => 1])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_complex_migrate')
            ->values(['author_id' => 2, 'resource_id' => 100, 'ordering' => 2])
            ->execute();

        // Migration logic
        if ($driver->tableExists('temp_complex_migrate')) {
            // Check - id already exists
            $needsId = !$driver->tableHasField('temp_complex_migrate', 'id');
            $this->assertFalse($needsId, 'Table created with id from start');

            // Add unique index (safe operation)
            if (!$driver->tableHasKey('temp_complex_migrate', 'uidx_author_resource')) {
                $driver->alterTable('temp_complex_migrate')
                    ->addUniqueIndex('uidx_author_resource', ['author_id', 'resource_id'])
                    ->execute();
            }
        }

        // Verify results
        $this->assertTrue($driver->tableHasField('temp_complex_migrate', 'id'), 'Should have id column');
        $this->assertTrue(
            $driver->tableHasKey('temp_complex_migrate', 'uidx_author_resource'),
            'Should have unique index'
        );

        // Verify data preserved
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('temp_complex_migrate')
            ->fetch('row');
        $this->assertEquals(2, $count->cnt, 'Data should be preserved');

        // Clean up
        $driver->dropTable('temp_complex_migrate', true);
    }

    /**
     * Test real-world migration pattern - safe version
     *
     * Based on Migration20141112203716ComResources but using safe pattern.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function realWorldMigrationPattern(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_resource_assoc', true);

        // Simulate #__resource_assoc table WITH id from the start
        $driver->createTable('temp_resource_assoc')
            ->id()
            ->integer('parent_id')
            ->integer('child_id')
            ->integer('ordering')
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('temp_resource_assoc')
            ->values(['parent_id' => 1, 'child_id' => 10, 'ordering' => 1])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_resource_assoc')
            ->values(['parent_id' => 1, 'child_id' => 11, 'ordering' => 2])
            ->execute();

        // Migration pattern - check if id exists first
        if ($driver->tableExists('temp_resource_assoc') && !$driver->tableHasField('temp_resource_assoc', 'id')) {
            // This won't execute because we created table with id
            $driver->addAutoIncrementPrimaryKey('temp_resource_assoc', 'id', true);
        }

        // Verify
        $this->assertTrue($driver->tableHasField('temp_resource_assoc', 'id'), 'id column should exist');

        // Verify data integrity
        $query = new Query($driver);
        $rows = $query->select('*')
            ->from('temp_resource_assoc')
            ->order('ordering', 'asc')
            ->fetch();

        $this->assertCount(2, $rows);
        $this->assertEquals(1, $rows[0]->parent_id);
        $this->assertEquals(10, $rows[0]->child_id);
        $this->assertNotEmpty($rows[0]->id);

        // Clean up
        $driver->dropTable('temp_resource_assoc', true);
    }

    /**
     * Test migration with conditional logic based on table state
     *
     * Safe pattern: Create WITH id, only add additional columns.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function migrationWithConditionalLogic(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_conditional', true);

        // Create WITH id from start (safe)
        $driver->createTable('temp_conditional')
            ->id()
            ->string('name', 100)
            ->execute();

        // Conditional migration logic
        $needsId = !$driver->tableHasField('temp_conditional', 'id');
        $needsEmail = !$driver->tableHasField('temp_conditional', 'email');

        $this->assertFalse($needsId, 'Should already have id');
        $this->assertTrue($needsEmail, 'Should need email');

        if ($needsId) {
            // Won't execute
            $driver->addAutoIncrementPrimaryKey('temp_conditional', 'id', true);
        }

        if ($needsEmail) {
            $driver->alterTable('temp_conditional')
                ->addColumn('email')->string(255)->nullable()
                ->execute();
        }

        // Verify both columns exist
        $this->assertTrue($driver->tableHasField('temp_conditional', 'id'));
        $this->assertTrue($driver->tableHasField('temp_conditional', 'email'));

        // Running again should not error
        $needsId = !$driver->tableHasField('temp_conditional', 'id');
        $needsEmail = !$driver->tableHasField('temp_conditional', 'email');

        $this->assertFalse($needsId, 'Should not need id on second run');
        $this->assertFalse($needsEmail, 'Should not need email on second run');

        // Clean up
        $driver->dropTable('temp_conditional', true);
    }

    /**
     * Test that table created with id from start works correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableWithIdFromStart(string $dbName, Driver $driver)
    {
        $tableName = 'temp_id_from_start';

        // Drop table if exists
        $driver->dropTable($tableName, true);

        // Best practice: Create table WITH id from the beginning
        $driver->createTable($tableName)
            ->id()
            ->string('name', 100)
            ->timestamps()
            ->execute();

        // Verify structure
        $this->assertTrue($driver->tableHasField($tableName, 'id'));
        $this->assertTrue($driver->tableHasField($tableName, 'name'));
        $this->assertTrue($driver->tableHasField($tableName, 'created'));
        $this->assertTrue($driver->tableHasField($tableName, 'modified'));

        // Insert data
        $query = new Query($driver);
        $query->insert($tableName)
            ->values(['name' => 'Test Item 1'])
            ->execute();

        $query = new Query($driver);
        $query->insert($tableName)
            ->values(['name' => 'Test Item 2'])
            ->execute();

        // Verify auto-increment works
        $query = new Query($driver);
        $rows = $query->select('*')
            ->from($tableName)
            ->order('id', 'asc')
            ->fetch();

        $this->assertCount(2, $rows);
        $this->assertNotEmpty($rows[0]->id);
        $this->assertNotEmpty($rows[1]->id);
        $this->assertEquals('Test Item 1', $rows[0]->name);
        $this->assertEquals('Test Item 2', $rows[1]->name);

        // Clean up
        $driver->dropTable($tableName, true);
    }
}
