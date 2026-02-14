<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;
use Hubzero\Database\Schema\Diff\ColumnDiff;
use Hubzero\Database\Schema\Diff\IndexDiff;
use Hubzero\Database\Schema\Diff\ForeignKeyDiff;

/**
 * Schema Comparator
 *
 * Compares two schema representations and identifies the differences.
 * Supports explicit rename hints and optional heuristic rename detection.
 *
 * Usage:
 * ```php
 * $comparator = new Comparator();
 *
 * // Compare two tables
 * $diff = $comparator->compareTables($oldTable, $newTable);
 *
 * if (!$diff->isEmpty()) {
 *     foreach ($diff->getAddedColumns() as $col) {
 *         echo "Added: {$col->getName()}\n";
 *     }
 * }
 *
 * // With rename hints
 * $comparator = new Comparator();
 * $comparator
 *     ->addTableRenameHint('old_users', 'users')
 *     ->addColumnRenameHint('users', 'fname', 'first_name')
 *     ->addColumnRenameHint('users', 'lname', 'last_name');
 *
 * $diff = $comparator->compareSchemas($from, $to);
 *
 * // With heuristic detection (opt-in)
 * $comparator = new Comparator();
 * $comparator->enableHeuristicRenameDetection(0.7);  // 70% similarity threshold
 * ```
 */
class Comparator
{
    /**
     * Type aliases for normalization
     *
     * Maps various type names to canonical forms for comparison.
     *
     * @var array
     */
    protected $typeAliases = [
        'integer' => 'int',
        'boolean' => 'tinyint',
        'bool' => 'tinyint',
        'serial' => 'int',
        'bigserial' => 'bigint',
        'smallserial' => 'smallint',
        'real' => 'float',
        'double precision' => 'double',
        'numeric' => 'decimal',
    ];

    /**
     * Integer types (display width should be ignored for comparison)
     *
     * @var array
     */
    protected $integerTypes = [
        'tinyint', 'smallint', 'mediumint', 'int', 'bigint'
    ];

    /**
     * Table rename hints: old_name => new_name
     *
     * @var array
     */
    protected $tableRenameHints = [];

    /**
     * Column rename hints: table_name => [old_col => new_col, ...]
     *
     * @var array
     */
    protected $columnRenameHints = [];

    /**
     * Whether heuristic rename detection is enabled
     *
     * @var bool
     */
    protected $heuristicRenameDetection = false;

    /**
     * Similarity threshold for heuristic rename detection (0.0 - 1.0)
     *
     * @var float
     */
    protected $renameSimilarityThreshold = 0.7;

    // =========================================================================
    // Rename Hints Configuration
    // =========================================================================

    /**
     * Add a table rename hint
     *
     * When comparing schemas, this tells the comparator that a table was
     * renamed rather than dropped and recreated.
     *
     * @param  string  $oldName  Original table name
     * @param  string  $newName  New table name
     * @return $this
     */
    public function addTableRenameHint(string $oldName, string $newName): self
    {
        $this->tableRenameHints[$oldName] = $newName;
        return $this;
    }

    /**
     * Add multiple table rename hints at once
     *
     * @param  array  $hints  [old_name => new_name, ...]
     * @return $this
     */
    public function addTableRenameHints(array $hints): self
    {
        foreach ($hints as $oldName => $newName) {
            $this->tableRenameHints[$oldName] = $newName;
        }
        return $this;
    }

    /**
     * Add a column rename hint for a specific table
     *
     * When comparing tables, this tells the comparator that a column was
     * renamed rather than dropped and recreated.
     *
     * @param  string  $tableName  Table name (new name if table was also renamed)
     * @param  string  $oldColumn  Original column name
     * @param  string  $newColumn  New column name
     * @return $this
     */
    public function addColumnRenameHint(string $tableName, string $oldColumn, string $newColumn): self
    {
        if (!isset($this->columnRenameHints[$tableName])) {
            $this->columnRenameHints[$tableName] = [];
        }
        $this->columnRenameHints[$tableName][$oldColumn] = $newColumn;
        return $this;
    }

