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
use Hubzero\Database\Traits\Hittable;
use Hubzero\Database\Tests\TestModels\HittableArticle;
use Hubzero\Database\Tests\TestModels\CustomHittableArticle;

/**
 * Hittable trait tests
 *
 * Tests for the Hittable trait that provides hit counting
 * functionality for models.
 */
class HittableTraitTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array Table names
     */
    protected static function getTestTables(): array
    {
        return ['hittable_articles', 'hittable_custom_articles'];
    }

    /**
     * Create test tables and insert seed data for a database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Create test table with hits column
        $driver->dropTable('hittable_articles', true);
        $driver->createTable('hittable_articles')
            ->id()
            ->string('title', 255)
            ->integer('state')->default(1)
            ->integer('hits')->default(0)
            ->execute();

        // Create table with custom column name
        $driver->dropTable('hittable_custom_articles', true);
        $driver->createTable('hittable_custom_articles')
            ->id()
            ->string('title', 255)
            ->integer('view_count')->default(0)
            ->execute();
    }

    /**
     * Setup test data before each test
     */
    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        // Clear booted state
        HittableArticle::clearBootedModels();
        CustomHittableArticle::clearBootedModels();

        Query::purgeCache();

        // Clear and reset tables
        $driver->truncateTable('hittable_articles');
        $driver->truncateTable('hittable_custom_articles');
    }

    // =========================================================================
    // Basic Hit Tests
    // =========================================================================

    /**
     * Test getHits() returns current value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetHitsReturnsCurrentValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 42, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);

        $this->assertEquals(42, $article->getHits());
    }

    /**
     * Test getHits() returns 0 for new record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetHitsReturnsZeroForNewRecord(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 0, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);

        $this->assertEquals(0, $article->getHits());
    }

    /**
     * Test hit() increments by 1 by default
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHitIncrementsByOneByDefault(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $result = $article->hit();

        $this->assertTrue($result);
        $this->assertEquals(11, $article->getHits());

        // Verify in database by reloading
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(11, $reloaded->getHits());
    }

    /**
     * Test hit() with custom amount
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHitWithCustomAmount(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $result = $article->hit(5);

        $this->assertTrue($result);
        $this->assertEquals(15, $article->getHits());

        // Verify in database by reloading
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(15, $reloaded->getHits());
    }

    /**
     * Test hit() with zero or negative amount returns true without changing
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHitWithZeroOrNegativeAmountReturnsTrue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);

        $this->assertTrue($article->hit(0));
        $this->assertEquals(10, $article->getHits());

        $this->assertTrue($article->hit(-5));
        $this->assertEquals(10, $article->getHits());
    }

    /**
     * Test multiple hits accumulate correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleHitsAccumulate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 0, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $article->hit();
        $article->hit();
        $article->hit();

        $this->assertEquals(3, $article->getHits());
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(3, $reloaded->getHits());
    }

    // =========================================================================
    // Set/Reset Hits Tests
    // =========================================================================

    /**
     * Test setHits() sets exact value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetHitsSetsExactValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $result = $article->setHits(100);

        $this->assertTrue($result);
        $this->assertEquals(100, $article->getHits());
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(100, $reloaded->getHits());
    }

    /**
     * Test setHits() with negative value sets to 0
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetHitsWithNegativeValueSetsToZero(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $result = $article->setHits(-5);

        $this->assertTrue($result);
        $this->assertEquals(0, $article->getHits());
    }

    /**
     * Test resetHits() sets to 0
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testResetHitsSetsToZero(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 100, 'state' => 1]]);
        $id = 1;

        $article = HittableArticle::one($id);
        $result = $article->resetHits();

        $this->assertTrue($result);
        $this->assertEquals(0, $article->getHits());
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(0, $reloaded->getHits());
    }

    // =========================================================================
    // Static Method Tests
    // =========================================================================

    /**
     * Test incrementHits() static method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIncrementHitsStaticMethod(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $result = HittableArticle::incrementHits($id);

        $this->assertTrue($result);
        // Verify in database by reloading
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(11, $reloaded->getHits());
    }

    /**
     * Test incrementHits() with custom amount
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIncrementHitsStaticMethodWithCustomAmount(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        $result = HittableArticle::incrementHits($id, 5);

        $this->assertTrue($result);
        // Verify in database by reloading
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(15, $reloaded->getHits());
    }

    /**
     * Test mostViewed() returns records ordered by hits
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMostViewedReturnsRecordsOrderedByHits(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [
            ['title' => 'Low Hits', 'hits' => 10, 'state' => 1],
            ['title' => 'High Hits', 'hits' => 100, 'state' => 1],
            ['title' => 'Medium Hits', 'hits' => 50, 'state' => 1]
        ]);

        $rows = HittableArticle::mostViewed(10);

        $this->assertCount(3, $rows);

        // Collect titles in order
        $titles = [];
        foreach ($rows as $row) {
            $titles[] = $row->title;
        }

        $this->assertEquals(['High Hits', 'Medium Hits', 'Low Hits'], $titles);
    }

    /**
     * Test mostViewed() respects limit
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMostViewedRespectsLimit(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [
            ['title' => 'Article 1', 'hits' => 10, 'state' => 1],
            ['title' => 'Article 2', 'hits' => 20, 'state' => 1],
            ['title' => 'Article 3', 'hits' => 30, 'state' => 1],
            ['title' => 'Article 4', 'hits' => 40, 'state' => 1],
            ['title' => 'Article 5', 'hits' => 50, 'state' => 1]
        ]);

        $rows = HittableArticle::mostViewed(3);

        $this->assertCount(3, $rows);
    }

    /**
     * Test mostViewed() with where conditions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMostViewedWithWhereConditions(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [
            ['title' => 'Published 1', 'hits' => 100, 'state' => 1],
            ['title' => 'Published 2', 'hits' => 50, 'state' => 1],
            ['title' => 'Unpublished', 'hits' => 200, 'state' => 0]
        ]);

        $rows = HittableArticle::mostViewed(10, ['state' => 1]);

        $this->assertCount(2, $rows);
        // Unpublished article with highest hits should be excluded
        foreach ($rows as $row) {
            $this->assertNotEquals('Unpublished', $row->title);
        }
    }

    /**
     * Test totalHits() returns sum of all hits
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTotalHitsReturnsSumOfAllHits(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [
            ['title' => 'Article 1', 'hits' => 10, 'state' => 1],
            ['title' => 'Article 2', 'hits' => 20, 'state' => 1],
            ['title' => 'Article 3', 'hits' => 30, 'state' => 1]
        ]);

        $total = HittableArticle::totalHits();

        $this->assertEquals(60, $total);
    }

    /**
     * Test totalHits() with where conditions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTotalHitsWithWhereConditions(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [
            ['title' => 'Published 1', 'hits' => 10, 'state' => 1],
            ['title' => 'Published 2', 'hits' => 20, 'state' => 1],
            ['title' => 'Unpublished', 'hits' => 100, 'state' => 0]
        ]);

        $total = HittableArticle::totalHits(['state' => 1]);

        $this->assertEquals(30, $total); // Only published articles
    }

    /**
     * Test totalHits() returns 0 for empty table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTotalHitsReturnsZeroForEmptyTable(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $total = HittableArticle::totalHits();

        $this->assertEquals(0, $total);
    }

    // =========================================================================
    // Column Configuration Tests
    // =========================================================================

    /**
     * Test getHitsColumn() returns correct column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetHitsColumnReturnsCorrectColumnName(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new HittableArticle();
        $this->assertEquals('hits', $article->getHitsColumn());

        $customArticle = new CustomHittableArticle();
        $this->assertEquals('view_count', $customArticle->getHitsColumn());
    }

    /**
     * Test custom column name works correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomColumnNameWorksCorrectly(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_custom_articles', [['title' => 'Custom Article', 'view_count' => 25]]);
        $id = 1;

        $article = CustomHittableArticle::one($id);

        $this->assertEquals(25, $article->getHits());

        $article->hit();
        $this->assertEquals(26, $article->getHits());

        // Verify in database by reloading
        $reloaded = CustomHittableArticle::one($id);
        $this->assertEquals(26, $reloaded->getHits());
    }

    // =========================================================================
    // Concurrent Update Safety
    // =========================================================================

    /**
     * Test that hit() uses atomic increment
     *
     * Note: This test verifies the SQL uses atomic increment syntax,
     * not actual concurrent behavior which would require threading.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHitUsesAtomicIncrement(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('hittable_articles', [['title' => 'Test Article', 'hits' => 10, 'state' => 1]]);
        $id = 1;

        // Set initial value in DB
        (new Query($driver))->update('hittable_articles')->set(['hits' => 50])->where('id', '=', $id)->execute();

        // Load article (has old value in memory)
        $article = HittableArticle::one($id);

        // Simulate another process updating hits
        (new Query($driver))->update('hittable_articles')->set(['hits' => 55])->where('id', '=', $id)->execute();

        // Now do our hit - should increment from 55 (current DB value), not 50
        $article->hit();

        // Database should have 56 (55 + 1), not 51 (50 + 1)
        $reloaded = HittableArticle::one($id);
        $this->assertEquals(56, $reloaded->getHits());
    }
}
