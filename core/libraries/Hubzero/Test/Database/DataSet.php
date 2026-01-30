<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Database;

/**
 * Represents a collection of database tables for test fixtures
 *
 * Lightweight replacement for PHPUnit\DbUnit\DataSet\IDataSet
 */
class DataSet
{
    /**
     * Tables in this dataset
     *
     * @var array<string, Table>
     */
    protected $tables = [];

    /**
     * Add a table to the dataset
     *
     * @param  Table  $table
     * @return void
     */
    public function addTable(Table $table): void
    {
        $this->tables[$table->getTableName()] = $table;
    }

    /**
     * Get a table by name
     *
     * @param  string  $tableName
     * @return Table|null
     */
    public function getTable(string $tableName): ?Table
    {
        return $this->tables[$tableName] ?? null;
    }

    /**
     * Get all table names
     *
     * @return array
     */
    public function getTableNames(): array
    {
        return array_keys($this->tables);
    }

    /**
     * Get all tables
     *
     * @return array<string, Table>
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * Check if a table exists in the dataset
     *
     * @param  string  $tableName
     * @return bool
     */
    public function hasTable(string $tableName): bool
    {
        return isset($this->tables[$tableName]);
    }

    /**
     * Get the number of tables
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->tables);
    }
}
