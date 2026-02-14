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

/**
 * String function abstraction tests
 *
 * Tests SQL string functions (CONCAT, REPLACE, LOWER, UPPER) across
 * multiple database backends to ensure cross-database compatibility.
 */
class StringFunctionTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['string_func_test'];
    }

    /**
     * Create test tables (no-op -- table is created per-test in setupTable())
     *
     * @param Driver $driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Table is created fresh per test via setupTable()
        // Just ensure it's clean at setup time
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Setup test table with sample data
     */
    private function setupTable(Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('string_func_test', true);

        // Create table
        $driver->createTable('string_func_test')
            ->id()
            ->string('first_name', 50)
            ->string('last_name', 50)
            ->string('username', 50)
            ->string('code', 10)
            ->string('description', 100)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('string_func_test')
            ->values([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'JohnDoe',
                'code' => 'abc123',
                'description' => 'Hello World'
            ])
            ->execute();

        $query = new Query($driver);
        $query->insert('string_func_test')
            ->values([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'username' => 'JaneSmith',
                'code' => 'xyz789',
                'description' => 'Test String'
            ])
            ->execute();
    }

    // =========================================================================
    // String Function Tests
    // =========================================================================

    /**
     * Test CONCAT function concatenates strings correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function concatCombinesStrings(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Update using CONCAT to combine first and last name
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('username', $query->concat(['first_name', "' '", 'last_name']))
            ->execute();

        // Verify the concatenation worked
        $query = new Query($driver);
        $results = $query->select('*')
            ->from('string_func_test')
            ->order('id', 'asc')
            ->fetch();

        $this->assertEquals('John Doe', $results[0]->username);
        $this->assertEquals('Jane Smith', $results[1]->username);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test REPLACE function substitutes text correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function replaceSubstitutesText(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Replace "World" with "Universe" in text column
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('description', $query->replace('description', "'World'", "'Universe'"))
            ->where('id', '=', 1)
            ->execute();

        // Verify the replacement worked
        $query = new Query($driver);
        $result = $query->select('description')
            ->from('string_func_test')
            ->where('id', '=', 1)
            ->fetch('row');

        $this->assertEquals('Hello Universe', $result->description);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test LOWER function converts to lowercase
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function lowerConvertsToLowercase(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Convert username to lowercase
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('username', $query->lower('username'))
            ->execute();

        // Verify conversion
        $query = new Query($driver);
        $results = $query->select('username')
            ->from('string_func_test')
            ->order('id', 'asc')
            ->fetch();

        $this->assertEquals('johndoe', $results[0]->username);
        $this->assertEquals('janesmith', $results[1]->username);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test UPPER function converts to uppercase
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function upperConvertsToUppercase(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Convert code to uppercase
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('code', $query->upper('code'))
            ->execute();

        // Verify conversion
        $query = new Query($driver);
        $results = $query->select('code')
            ->from('string_func_test')
            ->order('id', 'asc')
            ->fetch();

        $this->assertEquals('ABC123', $results[0]->code);
        $this->assertEquals('XYZ789', $results[1]->code);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test multiple string functions in one query
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleStringFunctions(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Update multiple columns with different string functions
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('username', $query->lower('username'))
            ->setRaw('code', $query->upper('code'))
            ->where('id', '=', 1)
            ->execute();

        // Verify both transformations worked
        $query = new Query($driver);
        $result = $query->select('*')
            ->from('string_func_test')
            ->where('id', '=', 1)
            ->fetch('row');

        $this->assertEquals('johndoe', $result->username);
        $this->assertEquals('ABC123', $result->code);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test CONCAT with more than 2 arguments
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function concatMultipleArguments(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Concatenate multiple parts: "last_name, first_name (username)"
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('description', $query->concat(['last_name', "', '", 'first_name', "' ('", 'username', "')'"]))
            ->where('id', '=', 1)
            ->execute();

        // Verify the complex concatenation
        $query = new Query($driver);
        $result = $query->select('description')
            ->from('string_func_test')
            ->where('id', '=', 1)
            ->fetch('row');

        $this->assertEquals('Doe, John (JohnDoe)', $result->description);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test REPLACE with multiple replacements
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function replaceMultipleTimes(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // First replace "Hello" with "Goodbye"
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('description', $query->replace('description', "'Hello'", "'Goodbye'"))
            ->where('id', '=', 1)
            ->execute();

        // Then replace "World" with "Everyone"
        $query = new Query($driver);
        $query->update('string_func_test')
            ->setRaw('description', $query->replace('description', "'World'", "'Everyone'"))
            ->where('id', '=', 1)
            ->execute();

        // Verify both replacements
        $query = new Query($driver);
        $result = $query->select('description')
            ->from('string_func_test')
            ->where('id', '=', 1)
            ->fetch('row');

        $this->assertEquals('Goodbye Everyone', $result->description);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }

    /**
     * Test string functions in SELECT clause
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function stringFunctionsInSelect(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        // Select with string function transformations
        $query = new Query($driver);
        $query2 = new Query($driver);
        $result = $query->select($query2->concat(['first_name', "' '", 'last_name']), 'full_name')
            ->select($query2->upper('code'), 'upper_code')
            ->select($query2->lower('username'), 'lower_username')
            ->from('string_func_test')
            ->where('id', '=', 1)
            ->fetch('row');

        $this->assertEquals('John Doe', $result->full_name);
        $this->assertEquals('ABC123', $result->upper_code);
        $this->assertEquals('johndoe', $result->lower_username);

        // Clean up
        $driver->dropTable('string_func_test', true);
    }
}
