<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema\Diff;

use Hubzero\Database\Schema\ForeignKeyInfo;

/**
 * Represents the differences between two foreign key definitions
 *
 * This value object captures what changed about a foreign key between
 * two schema states.
 *
 * Usage:
 * ```php
 * $fkDiff = $comparator->compareForeignKeys($oldFk, $newFk);
 * if ($fkDiff !== null) {
 *     echo "Foreign key {$fkDiff->getName()} changed";
 * }
 * ```
 */
class ForeignKeyDiff
{
    /**
     * Original foreign key definition
     *
     * @var ForeignKeyInfo
     */
    protected $fromForeignKey;

    /**
     * New foreign key definition
     *
     * @var ForeignKeyInfo
     */
    protected $toForeignKey;

    /**
     * Whether the local columns changed
     *
     * @var bool
     */
    protected $columnsChanged = false;

    /**
     * Whether the referenced table/columns changed
     *
     * @var bool
     */
    protected $referencesChanged = false;

    /**
     * Whether the ON DELETE action changed
     *
     * @var bool
     */
    protected $onDeleteChanged = false;

    /**
     * Whether the ON UPDATE action changed
     *
     * @var bool
     */
    protected $onUpdateChanged = false;

    /**
     * Create a new ForeignKeyDiff instance
     *
     * @param ForeignKeyInfo $fromForeignKey Original foreign key
     * @param ForeignKeyInfo $toForeignKey   New foreign key
     */
    public function __construct(ForeignKeyInfo $fromForeignKey, ForeignKeyInfo $toForeignKey)
    {
        $this->fromForeignKey = $fromForeignKey;
        $this->toForeignKey = $toForeignKey;
    }

    /**
     * Get the foreign key name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->fromForeignKey->getName();
    }

    /**
     * Get the original foreign key definition
     *
     * @return ForeignKeyInfo
     */
    public function getFromForeignKey(): ForeignKeyInfo
    {
        return $this->fromForeignKey;
    }

    /**
     * Get the new foreign key definition
     *
     * @return ForeignKeyInfo
     */
    public function getToForeignKey(): ForeignKeyInfo
    {
        return $this->toForeignKey;
    }

    /**
     * Mark that the local columns changed
     *
     * @return $this
     */
    public function setColumnsChanged(): self
    {
        $this->columnsChanged = true;
        return $this;
    }

    /**
     * Check if the local columns changed
     *
     * @return bool
     */
    public function hasColumnsChanged(): bool
    {
        return $this->columnsChanged;
    }

    /**
     * Mark that the references changed
     *
     * @return $this
     */
    public function setReferencesChanged(): self
    {
        $this->referencesChanged = true;
        return $this;
    }

    /**
     * Check if the references changed
     *
     * @return bool
     */
    public function hasReferencesChanged(): bool
    {
        return $this->referencesChanged;
    }

    /**
     * Mark that the ON DELETE action changed
     *
     * @return $this
     */
    public function setOnDeleteChanged(): self
    {
        $this->onDeleteChanged = true;
        return $this;
    }

    /**
     * Check if the ON DELETE action changed
     *
     * @return bool
     */
    public function hasOnDeleteChanged(): bool
    {
        return $this->onDeleteChanged;
    }

    /**
     * Mark that the ON UPDATE action changed
     *
     * @return $this
     */
    public function setOnUpdateChanged(): self
    {
        $this->onUpdateChanged = true;
        return $this;
    }

    /**
     * Check if the ON UPDATE action changed
     *
     * @return bool
     */
    public function hasOnUpdateChanged(): bool
    {
        return $this->onUpdateChanged;
    }

    /**
     * Check if anything changed
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        return $this->columnsChanged
            || $this->referencesChanged
            || $this->onDeleteChanged
            || $this->onUpdateChanged;
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
        if ($this->referencesChanged) {
            $changes[] = 'references';
        }
        if ($this->onDeleteChanged) {
            $changes[] = 'on_delete';
        }
        if ($this->onUpdateChanged) {
            $changes[] = 'on_update';
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
            'from' => $this->fromForeignKey->toArray(),
            'to' => $this->toForeignKey->toArray(),
            'changes' => $this->getChanges(),
        ];
    }
}
