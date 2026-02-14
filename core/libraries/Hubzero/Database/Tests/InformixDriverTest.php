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
use Hubzero\Database\Drivers\Informix\InformixDriver as Informix;
use Hubzero\Database\Query;

/**
 * Informix-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Informix driver pass with a no-op assertion.
 * If multiple Informix instances are configured, each test runs against
 * all of them.
 *
 * Configuration: Add 'informix' to DB_TEST_BACKENDS and set DB_INFORMIX_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('informix')]
#[Group('database')]
class InformixDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['ix_items', 'ix_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!($driver instanceof Informix)) {
            return;
        }

        // Drop in FK-safe order
        foreach (['ix_items', 'ix_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE ix_categories (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100),
                sort_order INTEGER DEFAULT 0,
                active SMALLINT DEFAULT 1
            )
        ");

        $driver->exec("
            CREATE TABLE ix_items (
                id SERIAL PRIMARY KEY,
                category_id INTEGER,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INTEGER DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                FOREIGN KEY (category_id) REFERENCES ix_categories(id)
            )
        ");

        $driver->exec(
            "INSERT INTO ix_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO ix_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO ix_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO ix_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO ix_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO ix_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO ix_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    /**
     * Check if the driver is Informix; if not, pass with no-op assertion.
     */
    private function requiresInformix(Driver $driver): bool
    {
        if (!($driver instanceof Informix)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up ad-hoc objects created by individual tests
        $adHocTables = [
            'ix_rename_dest', 'ix_rename_src',
            'ix_fk_child', 'ix_fk_parent',
            'ix_ddl_test', 'ix_trunc_test',
            'ix_serial_test', 'ix_view_src',
        ];
        $adHocViews = ['ix_test_view', 'ix_replace_view'];
        $adHocSequences = ['ix_test_seq'];

        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Informix)) {
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
        parent::tearDownAfterClass();
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function whereFullTextCompilesToLikeFallbackForSingleColumn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $query = new Query($driver);
        $query->select('*')
            ->from('ix_items')
            ->whereFullText('title', 'Laptop');

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('title like ?', $sql);
        $this->assertSame(['%Laptop%'], $bindings);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function whereFullTextCompilesLikeOrChainForMultipleColumns(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $query = new Query($driver);
        $query->select('*')
            ->from('ix_items')
            ->whereFullText(['title', 'status'], 'active');

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('title like ?', $sql);
        $this->assertStringContainsString('or status like ?', $sql);
        $this->assertSame(['%active%', '%active%'], $bindings);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function orWhereFullTextPreservesOrComposition(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $query = new Query($driver);
        $query->select('*')
            ->from('ix_items')
            ->whereEquals('id', 999)
            ->orWhereFullText('title', 'Laptop');

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('or title like ?', $sql);
        $this->assertSame([999, '%Laptop%'], $bindings);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function whereFullTextBtsModeCompilesToBtsContains(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $query = new Query($driver);
        $query->select('*')
            ->from('ix_items')
            ->whereFullText('title', 'Laptop', ['informix_mode' => 'bts']);

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('bts_contains(', $sql);
        $this->assertSame(['Laptop'], $bindings);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function whereFullTextNativeStrategyCompilesToBtsContains(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $query = new Query($driver);
        $query->select('*')
            ->from('ix_items')
            ->whereFullText('title', 'Laptop', ['strategy' => 'native']);

        $sql = strtolower($query->toSql());
        $bindings = $query->getBindings();

        $this->assertStringContainsString('bts_contains(', $sql);
        $this->assertSame(['Laptop'], $bindings);
    }

    // =========================================================================
    // Connection & Version
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function driverIsInformixInstance(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $this->assertInstanceOf(Informix::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val FROM sysmaster:sysdual');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canOpenMultipleConnectionsWithPhpunitCredentials(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $host = static::getEnv('DB_INFORMIX_HOST') ?? 'localhost';
        $service = static::getEnv('DB_INFORMIX_SERVICE') ?? '9088';
        $database = static::getEnv('DB_INFORMIX_DATABASE') ?? 'hubzero_test';
        $server = static::getEnv('DB_INFORMIX_SERVER') ?? 'informix';
        $protocol = static::getEnv('DB_INFORMIX_PROTOCOL') ?? 'onsoctcp';
        $user = static::getEnv('DB_INFORMIX_USER') ?? 'informix';
        $password = static::getEnv('DB_INFORMIX_PASSWORD') ?? 'in4mix';

        $dsn = sprintf(
            'informix:host=%s;service=%s;database=%s;server=%s;protocol=%s',
            $host,
            $service,
            $database,
            $server,
            $protocol
        );

        $connections = [];
        $opened = 0;
        $target = 4;

        try {
            for ($i = 0; $i < $target; $i++) {
                $pdo = new \PDO(
                    $dsn,
                    $user,
                    $password,
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );

                $stmt = $pdo->prepare('SELECT FIRST 1 tabid FROM systables WHERE tabid >= ?');
                $stmt->execute([1]);
                $this->assertNotFalse(
                    $stmt->fetchColumn(),
                    sprintf('Connection #%d could not run prepared query', $i + 1)
                );

                $connections[] = $pdo;
                $opened++;
            }
        } finally {
            foreach ($connections as $idx => $connection) {
                $connections[$idx] = null;
            }
        }

        $this->assertSame($target, $opened);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $version = $driver->getVersion();
        $this->assertNotEmpty($version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetDatabaseName(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $database = $driver->getDatabase();
        $this->assertNotEmpty($database);
    }

    // =========================================================================
    // Schema Introspection via Driver API
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsTrueForExistingTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('ix_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalseForMissingTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $this->assertFalse($driver->tableExists('ix_nonexistent_table'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $tables = $driver->getTableList();

        $this->assertContains('ix_categories', $tables);
        $this->assertContains('ix_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('ix_categories', true);

        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('slug', $columns);
        $this->assertArrayHasKey('sort_order', $columns);
        // id should be SERIAL (type code 6 -> 'serial')
        $this->assertEquals('serial', $columns['id']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsFullInfo(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('ix_categories', false);

        $this->assertArrayHasKey('name', $columns);
        $this->assertIsArray($columns['name']);
        $this->assertEquals('name', $columns['name']['name']);
        $this->assertArrayHasKey('type', $columns['name']);
        $this->assertArrayHasKey('allownull', $columns['name']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableKeysFindsPrimaryKey(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $keys = $driver->getTableKeys('ix_categories');
        $this->assertNotEmpty($keys);

        $this->assertArrayHasKey('PRIMARY', $keys, 'Should find a PRIMARY key entry');
        $hasPrimary = false;
        foreach ($keys as $keyName => $keyColumns) {
            $columns = is_array($keyColumns) ? $keyColumns : [$keyColumns];
            if (isset($columns[0]) && is_object($columns[0]) && !empty($columns[0]->isPrimary)) {
                $hasPrimary = true;
                break;
            }
        }
        $this->assertTrue($hasPrimary, 'Should find a primary key');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyReturnsIndexName(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('ix_categories');
        $this->assertNotNull($pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyColumnsReturnsColumnNames(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $columns = $driver->getPrimaryKeyColumns('ix_categories');
        $this->assertNotEmpty($columns);
        $this->assertContains('id', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('ix_items');
        $this->assertNotEmpty($fks, 'ix_items should have at least one foreign key');
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAllRecords(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery('SELECT * FROM ix_categories ORDER BY id');
        $rows = $driver->loadObjectList();

        $this->assertCount(3, $rows);
        $this->assertEquals('Electronics', trim($rows[0]->name));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithWhere(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("SELECT * FROM ix_items WHERE status = 'active'");
        $this->assertCount(3, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithJoin(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT i.title, c.name AS category_name
            FROM ix_items i
            JOIN ix_categories c ON i.category_id = c.id
            ORDER BY i.id
        ");
        $items = $driver->loadObjectList();
        $this->assertCount(4, $items);
        $this->assertEquals('Electronics', trim($items[0]->category_name));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetSerialId(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("INSERT INTO ix_categories (name, slug) VALUES ('Test', 'test-serial')");
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->setQuery("SELECT name FROM ix_categories WHERE id = $id");
        $this->assertEquals('Test', trim($driver->loadResult()));

        $driver->exec("DELETE FROM ix_categories WHERE id = $id");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("INSERT INTO ix_categories (name, slug) VALUES ('Before', 'update-test')");
        $driver->exec("UPDATE ix_categories SET name = 'After' WHERE slug = 'update-test'");

        $driver->setQuery("SELECT name FROM ix_categories WHERE slug = 'update-test'");
        $this->assertEquals('After', trim($driver->loadResult()));

        $driver->exec("DELETE FROM ix_categories WHERE slug = 'update-test'");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("INSERT INTO ix_categories (name, slug) VALUES ('Delete Me', 'delete-test')");
        $driver->exec("DELETE FROM ix_categories WHERE slug = 'delete-test'");

        $driver->setQuery("SELECT COUNT(*) FROM ix_categories WHERE slug = 'delete-test'");
        $this->assertEquals(0, $driver->loadResult());
    }

    // =========================================================================
    // Transactions
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCommitTransaction(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec("INSERT INTO ix_categories (name, slug) VALUES ('Committed', 'trans-commit')");
        $driver->transactionCommit();

        $driver->setQuery("SELECT COUNT(*) FROM ix_categories WHERE slug = 'trans-commit'");
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec("DELETE FROM ix_categories WHERE slug = 'trans-commit'");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        // Use a separate connection for isolation
        $config = static::$testDatabases[$dbName];
        $driver2 = new Informix($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec("INSERT INTO ix_categories (name, slug) VALUES ('Rolled Back', 'trans-rollback')");
        $driver2->transactionRollback();

        $driver->setQuery("SELECT COUNT(*) FROM ix_categories WHERE slug = 'trans-rollback'");
        $count = (int) $driver->loadResult();

        $driver->exec("DELETE FROM ix_categories WHERE slug = 'trans-rollback'");
        $driver2->disconnect();

        $this->assertEquals(0, $count, 'Rolled back row should not be visible');
    }

    // =========================================================================
    // Aggregates
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT
                COUNT(*) AS cnt,
                SUM(stock) AS total_stock,
                MIN(price) AS min_price,
                MAX(price) AS max_price
            FROM ix_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
        $this->assertEquals(19.99, $row->min_price);
        $this->assertEquals(999.99, $row->max_price);
    }

    // =========================================================================
    // Informix-Specific SQL Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseFirstSkipPagination(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery('SELECT FIRST 2 * FROM ix_items ORDER BY id');
        $this->assertCount(2, $driver->loadObjectList());

        $driver->setQuery('SELECT SKIP 1 FIRST 2 * FROM ix_items ORDER BY id');
        $items = $driver->loadObjectList();
        $this->assertCount(2, $items);
        $this->assertEquals('Phone', trim($items[0]->title));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canConcatenateWithDoubleBar(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("SELECT 'Hello' || ' ' || 'World' AS greeting FROM sysmaster:sysdual");
        $this->assertEquals('Hello World', trim($driver->loadResult()));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseNvlForNullCoalescing(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("SELECT NVL(NULL, 'default') AS result FROM sysmaster:sysdual");
        $this->assertEquals('default', trim($driver->loadResult()));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCaseExpression(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT title,
                CASE status
                    WHEN 'active' THEN 'Available'
                    WHEN 'draft' THEN 'Coming Soon'
                    ELSE 'Unknown'
                END AS label
            FROM ix_items ORDER BY id
        ");
        $items = $driver->loadObjectList();

        $this->assertEquals('Available', trim($items[0]->label));
        $this->assertEquals('Coming Soon', trim($items[3]->label));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGroupByWithHaving(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT category_id, COUNT(*) AS cnt
            FROM ix_items
            WHERE category_id IS NOT NULL
            GROUP BY category_id
            HAVING COUNT(*) >= 2
        ");
        $rows = $driver->loadObjectList();
        $this->assertCount(2, $rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseSubquery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT * FROM ix_items
            WHERE category_id IN (
                SELECT id FROM ix_categories WHERE active = 1
            )
        ");
        $this->assertCount(4, $driver->loadObjectList());
    }

    // =========================================================================
    // Sequences
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canCreateAndUseSequence(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $this->assertTrue($driver->createSequence('ix_test_seq', 100));
        $this->assertTrue($driver->sequenceExists('ix_test_seq'));

        $val = $driver->nextSequenceValue('ix_test_seq');
        $this->assertEquals(100, $val);

        $val = $driver->nextSequenceValue('ix_test_seq');
        $this->assertEquals(101, $val);

        $current = $driver->currentSequenceValue('ix_test_seq');
        $this->assertEquals(101, $current);

        $this->assertTrue($driver->dropSequence('ix_test_seq'));
        $this->assertFalse($driver->sequenceExists('ix_test_seq'));
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_view_src (id SERIAL PRIMARY KEY, name VARCHAR(50), active SMALLINT DEFAULT 1)");
        $driver->exec("INSERT INTO ix_view_src (name, active) VALUES ('Visible', 1)");
        $driver->exec("INSERT INTO ix_view_src (name, active) VALUES ('Hidden', 0)");

        // Create view
        $viewSql = "SELECT id, name FROM ix_view_src WHERE active = 1";
        $this->assertTrue($driver->createOrReplaceView('ix_test_view', $viewSql));
        $this->assertTrue($driver->viewExists('ix_test_view'));

        // Query through view
        $driver->setQuery("SELECT COUNT(*) FROM ix_test_view");
        $this->assertEquals(1, $driver->loadResult());

        // getViews() should include it
        $views = $driver->getViews();
        $this->assertContains('ix_test_view', $views);

        // Replace view
        $this->assertTrue($driver->createOrReplaceView('ix_test_view', "SELECT id, name FROM ix_view_src"));
        $driver->setQuery("SELECT COUNT(*) FROM ix_test_view");
        $this->assertEquals(2, $driver->loadResult());

        // viewExists for missing view
        $this->assertFalse($driver->viewExists('ix_nonexistent_view'));

        // Drop view
        $this->assertTrue($driver->dropView('ix_test_view'));
        $this->assertFalse($driver->viewExists('ix_test_view'));
    }

    // =========================================================================
    // DDL: Column Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropColumn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (id SERIAL PRIMARY KEY, name VARCHAR(100))");

        // Add column
        $this->assertTrue($driver->addColumn('ix_ddl_test', 'email', 'VARCHAR(200)'));
        $columns = $driver->getTableColumns('ix_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        // Drop column
        $this->assertTrue($driver->dropColumn('ix_ddl_test', 'email'));
        $columns = $driver->getTableColumns('ix_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canModifyColumnType(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (id SERIAL PRIMARY KEY, notes VARCHAR(50))");

        $this->assertTrue($driver->modifyColumn('ix_ddl_test', 'notes', 'VARCHAR(255)'));

        $columns = $driver->getTableColumns('ix_ddl_test', false);
        // Verify the column still exists with updated type info
        $this->assertArrayHasKey('notes', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRenameColumn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (id SERIAL PRIMARY KEY, old_name VARCHAR(100))");

        $this->assertTrue($driver->changeColumn('ix_ddl_test', 'old_name', 'new_name', ''));

        $columns = $driver->getTableColumns('ix_ddl_test', true);
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
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (id SERIAL PRIMARY KEY, name VARCHAR(100), email VARCHAR(200))");

        $this->assertTrue($driver->addIndex('ix_ddl_test', 'idx_ix_name', 'name'));

        $indexes = $driver->getIndexes('ix_ddl_test');
        $this->assertArrayHasKey('idx_ix_name', $indexes);

        $this->assertTrue($driver->dropIndex('ix_ddl_test', 'idx_ix_name'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddUniqueIndex(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (id SERIAL PRIMARY KEY, email VARCHAR(200))");

        $this->assertTrue($driver->addUniqueIndex('ix_ddl_test', 'idx_ix_email_uniq', 'email'));

        $indexes = $driver->getIndexes('ix_ddl_test');
        $this->assertArrayHasKey('idx_ix_email_uniq', $indexes);
        $columns = $indexes['idx_ix_email_uniq'];
        $firstCol = is_array($columns) ? $columns[0] : $columns;
        $this->assertTrue($firstCol->isUnique);
    }

    // =========================================================================
    // DDL: Foreign Key Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropForeignKey(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_fk_parent (id SERIAL PRIMARY KEY, name VARCHAR(100))");
        $driver->exec("CREATE TABLE ix_fk_child (id SERIAL PRIMARY KEY, parent_id INTEGER)");

        $this->assertTrue($driver->addForeignKey(
            'ix_fk_child',
            'fk_ix_child_parent',
            'parent_id',
            'ix_fk_parent',
            'id'
        ));

        $fks = $driver->getForeignKeys('ix_fk_child');
        $this->assertNotEmpty($fks);

        $this->assertTrue($driver->dropForeignKey('ix_fk_child', 'fk_ix_child_parent'));
    }

    // =========================================================================
    // DDL: Primary Key Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropPrimaryKey(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_ddl_test (record_id INTEGER NOT NULL, name VARCHAR(100))");

        $this->assertTrue($driver->addPrimaryKey('ix_ddl_test', ['record_id']));

        $pk = $driver->getPrimaryKey('ix_ddl_test');
        $this->assertNotNull($pk);

        $this->assertTrue($driver->dropPrimaryKey('ix_ddl_test'));
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_trunc_test (id SERIAL PRIMARY KEY, name VARCHAR(50))");
        $driver->exec("INSERT INTO ix_trunc_test (name) VALUES ('one')");
        $driver->exec("INSERT INTO ix_trunc_test (name) VALUES ('two')");

        $driver->setQuery("SELECT COUNT(*) FROM ix_trunc_test");
        $this->assertEquals(2, $driver->loadResult());

        $driver->truncateTable('ix_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM ix_trunc_test");
        $this->assertEquals(0, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRenameTable(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_rename_src (id SERIAL PRIMARY KEY, name VARCHAR(50))");
        $driver->exec("INSERT INTO ix_rename_src (name) VALUES ('test')");

        $driver->renameTable('ix_rename_src', 'ix_rename_dest');

        $this->assertTrue($driver->tableExists('ix_rename_dest'));
        $this->assertFalse($driver->tableExists('ix_rename_src'));

        $driver->setQuery("SELECT name FROM ix_rename_dest");
        $this->assertEquals('test', trim($driver->loadResult()));
    }

    // =========================================================================
    // SERIAL Auto-Increment Behavior
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function serialAutoIncrementsCorrectly(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $driver->exec("CREATE TABLE ix_serial_test (id SERIAL PRIMARY KEY, label VARCHAR(50))");

        $driver->exec("INSERT INTO ix_serial_test (label) VALUES ('first')");
        $id1 = $driver->insertid();

        $driver->exec("INSERT INTO ix_serial_test (label) VALUES ('second')");
        $id2 = $driver->insertid();

        $this->assertGreaterThan(0, $id1);
        $this->assertEquals($id1 + 1, $id2);
    }

    // =========================================================================
    // Query Builder Integration
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithLimit(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ix_items')
            ->order('id', 'asc')
            ->limit(2)
            ->fetch('rows');

        $this->assertCount(2, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithOffset(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ix_items')
            ->order('id', 'asc')
            ->limit(2)
            ->start(1)
            ->fetch('rows');

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', trim($items[0]->title));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderWhereInSubquery(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ix_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('ix_categories')
                    ->whereEquals('active', 1);
            })
            ->fetch('rows');

        $this->assertCount(4, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderHandlesEmptyWhereIn(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ix_items')
            ->whereIn('id', [])
            ->fetch('rows');

        $this->assertCount(0, $items);
    }

    // =========================================================================
    // Feature Detection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function featureDetectionMethodsReturnExpectedValues(string $dbName, Driver $driver): void
    {
        if (!$this->requiresInformix($driver)) {
            return;
        }

        $this->assertFalse($driver->supportsEnum());
        $this->assertFalse($driver->supportsEngine());
        $this->assertFalse($driver->supportsColumnPositioning());
        $this->assertTrue($driver->supportsSequences());
        $this->assertTrue($driver->supportsCheckConstraints());
        $this->assertTrue($driver->supportsWindowFunctions());
        $this->assertTrue($driver->supportsCTE());
    }
}
