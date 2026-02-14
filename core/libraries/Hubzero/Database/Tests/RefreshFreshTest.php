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
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\RefreshTestItem;
use Hubzero\Database\Tests\TestModels\RefreshTestComment;

/**
 * Refresh and Fresh method tests
 *
 * Tests for the refresh() and fresh() methods that reload model data
 * from the database.
 */
class RefreshFreshTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup (children before parents)
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['refresh_test_comments', 'refresh_test_items'];
    }

    /**
     * Create test tables
     *
     * @param Driver $driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('refresh_test_comments', true);
        $driver->dropTable('refresh_test_items', true);

        $driver->createTable('refresh_test_items')
            ->id()
            ->string('title', 255)
            ->string('content', 1000)->nullable()
            ->integer('view_count')->default(0)
            ->integer('status')->default(1)
            ->string('created', 50)->nullable()
            ->string('modified', 50)->nullable()
            ->execute();

        $driver->createTable('refresh_test_comments')
            ->id()
            ->integer('item_id')
            ->string('body', 1000)
            ->execute();
    }

    /**
     * Reset test state for each test method
     */
    private function resetRefreshState(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();
        $driver->setQuery("DELETE FROM refresh_test_comments");
        $driver->execute();
        $driver->setQuery("DELETE FROM refresh_test_items");
        $driver->execute();
    }

    /**
     * Insert a test record and return its ID
     */
    protected function insertTestRecord(Driver $driver, $title = 'Test Item', $content = 'Test content', $viewCount = 0)
    {
        $now = date('Y-m-d H:i:s');
        $query = new Query($driver);
        $query->insertMany('refresh_test_items', [[
            'title' => $title,
            'content' => $content,
            'view_count' => $viewCount,
            'status' => 1,
            'created' => $now,
            'modified' => $now,
        ]]);
        return $driver->insertid();
    }

    /**
     * Insert a comment for an item
     */
    protected function insertComment(Driver $driver, $itemId, $body = 'Test comment')
    {
        $query = new Query($driver);
        $query->insertMany('refresh_test_comments', [
            ['item_id' => $itemId, 'body' => $body]
        ]);
        return $driver->insertid();
    }

    /**
     * Update a record directly in the database (simulating external change)
     */
    protected function updateRecordDirectly(Driver $driver, $id, $field, $value)
    {
        if (is_string($value)) {
            $value = "'" . str_replace("'", "''", $value) . "'";
        }
        $driver->setQuery("UPDATE refresh_test_items SET $field = $value WHERE id = $id");
        $driver->execute();
    }

    // =========================================================================
    // refresh() Tests
    // =========================================================================

    /**
     * Test that refresh() reloads data from database
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshReloadsFromDatabase(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Original Title');

        $item = RefreshTestItem::one($id);
        $this->assertEquals('Original Title', $item->get('title'));

        // Simulate external change
        $this->updateRecordDirectly($driver, $id, 'title', 'Changed By External Process');
        Query::purgeCache();

        // Refresh should pick up the change
        $result = $item->refresh();

        $this->assertSame($item, $result); // Returns $this
        $this->assertEquals('Changed By External Process', $item->get('title'));
    }

    /**
     * Test that refresh() discards local unsaved changes
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshDiscardsLocalChanges(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Original Title');

        $item = RefreshTestItem::one($id);
        $item->set('title', 'Local Unsaved Change');
        $item->set('content', 'Modified Content');

        $this->assertTrue($item->isDirty());

        $item->refresh();

        $this->assertEquals('Original Title', $item->get('title'));
        $this->assertEquals('Test content', $item->get('content'));
        $this->assertFalse($item->isDirty());
    }

    /**
     * Test that refresh() clears cached relationships
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshClearsCachedRelationships(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $this->insertComment($driver, $id, 'Original Comment');

        $item = RefreshTestItem::one($id);

        // Access relationship to cache it
        $comments = $item->comments;
        $this->assertCount(1, $comments);

        // Add another comment externally
        $this->insertComment($driver, $id, 'New Comment');
        Query::purgeCache();

        // Refresh should clear the cached relationship
        $item->refresh();

        // Now accessing comments should show both
        $comments = $item->comments;
        $this->assertCount(2, $comments);
    }

    /**
     * Test that refresh() returns false for new (unsaved) models
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshReturnsFalseForNewModel(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $item = RefreshTestItem::blank();
        $item->set('title', 'New Item');

        $result = $item->refresh();

        $this->assertFalse($result);
    }

    /**
     * Test that refresh() returns false if record no longer exists
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshReturnsFalseIfRecordDeleted(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $item = RefreshTestItem::one($id);

        // Delete the record externally
        $driver->setQuery("DELETE FROM refresh_test_items WHERE id = $id");
        $driver->execute();
        Query::purgeCache();

        $result = $item->refresh();

        $this->assertFalse($result);
    }

    /**
     * Test that refresh() syncs original attributes (no dirty state)
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshSyncsOriginalAttributes(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Original Title');

        $item = RefreshTestItem::one($id);

        // Simulate external change
        $this->updateRecordDirectly($driver, $id, 'title', 'External Change');
        Query::purgeCache();

        $item->refresh();

        // After refresh, model should not be dirty
        $this->assertFalse($item->isDirty());
        $this->assertEquals('External Change', $item->getOriginal('title'));
    }

    // =========================================================================
    // fresh() Tests
    // =========================================================================

    /**
     * Test that fresh() returns a new instance
     */
    #[DataProvider('databaseProvider')]
    public function testFreshReturnsNewInstance(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = RefreshTestItem::one($id);
        $fresh = $item->fresh();

        $this->assertNotSame($item, $fresh);
        $this->assertInstanceOf(RefreshTestItem::class, $fresh);
        $this->assertEquals($item->get('id'), $fresh->get('id'));
    }

    /**
     * Test that fresh() leaves original instance unchanged
     */
    #[DataProvider('databaseProvider')]
    public function testFreshLeavesOriginalUnchanged(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Original Title');

        $item = RefreshTestItem::one($id);
        $item->set('title', 'Local Change');

        // Simulate external change
        $this->updateRecordDirectly($driver, $id, 'content', 'External Content Change');
        Query::purgeCache();

        $fresh = $item->fresh();

        // Original should still have local change
        $this->assertEquals('Local Change', $item->get('title'));

        // Fresh copy should have database values
        $this->assertEquals('Original Title', $fresh->get('title'));
        $this->assertEquals('External Content Change', $fresh->get('content'));
    }

    /**
     * Test that fresh() returns false for new (unsaved) models
     */
    #[DataProvider('databaseProvider')]
    public function testFreshReturnsFalseForNewModel(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $item = RefreshTestItem::blank();
        $item->set('title', 'New Item');

        $result = $item->fresh();

        $this->assertFalse($result);
    }

    /**
     * Test that fresh() returns false if record no longer exists
     */
    #[DataProvider('databaseProvider')]
    public function testFreshReturnsFalseIfRecordDeleted(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $item = RefreshTestItem::one($id);

        // Delete the record externally
        $driver->setQuery("DELETE FROM refresh_test_items WHERE id = $id");
        $driver->execute();
        Query::purgeCache();

        $result = $item->fresh();

        $this->assertFalse($result);
    }

    /**
     * Test that fresh() can eager load relationships
     */
    #[DataProvider('databaseProvider')]
    public function testFreshWithEagerLoading(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $this->insertComment($driver, $id, 'Comment 1');
        $this->insertComment($driver, $id, 'Comment 2');

        $item = RefreshTestItem::one($id);

        // Get fresh with eager loaded comments
        $fresh = $item->fresh(['comments']);

        $this->assertInstanceOf(RefreshTestItem::class, $fresh);
        // The relationship should be loaded
        $this->assertCount(2, $fresh->comments);
    }

    /**
     * Test that fresh() accepts string for single relationship
     */
    #[DataProvider('databaseProvider')]
    public function testFreshAcceptsStringForSingleRelationship(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $this->insertComment($driver, $id, 'Comment');

        $item = RefreshTestItem::one($id);

        // Get fresh with string (not array)
        $fresh = $item->fresh('comments');

        $this->assertInstanceOf(RefreshTestItem::class, $fresh);
        $this->assertCount(1, $fresh->comments);
    }

    /**
     * Test that fresh() returns model with no dirty state
     */
    #[DataProvider('databaseProvider')]
    public function testFreshReturnsCleanModel(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = RefreshTestItem::one($id);
        $fresh = $item->fresh();

        $this->assertFalse($fresh->isDirty());
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test refresh and fresh with numeric values
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshWithNumericValues(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item', 'Content', 100);

        $item = RefreshTestItem::one($id);
        $item->set('view_count', 999);

        // External change
        $this->updateRecordDirectly($driver, $id, 'view_count', 500);
        Query::purgeCache();

        $item->refresh();

        $this->assertEquals(500, $item->get('view_count'));
    }

    /**
     * Test multiple consecutive refreshes
     */
    #[DataProvider('databaseProvider')]
    public function testMultipleRefreshes(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Title 1');
        $item = RefreshTestItem::one($id);

        $this->updateRecordDirectly($driver, $id, 'title', 'Title 2');
        Query::purgeCache();
        $item->refresh();
        $this->assertEquals('Title 2', $item->get('title'));

        $this->updateRecordDirectly($driver, $id, 'title', 'Title 3');
        Query::purgeCache();
        $item->refresh();
        $this->assertEquals('Title 3', $item->get('title'));
    }

    /**
     * Test that refresh works correctly with chaining
     */
    #[DataProvider('databaseProvider')]
    public function testRefreshIsChainable(string $dbName, Driver $driver): void
    {
        $this->resetRefreshState($driver);

        $id = $this->insertTestRecord($driver, 'Test Item');
        $item = RefreshTestItem::one($id);

        // Should be able to chain after refresh
        $title = $item->refresh()->get('title');

        $this->assertEquals('Test Item', $title);
    }
}
