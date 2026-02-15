<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;
use Hubzero\Database\Schema\Diff\ColumnDiff;
use Hubzero\Database\Schema\Diff\IndexDiff;
use Hubzero\Database\Schema\Diff\ForeignKeyDiff;

/**
 * Diff SQL Generator
 *
 * Converts TableDiff objects into executable SQL statements.
 * Handles driver-specific syntax and operation ordering.
 *
 * Safe Mode:
 * Use safe mode flags to prevent destructive operations from being generated.
 * This is useful when working with partial schema definitions or when you want
 * to protect against accidental data loss.
 *
 * Usage:
 * ```php
 * $generator = new DiffSqlGenerator($driver);
 *
 * // Generate SQL to apply changes
 * $upSql = $generator->generateUp($diff);
 *
 * // Generate SQL to reverse changes
 * $downSql = $generator->generateDown($diff);
 *
 * // Safe mode - no destructive operations
 * $safeSql = $generator->generateSafeUp($diff);
 *
 * // Or with granular control
 * $sql = $generator->generateSchemaUp($diff, DiffSqlGenerator::SAFE_NO_DROP_TABLES);
 *
 * // Execute the changes
 * foreach ($upSql as $statement) {
 *     $driver->setQuery($statement)->execute();
 * }
 * ```
 */
class DiffSqlGenerator
{
    /**
     * Safe mode flag: Prevent DROP TABLE statements
     */
    public const SAFE_NO_DROP_TABLES = 1;

    /**
     * Safe mode flag: Prevent DROP COLUMN statements
     */
    public const SAFE_NO_DROP_COLUMNS = 2;

    /**
     * Safe mode flag: Prevent DROP INDEX statements
     */
    public const SAFE_NO_DROP_INDEXES = 4;

    /**
     * Safe mode flag: Prevent DROP FOREIGN KEY statements
     */
    public const SAFE_NO_DROP_FOREIGN_KEYS = 8;

    /**
     * Safe mode flag: Prevent column type modifications that could lose data
     * (e.g., VARCHAR(255) -> VARCHAR(100), INT -> TINYINT)
     */
    public const SAFE_NO_SHRINKING_MODIFICATIONS = 16;

    /**
     * Safe mode flag: Combination of all safe flags (maximum protection)
     */
    public const SAFE_ALL = 31;

    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Create a new DiffSqlGenerator
     *
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Generate SQL to transform table from old state to new state
     *
     * @param  TableDiff  $diff
     * @param  int        $safeFlags  Bitmask of SAFE_* constants (0 = no restrictions)
     * @return array  Array of SQL statements
     */
    public function generateUp(TableDiff $diff, int $safeFlags = 0): array
    {
        return $this->generateOrderedSql($diff, false, $safeFlags);
    }

    /**
     * Generate SQL to transform table using safe mode (no destructive operations)
     *
     * Equivalent to generateUp($diff, self::SAFE_ALL)
     *
     * @param  TableDiff  $diff
     * @return array  Array of SQL statements
     */
    public function generateSafeUp(TableDiff $diff): array
    {
        return $this->generateUp($diff, self::SAFE_ALL);
    }

    /**
     * Generate SQL to reverse the changes (new state back to old)
     *
     * @param  TableDiff  $diff
     * @param  int        $safeFlags  Bitmask of SAFE_* constants (0 = no restrictions)
     * @return array  Array of SQL statements
     */
    public function generateDown(TableDiff $diff, int $safeFlags = 0): array
    {
        return $this->generateOrderedSql($diff, true, $safeFlags);
    }

    /**
     * Generate SQL to reverse changes using safe mode (no destructive operations)
     *
     * Equivalent to generateDown($diff, self::SAFE_ALL)
     *
     * @param  TableDiff  $diff
     * @return array  Array of SQL statements
     */
    public function generateSafeDown(TableDiff $diff): array
    {
        return $this->generateDown($diff, self::SAFE_ALL);
    }

    /**
     * Generate SQL for an entire schema diff (multiple tables)
     *
     * Operations are ordered for safety:
     * 1. Drop removed tables (respecting foreign key dependencies)
     * 2. Create new tables
     * 3. Alter existing tables
     *
     * @param  SchemaDiff  $diff
     * @param  int         $safeFlags  Bitmask of SAFE_* constants (0 = no restrictions)
     * @return array  Array of SQL statements
     */
    public function generateSchemaUp(SchemaDiff $diff, int $safeFlags = 0): array
    {
        return $this->generateSchemaSql($diff, false, $safeFlags);
    }

    /**
     * Generate SQL for schema diff using safe mode (no destructive operations)
     *
     * Equivalent to generateSchemaUp($diff, self::SAFE_ALL)
     *
     * @param  SchemaDiff  $diff
     * @return array  Array of SQL statements
     */
    public function generateSafeSchemaUp(SchemaDiff $diff): array
    {
        return $this->generateSchemaUp($diff, self::SAFE_ALL);
    }

    /**
     * Generate SQL to reverse a schema diff
     *
     * @param  SchemaDiff  $diff
     * @param  int         $safeFlags  Bitmask of SAFE_* constants (0 = no restrictions)
     * @return array  Array of SQL statements
     */
    public function generateSchemaDown(SchemaDiff $diff, int $safeFlags = 0): array
    {
        return $this->generateSchemaSql($diff, true, $safeFlags);
    }

