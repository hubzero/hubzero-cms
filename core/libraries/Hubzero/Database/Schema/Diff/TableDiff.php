<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Diff;

use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\IndexInfo;
use Hubzero\Database\Schema\ForeignKeyInfo;

/**
 * Represents the differences between two table definitions
 *
 * This value object captures all changes needed to transform one table
 * schema into another, including column, index, and foreign key changes.
 *
 * Usage:
 * ```php
 * $diff = $comparator->compareTables($oldTable, $newTable);
 *
 * if (!$diff->isEmpty()) {
 *     foreach ($diff->getAddedColumns() as $col) {
 *         echo "Add column: {$col->getName()}\n";
 *     }
 *     foreach ($diff->getRemovedColumns() as $col) {
 *         echo "Drop column: {$col->getName()}\n";
 *     }
 *     foreach ($diff->getChangedColumns() as $colDiff) {
 *         echo "Modify column: {$colDiff->getName()}\n";
 *     }
 * }
 * ```
 */
class TableDiff
{
    /**
     * Original table definition
     *
     * @var TableInfo
     */
    protected $fromTable;

    /**
     * New table definition
     *
     * @var TableInfo
     */
    protected $toTable;

    /**
     * Columns that were added
     *
     * @var ColumnInfo[]
     */
    protected $addedColumns = [];

    /**
     * Columns that were removed
     *
     * @var ColumnInfo[]
     */
    protected $removedColumns = [];

    /**
     * Columns that were modified
     *
     * @var ColumnDiff[]
     */
    protected $changedColumns = [];

    /**
     * Columns that were renamed (old name => new name)
     *
     * @var array
     */
    protected $renamedColumns = [];

    /**
     * Indexes that were added
     *
     * @var IndexInfo[]
     */
    protected $addedIndexes = [];

    /**
     * Indexes that were removed
     *
     * @var IndexInfo[]
     */
    protected $removedIndexes = [];

    /**
     * Indexes that were modified
     *
     * @var IndexDiff[]
     */
    protected $changedIndexes = [];

    /**
     * Foreign keys that were added
     *
     * @var ForeignKeyInfo[]
     */
    protected $addedForeignKeys = [];

    /**
     * Foreign keys that were removed
     *
     * @var ForeignKeyInfo[]
     */
    protected $removedForeignKeys = [];

    /**
     * Foreign keys that were modified
     *
     * @var ForeignKeyDiff[]
     */
    protected $changedForeignKeys = [];

    /**
     * Whether the storage engine changed
     *
     * @var bool
     */
    protected $engineChanged = false;

    /**
     * Whether the character set changed
     *
     * @var bool
     */
    protected $charsetChanged = false;

    /**
     * Whether the collation changed
     *
     * @var bool
     */
    protected $collationChanged = false;

    /**
     * Whether the table was renamed
     *
     * @var bool
     */
    protected $tableRenamed = false;

    /**
     * Old table name (if renamed)
     *
     * @var string|null
     */
    protected $oldTableName = null;

    /**
     * New table name (if renamed)
     *
     * @var string|null
     */
    protected $newTableName = null;

    /**
     * Create a new TableDiff instance
     *
     * @param TableInfo $fromTable Original table
     * @param TableInfo $toTable   New table
     */
    public function __construct(TableInfo $fromTable, TableInfo $toTable)
    {
        $this->fromTable = $fromTable;
        $this->toTable = $toTable;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->fromTable->getName();
    }

    /**
     * Get the original table definition
     *
     * @return TableInfo
     */
    public function getFromTable(): TableInfo
    {
        return $this->fromTable;
    }

    /**
     * Get the new table definition
     *
     * @return TableInfo
     */
    public function getToTable(): TableInfo
    {
        return $this->toTable;
    }

    // =========================================================================
    // Column Changes
    // =========================================================================

    /**
     * Add an added column
     *
     * @param  ColumnInfo  $column
     * @return $this
     */
    public function addAddedColumn(ColumnInfo $column): self
    {
        $this->addedColumns[] = $column;
        return $this;
    }

    /**
     * Get columns that were added
     *
     * @return ColumnInfo[]
     */
    public function getAddedColumns(): array
    {
        return $this->addedColumns;
    }

    /**
     * Add a removed column
     *
     * @param  ColumnInfo  $column
     * @return $this
     */
    public function addRemovedColumn(ColumnInfo $column): self
    {
        $this->removedColumns[] = $column;
        return $this;
    }

    /**
     * Get columns that were removed
     *
     * @return ColumnInfo[]
     */
    public function getRemovedColumns(): array
    {
        return $this->removedColumns;
    }

    /**
     * Add a changed column
     *
     * @param  ColumnDiff  $columnDiff
     * @return $this
     */
    public function addChangedColumn(ColumnDiff $columnDiff): self
    {
        $this->changedColumns[] = $columnDiff;
        return $this;
    }

    /**
     * Get columns that were modified
     *
     * @return ColumnDiff[]
     */
    public function getChangedColumns(): array
    {
        return $this->changedColumns;
    }