    /**
     * Add multiple column rename hints for a table
     *
     * @param  string  $tableName  Table name
     * @param  array   $hints      [old_col => new_col, ...]
     * @return $this
     */
    public function addColumnRenameHints(string $tableName, array $hints): self
    {
        if (!isset($this->columnRenameHints[$tableName])) {
            $this->columnRenameHints[$tableName] = [];
        }
        foreach ($hints as $oldCol => $newCol) {
            $this->columnRenameHints[$tableName][$oldCol] = $newCol;
        }
        return $this;
    }

    /**
     * Enable heuristic rename detection
     *
     * When enabled, the comparator will attempt to detect renames by comparing
     * the similarity of column/table definitions. This is opt-in because it
     * can produce false positives.
     *
     * @param  float  $threshold  Similarity threshold (0.0 - 1.0), default 0.7
     * @return $this
     */
    public function enableHeuristicRenameDetection(float $threshold = 0.7): self
    {
        $this->heuristicRenameDetection = true;
        $this->renameSimilarityThreshold = max(0.0, min(1.0, $threshold));
        return $this;
    }

    /**
     * Disable heuristic rename detection
     *
     * @return $this
     */
    public function disableHeuristicRenameDetection(): self
    {
        $this->heuristicRenameDetection = false;
        return $this;
    }

    /**
     * Get table rename hints
     *
     * @return array  [old_name => new_name, ...]
     */
    public function getTableRenameHints(): array
    {
        return $this->tableRenameHints;
    }

    /**
     * Get column rename hints for a specific table
     *
     * @param  string  $tableName
     * @return array  [old_col => new_col, ...]
     */
    public function getColumnRenameHints(string $tableName): array
    {
        return $this->columnRenameHints[$tableName] ?? [];
    }

    /**
     * Clear all rename hints
     *
     * @return $this
     */
    public function clearHints(): self
    {
        $this->tableRenameHints = [];
        $this->columnRenameHints = [];
        return $this;
    }

    // =========================================================================
    // Table Comparison
    // =========================================================================

    /**
     * Compare two tables and return the differences
     *
     * @param  TableInfo  $fromTable  Original table
     * @param  TableInfo  $toTable    New table
     * @return TableDiff
     */
    public function compareTables(TableInfo $fromTable, TableInfo $toTable): TableDiff
    {
        $diff = new TableDiff($fromTable, $toTable);

        // Compare columns
        $this->compareTableColumns($diff, $fromTable, $toTable);

        // Compare indexes
        $this->compareTableIndexes($diff, $fromTable, $toTable);

        // Compare foreign keys
        $this->compareTableForeignKeys($diff, $fromTable, $toTable);

        // Compare table-level properties
        $this->compareTableProperties($diff, $fromTable, $toTable);

        return $diff;
    }

