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

/**
 * Named parameter tests
 *
 * Tests for setParameter(), setParameters(), and named placeholder support
 * in whereRaw(), selectRaw(), and havingRaw().
 *
 * Category A tests check the Query API (setParameter, hasParameter, etc.).
 * Category B tests execute queries against real databases and verify results.
 */
class NamedParametersTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['np_posts', 'np_users'];
    }

    /**
     * Get the CAST type keyword for integer on the given driver
     *
     * This is provided by the driver capability layer.
     */
    private function integerCastType(Driver $driver): string
    {
        return $driver->getIntegerCastKeyword();
    }

    /**
     * Set up test tables for a specific database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        try {
            $driver->dropTable('np_posts', true);
        } catch (\Exception $e) {
            // Table doesn't exist
        }

        try {
            $driver->dropTable('np_users', true);
        } catch (\Exception $e) {
            // Table doesn't exist
        }

        $schema = $driver->schema();
        $schema->createTable('np_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->integer('active')->default(1)
            ->integer('user_id')->nullable()
            ->execute();

        $schema->createTable('np_posts')
            ->id()
            ->integer('user_id')
            ->string('title', 255)
            ->execute();
    }

    /**
     * Reset test data before each functional test
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function resetTestState(Driver $driver): void
    {
        Query::purgeCache();

        $driver->truncateTable('np_posts');
        $driver->truncateTable('np_users');

        $query = new Query($driver);
        $query->insertMany('np_users', [
            ['name' => 'Test User', 'email' => 'test@test.com', 'active' => 1, 'user_id' => 1],
            ['name' => 'Admin', 'email' => 'admin@example.com', 'active' => 1, 'user_id' => 2],
            ['name' => 'Inactive User', 'email' => 'inactive@example.com', 'active' => 0, 'user_id' => 3],
        ]);

        $query2 = new Query($driver);
        $query2->insertMany('np_posts', [
            ['user_id' => 1, 'title' => 'Post A by User 1'],
            ['user_id' => 1, 'title' => 'Post B by User 1'],
            ['user_id' => 2, 'title' => 'Post C by User 2'],
        ]);
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
    // Category A: setParameter() / setParameters() Basic API Tests
    // =========================================================================

    /**
     * Test that setParameter() stores a value
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSetParameterStoresValue(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameter('name', 'Test User');

        $params = $query->getNamedParameters();
        $this->assertArrayHasKey('name', $params);
        $this->assertEquals('Test User', $params['name']);
    }

    /**
     * Test that setParameter() strips leading colon from name
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSetParameterStripsColon(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameter(':name', 'Test User');

        $params = $query->getNamedParameters();
        $this->assertArrayHasKey('name', $params);
        $this->assertArrayNotHasKey(':name', $params);
    }

    /**
     * Test that setParameter() returns self for chaining
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSetParameterReturnsChain(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $result = $query->setParameter('name', 'Test User');

        $this->assertSame($query, $result);
    }

    /**
     * Test that setParameters() sets multiple values
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSetParametersSetsMultipleValues(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameters([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@test.com'
        ]);

        $params = $query->getNamedParameters();
        $this->assertCount(3, $params);
        $this->assertEquals(1, $params['id']);
        $this->assertEquals('Test User', $params['name']);
        $this->assertEquals('test@test.com', $params['email']);
    }

    /**
     * Test that hasParameter() returns true for existing params
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHasParameterReturnsTrueForExisting(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameter('name', 'Test User');

        $this->assertTrue($query->hasParameter('name'));
        $this->assertTrue($query->hasParameter(':name')); // With colon
    }

    /**
     * Test that hasParameter() returns false for non-existing params
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHasParameterReturnsFalseForNonExisting(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $this->assertFalse($query->hasParameter('name'));
    }

    /**
     * Test that clearParameters() removes all parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testClearParametersRemovesAll(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameters(['name' => 'Test', 'email' => 'test@test.com']);
        $query->clearParameters();

        $this->assertEmpty($query->getNamedParameters());
        $this->assertFalse($query->hasParameter('name'));
    }

    /**
     * Test that getNamedParameters() returns empty array initially
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testGetNamedParametersEmptyInitially(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $this->assertIsArray($query->getNamedParameters());
        $this->assertEmpty($query->getNamedParameters());
    }

    /**
     * Test that missing named parameter throws exception
     *
     * The exception is thrown when conversion is attempted (i.e., when we have
     * named placeholders and either inline params or stored params trigger conversion).
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testMissingNamedParameterThrowsException(string $dbName, Driver $driver): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing named parameter ':other'");

        $query = new Query($driver);

        // This triggers conversion because we have named inline params,
        // but :other is not in the params array
        $query->select('*')
              ->from('np_users')
              ->whereRaw('name = :name AND email = :other', ['name' => 'Test']); // Missing :other
    }

    /**
     * Test that setParameters() can be called multiple times
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testMultipleSetParametersCalls(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameters(['id' => 1])
              ->setParameters(['name' => 'Test User']); // Adds to existing

        $params = $query->getNamedParameters();
        $this->assertCount(2, $params);
        $this->assertEquals(1, $params['id']);
        $this->assertEquals('Test User', $params['name']);
    }

    /**
     * Test that setParameter() overwrites existing parameter
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSetParameterOverwritesExisting(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameter('id', 1)
              ->setParameter('id', 2);

        $params = $query->getNamedParameters();
        $this->assertEquals(2, $params['id']);
    }

    /**
     * Test named parameters with various data types
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testNamedParametersWithVariousTypes(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->setParameters([
            'int_val' => 42,
            'float_val' => 3.14,
            'string_val' => 'test',
            'null_val' => null,
            'bool_true' => true,
            'bool_false' => false
        ]);

        $params = $query->getNamedParameters();

        $this->assertSame(42, $params['int_val']);
        $this->assertSame(3.14, $params['float_val']);
        $this->assertSame('test', $params['string_val']);
        $this->assertNull($params['null_val']);
        $this->assertTrue($params['bool_true']);
        $this->assertFalse($params['bool_false']);
    }

    /**
     * Test that named parameters work with getBindings()
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testNamedParametersInGetBindings(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $query->select('*')
              ->from('np_users')
              ->whereRaw('name = :name AND email = :email', [
                  'name' => 'Test User',
                  'email' => 'test@test.com'
              ]);

        $bindings = $query->getBindings();

        // Bindings should contain the values in order of appearance
        $this->assertCount(2, $bindings);
        $this->assertEquals('Test User', $bindings[0]);
        $this->assertEquals('test@test.com', $bindings[1]);
    }

    // =========================================================================
    // Category B: Functional Tests - whereRaw() with Named Parameters
    // =========================================================================

    /**
     * Test whereRaw() with inline named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testWhereRawWithInlineNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
              ->from('np_users')
              ->whereRaw('name = :name AND email = :email', [
                  'name' => 'Test User',
                  'email' => 'test@test.com'
              ])
              ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Test User', $results[0]->name);
        $this->assertEquals('test@test.com', $results[0]->email);
    }

    /**
     * Test whereRaw() with stored named parameters via setParameter()
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testWhereRawWithStoredParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->setParameter('name', 'Test User')
              ->setParameter('email', 'test@test.com')
              ->select('*')
              ->from('np_users')
              ->whereRaw('name = :name AND email = :email')
              ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Test User', $results[0]->name);
        $this->assertEquals('test@test.com', $results[0]->email);
    }

    /**
     * Test that inline parameters override stored parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testInlineParametersOverrideStored(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        // Set name to 'Test User' via stored param
        $results = $query->setParameter('name', 'Test User')
              ->select('*')
              ->from('np_users')
              // Override with 'Admin' via inline
              ->whereRaw('name = :name', ['name' => 'Admin'])
              ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Admin', $results[0]->name);
    }

    /**
     * Test whereRaw() with reused named parameter
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testWhereRawWithReusedParameter(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
              ->from('np_posts')
              ->whereRaw('user_id = :uid OR user_id = :uid', ['uid' => 1])
              ->fetch('rows');

        // user_id = 1 has 2 posts
        $this->assertCount(2, $results);
    }

    /**
     * Test orWhereRaw() with named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testOrWhereRawWithNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
              ->from('np_users')
              ->whereEquals('id', 1)
              ->orWhereRaw('name = :name', ['name' => 'Admin'])
              ->fetch('rows');

        // id=1 (Test User) OR name='Admin' => 2 results
        $this->assertCount(2, $results);
    }

    /**
     * Test that positional parameters still work (BC)
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testPositionalParametersStillWork(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('*')
              ->from('np_users')
              ->whereRaw('name = ? AND email = ?', ['Test User', 'test@test.com'])
              ->fetch('rows');

        $this->assertCount(1, $results);
        $this->assertEquals('Test User', $results[0]->name);
        $this->assertEquals('test@test.com', $results[0]->email);
    }

    // =========================================================================
    // Category B: Functional Tests - selectRaw() with Named Parameters
    // =========================================================================

    /**
     * Test selectRaw() with inline named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSelectRawWithInlineNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $cast = $this->integerCastType($driver);
        $query = new Query($driver);
        // Named parameter in selectRaw arithmetic expression (CAST keyword is driver-specific)
        $results = $query->selectRaw("id + CAST(:offset AS $cast)", ['offset' => 100])
              ->from('np_users')
              ->fetch('rows');

        $this->assertCount(3, $results);

        // Each row should have the id + 100 in the first column
        // The column name varies by database, so just check values exist
        $firstRow = (array) $results[0];
        $values = array_values($firstRow);
        // The computed column should be 101 for id=1
        $this->assertContains(101, array_map('intval', $values));
    }

    /**
     * Test selectRaw() with stored named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSelectRawWithStoredParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $cast = $this->integerCastType($driver);
        $query = new Query($driver);
        // Named parameter in selectRaw arithmetic expression (CAST keyword is driver-specific)
        $results = $query->setParameter('offset', 100)
              ->selectRaw("id + CAST(:offset AS $cast)")
              ->from('np_users')
              ->fetch('rows');

        $this->assertCount(3, $results);

        // Verify the computed value is present
        $firstRow = (array) $results[0];
        $values = array_values($firstRow);
        $this->assertContains(101, array_map('intval', $values));
    }

    /**
     * Test selectRaw() with no parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSelectRawWithNoParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->selectRaw('COUNT(*)')
              ->from('np_users')
              ->fetch('rows');

        $this->assertCount(1, $results);

        // The count should be 3 (three seeded users)
        $firstRow = (array) $results[0];
        $values = array_values($firstRow);
        $this->assertEquals(3, (int) $values[0]);
    }

    /**
     * Test selectRaw() with positional parameters (BC)
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSelectRawWithPositionalParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->selectRaw('COALESCE(id, ?)', [0])
              ->from('np_users')
              ->fetch('rows');

        $this->assertCount(3, $results);

        // COALESCE(id, 0) should return the id since id is not null
        $firstRow = (array) $results[0];
        $values = array_values($firstRow);
        $this->assertEquals(1, (int) $values[0]);
    }

    // =========================================================================
    // Category B: Functional Tests - havingRaw() with Named Parameters
    // =========================================================================

    /**
     * Test havingRaw() with inline named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHavingRawWithInlineNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('user_id')
              ->selectRaw('COUNT(*)', [], 'cnt')
              ->from('np_posts')
              ->group('user_id')
              ->havingRaw('COUNT(*) >= :min_count', ['min_count' => 2])
              ->fetch('rows');

        // Only user_id=1 has 2 posts, user_id=2 has 1 post
        $this->assertCount(1, $results);
        $this->assertEquals(1, (int) $results[0]->user_id);
    }

    /**
     * Test havingRaw() with stored named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHavingRawWithStoredParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->setParameter('min_count', 2)
              ->select('user_id')
              ->selectRaw('COUNT(*)', [], 'cnt')
              ->from('np_posts')
              ->group('user_id')
              ->havingRaw('COUNT(*) >= :min_count')
              ->fetch('rows');

        // Only user_id=1 has 2 posts
        $this->assertCount(1, $results);
        $this->assertEquals(1, (int) $results[0]->user_id);
    }

    /**
     * Test havingRaw() with positional parameters (BC)
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHavingRawWithPositionalParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('user_id')
              ->selectRaw('COUNT(*)', [], 'cnt')
              ->from('np_posts')
              ->group('user_id')
              ->havingRaw('COUNT(*) >= ?', [2])
              ->fetch('rows');

        // Only user_id=1 has 2 posts
        $this->assertCount(1, $results);
        $this->assertEquals(1, (int) $results[0]->user_id);
    }

    /**
     * Test orHavingRaw() with named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testOrHavingRawWithNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $query = new Query($driver);
        $results = $query->select('user_id')
              ->selectRaw('COUNT(*)', [], 'cnt')
              ->from('np_posts')
              ->group('user_id')
              ->havingRaw('COUNT(*) >= :min', ['min' => 1])
              ->orHavingRaw('COUNT(*) < :max', ['max' => 10])
              ->fetch('rows');

        // Both groups (user_id=1 with 2 posts, user_id=2 with 1 post)
        // match COUNT(*) >= 1 OR COUNT(*) < 10
        $this->assertCount(2, $results);
    }

    // =========================================================================
    // Category B: Complex Scenarios
    // =========================================================================

    /**
     * Test chaining multiple methods with named parameters
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testChainingMultipleMethodsWithNamedParameters(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $cast = $this->integerCastType($driver);
        $query = new Query($driver);
        // Named parameter in selectRaw arithmetic expression (CAST keyword is driver-specific)
        $results = $query->setParameter('offset', 100)
              ->setParameter('user_id', 1)
              ->setParameter('name', 'Test User')
              ->selectRaw("id + CAST(:offset AS $cast)")
              ->from('np_users')
              ->whereRaw('id = :user_id')
              ->whereRaw('name = :name')
              ->fetch('rows');

        // Should match exactly 1 user (id=1, name='Test User')
        $this->assertCount(1, $results);

        // The computed column (id + 100) should be 101
        $firstRow = (array) $results[0];
        $values = array_values($firstRow);
        $this->assertEquals(101, (int) $values[0]);
    }
}
