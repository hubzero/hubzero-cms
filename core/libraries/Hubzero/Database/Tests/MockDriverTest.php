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
use Hubzero\Database\Driver\Mock;
use Hubzero\Database\Driver\Sqlite;
use Hubzero\Database\Query;

/**
 * Mock driver-specific tests
 *
 * The Mock driver extends Sqlite with an in-memory (:memory:) database
 * and zero configuration. These tests verify Mock-specific behaviour
 * such as isolation, inheritance, and basic SQLite operations executed
 * through the Mock wrapper.
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Mock driver pass with a no-op assertion.
 *
 * Configuration: Add 'mock' to DB_TEST_BACKENDS in phpunit.xml
 * (this is the default backend when none is specified).
 */
#[Group('mock')]
#[Group('database')]
class MockDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['mk_items', 'mk_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (get_class($driver) !== Mock::class) {
            return;
        }

        foreach (['mk_items', 'mk_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE mk_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INTEGER DEFAULT 0,
                active INTEGER DEFAULT 1
            )
        ");

        $driver->exec("
            CREATE TABLE mk_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price REAL DEFAULT 0.00,
                stock INTEGER DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                FOREIGN KEY (category_id)
                    REFERENCES mk_categories(id) ON DELETE SET NULL
            )
        ");

        $query = new Query($driver);
        $query->insertMany('mk_categories', [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'sort_order' => 1,
                'active' => 1,
            ],
            [
                'name' => 'Books',
                'slug' => 'books',
                'sort_order' => 2,
                'active' => 1,
            ],
            [
                'name' => 'Archived',
                'slug' => 'archived',
                'sort_order' => 99,
                'active' => 0,
            ],
        ]);

        $query->insertMany('mk_items', [
            [
                'category_id' => 1,
                'title' => 'Laptop',
                'price' => 999.99,
                'stock' => 10,
                'status' => 'active',
            ],
            [
                'category_id' => 1,
                'title' => 'Phone',
                'price' => 599.99,
                'stock' => 25,
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'title' => 'Novel',
                'price' => 19.99,
                'stock' => 100,
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'title' => 'Textbook',
                'price' => 79.99,
                'stock' => 5,
                'status' => 'draft',
            ],
        ]);
    }

    private function requiresMock(Driver $driver): bool
    {
        if (get_class($driver) !== Mock::class) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocTables = [
            'mk_ddl_test', 'mk_isolation_test',
        ];

        foreach (static::getClassDrivers() as $driver) {
            if (get_class($driver) !== Mock::class) {
                continue;
            }
            foreach ($adHocTables as $table) {
                try {
                    $driver->dropTable($table, true);
                } catch (\Exception $e) {
                }
            }
        }
    }

    // =========================================================================
    // Identity & Inheritance
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function driverIsMockAndSqliteInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $this->assertSame(Mock::class, get_class($driver));
        $this->assertInstanceOf(Sqlite::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function driverIsNotPlainSqlite(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        // Mock is a subclass of Sqlite, not Sqlite itself
        $this->assertNotEquals(
            Sqlite::class,
            get_class($driver)
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function constructorIgnoresOptions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        // Create a new Mock with arbitrary options — all should be ignored
        $mock = new Mock([
            'database' => '/tmp/should_be_ignored.db',
            'host'     => 'should.be.ignored',
            'user'     => 'nobody',
            'password' => 'secret',
            'prefix'   => 'ignored_',
        ]);

        // It should still work as in-memory SQLite
        $mock->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $mock->loadResult());

        // Prefix should be empty, not 'ignored_'
        $this->assertEquals('', $mock->getPrefix());
    }

    // =========================================================================
    // Connection & Basic Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $this->assertNotEmpty($version);
        $this->assertMatchesRegularExpression('/^\d+\./', $version);
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('mk_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalseForMissingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $this->assertFalse($driver->tableExists('nonexistent_table'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $tables = $driver->getTableList();
        $this->assertContains('mk_categories', $tables);
        $this->assertContains('mk_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('mk_categories', true);

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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('mk_categories');
        $this->assertEquals('id', $pk);
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM mk_categories ORDER BY id'
        );
        $rows = $driver->loadObjectList();

        $this->assertCount(3, $rows);
        $this->assertEquals('Electronics', $rows[0]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithWhere(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM mk_items WHERE status = 'active'"
            . " ORDER BY id"
        );
        $rows = $driver->loadObjectList();

        $this->assertCount(3, $rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithJoin(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT i.title, c.name AS category"
            . " FROM mk_items i"
            . " INNER JOIN mk_categories c"
            . "   ON i.category_id = c.id"
            . " ORDER BY i.id"
        );
        $rows = $driver->loadObjectList();

        $this->assertCount(4, $rows);
        $this->assertEquals('Electronics', $rows[0]->category);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetAutoIncrementId(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mk_categories (name, slug)"
            . " VALUES ('Test', 'test-auto')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->exec(
            "DELETE FROM mk_categories WHERE id = $id"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mk_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE mk_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM mk_categories"
            . " WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM mk_categories"
            . " WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO mk_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM mk_categories"
            . " WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM mk_categories"
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO mk_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM mk_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM mk_categories"
            . " WHERE slug = 'trans-commit'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO mk_categories (name, slug)"
            . " VALUES ('RolledBack', 'trans-rollback')"
        );
        $driver->transactionRollback();

        $driver->setQuery(
            "SELECT COUNT(*) FROM mk_categories"
            . " WHERE slug = 'trans-rollback'"
        );
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // In-Memory Isolation
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function newMockInstanceIsIsolated(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        // The shared driver has mk_categories table
        $this->assertTrue($driver->tableExists('mk_categories'));

        // A brand new Mock instance should have an empty database
        $fresh = new Mock();
        $this->assertFalse($fresh->tableExists('mk_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function twoMockInstancesAreIndependent(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $mockA = new Mock();
        $mockB = new Mock();

        // Create table in A only
        $mockA->exec(
            "CREATE TABLE mk_isolation_test"
            . " (id INTEGER PRIMARY KEY, val TEXT)"
        );
        $mockA->exec(
            "INSERT INTO mk_isolation_test (id, val)"
            . " VALUES (1, 'hello')"
        );

        // A should see the table
        $this->assertTrue($mockA->tableExists('mk_isolation_test'));

        // B should NOT see the table
        $this->assertFalse($mockB->tableExists('mk_isolation_test'));
    }

    // =========================================================================
    // SQLite Features via Mock
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM mk_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseLimitOffset(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM mk_items ORDER BY id LIMIT 2 OFFSET 1"
        );
        $rows = $driver->loadObjectList();

        $this->assertCount(2, $rows);
        $this->assertEquals('Phone', $rows[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCoalesce(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT COALESCE(description, 'N/A') AS descr"
            . " FROM mk_items WHERE id = 1"
        );
        $result = $driver->loadResult();

        $this->assertEquals('N/A', $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCaseExpression(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT title,
                   CASE WHEN price > 500 THEN 'expensive'
                        ELSE 'affordable' END AS tier
            FROM mk_items ORDER BY id
        ");
        $rows = $driver->loadObjectList();

        $this->assertEquals('expensive', $rows[0]->tier);
        $this->assertEquals('affordable', $rows[2]->tier);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseGroupByAndHaving(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT category_id, COUNT(*) AS cnt
            FROM mk_items
            GROUP BY category_id
            HAVING COUNT(*) >= 2
            ORDER BY category_id
        ");
        $rows = $driver->loadObjectList();

        $this->assertCount(2, $rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseSubquery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT * FROM mk_items
            WHERE category_id IN (
                SELECT id FROM mk_categories WHERE active = 1
            )
            ORDER BY id
        ");
        $rows = $driver->loadObjectList();

        $this->assertCount(4, $rows);
    }

    // =========================================================================
    // DDL Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCreateAndDropTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE mk_ddl_test"
            . " (id INTEGER PRIMARY KEY, name TEXT)"
        );
        $this->assertTrue($driver->tableExists('mk_ddl_test'));

        $driver->dropTable('mk_ddl_test');
        $this->assertFalse($driver->tableExists('mk_ddl_test'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddColumn(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE mk_ddl_test"
            . " (id INTEGER PRIMARY KEY, name TEXT)"
        );
        $driver->exec(
            "ALTER TABLE mk_ddl_test ADD COLUMN email TEXT"
        );

        $columns = $driver->getTableColumns('mk_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $driver->dropTable('mk_ddl_test');
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('mk_items')
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('mk_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('mk_categories')
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        // Mock inherits Sqlite feature flags
        $this->assertFalse($driver->supportsEngine());
        $this->assertFalse($driver->supportsEnum());
        $this->assertFalse($driver->supportsUnsigned());
        // SQLite supports column positioning via table recreation
        $this->assertTrue($driver->supportsColumnPositioning());
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
        if (!$this->requiresMock($driver)) {
            return;
        }

        $driver->createTable('mk_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('mk_ddl_test'));

        $columns = $driver->getTableColumns('mk_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);

        $driver->dropTable('mk_ddl_test');
    }
}
