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
 * Tests for Adding Primary Keys to Existing Tables
 *
 * These tests verify the functionality of addAutoIncrementPrimaryKey() when
 * adding an id column to tables that were created without one.
 */
class AddPrimaryKeyToExistingTableTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [
            'temp_add_pk', 'temp_custom_pk', 'temp_pk_drop_add',
            'temp_pk_migration', 'temp_add_pk_schema',
        ];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if ($driver->getDriverName() === 'db2') {
            self::markTestSkipped('Not supported on this database');
        }

        // Tables are created/dropped per-test
    }

    /**
     * Test adding auto-increment primary key to existing table
     *
     * This is the core use case: table created without id, add it later.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addAutoIncrementPrimaryKey(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_add_pk', true);

        // Create table WITHOUT id column
        $driver->createTable('temp_add_pk')
            ->string('name', 50)
            ->string('value', 50)
            ->execute();

        $this->assertFalse($driver->tableHasField('temp_add_pk', 'id'));

        // Add auto-increment primary key
        $result = $driver->addAutoIncrementPrimaryKey('temp_add_pk', 'id', true);
        $this->assertTrue($result, 'addAutoIncrementPrimaryKey should return true');

        $this->assertTrue($driver->tableHasField('temp_add_pk', 'id'));

        // Insert data and verify auto-increment works
        $query = new Query($driver);
        $query->insert('temp_add_pk')
            ->values(['name' => 'test1', 'value' => 'val1'])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_add_pk')
            ->values(['name' => 'test2', 'value' => 'val2'])
            ->execute();

        $query = new Query($driver);
        $rows = $query->select('*')
            ->from('temp_add_pk')
            ->order('id', 'asc')
            ->fetch();

        $this->assertCount(2, $rows);
        $this->assertNotEmpty($rows[0]->id);
        $this->assertNotEmpty($rows[1]->id);

        // Clean up
        $driver->dropTable('temp_add_pk', true);
    }

    /**
     * Test addAutoIncrementPrimaryKey with custom column name
     *
     * Tests adding a primary key with a name other than 'id'.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addAutoIncrementPrimaryKeyCustomColumn(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_custom_pk', true);

        // Create table WITHOUT any primary key
        $driver->createTable('temp_custom_pk')
            ->string('name', 50)
            ->execute();

        // Add with custom column name
        $result = $driver->addAutoIncrementPrimaryKey('temp_custom_pk', 'record_id', true);
        $this->assertTrue($result);

        $this->assertTrue($driver->tableHasField('temp_custom_pk', 'record_id'));
        $this->assertFalse($driver->tableHasField('temp_custom_pk', 'id'));

        // Verify auto-increment works with custom name
        $query = new Query($driver);
        $query->insert('temp_custom_pk')
            ->values(['name' => 'test1'])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_custom_pk')
            ->values(['name' => 'test2'])
            ->execute();

        $query = new Query($driver);
        $rows = $query->select('*')
            ->from('temp_custom_pk')
            ->order('record_id', 'asc')
            ->fetch();

        $this->assertCount(2, $rows);
        $this->assertNotEmpty($rows[0]->record_id);
        $this->assertNotEmpty($rows[1]->record_id);

        // Clean up
        $driver->dropTable('temp_custom_pk', true);
    }

    /**
     * Test dropPrimaryKey followed by addAutoIncrementPrimaryKey
     *
     * This pattern: composite PK → drop it → add auto-increment PK
     * Common when migrating from composite keys to surrogate keys.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropPrimaryKeyThenAddAutoIncrement(string $dbName, Driver $driver)
    {
        // Drop table if exists
        $driver->dropTable('temp_pk_drop_add', true);

        // Create table with composite primary key
        $driver->createTable('temp_pk_drop_add')
            ->string('gid_number', 20)
            ->string('uid_number', 20)
            ->string('role', 50)
            ->primaryKey(['gid_number', 'uid_number'])
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('temp_pk_drop_add')
            ->values(['gid_number' => '1', 'uid_number' => '100', 'role' => 'member'])
            ->execute();

        $query = new Query($driver);
        $query->insert('temp_pk_drop_add')
            ->values(['gid_number' => '1', 'uid_number' => '101', 'role' => 'admin'])
            ->execute();

        // Migration pattern: drop primary key first
        $result1 = $driver->dropPrimaryKey('temp_pk_drop_add');
        $this->assertTrue($result1, 'dropPrimaryKey should return true');

        // Then add auto-increment primary key
        $result2 = $driver->addAutoIncrementPrimaryKey('temp_pk_drop_add', 'id', true);
        $this->assertTrue($result2, 'addAutoIncrementPrimaryKey should return true');

        // Verify data is preserved
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('temp_pk_drop_add')
            ->fetch('row');
        $this->assertEquals(2, $count->cnt, 'All data should be preserved');

        // Verify new id column works
        $query = new Query($driver);
        $rows = $query->select('*')
            ->from('temp_pk_drop_add')
            ->order('id', 'asc')
            ->fetch();

        $this->assertNotEmpty($rows[0]->id, 'Rows should have id');
        $this->assertEquals('1', $rows[0]->gid_number, 'Data should be preserved');

        // Clean up
        $driver->dropTable('temp_pk_drop_add', true);
    }

    /**
     * Test changing primary key composition (drop + add)
     *
     * Migrated from SchemaTest.php.
     * Tests DROP PK + ADD different composite PK (change PK columns).
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function primaryKeyColumnMigrationScenario(string $dbName, Driver $driver)
    {
        $schema = $driver->schema();

        // Drop table if exists
        $driver->dropTable('temp_pk_migration', true);

        // Create table with 3-column composite PK
        $driver->createTable('temp_pk_migration')
            ->string('sessnum', 50)
            ->string('job', 50)
            ->string('event', 50)
            ->string('venue', 50)
            ->primaryKey(['sessnum', 'job', 'event'])
            ->execute();

        $this->assertFalse($schema->hasPrimaryKeyColumn('temp_pk_migration', 'venue'));

        // Drop PK and add new 4-column composite PK (adds 'venue')
        $schema->table('temp_pk_migration')->alter()
            ->dropPrimaryKey()
            ->addPrimaryKey(['sessnum', 'job', 'event', 'venue'])
            ->execute();

        $this->assertTrue($schema->hasPrimaryKeyColumn('temp_pk_migration', 'venue'));

        // Cleanup
        $driver->dropTable('temp_pk_migration', true);
    }

    /**
     * Test adding non-auto-increment primary key to existing table
     *
     * Migrated from SchemaTest.php.
     * Tests schema->addPrimaryKey() (non-auto-increment).
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addPrimaryKeyToTableWithoutOne(string $dbName, Driver $driver)
    {
        $schema = $driver->schema();

        // Drop table if exists
        $driver->dropTable('temp_add_pk_schema', true);

        // Create a table without primary key
        $driver->createTable('temp_add_pk_schema')
            ->string('col_a', 50)
            ->string('col_b', 50)
            ->execute();

        // Add primary key (non-auto-increment)
        $result = $schema->addPrimaryKey('temp_add_pk_schema', 'col_a');
        $this->assertTrue($result);

        // Cleanup
        $schema->dropTable('temp_add_pk_schema');
    }
}
