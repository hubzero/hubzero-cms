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
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\Diff\TableDiff;

/**
 * Schema Comparison Integration Tests
 *
 * Tests the full schema comparison workflow with real database tables.
 */
class SchemaComparisonTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['cmp_test_v1', 'cmp_test_v2'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created/dropped per-test
    }

    /**
     * Clean up test tables before each test
     *
     * @param Driver $driver
     */
    private function cleanTestTables(Driver $driver): void
    {
        $driver->dropTable('cmp_test_v1', true);
        $driver->dropTable('cmp_test_v2', true);
    }

    // =========================================================================
    // Integration Tests
    // =========================================================================

    /**
     * Test comparing two real tables
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compareRealTables(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create v1 table
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('name', 100)->notNull()
            ->string('old_field', 100)
            ->execute();

        // Create v2 table (with changes)
        $driver->createTable('cmp_test_v2')
            ->id()
            ->string('name', 100)  // Made nullable
            ->string('email', 100)->notNull()
            ->integer('status')->default(0)
            ->execute();

        // Compare the tables
        $diff = $schema->compareTables('cmp_test_v1', 'cmp_test_v2');

        $this->assertInstanceOf(TableDiff::class, $diff, "[$dbName]");
        $this->assertFalse($diff->isEmpty(), "[$dbName]");

        // Should have added columns: email, status
        $addedNames = array_map(fn($c) => $c->getName(), $diff->getAddedColumns());
        $this->assertContains('email', $addedNames, "[$dbName] Should detect added 'email' column");
        $this->assertContains('status', $addedNames, "[$dbName] Should detect added 'status' column");

        // Should have removed column: old_field
        $removedNames = array_map(fn($c) => $c->getName(), $diff->getRemovedColumns());
        $this->assertContains('old_field', $removedNames, "[$dbName] Should detect removed 'old_field' column");

        // Name column changed (nullable changed)
        $changedNames = array_map(fn($c) => $c->getName(), $diff->getChangedColumns());
        $this->assertContains('name', $changedNames, "[$dbName] Should detect 'name' column nullability change");

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test generateTableDiffSql returns valid SQL
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function generateTableDiffSql(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create v1 table
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('name', 100)->notNull()
            ->execute();

        // Create v2 table with additional column
        $driver->createTable('cmp_test_v2')
            ->id()
            ->string('name', 100)->notNull()
            ->string('email', 100)
            ->execute();

        // Generate SQL
        $sql = $schema->generateTableDiffSql('cmp_test_v1', 'cmp_test_v2');

        $this->assertIsArray($sql, "[$dbName]");
        $this->assertNotEmpty($sql, "[$dbName]");

        // SQL should contain ADD COLUMN for email
        $sqlString = strtoupper(implode(' ', $sql));
        $this->assertStringContainsString('EMAIL', $sqlString, "[$dbName] Should generate ADD COLUMN for email");

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test comparing identical tables returns empty diff
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compareIdenticalTables(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create identical tables
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('name', 100)->notNull()
            ->execute();

        $driver->createTable('cmp_test_v2')
            ->id()
            ->string('name', 100)->notNull()
            ->execute();

        $diff = $schema->compareTables('cmp_test_v1', 'cmp_test_v2');

        $this->assertTrue($diff->isEmpty(), "[$dbName] Identical tables should have no diff");

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test comparing TableInfo objects from introspection
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compareTableInfoObjects(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create a table
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('name', 100)->notNull()
            ->execute();

        // Introspect it
        $table1 = $schema->introspectTable('cmp_test_v1');

        // Create a modified TableInfo
        $table2Data = $table1->toArray();
        $table2Data['columns'][] = [
            'name' => 'new_column',
            'full_type' => 'VARCHAR(100)',
            'nullable' => true,
            'default' => null,
            'auto_increment' => false,
        ];
        $table2 = new TableInfo($table2Data);

        // Compare
        $diff = $schema->compareTables($table1, $table2);

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getAddedColumns(), "[$dbName]");
        $this->assertEquals('new_column', $diff->getAddedColumns()[0]->getName(), "[$dbName]");

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test getComparator returns Comparator instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getComparatorReturnsInstance(string $dbName, Driver $driver): void
    {
        $schema = $driver->schema();
        $comparator = $schema->getComparator();

        $this->assertInstanceOf(Comparator::class, $comparator, "[$dbName]");
    }

    /**
     * Test getDiffSqlGenerator returns instance
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getDiffSqlGeneratorReturnsInstance(string $dbName, Driver $driver): void
    {
        $schema = $driver->schema();
        $generator = $schema->getDiffSqlGenerator();

        $this->assertInstanceOf(DiffSqlGenerator::class, $generator, "[$dbName]");
    }

    /**
     * Test generating reverse SQL
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function generateReverseSql(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create v1 table
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('name', 100)->notNull()
            ->execute();

        // Create v2 table with additional column
        $driver->createTable('cmp_test_v2')
            ->id()
            ->string('name', 100)->notNull()
            ->string('email', 100)
            ->execute();

        // Generate forward SQL (v1 -> v2)
        $upSql = $schema->generateTableDiffSql('cmp_test_v1', 'cmp_test_v2');

        // Generate reverse SQL (v2 -> v1)
        $downSql = $schema->generateTableDiffSqlReverse('cmp_test_v1', 'cmp_test_v2');

        $this->assertIsArray($upSql, "[$dbName]");
        $this->assertIsArray($downSql, "[$dbName]");

        // Up should add email
        $upString = strtoupper(implode(' ', $upSql));
        $this->assertStringContainsString('ADD', $upString, "[$dbName] Forward migration should ADD column");
        $this->assertStringContainsString('EMAIL', $upString, "[$dbName]");

        // Note: Not all databases support DROP COLUMN, so downSql might be empty or use other methods

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test diff with index changes
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function diffWithIndexChanges(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create v1 without index
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('email', 100)->notNull()
            ->execute();

        // Create v2 with index
        $driver->createTable('cmp_test_v2')
            ->id()
            ->string('email', 100)->notNull()
            ->index('idx_email', 'email')
            ->execute();

        $diff = $schema->compareTables('cmp_test_v1', 'cmp_test_v2');

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertNotEmpty($diff->getAddedIndexes(), "[$dbName] Should detect added index");

        // Cleanup
        $this->cleanTestTables($driver);
    }

    /**
     * Test destructive warning for dropped column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function destructiveWarningForDroppedColumn(string $dbName, Driver $driver): void
    {
        $this->cleanTestTables($driver);
        $schema = $driver->schema();

        // Create v1 with important_data column
        $driver->createTable('cmp_test_v1')
            ->id()
            ->string('important_data', 255)->notNull()
            ->execute();

        // Create v2 without important_data column
        $driver->createTable('cmp_test_v2')
            ->id()
            ->execute();

        $diff = $schema->compareTables('cmp_test_v1', 'cmp_test_v2');

        $this->assertTrue($diff->hasDestructiveChanges(), "[$dbName] Should detect destructive change");

        $warnings = $diff->getDestructiveWarnings();
        $this->assertNotEmpty($warnings, "[$dbName]");

        // Check if any warning contains the column name (case-insensitive)
        $hasColumnWarning = false;
        foreach ($warnings as $warning) {
            if (stripos($warning, 'important_data') !== false) {
                $hasColumnWarning = true;
                break;
            }
        }
        $this->assertTrue($hasColumnWarning, "[$dbName] Warning should mention 'important_data' column");

        // Cleanup
        $this->cleanTestTables($driver);
    }
}
