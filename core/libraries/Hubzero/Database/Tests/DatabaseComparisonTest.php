<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\SchemaManager;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\Diff\SchemaDiff;

/**
 * Database Comparison Integration Tests
 *
 * Tests the full database schema comparison workflow with real tables.
 */
class DatabaseComparisonTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables created/dropped in individual test methods
    }

    /**
     * Clean up test tables
     *
     * @param Driver $driver
     */
    protected function cleanupTables(Driver $driver): void
    {
        $tables = [
            'dbdiff_users', 'dbdiff_posts', 'dbdiff_comments', 'dbdiff_new', 'dbdiff_old',
            'dbdiff_rebuild_test', 'dbdiff_modify_test', 'dbdiff_combined_test', 'dbdiff_rename_test'
        ];

        foreach ($tables as $table) {
            $driver->dropTable($table, true);
        }
    }

    // =========================================================================
    // Integration Tests
    // =========================================================================

    /**
     * Test comparing full database schemas
     */
    #[DataProvider('databaseProvider')]
    public function testCompareFullDatabaseSchemas(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create initial tables using schema builder
        $schema->table('dbdiff_users')->create()
            ->increments('id')
            ->string('name', 255)
            ->execute();

        $schema->table('dbdiff_posts')->create()
            ->increments('id')
            ->string('title', 255)->nullable()
            ->execute();

        // Introspect only our test tables to avoid unrelated fixture drift.
        $currentSchema = $schema->introspectDatabase('dbdiff_');
        $testTables = array_map(static fn($table) => $table->toArray(), $currentSchema->getTables());

        $schema1 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $testTables,
        ]);

        // Add a new table
        $schema->table('dbdiff_comments')->create()
            ->increments('id')
            ->string('content', 255)->nullable()
            ->execute();

        // Modify existing table
        $schema->table('dbdiff_users')->alter()
            ->addColumn('email', 'string', ['length' => 255, 'nullable' => true])
            ->execute();

        // Introspect only our test tables to avoid unrelated fixture drift.
        $currentSchema2 = $schema->introspectDatabase('dbdiff_');
        $testTables2 = array_map(static fn($table) => $table->toArray(), $currentSchema2->getTables());

        $schema2 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $testTables2,
        ]);

        // Compare schemas
        $diff = $schema->compareSchemas($schema1, $schema2);

        $this->assertInstanceOf(SchemaDiff::class, $diff);
        $this->assertFalse($diff->isEmpty());

        // Should have added table (dbdiff_comments)
        $addedNames = $diff->getAddedTableNames();
        $this->assertContains('dbdiff_comments', $addedNames);

        // Should have changed table (dbdiff_users)
        $changedNames = $diff->getChangedTableNames();
        $this->assertContains('dbdiff_users', $changedNames);

        // Cleanup
        $this->cleanupTables($driver);
    }

    /**
     * Test generateSchemaDiffSql returns valid SQL
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateSchemaDiffSql(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create schema 1 (single table)
        $schema1 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false, 'auto_increment' => true],
                        ['name' => 'name', 'full_type' => 'TEXT', 'nullable' => false],
                    ],
                ],
            ],
        ]);

        // Create schema 2 (two tables, modified column)
        $schema2 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false, 'auto_increment' => true],
                        ['name' => 'name', 'full_type' => 'TEXT', 'nullable' => false],
                        ['name' => 'email', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
                [
                    'name' => 'dbdiff_posts',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'title', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        // Generate SQL
        $sql = $schema->generateSchemaDiffSql($schema1, $schema2);

        $this->assertIsArray($sql);
        $this->assertNotEmpty($sql);

        // Should contain CREATE TABLE for new table
        $sqlString = implode(' ', $sql);
        $this->assertStringContainsString('dbdiff_posts', strtolower($sqlString));
    }

    /**
     * Test compareToSchema convenience method
     */
    #[DataProvider('databaseProvider')]
    public function testCompareToSchema(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create current state
        $schema->table('dbdiff_users')->create()
            ->increments('id')
            ->string('name', 255)
            ->execute();

        // Define target schema (with additional column)
        $targetSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'name', 'full_type' => 'TEXT', 'nullable' => false],
                        ['name' => 'email', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        // Compare current database to target using only dbdiff_* tables.
        $currentSchema = $schema->introspectDatabase('dbdiff_');
        $testTables = array_map(static fn($table) => $table->toArray(), $currentSchema->getTables());

        $filteredCurrent = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $testTables,
        ]);

        $diff = $schema->compareSchemas($filteredCurrent, $targetSchema);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getChangedTables());

        // dbdiff_users should have email column added
        $tableDiff = $diff->getTableDiff('dbdiff_users');
        $this->assertNotNull($tableDiff);
        $this->assertCount(1, $tableDiff->getAddedColumns());
        $this->assertEquals('email', $tableDiff->getAddedColumns()[0]->getName());

        // Cleanup
        $this->cleanupTables($driver);
    }

    /**
     * Test schema comparison with table removal detection
     */
    #[DataProvider('databaseProvider')]
    public function testSchemaComparisonDetectsRemovedTable(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Schema with two tables
        $schema1 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_users',
                    'columns' => [['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false]],
                ],
                [
                    'name' => 'dbdiff_old',
                    'columns' => [['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false]],
                ],
            ],
        ]);

        // Schema with one table (dbdiff_old removed)
        $schema2 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_users',
                    'columns' => [['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false]],
                ],
            ],
        ]);

        $diff = $schema->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getRemovedTables());
        $this->assertEquals('dbdiff_old', $diff->getRemovedTables()[0]->getName());
        $this->assertTrue($diff->hasDestructiveChanges());

        // Cleanup
        $this->cleanupTables($driver);
    }

    /**
     * Test filtering schema diff by prefix
     */
    #[DataProvider('databaseProvider')]
    public function testFilterSchemaDiffByPrefix(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Schema with mixed prefixes
        $schema1 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'jos_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
                [
                    'name' => 'jos_posts',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
                [
                    'name' => 'other_data',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
            ],
        ]);

        $schema2 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'jos_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                        ['name' => 'email', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
                [
                    'name' => 'jos_posts',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                        ['name' => 'title', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
                [
                    'name' => 'other_data',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                        ['name' => 'value', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $diff = $schema->compareSchemas($schema1, $schema2);

        // Filter to only jos_* tables
        $filtered = $diff->filterByPattern('jos_*');

        $this->assertCount(2, $filtered->getChangedTables());
        $this->assertTrue($filtered->hasChangedTable('jos_users'));
        $this->assertTrue($filtered->hasChangedTable('jos_posts'));
        $this->assertFalse($filtered->hasChangedTable('other_data'));

        // Cleanup
        $this->cleanupTables($driver);
    }

    /**
     * Test summary of schema changes
     */
    #[DataProvider('databaseProvider')]
    public function testSchemaDiffSummary(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        $schema1 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'existing',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
                [
                    'name' => 'to_remove',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
            ],
        ]);

        $schema2 = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'existing',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                        ['name' => 'name', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
                [
                    'name' => 'new_table',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                    ],
                ],
            ],
        ]);

        $diff = $schema->compareSchemas($schema1, $schema2);
        $summary = $diff->getSummary();

        $this->assertEquals(1, $summary['tables']['added']);
        $this->assertEquals(1, $summary['tables']['removed']);
        $this->assertEquals(1, $summary['tables']['changed']);
        $this->assertEquals(1, $summary['total_column_changes']);

        // Cleanup
        $this->cleanupTables($driver);
    }

    // =========================================================================
    // Rename Hints Tests
    // =========================================================================

    /**
     * Test column rename hints are detected
     */
    #[DataProvider('databaseProvider')]
    public function testColumnRenameHints(string $dbName, Driver $driver)
    {
        $comparator = new Comparator();

        // Add hint that 'fname' was renamed to 'first_name'
        $comparator->addColumnRenameHint('users', 'fname', 'first_name');

        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'fname', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'first_name', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $diff = $comparator->compareSchemas($fromSchema, $toSchema);

        // Should have one changed table
        $this->assertCount(1, $diff->getChangedTables());

        $tableDiff = $diff->getChangedTables()[0];

        // Should have one renamed column
        $this->assertEquals(['fname' => 'first_name'], $tableDiff->getRenamedColumns());

        // Should NOT have added or removed columns
        $this->assertEmpty($tableDiff->getAddedColumns());
        $this->assertEmpty($tableDiff->getRemovedColumns());
    }

    /**
     * Test table rename hints are detected
     */
    #[DataProvider('databaseProvider')]
    public function testTableRenameHints(string $dbName, Driver $driver)
    {
        $comparator = new Comparator();

        // Add hint that 'old_users' was renamed to 'users'
        $comparator->addTableRenameHint('old_users', 'users');

        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'old_users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'name', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'name', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $diff = $comparator->compareSchemas($fromSchema, $toSchema);

        // Should have one renamed table
        $this->assertEquals(['old_users' => 'users'], $diff->getRenamedTables());

        // Should NOT have added or removed tables
        $this->assertEmpty($diff->getAddedTables());
        $this->assertEmpty($diff->getRemovedTables());
    }

    /**
     * Test heuristic rename detection (opt-in)
     */
    #[DataProvider('databaseProvider')]
    public function testHeuristicRenameDetection(string $dbName, Driver $driver)
    {
        $comparator = new Comparator();

        // Enable heuristic detection with low threshold
        $comparator->enableHeuristicRenameDetection(0.5);

        // Column with similar name and same type should be detected as rename
        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'user_name', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'username', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $diff = $comparator->compareSchemas($fromSchema, $toSchema);

        $tableDiff = $diff->getChangedTables()[0];

        // With heuristic detection enabled, should detect rename
        $this->assertEquals(['user_name' => 'username'], $tableDiff->getRenamedColumns());
        $this->assertEmpty($tableDiff->getAddedColumns());
        $this->assertEmpty($tableDiff->getRemovedColumns());
    }

    /**
     * Test heuristic detection is off by default
     */
    #[DataProvider('databaseProvider')]
    public function testHeuristicDetectionOffByDefault(string $dbName, Driver $driver)
    {
        $comparator = new Comparator();

        // Without heuristic detection, similar columns should be add/remove
        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'user_name', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'username', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $diff = $comparator->compareSchemas($fromSchema, $toSchema);

        $tableDiff = $diff->getChangedTables()[0];

        // Without heuristic detection, should be add + remove
        $this->assertEmpty($tableDiff->getRenamedColumns());
        $this->assertCount(1, $tableDiff->getAddedColumns());
        $this->assertCount(1, $tableDiff->getRemovedColumns());
    }

    /**
     * Test column rename with additional changes
     */
    #[DataProvider('databaseProvider')]
    public function testColumnRenameWithTypeChange(string $dbName, Driver $driver)
    {
        $comparator = new Comparator();
        $comparator->addColumnRenameHint('users', 'fname', 'first_name');

        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'fname', 'full_type' => 'VARCHAR(50)', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'users',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'first_name', 'full_type' => 'VARCHAR(100)', 'nullable' => false],
                    ],
                ],
            ],
        ]);

        $diff = $comparator->compareSchemas($fromSchema, $toSchema);
        $tableDiff = $diff->getChangedTables()[0];

        // Should have rename
        $this->assertEquals(['fname' => 'first_name'], $tableDiff->getRenamedColumns());

        // Should also have a column diff for the type/nullable change
        $changedColumns = $tableDiff->getChangedColumns();
        $this->assertCount(1, $changedColumns);

        $colDiff = $changedColumns[0];
        $this->assertTrue($colDiff->isRenamed());
        $this->assertTrue($colDiff->hasTypeChanged());
        $this->assertTrue($colDiff->hasNullableChanged());
    }

    /**
     * Test DiffSqlGenerator produces RENAME COLUMN SQL
     */
    #[DataProvider('databaseProvider')]
    public function testDiffSqlGeneratorProducesRenameColumnSql(string $dbName, Driver $driver)
    {
        $this->cleanupTables($driver);
        $schema = $driver->schema();

        $comparator = new Comparator();
        $comparator->addColumnRenameHint('dbdiff_rename_test', 'old_name', 'new_name');

        // Create table
        $schema->table('dbdiff_rename_test')->create()
            ->id('id')
            ->string('old_name', 255)->nullable()
            ->execute();

        $query = new Query($driver);
        $query->insertMany('dbdiff_rename_test', [
            ['id' => 1, 'old_name' => 'test value']
        ]);

        $fromSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_rename_test',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'old_name', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        $toSchema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => [
                [
                    'name' => 'dbdiff_rename_test',
                    'columns' => [
                        ['name' => 'id', 'full_type' => 'INTEGER', 'nullable' => false],
                        ['name' => 'new_name', 'full_type' => 'TEXT', 'nullable' => true],
                    ],
                ],
            ],
        ]);

        // Use the comparator with hints to generate the diff
        $diff = $comparator->compareSchemas($fromSchema, $toSchema);

        // Generate SQL using DiffSqlGenerator
        $generator = new DiffSqlGenerator($driver);
        $sql = $generator->generateSchemaUp($diff);

        $this->assertIsArray($sql);
        $this->assertNotEmpty($sql);

        // Execute the migration
        foreach ($sql as $statement) {
            $driver->setQuery($statement)->execute();
        }

        // Verify column was renamed using schema introspection
        $tableInfo = $schema->introspectTable('dbdiff_rename_test');
        $columnNames = array_map(fn($col) => strtolower($col->getName()), $tableInfo->getColumns());

        $this->assertContains('new_name', $columnNames);
        $this->assertNotContains('old_name', $columnNames);

        // Verify data was preserved
        $driver->setQuery("SELECT * FROM dbdiff_rename_test WHERE id = 1");
        $row = $driver->loadObject();
        $this->assertEquals('test value', $row->new_name);

        // Cleanup
        $this->cleanupTables($driver);
    }
}
