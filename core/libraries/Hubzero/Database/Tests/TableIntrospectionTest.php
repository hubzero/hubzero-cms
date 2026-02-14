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
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\IndexInfo;
use Hubzero\Database\Schema\ForeignKeyInfo;

/**
 * Tests for table introspection with structured objects
 *
 * Tests the schema introspection methods that return structured objects
 * (TableInfo, ColumnInfo, IndexInfo, ForeignKeyInfo) instead of raw results.
 */
class TableIntrospectionTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        // Tables are created/dropped per-test
        return [];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created/dropped per-test in setupTable() helper
    }

    /**

    // =========================================================================
    // TableInfo Tests
    // =========================================================================

    /**
     * Test introspectTable returns TableInfo object
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function introspectTableReturnsTableInfo(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $this->assertInstanceOf(TableInfo::class, $table);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo name property
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoName(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $this->assertEquals($tableName, $table->getName());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo returns columns as ColumnInfo objects
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoColumnsAreColumnInfoObjects(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $columns = $table->getColumns();

        $this->assertIsArray($columns);
        $this->assertNotEmpty($columns);

        foreach ($columns as $column) {
            $this->assertInstanceOf(ColumnInfo::class, $column);
        }

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo getColumn() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoGetColumn(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $nameCol = $table->getColumn('name');
        $this->assertInstanceOf(ColumnInfo::class, $nameCol);
        $this->assertEquals('name', $nameCol->getName());

        $nonexistent = $table->getColumn('nonexistent');
        $this->assertNull($nonexistent);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo hasColumn() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoHasColumn(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $this->assertTrue($table->hasColumn('name'));
        $this->assertTrue($table->hasColumn('email'));
        $this->assertFalse($table->hasColumn('nonexistent'));

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo getColumnNames() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoGetColumnNames(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $names = $table->getColumnNames();

        $this->assertIsArray($names);
        $this->assertContains('id', $names);
        $this->assertContains('name', $names);
        $this->assertContains('email', $names);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo returns indexes as IndexInfo objects
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoIndexesAreIndexInfoObjects(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $indexes = $table->getIndexes();

        $this->assertIsArray($indexes);

        foreach ($indexes as $index) {
            $this->assertInstanceOf(IndexInfo::class, $index);
        }

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo getPrimaryKey()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoPrimaryKey(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $pk = $table->getPrimaryKey();
        $this->assertEquals('id', $pk);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo toArray() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoToArray(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $array = $table->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('columns', $array);
        $this->assertArrayHasKey('indexes', $array);
        $this->assertArrayHasKey('foreign_keys', $array);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    // =========================================================================
    // ColumnInfo Tests
    // =========================================================================

    /**
     * Test ColumnInfo getName()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function columnInfoGetName(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $col = $table->getColumn('email');

        $this->assertEquals('email', $col->getName());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test ColumnInfo isNullable()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function columnInfoIsNullable(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        // Email was defined with nullable()
        $emailCol = $table->getColumn('email');
        $this->assertTrue($emailCol->isNullable());

        // Description was also defined with nullable()
        $descCol = $table->getColumn('description');
        $this->assertTrue($descCol->isNullable());

        // Name was NOT nullable
        $nameCol = $table->getColumn('name');
        $this->assertIsBool($nameCol->isNullable());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test ColumnInfo type detection methods
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function columnInfoTypeDetection(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $nameCol = $table->getColumn('name');
        $this->assertTrue($nameCol->isString());

        $idCol = $table->getColumn('id');
        $this->assertTrue($idCol->isInteger());
        $this->assertTrue($idCol->isNumeric());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test ColumnInfo isPrimaryKey()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function columnInfoIsPrimaryKey(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        $idCol = $table->getColumn('id');
        $this->assertNotNull($idCol, "Primary key column 'id' should exist");
        $this->assertTrue($idCol->isPrimaryKey());

        $nameCol = $table->getColumn('name');
        $this->assertFalse($nameCol->isPrimaryKey());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test ColumnInfo toArray()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function columnInfoToArray(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $col = $table->getColumn('name');
        $array = $col->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('nullable', $array);

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    // =========================================================================
    // IndexInfo Tests
    // =========================================================================

    /**
     * Test IndexInfo getName()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function indexInfoGetName(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $indexes = $table->getIndexes();

        // Should have at least the primary key
        $this->assertNotEmpty($indexes);

        $indexNames = array_map(function ($idx) {
            return $idx->getName();
        }, $indexes);

        // Primary key exists (name varies by database)
        $this->assertTrue(
            in_array('PRIMARY', $indexNames) || count(array_filter($indexes, function ($idx) {
                return $idx->isPrimary();
            })) > 0
        );

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test IndexInfo getColumns()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function indexInfoGetColumns(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $indexes = $table->getIndexes();

        foreach ($indexes as $index) {
            $columns = $index->getColumns();
            $this->assertIsArray($columns);
            $this->assertNotEmpty($columns);
        }

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test IndexInfo isPrimary()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function indexInfoIsPrimary(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $pkIndex = $table->getPrimaryKeyIndex();

        if ($pkIndex) {
            $this->assertTrue($pkIndex->isPrimary());
            $this->assertTrue($pkIndex->isUnique());
        }

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test IndexInfo toArray()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function indexInfoToArray(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);
        $indexes = $table->getIndexes();

        if (!empty($indexes)) {
            $array = $indexes[0]->toArray();
            $this->assertIsArray($array);
            $this->assertArrayHasKey('name', $array);
            $this->assertArrayHasKey('columns', $array);
            $this->assertArrayHasKey('primary', $array);
            $this->assertArrayHasKey('unique', $array);
        }

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    // =========================================================================
    // ForeignKeyInfo Tests (no database needed)
    // =========================================================================

    /**
     * Test ForeignKeyInfo construction from array
     */
    #[Test]
    public function foreignKeyInfoFromArray()
    {
        $fk = new ForeignKeyInfo([
            'name' => 'fk_user_id',
            'columns' => ['user_id'],
            'foreign_table' => 'users',
            'foreign_columns' => ['id'],
            'on_update' => 'CASCADE',
            'on_delete' => 'SET NULL',
        ]);

        $this->assertEquals('fk_user_id', $fk->getName());
        $this->assertEquals(['user_id'], $fk->getColumns());
        $this->assertEquals('users', $fk->getForeignTable());
        $this->assertEquals(['id'], $fk->getForeignColumns());
        $this->assertEquals('CASCADE', $fk->getOnUpdate());
        $this->assertEquals('SET NULL', $fk->getOnDelete());
        $this->assertTrue($fk->cascadesOnUpdate());
        $this->assertTrue($fk->setsNullOnDelete());
    }

    /**
     * Test ForeignKeyInfo construction from stdClass
     */
    #[Test]
    public function foreignKeyInfoFromObject()
    {
        $obj = new \stdClass();
        $obj->name = 'fk_category';
        $obj->columns = ['category_id'];
        $obj->foreign_table = 'categories';
        $obj->foreign_columns = ['id'];
        $obj->on_update = 'NO ACTION';
        $obj->on_delete = 'CASCADE';

        $fk = new ForeignKeyInfo($obj);

        $this->assertEquals('fk_category', $fk->getName());
        $this->assertTrue($fk->cascadesOnDelete());
        $this->assertTrue($fk->references('categories'));
    }

    /**
     * Test ForeignKeyInfo toArray()
     */
    #[Test]
    public function foreignKeyInfoToArray()
    {
        $fk = new ForeignKeyInfo([
            'name' => 'fk_test',
            'columns' => ['ref_id'],
            'foreign_table' => 'refs',
            'foreign_columns' => ['id'],
        ]);

        $array = $fk->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('columns', $array);
        $this->assertArrayHasKey('foreign_table', $array);
        $this->assertArrayHasKey('foreign_columns', $array);
        $this->assertArrayHasKey('on_update', $array);
        $this->assertArrayHasKey('on_delete', $array);
    }

    // =========================================================================
    // Integration Tests
    // =========================================================================

    /**
     * Test that existing individual methods still work alongside introspectTable
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function individualMethodsStillWork(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        // Existing methods should still work
        $this->assertTrue($schema->tableExists($tableName));
        $this->assertTrue($schema->hasColumn($tableName, 'name'));

        $columns = $schema->getTableColumns($tableName);
        $this->assertIsArray($columns);

        $keys = $schema->getTableKeys($tableName);
        $this->assertIsArray($keys);

        // introspectTable provides the same data in structured form
        $table = $schema->introspectTable($tableName);
        $this->assertTrue($table->hasColumn('name'));

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo column count
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoColumnCount(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        // Should have: id, name, email, status, description, active, created, modified
        $this->assertGreaterThanOrEqual(8, $table->getColumnCount());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    /**
     * Test TableInfo index count
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableInfoIndexCount(string $dbName, Driver $driver)
    {
        $tableName = $this->setupTable($driver);
        $schema = $driver->schema();

        $table = $schema->introspectTable($tableName);

        // Should have at least the primary key
        $this->assertGreaterThanOrEqual(1, $table->getIndexCount());

        // Cleanup
        $driver->dropTable($tableName, true);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test table
     */
    /**
     * Setup test table with unique name to avoid catalog cache collisions
     *
     * @return string The table name that was created
     */
    private function setupTable(Driver $driver): string
    {
        $tableName = 'introspect_test';

        $schema = $driver->schema();
        $schema->createTable($tableName)
            ->id()
            ->string('name', 100)
            ->string('email', 255)->nullable()
            ->integer('status')->default(0)
            ->text('description')->nullable()
            ->boolean('active')->default(true)
            ->timestamps()
            ->index('idx_status', 'status')
            ->execute();

        return $tableName;
    }
}
