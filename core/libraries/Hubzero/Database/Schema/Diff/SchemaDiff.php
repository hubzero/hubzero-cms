<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Diff;

use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;

/**
 * Represents the differences between two database schemas
 *
 * This value object captures all changes needed to transform one database
 * schema into another, including added tables, removed tables, and
 * modified tables (via TableDiff objects).
 *
 * Usage:
 * ```php
 * $diff = $comparator->compareSchemas($oldSchema, $newSchema);
 *
 * if (!$diff->isEmpty()) {
 *     // New tables to create
 *     foreach ($diff->getAddedTables() as $table) {
 *         echo "CREATE TABLE: {$table->getName()}\n";
 *     }
 *
 *     // Tables to drop
 *     foreach ($diff->getRemovedTables() as $table) {
 *         echo "DROP TABLE: {$table->getName()}\n";
 *     }
 *
 *     // Tables with changes
 *     foreach ($diff->getChangedTables() as $tableDiff) {
 *         echo "ALTER TABLE: {$tableDiff->getName()}\n";
 *         foreach ($tableDiff->getAddedColumns() as $col) {
 *             echo "  + {$col->getName()}\n";
 *         }
 *     }
 * }
 * ```
 */
class SchemaDiff
{
    /**
     * Original database schema
     *
     * @var DatabaseInfo
     */
    protected $fromSchema;

    /**
     * New database schema
     *
     * @var DatabaseInfo
     */
    protected $toSchema;

    /**
     * Tables that were added (exist in $to but not in $from)
     *
     * @var TableInfo[]
     */
    protected $addedTables = [];

    /**
     * Tables that were removed (exist in $from but not in $to)
     *
     * @var TableInfo[]
     */
    protected $removedTables = [];

    /**
     * Tables that were modified
     *
     * @var TableDiff[]
     */
    protected $changedTables = [];

    /**
     * Tables that were renamed (old name => new name)
     *
     * @var array
     */
    protected $renamedTables = [];

    /**
     * Create a new SchemaDiff instance
     *
     * @param DatabaseInfo $fromSchema Original schema
     * @param DatabaseInfo $toSchema   New schema
     */
    public function __construct(DatabaseInfo $fromSchema, DatabaseInfo $toSchema)
    {
        $this->fromSchema = $fromSchema;
        $this->toSchema = $toSchema;
    }

    /**
     * Get the original database schema
     *
     * @return DatabaseInfo
     */
    public function getFromSchema(): DatabaseInfo
    {
        return $this->fromSchema;
    }

    /**
     * Get the new database schema
     *
     * @return DatabaseInfo
     */
    public function getToSchema(): DatabaseInfo
    {
        return $this->toSchema;
    }

    // =========================================================================
    // Added Tables
    // =========================================================================

    /**
     * Add a table that was created
     *
     * @param  TableInfo  $table
     * @return $this
     */
    public function addAddedTable(TableInfo $table): self
    {
        $this->addedTables[$table->getName()] = $table;
        return $this;
    }

    /**
     * Get tables that were added
     *
     * @return TableInfo[]
     */
    public function getAddedTables(): array
    {
        return array_values($this->addedTables);
    }

    /**
     * Get added table names
     *
     * @return array
     */
    public function getAddedTableNames(): array
    {
        return array_keys($this->addedTables);
    }

    /**
     * Check if a specific table was added
     *
     * @param  string  $name
     * @return bool
     */
    public function hasAddedTable(string $name): bool
    {
        return isset($this->addedTables[$name]);
    }

    // =========================================================================
    // Removed Tables
    // =========================================================================

    /**
     * Add a table that was removed
     *
     * @param  TableInfo  $table
     * @return $this
     */
    public function addRemovedTable(TableInfo $table): self
    {
        $this->removedTables[$table->getName()] = $table;
        return $this;
    }

    /**
     * Get tables that were removed
     *
     * @return TableInfo[]
     */
    public function getRemovedTables(): array
    {
        return array_values($this->removedTables);
    }

    /**
     * Get removed table names
     *
     * @return array
     */
    public function getRemovedTableNames(): array
    {
        return array_keys($this->removedTables);
    }

    /**
     * Check if a specific table was removed
     *
     * @param  string  $name
     * @return bool
     */
    public function hasRemovedTable(string $name): bool
    {
        return isset($this->removedTables[$name]);
    }

    // =========================================================================
    // Changed Tables
    // =========================================================================

    /**
     * Add a table that was modified
     *
     * @param  TableDiff  $tableDiff
     * @return $this
     */
    public function addChangedTable(TableDiff $tableDiff): self
    {
        $this->changedTables[$tableDiff->getName()] = $tableDiff;
        return $this;
    }

    /**
     * Get tables that were modified
     *
     * @return TableDiff[]
     */
    public function getChangedTables(): array
    {
        return array_values($this->changedTables);
    }

    /**
     * Get changed table names
     *
     * @return array
     */
    public function getChangedTableNames(): array
    {
        return array_keys($this->changedTables);
    }

    /**
     * Get the diff for a specific table
     *
     * @param  string  $name
     * @return TableDiff|null
     */
    public function getTableDiff(string $name): ?TableDiff
    {
        return $this->changedTables[$name] ?? null;
    }

    /**
     * Check if a specific table was changed
     *
     * @param  string  $name
     * @return bool
     */
    public function hasChangedTable(string $name): bool
    {
        return isset($this->changedTables[$name]);
    }

    // =========================================================================
    // Renamed Tables
    // =========================================================================

    /**
     * Add a table rename
     *
     * @param  string  $oldName
     * @param  string  $newName
     * @return $this
     */
    public function addRenamedTable(string $oldName, string $newName): self
    {
        $this->renamedTables[$oldName] = $newName;
        return $this;
    }

