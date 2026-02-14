<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Fluent Column Builder for add/modify column operations with positioning
 *
 * Provides a fluent interface for column operations that allows specifying
 * column type using either raw SQL definitions or abstract types.
 *
 * Usage with raw SQL (backward compatible):
 * ```php
 * $schema->addColumn('#__users', 'email', 'VARCHAR(255)')
 *     ->after('name');
 * ```
 *
 * Usage with abstract types (new fluent API):
 * ```php
 * $schema->addColumn('#__users', 'email')
 *     ->string(255)
 *     ->nullable()
 *     ->after('name');
 *
 * $schema->addColumn('#__users', 'score')
 *     ->integer()
 *     ->unsigned()
 *     ->default(0);
 *
 * $schema->modifyColumn('#__users', 'score')
 *     ->bigInteger()
 *     ->default(100);
 * ```
 */
class ColumnBuilder
{
    use ColumnDefinitionTrait;

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
     * Column name
     *
     * @var string
     */
    protected $column;

    /**
     * New column name (for renames)
     *
     * @var string|null
     */
    protected $newColumn;

    /**
     * Raw SQL column definition (when not using abstract types)
     *
     * @var string|null
     */
    protected $definition;

    /**
     * Optional column comment (from constructor)
     *
     * @var string
     */
    protected $initialComment;

    /**
     * Operation type: 'add', 'modify', or 'rename'
     *
     * @var string
     */
    protected $operation;

    /**
     * Whether the operation has been executed
     *
     * @var bool
     */
    protected $executed = false;

    /**
     * Column position (after which column)
     *
     * @var string|null
     */
    protected $afterColumn = null;

    /**
     * Column position (before which column)
     *
     * @var string|null
     */
    protected $beforeColumn = null;

    /**
     * Whether to position at the beginning
     *
     * @var bool
     */
    protected $positionFirst = false;

    /**
     * Create a new column builder
     *
     * @param  Driver       $driver      Database driver
     * @param  string       $table       Table name
     * @param  string       $column      Column name
     * @param  string|null  $definition  Column definition (null for fluent abstract type API)
     * @param  string       $operation   Operation type: 'add' or 'modify'
     * @param  string       $comment     Optional column comment
     */
    public function __construct(
        Driver $driver,
        string $table,
        string $column,
        ?string $definition,
        string $operation,
        string $comment = '',
        ?string $newColumn = null
    ) {
        $this->driver = $driver;
        $this->table = $table;
        $this->column = $column;
        $this->definition = $definition;
        $this->operation = $operation;
        $this->initialComment = $comment;
        $this->newColumn = $newColumn;
    }

    /**
     * Position the column after a specific column
     *
     * @param   string  $afterColumn  Column to position after
     * @return  static
     */
    public function after(string $afterColumn): static
    {
        $this->afterColumn = $afterColumn;
        return $this;
    }

    /**
     * Position the column before a specific column
     *
     * @param   string  $beforeColumn  Column to position before
     * @return  static
     */
    public function before(string $beforeColumn): static
    {
        $this->beforeColumn = $beforeColumn;
        return $this;
    }

    /**
     * Position the column at the beginning of the table
     *
     * @return  static
     */
    public function first(): static
    {
        $this->positionFirst = true;
        return $this;
    }

    /**
     * Set the new column name (for rename operations)
     *
     * @param   string  $newColumn  The new name for the column
     * @return  static
     */
    public function to(string $newColumn): static
    {
        $this->newColumn = $newColumn;
        return $this;
    }

    /**
     * Position the column at the end of the table (default behavior)
     *
     * @return  bool
     */
    public function last(): bool
    {
        return $this->execute();
    }

    /**
     * Execute the column operation
     *
     * @return  bool
     */
    public function execute(): bool
    {
        if ($this->executed) {
            return true;
        }

        $this->executed = true;

        // Resolve the column definition (raw SQL or compiled abstract type)
        $definition = $this->resolveDefinition();
        $comment = $this->resolveComment();

        // Determine the appropriate driver method based on operation and position
        if ($this->positionFirst) {
            if ($this->operation === 'add') {
                return $this->driver->addColumnFirst($this->table, $this->column, $definition, $comment);
            }
            return $this->driver->modifyColumnFirst($this->table, $this->column, $definition, $comment);
        }

        if ($this->afterColumn !== null) {
            if ($this->operation === 'add') {
                return $this->driver->addColumnAfter(
                    $this->table,
                    $this->column,
                    $definition,
                    $this->afterColumn,
                    $comment
                );
            }
            return $this->driver->modifyColumnAfter(
                $this->table,
                $this->column,
                $definition,
                $this->afterColumn,
                $comment
            );
        }

        if ($this->beforeColumn !== null) {
            if ($this->operation === 'add') {
                return $this->driver->addColumnBefore(
                    $this->table,
                    $this->column,
                    $definition,
                    $this->beforeColumn,
                    $comment
                );
            }
            return $this->driver->modifyColumnBefore(
                $this->table,
                $this->column,
                $definition,
                $this->beforeColumn,
                $comment
            );
        }

        // Default: add/modify at end
        if ($this->operation === 'add') {
            return $this->driver->addColumn($this->table, $this->column, $definition, $comment);
        } elseif ($this->operation === 'modify') {
            return $this->driver->modifyColumn($this->table, $this->column, $definition, $comment);
        }

        // Rename operation
        if ($this->newColumn === null) {
            throw new \RuntimeException("New column name required for rename operation. Use ->to('new_name').");
        }

        return $this->driver->changeColumn($this->table, $this->column, $this->newColumn, $definition, $comment);
    }

    /**
     * Resolve the column definition (raw SQL or compiled abstract type)
     *
     * @return string
     * @throws \RuntimeException If no definition is available
     */
    protected function resolveDefinition(): string
    {
        // If we have an abstract type set, compile it
        if ($this->hasAbstractType()) {
            return $this->compileAbstractTypeToSql($this->driver);
        }

        // Otherwise, use the raw definition from constructor
        if ($this->definition !== null) {
            return $this->definition;
        }

        throw new \RuntimeException(
            "Column definition required. Either provide a raw SQL definition or use type methods " .
            "(e.g., ->integer(), ->string(255), ->boolean())."
        );
    }

    /**
     * Resolve the comment (from fluent API or constructor)
     *
     * @return string
     */
    protected function resolveComment(): string
    {
        // Prefer fluent API comment over constructor comment
        if (isset($this->columnModifiers['comment'])) {
            return $this->columnModifiers['comment'];
        }
        return $this->initialComment;
    }

    /**
     * Check if the operation has been executed
     *
     * @return bool
     */
    public function isExecuted(): bool
    {
        return $this->executed;
    }

    /**
     * Auto-execute on destruction if not already executed
     *
     * This allows usage like:
     *   $schema->addColumn('#__users', 'email', 'VARCHAR(255)');
     * Without requiring explicit ->execute() or positioning call.
     *
     * For fluent API usage, this also triggers execution:
     *   $schema->addColumn('#__users', 'score')->integer()->default(0);
     */
    public function __destruct()
    {
        if (!$this->executed) {
            // Only auto-execute if we have something to execute
            if ($this->definition !== null || $this->hasAbstractType()) {
                $this->execute();
            }
        }
    }
}
