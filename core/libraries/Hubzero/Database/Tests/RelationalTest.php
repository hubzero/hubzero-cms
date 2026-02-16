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
use Hubzero\Database\Tests\TestModels\User;
use Hubzero\Database\Tests\TestModels\Post;
use Hubzero\Database\Tests\TestModels\Tag;

/**
 * Relational ORM tests - runs against ALL configured databases
 *
 * Configuration: Add backends to DB_TEST_BACKENDS and set credentials in phpunit.xml
 *
 * Each test method runs once per configured database using PHPUnit data providers.
 */
class RelationalTest extends AbstractDriverTestCase
{
    /**
     * Whether tables have been created per database
     *
     * @var array
     */
    protected static $tablesCreated = [];

    protected static function getTestTables(): array
    {
        return ['test_users', 'test_posts', 'test_tags', 'test_post_tags'];
    }

    protected static function getTestModelClasses(): array
    {
        return [User::class, Post::class, Tag::class];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created on-demand in setupTables() per test
    }

    /**
     * Get table name with "test_" prefix
     *
     * @param   string  $table   Base table name
     * @return  string
     */
    protected function tableName(string $table): string
    {
        return 'test_' . $table;
    }

    /**
     * Set up test tables for a database (once per database)
     *
     * @param   Driver  $driver  Driver instance
     * @param   string  $dbName  Database name
     * @return  void
     */
    protected function setupTables(Driver $driver, string $dbName): void
    {
        if (isset(self::$tablesCreated[$dbName])) {
            $allExist = true;
            foreach (static::getTestTables() as $table) {
                if (!$driver->tableExists($table)) {
                    $allExist = false;
                    break;
                }
            }
            if ($allExist) {
                return;
            }
        }

        $usersTable = $this->tableName('users');
        $postsTable = $this->tableName('posts');
        $tagsTable = $this->tableName('tags');
        $postTagsTable = $this->tableName('post_tags');

        // Drop tables if they exist (children first)
        foreach ([$postTagsTable, $postsTable, $tagsTable, $usersTable] as $table) {
            $driver->dropTable($table, true);
        }

        // Create tables using schema builder
        $driver->createTable($usersTable)
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->execute();

        $driver->createTable($postsTable)
            ->id()
            ->integer('user_id')
            ->string('title', 255)
            ->text('content')
            ->execute();

        $driver->createTable($tagsTable)
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable($postTagsTable)
            ->integer('post_id')
            ->integer('tag_id')
            ->execute();

        self::$tablesCreated[$dbName] = true;
    }

