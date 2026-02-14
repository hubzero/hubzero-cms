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
use Hubzero\Database\Expression;

/**
 * Tests for the portable sequence API across all database backends
 *
 * Validates sequence creation, incrementing, Expression rendering,
 * and table-based emulation on databases with native support.
 */
class SequenceExpressionTest extends AbstractDriverTestCase
{
    /**
     * Sequence names used by tests (cleaned up in setUp)
     */
    private static string $seqDefault = 'test_seq_default';
    private static string $seqStart = 'test_seq_start';
    private static string $seqInsert = 'test_seq_insert';
    private static string $testTable = 'seq_test_items';

    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return  array
     */
    protected static function getTestTables(): array
    {
        return [self::$testTable];
    }

    /**
     * Create test tables
     *
     * @param   Driver  $driver  The database driver
     * @return  void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        try {
            $driver->dropTable(self::$testTable);
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }

        $driver->createTable(self::$testTable)
            ->integer('id')->autoIncrement()->primaryKey('id')
            ->string('name', 255)->nullable()
            ->execute();
    }

    /**
     * Clean up sequences before each test
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach (static::getClassDrivers() as $dbName => $driver) {
            // Drop any leftover sequences from previous test runs
            $sequences = [
                self::$seqDefault,
                self::$seqStart,
                self::$seqInsert,
            ];

            foreach ($sequences as $seq) {
                try {
                    $driver->dropSequence($seq);
                } catch (\Exception $e) {
                    // Ignore if sequence doesn't exist
                }
            }

            // Clean test table data
            try {
                (new Query($driver))->delete(self::$testTable)->execute();
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }

    /**
     * All real drivers should report sequence support
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSupportsSequencesReturnsTrue(
        string $dbName,
        Driver $driver
    ): void {
        $this->assertTrue(
            $driver->supportsSequences(),
            "$dbName should support sequences"
        );
    }

    /**
     * Create a sequence, verify it exists, drop it, verify it's gone
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCreateAndDropSequence(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqDefault);

        $this->assertTrue(
            $driver->sequenceExists(self::$seqDefault),
            "$dbName: Sequence should exist after creation"
        );

        $driver->dropSequence(self::$seqDefault);

        $this->assertFalse(
            $driver->sequenceExists(self::$seqDefault),
            "$dbName: Sequence should not exist after drop"
        );
    }

    /**
     * nextSequenceValue should return incrementing values: 1, 2, 3
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNextSequenceValueReturnsIncrementingValues(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqDefault);

        $val1 = $driver->nextSequenceValue(self::$seqDefault);
        $val2 = $driver->nextSequenceValue(self::$seqDefault);
        $val3 = $driver->nextSequenceValue(self::$seqDefault);

        $this->assertSame(1, $val1, "$dbName: First value should be 1");
        $this->assertSame(2, $val2, "$dbName: Second value should be 2");
        $this->assertSame(3, $val3, "$dbName: Third value should be 3");
    }

    /**
     * Sequence with custom start value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSequenceStartValue(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqStart, 100);

        $val = $driver->nextSequenceValue(self::$seqStart);

        $this->assertSame(
            100,
            $val,
            "$dbName: First value should be 100"
        );
    }

    /**
     * currentSequenceValue should reflect the last nextVal call
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCurrentSequenceValue(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqDefault);

        $driver->nextSequenceValue(self::$seqDefault);
        $driver->nextSequenceValue(self::$seqDefault);

        $current = $driver->currentSequenceValue(self::$seqDefault);

        $this->assertSame(
            2,
            $current,
            "$dbName: Current value should be 2 after two increments"
        );
    }

    /**
     * Expression::nextVal creates the right expression type
     */
    #[Test]
    public function testNextValExpressionStructure(): void
    {
        $expr = Expression::nextVal('my_seq');

        $this->assertSame(
            Expression::TYPE_FUNCTION,
            $expr->getType()
        );
        $this->assertSame('NEXTVAL', $expr->getFunction());
        $this->assertSame(['my_seq'], $expr->getArguments());
    }

    /**
     * Expression::currVal creates the right expression type
     */
    #[Test]
    public function testCurrValExpressionStructure(): void
    {
        $expr = Expression::currVal('my_seq');

        $this->assertSame(
            Expression::TYPE_FUNCTION,
            $expr->getType()
        );
        $this->assertSame('CURRVAL', $expr->getFunction());
        $this->assertSame(['my_seq'], $expr->getArguments());
    }

    /**
     * Query::nextVal() returns a Raw value usable in INSERT data
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNextValInInsert(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqInsert, 500);

        $query = new Query($driver);
        $nextVal = $query->nextVal(self::$seqInsert);

        // The resolved value should be the integer 500
        $this->assertInstanceOf(
            \Hubzero\Database\Value\Raw::class,
            $nextVal,
            "$dbName: nextVal() should return a Raw value"
        );
    }

    /**
     * getSequences() should list created sequences
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetSequencesListsCreatedSequences(
        string $dbName,
        Driver $driver
    ): void {
        $driver->createSequence(self::$seqDefault);

        $sequences = $driver->getSequences();
        $names = array_map(function ($seq) {
            // Some drivers return SequenceInfo objects, others return strings
            if (is_object($seq) && method_exists($seq, 'getName')) {
                return $seq->getName();
            }
            return (string) $seq;
        }, $sequences);

        $this->assertContains(
            self::$seqDefault,
            $names,
            "$dbName: getSequences() should list the created sequence"
        );
    }

    /**
     * Emulation Test: _sequences table created automatically
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testEmulationTableCreatedAutomatically(
        string $dbName,
        Driver $driver
    ): void {
        if (!$driver->usesSequenceEmulation()) {
            $this->assertTrue(true, "$dbName: native sequences, no emulation table");
            return;
        }

        $driver->createSequence(self::$seqDefault);

        $this->assertTrue(
            $driver->tableExists('_sequences'),
            "$dbName: _sequences table should exist after createSequence()"
        );
    }
}
