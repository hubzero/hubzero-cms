<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Table Gateway/Factory
 *
 * Provides a fluent entry point for table operations, acting as a factory
 * for TableBuilder (CREATE) and AlterTableBuilder (ALTER) while also
 * providing direct access to introspection methods.
 *
 * Usage:
 * ```php
 * $table = $db->schema()->table('#__users');
 *
 * // Create a new table
 * $table->create()
 *     ->id()
 *     ->string('name', 255)
 *     ->timestamps()
 *     ->execute();
 *
 * // Alter an existing table
 * $table->alter()
 *     ->addColumn('phone', 'VARCHAR(20)')
 *     ->dropColumn('fax')
 *     ->execute();
 *
 * // Introspection
 * if ($table->exists()) {
 *     $columns = $table->getColumns();
 * }
 *
 * // Drop the table
 * $table->drop();
 * ```
 *
 */
class Table
{
    /**
     * Database driver instance
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new Table instance
     *
     * @param  Driver  $driver  Database driver
     * @param  string  $table   Table name (can include prefix placeholder #__)
     */
    public function __construct(Driver $driver, string $table)
    {
        $this->driver = $driver;
        $this->table = $table;
    }

    // =========================================================================
    // Builder Factories
    // =========================================================================

    /**
     * Get a TableBuilder for CREATE TABLE operations
     *
     * @return TableBuilder
     */
    public function create(): TableBuilder
    {
        return new TableBuilder($this->driver, $this->table);
    }

    /**
     * Get an AlterTableBuilder for ALTER TABLE operations
     *
     * @return AlterTableBuilder
     */
    public function alter(): AlterTableBuilder
    {
        return new AlterTableBuilder($this->driver, $this->table);
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    /**
     * Drop this table
     *
     * @param  bool  $ifExists  Only drop if table exists (default: true)
     * @return bool
     */
    public function drop(bool $ifExists = true): bool
    {
        $this->driver->dropTable($this->table, $ifExists);
        return true;
    }

    /**
     * Truncate this table (delete all rows and reset auto-increment)
     *
     * @param  int  $nextId  The next auto-increment value (default 1)
     * @return bool
     */
    public function truncate(int $nextId = 1): bool
    {
        return $this->driver->truncateTable($this->table, $nextId);
    }

    /**
     * Rename this table
     *
     * @param  string  $newName  The new table name
     * @return bool
     */
    public function rename(string $newName): bool
    {
        $this->driver->renameTable($this->table, $newName);
        $this->table = $newName;
        return true;
    }

    // =========================================================================
    // Introspection
    // =========================================================================

    /**
     * Check if this table exists
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->driver->tableExists($this->table);
    }

    /**
     * Check if this table has a specific column
     *
     * @param  string  $column  Column name
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        return $this->driver->tableHasField($this->table, $column);
    }

    /**
     * Check if this table has a specific index/key
     *
     * @param  string  $key  Index/key name
     * @return bool
     */
    public function hasKey(string $key): bool
    {
        return $this->driver->tableHasKey($this->table, $key);
    }

    /**
     * Check if this table has a primary key
     *
     * @return bool
     */
    public function hasPrimaryKey(): bool
    {
        return (bool) $this->driver->getPrimaryKey($this->table);
    }

    /**
     * Get column information for this table
     *
     * @param  bool  $typeOnly  True (default) to only return field types
     * @return array
     */
    public function getColumns(bool $typeOnly = true): array
    {
        return $this->driver->getTableColumns($this->table, $typeOnly);
    }

    /**
     * Get the column names for this table
     *
     * @return array
     */
    public function getColumnNames(): array
    {
        return array_keys($this->driver->getTableColumns($this->table));
    }

    /**
     * Get key/index information for this table
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->driver->getTableKeys($this->table);
    }

    /**
     * Get index/key names for this table
     *
     * @return array
     */
    public function getIndexNames(): array
    {
        $keys = $this->driver->getTableKeys($this->table);
        $names = [];

        foreach ($keys as $keyName => $key) {
            if (is_string($keyName) && $keyName !== '') {
                $names[] = $keyName;
                continue;
            }

            if (is_object($key)) {
                $candidate = $key->Key_name ?? $key->name ?? null;
                if ($candidate) {
                    $names[] = $candidate;
                }
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Get the primary key column name for this table
     *
     * @return string|false
     */
    public function getPrimaryKey()
    {
        return $this->driver->getPrimaryKey($this->table);
    }

    /**
     * Get primary key column names for this table
     *
     * @return array
     */
    public function getPrimaryKeyColumns(): array
    {
        return $this->driver->getPrimaryKeyColumns($this->table);
    }

    /**
     * Check if primary key includes a specific column
     *
     * @param  string  $column  Column name
     * @return bool
     */
    public function hasPrimaryKeyColumn(string $column): bool
    {
        return in_array($column, $this->getPrimaryKeyColumns(), true);
    }

    /**
     * Get foreign key constraints for this table
     *
     * Returns an array of foreign key constraint objects with:
     * - name: Constraint name
     * - columns: Array of local column names
     * - foreign_table: Referenced table name
     * - foreign_columns: Array of referenced column names
     * - on_update: Update action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * - on_delete: Delete action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     *
     * @return array
     */
    public function getForeignKeys(): array
    {
        return $this->driver->getForeignKeys($this->table);
    }

    /**
     * Check if this table has a specific foreign key constraint
     *
     * @param  string  $name  The foreign key constraint name
     * @return bool
     */
    public function hasForeignKey(string $name): bool
    {
        return $this->driver->hasForeignKey($this->table, $name);
    }

    /**
     * Get the CREATE TABLE statement for this table
     *
     * @return string
     */
    public function getCreateStatement(): string
    {
        $result = $this->driver->getTableCreate($this->table);
        return $result[$this->table] ?? '';
    }

    /**
     * Get the storage engine for this table (MySQL only)
     *
     * @return string|bool
     */
    public function getEngine()
    {
        return $this->driver->getEngine($this->table);
    }

    /**
     * Get the character set for this table or a specific column
     *
     * @param  string|null  $column  Optional column name
     * @return string|bool
     */
    public function getCharacterSet(?string $column = null)
    {
        return $this->driver->getCharacterSet($this->table, $column);
    }

    /**
     * Get the auto-increment value for this table
     *
     * @return int|bool
     */
    public function getAutoIncrement()
    {
        return $this->driver->getAutoIncrement($this->table);
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->table;
    }

    /**
     * Get the database driver
     *
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }
}
