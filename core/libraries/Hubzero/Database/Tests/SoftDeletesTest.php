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
use Hubzero\Database\Tests\TestModels\EventStub;
use Hubzero\Database\Tests\TestModels\SoftDeletePost;
use Hubzero\Database\Tests\TestModels\CustomDeleteColumnPost;
use Hubzero\Database\Tests\TestModels\NonSoftDeletePost;

/**
 * Soft Deletes tests
 *
 * Tests for the SoftDeletes trait that allows records to be marked as deleted
 * via a timestamp rather than being physically removed from the database.
 */
class SoftDeletesTest extends AbstractDriverTestCase
{
    /**
     * Register the Event stub class alias if needed.
     * Called once before the first test in this class.
     */
    public static function setUpBeforeClass(): void
    {
        if (!class_exists('Event')) {
            class_alias(EventStub::class, 'Event');
        }

        parent::setUpBeforeClass();
    }

    /**
     * Return table names for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['soft_delete_posts', 'soft_delete_custom_posts'];
    }

    /**
     * Create test tables
     *
     * @param Driver $driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop tables if they exist
        $driver->dropTable('soft_delete_posts', true);
        $driver->dropTable('soft_delete_custom_posts', true);

        // Create main test table
        $driver->createTable('soft_delete_posts')
            ->id()
            ->string('title', 255)
            ->text('content')
            ->integer('status')->default(1)
            ->timestamp('deleted_at')->nullable()
            ->execute();
        // Create table with custom deleted column name
        $driver->createTable('soft_delete_custom_posts')
            ->id()
            ->string('title', 255)
            ->timestamp('removed_at')->nullable()
            ->execute();
    }

    /**
     * Reset state before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear booted state so scopes can be re-registered
        SoftDeletePost::clearBootedModels();
        CustomDeleteColumnPost::clearBootedModels();
        NonSoftDeletePost::clearBootedModels();

        // Purge query cache to ensure clean state
        Query::purgeCache();
    }

    /**
     * Clean up tables before each test
     *
     * @param string $dbName Database name
     * @param Driver $driver Driver instance
     */
    private function cleanTables(string $dbName, Driver $driver): void
    {
        $driver->truncateTable('soft_delete_posts');
        $driver->truncateTable('soft_delete_custom_posts');
    }

    /**
     * Helper to insert a test record directly
     */
    private function insertTestRecord(Driver $driver, string $title, ?string $deletedAt = null): int
    {
        $query = new Query($driver);
        $values = [
            'title' => $title,
            'content' => 'Test content',
            'status' => 1,
        ];

        if ($deletedAt !== null) {
            $values['deleted_at'] = $deletedAt;
        }

        $query->insert('soft_delete_posts')
            ->values($values)
            ->execute();

        return $driver->insertid();
    }

    // =========================================================================
    // Basic Soft Delete Tests
    // =========================================================================