    /**
     * Compare two database schemas and return the differences
     *
     * Identifies:
     * - Tables that were added (exist in $to but not in $from)
     * - Tables that were removed (exist in $from but not in $to)
     * - Tables that were modified (exist in both but have differences)
     * - Tables that were renamed (via hints or heuristic detection)
     *
     * Usage:
     * ```php
     * $comparator = new Comparator();
     * $diff = $comparator->compareSchemas($oldSchema, $newSchema);
     *
     * foreach ($diff->getAddedTables() as $table) {
     *     echo "New table: {$table->getName()}\n";
     * }
     *
     * foreach ($diff->getChangedTables() as $tableDiff) {
     *     echo "Modified: {$tableDiff->getName()}\n";
     * }
     *
     * foreach ($diff->getRenamedTables() as $oldName => $newName) {
     *     echo "Renamed: {$oldName} -> {$newName}\n";
     * }
     * ```
     *
     * @param  DatabaseInfo  $fromSchema  Original database schema
     * @param  DatabaseInfo  $toSchema    New database schema
     * @return SchemaDiff
     */
    public function compareSchemas(DatabaseInfo $fromSchema, DatabaseInfo $toSchema): SchemaDiff
    {
        $diff = new SchemaDiff($fromSchema, $toSchema);

        $fromTables = $this->indexByName($fromSchema->getTables());
        $toTables = $this->indexByName($toSchema->getTables());

        // Track which tables have been matched (either same name, renamed, or detected)
        $matchedFromTables = [];
        $matchedToTables = [];

        // 1. Process explicit table rename hints first
        foreach ($this->tableRenameHints as $oldName => $newName) {
            if (isset($fromTables[$oldName]) && isset($toTables[$newName])) {
                $diff->addRenamedTable($oldName, $newName);
                $matchedFromTables[$oldName] = true;
                $matchedToTables[$newName] = true;

                // Also compare the renamed tables for other changes
                $tableDiff = $this->compareTables($fromTables[$oldName], $toTables[$newName]);
                $tableDiff->setRenamed($oldName, $newName);
                if (!$tableDiff->isEmpty()) {
                    $diff->addChangedTable($tableDiff);
                }
            }
        }

        // 2. Find tables with same name (unchanged or modified)
        foreach ($fromTables as $name => $fromTable) {
            if (isset($matchedFromTables[$name])) {
                continue; // Already processed as rename
            }
            if (isset($toTables[$name])) {
                $matchedFromTables[$name] = true;
                $matchedToTables[$name] = true;

                $tableDiff = $this->compareTables($fromTable, $toTables[$name]);
                if (!$tableDiff->isEmpty()) {
                    $diff->addChangedTable($tableDiff);
                }
            }
        }

        // 3. Identify unmatched tables (potential adds/removes/renames)
        $unmatchedFrom = [];
        $unmatchedTo = [];

        foreach ($fromTables as $name => $table) {
            if (!isset($matchedFromTables[$name])) {
                $unmatchedFrom[$name] = $table;
            }
        }

        foreach ($toTables as $name => $table) {
            if (!isset($matchedToTables[$name])) {
                $unmatchedTo[$name] = $table;
            }
        }

        // 4. Heuristic rename detection (if enabled)
        if ($this->heuristicRenameDetection && !empty($unmatchedFrom) && !empty($unmatchedTo)) {
            $detectedRenames = $this->detectTableRenames($unmatchedFrom, $unmatchedTo);

            foreach ($detectedRenames as $oldName => $newName) {
                $diff->addRenamedTable($oldName, $newName);

                // Compare renamed tables for other changes
                $tableDiff = $this->compareTables($unmatchedFrom[$oldName], $unmatchedTo[$newName]);
                $tableDiff->setRenamed($oldName, $newName);
                if (!$tableDiff->isEmpty()) {
                    $diff->addChangedTable($tableDiff);
                }

                unset($unmatchedFrom[$oldName]);
                unset($unmatchedTo[$newName]);
            }
        }

        // 5. Remaining unmatched tables are truly added/removed
        foreach ($unmatchedTo as $table) {
            $diff->addAddedTable($table);
        }

        foreach ($unmatchedFrom as $table) {
            $diff->addRemovedTable($table);
        }

        return $diff;
    }

    /**
     * Detect table renames using heuristics
     *
     * Compares table structures to find likely renames based on column similarity.
     *
     * @param  array  $fromTables  Unmatched tables from source [name => TableInfo]
     * @param  array  $toTables    Unmatched tables from target [name => TableInfo]
     * @return array  [old_name => new_name, ...]
     */
    protected function detectTableRenames(array $fromTables, array $toTables): array
    {
        $renames = [];
        $usedTo = [];

        foreach ($fromTables as $fromName => $fromTable) {
            $bestMatch = null;
            $bestSimilarity = 0;

            foreach ($toTables as $toName => $toTable) {
                if (isset($usedTo[$toName])) {
                    continue;
                }

                $similarity = $this->calculateTableSimilarity($fromTable, $toTable);

                if ($similarity >= $this->renameSimilarityThreshold && $similarity > $bestSimilarity) {
                    $bestMatch = $toName;
                    $bestSimilarity = $similarity;
                }
            }

            if ($bestMatch !== null) {
                $renames[$fromName] = $bestMatch;
                $usedTo[$bestMatch] = true;
            }
        }

        return $renames;
    }

