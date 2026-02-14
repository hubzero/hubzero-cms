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
use Hubzero\Database\Drivers\Cubrid\CubridDriver as Cubrid;

/**
 * CUBRID-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Cubrid driver pass with a no-op assertion.
 *
 * Configuration: Add 'cubrid' to DB_TEST_BACKENDS and set DB_CUBRID_*
 * credentials in phpunit.xml or via environment variables.
 *
 * CUBRID is an open-source RDBMS optimized for web applications with
 * web-oriented SQL extensions, MVCC, native Java stored procedures,
 * high-availability, and database sharding support.
 */
#[Group('cubrid')]
#[Group('database')]
class CubridDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['cubrid_items', 'cubrid_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!($driver instanceof Cubrid)) {
            return;
        }

        foreach (['cubrid_items', 'cubrid_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE cubrid_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INT DEFAULT 0,
                active TINYINT DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $driver->exec("
            CREATE TABLE cubrid_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                title VARCHAR(200) NOT NULL,
                description STRING,
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INT DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_cubrid_items_status (status),
                CONSTRAINT fk_cubrid_items_cat FOREIGN KEY (category_id)
                    REFERENCES cubrid_categories(id) ON DELETE SET NULL
            )
        ");

        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO cubrid_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO cubrid_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO cubrid_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO cubrid_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    private function requiresCubrid(Driver $driver): bool
    {
        if (!($driver instanceof Cubrid)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['cubrid_test_view', 'cubrid_active_items'];
        $adHocTables = [
            'cubrid_rename_dest', 'cubrid_rename_src',
            'cubrid_fk_child', 'cubrid_fk_parent',
            'cubrid_ddl_test', 'cubrid_trunc_test',
            'cubrid_auto_test',
        ];

        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Cubrid)) {
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
            if (!($driver instanceof Cubrid)) {
                continue;
            }
            try {
                $driver->dropView('cubrid_active_items', true);
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
    public function driverIsCubridInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $this->assertInstanceOf(Cubrid::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresCubrid($driver)) {
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('driver_version', $info);
        $this->assertArrayHasKey('comment', $info);
        $this->assertEquals('CUBRID Database', $info['comment']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function serverInfoDriverVersionIsNormalized(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $info = $driver->getServerInfo();
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('driver_version', $info);
        $this->assertNotEmpty($info['version']);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', (string) $info['driver_version']);
        $this->assertStringContainsString((string) $info['driver_version'], (string) $info['version']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getVersionMatchesServerInfoVersion(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $info = $driver->getServerInfo();

        $this->assertSame((string) $info['version'], (string) $version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function sqlHelpersEmitExpectedCubridExpressions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
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
    // Schema Introspection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrueForExistingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('cubrid_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $tables = $driver->getTableList();
        $this->assertContains('cubrid_categories', $tables);
        $this->assertContains('cubrid_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('cubrid_categories', true);

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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('cubrid_categories');
        $this->assertEquals('id', $pk);
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM cubrid_categories ORDER BY id'
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug)"
            . " VALUES ('Test', 'test-auto')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->exec(
            "DELETE FROM cubrid_categories WHERE id = $id"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE cubrid_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM cubrid_categories"
            . " WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM cubrid_categories"
            . " WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM cubrid_categories WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM cubrid_categories"
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO cubrid_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM cubrid_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM cubrid_categories"
            . " WHERE slug = 'trans-commit'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $config = static::$testDatabases[$dbName];
        $driver2 = new Cubrid($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec(
            "INSERT INTO cubrid_categories (name, slug)"
            . " VALUES ('Rolled Back', 'trans-rollback')"
        );
        $driver2->transactionRollback();

        $driver->setQuery(
            "SELECT COUNT(*) FROM cubrid_categories"
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
    // CUBRID-Specific Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseRegexp(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        // Check if database charset supports REGEXP with UTF-8 patterns
        // CUBRID's REGEXP requires matching charsets between pattern and column
        try {
            // Create a test table to check charset
            $driver->exec("CREATE TABLE _charset_check (c VARCHAR(10))");
            $driver->setQuery("SHOW CREATE TABLE _charset_check");
            $result = $driver->loadRow();
            $driver->exec("DROP TABLE _charset_check");

            // Check if using UTF-8 collation
            $isUtf8 = stripos($result[1], 'utf8') !== false;

            if (!$isUtf8) {
                $this->markTestSkipped(
                    'CUBRID database must be created with UTF-8 charset for REGEXP tests. '
                    . 'Recreate with: cubrid createdb demodb en_US.utf8'
                );
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Cannot determine database charset: ' . $e->getMessage());
        }

        $driver->setQuery(
            "SELECT * FROM cubrid_items WHERE title REGEXP '^L'"
        );
        $items = $driver->loadObjectList();

        $this->assertCount(1, $items);
        $this->assertEquals('Laptop', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM cubrid_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $this->assertTrue($driver->createOrReplaceView(
            'cubrid_active_items',
            "SELECT id, title FROM cubrid_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('cubrid_active_items'));

        $driver->setQuery("SELECT COUNT(*) FROM cubrid_active_items");
        $this->assertEquals(3, $driver->loadResult());

        $this->assertTrue($driver->dropView('cubrid_active_items'));
        $this->assertFalse($driver->viewExists('cubrid_active_items'));
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE cubrid_ddl_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(100))"
        );

        $this->assertTrue(
            $driver->addColumn('cubrid_ddl_test', 'email', 'VARCHAR(200)')
        );
        $columns = $driver->getTableColumns('cubrid_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $this->assertTrue(
            $driver->dropColumn('cubrid_ddl_test', 'email')
        );
        $columns = $driver->getTableColumns('cubrid_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE cubrid_trunc_test"
            . " (id INT AUTO_INCREMENT PRIMARY KEY,"
            . " name VARCHAR(50))"
        );
        $driver->exec(
            "INSERT INTO cubrid_trunc_test (name) VALUES ('one')"
        );
        $driver->exec(
            "INSERT INTO cubrid_trunc_test (name) VALUES ('two')"
        );

        $driver->truncateTable('cubrid_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM cubrid_trunc_test");
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('cubrid_items')
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('cubrid_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('cubrid_categories')
                    ->whereEquals('active', 1);
            })
            ->fetch('rows');

        $this->assertCount(4, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithJoinDoesNotEmitFromAfterSet(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $sql = $driver->getQuery()
            ->update('cubrid_items')
            ->innerJoin('cubrid_categories', 'cubrid_items.category_id', 'cubrid_categories.id')
            ->set(['cubrid_items.status' => 'active'])
            ->whereEquals('cubrid_categories.active', 1)
            ->toString();

        $this->assertStringContainsString('UPDATE', strtoupper($sql));
        $this->assertStringContainsString('JOIN', strtoupper($sql));

        $setPos = null;
        $fromPos = null;

        if (preg_match('/\bSET\b/i', $sql, $setMatch, PREG_OFFSET_CAPTURE)) {
            $setPos = $setMatch[0][1];
        }
        if (preg_match('/\bFROM\b/i', $sql, $fromMatch, PREG_OFFSET_CAPTURE)) {
            $fromPos = $fromMatch[0][1];
        }

        $this->assertNotNull($setPos, 'Expected UPDATE SQL to include SET clause. SQL: ' . $sql);
        $this->assertTrue(
            $fromPos === null || $fromPos < $setPos,
            'CUBRID UPDATE+JOIN SQL must not place FROM after SET. SQL: ' . $sql
        );
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
        if (!$this->requiresCubrid($driver)) {
            return;
        }

        $driver->createTable('cubrid_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('cubrid_ddl_test'));

        $columns = $driver->getTableColumns('cubrid_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }
}
