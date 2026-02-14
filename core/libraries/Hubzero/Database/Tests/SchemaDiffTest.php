<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Driver;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * SchemaDiff tests
 *
 * Tests database-level schema comparison functionality with real databases.
 */
class SchemaDiffTest extends AbstractDriverTestCase
{
    /**
     * @var Comparator Schema comparator
     */
    private Comparator $comparator;

    protected static function getTestTables(): array
    {
        return [
            'diff_users', 'diff_posts', 'diff_comments',
            'diff_temp1', 'diff_temp2', 'diff_temp3', 'diff_unchanged',
        ];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created/dropped per-test
    }

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->comparator = new Comparator();
    }

    // =========================================================================
    // Basic Comparison Tests
    // =========================================================================

    /**
     * Test comparing identical schemas returns empty diff
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compareIdenticalSchemasReturnsEmptyDiff(string $dbName, Driver $driver): void
    {
        // Create test tables
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable('diff_posts')
            ->id()
            ->string('title', 255)
            ->execute();

        // Introspect schema twice
        $schema1 = $driver->schema()->introspectDatabase('diff_');
        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertInstanceOf(SchemaDiff::class, $diff, "[$dbName]");
        $this->assertTrue($diff->isEmpty(), "[$dbName] Identical schemas should have empty diff");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
    }

    /**
     * Test detecting added table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function detectsAddedTable(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('name', 255)
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Add another table
        $driver->createTable('diff_posts')
            ->id()
            ->string('title', 255)
            ->execute();

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getAddedTables(), "[$dbName]");
        $this->assertEquals('diff_posts', $diff->getAddedTables()[0]->getName(), "[$dbName]");
        $this->assertTrue($diff->hasAddedTable('diff_posts'), "[$dbName]");
        $this->assertFalse($diff->hasAddedTable('diff_users'), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
    }

    /**
     * Test detecting removed table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function detectsRemovedTable(string $dbName, Driver $driver): void
    {
        // Create initial schema with two tables
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->text('data')
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Remove one table
        $driver->dropTable('diff_temp1', true);

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getRemovedTables(), "[$dbName]");
        $this->assertEquals('diff_temp1', $diff->getRemovedTables()[0]->getName(), "[$dbName]");
        $this->assertTrue($diff->hasRemovedTable('diff_temp1'), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
    }

    /**
     * Test detecting changed table (added column)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function detectsChangedTable(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('name', 255)
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Add a column
        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getChangedTables(), "[$dbName]");
        $this->assertEquals('diff_users', $diff->getChangedTables()[0]->getName(), "[$dbName]");
        $this->assertTrue($diff->hasChangedTable('diff_users'), "[$dbName]");

        // Verify the table diff contains the added column
        $tableDiff = $diff->getTableDiff('diff_users');
        $this->assertInstanceOf(TableDiff::class, $tableDiff, "[$dbName]");
        $this->assertCount(1, $tableDiff->getAddedColumns(), "[$dbName]");
        $this->assertEquals('email', $tableDiff->getAddedColumns()[0]->getName(), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
    }

    /**
     * Test detecting multiple changes
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function detectsMultipleChanges(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);
        $driver->dropTable('diff_temp2', true);
        $driver->dropTable('diff_temp3', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->execute();

        $driver->createTable('diff_temp2')
            ->id()
            ->string('old_col', 100)
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Make multiple changes: drop temp1, add temp3, modify temp2
        $driver->dropTable('diff_temp1', true);

        $driver->createTable('diff_temp3')
            ->id()
            ->text('data')
            ->execute();

        $driver->schema()->addColumn('diff_temp2', 'new_col', 'VARCHAR(100)');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->isEmpty(), "[$dbName]");
        $this->assertCount(1, $diff->getAddedTables(), "[$dbName]");
        $this->assertCount(1, $diff->getRemovedTables(), "[$dbName]");
        $this->assertCount(1, $diff->getChangedTables(), "[$dbName]");

        $this->assertEquals('diff_temp3', $diff->getAddedTables()[0]->getName(), "[$dbName]");
        $this->assertEquals('diff_temp1', $diff->getRemovedTables()[0]->getName(), "[$dbName]");
        $this->assertEquals('diff_temp2', $diff->getChangedTables()[0]->getName(), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp2', true);
        $driver->dropTable('diff_temp3', true);
    }

    // =========================================================================
    // Summary and Statistics Tests
    // =========================================================================

    /**
     * Test getSummary returns correct counts
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getSummaryReturnsCorrectCounts(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);
        $driver->dropTable('diff_temp2', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Make changes: drop temp1, add temp2, modify users
        $driver->dropTable('diff_temp1', true);

        $driver->createTable('diff_temp2')
            ->id()
            ->execute();

        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);
        $summary = $diff->getSummary();

        $this->assertEquals(1, $summary['tables']['added'], "[$dbName]");
        $this->assertEquals(1, $summary['tables']['removed'], "[$dbName]");
        $this->assertEquals(1, $summary['tables']['changed'], "[$dbName]");
        $this->assertEquals(1, $summary['total_column_changes'], "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp2', true);
    }

    /**
     * Test getChangeCount returns total count
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getChangeCountReturnsTotal(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);
        $driver->dropTable('diff_temp2', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Make changes
        $driver->dropTable('diff_temp1', true);

        $driver->createTable('diff_temp2')
            ->id()
            ->execute();

        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        // 1 added table + 1 removed table + 1 added column = 3
        $this->assertGreaterThanOrEqual(3, $diff->getChangeCount(), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp2', true);
    }

    // =========================================================================
    // Destructive Change Detection Tests
    // =========================================================================

    /**
     * Test hasDestructiveChanges detects dropped table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasDestructiveChangesDetectsDroppedTable(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->text('data')
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Drop table
        $driver->dropTable('diff_temp1', true);

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertTrue($diff->hasDestructiveChanges(), "[$dbName]");

        $warnings = $diff->getDestructiveWarnings();
        $this->assertNotEmpty($warnings, "[$dbName]");
        $this->assertStringContainsString('diff_temp1', $warnings[0], "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
    }

    /**
     * Test hasDestructiveChanges detects dropped column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasDestructiveChangesDetectsDroppedColumn(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);

        $driver->createTable('diff_users')
            ->id()
            ->string('important_col', 255)
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Drop column
        $driver->schema()->dropColumn('diff_users', 'important_col');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertTrue($diff->hasDestructiveChanges(), "[$dbName]");

        $warnings = $diff->getDestructiveWarnings();
        $this->assertNotEmpty($warnings, "[$dbName]");
        $this->assertStringContainsString('important_col', $warnings[0], "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
    }

    /**
     * Test non-destructive changes don't trigger warning
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function nonDestructiveChangesNoWarning(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Make non-destructive changes: add column and table
        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');

        $driver->createTable('diff_posts')
            ->id()
            ->execute();

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);

        $this->assertFalse($diff->hasDestructiveChanges(), "[$dbName]");
        $this->assertEmpty($diff->getDestructiveWarnings(), "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
    }

    // =========================================================================
    // Serialization Tests
    // =========================================================================

    /**
     * Test toArray serialization
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function toArraySerialization(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Make changes
        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');

        $driver->createTable('diff_posts')
            ->id()
            ->execute();

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);
        $array = $diff->toArray();

        $this->assertArrayHasKey('from_database', $array, "[$dbName]");
        $this->assertArrayHasKey('to_database', $array, "[$dbName]");
        $this->assertArrayHasKey('added_tables', $array, "[$dbName]");
        $this->assertArrayHasKey('removed_tables', $array, "[$dbName]");
        $this->assertArrayHasKey('changed_tables', $array, "[$dbName]");
        $this->assertArrayHasKey('summary', $array, "[$dbName]");
        $this->assertArrayHasKey('destructive', $array, "[$dbName]");
        $this->assertArrayHasKey('warnings', $array, "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
    }

    // =========================================================================
    // Helper Method Tests
    // =========================================================================

    /**
     * Test getAddedTableNames
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getAddedTableNames(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
        $driver->dropTable('diff_comments', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Add tables
        $driver->createTable('diff_posts')
            ->id()
            ->execute();

        $driver->createTable('diff_comments')
            ->id()
            ->execute();

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);
        $names = $diff->getAddedTableNames();

        $this->assertCount(2, $names, "[$dbName]");
        $this->assertContains('diff_posts', $names, "[$dbName]");
        $this->assertContains('diff_comments', $names, "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
        $driver->dropTable('diff_comments', true);
    }

    /**
     * Test getRemovedTableNames
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRemovedTableNames(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_temp1', true);
        $driver->dropTable('diff_temp2', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $driver->createTable('diff_temp1')
            ->id()
            ->execute();

        $driver->createTable('diff_temp2')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Remove tables
        $driver->dropTable('diff_temp1', true);
        $driver->dropTable('diff_temp2', true);

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);
        $names = $diff->getRemovedTableNames();

        $this->assertCount(2, $names, "[$dbName]");
        $this->assertContains('diff_temp1', $names, "[$dbName]");
        $this->assertContains('diff_temp2', $names, "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
    }

    /**
     * Test getChangedTableNames
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getChangedTableNames(string $dbName, Driver $driver): void
    {
        // Create initial schema
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
        $driver->dropTable('diff_unchanged', true);

        $driver->createTable('diff_users')
            ->id()
            ->execute();

        $driver->createTable('diff_posts')
            ->id()
            ->execute();

        $driver->createTable('diff_unchanged')
            ->id()
            ->execute();

        $schema1 = $driver->schema()->introspectDatabase('diff_');

        // Modify tables
        $driver->schema()->addColumn('diff_users', 'email', 'VARCHAR(255)');
        $driver->schema()->addColumn('diff_posts', 'title', 'VARCHAR(255)');

        $schema2 = $driver->schema()->introspectDatabase('diff_');

        $diff = $this->comparator->compareSchemas($schema1, $schema2);
        $names = $diff->getChangedTableNames();

        $this->assertCount(2, $names, "[$dbName]");
        $this->assertContains('diff_users', $names, "[$dbName]");
        $this->assertContains('diff_posts', $names, "[$dbName]");
        $this->assertNotContains('diff_unchanged', $names, "[$dbName]");

        // Clean up
        $driver->dropTable('diff_users', true);
        $driver->dropTable('diff_posts', true);
        $driver->dropTable('diff_unchanged', true);
    }
}