    /**
     * Calculate similarity between two tables
     *
     * Returns a value between 0.0 (completely different) and 1.0 (identical structure).
     *
     * @param  TableInfo  $tableA
     * @param  TableInfo  $tableB
     * @return float
     */
    protected function calculateTableSimilarity(TableInfo $tableA, TableInfo $tableB): float
    {
        $columnsA = $this->indexByName($tableA->getColumns());
        $columnsB = $this->indexByName($tableB->getColumns());

        if (empty($columnsA) && empty($columnsB)) {
            return 1.0;
        }
        if (empty($columnsA) || empty($columnsB)) {
            return 0.0;
        }

        $allColumns = array_unique(array_merge(array_keys($columnsA), array_keys($columnsB)));
        $matchingColumns = 0;

        foreach ($allColumns as $colName) {
            if (isset($columnsA[$colName]) && isset($columnsB[$colName])) {
                // Column exists in both - check if types are similar
                if (
                    $this->typesAreEquivalent(
                        $columnsA[$colName]->getFullType(),
                        $columnsB[$colName]->getFullType()
                    )
                ) {
                    $matchingColumns += 1.0;
                } else {
                    $matchingColumns += 0.5; // Same name, different type
                }
            }
        }

        return $matchingColumns / count($allColumns);
    }

    /**
     * Compare columns between two tables
     *
     * @param  TableDiff  $diff
     * @param  TableInfo  $fromTable
     * @param  TableInfo  $toTable
     * @return void
     */
    protected function compareTableColumns(TableDiff $diff, TableInfo $fromTable, TableInfo $toTable): void
    {
        $fromColumns = $this->indexByName($fromTable->getColumns());
        $toColumns = $this->indexByName($toTable->getColumns());

        // Get column rename hints for this table (check both old and new table names)
        $tableName = $toTable->getName();
        $oldTableName = $fromTable->getName();
        $columnHints = array_merge(
            $this->getColumnRenameHints($tableName),
            $this->getColumnRenameHints($oldTableName)
        );

        // Track which columns have been matched
        $matchedFromColumns = [];
        $matchedToColumns = [];

        // 1. Process explicit column rename hints first
        foreach ($columnHints as $oldName => $newName) {
            if (isset($fromColumns[$oldName]) && isset($toColumns[$newName])) {
                $diff->addRenamedColumn($oldName, $newName);
                $matchedFromColumns[$oldName] = true;
                $matchedToColumns[$newName] = true;

                // Also check for other changes to the renamed column
                $colDiff = $this->compareColumns($fromColumns[$oldName], $toColumns[$newName]);
                if ($colDiff !== null && $colDiff->hasChanges()) {
                    $colDiff->setRenamed($oldName, $newName);
                    $diff->addChangedColumn($colDiff);
                }
            }
        }

        // 2. Find columns with same name (unchanged or modified)
        foreach ($fromColumns as $name => $fromCol) {
            if (isset($matchedFromColumns[$name])) {
                continue; // Already processed as rename
            }
            if (isset($toColumns[$name])) {
                $matchedFromColumns[$name] = true;
                $matchedToColumns[$name] = true;

                $colDiff = $this->compareColumns($fromCol, $toColumns[$name]);
                if ($colDiff !== null && $colDiff->hasChanges()) {
                    $diff->addChangedColumn($colDiff);
                }
            }
        }

        // 3. Identify unmatched columns
        $unmatchedFrom = [];
        $unmatchedTo = [];

        foreach ($fromColumns as $name => $column) {
            if (!isset($matchedFromColumns[$name])) {
                $unmatchedFrom[$name] = $column;
            }
        }

        foreach ($toColumns as $name => $column) {
            if (!isset($matchedToColumns[$name])) {
                $unmatchedTo[$name] = $column;
            }
        }

        // 4. Heuristic rename detection (if enabled)
        if ($this->heuristicRenameDetection && !empty($unmatchedFrom) && !empty($unmatchedTo)) {
            $detectedRenames = $this->detectColumnRenames($unmatchedFrom, $unmatchedTo);

            foreach ($detectedRenames as $oldName => $newName) {
                $diff->addRenamedColumn($oldName, $newName);

                // Check for other changes to the renamed column
                $colDiff = $this->compareColumns($unmatchedFrom[$oldName], $unmatchedTo[$newName]);
                if ($colDiff !== null && $colDiff->hasChanges()) {
                    $colDiff->setRenamed($oldName, $newName);
                    $diff->addChangedColumn($colDiff);
                }

                unset($unmatchedFrom[$oldName]);
                unset($unmatchedTo[$newName]);
            }
        }

        // 5. Remaining unmatched columns are truly added/removed
        foreach ($unmatchedTo as $column) {
            $diff->addAddedColumn($column);
        }

        foreach ($unmatchedFrom as $column) {
            $diff->addRemovedColumn($column);
        }
    }

