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
use Hubzero\Database\Tests\TestModels\ArticleWithoutScopes;
use Hubzero\Database\Tests\TestModels\ScopedArticle;
use Hubzero\Database\Tests\TestModels\ArticleWithObjectScope;
use Hubzero\Database\Tests\TestModels\PublishedScope;
use Hubzero\Database\Tests\TestModels\BootCountArticle;

/**
 * Global Scopes tests
 *
 * Tests for the global scope functionality that automatically applies
 * query constraints to all queries on a model.
 */
class GlobalScopesTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     */
    protected static function getTestTables(): array
    {
        return ['scoped_articles'];
    }

    /**
     * Create test tables
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('scoped_articles', true);

        $driver->createTable('scoped_articles')
            ->id()
            ->string('title', 255)->notNull()
            ->integer('active')->default(1)
            ->integer('tenant_id')->default(1)
            ->string('status', 50)->default('draft')
            ->execute();
    }

    /**
     * Commit pending transactions and clear booted models before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear booted state for all test model classes
        ScopedArticle::clearBootedModels();
        ArticleWithoutScopes::clearBootedModels();
        ArticleWithObjectScope::clearBootedModels();
    }

    /**
     * Setup test data
     */
    private function setupTestData(Driver $driver): void
    {
        // Set as default connection
        Relational::setDefaultConnection($driver);
        // Clear booted state
        ScopedArticle::clearBootedModels();

        // Purge cache
        Query::purgeCache();

        // Clear and reset table
        $driver->truncateTable('scoped_articles');

        // Add additional model clears
        ArticleWithoutScopes::clearBootedModels();
        ArticleWithObjectScope::clearBootedModels();

        // Insert test data
        $articles = [
            ['title' => 'Active Published', 'active' => 1, 'tenant_id' => 1, 'status' => 'published'],
            ['title' => 'Active Draft', 'active' => 1, 'tenant_id' => 1, 'status' => 'draft'],
            ['title' => 'Inactive Published', 'active' => 0, 'tenant_id' => 1, 'status' => 'published'],
            ['title' => 'Inactive Draft', 'active' => 0, 'tenant_id' => 1, 'status' => 'draft'],
            ['title' => 'Other Tenant Active', 'active' => 1, 'tenant_id' => 2, 'status' => 'published'],
            ['title' => 'Other Tenant Inactive', 'active' => 0, 'tenant_id' => 2, 'status' => 'draft'],
        ];

        $query = new Query($driver);
        $query->insertMany('scoped_articles', $articles);
    }

    // =========================================================================
    // Basic Global Scope Tests
    // =========================================================================

    /**
     * Test that model without global scopes returns all rows
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testModelWithoutScopesReturnsAllRows(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $articles = ArticleWithoutScopes::all()->rows();

        $this->assertCount(6, $articles);
    }

    /**
     * Test that addGlobalScope registers a scope
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testAddGlobalScopeRegistersScope(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        ScopedArticle::addGlobalScope('test', function ($query) {
            $query->whereEquals('active', 1);
        });

        $this->assertTrue(ScopedArticle::hasGlobalScope('test'));
    }

    /**
     * Test that global scope is applied to queries
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGlobalScopeIsAppliedToQueries(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // ScopedArticle has 'active' scope that filters active = 1
        $articles = ScopedArticle::all()->rows();

        // Should only return active articles (4 total: 2 tenant 1, 2 tenant 2)
        // But wait - we also have tenant scope, so only tenant 1 active = 2
        // Actually, let's check what the model defines
        $this->assertCount(2, $articles);

        foreach ($articles as $article) {
            $this->assertEquals(1, $article->get('active'));
            $this->assertEquals(1, $article->get('tenant_id'));
        }
    }

    /**
     * Test multiple global scopes are applied
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testMultipleGlobalScopesAreApplied(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // ScopedArticle has both 'active' and 'tenant' scopes
        $articles = ScopedArticle::all()->rows();

        // Should return only active articles from tenant 1
        $this->assertCount(2, $articles);

        foreach ($articles as $article) {
            $this->assertEquals(1, $article->get('active'));
            $this->assertEquals(1, $article->get('tenant_id'));
        }
    }

    // =========================================================================
    // withoutGlobalScopes() Tests
    // =========================================================================

    /**
     * Test withoutGlobalScopes removes all scopes
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWithoutGlobalScopesRemovesAllScopes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Use blank() first to get an instance, then chain
        $articles = ScopedArticle::blank()->withoutGlobalScopes()->rows();

        // Should return all 6 articles
        $this->assertCount(6, $articles);
    }

    /**
     * Test withoutGlobalScopes with specific scopes
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWithoutGlobalScopesWithSpecificScopes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Remove only the 'active' scope, keep 'tenant'
        $articles = ScopedArticle::blank()->withoutGlobalScopes(['active'])->rows();

        // Should return all tenant 1 articles (4 total)
        $this->assertCount(4, $articles);

        foreach ($articles as $article) {
            $this->assertEquals(1, $article->get('tenant_id'));
        }
    }

    /**
     * Test withoutGlobalScope removes single scope
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWithoutGlobalScopeRemovesSingleScope(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Remove only the 'tenant' scope, keep 'active'
        $articles = ScopedArticle::blank()->withoutGlobalScope('tenant')->rows();

        // Should return all active articles from all tenants (3 total)
        $this->assertCount(3, $articles);

        foreach ($articles as $article) {
            $this->assertEquals(1, $article->get('active'));
        }
    }

    /**
     * Test chaining multiple withoutGlobalScope calls
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testChainingMultipleWithoutGlobalScope(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $articles = ScopedArticle::blank()
                                 ->withoutGlobalScope('active')
                                 ->withoutGlobalScope('tenant')
                                 ->rows();

        // Should return all 6 articles
        $this->assertCount(6, $articles);
    }

    // =========================================================================
    // Scope Object Tests
    // =========================================================================

    /**
     * Test object-based global scope
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testObjectBasedGlobalScope(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $articles = ArticleWithObjectScope::all()->rows();

        // PublishedScope filters status = 'published'
        $this->assertCount(3, $articles);

        foreach ($articles as $article) {
            $this->assertEquals('published', $article->get('status'));
        }
    }

    /**
     * Test removing object-based scope by class name
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testRemovingObjectBasedScopeByClassName(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $articles = ArticleWithObjectScope::blank()->withoutGlobalScope(PublishedScope::class)->rows();

        // Should return all 6 articles
        $this->assertCount(6, $articles);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test getRemovedScopes returns correct scopes
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGetRemovedScopesReturnsCorrectScopes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = new ScopedArticle();
        $model->withoutGlobalScope('active');
        $model->withoutGlobalScope('tenant');

        $removed = $model->getRemovedScopes();

        $this->assertContains('active', $removed);
        $this->assertContains('tenant', $removed);
    }

    /**
     * Test getGlobalScopes returns registered scopes
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGetGlobalScopesReturnsRegisteredScopes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Force boot by executing a query (triggers newQuery -> bootIfNotBooted)
        ScopedArticle::all()->rows();

        $scopes = ScopedArticle::getGlobalScopes();

        $this->assertArrayHasKey('active', $scopes);
        $this->assertArrayHasKey('tenant', $scopes);
    }

    /**
     * Test hasGlobalScope returns correct result
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testHasGlobalScopeReturnsCorrectResult(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Force boot by executing a query
        ScopedArticle::all()->rows();

        $this->assertTrue(ScopedArticle::hasGlobalScope('active'));
        $this->assertTrue(ScopedArticle::hasGlobalScope('tenant'));
        $this->assertFalse(ScopedArticle::hasGlobalScope('nonexistent'));
    }

    /**
     * Test clearBootedModels resets state
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testClearBootedModelsResetsState(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Force boot by executing a query
        ScopedArticle::all()->rows();

        $this->assertTrue(ScopedArticle::hasGlobalScope('active'));

        // Clear
        ScopedArticle::clearBootedModels();

        // Scopes should be cleared
        $this->assertFalse(ScopedArticle::hasGlobalScope('active'));
    }

    /**
     * Test that boot is only called once per class
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testBootIsOnlyCalledOnce(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        BootCountArticle::clearBootedModels();
        BootCountArticle::$bootCount = 0;

        // Multiple queries - each needs to trigger newQuery
        BootCountArticle::all()->rows();
        BootCountArticle::all()->rows();
        BootCountArticle::all()->rows();

        // Boot should only be called once
        $this->assertEquals(1, BootCountArticle::$bootCount);
    }

    /**
     * Test scopes work with additional query constraints
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testScopesWorkWithAdditionalConstraints(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // ScopedArticle has active and tenant scopes
        // Add additional constraint for status
        $articles = ScopedArticle::whereEquals('status', 'published')->rows();

        // Should return only active, tenant 1, published articles (1)
        $this->assertCount(1, $articles);

        $article = $articles->first();
        $this->assertEquals('Active Published', $article->get('title'));
    }

    /**
     * Test invalid addGlobalScope throws exception
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testInvalidAddGlobalScopeThrowsException(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $this->expectException(\InvalidArgumentException::class);

        // String without callback should throw
        ScopedArticle::addGlobalScope('invalid');
    }
}
