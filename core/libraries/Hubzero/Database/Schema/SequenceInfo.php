<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Represents metadata about a database sequence
 *
 * Sequences are database objects that generate unique numeric values.
 * They are supported natively by PostgreSQL, Oracle, and SQL Server (2012+).
 * MySQL does not support sequences (use AUTO_INCREMENT instead).
 *
 * Usage:
 * ```php
 * $sequences = $schema->getSequences();
 * foreach ($sequences as $sequence) {
 *     echo $sequence->getName() . ': current=' . $sequence->getCurrentValue();
 *     echo ', increment=' . $sequence->getIncrement();
 * }
 * ```
 */
class SequenceInfo
{
    /**
     * Sequence name
     *
     * @var string
     */
    protected $name;

    /**
     * Schema/namespace containing the sequence
     *
     * @var string|null
     */
    protected $schema;

    /**
     * Current value of the sequence
     *
     * @var int|null
     */
    protected $currentValue;

    /**
     * Starting value
     *
     * @var int
     */
    protected $startValue;

    /**
     * Minimum value
     *
     * @var int
     */
    protected $minValue;

    /**
     * Maximum value
     *
     * @var int|string
     */
    protected $maxValue;

    /**
     * Increment/step value
     *
     * @var int
     */
    protected $increment;

    /**
     * Whether the sequence cycles when reaching max/min
     *
     * @var bool
     */
    protected $cycle;

    /**
     * Cache size for pre-allocated values
     *
     * @var int
     */
    protected $cache;

    /**
     * Data type of the sequence
     *
     * @var string
     */
    protected $dataType;

    /**
     * Constructor
     *
     * @param array $data Associative array of sequence properties
     */
    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->schema = $data['schema'] ?? null;
        $this->currentValue = $data['current_value'] ?? null;
        $this->startValue = (int) ($data['start_value'] ?? 1);
        $this->minValue = (int) ($data['min_value'] ?? 1);
        $this->maxValue = $data['max_value'] ?? PHP_INT_MAX;
        $this->increment = (int) ($data['increment'] ?? 1);
        $this->cycle = (bool) ($data['cycle'] ?? false);
        $this->cache = (int) ($data['cache'] ?? 1);
        $this->dataType = $data['data_type'] ?? 'bigint';
    }

    /**
     * Get the sequence name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the fully qualified name (schema.name)
     *
     * @return string
     */
    public function getFullName(): string
    {
        if ($this->schema) {
            return $this->schema . '.' . $this->name;
        }
        return $this->name;
    }

    /**
     * Get the schema/namespace
     *
     * @return string|null
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * Get the current value
     *
     * Note: This may be null if the sequence hasn't been used yet
     * or if the database doesn't expose this information.
     *
     * @return int|null
     */
    public function getCurrentValue(): ?int
    {
        return $this->currentValue;
    }

    /**
     * Get the starting value
     *
     * @return int
     */
    public function getStartValue(): int
    {
        return $this->startValue;
    }

    /**
     * Get the minimum value
     *
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * Get the maximum value
     *
     * @return int|string  May be a string for very large values
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Get the increment/step value
     *
     * @return int
     */
    public function getIncrement(): int
    {
        return $this->increment;
    }

    /**
     * Check if the sequence cycles
     *
     * @return bool
     */
    public function isCycling(): bool
    {
        return $this->cycle;
    }

    /**
     * Get the cache size
     *
     * @return int
     */
    public function getCacheSize(): int
    {
        return $this->cache;
    }

    /**
     * Get the data type
     *
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
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
            'schema' => $this->schema,
            'current_value' => $this->currentValue,
            'start_value' => $this->startValue,
            'min_value' => $this->minValue,
            'max_value' => $this->maxValue,
            'increment' => $this->increment,
            'cycle' => $this->cycle,
            'cache' => $this->cache,
            'data_type' => $this->dataType,
        ];
    }
}
