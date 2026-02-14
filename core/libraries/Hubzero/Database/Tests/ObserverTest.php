<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\ObserverTestItem;
use Hubzero\Database\Tests\TestModels\TestObserver;
use Hubzero\Database\Tests\TestModels\SecondTestObserver;
use Hubzero\Database\Tests\TestModels\CancelingObserver;
use Hubzero\Database\Tests\TestModels\ModifyingObserver;
use Hubzero\Database\Tests\TestModels\PartialObserver;

/**
 * Model Observer tests
 *
 * Tests for the observe() method that allows registering observer classes
 * to handle model lifecycle events.
 */
class ObserverTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['observer_test_items'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('observer_test_items', true);

        $driver->createTable('observer_test_items')
            ->id()
            ->string('title', 255)
            ->string('slug', 255)->nullable()
            ->text('content')->nullable()
            ->integer('status')->default(1)
            ->string('created', 50)->nullable()
            ->string('modified', 50)->nullable()
            ->execute();
    }

    /**
     * Reset test state for each test method
     */
    private function resetTestState(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();
        (new Query($driver))->delete('observer_test_items')->execute();
        TestObserver::reset();
        SecondTestObserver::reset();
        CancelingObserver::reset();
        ObserverTestItem::flushEventListeners();
    }

    /**
     * Insert a test record and return its ID
     */
    protected function insertTestRecord(Driver $driver, $title = 'Test Item')
    {
        $now = date('Y-m-d H:i:s');
        $query = new Query($driver);
        $query->insertMany('observer_test_items', [[
            'title' => $title,
            'slug' => 'test-item',
            'content' => 'Content',
            'status' => 1,
            'created' => $now,
            'modified' => $now,
        ]]);
        return $driver->insertid();
    }

    // =========================================================================
    // Basic Observer Registration Tests
    // =========================================================================

    /**
     * Test that observe() accepts a class name
     */
    #[DataProvider('databaseProvider')]
    public function testObserveAcceptsClassName(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('creating'));
        $this->assertTrue(TestObserver::wasCalled('created'));
    }

    /**
     * Test that observe() accepts an instance
     */
    #[DataProvider('databaseProvider')]
    public function testObserveAcceptsInstance(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $observer = new TestObserver();
        ObserverTestItem::observe($observer);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('creating'));
    }

    // =========================================================================
    // Event Firing Tests
    // =========================================================================

    /**
     * Test that creating event fires before insert
     */
    #[DataProvider('databaseProvider')]
    public function testCreatingEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('creating'));
        $this->assertEquals($item, TestObserver::getModel('creating'));
    }

    /**
     * Test that created event fires after insert
     */
    #[DataProvider('databaseProvider')]
    public function testCreatedEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('created'));
        $this->assertNotNull(TestObserver::getModel('created')->get('id'));
    }

    /**
     * Test that updating event fires before update
     */
    #[DataProvider('databaseProvider')]
    public function testUpdatingEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Original');
        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::one($id);
        $item->set('title', 'Updated');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('updating'));
    }

    /**
     * Test that updated event fires after update
     */
    #[DataProvider('databaseProvider')]
    public function testUpdatedEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Original');
        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::one($id);
        $item->set('title', 'Updated');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('updated'));
    }

    /**
     * Test that saving event fires on both create and update
     */
    #[DataProvider('databaseProvider')]
    public function testSavingEventFiresOnCreateAndUpdate(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);

        // Create
        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertEquals(1, TestObserver::getCallCount('saving'));

        // Update
        $item->set('title', 'Updated');
        $item->save();

        $this->assertEquals(2, TestObserver::getCallCount('saving'));
    }

    /**
     * Test that saved event fires on both create and update
     */
    #[DataProvider('databaseProvider')]
    public function testSavedEventFiresOnCreateAndUpdate(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);

        // Create
        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertEquals(1, TestObserver::getCallCount('saved'));

        // Update
        $item->set('title', 'Updated');
        $item->save();

        $this->assertEquals(2, TestObserver::getCallCount('saved'));
    }

    /**
     * Test that deleting event fires before delete
     */
    #[DataProvider('databaseProvider')]
    public function testDeletingEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Test');
        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::one($id);
        $item->destroy();

        $this->assertTrue(TestObserver::wasCalled('deleting'));
    }

    /**
     * Test that deleted event fires after delete
     */
    #[DataProvider('databaseProvider')]
    public function testDeletedEventFires(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Test');
        ObserverTestItem::observe(TestObserver::class);

        $item = ObserverTestItem::one($id);
        $item->destroy();

        $this->assertTrue(TestObserver::wasCalled('deleted'));
    }

    // =========================================================================
    // Event Cancellation Tests
    // =========================================================================

    /**
     * Test that returning false from creating cancels insert
     */
    #[DataProvider('databaseProvider')]
    public function testCreatingCanCancelInsert(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(CancelingObserver::class);
        CancelingObserver::cancelEvent('creating');

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $result = $item->save();

        $this->assertFalse($result);
        $this->assertNull($item->get('id'));

        // Verify nothing was inserted
        $query = new Query($driver);
        $row = $query->select('COUNT(*)', 'cnt')->from('observer_test_items')->fetch('row');
        $this->assertEquals(0, $row->cnt);
    }

    /**
     * Test that returning false from updating cancels update
     */
    #[DataProvider('databaseProvider')]
    public function testUpdatingCanCancelUpdate(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Original');
        ObserverTestItem::observe(CancelingObserver::class);
        CancelingObserver::cancelEvent('updating');

        $item = ObserverTestItem::one($id);
        $item->set('title', 'Updated');
        $result = $item->save();

        $this->assertFalse($result);

        // Verify original title unchanged
        Query::purgeCache();
        $reloaded = ObserverTestItem::one($id);
        $this->assertEquals('Original', $reloaded->get('title'));
    }

    /**
     * Test that returning false from saving cancels operation
     */
    #[DataProvider('databaseProvider')]
    public function testSavingCanCancelOperation(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(CancelingObserver::class);
        CancelingObserver::cancelEvent('saving');

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $result = $item->save();

        $this->assertFalse($result);
    }

    /**
     * Test that returning false from deleting cancels delete
     */
    #[DataProvider('databaseProvider')]
    public function testDeletingCanCancelDelete(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $id = $this->insertTestRecord($driver, 'Test');
        ObserverTestItem::observe(CancelingObserver::class);
        CancelingObserver::cancelEvent('deleting');

        $item = ObserverTestItem::one($id);
        $result = $item->destroy();

        $this->assertFalse($result);

        // Verify record still exists
        $query = new Query($driver);
        $row = $query->select('COUNT(*)', 'cnt')->from('observer_test_items')->whereEquals('id', $id)->fetch('row');
        $this->assertEquals(1, $row->cnt);
    }

    // =========================================================================
    // Multiple Observers Tests
    // =========================================================================

    /**
     * Test that multiple observers can be registered
     */
    #[DataProvider('databaseProvider')]
    public function testMultipleObserversCanBeRegistered(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);
        ObserverTestItem::observe(SecondTestObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        $this->assertTrue(TestObserver::wasCalled('creating'));
        $this->assertTrue(SecondTestObserver::wasCalled('creating'));
    }

    /**
     * Test that observers are called in registration order
     */
    #[DataProvider('databaseProvider')]
    public function testObserversCalledInOrder(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(TestObserver::class);
        ObserverTestItem::observe(SecondTestObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        // TestObserver should be called before SecondTestObserver
        // Use call order (sequence number) instead of timestamps to avoid timing issues
        $firstCallOrder = TestObserver::getCallOrder('creating');
        $secondCallOrder = SecondTestObserver::getCallOrder('creating');

        $this->assertLessThan(
            $secondCallOrder,
            $firstCallOrder,
            'First observer should be called before second observer'
        );
    }

    // =========================================================================
    // Observer Can Modify Model Tests
    // =========================================================================

    /**
     * Test that observer can modify model during creating
     */
    #[DataProvider('databaseProvider')]
    public function testObserverCanModifyModelDuringCreating(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(ModifyingObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test Title');
        $item->save();

        // Observer should have set the slug
        $this->assertEquals('test-title-modified', $item->get('slug'));
    }

    // =========================================================================
    // Partial Observer Tests
    // =========================================================================

    /**
     * Test that observer only needs to implement desired events
     */
    #[DataProvider('databaseProvider')]
    public function testPartialObserverWorks(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        ObserverTestItem::observe(PartialObserver::class);

        $item = ObserverTestItem::blank();
        $item->set('title', 'Test');
        $item->save();

        // PartialObserver only has creating method
        $this->assertTrue(PartialObserver::wasCalled('creating'));
        // created method doesn't exist, so no error should occur
    }
}
