<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Exception\UnsupportedSyntaxException;

/**
 * Multi-database SQL Syntax Test
 *
 * Tests SQL syntax generation and execution across multiple database backends.
 * Uses data provider pattern to run the same tests against all enabled databases.
 */
class SyntaxTest extends AbstractDriverTestCase
{
    /**
     * Test table name
     *
     * @var string
     */
    private static $testTable = 'syntax_test';

    /**
     * Secondary test table name (for JOIN tests)
     *
     * @var string
     */
    private static $testTable2 = 'syntax_test_2';

    /**
     * Tertiary test table name (for multi-join tests)
     *
     * @var string
     */
    private static $testTable3 = 'syntax_test_3';

    protected static function getTestTables(): array
    {
        return [self::$testTable, self::$testTable2, self::$testTable3];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing table if it exists
        try {
            $driver->dropTable(self::$testTable, true);
        } catch (\Exception $e) {
            // Table doesn't exist, which is fine
        }

        // Create table using schema builder for database-agnostic DDL
        $schema = $driver->schema();
        $schema->createTable(self::$testTable)
            ->id()
            ->string('name', 100)
            ->string('email', 100)
            ->integer('status')->default(1)
            ->timestamp('created')->default('CURRENT_TIMESTAMP')
            ->execute();

        // Drop and create secondary table for JOIN tests
        try {
            $driver->dropTable(self::$testTable2, true);
        } catch (\Exception $e) {
            // Ignore
        }

        $schema->createTable(self::$testTable2)
            ->id()
            ->string('name', 100)
            ->string('email', 100)
            ->integer('status')->default(1)
            ->timestamp('created')->default('CURRENT_TIMESTAMP')
            ->execute();

        try {
            $driver->dropTable(self::$testTable3, true);
        } catch (\Exception $e) {
            // Ignore
        }

        $schema->createTable(self::$testTable3)
            ->id()
            ->string('name', 100)
            ->string('email', 100)
            ->integer('status')->default(1)
            ->timestamp('created')->default('CURRENT_TIMESTAMP')
            ->execute();

        // Seed test data
        self::seedTestData($driver);
    }

