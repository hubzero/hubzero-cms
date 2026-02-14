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
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\UpsertItem;

/**
 * Tests for portable upsert functionality
 *
 * Validates single-row upsert, bulk upsertMany, the pushOrUpdate
 * convenience method, and ORM-level firstOrCreate/updateOrCreate
 * across all database backends.
 */
class UpsertTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return  array
     */
    protected static function getTestTables(): array
    {
        return ['upsert_items'];
    }

    /**
     * Create test tables
     *
     * @param   Driver  $driver  The database driver
     * @return  void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('upsert_items', true);

        $driver->schema()->createTable('upsert_items')
            ->id()
            ->string('code', 50)
            ->string('name', 255)->nullable()
            ->integer('value')->default(0)
            ->uniqueIndex('idx_upsert_code', 'code')
            ->execute();
    }

    /**
     * Set up test data before each test
     *
     * @param   Driver  $driver  The database driver
     * @return  void
     */
    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();

        // Clean up existing data
        (new Query($driver))->delete('upsert_items')->execute();

        // Reset autoincrement where possible
        try {
            $driver->setAutoIncrement('upsert_items', 0);
        } catch (\Exception $e) {
            // Ignore on drivers that don't support it
        }
    }

    /**
     * Helper to insert a row directly
     */
    private function insertRow(
        Driver $driver,
        string $code,
        string $name,
        int $value = 0
    ): void {
        (new Query($driver))->push('upsert_items', [
            'code' => $code,
            'name' => $name,
            'value' => $value,
        ]);
    }

    /**
     * Helper to read a row by code
     */
    private function getRowByCode(
        Driver $driver,
        string $code
    ): ?object {
        $driver->setQuery(
            'SELECT * FROM '
            . $driver->quoteName('upsert_items')
            . ' WHERE ' . $driver->quoteName('code')
            . ' = ' . $driver->quote($code)
        );

        $result = $driver->loadObject();

        return $result ?: null;
    }

    // =====================================================================
    // Single-Row Upsert Tests
    // =====================================================================

    /**
     * Upsert inserts a new row when no conflict exists
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertInsertsNewRow(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        (new Query($driver))
            ->upsert('upsert_items', [
                'code' => 'A001',
                'name' => 'Alpha',
                'value' => 10,
            ], ['name', 'value'], ['code'])
            ->execute();

        $row = $this->getRowByCode($driver, 'A001');

        $this->assertNotNull($row, "$dbName: Row should exist");
        $this->assertSame(
            'Alpha',
            $row->name,
            "$dbName: Name should be Alpha"
        );
        $this->assertSame(
            10,
            (int) $row->value,
            "$dbName: Value should be 10"
        );
    }

    /**
     * Upsert updates an existing row on conflict
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertUpdatesExistingRow(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'A001', 'Alpha', 10);

        (new Query($driver))
            ->upsert('upsert_items', [
                'code' => 'A001',
                'name' => 'Alpha Updated',
                'value' => 20,
            ], ['name', 'value'], ['code'])
            ->execute();

        $row = $this->getRowByCode($driver, 'A001');

        $this->assertNotNull($row, "$dbName: Row should exist");
        $this->assertSame(
            'Alpha Updated',
            $row->name,
            "$dbName: Name should be updated"
        );
        $this->assertSame(
            20,
            (int) $row->value,
            "$dbName: Value should be updated"
        );
    }

    /**
     * Upsert with selective update columns only updates specified columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertWithSelectiveUpdateColumns(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'A001', 'Alpha', 10);

        // Only update 'value', not 'name'
        (new Query($driver))
            ->upsert('upsert_items', [
                'code' => 'A001',
                'name' => 'Should Not Change',
                'value' => 99,
            ], ['value'], ['code'])
            ->execute();

        $row = $this->getRowByCode($driver, 'A001');

        $this->assertSame(
            99,
            (int) $row->value,
            "$dbName: Value should be updated to 99"
        );

        if ($driver->supportsSelectiveUpsertUpdateColumns()) {
            $this->assertSame(
                'Alpha',
                $row->name,
                "$dbName: Name should remain unchanged"
            );
        }
    }

    /**
     * Upsert with explicit conflict columns on UNIQUE index
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertWithExplicitConflictColumns(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'B001', 'Beta', 5);

        (new Query($driver))
            ->upsert('upsert_items', [
                'code' => 'B001',
                'name' => 'Beta Updated',
                'value' => 50,
            ], ['name', 'value'], ['code'])
            ->execute();

        $row = $this->getRowByCode($driver, 'B001');

        $this->assertSame(
            'Beta Updated',
            $row->name,
            "$dbName: Should update via UNIQUE code column"
        );
        $this->assertSame(
            50,
            (int) $row->value,
            "$dbName: Value should be updated"
        );
    }

    /**
     * Upsert does not affect other rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertPreservesOtherRows(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'A001', 'Alpha', 10);
        $this->insertRow($driver, 'B001', 'Beta', 20);

        (new Query($driver))
            ->upsert('upsert_items', [
                'code' => 'A001',
                'name' => 'Alpha Changed',
                'value' => 99,
            ], ['name', 'value'], ['code'])
            ->execute();

        $beta = $this->getRowByCode($driver, 'B001');

        $this->assertSame(
            'Beta',
            $beta->name,
            "$dbName: Beta row should be unchanged"
        );
        $this->assertSame(
            20,
            (int) $beta->value,
            "$dbName: Beta value should be unchanged"
        );
    }

    // =====================================================================
    // pushOrUpdate Convenience Method Tests
    // =====================================================================

    /**
     * pushOrUpdate inserts a new row
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPushOrUpdateInsertsNewRow(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $result = (new Query($driver))->pushOrUpdate(
            'upsert_items',
            ['code' => 'C001', 'name' => 'Charlie', 'value' => 30],
            ['name', 'value'],
            ['code']
        );

        $this->assertNotFalse(
            $result,
            "$dbName: pushOrUpdate should succeed"
        );

        $row = $this->getRowByCode($driver, 'C001');
        $this->assertNotNull($row, "$dbName: Row should exist");
        $this->assertSame('Charlie', $row->name);
    }

    /**
     * pushOrUpdate updates an existing row
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPushOrUpdateUpdatesExistingRow(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'C001', 'Charlie', 30);

        $result = (new Query($driver))->pushOrUpdate(
            'upsert_items',
            ['code' => 'C001', 'name' => 'Charlie Updated', 'value' => 60],
            ['name', 'value'],
            ['code']
        );

        $this->assertNotFalse(
            $result,
            "$dbName: pushOrUpdate should succeed"
        );

        $row = $this->getRowByCode($driver, 'C001');
        $this->assertSame(
            'Charlie Updated',
            $row->name,
            "$dbName: Name should be updated"
        );
    }

    // =====================================================================
    // Bulk Upsert (upsertMany) Tests
    // =====================================================================

    /**
     * upsertMany inserts multiple new rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertManyInsertsMultipleNewRows(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $rows = [
            ['code' => 'X01', 'name' => 'X-One', 'value' => 1],
            ['code' => 'X02', 'name' => 'X-Two', 'value' => 2],
            ['code' => 'X03', 'name' => 'X-Three', 'value' => 3],
        ];

        $count = (new Query($driver))->upsertMany(
            'upsert_items',
            $rows,
            ['name', 'value'],
            ['code']
        );

        $this->assertSame(
            3,
            $count,
            "$dbName: Should report 3 rows affected"
        );

        $this->assertNotNull($this->getRowByCode($driver, 'X01'));
        $this->assertNotNull($this->getRowByCode($driver, 'X02'));
        $this->assertNotNull($this->getRowByCode($driver, 'X03'));
    }

    /**
     * upsertMany updates existing rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertManyUpdatesExistingRows(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'X01', 'X-One', 1);
        $this->insertRow($driver, 'X02', 'X-Two', 2);

        $rows = [
            ['code' => 'X01', 'name' => 'X-One Updated', 'value' => 100],
            ['code' => 'X02', 'name' => 'X-Two Updated', 'value' => 200],
        ];

        $count = (new Query($driver))->upsertMany(
            'upsert_items',
            $rows,
            ['name', 'value'],
            ['code']
        );

        $this->assertSame(2, $count, "$dbName: Should report 2 rows");

        $row1 = $this->getRowByCode($driver, 'X01');
        $this->assertSame(
            'X-One Updated',
            $row1->name,
            "$dbName: X01 name should be updated"
        );
        $this->assertSame(100, (int) $row1->value);

        $row2 = $this->getRowByCode($driver, 'X02');
        $this->assertSame('X-Two Updated', $row2->name);
        $this->assertSame(200, (int) $row2->value);
    }

    /**
     * upsertMany handles mix of new and existing rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertManyMixedInsertAndUpdate(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'M01', 'Existing', 10);

        $rows = [
            ['code' => 'M01', 'name' => 'Updated', 'value' => 99],
            ['code' => 'M02', 'name' => 'New Row', 'value' => 50],
        ];

        $count = (new Query($driver))->upsertMany(
            'upsert_items',
            $rows,
            ['name', 'value'],
            ['code']
        );

        $this->assertSame(2, $count);

        $existing = $this->getRowByCode($driver, 'M01');
        $this->assertSame('Updated', $existing->name);
        $this->assertSame(99, (int) $existing->value);

        $newRow = $this->getRowByCode($driver, 'M02');
        $this->assertNotNull($newRow, "$dbName: New row should exist");
        $this->assertSame('New Row', $newRow->name);
    }

    /**
     * upsertMany with empty array returns 0
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertManyEmptyArray(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $count = (new Query($driver))->upsertMany(
            'upsert_items',
            [],
            ['name', 'value'],
            ['code']
        );

        $this->assertSame(0, $count, "$dbName: Empty array = 0 rows");
    }

    /**
     * upsertMany with inconsistent columns throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpsertManyInconsistentColumnsThrows(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $this->expectException(\InvalidArgumentException::class);

        (new Query($driver))->upsertMany(
            'upsert_items',
            [
                ['code' => 'A', 'name' => 'First', 'value' => 1],
                ['code' => 'B', 'name' => 'Second'],
            ],
            ['name', 'value'],
            ['code']
        );
    }

    // =====================================================================
    // ORM-Level Tests
    // =====================================================================

    /**
     * firstOrCreate finds existing record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFirstOrCreateFindsExisting(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'F001', 'Found', 42);

        $item = UpsertItem::firstOrCreate(
            ['code' => 'F001'],
            ['name' => 'Should Not Create', 'value' => 0]
        );

        $this->assertSame(
            'Found',
            $item->get('name'),
            "$dbName: Should return existing record"
        );
        $this->assertSame(42, (int) $item->get('value'));
    }

    /**
     * firstOrCreate creates when not found
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFirstOrCreateCreatesNew(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $item = UpsertItem::firstOrCreate(
            ['code' => 'NEW1'],
            ['name' => 'Created', 'value' => 77]
        );

        $this->assertSame('NEW1', $item->get('code'));
        $this->assertSame('Created', $item->get('name'));
        $this->assertSame(77, (int) $item->get('value'));

        // Verify it's actually in the database
        $row = $this->getRowByCode($driver, 'NEW1');
        $this->assertNotNull($row, "$dbName: Should be persisted");
    }

    /**
     * updateOrCreate updates existing record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdateOrCreateUpdatesExisting(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'U001', 'Original', 10);

        $item = UpsertItem::updateOrCreate(
            ['code' => 'U001'],
            ['name' => 'Modified', 'value' => 88]
        );

        $this->assertSame('Modified', $item->get('name'));
        $this->assertSame(88, (int) $item->get('value'));

        // Verify in database
        $row = $this->getRowByCode($driver, 'U001');
        $this->assertSame('Modified', $row->name);
    }

    /**
     * updateOrCreate creates when not found
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUpdateOrCreateCreatesNew(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $item = UpsertItem::updateOrCreate(
            ['code' => 'U002'],
            ['name' => 'Brand New', 'value' => 55]
        );

        $this->assertSame('U002', $item->get('code'));
        $this->assertSame('Brand New', $item->get('name'));
        $this->assertSame(55, (int) $item->get('value'));

        $row = $this->getRowByCode($driver, 'U002');
        $this->assertNotNull($row, "$dbName: Should be persisted");
    }

    /**
     * Relational::upsertMany bulk upserts via ORM static method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRelationalUpsertMany(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);
        $this->insertRow($driver, 'R01', 'Existing R', 10);

        $count = UpsertItem::upsertMany(
            [
                ['code' => 'R01', 'name' => 'Updated R', 'value' => 99],
                ['code' => 'R02', 'name' => 'New R', 'value' => 50],
            ],
            ['name', 'value'],
            ['code']
        );

        $this->assertSame(2, $count);

        $r01 = $this->getRowByCode($driver, 'R01');
        $this->assertSame('Updated R', $r01->name);

        $r02 = $this->getRowByCode($driver, 'R02');
        $this->assertNotNull($r02);
        $this->assertSame('New R', $r02->name);
    }
}
