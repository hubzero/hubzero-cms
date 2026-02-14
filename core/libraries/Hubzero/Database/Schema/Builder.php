<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;
use Hubzero\Database\Drivers\Base\BaseSchemaGrammar;
use Closure;

/**
 * Database Schema Builder
 *
 * Provides a database-agnostic way to create and modify database tables.
 * Uses TableDefinition to define table structure and Grammar to generate DDL.
 *
 * Example usage:
 * ```php
 * $schema = new Builder($db);
 *
 * // Create a table
 * $schema->create('users', function($table) {
 *     $table->id();
 *     $table->string('name');
 *     $table->string('email')->unique();
 *     $table->timestamp('created_at')->nullable();
 * });
 *
 * // Modify a table
 * $schema->table('users', function($table) {
 *     $table->string('phone', 20)->nullable();
 * });
 *
 * // Drop a table
 * $schema->drop('users');
 * ```
 */
class Builder
{
    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * DDL grammar for the current database
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * Create a new schema builder
     *
     * @param  Driver  $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->grammar = $this->createGrammar();
    }

    /**
     * Create the appropriate grammar for the database driver
     *
     * The driver determines which grammar implementation to use based on
     * its database type. This delegates grammar selection to the driver
     * using polymorphism instead of conditional type checking.
     *
     * @return BaseSchemaGrammar
     */
    protected function createGrammar(): BaseSchemaGrammar
    {
        return $this->driver->getSchemaGrammar();
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

    /**
     * Get the grammar instance
     *
     * @return BaseSchemaGrammar
     */
    public function getGrammar(): BaseSchemaGrammar
    {
        return $this->grammar;
    }

    /**
     * Create a new table
     *
     * @param  string   $table     Table name
     * @param  Closure  $callback  Callback that receives TableDefinition
     * @return void
     */
    public function create(string $table, Closure $callback): void
    {
        $blueprint = new TableDefinition($table);
        $callback($blueprint);

        // compileCreate() now returns an array of statements including
        // the CREATE TABLE and any necessary CREATE INDEX statements
        $this->execute($this->grammar->compileCreate($blueprint));
    }

    /**
     * Modify an existing table
     *
     * @param  string   $table     Table name
     * @param  Closure  $callback  Callback that receives TableDefinition
     * @return void
     */
    public function table(string $table, Closure $callback): void
    {
        $blueprint = new TableDefinition($table, true);
        $callback($blueprint);

        // Add new columns
        foreach ($this->grammar->compileAlterAdd($blueprint) as $statement) {
            $this->execute($statement);
        }

        // Modify existing columns
        foreach ($this->grammar->compileAlterModify($blueprint) as $statement) {
            $this->execute($statement);
        }

        // Drop columns
        foreach ($this->grammar->compileAlterDrop($blueprint) as $statement) {
            $this->execute($statement);
        }

        // Create indexes
        foreach ($this->grammar->compileIndexes($blueprint) as $statement) {
            $this->execute($statement);
        }
    }

    /**
     * Drop a table
     *
     * @param  string  $table  Table name
     * @return void
     */
    public function drop(string $table): void
    {
        $this->execute($this->grammar->compileDrop($table, false));
    }

    /**
     * Drop a table if it exists
     *
     * @param  string  $table  Table name
     * @return void
     */
    public function dropIfExists(string $table): void
    {
        $this->execute($this->grammar->compileDrop($table, true));
    }

    /**
     * Check if a table exists
     *
     * @param  string  $table  Table name
     * @return bool
     */
    public function hasTable(string $table): bool
    {
        return $this->driver->tableExists($table);
    }

    /**
     * Check if a column exists on a table
     *
     * @param  string  $table   Table name
     * @param  string  $column  Column name
     * @return bool
     */
    public function hasColumn(string $table, string $column): bool
    {
        return $this->driver->tableHasField($table, $column);
    }

    /**
     * Get the column listing for a table
     *
     * @param  string  $table  Table name
     * @return array
     */
    public function getColumnListing(string $table): array
    {
        return array_keys($this->driver->getTableColumns($table));
    }

    /**
     * Rename a table
     *
     * @param  string  $from  Current table name
     * @param  string  $to    New table name
     * @return void
     */
    public function rename(string $from, string $to): void
    {
        $this->driver->renameTable($from, $to);
    }

    /**
     * Execute a SQL statement or array of statements
     *
     * @param  string|array  $sql  Single SQL statement or array of statements
     * @return void
     */
    protected function execute($sql): void
    {
        $statements = is_array($sql) ? $sql : [$sql];

        foreach ($statements as $statement) {
            $this->driver->setQuery($statement);
            $this->driver->execute();
        }
    }

    /**
     * Get the SQL for a create operation without executing it
     *
     * Useful for debugging or logging
     *
     * @param  string   $table     Table name
     * @param  Closure  $callback  Callback that receives TableDefinition
     * @return array    Array of SQL statements
     */
    public function getCreateSql(string $table, Closure $callback): array
    {
        $blueprint = new TableDefinition($table);
        $callback($blueprint);

        // compileCreate() now returns an array of statements including
        // the CREATE TABLE and any necessary CREATE INDEX statements
        return $this->grammar->compileCreate($blueprint);
    }

    /**
     * Get the SQL for a table modification without executing it
     *
     * @param  string   $table     Table name
     * @param  Closure  $callback  Callback that receives TableDefinition
     * @return array    Array of SQL statements
     */
    public function getAlterSql(string $table, Closure $callback): array
    {
        $blueprint = new TableDefinition($table, true);
        $callback($blueprint);

        $statements = [];

        foreach ($this->grammar->compileAlterAdd($blueprint) as $statement) {
            $statements[] = $statement;
        }

        foreach ($this->grammar->compileAlterModify($blueprint) as $statement) {
            $statements[] = $statement;
        }

        foreach ($this->grammar->compileAlterDrop($blueprint) as $statement) {
            $statements[] = $statement;
        }

        foreach ($this->grammar->compileIndexes($blueprint) as $statement) {
            $statements[] = $statement;
        }

        return $statements;
    }
}
