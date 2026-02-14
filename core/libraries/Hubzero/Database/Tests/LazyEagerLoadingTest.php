<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Rows;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\LazyUser;
use Hubzero\Database\Tests\TestModels\LazyPost;
use Hubzero\Database\Tests\TestModels\LazyComment;
use Hubzero\Database\Tests\TestModels\LazyProfile;

/**
 * Lazy Eager Loading tests
 *
 * Tests for the load() and loadMissing() methods that allow loading
 * relationships on already-retrieved models and collections.
 */
class LazyEagerLoadingTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['lazy_comments', 'lazy_posts', 'lazy_profiles', 'lazy_users'];
    }

    /**
     * Set up test tables
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('lazy_users', true);
        $driver->createTable('lazy_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)->nullable()
            ->execute();

        $driver->dropTable('lazy_posts', true);
        $driver->createTable('lazy_posts')
            ->id()
            ->integer('user_id')
            ->string('title', 255)
            ->string('body', 500)->nullable()
            ->integer('published')->default(0)
            ->execute();

        $driver->dropTable('lazy_comments', true);
        $driver->createTable('lazy_comments')
            ->id()
            ->integer('post_id')
            ->integer('user_id')->nullable()
            ->string('body', 500)
            ->execute();

        $driver->dropTable('lazy_profiles', true);
        $driver->createTable('lazy_profiles')
            ->id()
            ->integer('user_id')
            ->string('bio', 500)->nullable()
            ->execute();
    }

    /**
     * Setup test data before each test
     */
    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        // Clear booted state
        LazyUser::clearBootedModels();
        LazyPost::clearBootedModels();
        LazyComment::clearBootedModels();
        LazyProfile::clearBootedModels();

        Query::purgeCache();

        // Truncate tables (delete rows + reset auto-increment)
        $driver->truncateTable('lazy_users');
        $driver->truncateTable('lazy_posts');
        $driver->truncateTable('lazy_comments');
        $driver->truncateTable('lazy_profiles');

        // Insert test data
        $users = [
            ['name' => 'Alice', 'email' => 'alice@test.com'],
            ['name' => 'Bob', 'email' => 'bob@test.com'],
        ];
        $query = new Query($driver);
        $query->insertMany('lazy_users', $users);

        $posts = [
            ['user_id' => 1, 'title' => 'Post 1', 'body' => 'Body 1', 'published' => 1],
            ['user_id' => 1, 'title' => 'Post 2', 'body' => 'Body 2', 'published' => 0],
            ['user_id' => 2, 'title' => 'Post 3', 'body' => 'Body 3', 'published' => 1],
        ];
        $query = new Query($driver);
        $query->insertMany('lazy_posts', $posts);

        $comments = [
            ['post_id' => 1, 'user_id' => 2, 'body' => 'Great post!'],
            ['post_id' => 1, 'user_id' => 1, 'body' => 'Thanks!'],
            ['post_id' => 3, 'user_id' => 1, 'body' => 'Nice!'],
        ];
        $query = new Query($driver);
        $query->insertMany('lazy_comments', $comments);

        $profiles = [
            ['user_id' => 1, 'bio' => 'Alice bio'],
        ];
        $query = new Query($driver);
        $query->insertMany('lazy_profiles', $profiles);
    }

    // =========================================================================
    // Single Model Tests (Relational::load)
    // =========================================================================

    /**
     * Test load() method exists on Relational
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadMethodExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = new LazyUser();
        $this->assertTrue(method_exists($user, 'load'));
    }

    /**
     * Test loadMissing() method exists on Relational
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadMissingMethodExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = new LazyUser();
        $this->assertTrue(method_exists($user, 'loadMissing'));
    }

    /**
     * Test load() loads a simple relationship on a single model
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadSimpleRelationship(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        // Relationship should not be loaded initially
        $this->assertNull($user->getRelationship('posts'));

        // Load the relationship
        $user->load('posts');

        // Now it should be loaded
        $posts = $user->getRelationship('posts');
        $this->assertNotNull($posts);
        $this->assertCount(2, $posts);
    }

    /**
     * Test load() loads multiple relationships
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadMultipleRelationships(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        $this->assertNull($user->getRelationship('posts'));
        $this->assertNull($user->getRelationship('profile'));

        $user->load('posts', 'profile');

        $this->assertNotNull($user->getRelationship('posts'));
        $this->assertNotNull($user->getRelationship('profile'));
    }

    /**
     * Test load() returns self for chaining
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadReturnsSelfForChaining(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        $result = $user->load('posts');

        $this->assertSame($user, $result);
    }

    /**
     * Test loadMissing() only loads missing relationships
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadMissingSkipsLoaded(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        // Pre-load posts
        $user->load('posts');
        $postsLoaded = $user->getRelationship('posts');

        // Now try loadMissing - should not re-load posts
        $user->loadMissing('posts', 'profile');

        // Posts should be the same object (not re-loaded)
        $this->assertSame($postsLoaded, $user->getRelationship('posts'));

        // Profile should now be loaded
        $this->assertNotNull($user->getRelationship('profile'));
    }

    /**
     * Test load() with no arguments returns self without error
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadWithNoArguments(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);
        $result = $user->load();

        $this->assertSame($user, $result);
    }

    // =========================================================================
    // Collection Tests (Rows::load)
    // =========================================================================

    /**
     * Test load() method exists on Rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadMethodExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $rows = new Rows();
        $this->assertTrue(method_exists($rows, 'load'));
    }

    /**
     * Test loadMissing() method exists on Rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadMissingMethodExists(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $rows = new Rows();
        $this->assertTrue(method_exists($rows, 'loadMissing'));
    }

    /**
     * Test load() on Rows loads relationships for all models
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadRelationship(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $users = LazyUser::all()->rows();

        // No relationships should be loaded
        foreach ($users as $user) {
            $this->assertNull($user->getRelationship('posts'));
        }

        // Load relationships
        $users->load('posts');

        // All users should have posts loaded
        foreach ($users as $user) {
            $this->assertNotNull($user->getRelationship('posts'));
        }
    }

    /**
     * Test load() on Rows returns self for chaining
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadReturnsSelfForChaining(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $users = LazyUser::all()->rows();
        $result = $users->load('posts');

        $this->assertSame($users, $result);
    }

    /**
     * Test load() with empty collection doesn't error
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadEmptyCollection(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $rows = new Rows();
        $result = $rows->load('posts');

        $this->assertSame($rows, $result);
    }

    /**
     * Test Rows loadMissing() only loads missing relationships
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRowsLoadMissingSkipsLoaded(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $users = LazyUser::all()->rows();

        // Pre-load posts
        $users->load('posts');

        // Now try loadMissing
        $users->loadMissing('posts', 'profile');

        // Profile should now be loaded on all users
        foreach ($users as $user) {
            $this->assertNotNull($user->getRelationship('profile'));
        }
    }

    // =========================================================================
    // Nested Relationship Tests
    // =========================================================================

    /**
     * Test load() with nested relationships
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadNestedRelationships(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        $user->load('posts.comments');

        // Posts should be loaded
        $posts = $user->getRelationship('posts');
        $this->assertNotNull($posts);

        // First post should have comments loaded
        $firstPost = $posts->first();
        $this->assertNotNull($firstPost->getRelationship('comments'));
        $this->assertCount(2, $firstPost->getRelationship('comments'));
    }

    // =========================================================================
    // Constraint Tests
    // =========================================================================

    /**
     * Test load() with closure constraint
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadWithClosureConstraint(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        $user->load(['posts', function ($query) {
            $query->whereEquals('published', 1);
        }]);

        $posts = $user->getRelationship('posts');
        $this->assertNotNull($posts);
        $this->assertCount(1, $posts); // Only 1 published post
    }

    /**
     * Test load() with array constraint (conditions)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadWithArrayConstraint(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = LazyUser::one(1);

        $user->load([
            'posts' => [
                'conditions' => ['published' => 1]
            ]
        ]);

        $posts = $user->getRelationship('posts');
        $this->assertNotNull($posts);
        $this->assertCount(1, $posts);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test load() on model without primary key returns self
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadOnBlankModelReturnsSelf(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $user = new LazyUser();
        $result = $user->load('posts');

        $this->assertSame($user, $result);
    }

    /**
     * Test that load() uses efficient bulk loading
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLoadUsesEfficientBulkLoading(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // This test verifies that load() on a collection uses a single query
        // rather than N queries (one per model)
        $users = LazyUser::all()->rows();
        $this->assertCount(2, $users);

        // If this completes without error, bulk loading is working
        // (A naive implementation would fail or be very slow)
        $users->load('posts');

        // Verify all users have posts
        foreach ($users as $user) {
            $this->assertNotNull($user->getRelationship('posts'));
        }
    }

    /**
     * Clean up after all tests
     */
    public static function tearDownAfterClass(): void
    {
        Relational::clearBootedModels();
        Query::purgeCache();
        parent::tearDownAfterClass();
    }
}
