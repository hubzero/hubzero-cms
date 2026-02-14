<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\IndexInfo;
use Hubzero\Database\Schema\ForeignKeyInfo;
use Hubzero\Database\Schema\Diff\TableDiff;
use Hubzero\Database\Schema\Diff\ColumnDiff;

/**
 * Comparator tests
 *
 * Tests the schema comparison logic.
 */
class ComparatorTest extends TestCase
{
    /**
     * @var Comparator
     */
    protected $comparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comparator = new Comparator();
    }

    // =========================================================================
    // Type Normalization Tests
    // =========================================================================

    /**
     * Test that integer display widths are ignored
     */
    public function testNormalizeTypeIgnoresIntegerDisplayWidth()
    {
        $comparator = $this->comparator;

        // INT(11) and INT should be equivalent
        $this->assertTrue($comparator->typesAreEquivalent('INT(11)', 'INT'));
        $this->assertTrue($comparator->typesAreEquivalent('int(11)', 'int'));
        $this->assertTrue($comparator->typesAreEquivalent('BIGINT(20)', 'BIGINT'));
        $this->assertTrue($comparator->typesAreEquivalent('TINYINT(1)', 'TINYINT'));
    }

    /**
     * Test that VARCHAR lengths are NOT ignored
     */
    public function testNormalizeTypePreservesVarcharLength()
    {
        $comparator = $this->comparator;

        $this->assertFalse($comparator->typesAreEquivalent('VARCHAR(255)', 'VARCHAR(100)'));
        $this->assertTrue($comparator->typesAreEquivalent('VARCHAR(255)', 'VARCHAR(255)'));
    }

    /**
     * Test type alias normalization
     */
    public function testNormalizeTypeHandlesAliases()
    {
        $comparator = $this->comparator;

        // INTEGER is alias for INT
        $this->assertTrue($comparator->typesAreEquivalent('INTEGER', 'INT'));

        // BOOLEAN is alias for TINYINT
        $this->assertTrue($comparator->typesAreEquivalent('BOOLEAN', 'TINYINT'));
    }

    /**
     * Test unsigned handling
     */
    public function testNormalizeTypeIgnoresUnsigned()
    {
        $comparator = $this->comparator;

        // Unsigned is tracked separately, not in type comparison
        $this->assertTrue($comparator->typesAreEquivalent('INT UNSIGNED', 'INT'));
        $this->assertTrue($comparator->typesAreEquivalent('int unsigned', 'INT'));
    }

    // =========================================================================
    // Column Comparison Tests
    // =========================================================================

    /**
     * Test comparing identical columns returns null
     */
    public function testCompareIdenticalColumnsReturnsNull()
    {
        $col1 = new ColumnInfo([
            'name' => 'id',
            'full_type' => 'INT(11)',
            'nullable' => false,
            'default' => null,
            'auto_increment' => true,
        ]);

        $col2 = new ColumnInfo([
            'name' => 'id',
            'full_type' => 'INT(11)',
            'nullable' => false,
            'default' => null,
            'auto_increment' => true,
        ]);

        $diff = $this->comparator->compareColumns($col1, $col2);

        $this->assertNull($diff);
    }

    /**
     * Test detecting type change
     */
    public function testCompareColumnsDetectsTypeChange()
    {
        $col1 = new ColumnInfo([
            'name' => 'status',
            'full_type' => 'INT',
            'nullable' => false,
        ]);

        $col2 = new ColumnInfo([
            'name' => 'status',
            'full_type' => 'BIGINT',
            'nullable' => false,
        ]);

        $diff = $this->comparator->compareColumns($col1, $col2);

        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertTrue($diff->hasTypeChanged());
        $this->assertFalse($diff->hasNullableChanged());
    }

    /**
     * Test detecting nullable change
     */
    public function testCompareColumnsDetectsNullableChange()
    {
        $col1 = new ColumnInfo([
            'name' => 'email',
            'full_type' => 'VARCHAR(255)',
            'nullable' => true,
        ]);

        $col2 = new ColumnInfo([
            'name' => 'email',
            'full_type' => 'VARCHAR(255)',
            'nullable' => false,
        ]);

        $diff = $this->comparator->compareColumns($col1, $col2);

        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertTrue($diff->hasNullableChanged());
        $this->assertFalse($diff->hasTypeChanged());
    }

    /**
     * Test detecting default value change
     */
    public function testCompareColumnsDetectsDefaultChange()
    {
        $col1 = new ColumnInfo([
            'name' => 'status',
            'full_type' => 'INT',
            'nullable' => false,
            'default' => '0',
        ]);

        $col2 = new ColumnInfo([
            'name' => 'status',
            'full_type' => 'INT',
            'nullable' => false,
            'default' => '1',
        ]);

        $diff = $this->comparator->compareColumns($col1, $col2);

        $this->assertInstanceOf(ColumnDiff::class, $diff);
        $this->assertTrue($diff->hasDefaultChanged());
    }

    /**
     * Test getChanges returns list of what changed
     */
    public function testColumnDiffGetChanges()
    {
        $col1 = new ColumnInfo([
            'name' => 'data',
            'full_type' => 'VARCHAR(100)',
            'nullable' => true,
            'default' => null,
        ]);

        $col2 = new ColumnInfo([
            'name' => 'data',
            'full_type' => 'TEXT',
            'nullable' => false,
            'default' => '',
        ]);

        $diff = $this->comparator->compareColumns($col1, $col2);

        $changes = $diff->getChanges();
        $this->assertContains('type', $changes);
        $this->assertContains('nullable', $changes);
        $this->assertContains('default', $changes);
    }

    // =========================================================================
    // Table Comparison Tests
    // =========================================================================

    /**
     * Test comparing identical tables returns empty diff
     */
    public function testCompareIdenticalTablesReturnsEmptyDiff()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false, 'auto_increment' => true],
                ['name' => 'name', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false, 'auto_increment' => true],
                ['name' => 'name', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertTrue($diff->isEmpty());
    }

    /**
     * Test detecting added column
     */
    public function testCompareTablesDetectsAddedColumn()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getAddedColumns());
        $this->assertEquals('email', $diff->getAddedColumns()[0]->getName());
    }

    /**
     * Test detecting removed column
     */
    public function testCompareTablesDetectsRemovedColumn()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'legacy_field', 'full_type' => 'VARCHAR(255)', 'nullable' => true],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getRemovedColumns());
        $this->assertEquals('legacy_field', $diff->getRemovedColumns()[0]->getName());
    }

    /**
     * Test detecting changed column
     */
    public function testCompareTablesDetectsChangedColumn()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'name', 'full_type' => 'VARCHAR(100)', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'name', 'full_type' => 'VARCHAR(255)', 'nullable' => true],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getChangedColumns());

        $colDiff = $diff->getChangedColumns()[0];
        $this->assertEquals('name', $colDiff->getName());
        $this->assertTrue($colDiff->hasTypeChanged());
        $this->assertTrue($colDiff->hasNullableChanged());
    }

    /**
     * Test detecting multiple changes
     */
    public function testCompareTablesDetectsMultipleChanges()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'old_col', 'full_type' => 'TEXT', 'nullable' => true],
                ['name' => 'changed_col', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'new_col', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
                ['name' => 'changed_col', 'full_type' => 'BIGINT', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertFalse($diff->isEmpty());
        $this->assertCount(1, $diff->getAddedColumns());
        $this->assertCount(1, $diff->getRemovedColumns());
        $this->assertCount(1, $diff->getChangedColumns());

        $this->assertEquals('new_col', $diff->getAddedColumns()[0]->getName());
        $this->assertEquals('old_col', $diff->getRemovedColumns()[0]->getName());
        $this->assertEquals('changed_col', $diff->getChangedColumns()[0]->getName());
    }

    // =========================================================================
    // Index Comparison Tests
    // =========================================================================

    /**
     * Test detecting added index
     */
    public function testCompareTablesDetectsAddedIndex()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false]],
            'indexes' => [],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false]],
            'indexes' => [
                ['name' => 'idx_email', 'columns' => ['email'], 'unique' => true, 'primary' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertCount(1, $diff->getAddedIndexes());
        $this->assertEquals('idx_email', $diff->getAddedIndexes()[0]->getName());
    }

    /**
     * Test detecting removed index
     */
    public function testCompareTablesDetectsRemovedIndex()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false]],
            'indexes' => [
                ['name' => 'idx_email', 'columns' => ['email'], 'unique' => false, 'primary' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false]],
            'indexes' => [],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertCount(1, $diff->getRemovedIndexes());
        $this->assertEquals('idx_email', $diff->getRemovedIndexes()[0]->getName());
    }

    // =========================================================================
    // Foreign Key Comparison Tests
    // =========================================================================

    /**
     * Test detecting added foreign key
     */
    public function testCompareTablesDetectsAddedForeignKey()
    {
        $table1 = new TableInfo([
            'name' => 'posts',
            'columns' => [['name' => 'user_id', 'full_type' => 'INT', 'nullable' => false]],
            'foreign_keys' => [],
        ]);

        $table2 = new TableInfo([
            'name' => 'posts',
            'columns' => [['name' => 'user_id', 'full_type' => 'INT', 'nullable' => false]],
            'foreign_keys' => [
                [
                    'name' => 'fk_user',
                    'columns' => ['user_id'],
                    'foreign_table' => 'users',
                    'foreign_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                    'on_update' => 'NO ACTION',
                ],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertCount(1, $diff->getAddedForeignKeys());
        $this->assertEquals('fk_user', $diff->getAddedForeignKeys()[0]->getName());
    }

    // =========================================================================
    // Destructive Change Detection Tests
    // =========================================================================

    /**
     * Test hasDestructiveChanges detects dropped columns
     */
    public function testHasDestructiveChangesDetectsDroppedColumn()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'data', 'full_type' => 'TEXT', 'nullable' => true],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertTrue($diff->hasDestructiveChanges());
        $this->assertNotEmpty($diff->getDestructiveWarnings());
    }

    /**
     * Test hasDestructiveChanges detects type changes
     */
    public function testHasDestructiveChangesDetectsTypeChange()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'data', 'full_type' => 'TEXT', 'nullable' => true],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'data', 'full_type' => 'VARCHAR(100)', 'nullable' => true],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);

        $this->assertTrue($diff->hasDestructiveChanges());
    }

    // =========================================================================
    // Summary and Serialization Tests
    // =========================================================================

    /**
     * Test getSummary returns correct counts
     */
    public function testGetSummaryReturnsCorrectCounts()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'removed', 'full_type' => 'TEXT', 'nullable' => true],
                ['name' => 'changed', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'added', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
                ['name' => 'changed', 'full_type' => 'BIGINT', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);
        $summary = $diff->getSummary();

        $this->assertEquals(1, $summary['columns']['added']);
        $this->assertEquals(1, $summary['columns']['removed']);
        $this->assertEquals(1, $summary['columns']['changed']);
    }

    /**
     * Test toArray serialization
     */
    public function testToArraySerialization()
    {
        $table1 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
            ],
        ]);

        $table2 = new TableInfo([
            'name' => 'users',
            'columns' => [
                ['name' => 'id', 'full_type' => 'INT', 'nullable' => false],
                ['name' => 'email', 'full_type' => 'VARCHAR(255)', 'nullable' => false],
            ],
        ]);

        $diff = $this->comparator->compareTables($table1, $table2);
        $array = $diff->toArray();

        $this->assertArrayHasKey('table', $array);
        $this->assertArrayHasKey('columns', $array);
        $this->assertArrayHasKey('summary', $array);
        $this->assertEquals('users', $array['table']);
    }
}
