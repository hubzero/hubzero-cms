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
use Hubzero\Database\Schema\AlterTableBuilder;

/**
 * Multi-database ALTER TABLE builder tests
 *
 * This test class runs all ALTER TABLE operations against all configured databases
 * using PHPUnit data providers. Each test method receives a database name and driver
 * instance, allowing the same test logic to verify behavior across database drivers.
 *
 * Test Configuration:
 * - Database credentials are configured via DB_*_* env vars in phpunit.xml
 * - Only enabled databases are tested
 * - Tests are automatically skipped if database is unavailable
 *
 * Usage:
 * ```bash
 * vendor/bin/phpunit core/libraries/Hubzero/Database/Tests/AlterTableBuilderTest.php
 * ```
 */
class AlterTableBuilderTest extends AbstractDriverTestCase
{
    /**
     * Tables created and dropped in individual test methods
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables created and dropped in individual test methods
    }

    /**
     * No persistent test tables - all created/dropped within individual tests
     */
    protected static function getTestTables(): array
    {
        return [];
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Create a test table with id + name columns using schema builder
     *
     * @param   Driver  $driver  Database driver
     * @param   string  $name    Table name
     * @param   array   $extraColumns  Extra columns to add: ['colname' => 'type', ...]
     * @return  void
     */
    protected function createTestTable(Driver $driver, string $name, array $extraColumns = []): void
    {
        $builder = $driver->createTable($name)->id()->string('name', 255);

        foreach ($extraColumns as $col => $type) {
            switch ($type) {
                case 'text':
                    $builder->text($col);
                    break;
                case 'integer':
                    $builder->integer($col);
                    break;
                default:
                    $builder->string($col, 255);
                    break;
            }
        }

        $builder->execute();
    }

    /**
     * Drop a test table if it exists
     *
     * @param   Driver  $driver  Database driver
     * @param   string  $name    Table name
     * @return  void
     */
    protected function dropTestTable(Driver $driver, string $name): void
    {
        try {
            $driver->dropTable($name, true);
        } catch (\Exception $e) {
            // Ignore errors on cleanup
        }
    }

    // =========================================================================
    // Basic ALTER TABLE Tests
    // =========================================================================

    /**
     * Test adding a nullable column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddColumnNullable(string $dbName, Driver $driver): void
    {
        $table = 'test_alter_nullable';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)
               ->addColumn('email', 'VARCHAR(255)')
               ->nullable()
               ->execute();

        $this->assertTrue(
            $driver->tableHasField($table, 'email'),
            "Column 'email' should exist on {$dbName}"
        );

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test that AlterTableBuilder returns fluent interface
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddColumnFluent(string $dbName, Driver $driver): void
    {
        $table = 'test_alter_fluent';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $builder = $driver->alterTable($table);
        $result = $builder->addColumn('email', 'VARCHAR(255)');

        $this->assertInstanceOf(AlterTableBuilder::class, $result);

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test dropping a column
     *
     * Note: Column dropping is not supported on all databases
     * and could trigger a table rebuild.
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDropColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_drop_col';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['email' => 'text']);

        $this->assertTrue($driver->tableHasField($table, 'email'));

        $driver->alterTable($table)
               ->dropColumn('email')
               ->execute();

        $this->assertFalse($driver->tableHasField($table, 'email'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding an index
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddIndex(string $dbName, Driver $driver): void
    {
        $table = 'test_add_idx';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['email' => 'string']);

        // Drop any lingering index from catalog cache before creating
        try {
            $driver->alterTable($table)->dropIndex('idx_email')->execute();
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        $driver->alterTable($table)
               ->addIndex('idx_email', 'email')
               ->execute();

        $this->assertTrue($driver->tableHasKey($table, 'idx_email'));

        // Explicitly drop index before table to help DB2 catalog cache
        try {
            $driver->alterTable($table)->dropIndex('idx_email')->execute();
        } catch (\Exception $e) {
            // Ignore if already dropped
        }

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test dropping an index
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDropIndex(string $dbName, Driver $driver): void
    {
        $table = 'test_drop_idx';

        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['email' => 'string']);

        $driver->alterTable($table)
               ->addIndex('idx_email', 'email')
               ->execute();

        $this->assertTrue($driver->tableHasKey($table, 'idx_email'));

        $driver->alterTable($table)
               ->dropIndex('idx_email')
               ->execute();

        $this->assertFalse($driver->tableHasKey($table, 'idx_email'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test that alterTable() returns AlterTableBuilder instance
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAlterTableReturnsFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->alterTable('test_fluent_return');

        $this->assertInstanceOf(AlterTableBuilder::class, $builder);
    }

    /**
     * Test that table->alter() returns AlterTableBuilder instance
     *
     * This tests the legacy API for backwards compatibility.
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableAlterReturnsFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->table('test_table_alter')->alter();

        $this->assertInstanceOf(AlterTableBuilder::class, $builder);
    }

    // =========================================================================
    // Column Type Tests
    // =========================================================================

    /**
     * Test adding a string column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddStringColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_add_string';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)->addString('title', 255)->execute();

        $this->assertTrue($driver->tableHasField($table, 'title'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding an integer column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddIntegerColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_add_integer';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)->addInteger('count')->execute();

        $this->assertTrue($driver->tableHasField($table, 'count'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding a text column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddTextColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_add_text';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)->addText('description')->execute();

        $this->assertTrue($driver->tableHasField($table, 'description'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding a datetime column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddDatetimeColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_add_datetime';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)->addDatetime('created_at')->execute();

        $this->assertTrue($driver->tableHasField($table, 'created_at'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding a boolean column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddBooleanColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_add_boolean';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)->addBoolean('is_active')->execute();

        $this->assertTrue($driver->tableHasField($table, 'is_active'));

        $this->dropTestTable($driver, $table);
    }

    // =========================================================================
    // Column Modifier Tests
    // =========================================================================

    /**
     * Test adding a column with default value
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddColumnWithDefault(string $dbName, Driver $driver): void
    {
        $table = 'test_add_default';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)
               ->addColumn('status', 'INTEGER')
               ->default(0)
               ->execute();

        $this->assertTrue($driver->tableHasField($table, 'status'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test adding a column with IF NOT EXISTS check
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddColumnIfNotExists(string $dbName, Driver $driver): void
    {
        $table = 'test_add_if_not_exists';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['email' => 'text']);

        if (!$driver->tableHasField($table, 'newcol')) {
            $driver->alterTable($table)
                   ->addColumn('newcol', 'VARCHAR(255)')
                   ->execute();

            $this->assertTrue($driver->tableHasField($table, 'newcol'));
        }

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test dropping multiple columns at once
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDropMultipleColumns(string $dbName, Driver $driver): void
    {
        $table = 'test_drop_multi';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['email' => 'text', 'phone' => 'text']);

        $driver->alterTable($table)
               ->dropColumn('email')
               ->dropColumn('phone')
               ->execute();

        $this->assertFalse($driver->tableHasField($table, 'email'));
        $this->assertFalse($driver->tableHasField($table, 'phone'));
        $this->assertTrue($driver->tableHasField($table, 'name'));

        $this->dropTestTable($driver, $table);
    }

    // =========================================================================
    // Column Modification Tests
    // =========================================================================

    /**
     * Test modifying a column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModifyColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_modify_col';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        $driver->alterTable($table)
               ->modifyColumn('name', 'VARCHAR(255) NOT NULL')
               ->execute();

        $this->assertTrue($driver->tableHasField($table, 'name'));

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test modifying a column with IF EXISTS check
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModifyColumnIfExists(string $dbName, Driver $driver): void
    {
        $table = 'test_modify_if_exists';

        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table);

        if ($driver->tableHasField($table, 'name')) {
            $driver->alterTable($table)
                   ->modifyColumn('name', 'VARCHAR(500)')
                   ->execute();

            $columns = $driver->getTableColumns($table, false);
            $this->assertArrayHasKey('name', $columns);
        }

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test renaming a column
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRenameColumn(string $dbName, Driver $driver): void
    {
        $table = 'test_rename_col';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['old_name' => 'text']);

        $driver->alterTable($table)
               ->renameColumn('old_name', 'new_name', 'TEXT')
               ->execute();

        $this->assertFalse($driver->tableHasField($table, 'old_name'));
        $this->assertTrue($driver->tableHasField($table, 'new_name'));

        $this->dropTestTable($driver, $table);
    }

    // =========================================================================
    // Index Tests
    // =========================================================================

    /**
     * Test that addIndex returns fluent builder
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddIndexFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->alterTable('test_idx_fluent');
        $result = $builder->addIndex('idx_test', 'name');

        $this->assertInstanceOf(AlterTableBuilder::class, $result);
    }

    /**
     * Test adding a unique index
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddUniqueIndexFluent(string $dbName, Driver $driver): void
    {
        $table = 'test_uidx_fluent';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['username' => 'string']);

        // Drop any lingering index from catalog cache before creating
        try {
            $driver->alterTable($table)->dropIndex('uidx_username')->execute();
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        $driver->alterTable($table)
               ->addUniqueIndex('uidx_username', 'username')
               ->execute();

        $this->assertTrue($driver->tableHasKey($table, 'uidx_username'));

        // Explicitly drop index before table to help DB2 catalog cache
        try {
            $driver->alterTable($table)->dropIndex('uidx_username')->execute();
        } catch (\Exception $e) {
            // Ignore if already dropped
        }

        $this->dropTestTable($driver, $table);
    }

    /**
     * Test that dropIndex returns fluent builder
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDropIndexFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->alterTable('test_drop_idx_fluent');
        $result = $builder->dropIndex('idx_test');

        $this->assertInstanceOf(AlterTableBuilder::class, $result);
    }

    /**
     * Test adding a fulltext index
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddFulltextIndex(string $dbName, Driver $driver): void
    {
        $table = 'test_fulltext_idx';
        $this->dropTestTable($driver, $table);
        $this->createTestTable($driver, $table, ['content' => 'string']);

        $driver->alterTable($table)
               ->addFulltextIndex('ft_content', 'content')
               ->execute();

        $this->assertTrue($driver->tableHasKey($table, 'ft_content'));

        $this->dropTestTable($driver, $table);
    }

    // =========================================================================
    // Primary Key Tests
    // =========================================================================

    /**
     * Test dropping primary key
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDropPrimaryKeyFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->alterTable('test_drop_pk');
        $result = $builder->dropPrimaryKey();

        $this->assertInstanceOf(AlterTableBuilder::class, $result);
    }

    /**
     * Test adding primary key
     *
     * @param   string  $dbName  Database name
     * @param   Driver  $driver  Database driver
     * @return  void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAddPrimaryKeyFluent(string $dbName, Driver $driver): void
    {
        $builder = $driver->alterTable('test_add_pk');
        $result = $builder->addPrimaryKey('id');

        $this->assertInstanceOf(AlterTableBuilder::class, $result);
    }
}
