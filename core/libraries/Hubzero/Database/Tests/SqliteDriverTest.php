<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Hubzero\Database\Driver;
use Hubzero\Database\Driver\Sqlite;
use Hubzero\Database\Query;
use Hubzero\Database\Schema\AlterTableBuilder;

/**
 * SQLite Driver Tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Sqlite driver pass with a no-op assertion.
 *
 * Tests SQLite-specific driver functionality, especially table rebuild operations
 * that SQLite requires for operations it doesn't support natively.
 *
 * Configuration: Add 'sqlite' to DB_TEST_BACKENDS and set DB_SQLITE_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('sqlite')]
#[Group('database')]
class SqliteDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [
            'drv_rebuild_test',
            'drv_modify_test',
            'drv_combined_test',
            'drv_simple_test',
        ];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (get_class($driver) !== Sqlite::class) {
            return;
        }

        // Tests create their own tables — nothing to set up globally
    }

    /**
     * Check if the driver is exactly Sqlite; if not, pass with a no-op assertion.
     */
    private function requiresSqlite(Driver $driver): bool
    {
        if (get_class($driver) !== Sqlite::class) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    /**
     * Drop test tables before each test to ensure clean state
     */
    protected function cleanupTables(Driver $driver): void
    {
        foreach (static::getTestTables() as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }

    /**
     * Test SQLite generates table rebuild SQL for DROP COLUMN
     *
     * SQLite has limited ALTER TABLE support. Before version 3.35.0, it didn't
     * support DROP COLUMN at all. The driver should generate table rebuild
     * statements (create temp table, copy data, drop old, rename temp).
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableRebuildForDropColumn(string $dbName, Driver $driver)
    {
        if (!$this->requiresSqlite($driver)) {
            return;
        }

        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create a table with multiple columns
        $schema->table('drv_rebuild_test')->create()
            ->id('id')
            ->string('name', 255)
            ->string('email', 255)->nullable()
            ->integer('age')->nullable()
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insertMany('drv_rebuild_test', [
            ['id' => 1, 'name' => 'Test', 'email' => 'test@test.com', 'age' => 25]
        ]);

        // Use AlterTableBuilder to drop the email column
        $schema->table('drv_rebuild_test')->alter()
            ->dropColumn('email')
            ->execute();

        // Verify column was dropped
        $tableInfo = $schema->introspectTable('drv_rebuild_test');
        $columnNames = array_map(fn($col) => strtolower($col->getName()), $tableInfo->getColumns());

        $this->assertContains('id', $columnNames);
        $this->assertContains('name', $columnNames);
        $this->assertContains('age', $columnNames);
        $this->assertNotContains('email', $columnNames);

        // Verify data was preserved
        $driver->setQuery("SELECT * FROM drv_rebuild_test WHERE id = 1");
        $row = $driver->loadObject();
        $this->assertEquals('Test', $row->name);
        $this->assertEquals(25, $row->age);
    }

    /**
     * Test SQLite generates table rebuild SQL for MODIFY COLUMN
     *
     * SQLite doesn't support modifying column types. The driver should
     * detect this and generate table rebuild statements.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableRebuildForModifyColumn(string $dbName, Driver $driver)
    {
        if (!$this->requiresSqlite($driver)) {
            return;
        }

        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create a table
        $schema->table('drv_modify_test')->create()
            ->id('id')
            ->string('status', 255)->nullable()
            ->execute();

        $query = new Query($driver);
        $query->insertMany('drv_modify_test', [
            ['id' => 1, 'status' => 'active']
        ]);

        // Modify the status column (this should trigger rebuild)
        $schema->table('drv_modify_test')->alter()
            ->modifyColumn('status', 'integer', ['nullable' => false, 'default' => 1])
            ->execute();

        // Verify modification worked
        $tableInfo = $schema->introspectTable('drv_modify_test');
        $statusCol = null;
        foreach ($tableInfo->getColumns() as $col) {
            if (strtolower($col->getName()) === 'status') {
                $statusCol = $col;
                break;
            }
        }

        $this->assertNotNull($statusCol);
        // SQLite maps INTEGER to INTEGER affinity
        $this->assertEquals('INTEGER', strtoupper($statusCol->getFullType()));
    }

    /**
     * Test SQLite handles combined ADD and DROP column operations
     *
     * When an ALTER TABLE has both ADD and DROP operations, SQLite should
     * perform a table rebuild that handles both operations atomically.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableRebuildCombinedOperations(string $dbName, Driver $driver)
    {
        if (!$this->requiresSqlite($driver)) {
            return;
        }

        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create initial table
        $schema->table('drv_combined_test')->create()
            ->id('id')
            ->string('old_col', 255)->nullable()
            ->string('keep_col', 255)->nullable()
            ->execute();

        $query = new Query($driver);
        $query->insertMany('drv_combined_test', [
            ['id' => 1, 'old_col' => 'old', 'keep_col' => 'keep']
        ]);

        // Drop old_col and add new_col in one operation
        $schema->table('drv_combined_test')->alter()
            ->dropColumn('old_col')
            ->addColumn('new_col', 'string', ['length' => 255, 'nullable' => true])
            ->execute();

        // Verify schema changes
        $tableInfo = $schema->introspectTable('drv_combined_test');
        $columnNames = array_map(fn($col) => strtolower($col->getName()), $tableInfo->getColumns());

        $this->assertContains('id', $columnNames);
        $this->assertContains('keep_col', $columnNames);
        $this->assertContains('new_col', $columnNames);
        $this->assertNotContains('old_col', $columnNames);

        // Verify data preserved
        $driver->setQuery("SELECT * FROM drv_combined_test WHERE id = 1");
        $row = $driver->loadObject();
        $this->assertEquals('keep', $row->keep_col);
    }

    /**
     * Test compileAlterTable detects when rebuild is needed
     *
     * This is a unit test of the driver method itself, not the full workflow.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCompileAlterTableDetectsRebuildNeed(string $dbName, Driver $driver)
    {
        if (!$this->requiresSqlite($driver)) {
            return;
        }

        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create a simple table
        $schema->table('drv_rebuild_test')->create()
            ->id('id')
            ->string('name', 255)
            ->execute();

        // Create an AlterTableBuilder with a DROP operation
        $builder = new AlterTableBuilder($driver, 'drv_rebuild_test');
        $builder->dropColumn('name');

        // Get the SQL from compileAlterTable
        $sql = $driver->compileAlterTable($builder);

        // Should contain rebuild statements (CREATE TABLE ... _rebuild_)
        $sqlString = implode(' ', $sql);
        $hasRebuild = stripos($sqlString, 'CREATE TABLE') !== false &&
                      stripos($sqlString, '_rebuild_') !== false;

        $this->assertTrue(
            $hasRebuild,
            "compileAlterTable should generate rebuild statements for DROP COLUMN. Got:\n" . $sqlString
        );
    }

    /**
     * Test simple ADD COLUMN doesn't trigger rebuild
     *
     * SQLite supports ADD COLUMN natively (since SQLite 3.1.0), so simple
     * column additions should use ALTER TABLE ADD COLUMN, not table rebuild.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSimpleAddColumnDoesNotRebuild(string $dbName, Driver $driver)
    {
        if (!$this->requiresSqlite($driver)) {
            return;
        }

        $this->cleanupTables($driver);
        $schema = $driver->schema();

        // Create a simple table
        $schema->table('drv_simple_test')->create()
            ->id('id')
            ->string('name', 255)
            ->execute();

        // Create an AlterTableBuilder with just ADD operation
        $builder = new AlterTableBuilder($driver, 'drv_simple_test');
        $builder->addColumn('email', 'string', ['length' => 255, 'nullable' => true]);

        // Get the SQL
        $sql = $driver->compileAlterTable($builder);

        // Should use simple ALTER TABLE ADD COLUMN, not rebuild
        $sqlString = implode(' ', $sql);
        $this->assertStringContainsString('ALTER TABLE', $sqlString);
        $this->assertStringContainsString('ADD COLUMN', $sqlString);
        $this->assertStringNotContainsString('_rebuild_', $sqlString);
    }
}