    /**
     * Test that destroy() sets deleted_at timestamp instead of removing record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function destroySetsSoftDeleteTimestamp(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);
        $this->assertNull($post->get('deleted_at'));

        $result = $post->destroy();
        $this->assertTrue($result);

        // Verify timestamp was set in database
        $query = new Query($driver);
        $row = $query->select('deleted_at')
            ->from('soft_delete_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertNotNull($row->deleted_at);
    }

    /**
     * Test that trashed() returns correct status
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function trashedReturnsCorrectStatus(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);
        $this->assertFalse($post->trashed());

        $post->destroy();
        $this->assertTrue($post->trashed());
    }

    /**
     * Test that destroy() returns false for already trashed records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function destroyReturnsFalseForAlreadyTrashed(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post', '2020-01-01 00:00:00');

        // Need to use withTrashed to get the soft deleted record
        $post = SoftDeletePost::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();

        $result = $post->destroy();
        $this->assertFalse($result);
    }

    // =========================================================================
    // Restore Tests
    // =========================================================================

    /**
     * Test that restore() clears deleted_at timestamp
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreClearsDeletedAtTimestamp(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post', '2020-01-01 00:00:00');

        // Get the soft deleted record
        $post = SoftDeletePost::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();

        $this->assertTrue($post->trashed());

        $result = $post->restore();
        $this->assertTrue($result);
        $this->assertFalse($post->trashed());

        // Verify in database
        $query = new Query($driver);
        $row = $query->select('deleted_at')
            ->from('soft_delete_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertNull($row->deleted_at);
    }

    /**
     * Test that restore() returns false for non-trashed records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreReturnsFalseForNonTrashed(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);
        $result = $post->restore();

        $this->assertFalse($result);
    }

    // =========================================================================
    // Force Delete Tests
    // =========================================================================

    /**
     * Test that forceDelete() permanently removes record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function forceDeletePermanentlyRemovesRecord(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);
        $result = $post->forceDelete();

        $this->assertTrue($result);

        // Verify record is completely gone
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('soft_delete_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertEquals(0, $count->cnt);
    }

    /**
     * Test that forceDelete() works on soft deleted records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function forceDeleteWorksOnSoftDeletedRecords(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post', '2020-01-01 00:00:00');

        $post = SoftDeletePost::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();

        $result = $post->forceDelete();
        $this->assertTrue($result);

        // Verify record is completely gone
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('soft_delete_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertEquals(0, $count->cnt);
    }

    // =========================================================================
    // Query Scope Tests
    // =========================================================================

    /**
     * Test that default queries exclude soft deleted records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function defaultQueriesExcludeSoftDeletedRecords(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post 1');
        $this->insertTestRecord($driver, 'Active Post 2');
        $this->insertTestRecord($driver, 'Deleted Post 1', '2020-01-01 00:00:00');
        $this->insertTestRecord($driver, 'Deleted Post 2', '2020-01-02 00:00:00');

        $posts = SoftDeletePost::all()->rows();

        $this->assertCount(2, $posts);

        foreach ($posts as $post) {
            $this->assertStringContainsString('Active', $post->get('title'));
        }
    }

    /**
     * Test that withTrashed() includes soft deleted records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function withTrashedIncludesSoftDeletedRecords(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post');
        $this->insertTestRecord($driver, 'Deleted Post', '2020-01-01 00:00:00');

        $posts = SoftDeletePost::blank()->withTrashed()->rows();

        $this->assertCount(2, $posts);
    }

    /**
     * Test that onlyTrashed() returns only soft deleted records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function onlyTrashedReturnsOnlySoftDeletedRecords(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post');
        $this->insertTestRecord($driver, 'Deleted Post 1', '2020-01-01 00:00:00');
        $this->insertTestRecord($driver, 'Deleted Post 2', '2020-01-02 00:00:00');

        $posts = SoftDeletePost::blank()->onlyTrashed()->rows();

        $this->assertCount(2, $posts);

        foreach ($posts as $post) {
            $this->assertStringContainsString('Deleted', $post->get('title'));
        }
    }

    /**
     * Test withTrashed works with additional query constraints
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function withTrashedWorksWithAdditionalConstraints(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post');
        $this->insertTestRecord($driver, 'Deleted Special Post', '2020-01-01 00:00:00');
        $this->insertTestRecord($driver, 'Deleted Normal Post', '2020-01-02 00:00:00');

        $posts = SoftDeletePost::blank()
            ->withTrashed()
            ->whereLike('title', '%Special%')
            ->rows();

        $this->assertCount(1, $posts);
        $this->assertStringContainsString('Special', $posts->first()->get('title'));
    }

    // =========================================================================
    // Custom Column Name Tests
    // =========================================================================

    /**
     * Test soft deletes with custom column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function softDeletesWithCustomColumnName(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);
        $query->insert('soft_delete_custom_posts')
            ->values(['title' => 'Custom Post'])
            ->execute();

        $id = $driver->insertid();

        $post = CustomDeleteColumnPost::one($id);
        $this->assertFalse($post->trashed());

        $post->destroy();
        $this->assertTrue($post->trashed());

        // Verify custom column was set
        $query = new Query($driver);
        $row = $query->select('removed_at')
            ->from('soft_delete_custom_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertNotNull($row->removed_at);
    }

    /**
     * Test getDeletedAtColumn returns correct column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getDeletedAtColumnReturnsCorrectName(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $post = new SoftDeletePost();
        $this->assertEquals('deleted_at', $post->getDeletedAtColumn());

        $customPost = new CustomDeleteColumnPost();
        $this->assertEquals('removed_at', $customPost->getDeletedAtColumn());
    }

    // =========================================================================
    // Model Without Soft Deletes Tests
    // =========================================================================

    /**
     * Test that model without trait performs hard delete
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function modelWithoutTraitPerformsHardDelete(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $query = new Query($driver);
        $query->insert('soft_delete_posts')
            ->values([
                'title' => 'Non-Soft Post',
                'content' => 'Test',
                'status' => 1
            ])
            ->execute();

        $id = $driver->insertid();

        $post = NonSoftDeletePost::one($id);
        $result = $post->destroy();

        // Base Relational::destroy() returns driver object (truthy on success)
        $this->assertNotFalse($result);

        // Verify record is completely gone (not soft deleted)
        $query = new Query($driver);
        $count = $query->select('COUNT(*) as cnt')
            ->from('soft_delete_posts')
            ->where('id', '=', $id)
            ->fetch('row');

        $this->assertEquals(0, $count->cnt);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test multiple soft delete and restore cycles
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleSoftDeleteRestoreCycles(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);

        // First cycle
        $post->destroy();
        $this->assertTrue($post->trashed());

        $post->restore();
        $this->assertFalse($post->trashed());

        // Second cycle
        $post->destroy();
        $this->assertTrue($post->trashed());

        // Purge cache and verify from fresh query
        Query::purgeCache();
        $freshPost = SoftDeletePost::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();
        $this->assertTrue($freshPost->trashed());

        $freshPost->restore();
        $this->assertFalse($freshPost->trashed());
    }

    /**
     * Test isForceDeleting returns correct state
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function isForceDeleting(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, 'Test Post');

        $post = SoftDeletePost::one($id);

        // Initially should not be force deleting
        $this->assertFalse($post->isForceDeleting());
    }

    /**
     * Test soft deleted records not included in count
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function softDeletedRecordsNotIncludedInCount(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post 1');
        $this->insertTestRecord($driver, 'Active Post 2');
        $this->insertTestRecord($driver, 'Deleted Post', '2020-01-01 00:00:00');

        $count = SoftDeletePost::all()->total();

        $this->assertEquals(2, $count);
    }

    /**
     * Test withTrashed count includes all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function withTrashedCountIncludesAllRecords(string $dbName, Driver $driver)
    {
        $this->cleanTables($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $this->insertTestRecord($driver, 'Active Post 1');
        $this->insertTestRecord($driver, 'Active Post 2');
        $this->insertTestRecord($driver, 'Deleted Post', '2020-01-01 00:00:00');

        $count = SoftDeletePost::blank()->withTrashed()->total();

        $this->assertEquals(3, $count);
    }
}
