<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Column definition for schema builder
 *
 * Represents a database column with its type and constraints.
 * Provides a fluent interface for defining column properties.
 */
class Column
{
    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * Column type (abstract type like 'integer', 'string', etc.)
     *
     * @var string
     */
    protected $type;

    /**
     * Type parameters (e.g., length for string, precision for decimal)
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Whether column allows NULL values
     *
     * @var bool
     */
    protected $nullable = false;

    /**
     * Default value for the column
     *
     * @var mixed
     */
    protected $default = null;

    /**
     * Whether a default has been explicitly set
     *
     * @var bool
     */
    protected $hasDefault = false;

    /**
     * Whether column is auto-incrementing
     *
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * Whether column is a primary key
     *
     * @var bool
     */
    protected $primaryKey = false;

    /**
     * Whether column has a unique constraint
     *
     * @var bool
     */
    protected $unique = false;

    /**
     * Whether column is unsigned (MySQL)
     *
     * @var bool
     */
    protected $unsigned = false;

    /**
     * Column comment
     *
     * @var string|null
     */
    protected $comment = null;

    /**
     * Position constraint (AFTER column_name or FIRST)
     *
     * @var string|null
     */
    protected $after = null;

    /**
     * Create a new column definition
     *
     * @param  string  $name  Column name
     * @param  string  $type  Abstract column type
     * @param  array   $parameters  Type parameters
     */
    public function __construct(string $name, string $type, array $parameters = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->parameters = $parameters;
    }

    /**
     * Get the column name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the abstract column type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get type parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Mark column as nullable
     *
     * @return $this
     */
    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Check if column is nullable
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set default value
     *
     * @param  mixed  $value
     * @return $this
     */
    public function default($value): self
    {
        $this->default = $value;
        $this->hasDefault = true;
        return $this;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Check if column has a default value
     *
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * Mark column as auto-incrementing
     *
     * @return $this
     */
    public function autoIncrement(): self
    {
        $this->autoIncrement = true;
        return $this;
    }

    /**
     * Check if column is auto-incrementing
     *
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * Mark column as primary key
     *
     * @return $this
     */
    public function primary(): self
    {
        $this->primaryKey = true;
        return $this;
    }

    /**
     * Check if column is primary key
     *
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

    /**
     * Mark column as unique
     *
     * @return $this
     */
    public function unique(): self
    {
        $this->unique = true;
        return $this;
    }

    /**
     * Check if column has unique constraint
     *
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Mark column as unsigned
     *
     * @return $this
     */
    public function unsigned(): self
    {
        $this->unsigned = true;
        return $this;
    }

    /**
     * Check if column is unsigned
     *
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * Set column comment
     *
     * @param  string  $comment
     * @return $this
     */
    public function comment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get column comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set column position (for ALTER TABLE)
     *
     * @param  string  $columnName
     * @return $this
     */
    public function after(string $columnName): self
    {
        $this->after = $columnName;
        return $this;
    }

    /**
     * Get after column name
     *
     * @return string|null
     */
    public function getAfter(): ?string
    {
        return $this->after;
    }
}
