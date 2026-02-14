<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Diff;

use Hubzero\Database\Schema\IndexInfo;

/**
 * Represents the differences between two index definitions
 *
 * This value object captures what changed about an index between
 * two schema states.
 *
 * Usage:
 * ```php
 * $idxDiff = $comparator->compareIndexes($oldIdx, $newIdx);
 * if ($idxDiff !== null) {
 *     echo "Index {$idxDiff->getName()} changed";
 * }
 * ```
 */
class IndexDiff
{
    /**
     * Original index definition
     *
     * @var IndexInfo
     */
    protected $fromIndex;

    /**
     * New index definition
     *
     * @var IndexInfo
     */
    protected $toIndex;

    /**
     * Whether the columns in the index changed
     *
     * @var bool
     */
    protected $columnsChanged = false;

    /**
     * Whether the unique constraint changed
     *
     * @var bool
     */
    protected $uniqueChanged = false;

    /**
     * Whether the index type changed (BTREE, HASH, FULLTEXT, etc.)
     *
     * @var bool
     */
    protected $typeChanged = false;

    /**
     * Create a new IndexDiff instance
     *
     * @param IndexInfo $fromIndex Original index
     * @param IndexInfo $toIndex   New index
     */
    public function __construct(IndexInfo $fromIndex, IndexInfo $toIndex)
    {
        $this->fromIndex = $fromIndex;
        $this->toIndex = $toIndex;
    }

    /**
     * Get the index name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->fromIndex->getName();
    }

    /**
     * Get the original index definition
     *
     * @return IndexInfo
     */
    public function getFromIndex(): IndexInfo
    {
        return $this->fromIndex;
    }

    /**
     * Get the new index definition
     *
     * @return IndexInfo
     */
    public function getToIndex(): IndexInfo
    {
        return $this->toIndex;
    }

    /**
     * Mark that the columns changed
     *
     * @return $this
     */
    public function setColumnsChanged(): self
    {
        $this->columnsChanged = true;
        return $this;
    }

    /**
     * Check if the columns changed
     *
     * @return bool
     */
    public function hasColumnsChanged(): bool
    {
        return $this->columnsChanged;
    }

    /**
     * Mark that the unique constraint changed
     *
     * @return $this
     */
    public function setUniqueChanged(): self
    {
        $this->uniqueChanged = true;
        return $this;
    }

    /**
     * Check if the unique constraint changed
     *
     * @return bool
     */
    public function hasUniqueChanged(): bool
    {
        return $this->uniqueChanged;
    }

    /**
     * Mark that the index type changed
     *
     * @return $this
     */
    public function setTypeChanged(): self
    {
        $this->typeChanged = true;
        return $this;
    }

    /**
     * Check if the index type changed
     *
     * @return bool
     */
    public function hasTypeChanged(): bool
    {
        return $this->typeChanged;
    }

    /**
     * Check if anything changed
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return $this->columnsChanged
            || $this->uniqueChanged
            || $this->typeChanged;
    }

    /**
     * Get a list of what changed
     *
     * @return array
     */
    public function getChanges(): array
    {
        $changes = [];

        if ($this->columnsChanged) {
            $changes[] = 'columns';
        }
        if ($this->uniqueChanged) {
            $changes[] = 'unique';
        }
        if ($this->typeChanged) {
            $changes[] = 'type';
        }

        return $changes;
    }

    /**
     * Convert to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'from' => $this->fromIndex->toArray(),
            'to' => $this->toIndex->toArray(),
            'changes' => $this->getChanges(),
        ];
    }
}