    /**
     * Seed test data
     *
     * @param   Driver  $driver  Driver instance
     * @param   string  $dbName  Database name
     * @return  void
     */
    protected function seedTestData(Driver $driver, string $dbName): void
    {
        $usersTable = $this->tableName('users');
        $postsTable = $this->tableName('posts');
        $tagsTable = $this->tableName('tags');
        $postTagsTable = $this->tableName('post_tags');

        // Clear existing data
        $driver->truncateTable($postTagsTable);
        $driver->truncateTable($postsTable);
        $driver->truncateTable($tagsTable);
        $driver->truncateTable($usersTable);

        // Insert users
        $driver->setQuery("INSERT INTO {$usersTable} (id, name, email) VALUES (1, 'Test User', 'test@example.com')");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$usersTable} (id, name, email) VALUES (2, 'Jane Doe', 'jane@example.com')");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$usersTable} (id, name, email) VALUES (3, 'Bob Smith', 'bob@example.com')");
        $driver->execute();

        // Insert posts
        $driver->setQuery(
            "INSERT INTO {$postsTable} (id, user_id, title, content)"
            . " VALUES (1, 1, 'First Post', 'Content of first post')"
        );
        $driver->execute();
        $driver->setQuery(
            "INSERT INTO {$postsTable} (id, user_id, title, content)"
            . " VALUES (2, 1, 'Second Post', 'Content of second post')"
        );
        $driver->execute();
        $driver->setQuery(
            "INSERT INTO {$postsTable} (id, user_id, title, content)"
            . " VALUES (3, 2, 'Third Post', 'Content of third post')"
        );
        $driver->execute();

        // Insert tags
        $driver->setQuery("INSERT INTO {$tagsTable} (id, name) VALUES (1, 'php')");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$tagsTable} (id, name) VALUES (2, 'database')");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$tagsTable} (id, name) VALUES (3, 'fun stuff')");
        $driver->execute();

        // Insert post_tags (many-to-many)
        $driver->setQuery("INSERT INTO {$postTagsTable} (post_id, tag_id) VALUES (1, 1)");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$postTagsTable} (post_id, tag_id) VALUES (1, 2)");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$postTagsTable} (post_id, tag_id) VALUES (1, 3)");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$postTagsTable} (post_id, tag_id) VALUES (2, 3)");
        $driver->execute();

        // Reset auto-increment past seeded IDs so new inserts don't conflict
        $driver->setAutoIncrement($usersTable, 4);
        $driver->setAutoIncrement($postsTable, 4);
        $driver->setAutoIncrement($tagsTable, 4);
    }

    /**
     * Clean up test data
     *
     * @param   Driver  $driver  Driver instance
     * @param   string  $dbName  Database name
     * @return  void
     */
    protected function cleanupTestData(Driver $driver, string $dbName): void
    {
        $usersTable = $this->tableName('users');
        $postsTable = $this->tableName('posts');
        $tagsTable = $this->tableName('tags');
        $postTagsTable = $this->tableName('post_tags');

        // Clear all data (children first for referential integrity)
        foreach ([$postTagsTable, $postsTable, $tagsTable, $usersTable] as $table) {
            try {
                $driver->setQuery("DELETE FROM {$table}");
                $driver->execute();
            } catch (\Exception $e) {
                fwrite(STDERR, sprintf(
                    "\n  [cleanup] %s DELETE FROM %s: %s\n",
                    $dbName,
                    $table,
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * Configure test models for the current database
     *
     * @param   string  $dbName  Database name
     * @return  void
     */
    protected function configureModels(string $dbName): void
    {
        User::useTable($this->tableName('users'));
        Post::useTable($this->tableName('posts'));
        Tag::useTable($this->tableName('tags'));
        Post::useTagsAssocTable($this->tableName('post_tags'));
        Tag::usePostsAssocTable($this->tableName('post_tags'));
    }

    // =========================================================================
    // Basic Model Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModelConstruction(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $model = User::blank();

        $this->assertInstanceOf(Relational::class, $model, "[$dbName] Model should be instance of Relational");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindById(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $user = User::one(1);

        $this->assertEquals(1, $user->get('id'), "[$dbName] Should find user with ID 1");
        $this->assertEquals('Test User', $user->get('name'), "[$dbName] User name should match");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOneOrFailThrowsException(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $this->expectException(\Hubzero\Error\Exception\RuntimeException::class);

        // No cleanup after this — PHPUnit intercepts the exception so
        // nothing below oneOrFail() executes. seedTestData() in the
        // next test deletes all rows before re-seeding.
        User::oneOrFail(999);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOneOrNewReturnsNewModel(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $user = User::oneOrNew(999);

        $this->assertTrue($user->isNew(), "[$dbName] Model should be new for non-existent ID");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFetchAll(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $users = User::all()->rows();

        $this->assertCount(3, $users, "[$dbName] Should have 3 users");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereEquals(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $users = User::all()
            ->whereEquals('name', 'Test User')
            ->rows();

        $this->assertCount(1, $users, "[$dbName] Should find 1 user named Test User");

        $this->cleanupTestData($driver, $dbName);
    }

    // =========================================================================
    // Relationship Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBelongsToOne(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $post = Post::oneOrFail(1);
        $user = $post->user;

        $this->assertNotNull($user, "[$dbName] Post should have a user");
        $this->assertEquals(1, $user->get('id'), "[$dbName] User ID should be 1");
        $this->assertEquals('Test User', $user->get('name'), "[$dbName] User name should match");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOneToMany(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $user = User::oneOrFail(1);
        $posts = $user->posts;

        $this->assertCount(2, $posts, "[$dbName] User 1 should have 2 posts");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testManyToMany(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $post = Post::oneOrFail(1);
        $tags = $post->tags;

        $this->assertCount(3, $tags, "[$dbName] Post 1 should have 3 tags");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereRelatedHasCount(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $users = User::all()
            ->whereRelatedHasCount('posts', 2)
            ->rows();

        $this->assertCount(1, $users, "[$dbName] Should find 1 user with 2+ posts");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereRelatedHas(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $posts = Post::all()
            ->whereRelatedHas('user', function ($user) {
                $user->whereEquals('name', 'Test User');
            })
            ->rows();

        $this->assertCount(2, $posts, "[$dbName] Should find 2 posts by Test User");

        $this->cleanupTestData($driver, $dbName);
    }

    // =========================================================================
    // CRUD Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCreateRecord(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $user = User::blank();
        $user->set('name', 'New User');
        $user->set('email', 'new@example.com');

        $result = $user->save();

        // save() returns the new ID on success, false on failure
        $this->assertNotFalse($result, "[$dbName] Save should not return false");
        $this->assertFalse($user->isNew(), "[$dbName] Model should no longer be new after save");
        $this->assertNotEmpty($user->get('id'), "[$dbName] Model should have an ID after save");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdateRecord(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $user = User::one(1);
        $user->set('name', 'Updated Name');

        $result = $user->save();

        // save() returns a truthy value on success, false on failure
        $this->assertNotFalse($result, "[$dbName] Save should not return false");

        // Reload and verify
        $reloaded = User::one(1);
        $this->assertEquals('Updated Name', $reloaded->get('name'), "[$dbName] Name should be updated");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeleteRecord(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        // Use an existing seeded user to avoid cascade delete issues
        // User 3 (Bob Smith) has no posts, so can be safely deleted
        $user = User::one(3);
        $this->assertFalse($user->isNew(), "[$dbName] User 3 should exist");
        $this->assertEquals(3, $user->get('id'), "[$dbName] Should be user ID 3");

        // Delete it
        $result = $user->destroy();

        $this->assertNotFalse($result, "[$dbName] Destroy should not return false");

        // Verify it's gone by direct query to avoid caching issues
        $tableName = $this->tableName('users');
        $driver->setQuery("SELECT COUNT(*) as cnt FROM {$tableName} WHERE id = 3");
        $row = $driver->loadObject();

        $this->assertEquals(0, (int)$row->cnt, "[$dbName] Deleted user should not exist in database");

        $this->cleanupTestData($driver, $dbName);
    }

    // =========================================================================
    // Query Builder Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOrderBy(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $users = User::all()
            ->order('name', 'asc')
            ->rows();

        $names = [];
        foreach ($users as $user) {
            $names[] = $user->get('name');
        }

        $sorted = $names;
        sort($sorted);

        $this->assertEquals($sorted, $names, "[$dbName] Users should be sorted by name");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLimit(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $users = User::all()
            ->limit(2)
            ->rows();

        $this->assertCount(2, $users, "[$dbName] Should return only 2 users");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCount(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $count = User::all()->total();

        $this->assertEquals(3, $count, "[$dbName] Should count 3 users");

        $this->cleanupTestData($driver, $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testForwardedPropertyReturnsRelatedValue(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        $this->seedTestData($driver, $dbName);
        $this->configureModels($dbName);
        Relational::setDefaultConnection($driver);

        $post = Post::oneOrFail(1)->forwardTo('user');

        $this->assertEquals(
            'test@example.com',
            $post->email,
            "[$dbName] Forwarded property should return related model value"
        );

        $this->cleanupTestData($driver, $dbName);
    }
}
