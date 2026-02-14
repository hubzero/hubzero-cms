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
use Hubzero\Database\Traits\Orderable;
use Hubzero\Database\Tests\TestModels\OrderableItem;
use Hubzero\Database\Tests\TestModels\OrderableMenuItem;
use Hubzero\Database\Tests\TestModels\CustomOrderableItem;

/**
 * Orderable trait tests
 *
 * Tests for the Orderable trait that provides ordering/sorting
 * functionality for models.
 */
class OrderableTraitTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['orderable_custom_items', 'orderable_menu_items', 'orderable_items'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('orderable_items', true);
        $driver->createTable('orderable_items')
            ->id()
            ->string('title', 255)
            ->integer('ordering')->default(0)
            ->execute();

        $driver->dropTable('orderable_menu_items', true);
        $driver->createTable('orderable_menu_items')
            ->id()
            ->string('title', 255)
            ->integer('menu_id')
            ->integer('ordering')->default(0)
            ->execute();

        $driver->dropTable('orderable_custom_items', true);
        $driver->createTable('orderable_custom_items')
            ->id()
            ->string('title', 255)
            ->integer('sort_order')->default(0)
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        OrderableItem::clearBootedModels();
        OrderableMenuItem::clearBootedModels();
        CustomOrderableItem::clearBootedModels();
        Query::purgeCache();

        $driver->truncateTable('orderable_items');
        $driver->truncateTable('orderable_menu_items');
        $driver->truncateTable('orderable_custom_items');
    }


    // =========================================================================
    // Basic Ordering Tests
    // =========================================================================

    /**
     * Test getOrdering() returns current value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetOrderingReturnsCurrentValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Test Item', 'ordering' => 5]]);
        $id = 1;

        $item = OrderableItem::one($id);

        $this->assertEquals(5, $item->getOrdering());
    }

    /**
     * Test setOrdering() sets the value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetOrderingSetsValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Test Item', 'ordering' => 1]]);
        $id = 1;

        $item = OrderableItem::one($id);
        $item->setOrdering(10);

        $this->assertEquals(10, $item->getOrdering());
    }

    /**
     * Test getMaxOrdering() returns highest value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetMaxOrderingReturnsHighestValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 5]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);

        $item = new OrderableItem();

        $this->assertEquals(5, $item->getMaxOrdering());
    }

    /**
     * Test setNewOrdering() sets next available ordering
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetNewOrderingSetsNextAvailableOrdering(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);

        $item = new OrderableItem();
        $item->set('title', 'New Item');
        $item->setNewOrdering();

        $this->assertEquals(3, $item->getOrdering());
    }

    // =========================================================================
    // Move Tests
    // =========================================================================

    /**
     * Test moveUp() decreases ordering
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveUpDecreasesOrdering(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $id2 = 2;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);

        $item = OrderableItem::one($id2);
        $result = $item->moveUp();

        $this->assertTrue($result);
        // After moveUp, Item 2 should have ordering 1 (swapped with Item 1)
        $this->assertEquals(1, $item->getOrdering());
    }

    /**
     * Test moveDown() increases ordering
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveDownIncreasesOrdering(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $id2 = 2;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);

        $item = OrderableItem::one($id2);
        $result = $item->moveDown();

        $this->assertTrue($result);
        // After moveDown, Item 2 should have ordering 3 (swapped with Item 3)
        $this->assertEquals(3, $item->getOrdering());
    }

    /**
     * Test move() with positive delta moves down
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveWithPositiveDeltaMovesDown(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $id1 = 1;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);

        $item = OrderableItem::one($id1);
        $result = $item->move(2);

        $this->assertTrue($result);
        // Item 1 moved down 2 positions
        $this->assertEquals(3, $item->getOrdering());
    }

    /**
     * Test move() with negative delta moves up
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveWithNegativeDeltaMovesUp(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);
        $id3 = 3;

        $item = OrderableItem::one($id3);
        $result = $item->move(-2);

        $this->assertTrue($result);
        // Item 3 moved up 2 positions
        $this->assertEquals(1, $item->getOrdering());
    }

    /**
     * Test move(0) returns true without changing anything
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveZeroReturnsTrue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item', 'ordering' => 2]]);
        $id = 1;

        $item = OrderableItem::one($id);
        $result = $item->move(0);

        $this->assertTrue($result);
        $this->assertEquals(2, $item->getOrdering());
    }

    /**
     * Test moveFirst() moves to position 1
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveFirstMovesToPositionOne(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);
        $id3 = 3;

        $item = OrderableItem::one($id3);
        $result = $item->moveFirst();

        $this->assertTrue($result);
        $this->assertEquals(1, $item->getOrdering());
    }

    /**
     * Test moveLast() moves to last position
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveLastMovesToLastPosition(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $id1 = 1;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);

        $item = OrderableItem::one($id1);
        $result = $item->moveLast();

        $this->assertTrue($result);
        $this->assertEquals(3, $item->getOrdering());
    }

    /**
     * Test moveTo() moves to specific position
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveToMovesToSpecificPosition(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 3', 'ordering' => 3]]);
        $id3 = 3;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 4', 'ordering' => 4]]);

        $item = OrderableItem::one($id3);
        $result = $item->moveTo(1);

        $this->assertTrue($result);
        $this->assertEquals(1, $item->getOrdering());
    }

    /**
     * Test moveTo() with same position returns true
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveToSamePositionReturnsTrue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item', 'ordering' => 2]]);
        $id = 1;

        $item = OrderableItem::one($id);
        $result = $item->moveTo(2);

        $this->assertTrue($result);
        $this->assertEquals(2, $item->getOrdering());
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    /**
     * Test moveUp on first item does nothing
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveUpOnFirstItemDoesNothing(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $id1 = 1;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);

        $item = OrderableItem::one($id1);
        $result = $item->moveUp();

        $this->assertTrue($result); // Returns true even when at edge
        $this->assertEquals(1, $item->getOrdering()); // Still at position 1
    }

    /**
     * Test moveDown on last item does nothing
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveDownOnLastItemDoesNothing(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 1', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item 2', 'ordering' => 2]]);
        $id2 = 2;

        $item = OrderableItem::one($id2);
        $result = $item->moveDown();

        $this->assertTrue($result); // Returns true even when at edge
        $this->assertEquals(2, $item->getOrdering()); // Still at position 2
    }

    // =========================================================================
    // Static Reorder Tests
    // =========================================================================

    /**
     * Test reorderAll() compacts ordering numbers
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testReorderAllCompactsOrderingNumbers(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Create items with gaps in ordering
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item A', 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item B', 'ordering' => 5]]);
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item C', 'ordering' => 10]]);

        $count = OrderableItem::reorderAll();

        $this->assertEquals(3, $count);

        // Check orderings are now sequential
        $items = OrderableItem::all()->order('ordering', 'asc')->rows()->toArray();
        $this->assertEquals(1, $items[0]['ordering']);
        $this->assertEquals(2, $items[1]['ordering']);
        $this->assertEquals(3, $items[2]['ordering']);
    }

    /**
     * Test reorderByIds() sets ordering based on ID array
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testReorderByIdsSetsOrderingBasedOnIdArray(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item A', 'ordering' => 1]]);
        $id1 = 1;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item B', 'ordering' => 2]]);
        $id2 = 2;
        $query = new Query($driver);
        $query->insertMany('orderable_items', [['title' => 'Item C', 'ordering' => 3]]);
        $id3 = 3;

        // Reorder: C, A, B
        $count = OrderableItem::reorderByIds([$id3, $id1, $id2]);

        $this->assertEquals(3, $count);

        // Check new orderings
        $this->assertEquals(2, OrderableItem::one($id1)->getOrdering()); // A is now 2nd
        $this->assertEquals(3, OrderableItem::one($id2)->getOrdering()); // B is now 3rd
        $this->assertEquals(1, OrderableItem::one($id3)->getOrdering()); // C is now 1st
    }

    // =========================================================================
    // Group-Scoped Ordering Tests
    // =========================================================================

    /**
     * Test ordering is scoped to group
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOrderingIsScopedToGroup(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Menu 1 items
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu1 Item1', 'menu_id' => 1, 'ordering' => 1]]);
        $id1 = 1;
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu1 Item2', 'menu_id' => 1, 'ordering' => 2]]);
        $id2 = 2;

        // Menu 2 items
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu2 Item1', 'menu_id' => 2, 'ordering' => 1]]);
        $id3 = 3;
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu2 Item2', 'menu_id' => 2, 'ordering' => 2]]);
        $id4 = 4;

        // Get max ordering for menu 1
        $item = OrderableMenuItem::one($id1);
        $maxOrdering = $item->getMaxOrdering();

        // Should only be 2 (items in menu 1), not 4 (all items)
        $this->assertEquals(2, $maxOrdering);
    }

    /**
     * Test moveUp in group doesn't affect other groups
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMoveUpInGroupDoesntAffectOtherGroups(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        // Menu 1 items
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu1 Item1', 'menu_id' => 1, 'ordering' => 1]]);
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu1 Item2', 'menu_id' => 1, 'ordering' => 2]]);
        $id2 = 2;

        // Menu 2 items (should not be affected)
        $query = new Query($driver);
        $query->insertMany('orderable_menu_items', [['title' => 'Menu2 Item1', 'menu_id' => 2, 'ordering' => 1]]);
        $id3 = 3;

        $item = OrderableMenuItem::one($id2);
        $item->moveUp();

        // Menu 1 Item 2 moved up
        $this->assertEquals(1, OrderableMenuItem::one($id2)->getOrdering());

        // Menu 2 Item 1 should be unchanged
        $this->assertEquals(1, OrderableMenuItem::one($id3)->getOrdering());
    }

    // =========================================================================
    // Custom Column Tests
    // =========================================================================

    /**
     * Test custom ordering column name works
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomOrderingColumnNameWorks(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('orderable_custom_items', [['title' => 'Custom Item', 'sort_order' => 5]]);
        $id = 1;

        $item = CustomOrderableItem::one($id);

        $this->assertEquals(5, $item->getOrdering());
        $this->assertEquals('sort_order', $item->getOrderingColumn());
    }
}
