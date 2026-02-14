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
use Hubzero\Database\Traits\Lockable;
use Hubzero\Database\Tests\TestModels\LockableArticle;
use Hubzero\Database\Tests\TestModels\CustomLockableArticle;

/**
 * Lockable trait tests
 *
 * Tests for the Lockable trait that provides checkout/checkin (row locking)
 * functionality for models.
 */
class LockableTraitTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['lockable_custom_articles', 'lockable_articles'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('lockable_articles', true);
        $driver->createTable('lockable_articles')
            ->id()
            ->string('title', 255)
            ->string('content', 500)->nullable()
            ->integer('checked_out')->nullable()
            ->string('checked_out_time', 50)->nullable()
            ->execute();

        $driver->dropTable('lockable_custom_articles', true);
        $driver->createTable('lockable_custom_articles')
            ->id()
            ->string('title', 255)
            ->integer('locked_by')->nullable()
            ->string('locked_at', 50)->nullable()
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        LockableArticle::clearBootedModels();
        CustomLockableArticle::clearBootedModels();
        Query::purgeCache();

        $driver->truncateTable('lockable_articles');
        $driver->truncateTable('lockable_custom_articles');
    }

    // =========================================================================
    // Basic Checkout/Checkin Tests
    // =========================================================================

    /**
     * Test that checkout() sets the user ID and timestamp
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckoutSetsUserIdAndTimestamp(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => null, 'checked_out_time' => null,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);
        $this->assertNull($article->get('checked_out'));
        $this->assertNull($article->get('checked_out_time'));

        $result = $article->checkout(42);

        $this->assertTrue($result);
        $this->assertEquals(42, $article->get('checked_out'));
        $this->assertNotNull($article->get('checked_out_time'));

        // Verify in database by reloading
        $reloaded = LockableArticle::one($id);
        $this->assertEquals(42, $reloaded->get('checked_out'));
        $this->assertNotEmpty($reloaded->get('checked_out_time'));
    }

    /**
     * Test that checkin() clears the lock
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckinClearsLock(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => '2024-01-15 10:00:00',
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);
        $this->assertEquals(42, $article->get('checked_out'));

        $result = $article->checkin();

        $this->assertTrue($result);
        $this->assertNull($article->get('checked_out'));
        $this->assertNull($article->get('checked_out_time'));

        // Verify in database by reloading
        $reloaded = LockableArticle::one($id);
        $this->assertNull($reloaded->get('checked_out'));
        $this->assertNull($reloaded->get('checked_out_time'));
    }

    /**
     * Test that checkin() on unlocked record returns true
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckinOnUnlockedRecordReturnsTrue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => null, 'checked_out_time' => null,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);
        $result = $article->checkin();

        $this->assertTrue($result);
    }

    /**
     * Test that checkout() fails with invalid user ID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckoutFailsWithInvalidUserId(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => null, 'checked_out_time' => null,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        $this->assertFalse($article->checkout(0));
        $this->assertFalse($article->checkout(-1));
    }

    // =========================================================================
    // isCheckedOut() Tests
    // =========================================================================

    /**
     * Test that isCheckedOut() returns false for unlocked record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsCheckedOutReturnsFalseForUnlockedRecord(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => null, 'checked_out_time' => null,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        $this->assertFalse($article->isCheckedOut(1));
    }

    /**
     * Test that isCheckedOut() returns false when same user has lock
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsCheckedOutReturnsFalseForSameUser(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        // Same user (42) should not be blocked
        $this->assertFalse($article->isCheckedOut(42));
    }

    /**
     * Test that isCheckedOut() returns true for different user
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsCheckedOutReturnsTrueForDifferentUser(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        // Different user (99) should be blocked
        $this->assertTrue($article->isCheckedOut(99));
    }

    /**
     * Test that checkout() fails when locked by another user
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckoutFailsWhenLockedByAnotherUser(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        // User 99 cannot checkout when user 42 has it
        $this->assertFalse($article->checkout(99));
    }

    /**
     * Test that same user can re-checkout
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSameUserCanReCheckout(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $time = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $time,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);
        $oldTime = $article->get('checked_out_time');

        // Same user can re-checkout
        $result = $article->checkout(42);

        $this->assertTrue($result);
        // Time should be updated
        $this->assertNotEquals($oldTime, $article->get('checked_out_time'));
    }

    // =========================================================================
    // Lock Expiration Tests
    // =========================================================================

    /**
     * Test that expired lock allows checkout by other user
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testExpiredLockAllowsCheckoutByOtherUser(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Create lock from 3 hours ago (default timeout is 2 hours)
        $expiredTime = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $expiredTime,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        // Lock is expired, so other user should be able to check out
        $this->assertFalse($article->isCheckedOut(99));
        $this->assertTrue($article->checkout(99));
    }

    /**
     * Test that isLockExpired() returns correct value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsLockExpiredReturnsCorrectValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Fresh lock
        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Fresh Lock', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $id1 = 1;
        $article1 = LockableArticle::one($id1);
        $this->assertFalse($article1->isLockExpired());

        // Expired lock (3 hours old)
        $query = new Query($driver);
        $time = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $query->insertMany('lockable_articles', [[
            'title' => 'Old Lock', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $time,
        ]]);
        $id2 = 2; // Second insert gets ID=2
        $article2 = LockableArticle::one($id2);
        $this->assertTrue($article2->isLockExpired());
    }

    // =========================================================================
    // Getter Tests
    // =========================================================================

    /**
     * Test getCheckedOutUserId()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetCheckedOutUserId(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        $this->assertEquals(42, $article->getCheckedOutUserId());
    }

    /**
     * Test getCheckedOutUserId() returns null for unlocked record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetCheckedOutUserIdReturnsNullForUnlockedRecord(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => null, 'checked_out_time' => null,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        $this->assertNull($article->getCheckedOutUserId());
    }

    /**
     * Test getCheckedOutTime()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetCheckedOutTime(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $time = '2024-01-15 10:30:00';
        $query = new Query($driver);
        $query->insertMany('lockable_articles', [[
            'title' => 'Test Article', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $time,
        ]]);
        $id = 1;

        $article = LockableArticle::one($id);

        $this->assertEquals($time, $article->getCheckedOutTime());
    }

    // =========================================================================
    // Static Method Tests
    // =========================================================================

    /**
     * Test checkinByUser() releases all locks by a user
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckinByUserReleasesAllUserLocks(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Article 1', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Article 2', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        // Different user
        $query->insertMany('lockable_articles', [[
            'title' => 'Article 3', 'content' => 'Test content',
            'checked_out' => 99, 'checked_out_time' => $now,
        ]]);

        $result = LockableArticle::checkinByUser(42);

        // Result is truthy (method succeeded)
        $this->assertGreaterThanOrEqual(1, $result);

        // Verify user 42's locks are cleared but user 99's remains
        $user42Locks = LockableArticle::blank()
            ->whereEquals('checked_out', 42)->total();
        $this->assertEquals(0, $user42Locks);

        $user99Locks = LockableArticle::blank()
            ->whereEquals('checked_out', 99)->total();
        $this->assertEquals(1, $user99Locks);
    }

    /**
     * Test checkinExpired() releases only expired locks
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCheckinExpiredReleasesOnlyExpiredLocks(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $now = date('Y-m-d H:i:s');
        $query->insertMany('lockable_articles', [[
            'title' => 'Fresh Lock', 'content' => 'Test content',
            'checked_out' => 42, 'checked_out_time' => $now,
        ]]);
        $query = new Query($driver);
        $time = date('Y-m-d H:i:s', strtotime('-3 hours'));
        $query->insertMany('lockable_articles', [[
            'title' => 'Expired Lock 1', 'content' => 'Test content',
            'checked_out' => 99, 'checked_out_time' => $time,
        ]]);
        $query = new Query($driver);
        $time = date('Y-m-d H:i:s', strtotime('-4 hours'));
        $query->insertMany('lockable_articles', [[
            'title' => 'Expired Lock 2', 'content' => 'Test content',
            'checked_out' => 100, 'checked_out_time' => $time,
        ]]);

        $result = LockableArticle::checkinExpired();

        // Result is truthy (method succeeded)
        $this->assertGreaterThanOrEqual(1, $result);

        // Verify fresh lock remains but expired ones are cleared
        $lockedCount = LockableArticle::blank()
            ->whereEquals('checked_out', 42)->total();
        $this->assertEquals(1, $lockedCount);

        $unlockedCount = LockableArticle::blank()
            ->whereIsNull('checked_out')->total();
        $this->assertEquals(2, $unlockedCount);
    }

    // =========================================================================
    // Custom Column Tests
    // =========================================================================

    /**
     * Test custom column names work correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomColumnNames(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Insert directly with custom columns
        $query = new Query($driver);
        $query->insertMany('lockable_custom_articles', [[
            'title' => 'Custom', 'locked_by' => null, 'locked_at' => null,
        ]]);
        $id = 1;

        $article = CustomLockableArticle::one($id);

        $article->checkout(42);

        $this->assertEquals(42, $article->get('locked_by'));
        $this->assertNotNull($article->get('locked_at'));

        $article->checkin();

        $this->assertNull($article->get('locked_by'));
        $this->assertNull($article->get('locked_at'));
    }

    /**
     * Test getLockTimeout() returns correct value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetLockTimeoutReturnsCorrectValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new LockableArticle();
        // Default 2 hours
        $this->assertEquals(7200, $article->getLockTimeout());
    }
}
