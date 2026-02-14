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
use Hubzero\Database\Drivers\Pgsql\PgsqlDriver as Pgsql;

/**
 * PostgreSQL-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Pgsql driver pass with a no-op assertion.
 * If multiple PostgreSQL instances are configured, each test runs against
 * all of them.
 *
 * Configuration: Add 'pgsql' to DB_TEST_BACKENDS and set DB_PGSQL_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('pgsql')]
#[Group('database')]
class PgsqlDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['pg_test_items', 'pg_test_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!($driver instanceof Pgsql)) {
            return;
        }

        foreach (['pg_test_items', 'pg_test_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE pg_test_categories (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INTEGER DEFAULT 0,
                active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $driver->exec("
            CREATE TABLE pg_test_items (
                id SERIAL PRIMARY KEY,
                category_id INTEGER REFERENCES pg_test_categories(id) ON DELETE SET NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price NUMERIC(10,2) DEFAULT 0.00,
                stock INTEGER DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $driver->exec("CREATE INDEX idx_pg_items_status ON pg_test_items (status)");

        $driver->exec(
            "INSERT INTO pg_test_categories (name, slug, sort_order) VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO pg_test_categories (name, slug, sort_order) VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO pg_test_categories (name, slug, sort_order, active) VALUES ('Archived', 'archived', 99, FALSE)"
        );

        $driver->exec(
            "INSERT INTO pg_test_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO pg_test_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO pg_test_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO pg_test_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    /**
     * Check if the driver is Pgsql; if not, pass with no-op assertion.
     */
    private function requiresPgsql(Driver $driver): bool
    {
        if (!($driver instanceof Pgsql)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['pg_test_view', 'pg_active_items'];
        $adHocTables = [
            'pg_rename_dest', 'pg_rename_src',
            'pg_fk_child', 'pg_fk_parent',
            'pg_ddl_test', 'pg_trunc_test',
            'pg_serial_test', 'pg_view_src',
            'pg_pk_test', 'pg_stored_test',
        ];
        $adHocSequences = ['pg_test_seq'];

        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Pgsql)) {
                continue;
            }
            foreach ($adHocViews as $view) {
                try {
                    $driver->dropView($view, true);
                } catch (\Exception $e) {
                }
            }
            foreach ($adHocTables as $table) {
                try {
                    $driver->dropTable($table, true);
                } catch (\Exception $e) {
                }
            }
            foreach ($adHocSequences as $seq) {
                try {
                    $driver->dropSequence($seq, true);
                } catch (\Exception $e) {
                }
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Drop views before parent drops the base tables they depend on
        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Pgsql)) {
                continue;
            }
            try {
                $driver->dropView('pg_active_items', true);
            } catch (\Exception $e) {
            }
        }

        parent::tearDownAfterClass();
    }

    // =========================================================================
    // Connection & Server Info
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function driverIsPgsqlInstance(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $this->assertInstanceOf(Pgsql::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $this->assertNotEmpty($version);
        $this->assertMatchesRegularExpression('/^\d+\./', $version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetServerInfo(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('version', $info);
    }

    // =========================================================================
    // Schema Introspection via Driver API
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrueForExistingTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('pg_test_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalseForMissingTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $this->assertFalse($driver->tableExists('pg_nonexistent_table'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $tables = $driver->getTableList();

        $this->assertContains('pg_test_categories', $tables);
        $this->assertContains('pg_test_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('pg_test_categories', true);

        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('slug', $columns);
        $this->assertArrayHasKey('active', $columns);
        // PostgreSQL reports 'integer' for SERIAL, 'boolean' for BOOLEAN
        $this->assertEquals('integer', $columns['id']);
        $this->assertEquals('boolean', $columns['active']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsFullInfo(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('pg_test_categories', false);

        $this->assertArrayHasKey('name', $columns);
        $this->assertIsObject($columns['name']);
        $this->assertEquals('name', $columns['name']->Field);
        $this->assertEquals('NO', $columns['name']->Null);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyReturnsColumnName(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('pg_test_categories');
        $this->assertEquals('id', $pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyColumns(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $columns = $driver->getPrimaryKeyColumns('pg_test_categories');
        $this->assertContains('id', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getIndexesIncludesUserIndex(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $indexes = $driver->getIndexes('pg_test_items');
        $this->assertNotEmpty($indexes);

        $indexNames = array_map(fn($idx) => $idx->name ?? $idx->indexname ?? '', $indexes);
        $this->assertContains('idx_pg_items_status', $indexNames);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('pg_test_items');
        $this->assertNotEmpty($fks, 'pg_test_items should have a foreign key on category_id');
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery('SELECT * FROM pg_test_categories ORDER BY id');
        $rows = $driver->loadObjectList();

        $this->assertCount(3, $rows);
        $this->assertEquals('Electronics', $rows[0]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithWhere(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("SELECT * FROM pg_test_items WHERE status = 'active'");
        $this->assertCount(3, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithJoin(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT i.title, c.name AS category_name
            FROM pg_test_items i
            JOIN pg_test_categories c ON i.category_id = c.id
            ORDER BY i.id
        ");
        $items = $driver->loadObjectList();
        $this->assertCount(4, $items);
        $this->assertEquals('Electronics', $items[0]->category_name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetSerialId(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("INSERT INTO pg_test_categories (name, slug) VALUES ('Test', 'test-serial')");
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->setQuery("SELECT name FROM pg_test_categories WHERE id = $id");
        $this->assertEquals('Test', $driver->loadResult());

        $driver->exec("DELETE FROM pg_test_categories WHERE id = $id");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("INSERT INTO pg_test_categories (name, slug) VALUES ('Before', 'update-test')");
        $driver->exec("UPDATE pg_test_categories SET name = 'After' WHERE slug = 'update-test'");

        $driver->setQuery("SELECT name FROM pg_test_categories WHERE slug = 'update-test'");
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec("DELETE FROM pg_test_categories WHERE slug = 'update-test'");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("INSERT INTO pg_test_categories (name, slug) VALUES ('Delete Me', 'delete-test')");
        $driver->exec("DELETE FROM pg_test_categories WHERE slug = 'delete-test'");

        $driver->setQuery("SELECT COUNT(*) FROM pg_test_categories WHERE slug = 'delete-test'");
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // Transactions
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCommitTransaction(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec("INSERT INTO pg_test_categories (name, slug) VALUES ('Committed', 'trans-commit')");
        $driver->transactionCommit();

        $driver->setQuery("SELECT COUNT(*) FROM pg_test_categories WHERE slug = 'trans-commit'");
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec("DELETE FROM pg_test_categories WHERE slug = 'trans-commit'");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $config = static::$testDatabases[$dbName];
        $driver2 = new Pgsql($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec("INSERT INTO pg_test_categories (name, slug) VALUES ('Rolled Back', 'trans-rollback')");
        $driver2->transactionRollback();

        $driver->setQuery("SELECT COUNT(*) FROM pg_test_categories WHERE slug = 'trans-rollback'");
        $count = (int) $driver->loadResult();

        $driver2->disconnect();

        $this->assertEquals(0, $count, 'Rolled back row should not be visible');
    }

    // =========================================================================
    // Aggregates & PostgreSQL SQL Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM pg_test_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
        $this->assertEquals(19.99, (float) $row->min_price);
        $this->assertEquals(999.99, (float) $row->max_price);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseLimitOffset(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery('SELECT * FROM pg_test_items ORDER BY id LIMIT 2 OFFSET 1');
        $items = $driver->loadObjectList();

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseBooleanLiterals(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("SELECT * FROM pg_test_categories WHERE active = TRUE ORDER BY id");
        $rows = $driver->loadObjectList();
        $this->assertCount(2, $rows);

        $driver->setQuery("SELECT * FROM pg_test_categories WHERE active = FALSE");
        $this->assertCount(1, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCoalesce(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("SELECT COALESCE(NULL, 'fallback') AS result");
        $this->assertEquals('fallback', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canConcatenateWithDoubleBar(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("SELECT 'Hello' || ' ' || 'World' AS greeting");
        $this->assertEquals('Hello World', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCaseExpression(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT title,
                CASE status
                    WHEN 'active' THEN 'Available'
                    WHEN 'draft' THEN 'Coming Soon'
                    ELSE 'Unknown'
                END AS label
            FROM pg_test_items ORDER BY id
        ");
        $items = $driver->loadObjectList();

        $this->assertEquals('Available', $items[0]->label);
        $this->assertEquals('Coming Soon', $items[3]->label);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGroupByWithHaving(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT category_id, COUNT(*) AS cnt
            FROM pg_test_items
            WHERE category_id IS NOT NULL
            GROUP BY category_id
            HAVING COUNT(*) >= 2
        ");
        $this->assertCount(2, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseSubquery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT * FROM pg_test_items
            WHERE category_id IN (
                SELECT id FROM pg_test_categories WHERE active = TRUE
            )
        ");
        $this->assertCount(4, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseExtractForDateParts(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT
                EXTRACT(YEAR FROM CURRENT_TIMESTAMP) AS yr,
                EXTRACT(MONTH FROM CURRENT_TIMESTAMP) AS mo,
                EXTRACT(DAY FROM CURRENT_TIMESTAMP) AS dy
        ");
        $row = $driver->loadObject();

        $this->assertGreaterThanOrEqual(2020, (int) $row->yr);
        $this->assertGreaterThanOrEqual(1, (int) $row->mo);
        $this->assertLessThanOrEqual(12, (int) $row->mo);
    }

    // =========================================================================
    // Sequences
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCreateAndUseSequence(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $this->assertTrue($driver->createSequence('pg_test_seq', 100));
        $this->assertTrue($driver->sequenceExists('pg_test_seq'));

        $val = $driver->nextSequenceValue('pg_test_seq');
        $this->assertEquals(100, $val);

        $val = $driver->nextSequenceValue('pg_test_seq');
        $this->assertEquals(101, $val);

        $sequences = $driver->getSequences();
        $this->assertContains('pg_test_seq', $sequences);

        $this->assertTrue($driver->dropSequence('pg_test_seq'));
        $this->assertFalse($driver->sequenceExists('pg_test_seq'));
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        // Create
        $this->assertTrue($driver->createOrReplaceView(
            'pg_active_items',
            "SELECT id, title FROM pg_test_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('pg_active_items'));

        // Query
        $driver->setQuery("SELECT COUNT(*) FROM pg_active_items");
        $this->assertEquals(3, $driver->loadResult());

        // List
        $views = $driver->getViews();
        $this->assertContains('pg_active_items', $views);

        // Replace
        $this->assertTrue($driver->createOrReplaceView(
            'pg_active_items',
            "SELECT id, title, price FROM pg_test_items WHERE status = 'active'"
        ));

        // Missing view
        $this->assertFalse($driver->viewExists('pg_nonexistent_view'));

        // Drop
        $this->assertTrue($driver->dropView('pg_active_items'));
        $this->assertFalse($driver->viewExists('pg_active_items'));
    }

    // =========================================================================
    // DDL: Column Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropColumn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, name VARCHAR(100))");

        $this->assertTrue($driver->addColumn('pg_ddl_test', 'email', 'VARCHAR(200)'));
        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $this->assertTrue($driver->dropColumn('pg_ddl_test', 'email'));
        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canModifyColumnType(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, notes VARCHAR(50))");

        $this->assertTrue($driver->modifyColumn('pg_ddl_test', 'notes', 'VARCHAR(255)'));

        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayHasKey('notes', $columns);
        $this->assertEquals('varchar(255)', $columns['notes']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRenameColumn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, old_name VARCHAR(100))");

        $this->assertTrue($driver->changeColumn('pg_ddl_test', 'old_name', 'new_name', ''));

        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayNotHasKey('old_name', $columns);
        $this->assertArrayHasKey('new_name', $columns);
    }

    // =========================================================================
    // DDL: Index Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropIndex(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, name VARCHAR(100), email VARCHAR(200))");

        $this->assertTrue($driver->addIndex('pg_ddl_test', 'idx_pg_name', 'name'));
        $indexes = $driver->getIndexes('pg_ddl_test');
        $indexNames = array_map(fn($idx) => $idx->name ?? $idx->indexname ?? '', $indexes);
        $this->assertContains('idx_pg_name', $indexNames);

        $this->assertTrue($driver->dropIndex('pg_ddl_test', 'idx_pg_name'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddUniqueIndex(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, email VARCHAR(200))");

        $this->assertTrue($driver->addUniqueIndex('pg_ddl_test', 'idx_pg_email_uniq', 'email'));

        $indexes = $driver->getIndexes('pg_ddl_test');
        $indexNames = array_map(fn($idx) => $idx->name ?? $idx->indexname ?? '', $indexes);
        $this->assertContains('idx_pg_email_uniq', $indexNames);
    }

    // =========================================================================
    // DDL: Foreign Key & Constraint Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropForeignKey(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_fk_parent (id SERIAL PRIMARY KEY, name VARCHAR(100))");
        $driver->exec("CREATE TABLE pg_fk_child (id SERIAL PRIMARY KEY, parent_id INTEGER)");

        $driver->exec(
            "ALTER TABLE pg_fk_child ADD CONSTRAINT fk_pg_child_parent"
            . " FOREIGN KEY (parent_id) REFERENCES pg_fk_parent(id)"
        );

        $fks = $driver->getForeignKeys('pg_fk_child');
        $this->assertNotEmpty($fks);

        $this->assertTrue($driver->dropConstraint('pg_fk_child', 'fk_pg_child_parent'));
    }

    // =========================================================================
    // DDL: Primary Key Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropPrimaryKey(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_pk_test (record_id INTEGER NOT NULL, name VARCHAR(100))");

        $this->assertTrue($driver->addPrimaryKey('pg_pk_test', ['record_id']));

        $pk = $driver->getPrimaryKey('pg_pk_test');
        $this->assertNotNull($pk);

        $this->assertTrue($driver->dropPrimaryKey('pg_pk_test'));
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_trunc_test (id SERIAL PRIMARY KEY, name VARCHAR(50))");
        $driver->exec("INSERT INTO pg_trunc_test (name) VALUES ('one')");
        $driver->exec("INSERT INTO pg_trunc_test (name) VALUES ('two')");

        $driver->setQuery("SELECT COUNT(*) FROM pg_trunc_test");
        $this->assertEquals(2, $driver->loadResult());

        $driver->truncateTable('pg_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM pg_trunc_test");
        $this->assertEquals(0, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRenameTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_rename_src (id SERIAL PRIMARY KEY, name VARCHAR(50))");
        $driver->exec("INSERT INTO pg_rename_src (name) VALUES ('test')");

        $driver->renameTable('pg_rename_src', 'pg_rename_dest');

        $this->assertTrue($driver->tableExists('pg_rename_dest'));
        $this->assertFalse($driver->tableExists('pg_rename_src'));

        $driver->setQuery("SELECT name FROM pg_rename_dest");
        $this->assertEquals('test', $driver->loadResult());
    }

    // =========================================================================
    // SERIAL Auto-Increment
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function serialAutoIncrementsCorrectly(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_serial_test (id SERIAL PRIMARY KEY, label VARCHAR(50))");

        $driver->exec("INSERT INTO pg_serial_test (label) VALUES ('first')");
        $id1 = $driver->insertid();

        $driver->exec("INSERT INTO pg_serial_test (label) VALUES ('second')");
        $id2 = $driver->insertid();

        $this->assertGreaterThan(0, $id1);
        $this->assertEquals($id1 + 1, $id2);
    }

    // =========================================================================
    // Auto-Increment Management
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetAndSetAutoIncrement(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_serial_test (id SERIAL PRIMARY KEY, label VARCHAR(50))");
        $driver->exec("INSERT INTO pg_serial_test (label) VALUES ('first')");

        $current = $driver->getAutoIncrement('pg_serial_test');
        $this->assertGreaterThan(0, $current);

        $driver->setAutoIncrement('pg_serial_test', 100);
        $driver->exec("INSERT INTO pg_serial_test (label) VALUES ('hundredth')");
        $id = $driver->insertid();

        $this->assertGreaterThanOrEqual(100, $id);
    }

    // =========================================================================
    // Query Builder Integration
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithLimit(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pg_test_items')
            ->order('id', 'asc')
            ->limit(2)
            ->fetch('rows');

        $this->assertCount(2, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithOffset(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pg_test_items')
            ->order('id', 'asc')
            ->limit(2)
            ->start(1)
            ->fetch('rows');

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderWhereInSubquery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pg_test_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('pg_test_categories')
                    ->whereEquals('active', true);
            })
            ->fetch('rows');

        $this->assertCount(4, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderHandlesEmptyWhereIn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pg_test_items')
            ->whereIn('id', [])
            ->fetch('rows');

        $this->assertCount(0, $items);
    }

    // =========================================================================
    // Feature Detection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function featureDetectionReturnsExpectedValues(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $this->assertFalse($driver->supportsColumnPositioning());
        $this->assertFalse($driver->supportsEngine());
        $this->assertTrue($driver->supportsDropColumn());
        $this->assertTrue($driver->supportsRenameColumn());
        $this->assertTrue($driver->supportsJson());
        $this->assertTrue($driver->supportsWindowFunctions());
        $this->assertTrue($driver->supportsCTE());
        $this->assertTrue($driver->supportsRegexp());
    }

    // =========================================================================
    // Schema Builder Integration
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCreateTableBuilder(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->createTable('pg_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('pg_ddl_test'));

        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAlterTableBuilder(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPgsql($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE pg_ddl_test (id SERIAL PRIMARY KEY, name VARCHAR(100))");

        $driver->alterTable('pg_ddl_test')
            ->addColumn('email', 'VARCHAR(200)', ['nullable' => true])
            ->execute();

        $columns = $driver->getTableColumns('pg_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);
    }
}
