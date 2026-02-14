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
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\LogTestArticle;

/**
 * Query Logging and Debugging tests
 *
 * Tests for query debugging methods (toSql, toRawSql, dump, dd)
 * and global query logging features.
 */
class QueryLoggingTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['log_test_articles'];
    }

    /**
     * Create test tables
     *
     * @param Driver $driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('log_test_articles', true);

        // Create log_test_articles table
        $driver->createTable('log_test_articles')
            ->id()
            ->string('title', 255)->notNull()
            ->string('status', 50)->default('draft')
            ->integer('view_count')->default(0)
            ->datetime('created')->nullable()
            ->execute();
    }

    /**
     * Setup test data before each test
     *
     * @param Driver $driver
     */
    private function setupTestData(Driver $driver): void
    {
        // Set as default connection for models
        Relational::setDefaultConnection($driver);

        // Clear booted state
        LogTestArticle::clearBootedModels();

        // Purge query cache
        Query::purgeCache();

        // Clear query log
        $driver->flushQueryLog();
        $driver->disableQueryLog();

        // Clear table and reset auto-increment
        $driver->truncateTable('log_test_articles');

        // Insert test data
        $articles = [
            ['title' => 'Article 1', 'status' => 'published', 'view_count' => 100],
            ['title' => 'Article 2', 'status' => 'draft', 'view_count' => 50],
            ['title' => 'Article 3', 'status' => 'published', 'view_count' => 200],
        ];
        $query = new Query($driver);
        $query->insertMany('log_test_articles', $articles);
    }

    // =========================================================================
    // Query Parameterization Tests
    // =========================================================================

    /**
     * Test query uses parameterized bindings (not inlined values)
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function queryUsesParameterization(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('status', 'published');

        // Verify bindings exist (proves parameterization)
        $bindings = $query->getBindings();
        $this->assertNotEmpty($bindings);
        $this->assertContains('published', $bindings);

        // Verify the query executes and returns correct results
        $results = $query->fetch('rows');
        $this->assertCount(2, $results);
    }

    /**
     * Test model query returns correct results
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function modelQueryReturnsCorrectResults(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $results = LogTestArticle::all()
            ->whereEquals('status', 'published')
            ->rows();

        $this->assertCount(2, $results);
    }

    // =========================================================================
    // Query getBindings() Tests
    // =========================================================================

    /**
     * Test getBindings returns bound values
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getBindingsReturnsBoundValues(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('status', 'published')
            ->whereEquals('view_count', 100);

        $bindings = $query->getBindings();

        $this->assertIsArray($bindings);
        $this->assertCount(2, $bindings);
        $this->assertContains('published', $bindings);
        $this->assertContains(100, $bindings);
    }

    /**
     * Test getQueryBindings on model
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getQueryBindingsOnModel(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $bindings = LogTestArticle::all()
            ->whereEquals('status', 'draft')
            ->getQueryBindings();

        $this->assertIsArray($bindings);
        $this->assertContains('draft', $bindings);
    }

    // =========================================================================
    // Query toRawSql() Tests
    // =========================================================================

    /**
     * Test toRawSql substitutes values
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function toRawSqlSubstitutesValues(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $sql = $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('status', 'published')
            ->toRawSql();

        $this->assertStringContainsString('SELECT', $sql);
        $this->assertStringContainsString("'published'", $sql);
        $this->assertStringNotContainsString('?', $sql);
    }

    /**
     * Test toRawSql handles integers
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function toRawSqlHandlesIntegers(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $sql = $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('view_count', 100)
            ->toRawSql();

        $this->assertStringContainsString('100', $sql);
        // Should not have quotes around integer
        $this->assertStringNotContainsString("'100'", $sql);
    }

    /**
     * Test toRawSql handles null
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function toRawSqlHandlesNull(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $sql = $query->select('*')
            ->from('log_test_articles')
            ->whereIsNull('created')
            ->toRawSql();

        $this->assertStringContainsString('NULL', $sql);
    }

    /**
     * Test toRawSql on model
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function toRawSqlOnModel(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $sql = LogTestArticle::all()
            ->whereEquals('status', 'published')
            ->toRawSql();

        $this->assertStringContainsString("'published'", $sql);
    }

    // =========================================================================
    // Query getDebugInfo() Tests
    // =========================================================================

    /**
     * Test getDebugInfo returns all debug data
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getDebugInfoReturnsAllData(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('status', 'published');

        $debug = $query->getDebugInfo();

        $this->assertIsArray($debug);
        $this->assertArrayHasKey('sql', $debug);
        $this->assertArrayHasKey('bindings', $debug);
        $this->assertArrayHasKey('raw_sql', $debug);

        $this->assertStringContainsString('SELECT', $debug['sql']);
        $this->assertContains('published', $debug['bindings']);
        $this->assertStringContainsString("'published'", $debug['raw_sql']);
    }

    /**
     * Test getDebugInfo on model
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getDebugInfoOnModel(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $debug = LogTestArticle::all()
            ->whereEquals('status', 'draft')
            ->getDebugInfo();

        $this->assertArrayHasKey('sql', $debug);
        $this->assertArrayHasKey('bindings', $debug);
        $this->assertArrayHasKey('raw_sql', $debug);
    }

    // =========================================================================
    // Query dump() Tests
    // =========================================================================

    /**
     * Test dump returns $this for chaining
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function dumpReturnsSelfForChaining(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->select('*')->from('log_test_articles');

        // Capture output
        ob_start();
        $result = $query->dump();
        ob_end_clean();

        $this->assertSame($query, $result);
    }

    /**
     * Test dump outputs debug information
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function dumpOutputsDebugInfo(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->select('*')
            ->from('log_test_articles')
            ->whereEquals('status', 'published');

        ob_start();
        $query->dump();
        $output = ob_get_clean();

        $this->assertStringContainsString('SQL:', $output);
        $this->assertStringContainsString('Bindings:', $output);
        $this->assertStringContainsString('Raw SQL:', $output);
        $this->assertStringContainsString('published', $output);
    }

    /**
     * Test dump on model returns $this
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function dumpOnModelReturnsSelf(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $model = LogTestArticle::all()->whereEquals('status', 'published');

        ob_start();
        $result = $model->dump();
        ob_end_clean();

        $this->assertSame($model, $result);
    }

    // =========================================================================
    // Query Logging Tests
    // =========================================================================

    /**
     * Test enableQueryLog enables logging
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function enableQueryLogEnablesLogging(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $this->assertFalse($driver->isQueryLogEnabled());

        $driver->enableQueryLog();

        $this->assertTrue($driver->isQueryLogEnabled());
    }

    /**
     * Test disableQueryLog disables logging
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function disableQueryLogDisablesLogging(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();
        $driver->disableQueryLog();

        $this->assertFalse($driver->isQueryLogEnabled());
    }

    /**
     * Test queries are logged when enabled
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function queriesAreLoggedWhenEnabled(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();
        $driver->flushQueryLog();

        // Execute a query
        $driver->setQuery('SELECT * FROM log_test_articles WHERE status = ?')->bind(['published']);
        $driver->execute();

        $log = $driver->getQueryLog();

        $this->assertNotEmpty($log);
        $this->assertArrayHasKey('sql', $log[0]);
        $this->assertArrayHasKey('bindings', $log[0]);
        $this->assertArrayHasKey('time', $log[0]);
    }

    /**
     * Test queries are not logged when disabled
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function queriesNotLoggedWhenDisabled(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->disableQueryLog();
        $driver->flushQueryLog();

        // Execute a query
        $driver->setQuery('SELECT * FROM log_test_articles');
        $driver->execute();

        $log = $driver->getQueryLog();

        $this->assertEmpty($log);
    }

    /**
     * Test flushQueryLog clears the log
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function flushQueryLogClearsLog(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();

        // Execute some queries
        $driver->setQuery('SELECT * FROM log_test_articles');
        $driver->execute();

        $this->assertNotEmpty($driver->getQueryLog());

        $driver->flushQueryLog();

        $this->assertEmpty($driver->getQueryLog());
    }

    /**
     * Test getQueryLogCount returns correct count
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getQueryLogCountReturnsCorrectCount(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();
        $driver->flushQueryLog();

        $driver->setQuery('SELECT * FROM log_test_articles');
        $driver->execute();

        $driver->setQuery('SELECT * FROM log_test_articles WHERE id = 1');
        $driver->execute();

        $this->assertEquals(2, $driver->getQueryLogCount());
    }

    /**
     * Test getQueryLogTotalTime returns total time
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function getQueryLogTotalTimeReturnsTotalTime(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();
        $driver->flushQueryLog();

        $driver->setQuery('SELECT * FROM log_test_articles');
        $driver->execute();

        $totalTime = $driver->getQueryLogTotalTime();

        $this->assertIsFloat($totalTime);
        $this->assertGreaterThanOrEqual(0, $totalTime);
    }

    /**
     * Test query log contains timing information
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function queryLogContainsTimingInfo(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $driver->enableQueryLog();
        $driver->flushQueryLog();

        $driver->setQuery('SELECT * FROM log_test_articles');
        $driver->execute();

        $log = $driver->getQueryLog();

        $this->assertArrayHasKey('time', $log[0]);
        $this->assertIsFloat($log[0]['time']);
    }

    // =========================================================================
    // Query Clause Functional Tests
    // =========================================================================

    /**
     * Test WHERE LIKE filters results correctly
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function whereLikeFiltersResults(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $results = LogTestArticle::all()
            ->whereLike('title', '%Article%')
            ->rows();

        $this->assertCount(3, $results);

        $results = LogTestArticle::all()
            ->whereLike('title', '%1')
            ->rows();

        $this->assertCount(1, $results);
    }

    /**
     * Test ORDER BY returns results in correct order
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function orderByReturnsOrderedResults(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $results = LogTestArticle::all()
            ->order('view_count', 'desc')
            ->rows();

        $counts = [];
        foreach ($results as $row) {
            $counts[] = (int) $row->view_count;
        }

        $this->assertEquals([200, 100, 50], $counts);
    }

    /**
     * Test LIMIT restricts result count
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function limitRestrictsResultCount(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $results = LogTestArticle::all()
            ->limit(2)
            ->rows();

        $this->assertCount(2, $results);
    }

    /**
     * Test complex query with multiple clauses returns correct results
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function complexQueryReturnsCorrectResults(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $results = LogTestArticle::all()
            ->whereEquals('status', 'published')
            ->whereLike('title', '%Article%')
            ->order('view_count', 'desc')
            ->limit(1)
            ->rows();

        $this->assertCount(1, $results);
        $first = $results->first();
        $this->assertEquals(200, (int) $first->view_count);
    }
}
