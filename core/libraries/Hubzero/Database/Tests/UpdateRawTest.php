<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;
use Hubzero\Database\Value\Raw;

/**
 * Tests for UPDATE queries with Raw value objects
 *
 * Raw values allow passing SQL expressions directly into queries
 * without parameter binding. These tests verify Raw values are executed
 * as SQL expressions rather than treated as literal strings.
 */
class UpdateRawTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     */
    protected static function getTestTables(): array
    {
        return ['update_raw_test'];
    }

    /**
     * Create test tables
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('update_raw_test', true);

        $schema = $driver->schema();
        $schema->createTable('update_raw_test')
            ->id()
            ->string('content', 255)
            ->integer('counter')->default(0)
            ->execute();
    }

    /**
     * Test Raw value executes SQL expression instead of literal string
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rawValueExecutesSqlExpression(string $dbName, Driver $driver)
    {
        // Setup table and data
        $this->setupTestTable($driver);
        $this->insertTestData($driver);

        // Execute UPDATE with Raw value using REPLACE() expression
        $query = new Query($driver);
        $query->update('update_raw_test')
              ->set(['content' => new Raw("REPLACE(content, 'foo', 'bar')")])
              ->whereEquals('id', 1);

        $query->execute();

        // Verify the Raw expression was executed (foo replaced with bar)
        $verifyQuery = new Query($driver);
        $result = $verifyQuery->from('update_raw_test')
                             ->select('content')
                             ->whereEquals('id', 1)
                             ->fetch('row');

        $this->assertEquals(
            'bar bar baz',
            $result->content,
            "Raw expression should execute REPLACE function, changing 'foo bar baz' to 'bar bar baz'"
        );

        // Cleanup
        $driver->dropTable('update_raw_test', true);
    }

    /**
     * Test multiple Raw values in same UPDATE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleRawValuesInSameUpdate(string $dbName, Driver $driver)
    {
        // Setup table and data
        $this->setupTestTable($driver);
        $this->insertTestData($driver);

        // Update multiple columns with Raw values
        $query = new Query($driver);
        $query->update('update_raw_test')
              ->set([
                  'content' => new Raw("REPLACE(content, 'baz', 'qux')"),
                  'counter' => new Raw('counter + 10')
              ])
              ->whereEquals('id', 2);

        $query->execute();

        // Verify both Raw expressions executed
        $verifyQuery = new Query($driver);
        $result = $verifyQuery->from('update_raw_test')
                             ->select('*')
                             ->whereEquals('id', 2)
                             ->fetch('row');

        $this->assertEquals(
            'hello world qux',
            $result->content,
            "First Raw expression should execute REPLACE function"
        );
        $this->assertEquals(
            30,
            $result->counter,
            "Second Raw expression should increment counter from 20 to 30"
        );

        // Cleanup
        $driver->dropTable('update_raw_test', true);
    }

    /**
     * Test mixing Raw values with regular bound values
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function mixingRawAndBoundValues(string $dbName, Driver $driver)
    {
        // Setup table and data
        $this->setupTestTable($driver);
        $this->insertTestData($driver);

        // Mix Raw expression with bound value
        $query = new Query($driver);
        $query->update('update_raw_test')
              ->set([
                  'content' => 'literal string',  // Bound parameter
                  'counter' => new Raw('counter + 5')  // Raw SQL expression
              ])
              ->whereEquals('id', 1);

        $query->execute();

        // Verify mixed values work correctly
        $verifyQuery = new Query($driver);
        $result = $verifyQuery->from('update_raw_test')
                             ->select('*')
                             ->whereEquals('id', 1)
                             ->fetch('row');

        $this->assertEquals(
            'literal string',
            $result->content,
            "Bound value should be treated as literal string"
        );
        $this->assertEquals(
            15,
            $result->counter,
            "Raw expression should increment counter from 10 to 15"
        );

        // Cleanup
        $driver->dropTable('update_raw_test', true);
    }

    /**
     * Test Raw value with WHERE clause
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rawValueWithWhereClause(string $dbName, Driver $driver)
    {
        // Setup table and data
        $this->setupTestTable($driver);
        $this->insertTestData($driver);

        // Update only rows matching WHERE condition
        $query = new Query($driver);
        $query->update('update_raw_test')
              ->set(['counter' => new Raw('counter * 2')])
              ->where('counter', '>', 15);

        $query->execute();

        // Verify only row 2 (counter=20) was updated
        $verifyQuery = new Query($driver);
        $results = $verifyQuery->from('update_raw_test')
                              ->select('*')
                              ->order('id', 'asc')
                              ->fetch('rows');

        $this->assertEquals(10, $results[0]->counter, "Row 1: counter=10 should be unchanged");
        $this->assertEquals(40, $results[1]->counter, "Row 2: counter=20 should be doubled to 40");

        // Cleanup
        $driver->dropTable('update_raw_test', true);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test table
     */
    private function setupTestTable(Driver $driver): void
    {
        $driver->dropTable('update_raw_test', true);

        $schema = $driver->schema();
        $schema->createTable('update_raw_test')
            ->id()
            ->string('content', 255)
            ->integer('counter')->default(0)
            ->execute();
    }

    /**
     * Insert test data
     */
    private function insertTestData(Driver $driver): void
    {
        $query = new Query($driver);
        $query->insertMany('update_raw_test', [
            ['content' => 'foo bar baz', 'counter' => 10],
            ['content' => 'hello world baz', 'counter' => 20],
        ]);
    }
}
