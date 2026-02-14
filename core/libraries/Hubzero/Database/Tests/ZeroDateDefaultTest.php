<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Driver\Sql as SqlDriver;
use Hubzero\Database\Query;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Schema\ColumnDefinitionTrait;

/**
 * Zero-date default translation tests
 *
 * Verifies that zero-date defaults ('0000-00-00 00:00:00') are translated
 * to DEFAULT NULL across all database backends. This handles both legacy
 * database conventions and stricter date-validation modes.
 */
class ZeroDateDefaultTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['zerodate_test'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables created and dropped per-test
    }

    // =========================================================================
    // isZeroDate() Unit Tests (No Database Needed)
    // =========================================================================

    #[Test]
    public function isZeroDateDetectsDatetimeZeroDate(): void
    {
        $this->assertTrue(SqlDriver::isZeroDate('0000-00-00 00:00:00'));
    }

    #[Test]
    public function isZeroDateDetectsDateOnlyZeroDate(): void
    {
        $this->assertTrue(SqlDriver::isZeroDate('0000-00-00'));
    }

    #[Test]
    public function isZeroDateDetectsIsoFormatZeroDate(): void
    {
        $this->assertTrue(SqlDriver::isZeroDate('0000-00-00T00:00:00'));
    }

    #[Test]
    public function isZeroDateReturnsFalseForNull(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate(null));
    }

    #[Test]
    public function isZeroDateReturnsFalseForEmptyString(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate(''));
    }

    #[Test]
    public function isZeroDateReturnsFalseForValidDate(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate('2024-01-15 10:30:00'));
    }

    #[Test]
    public function isZeroDateReturnsFalseForNumericZero(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate(0));
    }

    #[Test]
    public function isZeroDateReturnsFalseForBoolean(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate(false));
    }

    #[Test]
    public function isZeroDateReturnsFalseForNonZeroDateString(): void
    {
        $this->assertFalse(SqlDriver::isZeroDate('0000-00-01 00:00:00'));
    }

    // =========================================================================
    // Grammar::compileColumn() Tests (Column Object Path)
    // =========================================================================

    #[Test]
    public function columnWithZeroDatetimeDefaultBecomesNullable(): void
    {
        $column = new Column('created_at', 'datetime');
        $column->default('0000-00-00 00:00:00');

        // Column starts as NOT nullable
        $this->assertFalse($column->isNullable());
        // But has zero-date default
        $this->assertTrue(SqlDriver::isZeroDate($column->getDefault()));
    }

    #[Test]
    public function columnWithZeroDateDefaultBecomesNullable(): void
    {
        $column = new Column('birth_date', 'date');
        $column->default('0000-00-00');

        $this->assertFalse($column->isNullable());
        $this->assertTrue(SqlDriver::isZeroDate($column->getDefault()));
    }

    #[Test]
    public function columnWithNormalDefaultIsUnaffected(): void
    {
        $column = new Column('status', 'integer');
        $column->default(0);

        $this->assertFalse(SqlDriver::isZeroDate($column->getDefault()));
    }

    // =========================================================================
    // Functional Tests — CREATE TABLE (TableBuilder path)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function zeroDatetimeDefaultCreatesNullableColumn(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->datetime('published')->notNull()->default('0000-00-00 00:00:00')
            ->execute();

        $this->assertTrue(
            $driver->tableHasField('zerodate_test', 'published'),
            "[$dbName] Column 'published' should exist"
        );

        // Insert a row without specifying 'published' — default should be NULL
        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertNull(
            $row->published,
            "[$dbName] Zero-date default should be translated to NULL"
        );

        $driver->dropTable('zerodate_test', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function zeroDateDefaultCreatesNullableColumn(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->date('event_date')->notNull()->default('0000-00-00')
            ->execute();

        $this->assertTrue(
            $driver->tableHasField('zerodate_test', 'event_date'),
            "[$dbName] Column 'event_date' should exist"
        );

        // Insert without specifying event_date
        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertNull(
            $row->event_date,
            "[$dbName] Zero-date default should be translated to NULL"
        );

        $driver->dropTable('zerodate_test', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function normalDatetimeDefaultIsUnchanged(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->integer('status')->notNull()->default(0)
            ->string('label', 50)->default('pending')
            ->execute();

        // Insert without specifying status or label
        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertEquals(
            0,
            (int) $row->status,
            "[$dbName] Normal integer default should be unchanged"
        );
        $this->assertEquals(
            'pending',
            $row->label,
            "[$dbName] Normal string default should be unchanged"
        );

        $driver->dropTable('zerodate_test', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function nullableColumnWithZeroDateDefaultAcceptsNull(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        // Column already nullable + zero-date default
        $driver->createTable('zerodate_test')
            ->id()
            ->datetime('expires')->nullable()->default('0000-00-00 00:00:00')
            ->execute();

        // Should accept explicit NULL
        $query = new Query($driver);
        $driver->setQuery(
            "INSERT INTO " . $driver->quoteName('zerodate_test')
            . " (" . $driver->quoteName('expires') . ")"
            . " VALUES (NULL)"
        );
        $driver->execute();

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->fetch('row');

        $this->assertNull(
            $row->expires,
            "[$dbName] Column should accept NULL"
        );

        $driver->dropTable('zerodate_test', true);
    }

    // =========================================================================
    // Functional Tests — ALTER TABLE (AlterTableBuilder path)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableZeroDateDefaultBecomesNull(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        // Create table without datetime column first
        $driver->createTable('zerodate_test')
            ->id()
            ->string('title', 255)
            ->execute();

        // Add column with zero-date default via ALTER TABLE
        $driver->schema()->table('zerodate_test')->alter()
            ->addDatetime('modified')
            ->notNull()
            ->default('0000-00-00 00:00:00')
            ->execute();

        $this->assertTrue(
            $driver->tableHasField('zerodate_test', 'modified'),
            "[$dbName] Column 'modified' should exist after ALTER"
        );

        // Insert without specifying 'modified'
        $query = new Query($driver);
        $query->push('zerodate_test', ['title' => 'Test']);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->fetch('row');

        $this->assertNull(
            $row->modified,
            "[$dbName] ALTER TABLE zero-date default should be NULL"
        );

        $driver->dropTable('zerodate_test', true);
    }

    // =========================================================================
    // Functional Tests — TableDefinition + Grammar path
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableDefinitionZeroDateDefaultBecomesNull(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        // Use schema()->createTable() path
        $driver->schema()->createTable('zerodate_test')
            ->id()
            ->datetime('publish_up')->default('0000-00-00 00:00:00')
            ->datetime('publish_down')->default('0000-00-00 00:00:00')
            ->execute();

        // Insert without specifying dates
        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertNull(
            $row->publish_up,
            "[$dbName] publish_up zero-date default should be NULL"
        );
        $this->assertNull(
            $row->publish_down,
            "[$dbName] publish_down zero-date default should be NULL"
        );

        $driver->dropTable('zerodate_test', true);
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleZeroDateColumnsInSameTable(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->datetime('created')->notNull()->default('0000-00-00 00:00:00')
            ->datetime('modified')->notNull()->default('0000-00-00 00:00:00')
            ->datetime('publish_up')->notNull()->default('0000-00-00 00:00:00')
            ->datetime('publish_down')->notNull()->default('0000-00-00 00:00:00')
            ->execute();

        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertNull($row->created, "[$dbName] created");
        $this->assertNull($row->modified, "[$dbName] modified");
        $this->assertNull($row->publish_up, "[$dbName] publish_up");
        $this->assertNull($row->publish_down, "[$dbName] publish_down");

        $driver->dropTable('zerodate_test', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function explicitDateValueOverridesZeroDateDefault(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->datetime('created')->notNull()->default('0000-00-00 00:00:00')
            ->execute();

        // Insert with an explicit date value
        $query = new Query($driver);
        $query->insertMany('zerodate_test', [
            ['created' => '2024-06-15 10:30:00'],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->fetch('row');

        $this->assertEquals(
            '2024-06-15 10:30:00',
            $row->created,
            "[$dbName] Explicit date should be stored as-is"
        );

        $driver->dropTable('zerodate_test', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function mixedZeroDateAndNormalDefaults(
        string $dbName,
        Driver $driver
    ): void {
        $driver->dropTable('zerodate_test', true);

        $driver->createTable('zerodate_test')
            ->id()
            ->datetime('created')->notNull()->default('0000-00-00 00:00:00')
            ->integer('status')->notNull()->default(1)
            ->string('title', 255)->default('Untitled')
            ->execute();

        $query = new Query($driver);
        $query->push('zerodate_test', ['id' => 1]);

        $query2 = new Query($driver);
        $row = $query2->from('zerodate_test')
            ->select('*')
            ->whereEquals('id', 1)
            ->fetch('row');

        $this->assertNull(
            $row->created,
            "[$dbName] Zero-date default translated to NULL"
        );
        $this->assertEquals(
            1,
            (int) $row->status,
            "[$dbName] Normal integer default unchanged"
        );
        $this->assertEquals(
            'Untitled',
            $row->title,
            "[$dbName] Normal string default unchanged"
        );

        $driver->dropTable('zerodate_test', true);
    }
}