    /**
     * Seed test data into table
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private static function seedTestData(Driver $driver): void
    {
        // Insert using Query builder for database-agnostic inserts
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 1]);

        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 1]);

        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'User 3', 'email' => 'user3@example.com', 'status' => 0]);

        $query = new Query($driver);
        $query->push(self::$testTable2, ['name' => 'User 4', 'email' => 'user4@example.com', 'status' => 1]);
        $query = new Query($driver);
        $query->push(self::$testTable2, ['name' => 'User 5', 'email' => 'user5@example.com', 'status' => 0]);

        $query = new Query($driver);
        $query->push(self::$testTable3, ['name' => 'User 6', 'email' => 'user6@example.com', 'status' => 1]);
    }

    /**
     * Clean up after all tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    // =========================================================================
    // Basic SELECT Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSimpleSelect(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable);

        $sql = $query->toString();

        $this->assertStringContainsString('SELECT', $sql);
        $this->assertStringContainsString('FROM', $sql);

        // Execute and verify
        $rows = $query->fetch();
        $this->assertIsArray($rows);
        $this->assertGreaterThanOrEqual(3, count($rows), 'Should have at least 3 rows');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithWhere(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->whereEquals('status', 1);

        $rows = $query->fetch();

        $this->assertGreaterThanOrEqual(2, count($rows), 'Should find at least 2 users with status=1');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithOrderBy(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->order('id', 'asc');

        $sql = $query->toString();
        $this->assertStringContainsString('ORDER BY', $sql);

        $rows = $query->fetch();
        $this->assertNotEmpty($rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithLimit(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->limit(2);

        $sql = $query->toString();

        // Different databases use different syntax (LIMIT, FETCH FIRST, etc.)
        // But all should produce valid SQL that limits results
        $rows = $query->fetch();
        $this->assertCount(2, $rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSelectWithLimitAndOffset(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->order('id', 'asc')
              ->limit(2)
              ->start(1);

        $rows = $query->fetch();
        $this->assertCount(2, $rows);
    }

    // =========================================================================
    // INSERT Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBuildInsert(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->insert(self::$testTable)
              ->values(['name' => 'New User', 'email' => 'new@example.com', 'status' => 1]);

        $sql = $query->toString();

        $this->assertStringContainsString('INSERT', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testInsertExecution(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->insert(self::$testTable)
              ->values(['name' => 'Inserted User', 'email' => 'inserted@example.com', 'status' => 1])
              ->execute();

        // Verify it was inserted
        $query2 = new Query($driver);
        $rows = $query2->select('*')
                       ->from(self::$testTable)
                       ->whereLike('email', 'inserted@example.com')
                       ->fetch();

        $this->assertCount(1, $rows, 'Should find the inserted user');

        // Clean up
        $query3 = new Query($driver);
        $query3->delete(self::$testTable)
               ->whereLike('email', 'inserted@example.com')
               ->execute();
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreDoesNotFailOnDuplicate(string $dbName, Driver $driver): void
    {
        // Insert a row
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Ignore Test', 'email' => 'ignore@example.com', 'status' => 1]);

        // Get the inserted row's id
        $query2 = new Query($driver);
        $row = $query2->select('*')
                      ->from(self::$testTable)
                      ->whereLike('email', 'ignore@example.com')
                      ->fetch();
        $this->assertCount(1, $row);
        $id = $row[0]->id;

        // Insert again with the same id using ignore — should not throw
        $query3 = new Query($driver);
        $query3->insert(self::$testTable, true)
               ->values(['id' => $id, 'name' => 'Duplicate', 'email' => 'dup@example.com'])
               ->execute();

        // Verify original row is unchanged
        $query4 = new Query($driver);
        $verify = $query4->select('*')
                         ->from(self::$testTable)
                         ->whereEquals('id', $id)
                         ->fetch();
        $this->assertCount(1, $verify);
        $this->assertEquals('Ignore Test', $verify[0]->name);

        // Clean up
        $query5 = new Query($driver);
        $query5->delete(self::$testTable)
               ->whereLike('email', 'ignore@example.com')
               ->execute();
    }

    // =========================================================================
    // UPDATE Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBuildUpdate(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->update(self::$testTable)
              ->set(['name' => 'Updated'])
              ->whereEquals('id', 1);

        $sql = $query->toString();

        $this->assertStringContainsString('UPDATE', $sql);
        $this->assertStringContainsString('SET', $sql);
        $this->assertStringContainsString('WHERE', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdateExecution(string $dbName, Driver $driver): void
    {
        // Get the first user's ID
        $query = new Query($driver);
        $user = $query->select('*')
                      ->from(self::$testTable)
                      ->order('id', 'asc')
                      ->limit(1)
                      ->fetch();

        $this->assertNotEmpty($user);
        $userId = $user[0]->id;
        $originalName = $user[0]->name;

        // Update the name
        $query2 = new Query($driver);
        $query2->update(self::$testTable)
               ->set(['name' => 'Updated Name'])
               ->whereEquals('id', $userId)
               ->execute();

        // Verify update
        $query3 = new Query($driver);
        $updated = $query3->select('*')
                          ->from(self::$testTable)
                          ->whereEquals('id', $userId)
                          ->fetch();

        $this->assertEquals('Updated Name', $updated[0]->name);

        // Restore original name
        $query4 = new Query($driver);
        $query4->update(self::$testTable)
               ->set(['name' => $originalName])
               ->whereEquals('id', $userId)
               ->execute();
    }

    // =========================================================================
    // DELETE Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBuildDelete(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->delete(self::$testTable)
              ->whereEquals('id', 999);

        $sql = $query->toString();

        $this->assertStringContainsString('DELETE', $sql);
        $this->assertStringContainsString('FROM', $sql);
        $this->assertStringContainsString('WHERE', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeleteExecution(string $dbName, Driver $driver): void
    {
        // Insert a test row
        $query = new Query($driver);
        $query->push(self::$testTable, ['name' => 'Delete Me', 'email' => 'delete@example.com', 'status' => 1]);

        // Verify it exists
        $query2 = new Query($driver);
        $rows = $query2->select('*')
                       ->from(self::$testTable)
                       ->whereLike('email', 'delete@example.com')
                       ->fetch();
        $this->assertCount(1, $rows);

        // Delete it
        $query3 = new Query($driver);
        $query3->delete(self::$testTable)
               ->whereLike('email', 'delete@example.com')
               ->execute();

        // Verify deleted
        $query4 = new Query($driver);
        $rows = $query4->select('*')
                       ->from(self::$testTable)
                       ->whereLike('email', 'delete@example.com')
                       ->fetch();
        $this->assertCount(0, $rows);
    }

    // =========================================================================
    // JOIN Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testInnerJoin(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        // Use alias parameter (database-agnostic)
        $query->select('*')
              ->from(self::$testTable, 't1')
              ->join(self::$testTable . ' t2', 't1.id', 't2.id', 'inner');

        $sql = $query->toString();

        $this->assertStringContainsString('INNER JOIN', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLeftJoin(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        // Use alias parameter (database-agnostic)
        $query->select('*')
              ->from(self::$testTable, 't1')
              ->leftJoin(self::$testTable . ' t2', 't1.id', 't2.id');

        $sql = $query->toString();

        $this->assertStringContainsString('LEFT JOIN', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRightJoinSupport(string $dbName, Driver $driver): void
    {
        // RIGHT JOIN syntax_test (3 rows) with syntax_test_2 (2 rows)
        // All rows from syntax_test_2 (right side) should be preserved
        $query = new Query($driver);

        $results = $query->select('t2.*')
              ->from(self::$testTable, 't1')
              ->rightJoin(self::$testTable2 . ' AS t2', 't1.id', 't2.id')
              ->fetch();

        $this->assertCount(2, $results, 'RIGHT JOIN should return all rows from right table');
    }

    // =========================================================================
    // Complex Query Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereMultipleConditions(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->whereEquals('status', 1)
              ->whereLike('name', '%User%');

        $rows = $query->fetch();

        $this->assertGreaterThanOrEqual(1, count($rows), 'Should find at least 1 user matching conditions');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereIn(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from(self::$testTable)
              ->whereIn('id', [1, 2])
              ->order('id', 'asc');

        $rows = $query->fetch();

        $this->assertCount(2, $rows, 'Should find 2 users with id IN (1, 2)');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGroupBy(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('status, COUNT(*) as cnt')
              ->from(self::$testTable)
              ->group('status');

        $sql = $query->toString();
        $this->assertStringContainsString('GROUP BY', $sql);

        $rows = $query->fetch();
        $this->assertNotEmpty($rows);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHaving(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('status, COUNT(*) as cnt')
              ->from(self::$testTable)
              ->group('status')
              ->havingRaw('COUNT(*) > ?', [1]);

        $sql = $query->toString();
        $this->assertStringContainsString('HAVING', $sql);

        $rows = $query->fetch();
        // Should only return status groups with count > 1
        foreach ($rows as $row) {
            $this->assertGreaterThan(1, $row->cnt);
        }
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFullJoinSqlGeneration(string $dbName, Driver $driver): void
    {
        // Functional test: Execute FULL JOIN on existing seed data
        // This tests that the SQL generation works and executes without error
        $query = new Query($driver);
        $results = $query->select('*')
              ->from(self::$testTable)
              ->fullJoin(self::$testTable2, self::$testTable . '.id', self::$testTable2 . '.id')
              ->fetch();

        // Functional test: FULL JOIN should execute without error and return results
        // Seed data has 3 rows in table1 and 2 rows in table2
        // FULL JOIN should return all unique rows from both sides
        $this->assertGreaterThanOrEqual(2, count($results), "[$dbName] FULL JOIN should return at least 2 rows");
        $this->assertNotEmpty($results, "[$dbName] FULL JOIN should execute successfully and return results");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFullJoinMultipleSqlGeneration(string $dbName, Driver $driver): void
    {
        $this->expectException(UnsupportedSyntaxException::class);

        $query = new Query($driver);
        $query->select('*')
              ->from(self::$testTable)
              ->fullJoin(self::$testTable2, self::$testTable . '.id', self::$testTable2 . '.id')
              ->fullJoin(self::$testTable3, self::$testTable . '.id', self::$testTable3 . '.id')
              ->order(self::$testTable . '.id', 'asc')
              ->limit(10);

        $query->toString();
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFullJoinReversedKeysAllowed(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);
        $query->select('*')
              ->from(self::$testTable)
              ->fullJoin(self::$testTable2, self::$testTable2 . '.id', self::$testTable . '.id');

        $sql = $query->toString();
        $this->assertNotEmpty($sql, 'FULL JOIN should accept reversed join keys');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFullJoinUnqualifiedAmbiguousResolved(string $dbName, Driver $driver): void
    {
        // Smart resolution: left key prefers FROM table, right key prefers JOIN table
        $query = new Query($driver);
        $sql = $query->select('*')
              ->from(self::$testTable)
              ->fullJoin(self::$testTable2, 'id', 'id')
              ->toString();

        // Should generate valid SQL with qualified column names
        $this->assertNotEmpty($sql, "[$dbName] Smart resolution should generate valid SQL");

        // Verify both tables are referenced
        $wrappedTable1 = $driver->quoteName(self::$testTable);
        $wrappedTable2 = $driver->quoteName(self::$testTable2);
        $this->assertStringContainsString($wrappedTable1, $sql, "[$dbName] SQL should reference first table");
        $this->assertStringContainsString($wrappedTable2, $sql, "[$dbName] SQL should reference second table");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRightJoinReturnsCorrectRows(string $dbName, Driver $driver): void
    {
        // syntax_test has ids 1,2,3; syntax_test_2 has ids 1,2
        // RIGHT JOIN preserves all rows from the right table (syntax_test_2)
        $query = new Query($driver);
        $results = $query->select(self::$testTable2 . '.name')
              ->from(self::$testTable)
              ->rightJoin(self::$testTable2, self::$testTable . '.id', self::$testTable2 . '.id')
              ->fetch();

        $names = array_column($results, 'name');
        $this->assertCount(2, $names);
        $this->assertContains('User 4', $names);
        $this->assertContains('User 5', $names);
    }

    // =========================================================================
    // Syntax-Specific Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNormalizeColumns(string $dbName, Driver $driver): void
    {
        $columns = $driver->getTableColumns(self::$testTable);

        $this->assertIsArray($columns);
        $this->assertNotEmpty($columns);

        // All databases should have these columns (case-insensitive check)
        $columnKeys = array_map('strtolower', array_keys($columns));
        $this->assertContains('id', $columnKeys);
        $this->assertContains('name', $columnKeys);
        $this->assertContains('email', $columnKeys);
        $this->assertContains('status', $columnKeys);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQuoteName(string $dbName, Driver $driver): void
    {
        $quoted = $driver->quoteName('test_table');

        $this->assertNotEmpty($quoted);
        if ($driver->usesQuotedIdentifiers()) {
            $this->assertNotEquals('test_table', $quoted, 'Should add quotes');
        } else {
            $this->assertSame('test_table', $quoted, 'Should preserve bare identifier');
        }
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQuoteString(string $dbName, Driver $driver): void
    {
        $quoted = $driver->quote("test'value");

        $this->assertNotEmpty($quoted);
        // Should escape quotes and wrap in quotes
        $this->assertStringContainsString("test", $quoted);
        $this->assertNotEquals("test'value", $quoted, 'Should escape and quote');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPreparedStatements(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $rows = $query->select('*')
                      ->from(self::$testTable)
                      ->whereRaw('name = ?', ['User 1'])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals('User 1', $rows[0]->name);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNamedPlaceholders(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $rows = $query->select('*')
                      ->from(self::$testTable)
                      ->whereRaw('name = :name', ['name' => 'User 2'])
                      ->fetch();

        $this->assertCount(1, $rows);
        $this->assertEquals('User 2', $rows[0]->name);
    }
}