    /**
     * Detect column renames using heuristics
     *
     * Compares column definitions to find likely renames based on type similarity.
     *
     * @param  array  $fromColumns  Unmatched columns from source [name => ColumnInfo]
     * @param  array  $toColumns    Unmatched columns from target [name => ColumnInfo]
     * @return array  [old_name => new_name, ...]
     */
    protected function detectColumnRenames(array $fromColumns, array $toColumns): array
    {
        $renames = [];
        $usedTo = [];

        foreach ($fromColumns as $fromName => $fromColumn) {
            $bestMatch = null;
            $bestSimilarity = 0;

            foreach ($toColumns as $toName => $toColumn) {
                if (isset($usedTo[$toName])) {
                    continue;
                }

                $similarity = $this->calculateColumnSimilarity($fromColumn, $toColumn, $fromName, $toName);

                if ($similarity >= $this->renameSimilarityThreshold && $similarity > $bestSimilarity) {
                    $bestMatch = $toName;
                    $bestSimilarity = $similarity;
                }
            }

            if ($bestMatch !== null) {
                $renames[$fromName] = $bestMatch;
                $usedTo[$bestMatch] = true;
            }
        }

        return $renames;
    }

    /**
     * Calculate similarity between two columns
     *
     * Returns a value between 0.0 (completely different) and 1.0 (identical).
     *
     * @param  ColumnInfo  $colA
     * @param  ColumnInfo  $colB
     * @param  string      $nameA
     * @param  string      $nameB
     * @return float
     */
    protected function calculateColumnSimilarity(
        ColumnInfo $colA,
        ColumnInfo $colB,
        string $nameA,
        string $nameB
    ): float {
        $score = 0.0;
        $factors = 0;

        // Type similarity (40% weight)
        if ($this->typesAreEquivalent($colA->getFullType(), $colB->getFullType())) {
            $score += 0.4;
        }
        $factors += 0.4;

        // Nullable similarity (15% weight)
        if ($colA->isNullable() === $colB->isNullable()) {
            $score += 0.15;
        }
        $factors += 0.15;

        // Default similarity (15% weight)
        if ($this->defaultsAreEquivalent($colA->getDefault(), $colB->getDefault())) {
            $score += 0.15;
        }
        $factors += 0.15;

        // Name similarity using Levenshtein distance (30% weight)
        $nameSimilarity = $this->calculateNameSimilarity($nameA, $nameB);
        $score += 0.3 * $nameSimilarity;
        $factors += 0.3;

        return $score / $factors;
    }

    /**
     * Calculate similarity between two names
     *
     * Uses Levenshtein distance normalized to a 0-1 scale.
     *
     * @param  string  $nameA
     * @param  string  $nameB
     * @return float
     */
    protected function calculateNameSimilarity(string $nameA, string $nameB): float
    {
        $nameA = strtolower($nameA);
        $nameB = strtolower($nameB);

        if ($nameA === $nameB) {
            return 1.0;
        }

        $maxLen = max(strlen($nameA), strlen($nameB));
        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($nameA, $nameB);
        return 1.0 - ($distance / $maxLen);
    }

