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
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;
use Hubzero\Database\Schema\Diff\ColumnDiff;

/**
 * Safe Diff Mode tests
 *
 * Tests the safe mode functionality in DiffSqlGenerator that prevents
 * destructive operations like DROP TABLE and DROP COLUMN.
 */
class SafeDiffModeTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // No persistent tables needed - tests use in-memory TableInfo objects
    }

    /**
     * Create a TableInfo for testing
     */
    protected function createTableInfo(
        string $name,
        array $columns,
        array $indexes = [],
        array $foreignKeys = []
    ): TableInfo {
        $colDefs = [];
        foreach ($columns as $colName => $colDef) {
            if (is_string($colDef)) {
                $colDef = ['full_type' => $colDef];
            }
            $colDefs[] = array_merge([
                'name' => $colName,
                'full_type' => 'VARCHAR(255)',
                'nullable' => true,
                'default' => null,
                'auto_increment' => false,
                'unsigned' => false,
            ], $colDef);
        }

        return new TableInfo([
            'name' => $name,
            'columns' => $colDefs,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
        ]);
    }

    /**
     * Create a DatabaseInfo for testing
     */
    protected function createDatabaseInfo(array $tables): DatabaseInfo
    {
        $tableInfos = [];
        foreach ($tables as $name => $columns) {
            $tableInfos[] = $this->createTableInfo($name, $columns)->toArray();
        }

        return new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $tableInfos,
        ]);
    }

    // =========================================================================
    // Safe Flag Constants Tests (Pure Unit Tests)
    // =========================================================================

    /**
     * Test that safe flag constants are properly defined
     */
    #[Test]
    public function safeFlagConstantsAreDefined(): void
    {
        $this->assertEquals(1, DiffSqlGenerator::SAFE_NO_DROP_TABLES);
        $this->assertEquals(2, DiffSqlGenerator::SAFE_NO_DROP_COLUMNS);
        $this->assertEquals(4, DiffSqlGenerator::SAFE_NO_DROP_INDEXES);
        $this->assertEquals(8, DiffSqlGenerator::SAFE_NO_DROP_FOREIGN_KEYS);
        $this->assertEquals(16, DiffSqlGenerator::SAFE_NO_SHRINKING_MODIFICATIONS);
        $this->assertEquals(31, DiffSqlGenerator::SAFE_ALL);
    }

    /**
     * Test that SAFE_ALL includes all individual flags
     */
    #[Test]
    public function safeAllIncludesAllFlags(): void
    {
        $allFlags = DiffSqlGenerator::SAFE_NO_DROP_TABLES
            | DiffSqlGenerator::SAFE_NO_DROP_COLUMNS
            | DiffSqlGenerator::SAFE_NO_DROP_INDEXES
            | DiffSqlGenerator::SAFE_NO_DROP_FOREIGN_KEYS
            | DiffSqlGenerator::SAFE_NO_SHRINKING_MODIFICATIONS;

        $this->assertEquals($allFlags, DiffSqlGenerator::SAFE_ALL);
    }

    // =========================================================================
    // Schema-Level Safe Mode Tests
    // =========================================================================

    /**
     * Test that safe mode prevents DROP TABLE in schema diff
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModePreventsDropTableInSchemaDiff(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $schema1 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
            'legacy_table' => ['id' => 'INT', 'data' => 'TEXT'],
        ]);

        $schema2 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
        ]);

        $diff = $comparator->compareSchemas($schema1, $schema2);

        // Without safe mode - should include DROP TABLE
        $normalSql = $generator->generateSchemaUp($diff, 0);
        $this->assertNotEmpty($normalSql, "[$dbName]");
        $hasDropTable = false;
        foreach ($normalSql as $stmt) {
            if (stripos($stmt, 'DROP TABLE') !== false) {
                $hasDropTable = true;
                break;
            }
        }
        $this->assertTrue($hasDropTable, "[$dbName] Normal mode should include DROP TABLE");

        // With safe mode - should NOT include DROP TABLE
        $safeSql = $generator->generateSchemaUp($diff, DiffSqlGenerator::SAFE_NO_DROP_TABLES);
        $hasDropTable = false;
        foreach ($safeSql as $stmt) {
            if (stripos($stmt, 'DROP TABLE') !== false) {
                $hasDropTable = true;
                break;
            }
        }
        $this->assertFalse($hasDropTable, "[$dbName] Safe mode should not include DROP TABLE");
    }

    /**
     * Test generateSafeSchemaUp convenience method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function generateSafeSchemaUpConvenienceMethod(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $schema1 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
            'to_drop' => ['id' => 'INT'],
        ]);

        $schema2 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT'],  // removed column
        ]);

        $diff = $comparator->compareSchemas($schema1, $schema2);

        // Verify the diff detects the changes
        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertTrue($diff->hasRemovedTable('to_drop'), "[$dbName]");

        $safeSql = $generator->generateSafeSchemaUp($diff);

        // Safe mode should not generate DROP TABLE for to_drop
        $safeSqlString = implode(' ', $safeSql);
        $this->assertStringNotContainsStringIgnoringCase('DROP TABLE', $safeSqlString, "[$dbName]");
    }

    // =========================================================================
    // Table-Level Safe Mode Tests
    // =========================================================================

    /**
     * Test that safe mode prevents DROP COLUMN
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModePreventsDropColumn(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table1 = $this->createTableInfo('users', [
            'id' => ['full_type' => 'INT', 'auto_increment' => true],
            'name' => 'VARCHAR(255)',
            'email' => 'VARCHAR(255)',
            'legacy_field' => 'TEXT',
        ]);

        $table2 = $this->createTableInfo('users', [
            'id' => ['full_type' => 'INT', 'auto_increment' => true],
            'name' => 'VARCHAR(255)',
            'email' => 'VARCHAR(255)',
            // legacy_field removed
        ]);

        $diff = $comparator->compareTables($table1, $table2);

        // Verify the diff detects the removed column
        $this->assertCount(1, $diff->getRemovedColumns(), "[$dbName]");
        $this->assertEquals('legacy_field', $diff->getRemovedColumns()[0]->getName(), "[$dbName]");

        // Without safe mode - should generate SQL that removes the column
        $normalSql = $generator->generateUp($diff, 0);
        $this->assertNotEmpty($normalSql, "[$dbName] Normal mode should generate SQL for column removal");

        // With safe mode - column should be preserved
        $safeSql = $generator->generateUp($diff, DiffSqlGenerator::SAFE_NO_DROP_COLUMNS);
        $safeSqlString = implode(' ', $safeSql);

        // The safe SQL should not remove the legacy_field
        $hasDropForLegacy = stripos($safeSqlString, 'DROP') !== false &&
                            stripos($safeSqlString, 'legacy_field') !== false;
        $this->assertFalse($hasDropForLegacy, "[$dbName] Safe mode should not drop legacy_field");
    }

    /**
     * Test that safe mode prevents DROP INDEX
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModePreventsDropIndex(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table1 = $this->createTableInfo('users', [
            'id' => 'INT',
            'email' => 'VARCHAR(255)',
        ], [
            ['name' => 'idx_email', 'columns' => ['email'], 'unique' => false, 'primary' => false],
        ]);

        $table2 = $this->createTableInfo('users', [
            'id' => 'INT',
            'email' => 'VARCHAR(255)',
        ]);  // Index removed

        $diff = $comparator->compareTables($table1, $table2);

        // Without safe mode
        $normalSql = $generator->generateUp($diff, 0);
        $hasDropIndex = false;
        foreach ($normalSql as $stmt) {
            if (stripos($stmt, 'DROP INDEX') !== false) {
                $hasDropIndex = true;
                break;
            }
        }
        $this->assertTrue($hasDropIndex, "[$dbName] Normal mode should include DROP INDEX");

        // With safe mode
        $safeSql = $generator->generateUp($diff, DiffSqlGenerator::SAFE_NO_DROP_INDEXES);
        $hasDropIndex = false;
        foreach ($safeSql as $stmt) {
            if (stripos($stmt, 'DROP INDEX') !== false) {
                $hasDropIndex = true;
                break;
            }
        }
        $this->assertFalse($hasDropIndex, "[$dbName] Safe mode should not include DROP INDEX");
    }

    /**
     * Test that safe mode prevents DROP FOREIGN KEY
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModePreventsDropForeignKey(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table1 = $this->createTableInfo('posts', [
            'id' => 'INT',
            'user_id' => 'INT',
        ], [], [
            ['name' => 'fk_user', 'columns' => ['user_id'], 'foreign_table' => 'users', 'foreign_columns' => ['id']],
        ]);

        $table2 = $this->createTableInfo('posts', [
            'id' => 'INT',
            'user_id' => 'INT',
        ]);  // FK removed

        $diff = $comparator->compareTables($table1, $table2);

        // Verify the diff detects the removed FK
        $this->assertCount(1, $diff->getRemovedForeignKeys(), "[$dbName]");
        $this->assertEquals('fk_user', $diff->getRemovedForeignKeys()[0]->getName(), "[$dbName]");

        // With safe mode - FK should be preserved (no DROP FOREIGN KEY)
        $safeSql = $generator->generateUp($diff, DiffSqlGenerator::SAFE_NO_DROP_FOREIGN_KEYS);

        // Safe SQL should not remove the FK
        $safeSqlString = implode(' ', $safeSql);
        $hasDropFk = stripos($safeSqlString, 'DROP') !== false &&
                     (stripos($safeSqlString, 'FOREIGN') !== false || stripos($safeSqlString, 'fk_user') !== false);
        $this->assertFalse($hasDropFk, "[$dbName] Safe mode should not drop foreign key");
    }

    /**
     * Test SAFE_ALL prevents all destructive operations
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeAllPreventsAllDestructiveOperations(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table1 = $this->createTableInfo('users', [
            'id' => 'INT',
            'name' => 'VARCHAR(255)',
            'to_remove' => 'TEXT',
        ], [
            ['name' => 'idx_name', 'columns' => ['name'], 'unique' => false, 'primary' => false],
        ], [
            ['name' => 'fk_something', 'columns' => ['id'], 'foreign_table' => 'other', 'foreign_columns' => ['id']],
        ]);

        $table2 = $this->createTableInfo('users', [
            'id' => 'INT',
            'name' => 'VARCHAR(100)',  // shrunk
        ]);  // Everything else removed

        $diff = $comparator->compareTables($table1, $table2);

        // Verify diff detects all the removals
        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getRemovedColumns(), "[$dbName]");
        $this->assertCount(1, $diff->getRemovedIndexes(), "[$dbName]");
        $this->assertCount(1, $diff->getRemovedForeignKeys(), "[$dbName]");
        $this->assertCount(1, $diff->getChangedColumns(), "[$dbName]");

        $sql = $generator->generateUp($diff, DiffSqlGenerator::SAFE_ALL);

        // Safe ALL should prevent all destructive operations
        $sqlString = implode(' ', $sql);
        $this->assertStringNotContainsStringIgnoringCase('DROP', $sqlString, "[$dbName]");
        // Should not contain the shrunk type
        $this->assertStringNotContainsStringIgnoringCase('VARCHAR(100)', $sqlString, "[$dbName]");
    }

    /**
     * Test safe mode with empty diff
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModeWithEmptyDiff(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table = $this->createTableInfo('users', [
            'id' => 'INT',
            'name' => 'VARCHAR(255)',
        ]);

        $diff = $comparator->compareTables($table, $table);

        $this->assertTrue($diff->isEmpty(), "[$dbName]");

        $sql = $generator->generateSafeUp($diff);
        $this->assertEmpty($sql, "[$dbName]");
    }

    /**
     * Test safe mode allows adding new elements
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function safeModeAllowsAddingElements(string $dbName, Driver $driver): void
    {
        $comparator = new Comparator();
        $generator = new DiffSqlGenerator($driver);

        $table1 = $this->createTableInfo('users', [
            'id' => 'INT',
        ]);

        $table2 = $this->createTableInfo('users', [
            'id' => 'INT',
            'name' => 'VARCHAR(255)',
            'email' => 'VARCHAR(255)',
        ], [
            ['name' => 'idx_email', 'columns' => ['email'], 'unique' => true, 'primary' => false],
        ]);

        $diff = $comparator->compareTables($table1, $table2);

        $sql = $generator->generateSafeUp($diff);

        // Should contain ADD statements
        $hasAdd = false;
        foreach ($sql as $stmt) {
            if (stripos($stmt, 'ADD') !== false) {
                $hasAdd = true;
                break;
            }
        }
        $this->assertTrue($hasAdd, "[$dbName] Safe mode should allow ADD operations");
    }
}
