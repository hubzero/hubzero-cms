<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\TouchTestItem;

/**
 * Tests for the touch() method on Relational models
 *
 * The touch() method updates timestamp columns without modifying
 * other model attributes, useful for tracking last access times.
 */
class TouchMethodTest extends AbstractDriverTestCase
{
    /**
     * Return table names for automatic cleanup
     */
    protected static function getTestTables(): array
    {
        return ['touch_test_items'];
    }

    /**
     * Create test tables
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('touch_test_items', true);

        $schema = $driver->schema();
        $schema->createTable('touch_test_items')
            ->id()
            ->string('title', 255)
            ->string('content', 500)->nullable()
            ->datetime('modified')->nullable()
            ->datetime('last_viewed_at')->nullable()
            ->datetime('created')->nullable()
            ->execute();
    }

    // =========================================================================
    // Basic Touch Tests
    // =========================================================================

    /**
     * Test that touch() updates the default 'modified' column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchUpdatesModifiedColumn(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = []; // Clear column cache

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);
        $originalModified = $item->get('modified');

        // Small delay to ensure timestamp differs
        usleep(10000); // 10ms

        $result = $item->touch();

        $this->assertTrue($result);

        // Purge query cache and reload from database to verify
        Query::purgeCache();
        TouchTestItem::$columns = []; // Clear column cache again
        $reloaded = TouchTestItem::one($id);
        $this->assertNotEquals($originalModified, $reloaded->get('modified'));

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() updates a custom column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchUpdatesCustomColumn(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);
        $this->assertNull($item->get('last_viewed_at'));

        $result = $item->touch('last_viewed_at');

        $this->assertTrue($result);

        // Purge query cache and reload from database to verify
        Query::purgeCache();
        TouchTestItem::$columns = [];
        $reloaded = TouchTestItem::one($id);
        $this->assertNotNull($reloaded->get('last_viewed_at'));

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() returns false for non-existent column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchReturnsFalseForNonExistentColumn(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);
        $result = $item->touch('nonexistent_column');

        $this->assertFalse($result);

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() returns false for new (unsaved) models
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchReturnsFalseForNewModel(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $item = new TouchTestItem();
        $item->set('title', 'New Item');

        $result = $item->touch();

        $this->assertFalse($result);

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() doesn't modify other columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchDoesNotModifyOtherColumns(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Original Title');

        $item = TouchTestItem::one($id);
        $originalTitle = $item->get('title');
        $originalContent = $item->get('content');

        $item->touch();

        // Purge query cache and reload from database
        Query::purgeCache();
        TouchTestItem::$columns = [];
        $reloaded = TouchTestItem::one($id);

        $this->assertEquals($originalTitle, $reloaded->get('title'));
        $this->assertEquals($originalContent, $reloaded->get('content'));

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() updates the local model attribute
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchUpdatesLocalAttribute(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);
        $originalModified = $item->get('modified');

        usleep(10000); // 10ms

        $item->touch();

        // The local attribute should be updated without reloading
        $this->assertNotEquals($originalModified, $item->get('modified'));

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test that touch() syncs original to prevent dirty state
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchSyncsOriginalAttribute(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);
        $item->touch();

        // The touched column should not be marked as dirty
        $dirty = $item->getDirty();
        $this->assertArrayNotHasKey('modified', $dirty);

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test multiple sequential touches
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleSequentialTouches(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);

        $timestamps = [];
        for ($i = 0; $i < 3; $i++) {
            usleep(10000); // 10ms
            $item->touch();
            $timestamps[] = $item->get('modified');
        }

        // Each timestamp should be different (or at least non-decreasing)
        $this->assertGreaterThanOrEqual($timestamps[0], $timestamps[1]);
        $this->assertGreaterThanOrEqual($timestamps[1], $timestamps[2]);

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    /**
     * Test touch on different columns sequentially
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function touchDifferentColumnsSequentially(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);
        TouchTestItem::setDefaultConnection($driver);
        TouchTestItem::$columns = [];

        $id = $this->insertTestRecord($driver, 'Test Item');

        $item = TouchTestItem::one($id);

        $item->touch('modified');
        $item->touch('last_viewed_at');

        // Purge query cache and reload to verify both are set
        Query::purgeCache();
        TouchTestItem::$columns = [];
        $reloaded = TouchTestItem::one($id);
        $this->assertNotNull($reloaded->get('modified'));
        $this->assertNotNull($reloaded->get('last_viewed_at'));

        // Cleanup
        $driver->dropTable('touch_test_items', true);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test table
     */
    private function setupTable(Driver $driver): void
    {
        $driver->dropTable('touch_test_items', true);

        $schema = $driver->schema();
        $schema->createTable('touch_test_items')
            ->id()
            ->string('title', 255)
            ->string('content', 500)->nullable()
            ->datetime('modified')->nullable()
            ->datetime('last_viewed_at')->nullable()
            ->datetime('created')->nullable()
            ->execute();
    }

    /**
     * Insert test record
     */
    private function insertTestRecord(Driver $driver, string $title): int
    {
        $modified = '2020-01-01 00:00:00';

        $query = new Query($driver);
        $query->insert('touch_test_items')
              ->values([
                  'title' => $title,
                  'content' => 'Test content',
                  'modified' => $modified,
                  'created' => $modified
              ])
              ->execute();

        return $driver->insertid();
    }
}
