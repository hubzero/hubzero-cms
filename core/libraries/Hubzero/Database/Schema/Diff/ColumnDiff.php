<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Diff;

use Hubzero\Database\Schema\ColumnInfo;

/**
 * Represents the differences between two column definitions
 *
 * This value object captures what changed about a column between
 * two schema states, enabling targeted ALTER statements.
 *
 * Usage:
 * ```php
 * $colDiff = $comparator->compareColumns($oldCol, $newCol);
 * if ($colDiff !== null) {
 *     echo "Column {$colDiff->getName()} changed:";
 *     if ($colDiff->hasTypeChanged()) {
 *         echo " type";
 *     }
 *     if ($colDiff->hasNullableChanged()) {
 *         echo " nullable";
 *     }
 * }
 * ```
 */
class ColumnDiff
{
    /**
     * Original column definition
     *
     * @var ColumnInfo
     */
    protected $fromColumn;

    /**
     * New column definition
     *
     * @var ColumnInfo
     */
    protected $toColumn;

    /**
     * Whether the column type changed
     *
     * @var bool
     */
    protected $typeChanged = false;

    /**
     * Whether the nullable constraint changed
     *
     * @var bool
     */
    protected $nullableChanged = false;

    /**
     * Whether the default value changed
     *
     * @var bool
     */
    protected $defaultChanged = false;

    /**
     * Whether the auto-increment setting changed
     *
     * @var bool
     */
    protected $autoIncrementChanged = false;

    /**
     * Whether the unsigned setting changed
     *
     * @var bool
     */
    protected $unsignedChanged = false;

    /**
     * Whether the column comment changed
     *
     * @var bool
     */
    protected $commentChanged = false;

    /**
     * Whether the collation changed
     *
     * @var bool
     */
    protected $collationChanged = false;

    /**
     * Whether the column was renamed
     *
     * @var bool
     */
    protected $renamed = false;

    /**
     * Old column name (if renamed)
     *
     * @var string|null
     */
    protected $oldName = null;

    /**
     * New column name (if renamed)
     *
     * @var string|null
     */
    protected $newName = null;

    /**
     * Create a new ColumnDiff instance
     *
     * @param ColumnInfo $fromColumn Original column
     * @param ColumnInfo $toColumn   New column
     */
    public function __construct(ColumnInfo $fromColumn, ColumnInfo $toColumn)
    {
        $this->fromColumn = $fromColumn;
        $this->toColumn = $toColumn;
    }

    /**
     * Get the column name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->fromColumn->getName();
    }

    /**
     * Get the original column definition
     *
     * @return ColumnInfo
     */
    public function getFromColumn(): ColumnInfo
    {
        return $this->fromColumn;
    }

    /**
     * Get the new column definition
     *
     * @return ColumnInfo
     */
    public function getToColumn(): ColumnInfo
    {
        return $this->toColumn;
    }

    /**
     * Mark that the type changed
     *
     * @return $this
     */
    public function setTypeChanged(): self
    {
        $this->typeChanged = true;
        return $this;
    }

    /**
     * Check if the type changed
     *
     * @return bool
     */
    public function hasTypeChanged(): bool
    {
        return $this->typeChanged;
    }

    /**
     * Mark that the nullable constraint changed
     *
     * @return $this
     */
    public function setNullableChanged(): self
    {
        $this->nullableChanged = true;
        return $this;
    }

    /**
     * Check if the nullable constraint changed
     *
     * @return bool
     */
    public function hasNullableChanged(): bool
    {
        return $this->nullableChanged;
    }

    /**
     * Mark that the default value changed
     *
     * @return $this
     */
    public function setDefaultChanged(): self
    {
        $this->defaultChanged = true;
        return $this;
    }

    /**
     * Check if the default value changed
     *
     * @return bool
     */
    public function hasDefaultChanged(): bool
    {
        return $this->defaultChanged;
    }

    /**
     * Mark that the auto-increment setting changed
     *
     * @return $this
     */
    public function setAutoIncrementChanged(): self
    {
        $this->autoIncrementChanged = true;
        return $this;
    }

    /**
     * Check if the auto-increment setting changed
     *
     * @return bool
     */
    public function hasAutoIncrementChanged(): bool
    {
        return $this->autoIncrementChanged;
    }

    /**
     * Mark that the unsigned setting changed
     *
     * @return $this
     */
    public function setUnsignedChanged(): self
    {
        $this->unsignedChanged = true;
        return $this;
    }

    /**
     * Check if the unsigned setting changed
     *
     * @return bool
     */
    public function hasUnsignedChanged(): bool
    {
        return $this->unsignedChanged;
    }

    /**
     * Mark that the comment changed
     *
     * @return $this
     */
    public function setCommentChanged(): self
    {
        $this->commentChanged = true;
        return $this;
    }

    /**
     * Check if the comment changed
     *
     * @return bool
     */
    public function hasCommentChanged(): bool
    {
        return $this->commentChanged;
    }

    /**
     * Mark that the collation changed
     *
     * @return $this
     */
    public function setCollationChanged(): self
    {
        $this->collationChanged = true;
        return $this;
    }

    /**
     * Check if the collation changed
     *
     * @return bool
     */
    public function hasCollationChanged(): bool
    {
        return $this->collationChanged;
    }

    /**
     * Mark that the column was renamed
     *
     * @param  string  $oldName  Original column name
     * @param  string  $newName  New column name
     * @return $this
     */
    public function setRenamed(string $oldName, string $newName): self
    {
        $this->renamed = true;
        $this->oldName = $oldName;
        $this->newName = $newName;
        return $this;
    }

    /**
     * Check if the column was renamed
     *
     * @return bool
     */
    public function isRenamed(): bool
    {
        return $this->renamed;
    }

    /**
     * Get the old column name (before rename)
     *
     * @return string|null
     */
    public function getOldName(): ?string
    {
        return $this->oldName;
    }

    /**
     * Get the new column name (after rename)
     *
     * @return string|null
     */
    public function getNewName(): ?string
    {
        return $this->newName;
    }

    /**
     * Check if anything changed
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return $this->typeChanged
            || $this->nullableChanged
            || $this->defaultChanged
            || $this->autoIncrementChanged
            || $this->unsignedChanged
            || $this->commentChanged
            || $this->collationChanged
            || $this->renamed;
    }

    /**
     * Get a list of what changed
     *
     * @return array
     */
    public function getChanges(): array
    {
        $changes = [];

        if ($this->renamed) {
            $changes[] = 'renamed';
        }
        if ($this->typeChanged) {
            $changes[] = 'type';
        }
        if ($this->nullableChanged) {
            $changes[] = 'nullable';
        }
        if ($this->defaultChanged) {
            $changes[] = 'default';
        }
        if ($this->autoIncrementChanged) {
            $changes[] = 'auto_increment';
        }
        if ($this->unsignedChanged) {
            $changes[] = 'unsigned';
        }
        if ($this->commentChanged) {
            $changes[] = 'comment';
        }
        if ($this->collationChanged) {
            $changes[] = 'collation';
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
        $result = [
            'name' => $this->getName(),
            'from' => $this->fromColumn->toArray(),
            'to' => $this->toColumn->toArray(),
            'changes' => $this->getChanges(),
        ];

        if ($this->renamed) {
            $result['old_name'] = $this->oldName;
            $result['new_name'] = $this->newName;
        }

        return $result;
    }
}
