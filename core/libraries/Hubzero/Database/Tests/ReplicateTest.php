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
use Hubzero\Database\Tests\TestModels\ReplicateTestItem;

/**
 * Replicate method tests
 *
 * Tests for the replicate() method that creates a copy of a model
 * without the primary key for saving as a new record.
 */
class ReplicateTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['replicate_test_items'];
    }

    /**
     * Create test tables
     *
     * @param Driver $driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('replicate_test_items', true);

        $driver->createTable('replicate_test_items')
            ->id()
            ->string('title', 255)
            ->string('content', 1000)->nullable()
            ->string('slug', 255)->nullable()
            ->integer('status')->default(1)
            ->integer('view_count')->default(0)
            ->string('created', 50)->nullable()
            ->string('created_at', 50)->nullable()
            ->string('modified', 50)->nullable()
            ->string('modified_at', 50)->nullable()
            ->string('updated_at', 50)->nullable()
            ->string('published_at', 50)->nullable()
            ->integer('created_by')->nullable()
            ->execute();
    }

    /**
     * Reset test state for each test method
     */
    private function resetReplicateState(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();
        $driver->setQuery("DELETE FROM replicate_test_items");
        $driver->execute();
    }

    /**
     * Insert a test record and return its ID
     */
    protected function insertTestRecord(Driver $driver, $data = [])
    {
        $defaults = [
            'title' => 'Test Item',
            'content' => 'Test content',
            'slug' => 'test-item',
            'status' => 1,
            'view_count' => 100,
            'created' => '2024-01-15 10:00:00',
            'created_at' => '2024-01-15 10:00:00',
            'modified' => '2024-01-20 14:30:00',
            'modified_at' => '2024-01-20 14:30:00',
            'updated_at' => '2024-01-20 14:30:00',
            'published_at' => '2024-01-16 00:00:00',
            'created_by' => 42,
        ];
        $data = array_merge($defaults, $data);
        $query = new Query($driver);
        $query->insertMany('replicate_test_items', [$data]);
        return $driver->insertid();
    }

    // =========================================================================
    // Basic Replication Tests
    // =========================================================================

    /**
     * Test that replicate() returns a new instance
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateReturnsNewInstance(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();

        $this->assertNotSame($original, $copy);
        $this->assertInstanceOf(ReplicateTestItem::class, $copy);
    }

    /**
     * Test that replicate() removes the primary key
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateRemovesPrimaryKey(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $this->assertEquals($id, $original->get('id'));

        $copy = $original->replicate();

        $this->assertNull($copy->get('id'));
    }

    /**
     * Test that replicate() copies regular attributes
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateCopiesAttributes(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, [
            'title' => 'Original Title',
            'content' => 'Original Content',
            'status' => 1,
            'view_count' => 500,
        ]);

        $original = ReplicateTestItem::one($id);
        $copy = $original->replicate();

        $this->assertEquals('Original Title', $copy->get('title'));
        $this->assertEquals('Original Content', $copy->get('content'));
        $this->assertEquals(1, $copy->get('status'));
        $this->assertEquals(500, $copy->get('view_count'));
    }

    /**
     * Test that replicate() removes default timestamp columns
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateRemovesTimestamps(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();

        // Default excluded timestamp columns
        $this->assertNull($copy->get('created'));
        $this->assertNull($copy->get('created_at'));
        $this->assertNull($copy->get('modified'));
        $this->assertNull($copy->get('modified_at'));
        $this->assertNull($copy->get('updated_at'));
    }

    /**
     * Test that replicate() keeps non-excluded attributes
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateKeepsNonExcludedAttributes(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, [
            'published_at' => '2024-06-01 00:00:00',
            'created_by' => 99,
        ]);

        $original = ReplicateTestItem::one($id);
        $copy = $original->replicate();

        // These should NOT be excluded by default
        $this->assertEquals('2024-06-01 00:00:00', $copy->get('published_at'));
        $this->assertEquals(99, $copy->get('created_by'));
    }

    // =========================================================================
    // Custom Exclusion Tests
    // =========================================================================

    /**
     * Test that replicate() accepts custom exclusions
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateWithCustomExclusions(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, [
            'slug' => 'unique-slug',
            'published_at' => '2024-06-01 00:00:00',
        ]);

        $original = ReplicateTestItem::one($id);
        $copy = $original->replicate(['slug', 'published_at']);

        // Custom exclusions should be null
        $this->assertNull($copy->get('slug'));
        $this->assertNull($copy->get('published_at'));

        // Other attributes should still be copied
        $this->assertEquals($original->get('title'), $copy->get('title'));
        $this->assertEquals($original->get('content'), $copy->get('content'));
    }

    /**
     * Test that replicate() merges custom exclusions with defaults
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateMergesExclusionsWithDefaults(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate(['slug']);

        // Default exclusions should still apply
        $this->assertNull($copy->get('id'));
        $this->assertNull($copy->get('created'));

        // Custom exclusion should also apply
        $this->assertNull($copy->get('slug'));
    }

    /**
     * Test replicate with empty exclusion array
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateWithEmptyExclusionArray(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate([]);

        // Should still exclude defaults
        $this->assertNull($copy->get('id'));
        $this->assertNull($copy->get('created'));
    }

    // =========================================================================
    // Save Behavior Tests
    // =========================================================================

    /**
     * Test that replicated model can be saved as new record
     */
    #[DataProvider('databaseProvider')]
    public function testReplicatedModelCanBeSaved(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['title' => 'Original']);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();
        $copy->set('title', 'Copy of Original');
        $result = $copy->save();

        // save() returns insert ID for new records (truthy on success)
        $this->assertNotFalse($result);
        $this->assertNotNull($copy->get('id'));
        $this->assertNotEquals($original->get('id'), $copy->get('id'));
    }

    /**
     * Test that saving replicate creates distinct database records
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateCreatesSeparateRecords(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['title' => 'Original', 'content' => 'Content']);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();
        $copy->set('title', 'Copy');
        $copy->save();

        // Count records
        $driver->setQuery('SELECT COUNT(*) as cnt FROM replicate_test_items');
        $row = $driver->loadObject();
        $this->assertEquals(2, $row->cnt);

        // Verify both exist with correct titles
        Query::purgeCache();
        $reloadedOriginal = ReplicateTestItem::one($original->get('id'));
        $reloadedCopy = ReplicateTestItem::one($copy->get('id'));

        $this->assertEquals('Original', $reloadedOriginal->get('title'));
        $this->assertEquals('Copy', $reloadedCopy->get('title'));
    }

    /**
     * Test replicated model is marked as new (isNew)
     */
    #[DataProvider('databaseProvider')]
    public function testReplicatedModelIsNew(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver);
        $original = ReplicateTestItem::one($id);

        $this->assertFalse($original->isNew());

        $copy = $original->replicate();

        $this->assertTrue($copy->isNew());
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test replicate on a new (unsaved) model
     */
    #[DataProvider('databaseProvider')]
    public function testReplicateOnNewModel(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $model = ReplicateTestItem::blank();
        $model->set('title', 'Unsaved Item');
        $model->set('content', 'Some content');

        $copy = $model->replicate();

        $this->assertEquals('Unsaved Item', $copy->get('title'));
        $this->assertEquals('Some content', $copy->get('content'));
        $this->assertTrue($copy->isNew());
    }

    /**
     * Test replicate preserves null values
     */
    #[DataProvider('databaseProvider')]
    public function testReplicatePreservesNullValues(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['content' => null]);
        $original = ReplicateTestItem::one($id);

        $this->assertNull($original->get('content'));

        $copy = $original->replicate();

        // Null should be preserved (not excluded)
        $this->assertNull($copy->get('content'));
    }

    /**
     * Test replicate with integer zero value
     */
    #[DataProvider('databaseProvider')]
    public function testReplicatePreservesZeroValues(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['view_count' => 0, 'status' => 0]);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();

        $this->assertEquals(0, $copy->get('view_count'));
        $this->assertEquals(0, $copy->get('status'));
    }

    /**
     * Test multiple replicates from same original
     */
    #[DataProvider('databaseProvider')]
    public function testMultipleReplicates(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['title' => 'Original']);
        $original = ReplicateTestItem::one($id);

        $copy1 = $original->replicate();
        $copy2 = $original->replicate();
        $copy3 = $original->replicate();

        // All copies should be distinct instances
        $this->assertNotSame($copy1, $copy2);
        $this->assertNotSame($copy2, $copy3);

        // All should have the same copied data
        $this->assertEquals('Original', $copy1->get('title'));
        $this->assertEquals('Original', $copy2->get('title'));
        $this->assertEquals('Original', $copy3->get('title'));

        // All should be saveable as new records
        $copy1->set('title', 'Copy 1');
        $copy1->save();

        $copy2->set('title', 'Copy 2');
        $copy2->save();

        $copy3->set('title', 'Copy 3');
        $copy3->save();

        // Should have 4 records total
        $driver->setQuery('SELECT COUNT(*) as cnt FROM replicate_test_items');
        $row = $driver->loadObject();
        $this->assertEquals(4, $row->cnt);
    }

    /**
     * Test that modifying copy doesn't affect original
     */
    #[DataProvider('databaseProvider')]
    public function testModifyingCopyDoesNotAffectOriginal(string $dbName, Driver $driver): void
    {
        $this->resetReplicateState($driver);

        $id = $this->insertTestRecord($driver, ['title' => 'Original Title']);
        $original = ReplicateTestItem::one($id);

        $copy = $original->replicate();
        $copy->set('title', 'Modified Copy Title');
        $copy->set('content', 'New Content');

        // Original should be unchanged
        $this->assertEquals('Original Title', $original->get('title'));
        $this->assertEquals('Test content', $original->get('content'));
    }
}
