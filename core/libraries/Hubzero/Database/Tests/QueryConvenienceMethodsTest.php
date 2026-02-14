<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Relational;

/**
 * Query Convenience Methods tests
 *
 * Tests for the convenience methods added to the Query class:
 * - exists() / doesntExist()
 * - whereColumn() / orWhereColumn()
 * - increment() / decrement()
 * - whereBetween() / whereNotBetween()
 * - whereNotLike() / orWhereNotLike()
 * - whereBetweenColumns() / whereNotBetweenColumns()
 * - whereNot() / orWhereNot()
 * - whereFullText() / orWhereFullText()
 */
class QueryConvenienceMethodsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['conv_orders', 'conv_products', 'conv_users'];
    }

    /**
     * Set up test tables using schema builder
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('conv_orders', true);
        $driver->dropTable('conv_products', true);
        $driver->dropTable('conv_users', true);

        $driver->createTable('conv_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->integer('login_count')->default(0)
            ->integer('score')->default(0)
            ->string('created_at', 50)->nullable()
            ->string('updated_at', 50)->nullable()
            ->execute();

        // Add explicit per-column fulltext indexes for engines that require
        // index coverage on each searched column for native fulltext predicates.
        $driver->addFulltextIndex('conv_users', 'conv_users_ft_name', ['name']);
        $driver->addFulltextIndex('conv_users', 'conv_users_ft_email', ['email']);

        $driver->createTable('conv_products')
            ->id()
            ->string('name', 255)
            ->integer('stock')->default(0)
            ->integer('price')->default(0)
            ->execute();

        $driver->createTable('conv_orders')
            ->id()
            ->integer('user_id')
            ->integer('total')->default(0)
            ->string('created_at', 50)->nullable()
            ->string('shipped_at', 50)->nullable()
            ->execute();
    }

    /**
     * Seed test data into all tables
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function seedTestData(Driver $driver): void
    {
        $query = new Query($driver);

        // Users
        $query->insertMany('conv_users', [
            [
                'name' => 'Alice', 'email' => 'alice@example.com',
                'login_count' => 5, 'score' => 100,
                'created_at' => '2024-01-01', 'updated_at' => '2024-01-15',
            ],
            [
                'name' => 'Bob', 'email' => 'bob@example.com',
                'login_count' => 3, 'score' => 50,
                'created_at' => '2024-01-02', 'updated_at' => '2024-01-02',
            ],
            [
                'name' => 'Charlie', 'email' => 'charlie@example.com',
                'login_count' => 0, 'score' => 25,
                'created_at' => '2024-01-03', 'updated_at' => '2024-01-03',
            ],
        ]);

        // Products
        $query->insertMany('conv_products', [
            ['name' => 'Widget', 'stock' => 100, 'price' => 10],
            ['name' => 'Gadget', 'stock' => 50, 'price' => 20],
            ['name' => 'Gizmo', 'stock' => 0, 'price' => 30],
        ]);

        // Orders - insert rows with non-null shipped_at via insertMany
        $query->insertMany('conv_orders', [
            ['user_id' => 1, 'total' => 100, 'created_at' => '2024-01-10', 'shipped_at' => '2024-01-11'],
        ]);

        // Insert order with NULL shipped_at using raw SQL
        $driver->setQuery("INSERT INTO " . $driver->quoteName('conv_orders') . " ("
            . $driver->quoteName('user_id') . ", "
            . $driver->quoteName('total') . ", "
            . $driver->quoteName('created_at') . ", "
            . $driver->quoteName('shipped_at')
            . ") VALUES (1, 50, '2024-01-12', NULL)");
        $driver->execute();

        $query->insertMany('conv_orders', [
            ['user_id' => 2, 'total' => 75, 'created_at' => '2024-01-13', 'shipped_at' => '2024-01-14'],
        ]);
    }

    /**
     * Reset test state: purge cache, clean tables, reset auto-increment, re-seed data
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function resetTestState(Driver $driver): void
    {
        Query::purgeCache();

        $driver->truncateTable('conv_orders');
        $driver->truncateTable('conv_products');
        $driver->truncateTable('conv_users');

        $this->seedTestData($driver);
    }

    /**
     * Clean up after all tests - custom cleanup for Query::purgeCache()
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        Query::purgeCache();
        parent::tearDownAfterClass();
    }

    // =========================================================================
    // exists() / doesntExist() Tests
    // =========================================================================

    /**
     * Test exists() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testExistsMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'exists'));
    }

    /**
     * Test doesntExist() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testDoesntExistMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'doesntExist'));
    }

    /**
     * Test exists() returns true when rows exist
     */
    #[DataProvider('databaseProvider')]
    public function testExistsReturnsTrueWhenRowsExist(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_users')
            ->exists();

        $this->assertTrue($result);
    }

    /**
     * Test exists() returns false when no rows exist
     */
    #[DataProvider('databaseProvider')]
    public function testExistsReturnsFalseWhenNoRowsExist(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'NonExistent')
            ->exists();

        $this->assertFalse($result);
    }

    /**
     * Test doesntExist() returns true when no rows exist
     */
    #[DataProvider('databaseProvider')]
    public function testDoesntExistReturnsTrueWhenNoRowsExist(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'NonExistent')
            ->doesntExist();

        $this->assertTrue($result);
    }

    /**
     * Test doesntExist() returns false when rows exist
     */
    #[DataProvider('databaseProvider')]
    public function testDoesntExistReturnsFalseWhenRowsExist(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_users')
            ->doesntExist();

        $this->assertFalse($result);
    }

    /**
     * Test exists() with WHERE clause
     */
    #[DataProvider('databaseProvider')]
    public function testExistsWithWhereClause(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'Alice')
            ->exists();

        $this->assertTrue($result);
    }

    /**
     * Test doesntExist() on empty table
     */
    #[DataProvider('databaseProvider')]
    public function testDoesntExistOnEmptyTable(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Clear the products table
        $driver->setQuery('DELETE FROM ' . $driver->quoteName('conv_products'));
        $driver->execute();

        $query = new Query($driver);

        $result = $query->select('*')
            ->from('conv_products')
            ->doesntExist();

        $this->assertTrue($result);
    }

    // =========================================================================
    // whereColumn() / orWhereColumn() Tests
    // =========================================================================

    /**
     * Test whereColumn() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereColumn'));
    }

    /**
     * Test orWhereColumn() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereColumnMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'orWhereColumn'));
    }

    /**
     * Test whereColumn() returns self for chaining
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnReturnsSelfForChaining(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $query->select('*')->from('conv_users');

        $result = $query->whereColumn('created_at', 'updated_at');

        $this->assertSame($query, $result);
    }

    /**
     * Test whereColumn() with equality comparison
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnEquality(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where created_at equals updated_at (never updated)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereColumn('created_at', 'updated_at')
            ->fetch('rows');

        // Bob and Charlie have same created_at and updated_at
        $this->assertCount(2, $results);
    }

    /**
     * Test whereColumn() with explicit operator
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnWithOperator(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where updated_at is different from created_at (has been updated)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereColumn('created_at', '!=', 'updated_at')
            ->fetch('rows');

        // Alice has different created_at and updated_at
        $this->assertCount(1, $results);
    }

    /**
     * Test whereColumn() with > operator
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnGreaterThan(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where login_count > score (none should match with our test data)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereColumn('login_count', '>', 'score')
            ->fetch('rows');

        $this->assertCount(0, $results);
    }

    /**
     * Test whereColumn() with < operator
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnLessThan(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where login_count < score (all users)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereColumn('login_count', '<', 'score')
            ->fetch('rows');

        $this->assertCount(3, $results);
    }

    /**
     * Test orWhereColumn()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereColumn(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where login_count > 4 OR login_count < score
        $results = $query->select('*')
            ->from('conv_users')
            ->where('login_count', '>', 4)
            ->orWhereColumn('login_count', '<', 'score')
            ->fetch('rows');

        // All users match (either Alice with login > 4, or all have login < score)
        $this->assertCount(3, $results);
    }

    /**
     * Test whereColumn() combined with regular where
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnCombinedWithWhere(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Orders where created_at != shipped_at AND total >= 75
        $results = $query->select('*')
            ->from('conv_orders')
            ->whereColumn('created_at', '!=', 'shipped_at')
            ->where('total', '>=', 75)
            ->fetch('rows');

        // Order 1 (100, shipped different day) and Order 3 (75, shipped different day)
        $this->assertCount(2, $results);
    }

    /**
     * Test whereColumn() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereColumnGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_users')
            ->whereColumn('created_at', '!=', 'updated_at')
            ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Alice', $results[0]->name);
    }

    // =========================================================================
    // increment() / decrement() Tests
    // =========================================================================

    /**
     * Test increment() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'increment'));
    }

    /**
     * Test decrement() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'decrement'));
    }

    /**
     * Test increment() by default amount (1)
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementByDefault(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Get initial value
        $sql = "SELECT " . $driver->quoteName('login_count')
            . " FROM " . $driver->quoteName('conv_users')
            . " WHERE " . $driver->quoteName('id') . " = 1";
        $initial = $driver->setQuery($sql)->loadResult();
        $this->assertEquals(5, $initial);

        // Increment login_count for user 1
        $affected = $query->from('conv_users')
            ->whereEquals('id', 1)
            ->increment('login_count');

        $this->assertEquals(1, $affected);

        // Verify the increment
        $sql = "SELECT " . $driver->quoteName('login_count')
            . " FROM " . $driver->quoteName('conv_users')
            . " WHERE " . $driver->quoteName('id') . " = 1";
        $result = $driver->setQuery($sql)->loadResult();
        $this->assertEquals(6, $result);
    }

    /**
     * Test increment() by custom amount
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementByCustomAmount(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Increment score by 25 for user 2
        $affected = $query->from('conv_users')
            ->whereEquals('id', 2)
            ->increment('score', 25);

        $this->assertEquals(1, $affected);

        // Verify the increment (50 + 25 = 75)
        $sql = "SELECT " . $driver->quoteName('score')
            . " FROM " . $driver->quoteName('conv_users')
            . " WHERE " . $driver->quoteName('id') . " = 2";
        $result = $driver->setQuery($sql)->loadResult();
        $this->assertEquals(75, $result);
    }

    /**
     * Test increment() with extra columns
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementWithExtraColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $newTimestamp = '2024-02-01 12:00:00';

        // Increment login_count and update timestamp
        $affected = $query->from('conv_users')
            ->whereEquals('id', 1)
            ->increment('login_count', 1, ['updated_at' => $newTimestamp]);

        $this->assertEquals(1, $affected);

        // Verify both the increment and the extra column update
        $sql = "SELECT " . $driver->quoteName('login_count')
            . ", " . $driver->quoteName('updated_at')
            . " FROM " . $driver->quoteName('conv_users')
            . " WHERE " . $driver->quoteName('id') . " = 1";
        $result = $driver->setQuery($sql)->loadAssoc();
        $this->assertEquals(6, $result['login_count']);
        $this->assertEquals($newTimestamp, $result['updated_at']);
    }

    /**
     * Test increment() affects multiple rows
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementMultipleRows(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Increment score for all users
        $affected = $query->from('conv_users')
            ->increment('score', 10);

        $this->assertEquals(3, $affected);

        // Verify all users got incremented
        $sql = "SELECT " . $driver->quoteName('score')
            . " FROM " . $driver->quoteName('conv_users')
            . " ORDER BY " . $driver->quoteName('id');
        $results = $driver->setQuery($sql)->loadColumn();
        $this->assertEquals([110, 60, 35], $results);
    }

    /**
     * Test decrement() by default amount (1)
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementByDefault(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Decrement stock for product 1
        $affected = $query->from('conv_products')
            ->whereEquals('id', 1)
            ->decrement('stock');

        $this->assertEquals(1, $affected);

        // Verify the decrement (100 - 1 = 99)
        $sql = "SELECT " . $driver->quoteName('stock')
            . " FROM " . $driver->quoteName('conv_products')
            . " WHERE " . $driver->quoteName('id') . " = 1";
        $result = $driver->setQuery($sql)->loadResult();
        $this->assertEquals(99, $result);
    }

    /**
     * Test decrement() by custom amount
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementByCustomAmount(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Decrement stock by 25 for product 2
        $affected = $query->from('conv_products')
            ->whereEquals('id', 2)
            ->decrement('stock', 25);

        $this->assertEquals(1, $affected);

        // Verify the decrement (50 - 25 = 25)
        $sql = "SELECT " . $driver->quoteName('stock')
            . " FROM " . $driver->quoteName('conv_products')
            . " WHERE " . $driver->quoteName('id') . " = 2";
        $result = $driver->setQuery($sql)->loadResult();
        $this->assertEquals(25, $result);
    }

    /**
     * Test decrement() with extra columns
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementWithExtraColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Decrement stock and update name
        $affected = $query->from('conv_products')
            ->whereEquals('id', 1)
            ->decrement('stock', 5, ['name' => 'Widget (Low Stock)']);

        $this->assertEquals(1, $affected);

        // Verify both the decrement and the extra column update
        $sql = "SELECT " . $driver->quoteName('stock')
            . ", " . $driver->quoteName('name')
            . " FROM " . $driver->quoteName('conv_products')
            . " WHERE " . $driver->quoteName('id') . " = 1";
        $result = $driver->setQuery($sql)->loadAssoc();
        $this->assertEquals(95, $result['stock']);
        $this->assertEquals('Widget (Low Stock)', $result['name']);
    }

    /**
     * Test decrement() affects zero rows when condition not met
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementAffectsZeroRows(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Try to decrement for non-existent product
        $affected = $query->from('conv_products')
            ->whereEquals('id', 9999)
            ->decrement('stock');

        $this->assertEquals(0, $affected);
    }

    /**
     * Test increment() throws exception without table
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementThrowsExceptionWithoutTable(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot increment: no table specified');

        $query = new Query($driver);
        $query->increment('column');
    }

    /**
     * Test decrement() throws exception without table
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementThrowsExceptionWithoutTable(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot decrement: no table specified');

        $query = new Query($driver);
        $query->decrement('column');
    }

    /**
     * Test increment() returns int type
     */
    #[DataProvider('databaseProvider')]
    public function testIncrementReturnsInt(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->from('conv_users')
            ->whereEquals('id', 1)
            ->increment('login_count');

        $this->assertIsInt($result);
    }

    /**
     * Test decrement() returns int type
     */
    #[DataProvider('databaseProvider')]
    public function testDecrementReturnsInt(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $result = $query->from('conv_products')
            ->whereEquals('id', 1)
            ->decrement('stock');

        $this->assertIsInt($result);
    }

    // =========================================================================
    // whereBetween() / whereNotBetween() Tests
    // =========================================================================

    /**
     * Test whereBetween() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereBetween'));
    }

    /**
     * Test whereNotBetween() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotBetweenMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereNotBetween'));
    }

    /**
     * Test whereBetween() filters correctly
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenFiltersCorrectly(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users with login_count between 3 and 5 (Bob=3, Alice=5)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereBetween('login_count', [3, 5])
            ->fetch('rows');

        $this->assertCount(2, $results);
    }

    /**
     * Test whereBetween() with numeric values
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenWithNumericValues(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Products with stock between 25 and 75
        $results = $query->select('*')
            ->from('conv_products')
            ->whereBetween('stock', [25, 75])
            ->fetch('rows');

        // Gadget has 50 stock
        $this->assertCount(1, $results);
    }

    /**
     * Test whereBetween() with dates
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenWithDates(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users created between Jan 1 and Jan 2
        $results = $query->select('*')
            ->from('conv_users')
            ->whereBetween('created_at', ['2024-01-01', '2024-01-02'])
            ->fetch('rows');

        // Alice (Jan 1) and Bob (Jan 2)
        $this->assertCount(2, $results);
    }

    /**
     * Test orWhereBetween()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereBetween(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users with login_count = 0 OR login_count between 4 and 6
        $results = $query->select('*')
            ->from('conv_users')
            ->whereEquals('login_count', 0)
            ->orWhereBetween('login_count', [4, 6])
            ->fetch('rows');

        // Charlie (0) and Alice (5)
        $this->assertCount(2, $results);
    }

    /**
     * Test whereNotBetween() filters correctly
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotBetweenFiltersCorrectly(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users with login_count NOT between 1 and 4 (excludes Bob=3)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereNotBetween('login_count', [1, 4])
            ->fetch('rows');

        // Alice (5) and Charlie (0)
        $this->assertCount(2, $results);
    }

    /**
     * Test orWhereNotBetween()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNotBetween(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where name = 'Alice' OR login_count NOT between 1 and 10
        $results = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'Alice')
            ->orWhereNotBetween('login_count', [1, 10])
            ->fetch('rows');

        // Alice (explicit) and Charlie (login_count=0, not between 1-10)
        $this->assertCount(2, $results);
    }

    /**
     * Test whereBetween() throws exception with wrong number of values
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenThrowsExceptionWithWrongValues(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('whereBetween requires exactly 2 values');

        $query = new Query($driver);
        $query->select('*')
            ->from('conv_users')
            ->whereBetween('login_count', [1, 2, 3])
            ->fetch('rows');
    }

    /**
     * Test whereBetween() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_users')
            ->whereBetween('score', [50, 100])
            ->fetch('rows');

        $this->assertCount(2, $results);
    }

    /**
     * Test whereNotBetween() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotBetweenGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_users')
            ->whereNotBetween('score', [50, 100])
            ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Charlie', $results[0]->name);
    }

    // =========================================================================
    // whereNotLike() Tests
    // =========================================================================

    /**
     * Test whereNotLike() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotLikeMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereNotLike'));
    }

    /**
     * Test orWhereNotLike() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNotLikeMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'orWhereNotLike'));
    }

    /**
     * Test whereNotLike() filters correctly
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotLikeFiltersCorrectly(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users whose name does NOT contain 'li' (excludes Alice and Charlie)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereNotLike('name', 'li')
            ->fetch('rows');

        // Only Bob
        $this->assertCount(1, $results);
    }

    /**
     * Test orWhereNotLike()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNotLike(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where name = 'Alice' OR name does NOT contain 'o'
        $results = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'Alice')
            ->orWhereNotLike('name', 'o')
            ->fetch('rows');

        // Alice (explicit) and Charlie (no 'o')
        $this->assertCount(2, $results);
    }

    /**
     * Test whereNotLike() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotLikeGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_users')
            ->whereNotLike('email', 'spam')
            ->fetch('rows');

        $this->assertCount(3, $results);
    }

    /**
     * Test whereBetween() combined with other clauses
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenCombinedWithOtherClauses(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users with score between 25 and 75 AND login_count > 0
        $results = $query->select('*')
            ->from('conv_users')
            ->whereBetween('score', [25, 75])
            ->where('login_count', '>', 0)
            ->fetch('rows');

        // Bob (score=50, login=3)
        $this->assertCount(1, $results);
    }

    // =========================================================================
    // whereBetweenColumns() Tests
    // =========================================================================

    /**
     * Test whereBetweenColumns() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenColumnsMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereBetweenColumns'));
    }

    /**
     * Test whereNotBetweenColumns() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotBetweenColumnsMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereNotBetweenColumns'));
    }

    /**
     * Test whereBetweenColumns() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenColumnsGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_orders')
            ->whereBetweenColumns('total', ['id', 'user_id'])
            ->fetch('rows');

        $this->assertCount(0, $results);
    }

    /**
     * Test whereNotBetweenColumns() generates correct SQL
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotBetweenColumnsGeneratesCorrectSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_orders')
            ->whereNotBetweenColumns('total', ['id', 'user_id'])
            ->fetch('rows');

        $this->assertCount(3, $results);
    }

    /**
     * Test whereBetweenColumns() filters correctly
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenColumnsFiltersCorrectly(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Add a test product with known column relationships
        $driver->setQuery("INSERT INTO " . $driver->quoteName('conv_products') . " ("
            . $driver->quoteName('name') . ", "
            . $driver->quoteName('stock') . ", "
            . $driver->quoteName('price')
            . ") VALUES ('Test', 10, 50)");
        $driver->execute();

        $query = new Query($driver);

        // Find products where stock is between id and price
        $results = $query->select('*')
            ->from('conv_products')
            ->whereBetweenColumns('stock', ['id', 'price'])
            ->fetch('rows');

        // This tests that the SQL executes correctly
        $this->assertIsArray($results);
    }

    /**
     * Test orWhereBetweenColumns()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereBetweenColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_products')
            ->whereEquals('id', 1)
            ->orWhereBetweenColumns('stock', ['id', 'price'])
            ->fetch('rows');

        $this->assertCount(1, $results);
    }

    /**
     * Test orWhereNotBetweenColumns()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNotBetweenColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_products')
            ->whereEquals('id', 1)
            ->orWhereNotBetweenColumns('stock', ['id', 'price'])
            ->fetch('rows');

        $this->assertCount(3, $results);
    }

    /**
     * Test whereBetweenColumns() throws exception with wrong number of columns
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenColumnsThrowsExceptionWithWrongColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('whereBetweenColumns requires exactly 2 column names');

        $query = new Query($driver);
        $query->select('*')
            ->from('conv_products')
            ->whereBetweenColumns('stock', ['col1', 'col2', 'col3'])
            ->fetch('rows');
    }

    /**
     * Test whereBetweenColumns() returns self for chaining
     */
    #[DataProvider('databaseProvider')]
    public function testWhereBetweenColumnsReturnsSelfForChaining(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $query->select('*')->from('conv_products');

        $result = $query->whereBetweenColumns('stock', ['id', 'price']);

        $this->assertSame($query, $result);
    }

    // =========================================================================
    // whereNot() / orWhereNot() Tests
    // =========================================================================

    /**
     * Test whereNot() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereNot'));
    }

    /**
     * Test orWhereNot() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNotMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'orWhereNot'));
    }

    /**
     * Test whereNot() generates SQL with NOT wrapper
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotGeneratesSqlWithNotWrapper(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        $results = $query->select('*')
            ->from('conv_users')
            ->whereNot(function ($q) {
                $q->whereEquals('name', 'Alice');
            })
            ->fetch('rows');

        $this->assertCount(2, $results);
    }

    /**
     * Test whereNot() filters correctly with single condition
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotFiltersSingleCondition(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where NOT (name = 'Alice')
        $results = $query->select('*')
            ->from('conv_users')
            ->whereNot(function ($q) {
                $q->whereEquals('name', 'Alice');
            })
            ->fetch('rows');

        // Bob and Charlie
        $this->assertCount(2, $results);
    }

    /**
     * Test whereNot() filters correctly with multiple conditions
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotFiltersMultipleConditions(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where NOT (login_count = 0 OR login_count = 3)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereNot(function ($q) {
                $q->whereEquals('login_count', 0)
                  ->orWhereEquals('login_count', 3);
            })
            ->fetch('rows');

        // Only Alice (login_count = 5)
        $this->assertCount(1, $results);
    }

    /**
     * Test orWhereNot()
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereNot(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where name = 'Alice' OR NOT (login_count = 3)
        $results = $query->select('*')
            ->from('conv_users')
            ->whereEquals('name', 'Alice')
            ->orWhereNot(function ($q) {
                $q->whereEquals('login_count', 3);
            })
            ->fetch('rows');

        // Alice (explicit) and Charlie (login_count = 0, not 3)
        // Note: Alice also satisfies NOT (login_count = 3), but we're using OR so she's included anyway
        $this->assertGreaterThanOrEqual(2, count($results));
    }

    /**
     * Test whereNot() returns self for chaining
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotReturnsSelfForChaining(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $query->select('*')->from('conv_users');

        $result = $query->whereNot(function ($q) {
            $q->whereEquals('name', 'Test');
        });

        $this->assertSame($query, $result);
    }

    /**
     * Test whereNot() combined with other where clauses
     */
    #[DataProvider('databaseProvider')]
    public function testWhereNotCombinedWithOtherClauses(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);

        // Users where score > 30 AND NOT (name = 'Alice')
        $results = $query->select('*')
            ->from('conv_users')
            ->where('score', '>', 30)
            ->whereNot(function ($q) {
                $q->whereEquals('name', 'Alice');
            })
            ->fetch('rows');

        // Bob (score=50, not Alice)
        $this->assertCount(1, $results);
    }

    // =========================================================================
    // whereFullText() Tests
    // =========================================================================

    /**
     * Test whereFullText() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'whereFullText'));
    }

    /**
     * Test orWhereFullText() method exists on Query
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereFullTextMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'orWhereFullText'));
    }

    /**
     * Test whereFullText() with single column executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextSingleColumn(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereFullText('name', 'Alice')
            ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Alice', $results[0]->name);
    }

    /**
     * Test whereFullText() with multiple columns executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextMultipleColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereFullText(['name', 'email'], 'alice')
            ->fetch('rows');

        $this->assertCount(1, $results);
    }

    /**
     * Test whereFullText() with boolean mode executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextBooleanMode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereFullText('name', 'Alice', ['mode' => 'boolean'])
            ->fetch('rows');

        $this->assertCount(1, $results);
    }

    /**
     * Test whereFullText() with expansion mode executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextExpansionMode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereFullText('name', 'Alice', ['mode' => 'expansion'])
            ->fetch('rows');

        $this->assertIsArray($results);
    }

    /**
     * Test whereFullText() natural mode executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextNaturalMode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereFullText('name', 'Alice', ['mode' => 'natural'])
            ->fetch('rows');

        $this->assertCount(1, $results);
    }

    /**
     * Test orWhereFullText() executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereFullTextExecutes(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('conv_users')
            ->whereEquals('id', 999)
            ->orWhereFullText('name', 'Alice')
            ->fetch('rows');

        $this->assertCount(1, $results);
    }

    /**
     * Test whereFullText() returns self for chaining
     */
    #[DataProvider('databaseProvider')]
    public function testWhereFullTextReturnsSelfForChaining(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $query->select('*')->from('conv_users');

        $result = $query->whereFullText('name', 'search');

        $this->assertSame($query, $result);
    }
}