    /**
     * Generate SQL to reverse schema diff using safe mode (no destructive operations)
     *
     * Equivalent to generateSchemaDown($diff, self::SAFE_ALL)
     *
     * @param  SchemaDiff  $diff
     * @return array  Array of SQL statements
     */
    public function generateSafeSchemaDown(SchemaDiff $diff): array
    {
        return $this->generateSchemaDown($diff, self::SAFE_ALL);
    }

    /**
     * Check if a safe flag is set
     *
     * @param  int  $safeFlags  Current safe flags bitmask
     * @param  int  $flag       Flag to check
     * @return bool
     */
    protected function hasSafeFlag(int $safeFlags, int $flag): bool
    {
        return ($safeFlags & $flag) === $flag;
    }

    /**
     * Generate SQL for schema-level changes
     *
     * @param  SchemaDiff  $diff
     * @param  bool        $reverse
     * @param  int         $safeFlags  Bitmask of SAFE_* constants
     * @return array
     */
    protected function generateSchemaSql(SchemaDiff $diff, bool $reverse = false, int $safeFlags = 0): array
    {
        $sql = [];
        $skipDropTables = $this->hasSafeFlag($safeFlags, self::SAFE_NO_DROP_TABLES);
        $skipDropForeignKeys = $this->hasSafeFlag($safeFlags, self::SAFE_NO_DROP_FOREIGN_KEYS);

        if ($reverse) {
            // Reverse: drop added tables, create removed tables, reverse alterations

            // 1. Drop tables that were added (unless safe mode)
            if (!$skipDropTables) {
                foreach ($diff->getAddedTables() as $table) {
                    $sql[] = $this->generateDropTable($table);
                }
            }

            // 2. Reverse alterations to changed tables
            foreach ($diff->getChangedTables() as $tableDiff) {
                $sql = array_merge($sql, $this->generateDown($tableDiff, $safeFlags));
            }

            // 3. Reverse table renames (rename back to original names)
            foreach ($diff->getRenamedTables() as $oldName => $newName) {
                $sql[] = $this->generateRenameTable($newName, $oldName);
            }

            // 4. Recreate tables that were removed
            foreach ($diff->getRemovedTables() as $table) {
                $sql[] = $this->generateCreateTable($table);
            }
        } else {
            // Forward: drop removed tables, create added tables, apply alterations

            // 1. Drop foreign keys from tables being removed (unless safe mode)
            // (to avoid FK constraint errors)
            if (!$skipDropTables && !$skipDropForeignKeys) {
                foreach ($diff->getRemovedTables() as $table) {
                    foreach ($table->getForeignKeys() as $fk) {
                        $sql[] = $this->generateDropForeignKey($table->getName(), $fk);
                    }
                }
            }

            // 2. Drop removed tables (unless safe mode)
            if (!$skipDropTables) {
                foreach ($diff->getRemovedTables() as $table) {
                    $sql[] = $this->generateDropTable($table);
                }
            }

            // 3. Rename tables
            foreach ($diff->getRenamedTables() as $oldName => $newName) {
                $sql[] = $this->generateRenameTable($oldName, $newName);
            }

            // 4. Create new tables
            foreach ($diff->getAddedTables() as $table) {
                $sql[] = $this->generateCreateTable($table);
            }

            // 5. Alter existing tables (including renamed ones)
            foreach ($diff->getChangedTables() as $tableDiff) {
                $sql = array_merge($sql, $this->generateUp($tableDiff, $safeFlags));
            }
        }

        return array_filter($sql);
    }

    /**
     * Generate SQL to rename a table
     *
     * @param  string  $oldName
     * @param  string  $newName
     * @return string
     */
    protected function generateRenameTable(string $oldName, string $newName): string
    {
        $oldTable = $this->driver->quoteName($oldName);
        $newTable = $this->driver->quoteName($newName);

        $driverName = $this->getDriverName();

        if ($driverName === 'sqlsrv') {
            // SQL Server uses sp_rename
            return "EXEC sp_rename '{$oldName}', '{$newName}'";
        }

        // MySQL, PostgreSQL, SQLite all support RENAME TABLE or ALTER TABLE RENAME
        return "ALTER TABLE {$oldTable} RENAME TO {$newTable}";
    }