    /**
     * Compare two columns and return what changed
     *
     * @param  ColumnInfo  $fromColumn  Original column
     * @param  ColumnInfo  $toColumn    New column
     * @return ColumnDiff|null  Null if columns are identical
     */
    public function compareColumns(ColumnInfo $fromColumn, ColumnInfo $toColumn): ?ColumnDiff
    {
        $diff = new ColumnDiff($fromColumn, $toColumn);

        // Compare type
        if (!$this->typesAreEquivalent($fromColumn->getFullType(), $toColumn->getFullType())) {
            $diff->setTypeChanged();
        }

        // Compare nullable
        if ($fromColumn->isNullable() !== $toColumn->isNullable()) {
            $diff->setNullableChanged();
        }

        // Compare default value (skip for auto-increment columns — sequence
        // names are auto-generated and always differ between tables)
        $bothAutoIncrement = $fromColumn->isAutoIncrement()
            && $toColumn->isAutoIncrement();
        if (
            !$bothAutoIncrement
            && !$this->defaultsAreEquivalent(
                $fromColumn->getDefault(),
                $toColumn->getDefault()
            )
        ) {
            $diff->setDefaultChanged();
        }

        // Compare auto-increment
        if ($fromColumn->isAutoIncrement() !== $toColumn->isAutoIncrement()) {
            $diff->setAutoIncrementChanged();
        }

        // Compare unsigned
        if ($fromColumn->isUnsigned() !== $toColumn->isUnsigned()) {
            $diff->setUnsignedChanged();
        }

        // Compare comment
        if ($fromColumn->getComment() !== $toColumn->getComment()) {
            $diff->setCommentChanged();
        }

        // Compare collation
        if ($fromColumn->getCollation() !== $toColumn->getCollation()) {
            $diff->setCollationChanged();
        }

        return $diff->hasChanges() ? $diff : null;
    }

    /**
     * Compare indexes between two tables
     *
     * @param  TableDiff  $diff
     * @param  TableInfo  $fromTable
     * @param  TableInfo  $toTable
     * @return void
     */
    protected function compareTableIndexes(TableDiff $diff, TableInfo $fromTable, TableInfo $toTable): void
    {
        $fromIndexes = $this->indexByName($fromTable->getIndexes());
        $toIndexes = $this->indexByName($toTable->getIndexes());

        // Find added indexes
        foreach ($toIndexes as $name => $index) {
            if (!isset($fromIndexes[$name])) {
                // Check if there's an equivalent index with different name
                $equivalent = $this->findEquivalentIndex($index, $fromIndexes);
                if ($equivalent === null) {
                    $diff->addAddedIndex($index);
                }
            }
        }

        // Find removed indexes
        foreach ($fromIndexes as $name => $index) {
            if (!isset($toIndexes[$name])) {
                // Check if there's an equivalent index with different name
                $equivalent = $this->findEquivalentIndex($index, $toIndexes);
                if ($equivalent === null) {
                    $diff->addRemovedIndex($index);
                }
            }
        }

        // Find changed indexes
        foreach ($fromIndexes as $name => $fromIndex) {
            if (isset($toIndexes[$name])) {
                $idxDiff = $this->compareIndexes($fromIndex, $toIndexes[$name]);
                if ($idxDiff !== null && $idxDiff->hasChanges()) {
                    $diff->addChangedIndex($idxDiff);
                }
            }
        }
    }

    /**
     * Compare two indexes and return what changed
     *
     * @param  IndexInfo  $fromIndex  Original index
     * @param  IndexInfo  $toIndex    New index
     * @return IndexDiff|null  Null if indexes are identical
     */
    public function compareIndexes(IndexInfo $fromIndex, IndexInfo $toIndex): ?IndexDiff
    {
        $diff = new IndexDiff($fromIndex, $toIndex);

        // Compare columns (order matters)
        if ($fromIndex->getColumns() !== $toIndex->getColumns()) {
            $diff->setColumnsChanged();
        }

        // Compare unique
        if ($fromIndex->isUnique() !== $toIndex->isUnique()) {
            $diff->setUniqueChanged();
        }

        // Compare type (BTREE, HASH, FULLTEXT, etc.)
        if (strtoupper($fromIndex->getType()) !== strtoupper($toIndex->getType())) {
            $diff->setTypeChanged();
        }

        return $diff->hasChanges() ? $diff : null;
    }

