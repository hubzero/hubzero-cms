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
use Hubzero\Database\Driver\Mariadb;

/**
 * MariaDB-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Mariadb driver pass with a no-op assertion.
 *
 * Configuration: Add 'mariadb' to DB_TEST_BACKENDS and set DB_MARIADB_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('mariadb')]
#[Group('database')]
class MariadbDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['mdb_items', 'mdb_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (get_class($driver) !== Mariadb::class) {
            return;
        }

        foreach (['mdb_items', 'mdb_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE mdb_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INT DEFAULT 0,
                active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec("
            CREATE TABLE mdb_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INT DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_mdb_items_status (status),
                CONSTRAINT fk_mdb_items_cat FOREIGN KEY (category_id)
                    REFERENCES mdb_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec(
            "INSERT INTO mdb_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO mdb_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO mdb_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO mdb_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO mdb_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO mdb_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO mdb_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    private function requiresMariadb(Driver $driver): bool
    {
        if (get_class($driver) !== Mariadb::class) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['mdb_test_view', 'mdb_active_items'];
        $adHocTables = [
            'mdb_rename_dest', 'mdb_rename_src',
            'mdb_fk_child', 'mdb_fk_parent',
            'mdb_ddl_test', 'mdb_trunc_test',
            'mdb_auto_test', 'mdb_seq_table',
        ];
        $adHocSequences = ['mdb_test_seq'];

        foreach (static::getClassDrivers() as $driver) {
            if (get_class($driver) !== Mariadb::class) {
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
        foreach (static::getClassDrivers() as $driver) {
            if (get_class($driver) !== Mariadb::class) {
                continue;
            }
            try {
                $driver->dropView('mdb_active_items', true);
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
    public function driverIsMariadbInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $this->assertSame(Mariadb::class, get_class($driver));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $this->assertNotEmpty($version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetServerInfo(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('version', $info);
    }

    // =========================================================================
    // Schema Introspection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrueForExistingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('mdb_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $tables = $driver->getTableList();
        $this->assertContains('mdb_categories', $tables);
        $this->assertContains('mdb_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('mdb_categories', true);

        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('active', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyReturnsColumnName(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('mdb_categories');
        $this->assertEquals('id', $pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('mdb_items');
        $this->assertNotEmpty($fks);
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM mdb_categories ORDER BY id'
        );
        $rows = $driver->loadObjectList();

        $this->assertCount(3, $rows);
        $this->assertEquals('Electronics', $rows[0]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetAutoIncrementId(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mdb_categories (name, slug)"
            . " VALUES ('Test', 'test-auto')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->exec(
            "DELETE FROM mdb_categories WHERE id = $id"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mdb_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE mdb_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM mdb_categories"
            . " WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM mdb_categories"
            . " WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mdb_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM mdb_categories WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM mdb_categories"
            . " WHERE slug = 'delete-test'"
        );
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // Transactions
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCommitTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO mdb_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM mdb_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM mdb_categories"
            . " WHERE slug = 'trans-commit'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $config = static::$testDatabases[$dbName];
        $driver2 = new Mariadb($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec(
            "INSERT INTO mdb_categories (name, slug)"
            . " VALUES ('Rolled Back', 'trans-rollback')"
        );
        $driver2->transactionRollback();

        $driver->setQuery(
            "SELECT COUNT(*) FROM mdb_categories"
            . " WHERE slug = 'trans-rollback'"
        );
        $count = (int) $driver->loadResult();

        $driver2->disconnect();

        $this->assertEquals(
            0,
            $count,
            'Rolled back row should not be visible'
        );
    }

    // =========================================================================
    // MariaDB-Specific Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseRegexp(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM mdb_items WHERE title REGEXP '^L'"
        );
        $items = $driver->loadObjectList();

        $this->assertCount(1, $items);
        $this->assertEquals('Laptop', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasAriaOrInnoDBEngine(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        // MariaDB always has InnoDB
        $this->assertTrue($driver->hasEngine('InnoDB'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM mdb_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
    }

    // =========================================================================
    // Sequences (MariaDB 10.3+)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function sequenceSupportReflectsVersion(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        // Just verify the method works — result depends on version
        $this->assertIsBool($driver->supportsSequences());
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $this->assertTrue($driver->createOrReplaceView(
            'mdb_active_items',
            "SELECT id, title FROM mdb_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('mdb_active_items'));

        $driver->setQuery("SELECT COUNT(*) FROM mdb_active_items");
        $this->assertEquals(3, $driver->loadResult());

        $this->assertTrue($driver->dropView('mdb_active_items'));
        $this->assertFalse($driver->viewExists('mdb_active_items'));
    }

    // =========================================================================
    // DDL
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropColumn(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE mdb_ddl_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(100)) ENGINE=InnoDB"
        );

        $this->assertTrue(
            $driver->addColumn('mdb_ddl_test', 'email', 'VARCHAR(200)')
        );
        $columns = $driver->getTableColumns('mdb_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $this->assertTrue(
            $driver->dropColumn('mdb_ddl_test', 'email')
        );
        $columns = $driver->getTableColumns('mdb_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE mdb_trunc_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(50)) ENGINE=InnoDB"
        );
        $driver->exec(
            "INSERT INTO mdb_trunc_test (name) VALUES ('one')"
        );
        $driver->exec(
            "INSERT INTO mdb_trunc_test (name) VALUES ('two')"
        );

        $driver->truncateTable('mdb_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM mdb_trunc_test");
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // Query Builder Integration
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithLimit(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('mdb_items')
            ->order('id', 'asc')
            ->limit(2)
            ->fetch('rows');

        $this->assertCount(2, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderWhereInSubquery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('mdb_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('mdb_categories')
                    ->whereEquals('active', 1);
            })
            ->fetch('rows');

        $this->assertCount(4, $items);
    }

    // =========================================================================
    // Feature Detection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function featureDetectionReturnsExpectedValues(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $this->assertTrue($driver->supportsEngine());
        $this->assertTrue($driver->supportsEnum());
        $this->assertTrue($driver->supportsFulltext());
        $this->assertTrue($driver->supportsUnsigned());
        $this->assertTrue($driver->supportsColumnPositioning());
        $this->assertTrue($driver->supportsRegexp());
    }

    // =========================================================================
    // Schema Builder Integration
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCreateTableBuilder(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMariadb($driver)) {
            return;
        }

        $driver->createTable('mdb_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('mdb_ddl_test'));

        $columns = $driver->getTableColumns('mdb_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }
}
