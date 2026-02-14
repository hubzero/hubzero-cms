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
use Hubzero\Database\Drivers\Percona\PerconaDriver as Percona;

/**
 * Percona Server-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Percona driver pass with a no-op assertion.
 *
 * Configuration: Add 'percona' to DB_TEST_BACKENDS and set DB_PERCONA_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('percona')]
#[Group('database')]
class PerconaDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['pc_items', 'pc_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!($driver instanceof Percona)) {
            return;
        }

        foreach (['pc_items', 'pc_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE pc_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INT DEFAULT 0,
                active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec("
            CREATE TABLE pc_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INT DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_pc_items_status (status),
                CONSTRAINT fk_pc_items_cat FOREIGN KEY (category_id)
                    REFERENCES pc_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec(
            "INSERT INTO pc_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO pc_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO pc_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO pc_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO pc_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO pc_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO pc_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    private function requiresPercona(Driver $driver): bool
    {
        if (!($driver instanceof Percona)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['pc_test_view', 'pc_active_items'];
        $adHocTables = [
            'pc_ddl_test', 'pc_trunc_test', 'pc_auto_test',
        ];

        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Percona)) {
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
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Percona)) {
                continue;
            }
            try {
                $driver->dropView('pc_active_items', true);
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
    public function driverIsPerconaInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $this->assertInstanceOf(Percona::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $this->assertNotEmpty($version);
        $this->assertMatchesRegularExpression('/^\d+\./', $version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetServerInfo(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('pc_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $tables = $driver->getTableList();
        $this->assertContains('pc_categories', $tables);
        $this->assertContains('pc_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('pc_categories', true);

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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('pc_categories');
        $this->assertEquals('id', $pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('pc_items');
        $this->assertNotEmpty($fks);
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM pc_categories ORDER BY id'
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO pc_categories (name, slug)"
            . " VALUES ('Test', 'test-auto')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->exec(
            "DELETE FROM pc_categories WHERE id = $id"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO pc_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE pc_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM pc_categories WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM pc_categories WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO pc_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM pc_categories WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM pc_categories"
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO pc_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM pc_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM pc_categories WHERE slug = 'trans-commit'"
        );
    }

    // =========================================================================
    // Percona-Specific Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasInnoDBEngine(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $this->assertTrue($driver->hasEngine('InnoDB'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM pc_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseRegexp(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM pc_items WHERE title REGEXP '^L'"
        );
        $items = $driver->loadObjectList();

        $this->assertCount(1, $items);
        $this->assertEquals('Laptop', $items[0]->title);
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $this->assertTrue($driver->createOrReplaceView(
            'pc_active_items',
            "SELECT id, title FROM pc_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('pc_active_items'));

        $driver->setQuery("SELECT COUNT(*) FROM pc_active_items");
        $this->assertEquals(3, $driver->loadResult());

        $this->assertTrue($driver->dropView('pc_active_items'));
        $this->assertFalse($driver->viewExists('pc_active_items'));
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pc_items')
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('pc_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('pc_categories')
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
        if (!$this->requiresPercona($driver)) {
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
        if (!$this->requiresPercona($driver)) {
            return;
        }

        $driver->createTable('pc_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('pc_ddl_test'));

        $columns = $driver->getTableColumns('pc_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }
}
