<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Drivers\Base\BaseSqlSyntax;

/**
 * INSERT IGNORE and INSERT ... SELECT tests
 *
 * Tests for the insertIgnore() and fromSelect() functionality that enables
 * INSERT IGNORE and INSERT ... SELECT patterns with the fluent API.
 *
 * All tests execute queries against real databases and verify results.
 */
class InsertIgnoreSelectTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['iis_dest_users', 'iis_unique_emails', 'iis_source_users'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop in reverse dependency order
        try {
            $driver->dropTable('iis_dest_users', true);
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        try {
            $driver->dropTable('iis_unique_emails', true);
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        try {
            $driver->dropTable('iis_source_users', true);
        } catch (\Exception $e) {
            // Table doesn't exist
        }

        $schema = $driver->schema();

        // Source users - standard table with test data
        $schema->createTable('iis_source_users')
            ->id()
            ->string('username', 255)
            ->string('password_hash', 255)
            ->string('email', 255)
            ->integer('active')->default(1)
            ->string('created', 50)->nullable()
            ->execute();
        // Dest users - has auto-increment PK named user_id
        // The PK constraint prevents duplicate user_id inserts
        $schema->createTable('iis_dest_users')
            ->id('user_id')
            ->string('passhash', 255)
            ->string('email', 255)->nullable()
            ->uniqueIndex('idx_iis_du_email', 'email')
            ->execute();
        // Unique emails - has UNIQUE constraint on email for INSERT IGNORE testing
        $schema->createTable('iis_unique_emails')
            ->id()
            ->string('email', 255)
            ->string('name', 255)->nullable()
            ->uniqueIndex('idx_iis_ue_email', 'email')
            ->execute();
    }

    /**
     * Seed source data for tests
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function seedSourceData(Driver $driver): void
    {
        $query = new Query($driver);
        $query->insertMany('iis_source_users', [
            ['username' => 'alice', 'password_hash' => 'hash1', 'email' => 'alice@example.com', 'active' => 1],
            ['username' => 'bob', 'password_hash' => 'hash2', 'email' => 'bob@example.com', 'active' => 1],
            ['username' => 'charlie', 'password_hash' => 'hash3', 'email' => 'charlie@example.com', 'active' => 0],
        ]);
    }

    /**
     * Reset test data before each test
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function resetTestState(Driver $driver): void
    {
        Query::purgeCache();

        $driver->truncateTable('iis_dest_users');
        $driver->truncateTable('iis_unique_emails');
        $driver->truncateTable('iis_source_users');

        $this->seedSourceData($driver);
    }


    // =========================================================================
    // Method Existence Tests
    // =========================================================================

    /**
     * Test insertIgnore() method exists
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreMethodExists(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(Query::class, 'insertIgnore'),
            'Query class should have insertIgnore method'
        );
    }

    /**
     * Test fromSelect() method exists
     */
    #[DataProvider('databaseProvider')]
    public function testFromSelectMethodExists(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(Query::class, 'fromSelect'),
            'Query class should have fromSelect method'
        );
    }

    /**
     * Test columns() method exists
     */
    #[DataProvider('databaseProvider')]
    public function testColumnsMethodExists(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(Query::class, 'columns'),
            'Query class should have columns method'
        );
    }

    /**
     * Test fromSelectRaw() method exists
     */
    #[DataProvider('databaseProvider')]
    public function testFromSelectRawMethodExists(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(Query::class, 'fromSelectRaw'),
            'Query class should have fromSelectRaw method'
        );
    }

    /**
     * Test Syntax class has setColumns method
     */
    #[DataProvider('databaseProvider')]
    public function testSyntaxHasSetColumnsMethod(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(BaseSqlSyntax::class, 'setColumns'),
            'Syntax class should have setColumns method'
        );
    }

    /**
     * Test Syntax class has setInsertSelect method
     */
    #[DataProvider('databaseProvider')]
    public function testSyntaxHasSetInsertSelectMethod(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(BaseSqlSyntax::class, 'setInsertSelect'),
            'Syntax class should have setInsertSelect method'
        );
    }

    /**
     * Test Syntax class has resetInsertSelect method
     */
    #[DataProvider('databaseProvider')]
    public function testSyntaxHasResetInsertSelectMethod(string $dbName, Driver $driver): void
    {
        $this->assertTrue(
            method_exists(BaseSqlSyntax::class, 'resetInsertSelect'),
            'Syntax class should have resetInsertSelect method'
        );
    }

    // =========================================================================
    // INSERT IGNORE Tests
    // =========================================================================

    /**
     * Test insertIgnore() works by inserting and then ignoring duplicates
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreIgnoresDuplicates(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Insert first record
        $query = new Query($driver);
        $query->insertIgnore('iis_unique_emails')
            ->values(['email' => 'duplicate@example.com', 'name' => 'First'])
            ->execute();

        // Try to insert duplicate - should not throw error
        $query2 = new Query($driver);
        $query2->insertIgnore('iis_unique_emails')
            ->values(['email' => 'duplicate@example.com', 'name' => 'Second'])
            ->execute();

        // Verify only one record exists
        $driver->setQuery("SELECT COUNT(*) as cnt FROM iis_unique_emails WHERE email = 'duplicate@example.com'");
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->cnt);

        // Verify first record was kept (not updated)
        $driver->setQuery("SELECT name FROM iis_unique_emails WHERE email = 'duplicate@example.com'");
        $result = $driver->loadObject();
        $this->assertEquals('First', $result->name);
    }

    /**
     * Test insertIgnore() is equivalent to insert($table, true)
     *
     * Both methods should handle duplicates the same way.
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreEquivalentToInsertTrue(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Insert via insertIgnore
        $query1 = new Query($driver);
        $query1->insertIgnore('iis_unique_emails')
            ->values(['email' => 'equiv@test.com', 'name' => 'First'])
            ->execute();

        // Insert duplicate via insert($table, true)
        $query2 = new Query($driver);
        $query2->insert('iis_unique_emails', true)
            ->values(['email' => 'equiv@test.com', 'name' => 'Second'])
            ->execute();

        // Both should result in same behavior - only 1 record
        $driver->setQuery("SELECT COUNT(*) as cnt FROM iis_unique_emails WHERE email = 'equiv@test.com'");
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->cnt);
    }

    // =========================================================================
    // INSERT ... SELECT Tests (Query Object Approach)
    // =========================================================================

    /**
     * Test INSERT ... SELECT with Query object (NO CLOSURES)
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectWithQueryObject(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users')
            ->where('active', '=', 1);

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery)
            ->execute();

        // Should insert 2 active users (alice and bob)
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(2, (int) $result->cnt, 'Should insert 2 active users');
    }

    /**
     * Test INSERT IGNORE ... SELECT with Query object
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreSelectWithQueryObject(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users');

        $insertQuery = new Query($driver);
        $insertQuery->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery)
            ->execute();

        // All 3 source users should be inserted
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(3, (int) $result->cnt);
    }

    /**
     * Test INSERT ... SELECT preserves bindings
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectPreservesBindings(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->from('iis_source_users')
            ->where('active', '=', 1)
            ->where('email', 'LIKE', '%@example.com');

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash'])
            ->fromSelect($selectQuery);

        $bindings = $insertQuery->getBindings();

        // Should have 2 bindings from WHERE clauses
        $this->assertCount(2, $bindings);
        $this->assertEquals(1, $bindings[0]);
        $this->assertEquals('%@example.com', $bindings[1]);
    }

    /**
     * Test INSERT ... SELECT without columns specified executes successfully
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectWithoutColumns(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Select columns that match dest_users schema: user_id, passhash, email
        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users')
            ->where('active', '=', 1);

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->fromSelect($selectQuery)
            ->execute();

        // Should insert 2 active users
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(2, (int) $result->cnt);
    }

    // =========================================================================
    // INSERT ... SELECT Tests (Closure Approach)
    // =========================================================================

    /**
     * Test INSERT ... SELECT with closure
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectWithClosure(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect(function ($q) {
                $q->select('id')
                  ->select('password_hash')
                  ->select('email')
                  ->from('iis_source_users')
                  ->where('active', '=', 1);
            })
            ->execute();

        // Should insert 2 active users
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(2, (int) $result->cnt);
    }

    /**
     * Test INSERT IGNORE ... SELECT with closure
     */
    #[DataProvider('databaseProvider')]
    public function testInsertIgnoreSelectWithClosure(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $insertQuery = new Query($driver);
        $insertQuery->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect(function ($q) {
                $q->select('id')
                  ->select('password_hash')
                  ->select('email')
                  ->from('iis_source_users');
            })
            ->execute();

        // All 3 source users should be inserted
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(3, (int) $result->cnt);
    }

    /**
     * Test Query object and closure produce identical results
     */
    #[DataProvider('databaseProvider')]
    public function testQueryObjectAndClosureProduceSameResults(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Query object approach
        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users')
            ->where('active', '=', 1);

        $insertQuery1 = new Query($driver);
        $insertQuery1->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery)
            ->execute();

        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $count1 = (int) $driver->loadObject()->cnt;

        // Reset dest table for closure approach
        $driver->setQuery('DELETE FROM iis_dest_users');
        $driver->execute();

        // Closure approach
        $insertQuery2 = new Query($driver);
        $insertQuery2->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect(function ($q) {
                $q->select('id')
                  ->select('password_hash')
                  ->select('email')
                  ->from('iis_source_users')
                  ->where('active', '=', 1);
            })
            ->execute();

        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $count2 = (int) $driver->loadObject()->cnt;

        $this->assertEquals($count1, $count2, 'Query object and closure should produce identical results');
        $this->assertEquals(2, $count1, 'Both should insert 2 active users');
    }

    // =========================================================================
    // INSERT ... SELECT Tests (Raw SQL Approach)
    // =========================================================================

    /**
     * Test INSERT ... SELECT with raw SQL
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectWithRawSql(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Build the raw SQL using quoted names for database compatibility
        $sourceTable = $driver->quoteName('iis_source_users');
        $idCol = $driver->quoteName('id');
        $passCol = $driver->quoteName('password_hash');
        $emailCol = $driver->quoteName('email');
        $activeCol = $driver->quoteName('active');

        $rawSql = "SELECT {$idCol}, {$passCol}, {$emailCol} FROM {$sourceTable} WHERE {$activeCol} = ?";

        $insertQuery = new Query($driver);
        $insertQuery->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelectRaw($rawSql, [1]);

        // Verify bindings before execution (execute consumes them)
        $bindings = $insertQuery->getBindings();
        $this->assertCount(1, $bindings);
        $this->assertEquals(1, $bindings[0]);

        // Execute
        $insertQuery->execute();

        // Should insert 2 active users
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(2, (int) $result->cnt);
    }

    /**
     * Test fromSelectRaw() with no bindings
     */
    #[DataProvider('databaseProvider')]
    public function testFromSelectRawWithoutBindings(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $sourceTable = $driver->quoteName('iis_source_users');
        $idCol = $driver->quoteName('id');
        $passCol = $driver->quoteName('password_hash');
        $emailCol = $driver->quoteName('email');

        $rawSql = "SELECT {$idCol}, {$passCol}, {$emailCol} FROM {$sourceTable}";

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelectRaw($rawSql);

        $bindings = $insertQuery->getBindings();
        $this->assertCount(0, $bindings);

        // Execute to verify it works
        $insertQuery->execute();

        // All 3 source users should be inserted
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(3, (int) $result->cnt);
    }

    // =========================================================================
    // Error Handling Tests
    // =========================================================================

    /**
     * Test fromSelect() throws exception for invalid input
     */
    #[DataProvider('databaseProvider')]
    public function testFromSelectThrowsExceptionForInvalidInput(string $dbName, Driver $driver): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('fromSelect() expects a Query object or closure');

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->fromSelect('invalid string');
    }

    // =========================================================================
    // Syntax Class Functional Tests
    // =========================================================================

    /**
     * Test buildValues() handles INSERT SELECT correctly
     *
     * Verifies that INSERT ... SELECT executes and does not include VALUES.
     */
    #[DataProvider('databaseProvider')]
    public function testBuildValuesHandlesInsertSelect(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Use the Query API to execute INSERT ... SELECT
        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users');

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery)
            ->execute();

        // All 3 source users should be inserted
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(3, (int) $result->cnt);
    }

    /**
     * Test buildValues() handles regular INSERT VALUES correctly (regression test)
     */
    #[DataProvider('databaseProvider')]
    public function testBuildValuesHandlesRegularInsert(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $query->insert('iis_unique_emails')
            ->values(['email' => 'regular@test.com', 'name' => 'Regular'])
            ->execute();

        $driver->setQuery("SELECT COUNT(*) as cnt FROM iis_unique_emails WHERE email = 'regular@test.com'");
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->cnt);
    }

    /**
     * Test that after using INSERT SELECT, a new Query works for regular INSERT
     *
     * Verifies that the INSERT SELECT state on one Query does not affect
     * a separate Query object used for a regular INSERT.
     */
    #[DataProvider('databaseProvider')]
    public function testNewQueryAfterInsertSelectWorksForRegularInsert(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // Set up INSERT SELECT and execute it
        $query1 = new Query($driver);
        $query1->insert('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect(function ($q) {
                $q->select('id')->select('password_hash')->select('email')->from('iis_source_users');
            })
            ->execute();

        // A new query should work fine for regular insert
        $query2 = new Query($driver);
        $query2->insert('iis_unique_emails')
            ->values(['email' => 'reset@test.com', 'name' => 'Reset'])
            ->execute();

        $driver->setQuery("SELECT COUNT(*) as cnt FROM iis_unique_emails WHERE email = 'reset@test.com'");
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->cnt);
    }

    // =========================================================================
    // Integration Tests
    // =========================================================================

    /**
     * Test complete INSERT IGNORE ... SELECT workflow
     *
     * First inserts active users, then tries to insert all users (including duplicates).
     * INSERT IGNORE should skip duplicate rows and insert non-duplicates.
     */
    #[DataProvider('databaseProvider')]
    public function testCompleteInsertIgnoreSelectWorkflow(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        // First insert - only active users
        $selectQuery1 = new Query($driver);
        $selectQuery1->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users')
            ->where('active', '=', 1);

        $insertQuery1 = new Query($driver);
        $insertQuery1->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery1)
            ->execute();

        // Verify 2 records inserted (alice and bob)
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(2, (int) $result->cnt);

        // Second insert with overlapping data - should ignore duplicates
        $selectQuery2 = new Query($driver);
        $selectQuery2->select('id')
            ->select('password_hash')
            ->select('email')
            ->from('iis_source_users');

        $insertQuery2 = new Query($driver);
        $insertQuery2->insertIgnore('iis_dest_users')
            ->columns(['user_id', 'passhash', 'email'])
            ->fromSelect($selectQuery2)
            ->execute();

        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $finalCount = (int) $result->cnt;

        // INSERT IGNORE with duplicates should skip duplicate rows and insert non-duplicates
        $this->assertEquals(3, $finalCount, 'INSERT IGNORE should skip duplicates and insert charlie');
    }

    /**
     * Test INSERT SELECT with complex WHERE conditions
     */
    #[DataProvider('databaseProvider')]
    public function testInsertSelectWithComplexWhere(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $selectQuery = new Query($driver);
        $selectQuery->select('id')
            ->select('password_hash')
            ->from('iis_source_users')
            ->where('active', '=', 1)
            ->where('email', 'LIKE', '%alice%');

        $insertQuery = new Query($driver);
        $insertQuery->insert('iis_dest_users')
            ->columns(['user_id', 'passhash'])
            ->fromSelect($selectQuery)
            ->execute();

        // Should insert only Alice
        $driver->setQuery('SELECT COUNT(*) as cnt FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->cnt);

        // Verify it's Alice's record (id=1 from source)
        $driver->setQuery('SELECT user_id FROM iis_dest_users');
        $result = $driver->loadObject();
        $this->assertEquals(1, (int) $result->user_id);
    }
}
