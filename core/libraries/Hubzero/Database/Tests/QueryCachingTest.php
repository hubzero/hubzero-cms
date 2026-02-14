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
use Hubzero\Database\Tests\TestModels\MockCacheStore;

/**
 * Query caching tests
 *
 * Tests for the remember() and rememberForever() persistent caching functionality.
 */
class QueryCachingTest extends AbstractDriverTestCase
{
    /**
     * A simple mock cache store for testing
     *
     * @var  MockCacheStore
     **/
    private $mockCache;

    protected static function getTestTables(): array
    {
        return ['users'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('users', true);

        $driver->createTable('users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insertMany('users', [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
        ]);
    }

    /**
     * Set up test fixtures
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mock cache store
        $this->mockCache = new MockCacheStore();

        // Clear any existing cache store
        Query::setCacheStore(null);

        // Purge in-memory cache
        Query::purgeCache();
    }

    /**
     * Clean up after tests
     *
     * @return  void
     **/
    protected function tearDown(): void
    {
        // Clear cache store
        Query::setCacheStore(null);
        Query::purgeCache();

        parent::tearDown();
    }

    /**
     * Test that setCacheStore() and getCacheStore() work correctly
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function setAndGetCacheStore(string $dbName, Driver $driver): void
    {
        $this->assertNull(Query::getCacheStore(), 'Cache store should be null initially');

        Query::setCacheStore($this->mockCache);
        $this->assertSame($this->mockCache, Query::getCacheStore(), 'Cache store should be the mock store');

        Query::setCacheStore(null);
        $this->assertNull(Query::getCacheStore(), 'Cache store should be null after reset');
    }

    /**
     * Test that remember() returns chainable query
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rememberIsChainable(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $result = $query->select('*')->from('users')->remember(60);

        $this->assertInstanceOf(Query::class, $result, 'remember() should return Query instance');
    }

    /**
     * Test that rememberForever() returns chainable query
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rememberForeverIsChainable(string $dbName, Driver $driver): void
    {
        $query = new Query($driver);

        $result = $query->select('*')->from('users')->rememberForever();

        $this->assertInstanceOf(Query::class, $result, 'rememberForever() should return Query instance');
    }

    /**
     * Test that remember() uses the injected cache store
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rememberUsesInjectedCacheStore(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        $query = new Query($driver);

        // Execute query with remember
        $rows = $query->select('*')
                      ->from('users')
                      ->remember(60)
                      ->fetch();

        // Check that something was stored in the mock cache
        $this->assertGreaterThan(0, $this->mockCache->putCount, 'Cache put() should have been called');
        $this->assertEquals(60, $this->mockCache->lastTtl, 'TTL should be 60 minutes');
    }

    /**
     * Test that rememberForever() stores with 0 TTL
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rememberForeverStoresWithZeroTtl(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        $query = new Query($driver);

        // Execute query with rememberForever
        $rows = $query->select('*')
                      ->from('users')
                      ->rememberForever()
                      ->fetch();

        // Check that something was stored with 0 TTL
        $this->assertGreaterThan(0, $this->mockCache->putCount, 'Cache put() should have been called');
        $this->assertEquals(0, $this->mockCache->lastTtl, 'TTL should be 0 for forever');
    }

    /**
     * Test that cached results are returned on subsequent calls
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function cachedResultsAreReturned(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        // First query - should hit database
        $query1 = new Query($driver);
        $rows1 = $query1->select('*')
                        ->from('users')
                        ->remember(60)
                        ->fetch();

        // Purge in-memory cache to force persistent cache lookup
        Query::purgeCache();

        // Second query (same) - should hit cache
        $query2 = new Query($driver);
        $rows2 = $query2->select('*')
                        ->from('users')
                        ->remember(60)
                        ->fetch();

        // Cache should have been checked
        $this->assertGreaterThan(0, $this->mockCache->getCount, 'Cache get() should have been called');

        // Results should be the same
        $this->assertEquals($rows1, $rows2, 'Cached results should match original');
    }

    /**
     * Test that custom cache prefix works
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function customCachePrefix(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        $query = new Query($driver);

        // Execute query with custom prefix
        $rows = $query->select('*')
                      ->from('users')
                      ->remember(60, 'custom_prefix_')
                      ->fetch();

        // Check that the key starts with custom prefix
        $this->assertStringStartsWith(
            'custom_prefix_',
            $this->mockCache->lastKey,
            'Cache key should have custom prefix'
        );
    }

    /**
     * Test that forgetCached() removes items from cache
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function forgetCachedRemovesFromCache(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        // Pre-populate cache
        $this->mockCache->put('hubzero_query_testkey', 'testvalue', 60);

        $query = new Query($driver);

        // Forget the cached item
        $result = $query->forgetCached('testkey');

        // Cache forget() should have been called
        $this->assertGreaterThan(0, $this->mockCache->forgetCount, 'Cache forget() should have been called');
    }

    /**
     * Test that without cache store, in-memory cache is used
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function fallsBackToInMemoryCache(string $dbName, Driver $driver): void
    {
        // Don't set any cache store - ensure APCu fallback is also avoided
        Query::setCacheStore(null);

        // First query
        $query1 = new Query($driver);
        $rows1 = $query1->select('*')
                        ->from('users')
                        ->whereEquals('id', '1')
                        ->fetch();

        // Second identical query should use in-memory cache
        $query2 = new Query($driver);
        $rows2 = $query2->select('*')
                        ->from('users')
                        ->whereEquals('id', '1')
                        ->fetch();

        // Results should match (in-memory cache working)
        $this->assertEquals($rows1, $rows2, 'In-memory cached results should match');
    }

    /**
     * Test that noCache bypasses cache
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function noCacheBypassesCache(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        // First query with cache
        $query1 = new Query($driver);
        $rows1 = $query1->select('*')
                        ->from('users')
                        ->remember(60)
                        ->fetch();

        $putCountAfterFirst = $this->mockCache->putCount;

        // Second query with noCache=true
        $query2 = new Query($driver);
        $rows2 = $query2->select('*')
                        ->from('users')
                        ->remember(60)
                        ->fetch('rows', true);  // noCache = true

        // Another put should have happened since noCache was true
        $this->assertGreaterThan(
            $putCountAfterFirst,
            $this->mockCache->putCount,
            'Cache should have been updated with noCache=true'
        );
    }

    /**
     * Test that different queries get different cache keys
     *
     * @return  void
     **/
    #[Test]
    #[DataProvider('databaseProvider')]
    public function differentQueriesGetDifferentKeys(string $dbName, Driver $driver): void
    {
        Query::setCacheStore($this->mockCache);

        // First query
        $query1 = new Query($driver);
        $query1->select('*')
               ->from('users')
               ->whereEquals('id', '1')
               ->remember(60)
               ->fetch();

        $key1 = $this->mockCache->lastKey;

        // Different query
        $query2 = new Query($driver);
        $query2->select('*')
               ->from('users')
               ->whereEquals('id', '2')
               ->remember(60)
               ->fetch();

        $key2 = $this->mockCache->lastKey;

        $this->assertNotEquals($key1, $key2, 'Different queries should have different cache keys');
    }
}
