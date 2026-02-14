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
use Hubzero\Database\Tests\TestModels\HookTestItem;
use Hubzero\Database\Tests\TestModels\HookTestItemWithSoftDeletes;
use Hubzero\Database\Tests\TestModels\LifecycleEventStub;

/**
 * Model Lifecycle Hooks tests
 *
 * Tests for the lifecycle event system that fires callbacks at specific
 * points during create/update/delete operations.
 */
class LifecycleHooksTest extends AbstractDriverTestCase
{
    /**
     * Track callback executions for assertions
     *
     * @var array
     */
    private static $callbackLog = [];

    protected static function getTestTables(): array
    {
        return ['hook_test_items'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('hook_test_items', true);
        $driver->createTable('hook_test_items')
            ->id()
            ->string('title', 255)
            ->string('slug', 255)->nullable()
            ->integer('status')->default(1)
            ->string('deleted_at', 50)->nullable()
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        // Register Event stub for SoftDeletes trait (requires global Event class)
        if (!class_exists('Event')) {
            class_alias(LifecycleEventStub::class, 'Event');
        }

        Relational::setDefaultConnection($driver);
        HookTestItem::clearBootedModels();
        HookTestItemWithSoftDeletes::clearBootedModels();
        Query::purgeCache();

        // Clear callback log
        self::$callbackLog = [];

        $driver->truncateTable('hook_test_items');
    }

    /**
     * Helper to log callback execution
     */
    public static function logCallback($event, $model = null)
    {
        self::$callbackLog[] = [
            'event' => $event,
            'model_id' => $model ? $model->get('id') : null,
            'model_title' => $model ? $model->get('title') : null,
        ];
    }

    /**
     * Helper to create a test record directly (without hooks)
     */
    private function createTestRecord(Driver $driver, string $title, string $deletedAt = null): int
    {
        $query = new Query($driver);
        $query->insertMany('hook_test_items', [
            ['title' => $title, 'slug' => null, 'status' => 1, 'deleted_at' => $deletedAt],
        ]);

        // Get the ID by querying back
        $query = new Query($driver);
        $id = $query->select('id')
            ->from('hook_test_items')
            ->whereEquals('title', $title)
            ->fetch('column');

        return (int)$id[0];
    }

    // =========================================================================
    // Creating/Created Event Tests
    // =========================================================================

    /**
     * Test that creating event fires before insert
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCreatingEventFiresBeforeInsert(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(function ($model) {
            self::logCallback('creating', $model);
            // Set slug from title
            $model->set('slug', strtolower(str_replace(' ', '-', $model->get('title'))));
        });

        $item = HookTestItem::blank();
        $item->set('title', 'Test Title');
        $item->save();

        // Verify creating was called
        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'creating'));

        // Verify slug was set by callback
        Query::purgeCache();
        $reloaded = HookTestItem::one($item->get('id'));
        $this->assertEquals('test-title', $reloaded->get('slug'));
    }

    /**
     * Test that creating event can cancel the operation
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCreatingEventCanCancelOperation(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(function ($model) {
            self::logCallback('creating', $model);
            return false; // Cancel
        });

        $item = HookTestItem::blank();
        $item->set('title', 'Should Not Save');
        $result = $item->save();

        $this->assertFalse($result);

        // Verify no record was created
        $query = new Query($driver);
        $count = $query->from('hook_test_items')->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Test that created event fires after insert
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCreatedEventFiresAfterInsert(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::created(function ($model) {
            self::logCallback('created', $model);
        });

        $item = HookTestItem::blank();
        $item->set('title', 'Test Item');
        $item->save();

        // Verify created was called with the model's ID
        $createdLogs = array_filter(self::$callbackLog, fn($log) => $log['event'] === 'created');
        $this->assertCount(1, $createdLogs);

        $log = array_values($createdLogs)[0];
        $this->assertNotNull($log['model_id']);
    }

    // =========================================================================
    // Updating/Updated Event Tests
    // =========================================================================

    /**
     * Test that updating event fires before update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdatingEventFiresBeforeUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Original Title');

        HookTestItem::updating(function ($model) {
            self::logCallback('updating', $model);
        });

        $item = HookTestItem::one($id);
        $item->set('title', 'Updated Title');
        $item->save();

        // Verify updating was called
        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'updating'));
    }

    /**
     * Test that updating event can cancel the operation
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdatingEventCanCancelOperation(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Original Title');

        HookTestItem::updating(function ($model) {
            self::logCallback('updating', $model);
            return false; // Cancel
        });

        $item = HookTestItem::one($id);
        $item->set('title', 'Should Not Update');
        $result = $item->save();

        $this->assertFalse($result);

        // Verify title was not changed
        Query::purgeCache();
        $reloaded = HookTestItem::one($id);
        $this->assertEquals('Original Title', $reloaded->get('title'));
    }

    /**
     * Test that updated event fires after update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdatedEventFiresAfterUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Original Title');

        HookTestItem::updated(function ($model) {
            self::logCallback('updated', $model);
        });

        $item = HookTestItem::one($id);
        $item->set('title', 'Updated Title');
        $item->save();

        // Verify updated was called
        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'updated'));
    }

    // =========================================================================
    // Saving/Saved Event Tests
    // =========================================================================

    /**
     * Test that saving event fires for both create and update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSavingEventFiresForBothCreateAndUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::saving(function ($model) {
            self::logCallback('saving', $model);
        });

        // Create
        $item = HookTestItem::blank();
        $item->set('title', 'New Item');
        $item->save();

        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'saving'));

        // Update
        $item->set('title', 'Updated Item');
        $item->save();

        $this->assertCount(2, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'saving'));
    }

    /**
     * Test that saving event can cancel the operation
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSavingEventCanCancelOperation(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::saving(function ($model) {
            self::logCallback('saving', $model);
            return false; // Cancel
        });

        $item = HookTestItem::blank();
        $item->set('title', 'Should Not Save');
        $result = $item->save();

        $this->assertFalse($result);
    }

    /**
     * Test that saved event fires after both create and update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSavedEventFiresForBothCreateAndUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::saved(function ($model) {
            self::logCallback('saved', $model);
        });

        // Create
        $item = HookTestItem::blank();
        $item->set('title', 'New Item');
        $item->save();

        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'saved'));

        // Update
        $item->set('title', 'Updated Item');
        $item->save();

        $this->assertCount(2, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'saved'));
    }

    // =========================================================================
    // Deleting/Deleted Event Tests
    // =========================================================================

    /**
     * Test that deleting event fires before delete
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeletingEventFiresBeforeDelete(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'To Delete');

        HookTestItem::deleting(function ($model) {
            self::logCallback('deleting', $model);
        });

        $item = HookTestItem::one($id);
        $item->destroy();

        // Verify deleting was called
        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'deleting'));
    }

    /**
     * Test that deleting event can cancel the operation
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeletingEventCanCancelOperation(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Protected Item');

        HookTestItem::deleting(function ($model) {
            self::logCallback('deleting', $model);
            return false; // Cancel
        });

        $item = HookTestItem::one($id);
        $result = $item->destroy();

        $this->assertFalse($result);

        // Verify record still exists
        $query = new Query($driver);
        $count = $query->from('hook_test_items')->whereEquals('id', $id)->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test that deleted event fires after delete
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeletedEventFiresAfterDelete(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'To Delete');

        HookTestItem::deleted(function ($model) {
            self::logCallback('deleted', $model);
        });

        $item = HookTestItem::one($id);
        $item->destroy();

        // Verify deleted was called
        $this->assertCount(1, array_filter(self::$callbackLog, fn($log) => $log['event'] === 'deleted'));
    }

    // =========================================================================
    // Event Order Tests
    // =========================================================================

    /**
     * Test correct event order for create
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCorrectEventOrderForCreate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::saving(fn($m) => self::logCallback('saving', $m));
        HookTestItem::creating(fn($m) => self::logCallback('creating', $m));
        HookTestItem::created(fn($m) => self::logCallback('created', $m));
        HookTestItem::saved(fn($m) => self::logCallback('saved', $m));

        $item = HookTestItem::blank();
        $item->set('title', 'Test Order');
        $item->save();

        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['saving', 'creating', 'created', 'saved'], $events);
    }

    /**
     * Test correct event order for update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCorrectEventOrderForUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Original');

        HookTestItem::saving(fn($m) => self::logCallback('saving', $m));
        HookTestItem::updating(fn($m) => self::logCallback('updating', $m));
        HookTestItem::updated(fn($m) => self::logCallback('updated', $m));
        HookTestItem::saved(fn($m) => self::logCallback('saved', $m));

        $item = HookTestItem::one($id);
        $item->set('title', 'Updated');
        $item->save();

        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['saving', 'updating', 'updated', 'saved'], $events);
    }

    // =========================================================================
    // Multiple Callbacks Tests
    // =========================================================================

    /**
     * Test multiple callbacks for same event
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleCallbacksForSameEvent(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(fn($m) => self::logCallback('creating-1', $m));
        HookTestItem::creating(fn($m) => self::logCallback('creating-2', $m));
        HookTestItem::creating(fn($m) => self::logCallback('creating-3', $m));

        $item = HookTestItem::blank();
        $item->set('title', 'Test Multiple');
        $item->save();

        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['creating-1', 'creating-2', 'creating-3'], $events);
    }

    /**
     * Test that first callback returning false stops chain
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFirstCallbackReturningFalseStopsChain(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(fn($m) => self::logCallback('creating-1', $m));
        HookTestItem::creating(function ($m) {
            self::logCallback('creating-2', $m);
            return false;
        });
        HookTestItem::creating(fn($m) => self::logCallback('creating-3', $m));

        $item = HookTestItem::blank();
        $item->set('title', 'Test Chain Stop');
        $result = $item->save();

        $this->assertFalse($result);

        // Only first two callbacks should have run
        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['creating-1', 'creating-2'], $events);
    }

    // =========================================================================
    // Soft Delete Hooks Tests
    // Note: These tests are skipped because they require the Event facade which
    // is not available in standalone tests. The soft delete trait triggers
    // framework events that require full application bootstrap.
    // =========================================================================

    /**
     * Test that deleting/deleted lifecycle hooks fire for soft delete
     *
     * Note: Soft delete also triggers Event::trigger('system.onContentSoftDelete')
     * which requires full framework bootstrap. This test only verifies the
     * model lifecycle hooks work.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDeletingDeletedLifecycleHooksFireForSoftDelete(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Soft Delete Test');

        HookTestItemWithSoftDeletes::deleting(fn($m) => self::logCallback('deleting', $m));
        HookTestItemWithSoftDeletes::deleted(fn($m) => self::logCallback('deleted', $m));

        $item = HookTestItemWithSoftDeletes::one($id);
        $item->destroy();

        // Verify lifecycle hooks were called
        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['deleting', 'deleted'], $events);

        // Verify it was soft deleted
        $query = new Query($driver);
        $deletedAt = $query->select('deleted_at')
            ->from('hook_test_items')
            ->whereEquals('id', $id)
            ->fetch('column');
        $this->assertNotNull($deletedAt[0]);
    }

    /**
     * Test that restoring/restored lifecycle hooks fire
     *
     * Note: Restore also triggers Event::trigger('system.onContentRestore')
     * which requires full framework bootstrap. This test only verifies the
     * model lifecycle hooks work.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRestoringRestoredLifecycleHooksFire(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Restore Test', '2020-01-01 00:00:00');

        HookTestItemWithSoftDeletes::restoring(fn($m) => self::logCallback('restoring', $m));
        HookTestItemWithSoftDeletes::restored(fn($m) => self::logCallback('restored', $m));

        $item = HookTestItemWithSoftDeletes::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();

        $item->restore();

        // Verify lifecycle hooks were called
        $events = array_column(self::$callbackLog, 'event');
        $this->assertEquals(['restoring', 'restored'], $events);

        // Verify it was restored
        $query = new Query($driver);
        $deletedAt = $query->select('deleted_at')
            ->from('hook_test_items')
            ->whereEquals('id', $id)
            ->fetch('column');
        $this->assertNull($deletedAt[0]);
    }

    /**
     * Test that restoring lifecycle hook can cancel restore
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRestoringLifecycleHookCanCancelRestore(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $id = $this->createTestRecord($driver, 'Protected Trash', '2020-01-01 00:00:00');

        HookTestItemWithSoftDeletes::restoring(function ($m) {
            self::logCallback('restoring', $m);
            return false; // Cancel
        });

        $item = HookTestItemWithSoftDeletes::blank()->withTrashed()
            ->whereEquals('id', $id)
            ->row();

        $result = $item->restore();

        $this->assertFalse($result);

        // Verify still trashed
        $query = new Query($driver);
        $deletedAt = $query->select('deleted_at')
            ->from('hook_test_items')
            ->whereEquals('id', $id)
            ->fetch('column');
        $this->assertNotNull($deletedAt[0]);
    }

    // =========================================================================
    // Registration Method Tests
    // =========================================================================

    /**
     * Test getModelEvents returns registered events
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetModelEventsReturnsRegisteredEvents(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(fn($m) => null);
        HookTestItem::created(fn($m) => null);
        HookTestItem::updating(fn($m) => null);

        $events = HookTestItem::getModelEvents();

        $this->assertArrayHasKey('creating', $events);
        $this->assertArrayHasKey('created', $events);
        $this->assertArrayHasKey('updating', $events);
        $this->assertArrayNotHasKey('deleting', $events);
    }

    /**
     * Test hasModelEvent returns correct result
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHasModelEventReturnsCorrectResult(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(fn($m) => null);

        $this->assertTrue(HookTestItem::hasModelEvent('creating'));
        $this->assertFalse(HookTestItem::hasModelEvent('deleting'));
    }

    /**
     * Test clearBootedModels clears events
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testClearBootedModelsClearsEvents(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        HookTestItem::creating(fn($m) => null);
        $this->assertTrue(HookTestItem::hasModelEvent('creating'));

        HookTestItem::clearBootedModels();

        $this->assertFalse(HookTestItem::hasModelEvent('creating'));
    }
}
