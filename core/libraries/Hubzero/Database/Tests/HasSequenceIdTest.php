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
use Hubzero\Database\Tests\TestModels\SequenceItem;
use Hubzero\Database\Tests\TestModels\CustomSequenceItem;

/**
 * Tests for the HasSequenceId trait
 *
 * Validates that models using HasSequenceId get their primary key
 * assigned from a database sequence on creation.
 */
class HasSequenceIdTest extends AbstractDriverTestCase
{
    /**
     * Sequence names used by tests
     */
    private static string $defaultSeq = 'seq_items_id_seq';
    private static string $customSeq = 'custom_item_seq';

    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return  array
     */
    protected static function getTestTables(): array
    {
        return ['seq_items'];
    }

    /**
     * Create test tables
     *
     * @param   Driver  $driver  The database driver
     * @return  void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('seq_items', true);

        // Create table WITHOUT auto-increment since the sequence will
        // provide the ID. Use a simple integer primary key.
        $driver->createTable('seq_items')
            ->integer('id')->primaryKey('id')
            ->string('name', 255)->nullable()
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

        // Clear booted state so bootHasSequenceId re-registers
        SequenceItem::clearBootedModels();
        CustomSequenceItem::clearBootedModels();

        Query::purgeCache();

        // Clean up rows via DELETE instead of TRUNCATE.
        // This table uses explicit sequence-assigned IDs (no auto-increment),
        // so a plain delete is the portable reset path.
        (new Query($driver))->delete('seq_items')->execute();

        try {
            $driver->dropSequence(self::$defaultSeq);
        } catch (\Exception $e) {
            // Ignore
        }
        try {
            $driver->dropSequence(self::$customSeq);
        } catch (\Exception $e) {
            // Ignore
        }
    }

    // =========================================================================
    // Sequence Name Tests
    // =========================================================================

    /**
     * getSequenceName() returns {table}_id_seq by default
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetSequenceNameReturnsDefault(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $item = new SequenceItem();

        $this->assertSame(
            'seq_items_id_seq',
            $item->getSequenceName()
        );
    }

    /**
     * getSequenceName() returns custom name from $sequenceName property
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetSequenceNameReturnsCustom(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $item = new CustomSequenceItem();

        $this->assertSame(
            'custom_item_seq',
            $item->getSequenceName()
        );
    }

    // =========================================================================
    // ID Assignment Tests
    // =========================================================================

    /**
     * Saving a model assigns an ID from the sequence
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSequenceIdAssignedOnCreate(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        // Create the sequence
        $driver->createSequence(self::$defaultSeq);

        $item = new SequenceItem();
        $item->set('name', 'First Item');
        $result = $item->save();

        $this->assertNotFalse($result);
        $this->assertSame(
            1,
            (int) $item->get('id'),
            "$dbName: First item should get ID 1 from sequence"
        );
    }

    /**
     * Multiple models get unique sequential IDs
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleModelsGetUniqueIds(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $driver->createSequence(self::$defaultSeq);

        $item1 = new SequenceItem();
        $item1->set('name', 'Item 1');
        $item1->save();

        $item2 = new SequenceItem();
        $item2->set('name', 'Item 2');
        $item2->save();

        $item3 = new SequenceItem();
        $item3->set('name', 'Item 3');
        $item3->save();

        $this->assertSame(1, (int) $item1->get('id'));
        $this->assertSame(2, (int) $item2->get('id'));
        $this->assertSame(3, (int) $item3->get('id'));
    }

    /**
     * Explicit PK is not overwritten by the sequence
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testExplicitIdNotOverwritten(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $driver->createSequence(self::$defaultSeq);

        $item = new SequenceItem();
        $item->set('id', 999);
        $item->set('name', 'Explicit ID');
        $item->save();

        $this->assertSame(
            999,
            (int) $item->get('id'),
            "$dbName: Explicit ID should be preserved"
        );
    }

    /**
     * Custom sequence name works with model
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomSequenceNameWorks(
        string $dbName,
        Driver $driver
    ): void {
        $this->setupTestData($driver);

        $driver->createSequence(self::$customSeq, 100);

        $item = new CustomSequenceItem();
        $item->set('name', 'Custom Seq Item');
        $item->save();

        $this->assertSame(
            100,
            (int) $item->get('id'),
            "$dbName: Should get ID 100 from custom sequence starting at 100"
        );
    }
}
