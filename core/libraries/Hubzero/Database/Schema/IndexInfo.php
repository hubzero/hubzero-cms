<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents metadata about a database index
 *
 * This class provides a structured, typed interface for index information
 * instead of raw arrays. It normalizes data across different database engines.
 *
 * Usage:
 * ```php
 * $table = $schema->introspectTable('#__users');
 * foreach ($table->getIndexes() as $index) {
 *     echo $index->getName() . ': ' . implode(', ', $index->getColumns());
 *     if ($index->isUnique()) {
 *         echo ' (unique)';
 *     }
 * }
 * ```
 */
class IndexInfo
{
    /**
     * Index name
     *
     * @var string
     */
    protected $name;

    /**
     * Column names in the index
     *
     * @var array
     */
    protected $columns;

    /**
     * Whether this is the primary key
     *
     * @var bool
     */
    protected $primary;

    /**
     * Whether this is a unique index
     *
     * @var bool
     */
    protected $unique;

    /**
     * Index type (BTREE, HASH, FULLTEXT, SPATIAL)
     *
     * @var string
     */
    protected $type;

    /**
     * Index comment
     *
     * @var string|null
     */
    protected $comment;

    /**
     * Create a new IndexInfo instance
     *
     * @param array $data Index data from database introspection
     */
    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->primary = $data['primary'] ?? false;
        $this->unique = $data['unique'] ?? $this->primary;
        $this->type = $data['type'] ?? 'BTREE';
        $this->comment = $data['comment'] ?? null;
    }

    /**
     * Get the index name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column names in this index
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Check if this is the primary key
     *
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * Check if this is a unique index
     *
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Get the index type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Check if this is a fulltext index
     *
     * @return bool
     */
    public function isFulltext(): bool
    {
        return strtoupper($this->type) === 'FULLTEXT';
    }

    /**
     * Check if this is a spatial index
     *
     * @return bool
     */
    public function isSpatial(): bool
    {
        return strtoupper($this->type) === 'SPATIAL';
    }

    /**
     * Get the index comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Check if index contains a specific column
     *
     * @param  string  $column
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        return in_array($column, $this->columns);
    }

    /**
     * Get the number of columns in this index
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        return count($this->columns);
    }

    /**
     * Check if this is a composite (multi-column) index
     *
     * @return bool
     */
    public function isComposite(): bool
    {
        return count($this->columns) > 1;
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
            'primary' => $this->primary,
            'unique' => $this->unique,
            'type' => $this->type,
            'comment' => $this->comment,
        ];
    }
}