    /**
     * Find an index with equivalent columns (regardless of name)
     *
     * @param  IndexInfo  $needle
     * @param  array      $haystack  IndexInfo[] indexed by name
     * @return IndexInfo|null
     */
    protected function findEquivalentIndex(IndexInfo $needle, array $haystack): ?IndexInfo
    {
        $needleColumns = $needle->getColumns();
        $needleUnique = $needle->isUnique();

        foreach ($haystack as $index) {
            if ($index->getColumns() === $needleColumns && $index->isUnique() === $needleUnique) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Compare foreign keys between two tables
     *
     * @param  TableDiff  $diff
     * @param  TableInfo  $fromTable
     * @param  TableInfo  $toTable
     * @return void
     */
    protected function compareTableForeignKeys(TableDiff $diff, TableInfo $fromTable, TableInfo $toTable): void
    {
        $fromFks = $this->indexByName($fromTable->getForeignKeys());
        $toFks = $this->indexByName($toTable->getForeignKeys());

        // Find added foreign keys
        foreach ($toFks as $name => $fk) {
            if (!isset($fromFks[$name])) {
                // Check if there's an equivalent FK with different name
                $equivalent = $this->findEquivalentForeignKey($fk, $fromFks);
                if ($equivalent === null) {
                    $diff->addAddedForeignKey($fk);
                }
            }
        }

        // Find removed foreign keys
        foreach ($fromFks as $name => $fk) {
            if (!isset($toFks[$name])) {
                // Check if there's an equivalent FK with different name
                $equivalent = $this->findEquivalentForeignKey($fk, $toFks);
                if ($equivalent === null) {
                    $diff->addRemovedForeignKey($fk);
                }
            }
        }

        // Find changed foreign keys
        foreach ($fromFks as $name => $fromFk) {
            if (isset($toFks[$name])) {
                $fkDiff = $this->compareForeignKeys($fromFk, $toFks[$name]);
                if ($fkDiff !== null && $fkDiff->hasChanges()) {
                    $diff->addChangedForeignKey($fkDiff);
                }
            }
        }
    }

    /**
     * Compare two foreign keys and return what changed
     *
     * @param  ForeignKeyInfo  $fromFk  Original foreign key
     * @param  ForeignKeyInfo  $toFk    New foreign key
     * @return ForeignKeyDiff|null  Null if foreign keys are identical
     */
    public function compareForeignKeys(ForeignKeyInfo $fromFk, ForeignKeyInfo $toFk): ?ForeignKeyDiff
    {
        $diff = new ForeignKeyDiff($fromFk, $toFk);

        // Compare local columns
        if ($fromFk->getColumns() !== $toFk->getColumns()) {
            $diff->setColumnsChanged();
        }

        // Compare references (table and columns)
        if (
            $fromFk->getForeignTable() !== $toFk->getForeignTable()
            || $fromFk->getForeignColumns() !== $toFk->getForeignColumns()
        ) {
            $diff->setReferencesChanged();
        }

        // Compare ON DELETE action
        if (strtoupper($fromFk->getOnDelete()) !== strtoupper($toFk->getOnDelete())) {
            $diff->setOnDeleteChanged();
        }

        // Compare ON UPDATE action
        if (strtoupper($fromFk->getOnUpdate()) !== strtoupper($toFk->getOnUpdate())) {
            $diff->setOnUpdateChanged();
        }

        return $diff->hasChanges() ? $diff : null;
    }

    /**
     * Find a foreign key with equivalent definition (regardless of name)
     *
     * @param  ForeignKeyInfo  $needle
     * @param  array           $haystack  ForeignKeyInfo[] indexed by name
     * @return ForeignKeyInfo|null
     */
    protected function findEquivalentForeignKey(ForeignKeyInfo $needle, array $haystack): ?ForeignKeyInfo
    {
        foreach ($haystack as $fk) {
            if (
                $fk->getColumns() === $needle->getColumns()
                && $fk->getForeignTable() === $needle->getForeignTable()
                && $fk->getForeignColumns() === $needle->getForeignColumns()
            ) {
                return $fk;
            }
        }

        return null;
    }

    /**
     * Compare table-level properties
     *
     * @param  TableDiff  $diff
     * @param  TableInfo  $fromTable
     * @param  TableInfo  $toTable
     * @return void
     */
    protected function compareTableProperties(TableDiff $diff, TableInfo $fromTable, TableInfo $toTable): void
    {
        // Compare engine
        if ($fromTable->getEngine() !== null && $toTable->getEngine() !== null) {
            if (strtoupper($fromTable->getEngine()) !== strtoupper($toTable->getEngine())) {
                $diff->setEngineChanged();
            }
        }

        // Compare charset
        if ($fromTable->getCharset() !== $toTable->getCharset()) {
            $diff->setCharsetChanged();
        }

        // Compare collation
        if ($fromTable->getCollation() !== $toTable->getCollation()) {
            $diff->setCollationChanged();
        }
    }

    /**
     * Check if two column types are equivalent
     *
     * Handles variations like INT(11) vs INT, VARCHAR(255) vs VARCHAR(255), etc.
     *
     * @param  string  $typeA
     * @param  string  $typeB
     * @return bool
     */
    public function typesAreEquivalent(string $typeA, string $typeB): bool
    {
        $normalizedA = $this->normalizeType($typeA);
        $normalizedB = $this->normalizeType($typeB);

        return $normalizedA === $normalizedB;
    }

    /**
     * Normalize a column type for comparison
     *
     * @param  string  $type
     * @return string
     */
    public function normalizeType(string $type): string
    {
        $type = strtolower(trim($type));

        // Remove 'unsigned' suffix (tracked separately)
        $type = preg_replace('/\s+unsigned\b/i', '', $type);

        // Remove 'signed' suffix
        $type = preg_replace('/\s+signed\b/i', '', $type);

        // Extract base type and parameters
        if (preg_match('/^([a-z]+)(?:\(([^)]+)\))?(.*)$/', $type, $matches)) {
            $baseType = $matches[1];
            $params = $matches[2] ?? '';
            $suffix = trim($matches[3] ?? '');

            // Apply type aliases
            if (isset($this->typeAliases[$baseType])) {
                $baseType = $this->typeAliases[$baseType];
            }

            // For integer types, ignore display width (MySQL quirk)
            // INT(11) and INT are the same
            if (in_array($baseType, $this->integerTypes)) {
                $params = '';
            }

            // Rebuild type
            $type = $baseType;
            if ($params !== '') {
                $type .= '(' . $params . ')';
            }
            if ($suffix !== '') {
                $type .= ' ' . $suffix;
            }
        }

        return trim($type);
    }

    /**
     * Check if two default values are equivalent
     *
     * @param  mixed  $defaultA
     * @param  mixed  $defaultB
     * @return bool
     */
    protected function defaultsAreEquivalent($defaultA, $defaultB): bool
    {
        // Both null
        if ($defaultA === null && $defaultB === null) {
            return true;
        }

        // One null, one not
        if ($defaultA === null || $defaultB === null) {
            return false;
        }

        // Normalize string representations
        $a = $this->normalizeDefault($defaultA);
        $b = $this->normalizeDefault($defaultB);

        return $a === $b;
    }

    /**
     * Normalize a default value for comparison
     *
     * @param  mixed  $default
     * @return string
     */
    protected function normalizeDefault($default): string
    {
        if ($default === null) {
            return 'NULL';
        }

        $default = (string) $default;

        // Remove quotes
        $default = trim($default, "'\"");

        // Normalize boolean-like values
        $lower = strtolower($default);
        if ($lower === 'true' || $lower === '1') {
            return '1';
        }
        if ($lower === 'false' || $lower === '0') {
            return '0';
        }

        // Normalize CURRENT_TIMESTAMP variations
        if (preg_match('/^current_timestamp/i', $default)) {
            return 'CURRENT_TIMESTAMP';
        }

        // Normalize PostgreSQL nextval() sequences (auto-increment defaults)
        // e.g. "nextval('tablename_id_seq'::regclass)" → "AUTO_INCREMENT"
        if (preg_match('/^nextval\s*\(/i', $default)) {
            return 'AUTO_INCREMENT';
        }

        return $default;
    }

    /**
     * Index an array of objects by their getName() method
     *
     * @param  array  $items  Objects with getName() method
     * @return array  Indexed by name
     */
    protected function indexByName(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item->getName()] = $item;
        }
        return $indexed;
    }
}