    /**
     * Add a renamed column
     *
     * @param  string  $oldName
     * @param  string  $newName
     * @return $this
     */
    public function addRenamedColumn(string $oldName, string $newName): self
    {
        $this->renamedColumns[$oldName] = $newName;
        return $this;
    }

    /**
     * Get columns that were renamed
     *
     * @return array  Old name => new name
     */
    public function getRenamedColumns(): array
    {
        return $this->renamedColumns;
    }

    /**
     * Check if there are any column changes
     *
     * @return bool
     */
    public function hasColumnChanges(): bool
    {
        return !empty($this->addedColumns)
            || !empty($this->removedColumns)
            || !empty($this->changedColumns)
            || !empty($this->renamedColumns);
    }

    // =========================================================================
    // Index Changes
    // =========================================================================

    /**
     * Add an added index
     *
     * @param  IndexInfo  $index
     * @return $this
     */
    public function addAddedIndex(IndexInfo $index): self
    {
        $this->addedIndexes[] = $index;
        return $this;
    }

    /**
     * Get indexes that were added
     *
     * @return IndexInfo[]
     */
    public function getAddedIndexes(): array
    {
        return $this->addedIndexes;
    }

    /**
     * Add a removed index
     *
     * @param  IndexInfo  $index
     * @return $this
     */
    public function addRemovedIndex(IndexInfo $index): self
    {
        $this->removedIndexes[] = $index;
        return $this;
    }

    /**
     * Get indexes that were removed
     *
     * @return IndexInfo[]
     */
    public function getRemovedIndexes(): array
    {
        return $this->removedIndexes;
    }

    /**
     * Add a changed index
     *
     * @param  IndexDiff  $indexDiff
     * @return $this
     */
    public function addChangedIndex(IndexDiff $indexDiff): self
    {
        $this->changedIndexes[] = $indexDiff;
        return $this;
    }

    /**
     * Get indexes that were modified
     *
     * @return IndexDiff[]
     */
    public function getChangedIndexes(): array
    {
        return $this->changedIndexes;
    }

    /**
     * Check if there are any index changes
     *
     * @return bool
     */
    public function hasIndexChanges(): bool
    {
        return !empty($this->addedIndexes)
            || !empty($this->removedIndexes)
            || !empty($this->changedIndexes);
    }

    // =========================================================================
    // Foreign Key Changes
    // =========================================================================

    /**
     * Add an added foreign key
     *
     * @param  ForeignKeyInfo  $foreignKey
     * @return $this
     */
    public function addAddedForeignKey(ForeignKeyInfo $foreignKey): self
    {
        $this->addedForeignKeys[] = $foreignKey;
        return $this;
    }

    /**
     * Get foreign keys that were added
     *
     * @return ForeignKeyInfo[]
     */
    public function getAddedForeignKeys(): array
    {
        return $this->addedForeignKeys;
    }

    /**
     * Add a removed foreign key
     *
     * @param  ForeignKeyInfo  $foreignKey
     * @return $this
     */
    public function addRemovedForeignKey(ForeignKeyInfo $foreignKey): self
    {
        $this->removedForeignKeys[] = $foreignKey;
        return $this;
    }

    /**
     * Get foreign keys that were removed
     *
     * @return ForeignKeyInfo[]
     */
    public function getRemovedForeignKeys(): array
    {
        return $this->removedForeignKeys;
    }

    /**
     * Add a changed foreign key
     *
     * @param  ForeignKeyDiff  $foreignKeyDiff
     * @return $this
     */
    public function addChangedForeignKey(ForeignKeyDiff $foreignKeyDiff): self
    {
        $this->changedForeignKeys[] = $foreignKeyDiff;
        return $this;
    }

    /**
     * Get foreign keys that were modified
     *
     * @return ForeignKeyDiff[]
     */
    public function getChangedForeignKeys(): array
    {
        return $this->changedForeignKeys;
    }

    /**
     * Check if there are any foreign key changes
     *
     * @return bool
     */
    public function hasForeignKeyChanges(): bool
    {
        return !empty($this->addedForeignKeys)
            || !empty($this->removedForeignKeys)
            || !empty($this->changedForeignKeys);
    }

    // =========================================================================
    // Table-Level Changes
    // =========================================================================

    /**
     * Mark that the engine changed
     *
     * @return $this
     */
    public function setEngineChanged(): self
    {
        $this->engineChanged = true;
        return $this;
    }

    /**
     * Check if the engine changed
     *
     * @return bool
     */
    public function hasEngineChanged(): bool
    {
        return $this->engineChanged;
    }

    /**
     * Mark that the charset changed
     *
     * @return $this
     */
    public function setCharsetChanged(): self
    {
        $this->charsetChanged = true;
        return $this;
    }

    /**
     * Check if the charset changed
     *
     * @return bool
     */
    public function hasCharsetChanged(): bool
    {
        return $this->charsetChanged;
    }