    /**
     * Get renamed tables
     *
     * @return array  Old name => new name
     */
    public function getRenamedTables(): array
    {
        return $this->renamedTables;
    }

    /**
     * Check if any tables were renamed
     *
     * @return bool
     */
    public function hasRenamedTables(): bool
    {
        return !empty($this->renamedTables);
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
        return empty($this->addedTables)
            && empty($this->removedTables)
            && empty($this->changedTables)
            && empty($this->renamedTables);
    }

    /**
     * Get a summary of changes
     *
     * @return array
     */
    public function getSummary(): array
    {
        $totalColumnChanges = 0;
        $totalIndexChanges = 0;
        $totalFkChanges = 0;

        foreach ($this->changedTables as $tableDiff) {
            $summary = $tableDiff->getSummary();
            $totalColumnChanges += $summary['columns']['added']
                + $summary['columns']['removed']
                + $summary['columns']['changed'];
            $totalIndexChanges += $summary['indexes']['added']
                + $summary['indexes']['removed']
                + $summary['indexes']['changed'];
            $totalFkChanges += $summary['foreign_keys']['added']
                + $summary['foreign_keys']['removed']
                + $summary['foreign_keys']['changed'];
        }

        return [
            'tables' => [
                'added' => count($this->addedTables),
                'removed' => count($this->removedTables),
                'changed' => count($this->changedTables),
                'renamed' => count($this->renamedTables),
            ],
            'total_column_changes' => $totalColumnChanges,
            'total_index_changes' => $totalIndexChanges,
            'total_foreign_key_changes' => $totalFkChanges,
        ];
    }

    /**
     * Check if any changes are destructive (could lose data)
     *
     * @return bool
     */
    public function hasDestructiveChanges(): bool
    {
        // Removing tables is destructive
        if (!empty($this->removedTables)) {
            return true;
        }

        // Check each changed table for destructive changes
        foreach ($this->changedTables as $tableDiff) {
            if ($tableDiff->hasDestructiveChanges()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a list of all destructive changes with warnings
     *
     * @return array
     */
    public function getDestructiveWarnings(): array
    {
        $warnings = [];

        // Dropped tables
        foreach ($this->removedTables as $table) {
            $warnings[] = "DROP TABLE `{$table->getName()}` will permanently delete all data";
        }

        // Warnings from changed tables
        foreach ($this->changedTables as $tableDiff) {
            foreach ($tableDiff->getDestructiveWarnings() as $warning) {
                $warnings[] = "[{$tableDiff->getName()}] $warning";
            }
        }

        return $warnings;
    }

    /**
     * Get the total number of changes
     *
     * @return int
     */
    public function getChangeCount(): int
    {
        $count = count($this->addedTables)
            + count($this->removedTables)
            + count($this->renamedTables);

        foreach ($this->changedTables as $tableDiff) {
            $summary = $tableDiff->getSummary();
            $count += $summary['columns']['added']
                + $summary['columns']['removed']
                + $summary['columns']['changed']
                + $summary['indexes']['added']
                + $summary['indexes']['removed']
                + $summary['foreign_keys']['added']
                + $summary['foreign_keys']['removed'];
        }

        return $count;
    }

    // =========================================================================
    // Filtering
    // =========================================================================

    /**
     * Get tables that match a pattern
     *
     * @param  string  $pattern  Glob pattern (e.g., 'jos_users*')
     * @return self  New SchemaDiff with only matching tables
     */
    public function filterByPattern(string $pattern): self
    {
        // preg_quote escapes * to \* and ? to \?, so we replace the escaped versions
        $regex = '/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';

        $filtered = new self($this->fromSchema, $this->toSchema);

        foreach ($this->addedTables as $name => $table) {
            if (preg_match($regex, $name)) {
                $filtered->addAddedTable($table);
            }
        }

        foreach ($this->removedTables as $name => $table) {
            if (preg_match($regex, $name)) {
                $filtered->addRemovedTable($table);
            }
        }

        foreach ($this->changedTables as $name => $tableDiff) {
            if (preg_match($regex, $name)) {
                $filtered->addChangedTable($tableDiff);
            }
        }

        return $filtered;
    }

    /**
     * Exclude tables matching a pattern
     *
     * @param  string  $pattern  Glob pattern
     * @return self  New SchemaDiff without matching tables
     */
    public function excludeByPattern(string $pattern): self
    {
        // preg_quote escapes * to \* and ? to \?, so we replace the escaped versions
        $regex = '/^' . str_replace(['\*', '\?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';

        $filtered = new self($this->fromSchema, $this->toSchema);

        foreach ($this->addedTables as $name => $table) {
            if (!preg_match($regex, $name)) {
                $filtered->addAddedTable($table);
            }
        }

        foreach ($this->removedTables as $name => $table) {
            if (!preg_match($regex, $name)) {
                $filtered->addRemovedTable($table);
            }
        }

        foreach ($this->changedTables as $name => $tableDiff) {
            if (!preg_match($regex, $name)) {
                $filtered->addChangedTable($tableDiff);
            }
        }

        return $filtered;
    }

    // =========================================================================
    // Serialization
    // =========================================================================

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'from_database' => $this->fromSchema->getName(),
            'to_database' => $this->toSchema->getName(),
            'added_tables' => array_map(fn($t) => $t->toArray(), $this->addedTables),
            'removed_tables' => array_map(fn($t) => $t->toArray(), $this->removedTables),
            'changed_tables' => array_map(fn($t) => $t->toArray(), $this->changedTables),
            'renamed_tables' => $this->renamedTables,
            'summary' => $this->getSummary(),
            'destructive' => $this->hasDestructiveChanges(),
            'warnings' => $this->getDestructiveWarnings(),
        ];
    }
}
