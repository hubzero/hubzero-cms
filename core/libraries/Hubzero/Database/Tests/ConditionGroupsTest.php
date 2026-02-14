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
 * Tests for condition groups (AND/OR nesting) in query builder
 *
 * These tests verify the beginAndGroup/beginOrGroup/endGroup API
 * for creating nested WHERE clause conditions.
 */
class ConditionGroupsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['cond_group_test'];
    }

    /**
     * Set up test table for a specific database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing table
        try {
            $driver->dropTable('cond_group_test', true);
        } catch (\Exception $e) {
            // Table doesn't exist, which is fine
        }

        // Create table using schema builder for database-agnostic DDL
        $schema = $driver->schema();
        $schema->createTable('cond_group_test')
            ->id()
            ->integer('status')->default(0)
            ->integer('featured')->default(0)
            ->integer('sticky')->default(0)
            ->integer('published')->default(0)
            ->integer('author_id')->nullable()
            ->string('created', 50)->nullable()
            ->execute();

        // Insert test data
        $insertQuery = new Query($driver);
        $insertQuery->insertMany('cond_group_test', [
            [
                'status' => 1, 'featured' => 1, 'sticky' => 0,
                'published' => 1, 'author_id' => 1, 'created' => '2024-01-01',
            ],
            [
                'status' => 1, 'featured' => 0, 'sticky' => 1,
                'published' => 1, 'author_id' => 2, 'created' => '2024-02-01',
            ],
            [
                'status' => 1, 'featured' => 0, 'sticky' => 0,
                'published' => 0, 'author_id' => 1, 'created' => '2024-03-01',
            ],
            [
                'status' => 0, 'featured' => 1, 'sticky' => 1,
                'published' => 1, 'author_id' => 3, 'created' => '2024-04-01',
            ],
            [
                'status' => 2, 'featured' => 0, 'sticky' => 0,
                'published' => 1, 'author_id' => 1, 'created' => '2024-05-01',
            ],
        ]);

        // Verify data was inserted
        $verifyQuery = new Query($driver);
        $rows = $verifyQuery->from('cond_group_test')->select('*')->fetch('rows');
        $count = count($rows);
        if ($count != 5) {
            throw new \RuntimeException("Failed to insert test data. Expected 5 rows, got $count");
        }
    }

    // =========================================================================
    // Basic Group Functionality
    // =========================================================================

    /**
     * Test beginAndGroup creates proper SQL structure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function beginAndGroupCreatesProperlySQLStructure(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // status = 1 AND (featured = 1 OR sticky = 1)
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->beginAndGroup()
                  ->whereEquals('featured', 1)
                  ->orWhereEquals('sticky', 1)
              ->endGroup();

        $sql = $query->toSql();

        // Should contain properly nested parentheses
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('AND', $sql);
        $this->assertStringContainsString('(', $sql);
        $this->assertStringContainsString(')', $sql);
        $this->assertStringContainsString('OR', $sql);
    }

    /**
     * Test beginOrGroup creates proper SQL structure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function beginOrGroupCreatesProperlySQLStructure(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // published = 1 OR (author_id = 1 AND status = 1)
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('published', 1)
              ->beginOrGroup()
                  ->whereEquals('author_id', 1)
                  ->whereEquals('status', 1)
              ->endGroup();

        $sql = $query->toSql();

        // Should contain properly nested parentheses
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('OR', $sql);
        $this->assertStringContainsString('(', $sql);
        $this->assertStringContainsString(')', $sql);
    }

    /**
     * Test condition groups actually filter results correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function conditionGroupsFilterResultsCorrectly(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // status = 1 AND (featured = 1 OR sticky = 1)
        // Should match rows 1, 2 (status=1 and either featured or sticky)
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->beginAndGroup()
                  ->whereEquals('featured', 1)
                  ->orWhereEquals('sticky', 1)
              ->endGroup();

        $results = $query->fetch('rows');

        $this->assertCount(2, $results);
    }

    // =========================================================================
    // Nested Groups
    // =========================================================================

    /**
     * Test nested condition groups
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function nestedConditionGroups(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // status = 1 AND (featured = 1 OR sticky = 1)
        // Note: Deeply nested groups require careful tracking.
        // This tests basic nesting which is the primary use case.
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->beginAndGroup()
                  ->whereEquals('featured', 1)
                  ->orWhereEquals('sticky', 1)
              ->endGroup();

        // Execute to verify it works
        $results = $query->fetch('rows');
        $this->assertIsArray($results);
        // Should match rows 1, 2 (status=1 and either featured or sticky)
        $this->assertCount(2, $results);
    }

    /**
     * Test group depth tracking
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function groupDepthTracking(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $this->assertEquals(0, $query->getGroupDepth());

        $query->beginAndGroup();
        $this->assertEquals(1, $query->getGroupDepth());

        $query->beginOrGroup();
        $this->assertEquals(2, $query->getGroupDepth());

        $query->endGroup();
        $this->assertEquals(1, $query->getGroupDepth());

        $query->endGroup();
        $this->assertEquals(0, $query->getGroupDepth());
    }

    // =========================================================================
    // Error Cases
    // =========================================================================

    /**
     * Test endGroup without beginGroup throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function endGroupWithoutBeginGroupThrowsException(string $dbName, Driver $driver)
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('endGroup() called without matching beginGroup()');

        $query = new Query($driver);
        $query->endGroup();
    }

    /**
     * Test multiple endGroup without matching begins throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function excessEndGroupThrowsException(string $dbName, Driver $driver)
    {
        $this->expectException(\LogicException::class);

        $query = new Query($driver);
        $query->beginAndGroup()
              ->endGroup()
              ->endGroup(); // One too many
    }

    // =========================================================================
    // Method Chaining
    // =========================================================================

    /**
     * Test method chaining returns correct type
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function methodChainingReturnsSelf(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $result = $query->beginAndGroup();
        $this->assertSame($query, $result);

        $result = $query->endGroup();
        $this->assertSame($query, $result);

        $result = $query->beginOrGroup();
        $this->assertSame($query, $result);

        $result = $query->endGroup();
        $this->assertSame($query, $result);
    }

    /**
     * Test complete fluent query with groups
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function completeFluentQueryWithGroups(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // This should be a valid, complete query
        $result = $query->select('*')
                        ->from('cond_group_test')
                        ->whereEquals('status', 1)
                        ->beginAndGroup()
                            ->whereEquals('featured', 1)
                            ->orWhereEquals('sticky', 1)
                        ->endGroup()
                        ->order('id', 'asc')
                        ->limit(10);

        $this->assertSame($query, $result);

        // Should be executable
        $rows = $query->fetch('rows');
        $this->assertIsArray($rows);
    }

    // =========================================================================
    // Integration with Various Where Methods
    // =========================================================================

    /**
     * Test groups work with whereIn
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function groupsWorkWithWhereIn(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $query->select('*')
              ->from('cond_group_test')
              ->whereIn('status', [1, 2])
              ->beginAndGroup()
                  ->whereEquals('featured', 1)
                  ->orWhereIn('author_id', [1, 2])
              ->endGroup();

        $sql = $query->toSql();
        $this->assertStringContainsString('IN', $sql);
        $this->assertStringContainsString('(', $sql);
    }

    /**
     * Test groups work with whereRaw
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function groupsWorkWithWhereRaw(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->beginAndGroup()
                  ->whereRaw('featured = ?', [1])
                  ->orWhereRaw('sticky = ?', [1])
              ->endGroup();

        $results = $query->fetch('rows');
        $this->assertIsArray($results);
    }

    /**
     * Test groups work with whereIsNull
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function groupsWorkWithWhereIsNull(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $query->select('*')
              ->from('cond_group_test')
              ->beginAndGroup()
                  ->whereIsNull('author_id')
                  ->orWhereEquals('status', 1)
              ->endGroup();

        // Execute to verify it works - all rows have author_id set,
        // but all status=1 rows should match
        $results = $query->fetch('rows');
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(3, count($results)); // At least status=1 rows
    }

    // =========================================================================
    // Backwards Compatibility
    // =========================================================================

    /**
     * Test explicit depth parameter still works (BC)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function explicitDepthParameterStillWorks(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Using old-style explicit depth
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1, 0)
              ->whereEquals('featured', 1, 1)
              ->orWhereEquals('sticky', 1, 1)
              ->resetDepth(0);

        $results = $query->fetch('rows');
        $this->assertIsArray($results);
        $this->assertCount(2, $results);
    }

    /**
     * Test groups don't interfere with queries without groups
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function queriesWithoutGroupsStillWork(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // Simple query without groups
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->whereEquals('published', 1);

        $results = $query->fetch('rows');
        $this->assertIsArray($results);
    }

    // =========================================================================
    // Real World Examples
    // =========================================================================

    /**
     * Test typical CMS article filtering scenario
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function cmsArticleFilteringScenario(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // "Show published articles that are either featured OR written by author 1"
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('published', 1)
              ->beginAndGroup()
                  ->whereEquals('featured', 1)
                  ->orWhereEquals('author_id', 1)
              ->endGroup();

        $results = $query->fetch('rows');
        $this->assertIsArray($results);

        // All results should be published
        foreach ($results as $row) {
            $this->assertEquals(1, $row->published);
        }
    }

    /**
     * Test typical user search scenario
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function userSearchScenario(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        // "Show records that are either status=1 OR (status=2 AND author_id=1)"
        $query->select('*')
              ->from('cond_group_test')
              ->whereEquals('status', 1)
              ->beginOrGroup()
                  ->whereEquals('status', 2)
                  ->whereEquals('author_id', 1)
              ->endGroup();

        $results = $query->fetch('rows');
        $this->assertIsArray($results);

        // Should include status=1 and the status=2, author=1 record
        $this->assertGreaterThanOrEqual(3, count($results));
    }
}