    /**
     * Mark that the collation changed
     *
     * @return $this
     */
    public function setCollationChanged(): self
    {
        $this->collationChanged = true;
        return $this;
    }

    /**
     * Check if the collation changed
     *
     * @return bool
     */
    public function hasCollationChanged(): bool
    {
        return $this->collationChanged;
    }

    /**
     * Mark that the table was renamed
     *
     * @param  string  $oldName  Original table name
     * @param  string  $newName  New table name
     * @return $this
     */
    public function setRenamed(string $oldName, string $newName): self
    {
        $this->tableRenamed = true;
        $this->oldTableName = $oldName;
        $this->newTableName = $newName;
        return $this;
    }

    /**
     * Check if the table was renamed
     *
     * @return bool
     */
    public function isRenamed(): bool
    {
        return $this->tableRenamed;
    }

    /**
     * Get the old table name (before rename)
     *
     * @return string|null
     */
    public function getOldTableName(): ?string
    {
        return $this->oldTableName;
    }

    /**
     * Get the new table name (after rename)
     *
     * @return string|null
     */
    public function getNewTableName(): ?string
    {
        return $this->newTableName;
    }

    /**
     * Check if there are any table-level changes
     *
     * @return bool
     */
    public function hasTableLevelChanges(): bool
    {
        return $this->engineChanged
            || $this->charsetChanged
            || $this->collationChanged
            || $this->tableRenamed;
    }

    // =========================================================================
    // Summary Methods
    // =========================================================================

    /**
     * Check if the diff is empty (no changes)
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->hasColumnChanges()
            && !$this->hasIndexChanges()
            && !$this->hasForeignKeyChanges()
            && !$this->hasTableLevelChanges();
    }

    /**
     * Get a summary of changes
     *
     * @return array
     */
    public function getSummary(): array
    {
        return [
            'columns' => [
                'added' => count($this->addedColumns),
                'removed' => count($this->removedColumns),
                'changed' => count($this->changedColumns),
                'renamed' => count($this->renamedColumns),
            ],
            'indexes' => [
                'added' => count($this->addedIndexes),
                'removed' => count($this->removedIndexes),
                'changed' => count($this->changedIndexes),
            ],
            'foreign_keys' => [
                'added' => count($this->addedForeignKeys),
                'removed' => count($this->removedForeignKeys),
                'changed' => count($this->changedForeignKeys),
            ],
            'table' => [
                'engine_changed' => $this->engineChanged,
                'charset_changed' => $this->charsetChanged,
                'collation_changed' => $this->collationChanged,
            ],
        ];
    }

    /**
     * Check if any changes are destructive (could lose data)
     *
     * @return bool
     */
    public function hasDestructiveChanges(): bool
    {
        // Removing columns is destructive
        if (!empty($this->removedColumns)) {
            return true;
        }

        // Changing column types might be destructive
        foreach ($this->changedColumns as $colDiff) {
            if ($colDiff->hasTypeChanged()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a list of destructive changes with warnings
     *
     * @return array
     */
    public function getDestructiveWarnings(): array
    {
        $warnings = [];

        foreach ($this->removedColumns as $column) {
            $warnings[] = "DROP COLUMN `{$column->getName()}` will permanently delete data";
        }

        foreach ($this->changedColumns as $colDiff) {
            if ($colDiff->hasTypeChanged()) {
                $from = $colDiff->getFromColumn()->getFullType();
                $to = $colDiff->getToColumn()->getFullType();
                $warnings[] = "MODIFY COLUMN `{$colDiff->getName()}` from {$from} to {$to} may truncate data";
            }
        }

        return $warnings;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'table' => $this->getName(),
            'from' => $this->fromTable->toArray(),
            'to' => $this->toTable->toArray(),
            'columns' => [
                'added' => array_map(fn($c) => $c->toArray(), $this->addedColumns),
                'removed' => array_map(fn($c) => $c->toArray(), $this->removedColumns),
                'changed' => array_map(fn($c) => $c->toArray(), $this->changedColumns),
                'renamed' => $this->renamedColumns,
            ],
            'indexes' => [
                'added' => array_map(fn($i) => $i->toArray(), $this->addedIndexes),
                'removed' => array_map(fn($i) => $i->toArray(), $this->removedIndexes),
                'changed' => array_map(fn($i) => $i->toArray(), $this->changedIndexes),
            ],
            'foreign_keys' => [
                'added' => array_map(fn($f) => $f->toArray(), $this->addedForeignKeys),
                'removed' => array_map(fn($f) => $f->toArray(), $this->removedForeignKeys),
                'changed' => array_map(fn($f) => $f->toArray(), $this->changedForeignKeys),
            ],
            'table_changes' => [
                'engine' => $this->engineChanged,
                'charset' => $this->charsetChanged,
                'collation' => $this->collationChanged,
            ],
            'summary' => $this->getSummary(),
            'destructive' => $this->hasDestructiveChanges(),
            'warnings' => $this->getDestructiveWarnings(),
        ];
    }
}
