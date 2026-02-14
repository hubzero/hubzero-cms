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
use Hubzero\Database\Driver\Mysql;

/**
 * MySQL-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Mysql driver pass with a no-op assertion.
 *
 * Configuration: Add 'mysql' to DB_TEST_BACKENDS and set DB_MYSQL_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('mysql')]
#[Group('database')]
class MysqlDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['my_items', 'my_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!static::isMysqlOnly($driver)) {
            return;
        }

        foreach (['my_items', 'my_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE my_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INT DEFAULT 0,
                active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec("
            CREATE TABLE my_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INT DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_my_items_status (status),
                INDEX idx_my_items_cat (category_id),
                CONSTRAINT fk_my_items_cat FOREIGN KEY (category_id)
                    REFERENCES my_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $driver->exec(
            "INSERT INTO my_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO my_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO my_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO my_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO my_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO my_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO my_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    /**
     * Check if driver is exactly Mysql
     */
    private static function isMysqlOnly(Driver $driver): bool
    {
        return get_class($driver) === Mysql::class;
    }

    private function requiresMysql(Driver $driver): bool
    {
        if (!static::isMysqlOnly($driver)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['my_test_view', 'my_active_items'];
        $adHocTables = [
            'my_rename_dest', 'my_rename_src',
            'my_fk_child', 'my_fk_parent',
            'my_ddl_test', 'my_trunc_test',
            'my_auto_test', 'my_engine_test',
        ];

        foreach (static::getClassDrivers() as $driver) {
            if (!static::isMysqlOnly($driver)) {
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
            if (!static::isMysqlOnly($driver)) {
                continue;
            }
            try {
                $driver->dropView('my_active_items', true);
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
    public function driverIsMysqlInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertInstanceOf(Mysql::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMysql($driver)) {
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('driver_version', $info);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function serverInfoDriverVersionIsNormalized(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('driver_version', $info);
        $this->assertNotEmpty($info['version']);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', (string) $info['driver_version']);
        $this->assertStringStartsWith((string) $info['driver_version'], (string) $info['version']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getVersionMatchesServerInfoVersion(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $info = $driver->getServerInfo();

        $this->assertSame((string) $info['version'], (string) $version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function sqlHelpersEmitExpectedMysqlFamilyExpressions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertSame("title REGEXP '^L'", $driver->sqlRegexp('title', '^L'));
        $this->assertSame("title NOT REGEXP '^L'", $driver->sqlRegexp('title', '^L', true));
        $this->assertSame("DATE_SUB(created_at, INTERVAL 7 DAY)", $driver->sqlDateSub('created_at', 7, 'day'));
        $this->assertSame("DATE_ADD(created_at, INTERVAL 2 HOUR)", $driver->sqlDateAdd('created_at', 2, 'hour'));
        $this->assertSame("DATE_FORMAT(created_at, '%Y-%m-%d')", $driver->sqlDateFormat('created_at', '%Y-%m-%d'));
        $this->assertSame("YEAR(created_at)", $driver->sqlYear('created_at'));
        $this->assertSame("MONTH(created_at)", $driver->sqlMonth('created_at'));
        $this->assertSame("UNIX_TIMESTAMP(created_at)", $driver->sqlUnixTimestamp('created_at'));
        $this->assertSame("SUBSTRING_INDEX(title, '-', 2)", $driver->sqlSubstringIndex('title', '-', 2));
        $this->assertSame("CONCAT(first_name, ' ', last_name)", $driver->sqlConcat(['first_name', "' '", 'last_name']));
        $this->assertSame(
            "CONCAT_WS(', ', first_name, last_name)",
            $driver->sqlConcatWs(', ', ['first_name', 'last_name'])
        );
    }

    // =========================================================================
    // Schema Introspection via Driver API
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrueForExistingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('my_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalseForMissingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertFalse(
            $driver->tableExists('my_nonexistent_table')
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $tables = $driver->getTableList();
        $this->assertContains('my_categories', $tables);
        $this->assertContains('my_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('my_categories', true);

        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('active', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsFullInfo(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('my_categories', false);

        $this->assertArrayHasKey('name', $columns);
        $this->assertIsObject($columns['name']);
        $this->assertEquals('name', $columns['name']->Field);
        $this->assertEquals('NO', $columns['name']->Null);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyReturnsColumnName(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('my_categories');
        $this->assertEquals('id', $pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('my_items');
        $this->assertNotEmpty(
            $fks,
            'my_items should have a foreign key on category_id'
        );
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery('SELECT * FROM my_categories ORDER BY id');
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM my_items WHERE status = 'active'"
        );
        $this->assertCount(3, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetAutoIncrementId(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO my_categories (name, slug)"
            . " VALUES ('Test', 'test-auto')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->setQuery(
            "SELECT name FROM my_categories WHERE id = $id"
        );
        $this->assertEquals('Test', $driver->loadResult());

        $driver->exec("DELETE FROM my_categories WHERE id = $id");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO my_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE my_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM my_categories WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM my_categories WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO my_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM my_categories WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM my_categories"
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO my_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM my_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM my_categories WHERE slug = 'trans-commit'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $config = static::$testDatabases[$dbName];
        $driver2 = new Mysql($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec(
            "INSERT INTO my_categories (name, slug)"
            . " VALUES ('Rolled Back', 'trans-rollback')"
        );
        $driver2->transactionRollback();

        $driver->setQuery(
            "SELECT COUNT(*) FROM my_categories"
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
    // MySQL-Specific Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseLimitOffset(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM my_items ORDER BY id LIMIT 2 OFFSET 1'
        );
        $items = $driver->loadObjectList();

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseIfNull(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT IFNULL(NULL, 'fallback') AS result"
        );
        $this->assertEquals('fallback', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseConcat(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT CONCAT('Hello', ' ', 'World') AS greeting"
        );
        $this->assertEquals('Hello World', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseRegexp(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM my_items WHERE title REGEXP '^L'"
        );
        $items = $driver->loadObjectList();

        $this->assertCount(1, $items);
        $this->assertEquals('Laptop', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasInnoDBEngine(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM my_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
        $this->assertEquals(19.99, (float) $row->min_price);
        $this->assertEquals(999.99, (float) $row->max_price);
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertTrue($driver->createOrReplaceView(
            'my_active_items',
            "SELECT id, title FROM my_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('my_active_items'));

        $driver->setQuery("SELECT COUNT(*) FROM my_active_items");
        $this->assertEquals(3, $driver->loadResult());

        $this->assertTrue($driver->dropView('my_active_items'));
        $this->assertFalse($driver->viewExists('my_active_items'));
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE my_ddl_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(100)) ENGINE=InnoDB"
        );

        $this->assertTrue(
            $driver->addColumn('my_ddl_test', 'email', 'VARCHAR(200)')
        );
        $columns = $driver->getTableColumns('my_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $this->assertTrue(
            $driver->dropColumn('my_ddl_test', 'email')
        );
        $columns = $driver->getTableColumns('my_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE my_trunc_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(50)) ENGINE=InnoDB"
        );
        $driver->exec(
            "INSERT INTO my_trunc_test (name) VALUES ('one')"
        );
        $driver->exec(
            "INSERT INTO my_trunc_test (name) VALUES ('two')"
        );

        $driver->truncateTable('my_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM my_trunc_test");
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // Auto-Increment Management
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function autoIncrementWorksCorrectly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE my_auto_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " label VARCHAR(50)) ENGINE=InnoDB"
        );

        $driver->exec(
            "INSERT INTO my_auto_test (label) VALUES ('first')"
        );
        $id1 = $driver->insertid();

        $driver->exec(
            "INSERT INTO my_auto_test (label) VALUES ('second')"
        );
        $id2 = $driver->insertid();

        $this->assertGreaterThan(0, $id1);
        $this->assertEquals($id1 + 1, $id2);
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('my_items')
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('my_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('my_categories')
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $this->assertTrue($driver->supportsEngine());
        $this->assertTrue($driver->supportsEnum());
        $this->assertTrue($driver->supportsFulltext());
        $this->assertTrue($driver->supportsUnsigned());
        $this->assertTrue($driver->supportsColumnComments());
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
        if (!$this->requiresMysql($driver)) {
            return;
        }

        $driver->createTable('my_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('my_ddl_test'));

        $columns = $driver->getTableColumns('my_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }
}
