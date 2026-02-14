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
use Hubzero\Database\Relationship\OneToMany;
use Hubzero\Database\Tests\TestModels\OnCondPost;
use Hubzero\Database\Tests\TestModels\OnCondComment;

/**
 * Tests for Relationship::onCondition() - pre-filtered relationships
 *
 * These tests verify the onCondition API that allows defining default
 * conditions on relationships, similar to Yii's onCondition feature.
 */
class OnConditionTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['oncond_comments', 'oncond_posts'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Create tables
        $driver->dropTable('oncond_posts', true);
        $driver->createTable('oncond_posts')
            ->id()
            ->string('title', 255)
            ->execute();
        $driver->dropTable('oncond_comments', true);
        $driver->createTable('oncond_comments')
            ->id()
            ->integer('post_id')
            ->string('content', 500)
            ->integer('status')->default(0)
            ->string('type', 50)->default('general')
            ->string('created_at', 50)->nullable()
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        OnCondPost::clearBootedModels();
        OnCondComment::clearBootedModels();
        Query::purgeCache();

        // Truncate tables (delete rows + reset auto-increment)
        $driver->truncateTable('oncond_posts');
        $driver->truncateTable('oncond_comments');

        // Insert posts
        $q = new Query($driver);
        $q->insertMany('oncond_posts', [
            ['title' => 'Post 1'],
            ['title' => 'Post 2'],
        ]);

        // Insert comments
        $q = new Query($driver);
        $q->insertMany('oncond_comments', [
            [
                'post_id' => 1, 'content' => 'Active Comment 1',
                'status' => 1, 'type' => 'general', 'created_at' => '2024-01-01 00:00:00',
            ],
            [
                'post_id' => 1, 'content' => 'Active Comment 2',
                'status' => 1, 'type' => 'featured', 'created_at' => '2024-02-01 00:00:00',
            ],
            [
                'post_id' => 1, 'content' => 'Inactive Comment',
                'status' => 0, 'type' => 'general', 'created_at' => '2024-03-01 00:00:00',
            ],
            [
                'post_id' => 2, 'content' => 'Active Comment 3',
                'status' => 1, 'type' => 'featured', 'created_at' => '2024-01-15 00:00:00',
            ],
            [
                'post_id' => 2, 'content' => 'Inactive Comment 2',
                'status' => 0, 'type' => 'general', 'created_at' => '2024-02-15 00:00:00',
            ],
            [
                'post_id' => 2, 'content' => 'Inactive Comment 3',
                'status' => 0, 'type' => 'featured', 'created_at' => '2024-03-15 00:00:00',
            ],
        ]);
    }

    /**
     * Clean up after all tests - custom cleanup for model state
     */
    public static function tearDownAfterClass(): void
    {
        Relational::clearBootedModels();
        Query::purgeCache();
        parent::tearDownAfterClass();
    }

    // =========================================================================
    // Basic onCondition Tests
    // =========================================================================

    /**
     * Test onCondition with simple equality (2 args)
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionWithSimpleEquality(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(1);

        // activeComments uses onCondition('status', 1)
        $comments = $post->activeComments()->rows();

        $this->assertCount(2, $comments);

        foreach ($comments as $comment) {
            $this->assertEquals(1, $comment->status);
        }
    }

    /**
     * Test onCondition with operator (3 args)
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionWithOperator(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        // Use > operator
        $relationship->onCondition('status', '>=', 1);

        $this->assertTrue($relationship->hasDefaultConditions());
        $this->assertEquals(1, $relationship->getDefaultConditionCount());
    }

    /**
     * Test onCondition with closure
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionWithClosure(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(1);

        // complexComments uses closure
        $comments = $post->complexComments()->rows();

        // Should only return active, general comments
        $this->assertCount(1, $comments);
        $this->assertEquals('Active Comment 1', $comments->first()->content);
    }

    /**
     * Test chaining multiple onConditions
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleOnConditions(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(1);

        // activeFeatured uses two onCondition calls
        $comments = $post->activeFeatured()->rows();

        // Should only return active featured comments
        $this->assertCount(1, $comments);
        $this->assertEquals('Active Comment 2', $comments->first()->content);
    }

    // =========================================================================
    // Convenience Method Tests
    // =========================================================================

    /**
     * Test onConditionIn
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionIn(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship->onConditionIn('status', [1, 2]);

        $this->assertTrue($relationship->hasDefaultConditions());
    }

    /**
     * Test onConditionNotIn
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionNotIn(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship->onConditionNotIn('status', [0, -1]);

        $this->assertTrue($relationship->hasDefaultConditions());
    }

    /**
     * Test onConditionNull
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionNull(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship->onConditionNull('deleted_at');

        $this->assertTrue($relationship->hasDefaultConditions());
    }

    /**
     * Test onConditionNotNull
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionNotNull(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship->onConditionNotNull('created_at');

        $this->assertTrue($relationship->hasDefaultConditions());
    }

    // =========================================================================
    // Fluent API Tests
    // =========================================================================

    /**
     * Test method chaining returns relationship instance
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMethodChainingReturnsSelf(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $result = $relationship
            ->onCondition('status', 1)
            ->onConditionIn('type', ['general', 'featured'])
            ->cascadeOnDelete();

        $this->assertSame($relationship, $result);
        $this->assertTrue($relationship->hasDefaultConditions());
        $this->assertEquals(2, $relationship->getDefaultConditionCount());
        $this->assertTrue($relationship->shouldCascadeOnDelete());
    }

    /**
     * Test withoutDefaultConditions clears conditions
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWithoutDefaultConditions(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship->onCondition('status', 1);
        $this->assertTrue($relationship->hasDefaultConditions());

        $relationship->withoutDefaultConditions();
        $this->assertFalse($relationship->hasDefaultConditions());
    }

    // =========================================================================
    // Backwards Compatibility Tests
    // =========================================================================

    /**
     * Test relationships without onCondition work unchanged
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationshipsWithoutOnConditionUnchanged(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(1);

        // allComments has no onCondition
        $comments = $post->allComments()->rows();

        // Should return all 3 comments for post 1
        $this->assertCount(3, $comments);
    }

    /**
     * Test additional where clauses can be chained after onCondition
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChainingAdditionalWhereClauses(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(1);

        // Start with activeComments (status=1), then add more conditions
        $comments = $post->activeComments()
            ->whereEquals('type', 'featured')
            ->rows();

        // Only active featured comments
        $this->assertCount(1, $comments);
        $this->assertEquals('Active Comment 2', $comments->first()->content);
    }

    // =========================================================================
    // Integration Tests
    // =========================================================================

    /**
     * Test onCondition works with relationship methods
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnConditionWithRelationshipMethods(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post = OnCondPost::one(2);

        // activeComments should only return 1 comment for post 2
        $comments = $post->activeComments()->rows();

        $this->assertCount(1, $comments);
        $this->assertEquals('Active Comment 3', $comments->first()->content);
    }

    /**
     * Test that different posts get correctly filtered results
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDifferentPostsGetCorrectResults(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $post1 = OnCondPost::one(1);
        $post2 = OnCondPost::one(2);

        $post1Active = $post1->activeComments()->rows();
        $post2Active = $post2->activeComments()->rows();

        // Post 1 has 2 active comments
        $this->assertCount(2, $post1Active);

        // Post 2 has 1 active comment
        $this->assertCount(1, $post2Active);
    }

    /**
     * Test onCondition with orOnCondition
     *
     * @return void
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOrOnCondition(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);
        $relationship = new OneToMany(
            new OnCondPost(),
            new OnCondComment(),
            'id',
            'post_id'
        );

        $relationship
            ->onCondition('status', 1)
            ->orOnCondition('type', 'featured');

        $this->assertEquals(2, $relationship->getDefaultConditionCount());
    }
}
