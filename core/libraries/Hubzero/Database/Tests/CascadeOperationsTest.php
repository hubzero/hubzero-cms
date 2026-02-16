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
use Hubzero\Database\Query;
use Hubzero\Database\Relational;
use Hubzero\Database\Relationship\Relationship;
use Hubzero\Database\Relationship\OneToMany;
use Hubzero\Database\Tests\TestModels\CascadePost;
use Hubzero\Database\Tests\TestModels\CascadePostNoCascade;
use Hubzero\Database\Tests\TestModels\CascadePostWithCascadeDelete;
use Hubzero\Database\Tests\TestModels\CascadePostWithBulkCascadeDelete;
use Hubzero\Database\Tests\TestModels\CascadePostWithCascadeSave;
use Hubzero\Database\Tests\TestModels\CascadePostWithOrphanRemoval;
use Hubzero\Database\Tests\TestModels\CascadePostWithFailingCascadeSave;
use Hubzero\Database\Tests\TestModels\CascadeComment;
use Hubzero\Database\Tests\TestModels\CascadeFailingCommentSave;

/**
 * Tests for ORM cascade operations (cascadeOnDelete, cascadeOnSave, orphanRemoval)
 *
 * These tests verify the backwards-compatible, opt-in cascade behavior
 * for relationship operations.
 */
class CascadeOperationsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['cascade_posts', 'cascade_comments', 'cascade_profiles'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('cascade_profiles', true);
        $driver->dropTable('cascade_comments', true);
        $driver->dropTable('cascade_posts', true);

        $schema = $driver->schema();

        // Create parent table
        $schema->createTable('cascade_posts')
            ->id()
            ->string('title', 255)
            ->text('body')->nullable()
            ->execute();

        // Create child table
        $schema->createTable('cascade_comments')
            ->id()
            ->integer('post_id')
            ->text('content')
            ->integer('is_dirty')->default(0)
            ->execute();

        // Create profile table (for one-to-one cascade)
        $schema->createTable('cascade_profiles')
            ->id()
            ->integer('post_id')
            ->string('author_name', 255)->nullable()
            ->execute();
    }

    /**
     * Set up before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear booted models cache
        CascadePost::clearBootedModels();
        CascadePostNoCascade::clearBootedModels();
        CascadePostWithCascadeDelete::clearBootedModels();
        CascadePostWithBulkCascadeDelete::clearBootedModels();
        CascadePostWithCascadeSave::clearBootedModels();
        CascadePostWithOrphanRemoval::clearBootedModels();
        CascadePostWithFailingCascadeSave::clearBootedModels();
        CascadeComment::clearBootedModels();
        CascadeFailingCommentSave::clearBootedModels();

        // Purge query cache
        Query::purgeCache();
    }

    /**
     * Clean up test data before each test
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function cleanupData(Driver $driver): void
    {
        try {
            foreach (array_reverse(static::getTestTables()) as $table) {
                $driver->exec("DELETE FROM " . $driver->quoteName($table));
            }
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    // =========================================================================
    // Relationship Fluent Method Tests
    // =========================================================================

    /**
     * Test cascadeOnDelete() fluent method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeOnDeleteFluentMethod(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        $this->assertFalse($relationship->shouldCascadeOnDelete());

        $result = $relationship->cascadeOnDelete();

        $this->assertSame($relationship, $result);
        $this->assertTrue($relationship->shouldCascadeOnDelete());
    }

    /**
     * Test cascadeOnDelete(false) disables cascade
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeOnDeleteCanBeDisabled(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        $relationship->cascadeOnDelete();
        $this->assertTrue($relationship->shouldCascadeOnDelete());

        $relationship->cascadeOnDelete(false);
        $this->assertFalse($relationship->shouldCascadeOnDelete());
    }

    /**
     * Test cascadeOnSave() fluent method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeOnSaveFluentMethod(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        $this->assertFalse($relationship->shouldCascadeOnSave());

        $result = $relationship->cascadeOnSave();

        $this->assertSame($relationship, $result);
        $this->assertTrue($relationship->shouldCascadeOnSave());
    }

    /**
     * Test orphanRemoval() fluent method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOrphanRemovalFluentMethod(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        $this->assertFalse($relationship->shouldRemoveOrphans());

        $result = $relationship->orphanRemoval();

        $this->assertSame($relationship, $result);
        $this->assertTrue($relationship->shouldRemoveOrphans());
    }

    /**
     * Test method chaining for all cascade options
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeMethodChaining(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        $result = $relationship
            ->cascadeOnDelete()
            ->cascadeOnSave()
            ->orphanRemoval();

        $this->assertSame($relationship, $result);
        $this->assertTrue($relationship->shouldCascadeOnDelete());
        $this->assertTrue($relationship->shouldCascadeOnSave());
        $this->assertTrue($relationship->shouldRemoveOrphans());
    }

    // =========================================================================
    // Cascade Delete Tests
    // =========================================================================

    /**
     * Test that cascade delete is not performed by default (BC)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNoCascadeDeleteByDefault(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post
        $post = new CascadePostNoCascade();
        $post->set('title', 'Test Post');
        $post->set('body', 'Test body');
        $post->save();

        $postId = $post->get('id');

        // Create comments
        $comment1 = new CascadeComment();
        $comment1->set('post_id', $postId);
        $comment1->set('content', 'Comment 1');
        $comment1->save();

        $comment2 = new CascadeComment();
        $comment2->set('post_id', $postId);
        $comment2->set('content', 'Comment 2');
        $comment2->save();

        // Delete post
        $post->destroy();

        // Comments should still exist (no cascade)
        $sql = "SELECT COUNT(*) as cnt FROM "
            . $driver->quoteName('cascade_comments')
            . " WHERE post_id = " . $driver->quote($postId);
        $result = $driver->setQuery($sql)->loadObject();

        $this->assertEquals(
            2,
            $result->cnt,
            'Comments should still exist when cascade is not enabled'
        );
    }

    /**
     * Test cascade delete when enabled
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeDeleteWhenEnabled(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post
        $post = new CascadePostWithCascadeDelete();
        $post->set('title', 'Test Post');
        $post->set('body', 'Test body');
        $post->save();

        $postId = $post->get('id');

        // Create comments
        $comment1 = new CascadeComment();
        $comment1->set('post_id', $postId);
        $comment1->set('content', 'Comment 1');
        $comment1->save();

        $comment2 = new CascadeComment();
        $comment2->set('post_id', $postId);
        $comment2->set('content', 'Comment 2');
        $comment2->save();

        // Delete post - should cascade to comments
        $result = $post->destroy();

        // Destroy returns the driver result, which may not be strictly boolean
        // Just verify it's truthy (not false)
        $this->assertNotFalse($result, 'Destroy should not return false');

        // Comments should be deleted
        $sql = "SELECT COUNT(*) as cnt FROM "
            . $driver->quoteName('cascade_comments')
            . " WHERE post_id = " . $driver->quote($postId);
        $result = $driver->setQuery($sql)->loadObject();

        $this->assertEquals(
            0,
            $result->cnt,
            'Comments should be deleted when cascade is enabled'
        );
    }

    // =========================================================================
    // Getter Method Tests
    // =========================================================================

    /**
     * Test getRelated() returns the related model
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetRelatedReturnsRelatedModel(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $parent = new CascadePost();
        $related = new CascadeComment();

        $relationship = new OneToMany($parent, $related, 'id', 'post_id');

        $this->assertSame($related, $relationship->getRelated());
    }

    /**
     * Test getModel() returns the parent model
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetModelReturnsParentModel(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $parent = new CascadePost();
        $related = new CascadeComment();

        $relationship = new OneToMany($parent, $related, 'id', 'post_id');

        $this->assertSame($parent, $relationship->getModel());
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test cascade delete with no related records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeDeleteWithNoRelatedRecords(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post with no comments
        $post = new CascadePostWithCascadeDelete();
        $post->set('title', 'Empty Post');
        $post->set('body', 'No comments');
        $post->save();

        // Delete should succeed (not return false)
        $result = $post->destroy();

        $this->assertNotFalse($result);
    }

    /**
     * Test that cascade options are independent
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeOptionsAreIndependent(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        // Enable only delete
        $relationship->cascadeOnDelete();

        $this->assertTrue($relationship->shouldCascadeOnDelete());
        $this->assertFalse($relationship->shouldCascadeOnSave());
        $this->assertFalse($relationship->shouldRemoveOrphans());

        // Enable only save
        $relationship2 = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );
        $relationship2->cascadeOnSave();

        $this->assertFalse($relationship2->shouldCascadeOnDelete());
        $this->assertTrue($relationship2->shouldCascadeOnSave());
        $this->assertFalse($relationship2->shouldRemoveOrphans());
    }

    // =========================================================================
    // Orphan Tracking Tests
    // =========================================================================

    /**
     * Test trackOrphansFor() stores original IDs
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTrackOrphansForStoresOriginalIds(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post with comments
        $post = new CascadePostWithOrphanRemoval();
        $post->set('title', 'Test Post');
        $post->save();

        $postId = $post->get('id');

        $comment1 = new CascadeComment();
        $comment1->set('post_id', $postId);
        $comment1->set('content', 'Comment 1');
        $comment1->save();

        $comment2 = new CascadeComment();
        $comment2->set('post_id', $postId);
        $comment2->set('content', 'Comment 2');
        $comment2->save();

        // Reload post and track orphans
        $post = CascadePostWithOrphanRemoval::one($postId);
        $post->trackOrphansFor('comments');

        // Original IDs should be stored - property is on base Relational class
        $reflection = new \ReflectionClass(Relational::class);
        $prop = $reflection->getProperty('originalRelationshipIds');
        $prop->setAccessible(true);
        $originalIds = $prop->getValue($post);

        $this->assertArrayHasKey('comments', $originalIds);
        $this->assertCount(2, $originalIds['comments']);
    }

    // =========================================================================
    // Bulk Cascade Delete Tests
    // =========================================================================

    /**
     * Test cascadeOnDelete with bulk: true parameter
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeOnDeleteBulkParameter(string $dbName, Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        $relationship = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );

        // Default: no bulk
        $relationship->cascadeOnDelete();
        $this->assertTrue($relationship->shouldCascadeOnDelete());
        $this->assertFalse($relationship->shouldBulkCascadeDelete());

        // With bulk: true
        $relationship2 = new OneToMany(
            new CascadePost(),
            new CascadeComment(),
            'id',
            'post_id'
        );
        $relationship2->cascadeOnDelete(true, true);
        $this->assertTrue($relationship2->shouldCascadeOnDelete());
        $this->assertTrue($relationship2->shouldBulkCascadeDelete());
    }

    /**
     * Test bulk cascade delete actually deletes related records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBulkCascadeDeleteWorks(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post
        $post = new CascadePostWithBulkCascadeDelete();
        $post->set('title', 'Bulk Test Post');
        $post->set('body', 'Testing bulk delete');
        $post->save();

        $postId = $post->get('id');

        // Create multiple comments
        for ($i = 1; $i <= 5; $i++) {
            $comment = new CascadeComment();
            $comment->set('post_id', $postId);
            $comment->set('content', "Comment {$i}");
            $comment->save();
        }

        // Verify comments exist
        $sql = "SELECT COUNT(*) as cnt FROM "
            . $driver->quoteName('cascade_comments')
            . " WHERE post_id = " . $driver->quote($postId);
        $result = $driver->setQuery($sql)->loadObject();
        $this->assertEquals(5, $result->cnt);

        // Delete post - should bulk cascade to comments
        $post->destroy();

        // Comments should be deleted
        $sql = "SELECT COUNT(*) as cnt FROM "
            . $driver->quoteName('cascade_comments')
            . " WHERE post_id = " . $driver->quote($postId);
        $result = $driver->setQuery($sql)->loadObject();

        $this->assertEquals(
            0,
            $result->cnt,
            'Bulk cascade delete should remove all comments'
        );
    }

    /**
     * Test that bulk: false (default) still works
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNonBulkCascadeDeleteStillWorks(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        // Create a post with regular (non-bulk) cascade
        $post = new CascadePostWithCascadeDelete();
        $post->set('title', 'Regular Cascade Test');
        $post->save();

        $postId = $post->get('id');

        // Create comments
        $comment1 = new CascadeComment();
        $comment1->set('post_id', $postId);
        $comment1->set('content', 'Comment 1');
        $comment1->save();

        $comment2 = new CascadeComment();
        $comment2->set('post_id', $postId);
        $comment2->set('content', 'Comment 2');
        $comment2->save();

        // Delete post
        $post->destroy();

        // Comments should be deleted
        $sql = "SELECT COUNT(*) as cnt FROM "
            . $driver->quoteName('cascade_comments')
            . " WHERE post_id = " . $driver->quote($postId);
        $result = $driver->setQuery($sql)->loadObject();

        $this->assertEquals(0, $result->cnt);
    }

    /**
     * Test parent save is rolled back when cascade save fails
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCascadeSaveFailureRollsBackParentSave(string $dbName, Driver $driver): void
    {
        $this->cleanupData($driver);
        Relational::setDefaultConnection($driver);

        $post = new CascadePostWithFailingCascadeSave();
        $post->set('title', 'Should Roll Back');
        $post->set('body', 'Parent insert should not persist');

        $failingComment = new CascadeFailingCommentSave();
        $failingComment->set('content', 'This save is forced to fail');

        // Mark relationship as loaded so performCascadeSaves() will process it.
        $post->addRelationship('comments', $failingComment);

        $result = $post->save();
        $this->assertFalse($result, 'Save should fail when cascade save fails');

        $sql = "SELECT COUNT(*) as cnt FROM " . $driver->quoteName('cascade_posts')
            . " WHERE title = " . $driver->quote('Should Roll Back');
        $row = $driver->setQuery($sql)->loadObject();

        $this->assertEquals(
            0,
            (int) $row->cnt,
            'Parent row should be rolled back when cascade save fails'
        );
    }
}
