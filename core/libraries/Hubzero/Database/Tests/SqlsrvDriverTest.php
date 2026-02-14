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
use Hubzero\Database\Drivers\Sqlsrv\SqlsrvDriver as Sqlsrv;

/**
 * SQL Server-specific driver tests
 *
 * Runs against all configured backends via the standard databaseProvider.
 * Tests that receive a non-Sqlsrv driver pass with a no-op assertion.
 *
 * Configuration: Add 'sqlsrv' to DB_TEST_BACKENDS and set DB_SQLSRV_*
 * credentials in phpunit.xml or via environment variables.
 */
#[Group('sqlsrv')]
#[Group('database')]
class SqlsrvDriverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['ss_items', 'ss_categories'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        if (!($driver instanceof Sqlsrv)) {
            return;
        }

        foreach (['ss_items', 'ss_categories'] as $table) {
            try {
                $driver->dropTable($table, true);
            } catch (\Exception $e) {
            }
        }

        $driver->exec("
            CREATE TABLE ss_categories (
                id INT IDENTITY(1,1) PRIMARY KEY,
                name NVARCHAR(100) NOT NULL,
                slug NVARCHAR(100),
                sort_order INT DEFAULT 0,
                active BIT DEFAULT 1,
                created_at DATETIME2(0) DEFAULT GETDATE()
            )
        ");

        $driver->exec("
            CREATE TABLE ss_items (
                id INT IDENTITY(1,1) PRIMARY KEY,
                category_id INT NULL,
                title NVARCHAR(200) NOT NULL,
                description NVARCHAR(MAX),
                price DECIMAL(10,2) DEFAULT 0.00,
                stock INT DEFAULT 0,
                status NVARCHAR(20) DEFAULT 'active',
                created_at DATETIME2(0) DEFAULT GETDATE(),
                CONSTRAINT fk_ss_items_cat FOREIGN KEY (category_id)
                    REFERENCES ss_categories(id) ON DELETE SET NULL
            )
        ");

        $driver->exec(
            "CREATE INDEX idx_ss_items_status ON ss_items (status)"
        );
        $driver->exec(
            "CREATE INDEX idx_ss_items_cat ON ss_items (category_id)"
        );

        $driver->exec(
            "INSERT INTO ss_categories (name, slug, sort_order)"
            . " VALUES ('Electronics', 'electronics', 1)"
        );
        $driver->exec(
            "INSERT INTO ss_categories (name, slug, sort_order)"
            . " VALUES ('Books', 'books', 2)"
        );
        $driver->exec(
            "INSERT INTO ss_categories (name, slug, sort_order, active)"
            . " VALUES ('Archived', 'archived', 99, 0)"
        );

        $driver->exec(
            "INSERT INTO ss_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Laptop', 999.99, 10, 'active')"
        );
        $driver->exec(
            "INSERT INTO ss_items (category_id, title, price, stock, status)"
            . " VALUES (1, 'Phone', 599.99, 25, 'active')"
        );
        $driver->exec(
            "INSERT INTO ss_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Novel', 19.99, 100, 'active')"
        );
        $driver->exec(
            "INSERT INTO ss_items (category_id, title, price, stock, status)"
            . " VALUES (2, 'Textbook', 79.99, 5, 'draft')"
        );
    }

    private function requiresSqlsrv(Driver $driver): bool
    {
        if (!($driver instanceof Sqlsrv)) {
            $this->assertTrue(true);
            return false;
        }
        return true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $adHocViews = ['ss_test_view', 'ss_active_items'];
        $adHocTables = [
            'ss_rename_dest', 'ss_rename_src',
            'ss_fk_child', 'ss_fk_parent',
            'ss_ddl_test', 'ss_trunc_test',
            'ss_identity_test', 'ss_view_src',
            'ss_pk_test',
        ];

        foreach (static::getClassDrivers() as $driver) {
            if (!($driver instanceof Sqlsrv)) {
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
            if (!($driver instanceof Sqlsrv)) {
                continue;
            }
            try {
                $driver->dropView('ss_active_items', true);
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
    public function driverIsSqlsrvInstance(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $this->assertInstanceOf(Sqlsrv::class, $driver);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canExecuteBasicQuery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery('SELECT 1 AS val');
        $this->assertEquals(1, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGetVersion(string $dbName, Driver $driver): void
    {
        if (!$this->requiresSqlsrv($driver)) {
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
        if (!$this->requiresSqlsrv($driver)) {
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
    public function tableExistsReturnsTrueForExistingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $this->assertTrue($driver->tableExists('ss_categories'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableExistsReturnsFalseForMissingTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $this->assertFalse($driver->tableExists('ss_nonexistent_table'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableListIncludesTestTables(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $tables = $driver->getTableList();

        $this->assertContains('ss_categories', $tables);
        $this->assertContains('ss_items', $tables);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsTypeOnly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('ss_categories', true);

        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('slug', $columns);
        $this->assertArrayHasKey('active', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTableColumnsFullInfo(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $columns = $driver->getTableColumns('ss_categories', false);

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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $pk = $driver->getPrimaryKey('ss_categories');
        $this->assertEquals('id', $pk);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getPrimaryKeyColumns(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $columns = $driver->getPrimaryKeyColumns('ss_categories');
        $this->assertContains('id', $columns);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function getForeignKeysForChildTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $fks = $driver->getForeignKeys('ss_items');
        $this->assertNotEmpty(
            $fks,
            'ss_items should have a foreign key on category_id'
        );
    }

    // =========================================================================
    // CRUD Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectAll(string $dbName, Driver $driver): void
    {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery('SELECT * FROM ss_categories ORDER BY id');
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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM ss_items WHERE status = 'active'"
        );
        $this->assertCount(3, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canSelectWithJoin(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT i.title, c.name AS category_name
            FROM ss_items i
            JOIN ss_categories c ON i.category_id = c.id
            ORDER BY i.id
        ");
        $items = $driver->loadObjectList();
        $this->assertCount(4, $items);
        $this->assertEquals('Electronics', $items[0]->category_name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canInsertAndGetIdentityId(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO ss_categories (name, slug)"
            . " VALUES ('Test', 'test-identity')"
        );
        $id = $driver->insertid();

        $this->assertGreaterThan(0, $id);

        $driver->setQuery(
            "SELECT name FROM ss_categories WHERE id = $id"
        );
        $this->assertEquals('Test', $driver->loadResult());

        $driver->exec(
            "DELETE FROM ss_categories WHERE id = $id"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUpdateRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO ss_categories (name, slug)"
            . " VALUES ('Before', 'update-test')"
        );
        $driver->exec(
            "UPDATE ss_categories SET name = 'After'"
            . " WHERE slug = 'update-test'"
        );

        $driver->setQuery(
            "SELECT name FROM ss_categories WHERE slug = 'update-test'"
        );
        $this->assertEquals('After', $driver->loadResult());

        $driver->exec(
            "DELETE FROM ss_categories WHERE slug = 'update-test'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canDeleteRecord(string $dbName, Driver $driver): void
    {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "INSERT INTO ss_categories (name, slug)"
            . " VALUES ('Delete Me', 'delete-test')"
        );
        $driver->exec(
            "DELETE FROM ss_categories WHERE slug = 'delete-test'"
        );

        $driver->setQuery(
            "SELECT COUNT(*) FROM ss_categories"
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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->transactionStart();
        $driver->exec(
            "INSERT INTO ss_categories (name, slug)"
            . " VALUES ('Committed', 'trans-commit')"
        );
        $driver->transactionCommit();

        $driver->setQuery(
            "SELECT COUNT(*) FROM ss_categories"
            . " WHERE slug = 'trans-commit'"
        );
        $this->assertEquals(1, $driver->loadResult());

        $driver->exec(
            "DELETE FROM ss_categories WHERE slug = 'trans-commit'"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRollbackTransaction(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $config = static::$testDatabases[$dbName];
        $driver2 = new Sqlsrv($config);
        $driver2->connect();

        $driver2->transactionStart();
        $driver2->exec(
            "INSERT INTO ss_categories (name, slug)"
            . " VALUES ('Rolled Back', 'trans-rollback')"
        );
        $driver2->transactionRollback();

        $driver->setQuery(
            "SELECT COUNT(*) FROM ss_categories"
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
    // Aggregates & SQL Server SQL Features
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseAggregateFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT COUNT(*) AS cnt, SUM(stock) AS total_stock,
                   MIN(price) AS min_price, MAX(price) AS max_price
            FROM ss_items
        ");
        $row = $driver->loadObject();

        $this->assertEquals(4, $row->cnt);
        $this->assertEquals(140, $row->total_stock);
        $this->assertEquals(19.99, (float) $row->min_price);
        $this->assertEquals(999.99, (float) $row->max_price);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseTopForLimit(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT TOP 2 * FROM ss_items ORDER BY id'
        );
        $this->assertCount(2, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseOffsetFetch(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            'SELECT * FROM ss_items ORDER BY id'
            . ' OFFSET 1 ROWS FETCH NEXT 2 ROWS ONLY'
        );
        $items = $driver->loadObjectList();

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseBitLiterals(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT * FROM ss_categories WHERE active = 1 ORDER BY id"
        );
        $rows = $driver->loadObjectList();
        $this->assertCount(2, $rows);

        $driver->setQuery(
            "SELECT * FROM ss_categories WHERE active = 0"
        );
        $this->assertCount(1, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseIsNull(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT ISNULL(NULL, 'fallback') AS result"
        );
        $this->assertEquals('fallback', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canConcatenateWithConcat(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery(
            "SELECT CONCAT('Hello', ' ', 'World') AS greeting"
        );
        $this->assertEquals('Hello World', $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseCaseExpression(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT title,
                CASE status
                    WHEN 'active' THEN 'Available'
                    WHEN 'draft' THEN 'Coming Soon'
                    ELSE 'Unknown'
                END AS label
            FROM ss_items ORDER BY id
        ");
        $items = $driver->loadObjectList();

        $this->assertEquals('Available', $items[0]->label);
        $this->assertEquals('Coming Soon', $items[3]->label);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canGroupByWithHaving(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT category_id, COUNT(*) AS cnt
            FROM ss_items
            WHERE category_id IS NOT NULL
            GROUP BY category_id
            HAVING COUNT(*) >= 2
        ");
        $this->assertCount(2, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseSubquery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT * FROM ss_items
            WHERE category_id IN (
                SELECT id FROM ss_categories WHERE active = 1
            )
        ");
        $this->assertCount(4, $driver->loadObjectList());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canUseDatePartFunctions(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->setQuery("
            SELECT
                DATEPART(YEAR, GETDATE()) AS yr,
                DATEPART(MONTH, GETDATE()) AS mo,
                DATEPART(DAY, GETDATE()) AS dy
        ");
        $row = $driver->loadObject();

        $this->assertGreaterThanOrEqual(2020, (int) $row->yr);
        $this->assertGreaterThanOrEqual(1, (int) $row->mo);
        $this->assertLessThanOrEqual(12, (int) $row->mo);
    }

    // =========================================================================
    // Views
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canManageViews(string $dbName, Driver $driver): void
    {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        // Create
        $this->assertTrue($driver->createOrReplaceView(
            'ss_active_items',
            "SELECT id, title FROM ss_items WHERE status = 'active'"
        ));
        $this->assertTrue($driver->viewExists('ss_active_items'));

        // Query
        $driver->setQuery("SELECT COUNT(*) FROM ss_active_items");
        $this->assertEquals(3, $driver->loadResult());

        // List
        $views = $driver->getViews();
        $this->assertContains('ss_active_items', $views);

        // Missing view
        $this->assertFalse(
            $driver->viewExists('ss_nonexistent_view')
        );

        // Drop
        $this->assertTrue($driver->dropView('ss_active_items'));
        $this->assertFalse($driver->viewExists('ss_active_items'));
    }

    // =========================================================================
    // DDL: Column Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropColumn(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_ddl_test"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " name NVARCHAR(100))"
        );

        $this->assertTrue(
            $driver->addColumn('ss_ddl_test', 'email', 'NVARCHAR(200)')
        );
        $columns = $driver->getTableColumns('ss_ddl_test', true);
        $this->assertArrayHasKey('email', $columns);

        $this->assertTrue(
            $driver->dropColumn('ss_ddl_test', 'email')
        );
        $columns = $driver->getTableColumns('ss_ddl_test', true);
        $this->assertArrayNotHasKey('email', $columns);
    }

    // =========================================================================
    // DDL: Index Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropIndex(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_ddl_test"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " name NVARCHAR(100), email NVARCHAR(200))"
        );

        $this->assertTrue(
            $driver->addIndex('ss_ddl_test', 'idx_ss_name', 'name')
        );

        $this->assertTrue(
            $driver->dropIndex('ss_ddl_test', 'idx_ss_name')
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddUniqueIndex(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_ddl_test"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " email NVARCHAR(200))"
        );

        $this->assertTrue(
            $driver->addUniqueIndex(
                'ss_ddl_test',
                'idx_ss_email_uniq',
                'email'
            )
        );
    }

    // =========================================================================
    // DDL: Foreign Key Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canAddAndDropForeignKey(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_fk_parent"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " name NVARCHAR(100))"
        );
        $driver->exec(
            "CREATE TABLE ss_fk_child"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " parent_id INT)"
        );

        $driver->exec(
            "ALTER TABLE ss_fk_child"
            . " ADD CONSTRAINT fk_ss_child_parent"
            . " FOREIGN KEY (parent_id)"
            . " REFERENCES ss_fk_parent(id)"
        );

        $fks = $driver->getForeignKeys('ss_fk_child');
        $this->assertNotEmpty($fks);

        $this->assertTrue(
            $driver->dropConstraint('ss_fk_child', 'fk_ss_child_parent')
        );
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canTruncateTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_trunc_test"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " name NVARCHAR(50))"
        );
        $driver->exec(
            "INSERT INTO ss_trunc_test (name) VALUES ('one')"
        );
        $driver->exec(
            "INSERT INTO ss_trunc_test (name) VALUES ('two')"
        );

        $driver->setQuery("SELECT COUNT(*) FROM ss_trunc_test");
        $this->assertEquals(2, $driver->loadResult());

        $driver->truncateTable('ss_trunc_test');

        $driver->setQuery("SELECT COUNT(*) FROM ss_trunc_test");
        $this->assertEquals(0, $driver->loadResult());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function canRenameTable(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_rename_src"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " name NVARCHAR(50))"
        );
        $driver->exec(
            "INSERT INTO ss_rename_src (name) VALUES ('test')"
        );

        $driver->renameTable('ss_rename_src', 'ss_rename_dest');

        $this->assertTrue($driver->tableExists('ss_rename_dest'));
        $this->assertFalse($driver->tableExists('ss_rename_src'));

        $driver->setQuery("SELECT name FROM ss_rename_dest");
        $this->assertEquals('test', $driver->loadResult());
    }

    // =========================================================================
    // IDENTITY Auto-Increment
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function identityAutoIncrementsCorrectly(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->exec(
            "CREATE TABLE ss_identity_test"
            . " (id INT IDENTITY(1,1) PRIMARY KEY,"
            . " label NVARCHAR(50))"
        );

        $driver->exec(
            "INSERT INTO ss_identity_test (label) VALUES ('first')"
        );
        $id1 = $driver->insertid();

        $driver->exec(
            "INSERT INTO ss_identity_test (label) VALUES ('second')"
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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ss_items')
            ->order('id', 'asc')
            ->limit(2)
            ->fetch('rows');

        $this->assertCount(2, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderSelectWithOffset(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ss_items')
            ->order('id', 'asc')
            ->limit(2)
            ->start(1)
            ->fetch('rows');

        $this->assertCount(2, $items);
        $this->assertEquals('Phone', $items[0]->title);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderWhereInSubquery(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ss_items')
            ->whereIn('category_id', function ($sub) {
                $sub->select('id')
                    ->from('ss_categories')
                    ->whereEquals('active', 1);
            })
            ->fetch('rows');

        $this->assertCount(4, $items);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function queryBuilderHandlesEmptyWhereIn(
        string $dbName,
        Driver $driver
    ): void {
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $items = $driver->getQuery()
            ->select('*')
            ->from('ss_items')
            ->whereIn('id', [])
            ->fetch('rows');

        $this->assertCount(0, $items);
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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $this->assertFalse($driver->supportsRegexp());
        $this->assertFalse($driver->supportsColumnPositioning());
        $this->assertFalse($driver->supportsEngine());
        $this->assertFalse($driver->supportsEnum());
        $this->assertFalse($driver->supportsUnsigned());
        $this->assertTrue($driver->supportsFulltext());
        $this->assertTrue($driver->supportsDropColumn());
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
        if (!$this->requiresSqlsrv($driver)) {
            return;
        }

        $driver->createTable('ss_ddl_test')
            ->id()
            ->string('name', 100)
            ->integer('score')->default(0)->nullable()
            ->execute();

        $this->assertTrue($driver->tableExists('ss_ddl_test'));

        $columns = $driver->getTableColumns('ss_ddl_test', true);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertArrayHasKey('score', $columns);
    }
}