    /**
     * Generate CREATE TABLE SQL from TableInfo
     *
     * @param  TableInfo  $table
     * @return string
     */
    protected function generateCreateTable(TableInfo $table): string
    {
        $tableName = $this->driver->quoteName($table->getName());
        $columns = [];
        $constraints = [];

        foreach ($table->getColumns() as $column) {
            $colName = $this->driver->quoteName($column->getName());
            $colDef = $this->buildColumnDefinition($column);
            $columns[] = "{$colName} {$colDef}";
        }

        // Primary key
        $pk = $table->getPrimaryKey();
        if ($pk) {
            $pkColumns = is_array($pk) ? $pk : [$pk];
            $pkCols = implode(', ', array_map([$this->driver, 'quoteName'], $pkColumns));
            $constraints[] = "PRIMARY KEY ({$pkCols})";
        }

        // Indexes (non-primary)
        foreach ($table->getIndexes() as $index) {
            if (!$index->isPrimary()) {
                $idxCols = implode(', ', array_map([$this->driver, 'quoteName'], $index->getColumns()));
                $unique = $index->isUnique() ? 'UNIQUE ' : '';
                $constraints[] = "{$unique}KEY {$this->driver->quoteName($index->getName())} ({$idxCols})";
            }
        }

        // Foreign keys
        foreach ($table->getForeignKeys() as $fk) {
            $fkCols = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getColumns()));
            $refTable = $this->driver->quoteName($fk->getForeignTable());
            $refCols = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getForeignColumns()));

            $constraint = "CONSTRAINT {$this->driver->quoteName($fk->getName())} ";
            $constraint .= "FOREIGN KEY ({$fkCols}) REFERENCES {$refTable} ({$refCols})";

            if ($fk->getOnDelete() && strtoupper($fk->getOnDelete()) !== 'NO ACTION') {
                $constraint .= " ON DELETE {$fk->getOnDelete()}";
            }
            if ($fk->getOnUpdate() && strtoupper($fk->getOnUpdate()) !== 'NO ACTION') {
                $constraint .= " ON UPDATE {$fk->getOnUpdate()}";
            }

            $constraints[] = $constraint;
        }

        $allDefs = array_merge($columns, $constraints);
        $sql = "CREATE TABLE {$tableName} (\n  " . implode(",\n  ", $allDefs) . "\n)";

        // Table options (MySQL)
        $engine = $table->getEngine();
        if ($engine && $this->driver->supportsEngine()) {
            $sql .= " ENGINE={$engine}";
        }

        $charset = $table->getCharset();
        if ($charset && $this->driver->supportsTableCharset()) {
            $sql .= " DEFAULT CHARSET={$charset}";
        }

        return $sql;
    }

    /**
     * Generate DROP TABLE SQL
     *
     * @param  TableInfo  $table
     * @return string
     */
    protected function generateDropTable(TableInfo $table): string
    {
        $tableName = $this->driver->quoteName($table->getName());
        return "DROP TABLE IF EXISTS {$tableName}";
    }

    /**
     * Generate SQL statements in the correct order
     *
     * Delegates to AlterTableBuilder which handles driver-specific SQL generation
     * and proper operation ordering for all database drivers.
     *
     * @param  TableDiff  $diff
     * @param  bool       $reverse    Generate reverse (down) migration
     * @param  int        $safeFlags  Bitmask of SAFE_* constants
     * @return array
     */
    protected function generateOrderedSql(TableDiff $diff, bool $reverse = false, int $safeFlags = 0): array
    {
        return $this->generateBuilderSql($diff, $reverse, $safeFlags);
    }

    // =========================================================================
    // AlterTableBuilder Delegation
    // =========================================================================

    /**
     * Generate SQL using AlterTableBuilder
     *
     * Delegates to AlterTableBuilder which handles driver-specific SQL generation
     * including SQLite table rebuild, MySQL combined ALTER, PostgreSQL separate
     * statements, and SQL Server syntax.
     *
     * @param  TableDiff  $diff
     * @param  bool       $reverse
     * @param  int        $safeFlags  Bitmask of SAFE_* constants
     * @return array
     */
    protected function generateBuilderSql(TableDiff $diff, bool $reverse = false, int $safeFlags = 0): array
    {
        $tableName = $diff->getFromTable()->getName();
        $builder = new AlterTableBuilder($this->driver, $tableName);

        // Provide the source table info for drivers that need it (e.g., SQLite table rebuild)
        $builder->setSourceTableInfo($diff->getFromTable());

        // Extract safe mode flags
        $skipDropColumns = $this->hasSafeFlag($safeFlags, self::SAFE_NO_DROP_COLUMNS);
        $skipDropIndexes = $this->hasSafeFlag($safeFlags, self::SAFE_NO_DROP_INDEXES);
        $skipDropForeignKeys = $this->hasSafeFlag($safeFlags, self::SAFE_NO_DROP_FOREIGN_KEYS);
        $skipShrinkingMods = $this->hasSafeFlag($safeFlags, self::SAFE_NO_SHRINKING_MODIFICATIONS);

        if ($reverse) {
            // Reverse: drop added, add removed, reverse modifications

            // Drop foreign keys that were added (before dropping columns they may reference)
            if (!$skipDropForeignKeys) {
                foreach ($diff->getAddedForeignKeys() as $fk) {
                    $builder->dropForeign($fk->getName());
                }

                // Reverse changed foreign keys (re-add the original)
                foreach ($diff->getChangedForeignKeys() as $fkDiff) {
                    $builder->dropForeign($fkDiff->getToForeignKey()->getName());
                }
            }

            // Drop indexes that were added
            if (!$skipDropIndexes) {
                foreach ($diff->getAddedIndexes() as $index) {
                    if (!$index->isPrimary()) {
                        $builder->dropIndex($index->getName());
                    }
                }

                // Reverse changed indexes
                foreach ($diff->getChangedIndexes() as $idxDiff) {
                    if (!$idxDiff->getToIndex()->isPrimary()) {
                        $builder->dropIndex($idxDiff->getToIndex()->getName());
                    }
                }
            }

            // Drop columns that were added
            if (!$skipDropColumns) {
                foreach ($diff->getAddedColumns() as $column) {
                    $builder->dropColumn($column->getName());
                }
            }

            // Re-add columns that were removed (with their original definitions)
            foreach ($diff->getRemovedColumns() as $column) {
                $this->addColumnToBuilder($builder, $column);
            }

            // Reverse column renames (rename back to original names)
            // Build lookup of columns by name from source table
            $fromColumns = [];
            foreach ($diff->getFromTable()->getColumns() as $col) {
                $fromColumns[$col->getName()] = $col;
            }

            foreach ($diff->getRenamedColumns() as $oldName => $newName) {
                // Reverse: rename from newName back to oldName
                $colType = 'VARCHAR(255)'; // Default fallback
                if (isset($fromColumns[$oldName])) {
                    $colType = $this->mapColumnTypeForBuilder($fromColumns[$oldName]);
                }
                $builder->renameColumn($newName, $oldName, $colType);
            }

            // Reverse column modifications (use fromColumn)
            foreach ($diff->getChangedColumns() as $colDiff) {
                // Skip if safe mode prevents shrinking modifications
                if ($skipShrinkingMods && $this->isColumnShrinking($colDiff, true)) {
                    continue;
                }

                // If this column was renamed and has additional changes, we need to
                // reverse those changes after the reverse rename
                if ($colDiff->isRenamed()) {
                    $hasOtherChanges = $colDiff->hasTypeChanged()
                        || $colDiff->hasNullableChanged()
                        || $colDiff->hasDefaultChanged()
                        || $colDiff->hasAutoIncrementChanged()
                        || $colDiff->hasUnsignedChanged()
                        || $colDiff->hasCommentChanged()
                        || $colDiff->hasCollationChanged();

                    if ($hasOtherChanges) {
                        $fromCol = $colDiff->getFromColumn();
                        $this->modifyColumnInBuilder($builder, $fromCol);
                    }
                } else {
                    $fromCol = $colDiff->getFromColumn();
                    $this->modifyColumnInBuilder($builder, $fromCol);
                }
            }

            // Re-add indexes that were removed
            if (!$skipDropIndexes) {
                foreach ($diff->getRemovedIndexes() as $index) {
                    if (!$index->isPrimary()) {
                        $this->addIndexToBuilder($builder, $index);
                    }
                }

                // Re-add original version of changed indexes
                foreach ($diff->getChangedIndexes() as $idxDiff) {
                    if (!$idxDiff->getFromIndex()->isPrimary()) {
                        $this->addIndexToBuilder($builder, $idxDiff->getFromIndex());
                    }
                }
            }

            // Re-add foreign keys that were removed
            if (!$skipDropForeignKeys) {
                foreach ($diff->getRemovedForeignKeys() as $fk) {
                    $this->addForeignKeyToBuilder($builder, $fk);
                }

                // Re-add original version of changed foreign keys
                foreach ($diff->getChangedForeignKeys() as $fkDiff) {
                    $this->addForeignKeyToBuilder($builder, $fkDiff->getFromForeignKey());
                }
            }

            // Reverse table-level changes
            if ($diff->hasEngineChanged()) {
                $originalEngine = $diff->getFromTable()->getEngine();
                if ($originalEngine) {
                    $builder->engine($originalEngine);
                }
            }

            if ($diff->hasCharsetChanged()) {
                $originalCharset = $diff->getFromTable()->getCharset();
                $originalCollation = $diff->getFromTable()->getCollation();
                if ($originalCharset) {
                    $builder->charset($originalCharset);
                }
                if ($originalCollation) {
                    $builder->collation($originalCollation);
                }
            }
        } else {
            // Forward: drop removed, add added, apply modifications

            // Drop foreign keys first (they may reference columns being dropped)
            if (!$skipDropForeignKeys) {
                foreach ($diff->getRemovedForeignKeys() as $fk) {
                    $builder->dropForeign($fk->getName());
                }

                // Drop changed foreign keys (will be re-added with new definition)
                foreach ($diff->getChangedForeignKeys() as $fkDiff) {
                    $builder->dropForeign($fkDiff->getFromForeignKey()->getName());
                }
            }

            // Drop indexes
            if (!$skipDropIndexes) {
                foreach ($diff->getRemovedIndexes() as $index) {
                    if (!$index->isPrimary()) {
                        $builder->dropIndex($index->getName());
                    }
                }

                // Drop changed indexes (will be re-added)
                foreach ($diff->getChangedIndexes() as $idxDiff) {
                    if (!$idxDiff->getFromIndex()->isPrimary()) {
                        $builder->dropIndex($idxDiff->getFromIndex()->getName());
                    }
                }
            }

            // Drop removed columns
            if (!$skipDropColumns) {
                foreach ($diff->getRemovedColumns() as $column) {
                    $builder->dropColumn($column->getName());
                }
            }

            // Rename columns (must be done before modify to have correct names)
            // Build lookup of columns by name from target table
            $toColumns = [];
            foreach ($diff->getToTable()->getColumns() as $col) {
                $toColumns[$col->getName()] = $col;
            }

            foreach ($diff->getRenamedColumns() as $oldName => $newName) {
                // Get the column type from the target table
                $colType = 'VARCHAR(255)'; // Default fallback
                if (isset($toColumns[$newName])) {
                    $colType = $this->mapColumnTypeForBuilder($toColumns[$newName]);
                }
                $builder->renameColumn($oldName, $newName, $colType);
            }

            // Modify changed columns (use toColumn)
            // For renamed columns with other changes, the rename is handled above
            // and additional changes (type, nullable, etc.) are applied here
            foreach ($diff->getChangedColumns() as $colDiff) {
                // Skip if safe mode prevents shrinking modifications
                if ($skipShrinkingMods && $this->isColumnShrinking($colDiff, false)) {
                    continue;
                }

                // If this column was renamed and has additional changes beyond the rename
                if ($colDiff->isRenamed()) {
                    $hasOtherChanges = $colDiff->hasTypeChanged()
                        || $colDiff->hasNullableChanged()
                        || $colDiff->hasDefaultChanged()
                        || $colDiff->hasAutoIncrementChanged()
                        || $colDiff->hasUnsignedChanged()
                        || $colDiff->hasCommentChanged()
                        || $colDiff->hasCollationChanged();

                    if ($hasOtherChanges) {
                        // Apply modifications to the new column name
                        $toCol = $colDiff->getToColumn();
                        $this->modifyColumnInBuilder($builder, $toCol);
                    }
                } else {
                    $toCol = $colDiff->getToColumn();
                    $this->modifyColumnInBuilder($builder, $toCol);
                }
            }

            // Add new columns
            foreach ($diff->getAddedColumns() as $column) {
                $this->addColumnToBuilder($builder, $column);
            }

            // Add new indexes
            foreach ($diff->getAddedIndexes() as $index) {
                if (!$index->isPrimary()) {
                    $this->addIndexToBuilder($builder, $index);
                }
            }

            // Re-add changed indexes with new definition
            if (!$skipDropIndexes) {
                foreach ($diff->getChangedIndexes() as $idxDiff) {
                    if (!$idxDiff->getToIndex()->isPrimary()) {
                        $this->addIndexToBuilder($builder, $idxDiff->getToIndex());
                    }
                }
            }

            // Add new foreign keys
            foreach ($diff->getAddedForeignKeys() as $fk) {
                $this->addForeignKeyToBuilder($builder, $fk);
            }

            // Re-add changed foreign keys with new definition
            if (!$skipDropForeignKeys) {
                foreach ($diff->getChangedForeignKeys() as $fkDiff) {
                    $this->addForeignKeyToBuilder($builder, $fkDiff->getToForeignKey());
                }
            }

            // Table-level changes
            if ($diff->hasEngineChanged()) {
                $newEngine = $diff->getToTable()->getEngine();
                if ($newEngine) {
                    $builder->engine($newEngine);
                }
            }

            if ($diff->hasCharsetChanged()) {
                $newCharset = $diff->getToTable()->getCharset();
                $newCollation = $diff->getToTable()->getCollation();
                if ($newCharset) {
                    $builder->charset($newCharset);
                }
                if ($newCollation) {
                    $builder->collation($newCollation);
                }
            }
        }

        // Generate and return SQL statements
        return $builder->toSql();
    }

    /**
     * Check if a column modification would shrink the data capacity
     *
     * Detects potentially data-losing changes like:
     * - VARCHAR(255) -> VARCHAR(100)
     * - INT -> TINYINT
     * - BIGINT -> INT
     *
     * @param  ColumnDiff  $colDiff
     * @param  bool        $reverse  Check in reverse direction
     * @return bool
     */
    protected function isColumnShrinking(ColumnDiff $colDiff, bool $reverse = false): bool
    {
        if (!$colDiff->hasTypeChanged()) {
            return false;
        }

        $fromCol = $reverse ? $colDiff->getToColumn() : $colDiff->getFromColumn();
        $toCol = $reverse ? $colDiff->getFromColumn() : $colDiff->getToColumn();

        $fromType = strtoupper($fromCol->getFullType());
        $toType = strtoupper($toCol->getFullType());

        // Extract base type and size
        $fromSize = $this->extractTypeSize($fromType);
        $toSize = $this->extractTypeSize($toType);

        // If both have explicit sizes, compare them
        if ($fromSize !== null && $toSize !== null && $toSize < $fromSize) {
            return true;
        }

        // Check integer type hierarchy: BIGINT > INT > MEDIUMINT > SMALLINT > TINYINT
        $intHierarchy = ['TINYINT' => 1, 'SMALLINT' => 2, 'MEDIUMINT' => 3, 'INT' => 4, 'INTEGER' => 4, 'BIGINT' => 5];
        $fromBase = $this->extractBaseType($fromType);
        $toBase = $this->extractBaseType($toType);

        if (isset($intHierarchy[$fromBase]) && isset($intHierarchy[$toBase])) {
            if ($intHierarchy[$toBase] < $intHierarchy[$fromBase]) {
                return true;
            }
        }

        // Check text type hierarchy: LONGTEXT > MEDIUMTEXT > TEXT > TINYTEXT
        $textHierarchy = ['TINYTEXT' => 1, 'TEXT' => 2, 'MEDIUMTEXT' => 3, 'LONGTEXT' => 4];
        if (isset($textHierarchy[$fromBase]) && isset($textHierarchy[$toBase])) {
            if ($textHierarchy[$toBase] < $textHierarchy[$fromBase]) {
                return true;
            }
        }

        // Check blob type hierarchy: LONGBLOB > MEDIUMBLOB > BLOB > TINYBLOB
        $blobHierarchy = ['TINYBLOB' => 1, 'BLOB' => 2, 'MEDIUMBLOB' => 3, 'LONGBLOB' => 4];
        if (isset($blobHierarchy[$fromBase]) && isset($blobHierarchy[$toBase])) {
            if ($blobHierarchy[$toBase] < $blobHierarchy[$fromBase]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract size from a type definition (e.g., VARCHAR(255) -> 255)
     *
     * @param  string  $type
     * @return int|null
     */
    protected function extractTypeSize(string $type): ?int
    {
        if (preg_match('/\((\d+)(?:,\d+)?\)/', $type, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Extract base type from a type definition (e.g., VARCHAR(255) -> VARCHAR)
     *
     * @param  string  $type
     * @return string
     */
    protected function extractBaseType(string $type): string
    {
        // Remove size specification and unsigned/signed
        $type = preg_replace('/\([^)]+\)/', '', $type);
        $type = preg_replace('/\s+(UNSIGNED|SIGNED)\b/i', '', $type);
        return trim($type);
    }

    /**
     * Add a column to AlterTableBuilder from ColumnInfo
     *
     * @param  AlterTableBuilder  $builder
     * @param  ColumnInfo         $column
     * @return void
     */
    protected function addColumnToBuilder(AlterTableBuilder $builder, ColumnInfo $column): void
    {
        $type = $this->mapColumnTypeForBuilder($column);

        $builder->addColumn($column->getName(), $type);

        // Apply modifiers
        if (!$column->isNullable()) {
            $builder->notNull();
        } else {
            $builder->nullable();
        }

        if ($column->hasDefault()) {
            $builder->default($column->getDefault());
        }

        if ($column->isAutoIncrement()) {
            $builder->autoIncrement();
        }

        if ($column->isUnsigned()) {
            $builder->unsigned();
        }
    }

    /**
     * Modify a column in AlterTableBuilder from ColumnInfo
     *
     * @param  AlterTableBuilder  $builder
     * @param  ColumnInfo         $column
     * @return void
     */
    protected function modifyColumnInBuilder(AlterTableBuilder $builder, ColumnInfo $column): void
    {
        $type = $this->mapColumnTypeForBuilder($column);

        $builder->modifyColumn($column->getName(), $type);

        // Apply modifiers
        if (!$column->isNullable()) {
            $builder->notNull();
        } else {
            $builder->nullable();
        }

        if ($column->hasDefault()) {
            $builder->default($column->getDefault());
        }

        if ($column->isAutoIncrement()) {
            $builder->autoIncrement();
        }

        if ($column->isUnsigned()) {
            $builder->unsigned();
        }
    }

    /**
     * Add an index to AlterTableBuilder from IndexInfo
     *
     * @param  AlterTableBuilder  $builder
     * @param  IndexInfo          $index
     * @return void
     */
    protected function addIndexToBuilder(AlterTableBuilder $builder, IndexInfo $index): void
    {
        $columns = $index->getColumns();

        if ($index->isUnique()) {
            $builder->addUniqueIndex($index->getName(), $columns);
        } elseif ($index->isFulltext()) {
            $builder->addFulltextIndex($index->getName(), $columns);
        } else {
            $builder->addIndex($index->getName(), $columns);
        }
    }

    /**
     * Add a foreign key to AlterTableBuilder from ForeignKeyInfo
     *
     * @param  AlterTableBuilder  $builder
     * @param  ForeignKeyInfo     $fk
     * @return void
     */
    protected function addForeignKeyToBuilder(AlterTableBuilder $builder, ForeignKeyInfo $fk): void
    {
        $columns = $fk->getColumns();
        $foreignColumns = $fk->getForeignColumns();

        // AlterTableBuilder.addForeign() supports single column FKs
        // For multi-column FKs, use the first column (most FKs are single-column)
        $column = is_array($columns) ? ($columns[0] ?? '') : $columns;
        $foreignColumn = is_array($foreignColumns) ? ($foreignColumns[0] ?? 'id') : $foreignColumns;

        $onDelete = $fk->getOnDelete() ?: 'CASCADE';
        $onUpdate = $fk->getOnUpdate() ?: 'CASCADE';

        // Normalize NO ACTION to CASCADE (more common default)
        if (strtoupper($onDelete) === 'NO ACTION') {
            $onDelete = 'CASCADE';
        }
        if (strtoupper($onUpdate) === 'NO ACTION') {
            $onUpdate = 'CASCADE';
        }

        $builder->addForeign(
            $column,
            $fk->getForeignTable(),
            $foreignColumn,
            $onDelete,
            $onUpdate
        );
    }

    /**
     * Map ColumnInfo type to a format suitable for AlterTableBuilder
     *
     * @param  ColumnInfo  $column
     * @return string
     */
    protected function mapColumnTypeForBuilder(ColumnInfo $column): string
    {
        $type = $column->getFullType();

        // Remove UNSIGNED from type (handled separately as modifier)
        $type = preg_replace('/\s+unsigned\b/i', '', $type);

        return trim($type);
    }

    // =========================================================================
    // Column Operations
    // =========================================================================

    /**
     * Generate SQL to add a column
     *
     * @param  string      $table
     * @param  ColumnInfo  $column
     * @return string|null
     */
    protected function generateAddColumn(string $table, ColumnInfo $column): ?string
    {
        $table = $this->driver->quoteName($table);
        $columnName = $this->driver->quoteName($column->getName());
        $definition = $this->buildColumnDefinition($column);

        return "ALTER TABLE {$table} ADD COLUMN {$columnName} {$definition}";
    }

    /**
     * Generate SQL to drop a column
     *
     * @param  string      $table
     * @param  ColumnInfo  $column
     * @return string|null
     */
    protected function generateDropColumn(string $table, ColumnInfo $column): ?string
    {
        if (!$this->driver->supportsDropColumn()) {
            // SQLite doesn't support DROP COLUMN in older versions
            // Would need table rebuild - return null and handle separately
            return null;
        }

        $table = $this->driver->quoteName($table);
        $columnName = $this->driver->quoteName($column->getName());

        return "ALTER TABLE {$table} DROP COLUMN {$columnName}";
    }

    /**
     * Generate SQL to modify a column
     *
     * @param  string      $table
     * @param  ColumnDiff  $colDiff
     * @param  bool        $reverse  Use fromColumn instead of toColumn
     * @return string|null
     */
    protected function generateModifyColumn(string $table, ColumnDiff $colDiff, bool $reverse = false): ?string
    {
        $column = $reverse ? $colDiff->getFromColumn() : $colDiff->getToColumn();
        $table = $this->driver->quoteName($table);
        $columnName = $this->driver->quoteName($column->getName());
        $definition = $this->buildColumnDefinition($column);

        // MySQL/MariaDB use MODIFY COLUMN
        // PostgreSQL uses ALTER COLUMN with separate statements
        // SQLite requires table rebuild

        $driverName = $this->getDriverName();

        if ($driverName === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN
            return null;
        }

        if ($driverName === 'pgsql') {
            // PostgreSQL needs separate ALTER COLUMN statements
            return $this->generatePostgresModifyColumn($table, $column, $colDiff);
        }

        // MySQL/MariaDB
        return "ALTER TABLE {$table} MODIFY COLUMN {$columnName} {$definition}";
    }

    /**
     * Generate PostgreSQL-specific column modification
     *
     * @param  string      $table
     * @param  ColumnInfo  $column
     * @param  ColumnDiff  $colDiff
     * @return string
     */
    protected function generatePostgresModifyColumn(string $table, ColumnInfo $column, ColumnDiff $colDiff): string
    {
        $statements = [];
        $columnName = $this->driver->quoteName($column->getName());

        if ($colDiff->hasTypeChanged()) {
            $type = $column->getFullType();
            $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$columnName} TYPE {$type}";
        }

        if ($colDiff->hasNullableChanged()) {
            if ($column->isNullable()) {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$columnName} DROP NOT NULL";
            } else {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$columnName} SET NOT NULL";
            }
        }

        if ($colDiff->hasDefaultChanged()) {
            $default = $column->getDefault();
            if ($default === null) {
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$columnName} DROP DEFAULT";
            } else {
                $defaultValue = $this->quoteDefault($default);
                $statements[] = "ALTER TABLE {$table} ALTER COLUMN {$columnName} SET DEFAULT {$defaultValue}";
            }
        }

        return implode('; ', $statements);
    }

    /**
     * Generate SQL to add an index
     *
     * @param  string     $table
     * @param  IndexInfo  $index
     * @return string|null
     */
    protected function generateAddIndex(string $table, IndexInfo $index): ?string
    {
        $tableName = $this->driver->quoteName($table);
        $indexName = $this->driver->quoteName($index->getName());
        $columns = implode(', ', array_map([$this->driver, 'quoteName'], $index->getColumns()));

        if ($index->isFulltext()) {
            if (!$this->driver->supportsFulltext()) {
                return null;
            }
            return "CREATE FULLTEXT INDEX {$indexName} ON {$tableName} ({$columns})";
        }

        $unique = $index->isUnique() ? 'UNIQUE ' : '';

        return "CREATE {$unique}INDEX {$indexName} ON {$tableName} ({$columns})";
    }

    /**
     * Generate SQL to drop an index
     *
     * @param  string     $table
     * @param  IndexInfo  $index
     * @return string|null
     */
    protected function generateDropIndex(string $table, IndexInfo $index): ?string
    {
        $indexName = $this->driver->quoteName($index->getName());

        $driverName = $this->getDriverName();

        if ($driverName === 'mysql' || $driverName === 'mariadb') {
            $tableName = $this->driver->quoteName($table);
            return "DROP INDEX {$indexName} ON {$tableName}";
        }

        // PostgreSQL, SQLite
        return "DROP INDEX {$indexName}";
    }

    /**
     * Generate SQL to add a foreign key
     *
     * @param  string          $table
     * @param  ForeignKeyInfo  $fk
     * @return string|null
     */
    protected function generateAddForeignKey(string $table, ForeignKeyInfo $fk): ?string
    {
        $tableName = $this->driver->quoteName($table);
        $constraintName = $this->driver->quoteName($fk->getName());
        $columns = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getColumns()));
        $refTable = $this->driver->quoteName($fk->getForeignTable());
        $refColumns = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getForeignColumns()));

        $sql = "ALTER TABLE {$tableName} ADD CONSTRAINT {$constraintName} ";
        $sql .= "FOREIGN KEY ({$columns}) REFERENCES {$refTable} ({$refColumns})";

        $onDelete = $fk->getOnDelete();
        if ($onDelete && strtoupper($onDelete) !== 'NO ACTION') {
            $sql .= " ON DELETE {$onDelete}";
        }

        $onUpdate = $fk->getOnUpdate();
        if ($onUpdate && strtoupper($onUpdate) !== 'NO ACTION') {
            $sql .= " ON UPDATE {$onUpdate}";
        }

        return $sql;
    }

    /**
     * Generate SQL to drop a foreign key
     *
     * @param  string          $table
     * @param  ForeignKeyInfo  $fk
     * @return string|null
     */
    protected function generateDropForeignKey(string $table, ForeignKeyInfo $fk): ?string
    {
        $tableName = $this->driver->quoteName($table);
        $constraintName = $this->driver->quoteName($fk->getName());

        $driverName = $this->getDriverName();

        if ($driverName === 'mysql' || $driverName === 'mariadb') {
            return "ALTER TABLE {$tableName} DROP FOREIGN KEY {$constraintName}";
        }

        // PostgreSQL, SQLite
        return "ALTER TABLE {$tableName} DROP CONSTRAINT {$constraintName}";
    }

    /**
     * Generate SQL to change storage engine
     *
     * @param  string       $table
     * @param  string|null  $engine
     * @return string|null
     */
    protected function generateChangeEngine(string $table, ?string $engine): ?string
    {
        if (!$this->driver->supportsEngine() || $engine === null) {
            return null;
        }

        $tableName = $this->driver->quoteName($table);
        return "ALTER TABLE {$tableName} ENGINE = {$engine}";
    }

    /**
     * Generate SQL to change character set
     *
     * @param  string       $table
     * @param  string|null  $charset
     * @param  string|null  $collation
     * @return string|null
     */
    protected function generateChangeCharset(string $table, ?string $charset, ?string $collation): ?string
    {
        if (!$this->driver->supportsTableCharset() || $charset === null) {
            return null;
        }

        $tableName = $this->driver->quoteName($table);
        $sql = "ALTER TABLE {$tableName} CONVERT TO CHARACTER SET {$charset}";

        if ($collation) {
            $sql .= " COLLATE {$collation}";
        }

        return $sql;
    }

    /**
     * Build a column definition string from ColumnInfo
     *
     * @param  ColumnInfo  $column
     * @return string
     */
    protected function buildColumnDefinition(ColumnInfo $column): string
    {
        $parts = [];

        // Type
        $type = $column->getFullType();
        if ($column->isUnsigned() && stripos($type, 'unsigned') === false) {
            $type .= ' UNSIGNED';
        }
        $parts[] = $type;

        // NULL/NOT NULL
        if ($column->isNullable()) {
            $parts[] = 'NULL';
        } else {
            $parts[] = 'NOT NULL';
        }

        // Default value
        if ($column->hasDefault()) {
            $default = $column->getDefault();
            $parts[] = 'DEFAULT ' . $this->quoteDefault($default);
        }

        // Auto-increment
        if ($column->isAutoIncrement()) {
            $driverName = $this->getDriverName();
            if ($driverName === 'pgsql') {
                // PostgreSQL uses SERIAL types, handled differently
            } else {
                $parts[] = 'AUTO_INCREMENT';
            }
        }

        // Comment
        $comment = $column->getComment();
        if ($comment) {
            $parts[] = "COMMENT " . $this->driver->quote($comment);
        }

        return implode(' ', $parts);
    }

    /**
     * Quote a default value appropriately
     *
     * @param  mixed  $value
     * @return string
     */
    protected function quoteDefault($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        // Check for SQL expressions (CURRENT_TIMESTAMP, etc.)
        $upper = strtoupper((string) $value);
        if (in_array($upper, ['CURRENT_TIMESTAMP', 'CURRENT_DATE', 'CURRENT_TIME', 'NULL'])) {
            return $upper;
        }

        // Check for function calls
        if (preg_match('/^[A-Z_]+\s*\(/i', $value)) {
            return $value;
        }

        // Numeric values
        if (is_numeric($value)) {
            return (string) $value;
        }

        // String values
        return $this->driver->quote($value);
    }

    /**
     * Get the driver name
     *
     * Handles both real driver classes and PHPUnit mock classes.
     *
     * @return string  One of: mysql, mariadb, pgsql, sqlite, sqlsrv, percona
     */
    protected function getDriverName(): string
    {
        if (method_exists($this->driver, 'getDriverType')) {
            $name = strtolower((string) $this->driver->getDriverType());
            if ($name !== '' && $name !== 'unknown') {
                return $name;
            }
        }

        $class = get_class($this->driver);

        // Known driver class names (case-insensitive matching)
        $knownDrivers = ['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv', 'percona'];

        // Check if any known driver name appears in the class name
        $classLower = strtolower($class);
        foreach ($knownDrivers as $driver) {
            if (strpos($classLower, $driver) !== false) {
                return $driver;
            }
        }

        // Fallback: extract from namespace (for non-mock classes)
        $parts = explode('\\', $class);
        return strtolower(end($parts));
    }
}
