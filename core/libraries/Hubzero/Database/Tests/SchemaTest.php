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
use Hubzero\Database\SchemaManager;
use Hubzero\Database\Schema\Table;
use Hubzero\Database\Schema\TableBuilder;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Query;

/**
 * Schema abstraction layer tests
 *
 * Tests the SchemaManager class and its methods for database-agnostic
 * schema operations across multiple database backends.
 */
class SchemaTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [
            'users', 'posts',
            'temp_drop_test', 'temp_rename_source', 'temp_rename_target',
            'temp_index_test', 'temp_multi_idx', 'temp_unique_idx',
            'temp_fulltext', 'temp_drop_idx', 'temp_add_col',
            'temp_drop_col', 'temp_rename_col', 'temp_auto_test',
            'temp_setauto_test', 'temp_drop_pk', 'temp_add_pk',
            'temp_pk_cols', 'temp_pk_migration',
        ];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop test tables if they exist
        $driver->dropTable('users', true);
        $driver->dropTable('posts', true);

        // Create users table
        $driver->createTable('users')
            ->id()
            ->string('name', 100)
            ->string('email', 255)
            ->execute();

        // Create posts table
        $driver->createTable('posts')
            ->id()
            ->string('title', 255)
            ->text('content')
            ->integer('user_id')
            ->execute();
    }

    /**
     * Clean up temporary tables for a specific database and ensure base tables exist
     *
     * @param string $dbName Database name
     * @param Driver $driver Driver instance
     */
    private function cleanupTempTables(string $dbName, Driver $driver): void
    {
        $tempTables = [
            'temp_drop_test',
            'temp_rename_source',
            'temp_rename_target',
            'temp_index_test',
            'temp_multi_idx',
            'temp_unique_idx',
            'temp_fulltext',
            'temp_drop_idx',
            'temp_add_col',
            'temp_drop_col',
            'temp_rename_col',
            'temp_auto_test',
            'temp_setauto_test',
            'temp_drop_pk',
            'temp_add_pk',
            'temp_pk_cols',
            'temp_pk_migration',
        ];

        foreach ($tempTables as $table) {
            $driver->dropTable($table, true);
        }

        // Ensure base test tables exist (they may have been dropped by previous tests)
        if (!$driver->tableExists('users') || !$driver->tableExists('posts')) {
            static::setUpDatabase($driver);
        }
    }

    // =========================================================================
    // Schema Instance and Factory Tests
    // =========================================================================

    /**
     * Test that schema() returns a SchemaManager instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaReturnsSchemaInstance(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertInstanceOf(SchemaManager::class, $schema);
    }

    /**
     * Test that getDriver returns the driver instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getDriverReturnsDriver(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertSame($driver, $schema->getDriver());
    }

    /**
     * Test that table() returns a Table instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableReturnsTableInstance(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $table = $schema->table('users');

        $this->assertInstanceOf(Table::class, $table);
    }

    /**
     * Test that createTable() returns a TableBuilder instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableReturnsTableBuilder(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $builder = $schema->createTable('new_table');

        $this->assertInstanceOf(TableBuilder::class, $builder);
    }

    /**
     * Test that alterTable() returns an AlterTableBuilder instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableReturnsAlterTableBuilder(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $builder = $schema->alterTable('users');

        $this->assertInstanceOf(AlterTableBuilder::class, $builder);
    }

    // =========================================================================
    // Table Existence Tests
    // =========================================================================

    /**
     * Test tableExists returns true for existing tables
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrue(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertTrue($schema->tableExists('users'));
        $this->assertTrue($schema->tableExists('posts'));
    }

    /**
     * Test tableExists returns false for non-existing tables
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalse(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertFalse($schema->tableExists('nonexistent_table'));
    }

    /**
     * Test tableNotExists convenience method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableNotExists(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertTrue($schema->tableNotExists('nonexistent_table'));
        $this->assertFalse($schema->tableNotExists('users'));
    }

    // =========================================================================
    // Column Introspection Tests
    // =========================================================================

    /**
     * Test hasColumn returns true for existing columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasColumnReturnsTrue(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertTrue($schema->hasColumn('users', 'id'));
        $this->assertTrue($schema->hasColumn('users', 'name'));
        $this->assertTrue($schema->hasColumn('users', 'email'));
    }

    /**
     * Test hasColumn returns false for non-existing columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasColumnReturnsFalse(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertFalse($schema->hasColumn('users', 'nonexistent'));
    }

    /**
     * Test getTableColumns returns column types
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $columns = $schema->getTableColumns('users', true);

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
    }

    /**
     * Test getColumnListing returns column names
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getColumnListing(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $columns = $schema->getColumnListing('users');

        $this->assertIsArray($columns);
        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
        $this->assertContains('email', $columns);
    }

    // =========================================================================
    // Key/Index Introspection Tests
    // =========================================================================

    /**
     * Test hasKey returns true for PRIMARY key
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasKeyReturnsTrue(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertTrue($schema->hasKey('users', 'PRIMARY'));
    }

    /**
     * Test hasPrimaryKey returns true when a primary key exists
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasPrimaryKeyReturnsTrue(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertTrue($schema->hasPrimaryKey('users'));
    }

    /**
     * Test Table::hasPrimaryKey returns true for a table with a primary key
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableHasPrimaryKeyReturnsTrue(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $table = $driver->schema()->table('users');

        $this->assertTrue($table->hasPrimaryKey());
    }

    /**
     * Test hasKey returns false for non-existing keys
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasKeyReturnsFalse(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $this->assertFalse($schema->hasKey('users', 'nonexistent_key'));
    }

    /**
     * Test getTableKeys returns key information
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableKeys(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $keys = $schema->getTableKeys('users');

        $this->assertIsArray($keys);
        $this->assertArrayHasKey('PRIMARY', $keys);
    }

    /**
     * Test getIndexNames returns key/index names
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getIndexNames(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $names = $schema->getIndexNames('users');

        $this->assertIsArray($names);
        $this->assertContains('PRIMARY', $names);
    }

    /**
     * Test getPrimaryKey returns primary key column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKey(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $pk = $schema->getPrimaryKey('users');
        $this->assertEquals('id', $pk);
    }

    /**
     * Test getPrimaryKeyColumns returns composite primary key columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyColumns(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        $driver->createTable('temp_pk_cols')
            ->string('col_a', 50)
            ->string('col_b', 50)
            ->primaryKey(['col_a', 'col_b'])
            ->execute();

        $pk = $schema->getPrimaryKeyColumns('temp_pk_cols');
        $this->assertEquals(['col_a', 'col_b'], $pk);
        $this->assertTrue($schema->hasPrimaryKeyColumn('temp_pk_cols', 'col_a'));
        $this->assertFalse($schema->hasPrimaryKeyColumn('temp_pk_cols', 'col_c'));
    }

    // =========================================================================
    // Table Operations Tests
    // =========================================================================

    /**
     * Test dropTable removes a table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropTable(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_drop_test')
            ->id()
            ->execute();

        $this->assertTrue($schema->tableExists('temp_drop_test'));

        // Drop it
        $schema->dropTable('temp_drop_test');

        $this->assertFalse($schema->tableExists('temp_drop_test'));
    }

    /**
     * Test dropTable with IF EXISTS doesn't error on missing table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropTableIfExists(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Should not throw exception
        $result = $schema->dropTable('nonexistent_table_xyz', true);

        $this->assertInstanceOf(SchemaManager::class, $result);
    }

    /**
     * Test renameTable renames a table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function renameTable(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_rename_source')
            ->id()
            ->execute();

        $this->assertTrue($schema->tableExists('temp_rename_source'));

        // Rename it
        $schema->renameTable('temp_rename_source', 'temp_rename_target');

        $this->assertFalse($schema->tableExists('temp_rename_source'));
        $this->assertTrue($schema->tableExists('temp_rename_target'));

        // Cleanup
        $schema->dropTable('temp_rename_target');
    }

    // =========================================================================
    // Index Operations Tests
    // =========================================================================

    /**
     * Test addIndex creates an index
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addIndex(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_index_test')
            ->id()
            ->string('name', 100)
            ->string('email', 255)
            ->execute();

        // Add an index
        $result = $schema->addIndex('temp_index_test', 'idx_name', 'name');
        $this->assertTrue($result);

        // Verify index exists
        $this->assertTrue($schema->hasKey('temp_index_test', 'idx_name'));

        // Cleanup
        $schema->dropTable('temp_index_test');
    }

    /**
     * Test addIndex with multiple columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addIndexMultipleColumns(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_multi_idx')
            ->id()
            ->string('first_name', 100)
            ->string('last_name', 100)
            ->execute();

        // Add a composite index
        $result = $schema->addIndex('temp_multi_idx', 'idx_full_name', ['first_name', 'last_name']);
        $this->assertTrue($result);

        // Verify index exists
        $this->assertTrue($schema->hasKey('temp_multi_idx', 'idx_full_name'));

        // Cleanup
        $schema->dropTable('temp_multi_idx');
    }

    /**
     * Test addUniqueIndex creates a unique index
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addUniqueIndex(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_unique_idx')
            ->id()
            ->string('email', 255)
            ->execute();

        // Add a unique index
        $result = $schema->addUniqueIndex('temp_unique_idx', 'idx_email', 'email');
        $this->assertTrue($result);

        // Verify index exists
        $this->assertTrue($schema->hasKey('temp_unique_idx', 'idx_email'));

        // Cleanup
        $schema->dropTable('temp_unique_idx');
    }

    /**
     * Test dropIndex removes an index
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropIndex(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table with an index
        $driver->createTable('temp_drop_idx')
            ->id()
            ->string('name', 100)
            ->index('idx_drop_name', 'name')
            ->execute();

        $this->assertTrue($schema->hasKey('temp_drop_idx', 'idx_drop_name'));

        // Drop the index
        $result = $schema->dropIndex('temp_drop_idx', 'idx_drop_name');
        $this->assertTrue($result);

        $this->assertFalse($schema->hasKey('temp_drop_idx', 'idx_drop_name'));

        // Cleanup
        $schema->dropTable('temp_drop_idx');
    }

    // =========================================================================
    // Column Operations Tests
    // =========================================================================

    /**
     * Test addColumn adds a column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function addColumn(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_add_col')
            ->id()
            ->execute();

        $this->assertFalse($schema->hasColumn('temp_add_col', 'new_column'));

        // Add a column - use database-appropriate type
        $schema->addColumn('temp_add_col', 'new_column', 'VARCHAR(255)');

        $this->assertTrue($schema->hasColumn('temp_add_col', 'new_column'));

        // Cleanup
        $schema->dropTable('temp_add_col');
    }

    /**
     * Test dropColumn removes a column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropColumn(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table with a column to drop
        $driver->createTable('temp_drop_col')
            ->id()
            ->string('to_drop', 100)
            ->string('keep_me', 100)
            ->execute();

        $this->assertTrue($schema->hasColumn('temp_drop_col', 'to_drop'));

        // Drop the column
        $result = $schema->dropColumn('temp_drop_col', 'to_drop');
        $this->assertTrue($result);

        $this->assertFalse($schema->hasColumn('temp_drop_col', 'to_drop'));
        $this->assertTrue($schema->hasColumn('temp_drop_col', 'keep_me'));

        // Cleanup
        $schema->dropTable('temp_drop_col');
    }

    /**
     * Test renameColumn renames a column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function renameColumn(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a temporary table
        $driver->createTable('temp_rename_col')
            ->id()
            ->string('old_name', 100)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('temp_rename_col')
            ->values(['old_name' => 'test value'])
            ->execute();

        $this->assertTrue($schema->hasColumn('temp_rename_col', 'old_name'));

        // Rename the column - use database-appropriate type
        $schema->renameColumn('temp_rename_col', 'old_name', 'new_name', 'VARCHAR(100)');

        $this->assertFalse($schema->hasColumn('temp_rename_col', 'old_name'));
        $this->assertTrue($schema->hasColumn('temp_rename_col', 'new_name'));

        // Verify data is preserved
        $query = new Query($driver);
        $value = $query->select('new_name')
            ->from('temp_rename_col')
            ->fetch('row');
        $this->assertEquals('test value', $value->new_name);

        // Cleanup
        $schema->dropTable('temp_rename_col');
    }

    // =========================================================================
    // Auto-Increment Tests
    // =========================================================================

    /**
     * Test getAutoIncrement returns next auto-increment value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getAutoIncrement(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a table
        $driver->createTable('temp_auto_test')
            ->id()
            ->string('name', 100)
            ->execute();

        // Insert some rows
        $query = new Query($driver);
        $query->insert('temp_auto_test')->values(['name' => 'Row 1'])->execute();

        $query = new Query($driver);
        $query->insert('temp_auto_test')->values(['name' => 'Row 2'])->execute();

        // Get auto increment value
        $autoIncrement = $schema->getAutoIncrement('temp_auto_test');
        $this->assertEquals(3, $autoIncrement);

        // Cleanup
        $schema->dropTable('temp_auto_test');
    }

    /**
     * Test setAutoIncrement sets the starting value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function setAutoIncrement(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a table
        $driver->createTable('temp_setauto_test')
            ->id()
            ->string('name', 100)
            ->execute();

        // Insert a row to initialize auto-increment
        $query = new Query($driver);
        $query->insert('temp_setauto_test')->values(['name' => 'Initial'])->execute();

        // Set auto increment to 100
        $result = $schema->setAutoIncrement('temp_setauto_test', 100);
        $this->assertTrue($result);

        // Insert another row
        $query = new Query($driver);
        $query->insert('temp_setauto_test')->values(['name' => 'After set'])->execute();

        // Verify the new row has id >= 100
        $query = new Query($driver);
        $id = $query->select('id')
            ->from('temp_setauto_test')
            ->where('name', '=', 'After set')
            ->fetch('row');
        $this->assertGreaterThanOrEqual(100, (int) $id->id);

        // Cleanup
        $schema->dropTable('temp_setauto_test');
    }

    // =========================================================================
    // Primary Key Operations Tests
    // =========================================================================

    /**
     * Test dropPrimaryKey removes primary key
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropPrimaryKey(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();

        // Create a table with composite primary key
        $driver->createTable('temp_drop_pk')
            ->string('col_a', 50)
            ->string('col_b', 50)
            ->primaryKey(['col_a', 'col_b'])
            ->execute();

        // Verify PRIMARY key exists
        $this->assertTrue($schema->hasKey('temp_drop_pk', 'PRIMARY'));

        // Drop primary key
        $result = $schema->dropPrimaryKey('temp_drop_pk');
        $this->assertTrue($result);

        // Cleanup
        $schema->dropTable('temp_drop_pk');
    }

    // =========================================================================
    // Table List Tests
    // =========================================================================

    /**
     * Test getTableList returns list of tables
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableList(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $tables = $schema->getTableList();

        $this->assertIsArray($tables);
        $this->assertContains('users', $tables);
        $this->assertContains('posts', $tables);
    }

    /**
     * Test getTableCreate returns CREATE TABLE statement
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableCreate(string $dbName, Driver $driver)
    {
        $this->cleanupTempTables($dbName, $driver);

        $schema = $driver->schema();
        $create = $schema->getTableCreate('users');

        $this->assertIsArray($create);
        $this->assertArrayHasKey('users', $create);
        $this->assertStringContainsString('CREATE', strtoupper($create['users']));
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }
}
