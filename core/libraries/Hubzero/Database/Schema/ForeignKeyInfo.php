<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents metadata about a foreign key constraint
 *
 * This class provides a structured, typed interface for foreign key information.
 * It normalizes data across different database engines.
 *
 * Usage:
 * ```php
 * $table = $schema->introspectTable('#__posts');
 * foreach ($table->getForeignKeys() as $fk) {
 *     echo $fk->getName() . ': ';
 *     echo implode(', ', $fk->getColumns()) . ' -> ';
 *     echo $fk->getForeignTable() . '.' . implode(', ', $fk->getForeignColumns());
 * }
 * ```
 */
class ForeignKeyInfo
{
    /**
     * Constraint name
     *
     * @var string
     */
    protected $name;

    /**
     * Local column names
     *
     * @var array
     */
    protected $columns;

    /**
     * Referenced table name
     *
     * @var string
     */
    protected $foreignTable;

    /**
     * Referenced column names
     *
     * @var array
     */
    protected $foreignColumns;

    /**
     * ON UPDATE action
     *
     * @var string
     */
    protected $onUpdate;

    /**
     * ON DELETE action
     *
     * @var string
     */
    protected $onDelete;

    /**
     * Create a new ForeignKeyInfo instance
     *
     * @param array|object $data Foreign key data from database introspection
     */
    public function __construct($data = [])
    {
        // Handle both array and object (stdClass) input
        if (is_object($data)) {
            $data = (array) $data;
        }

        $this->name = $data['name'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->foreignTable = $data['foreign_table'] ?? '';
        $this->foreignColumns = $data['foreign_columns'] ?? [];
        $this->onUpdate = $data['on_update'] ?? 'NO ACTION';
        $this->onDelete = $data['on_delete'] ?? 'NO ACTION';
    }

    /**
     * Get the constraint name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the local column names
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the referenced table name
     *
     * @return string
     */
    public function getForeignTable(): string
    {
        return $this->foreignTable;
    }

    /**
     * Get the referenced column names
     *
     * @return array
     */
    public function getForeignColumns(): array
    {
        return $this->foreignColumns;
    }

    /**
     * Get the ON UPDATE action
     *
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }

    /**
     * Get the ON DELETE action
     *
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * Check if ON DELETE CASCADE is set
     *
     * @return bool
     */
    public function cascadesOnDelete(): bool
    {
        return strtoupper($this->onDelete) === 'CASCADE';
    }

    /**
     * Check if ON UPDATE CASCADE is set
     *
     * @return bool
     */
    public function cascadesOnUpdate(): bool
    {
        return strtoupper($this->onUpdate) === 'CASCADE';
    }

    /**
     * Check if ON DELETE SET NULL is set
     *
     * @return bool
     */
    public function setsNullOnDelete(): bool
    {
        return strtoupper($this->onDelete) === 'SET NULL';
    }

    /**
     * Check if foreign key references a specific table
     *
     * @param  string  $table
     * @return bool
     */
    public function references(string $table): bool
    {
        return $this->foreignTable === $table;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'foreign_table' => $this->foreignTable,
            'foreign_columns' => $this->foreignColumns,
            'on_update' => $this->onUpdate,
            'on_delete' => $this->onDelete,
        ];
    }
}
