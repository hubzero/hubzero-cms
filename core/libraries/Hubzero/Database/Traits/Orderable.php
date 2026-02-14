<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Orderable trait for Relational models
 *
 * This trait provides ordering/sorting functionality for models, allowing records
 * to be manually ordered with move up/down operations and reordering capabilities.
 *
 * ## Usage
 *
 * Add the trait to your model and ensure your table has an ordering column:
 *
 * ```php
 * class MenuItem extends Relational
 * {
 *     use \Hubzero\Database\Traits\Orderable;
 *
 *     protected $table = '#__menu_items';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Your table needs an ordering column (INT):
 *
 * ```sql
 * ALTER TABLE #__menu_items ADD COLUMN ordering INT NOT NULL DEFAULT 0;
 * CREATE INDEX idx_ordering ON #__menu_items (ordering);
 * ```
 *
 * Or using schema builder:
 *
 * ```php
 * $this->schema()->alterTable('#__menu_items')
 *     ->addColumn('ordering', 'integer', ['default' => 0])
 *     ->addIndex('idx_ordering', ['ordering'])
 *     ->execute();
 * ```
 *
 * ### Custom Column Name
 *
 * Override the column name if needed:
 *
 * ```php
 * protected $orderingColumn = 'sort_order';
 * ```
 *
 * ### Ordering Groups
 *
 * Often you want ordering within groups (e.g., menu items per menu):
 *
 * ```php
 * protected $orderingGroup = ['menu_id'];  // Order within each menu
 * // or
 * protected $orderingGroup = ['category_id', 'state'];  // Multiple columns
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // Move up one position
 * $item->moveUp();
 *
 * // Move down one position
 * $item->moveDown();
 *
 * // Move by delta (-2 = up 2 positions, +3 = down 3 positions)
 * $item->move(-2);
 *
 * // Move to first position
 * $item->moveFirst();
 *
 * // Move to last position
 * $item->moveLast();
 *
 * // Move to specific position
 * $item->moveTo(5);
 *
 * // Reorder all items (compact/renumber)
 * MenuItem::reorderAll(['menu_id' => 1]);
 *
 * // Set ordering on new record
 * $item->setNewOrdering();
 * $item->save();
 * ```
 */
trait Orderable
{
    /**
     * Get the name of the ordering column
     *
     * @return  string
     */
    public function getOrderingColumn(): string
    {
        return property_exists($this, 'orderingColumn')
            ? $this->orderingColumn
            : 'ordering';
    }

    /**
     * Get the ordering group columns
     *
     * These columns define the scope within which ordering applies.
     * For example, if orderingGroup is ['menu_id'], items are ordered
     * within each menu separately.
     *
     * @return  array
     */
    public function getOrderingGroup(): array
    {
        if (property_exists($this, 'orderingGroup')) {
            return (array) $this->orderingGroup;
        }
        return [];
    }

    /**
     * Get the current ordering value
     *
     * @return  int
     */
    public function getOrdering(): int
    {
        return (int) $this->get($this->getOrderingColumn(), 0);
    }

    /**
     * Set the ordering value
     *
     * @param   int   $ordering  The ordering value
     * @return  self
     */
    public function setOrdering(int $ordering)
    {
        $this->set($this->getOrderingColumn(), $ordering);
        return $this;
    }

    /**
     * Move the record up or down by a delta
     *
     * @param   int   $delta  Negative = move up, Positive = move down
     * @return  bool  True on success
     */
    public function move(int $delta): bool
    {
        if ($delta == 0) {
            return true;
        }

        $orderingCol = $this->getOrderingColumn();
        $currentOrdering = $this->getOrdering();
        $newOrdering = $currentOrdering + $delta;

        if ($newOrdering < 1) {
            $newOrdering = 1;
        }

        // Build query for adjacent record
        $query = $this->getQuery()
            ->select($this->getPrimaryKey())
            ->select($orderingCol)
            ->from($this->getTableName());

        // Apply group constraints
        $this->applyOrderingGroupConstraints($query);

        if ($delta < 0) {
            // Moving up: find record with ordering just below target
            $query->where($orderingCol, '<', $currentOrdering)
                  ->order($orderingCol, 'desc');
        } else {
            // Moving down: find record with ordering just above target
            $query->where($orderingCol, '>', $currentOrdering)
                  ->order($orderingCol, 'asc');
        }

        $query->limit(abs($delta));

        $adjacentRecords = $query->fetch('rows');

        if (empty($adjacentRecords)) {
            return true; // Already at the edge
        }

        // Swap with the last record in the delta range
        $swapRecord = end($adjacentRecords);
        $swapOrdering = (int) $swapRecord->{$orderingCol};
        $swapId = $swapRecord->{$this->getPrimaryKey()};

        // Update adjacent record
        $this->getQuery()
            ->update($this->getTableName())
            ->set([$orderingCol => $currentOrdering])
            ->whereEquals($this->getPrimaryKey(), $swapId)
            ->execute();

        // Update this record
        $this->set($orderingCol, $swapOrdering);
        $this->getQuery()
            ->update($this->getTableName())
            ->set([$orderingCol => $swapOrdering])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        $this->syncOriginalAttribute($orderingCol);

        return true;
    }

    /**
     * Move the record up one position
     *
     * @return  bool
     */
    public function moveUp(): bool
    {
        return $this->move(-1);
    }

    /**
     * Move the record down one position
     *
     * @return  bool
     */
    public function moveDown(): bool
    {
        return $this->move(1);
    }

    /**
     * Move the record to the first position
     *
     * @return  bool
     */
    public function moveFirst(): bool
    {
        return $this->moveTo(1);
    }

    /**
     * Move the record to the last position
     *
     * @return  bool
     */
    public function moveLast(): bool
    {
        $maxOrdering = $this->getMaxOrdering();
        return $this->moveTo($maxOrdering);
    }

    /**
     * Move the record to a specific position
     *
     * @param   int   $position  The target position (1-based)
     * @return  bool
     */
    public function moveTo(int $position): bool
    {
        if ($position < 1) {
            $position = 1;
        }

        $orderingCol = $this->getOrderingColumn();
        $currentOrdering = $this->getOrdering();

        if ($position == $currentOrdering) {
            return true;
        }

        // Shift other records
        if ($position < $currentOrdering) {
            // Moving up: shift records down
            $query = $this->getQuery()
                ->update($this->getTableName())
                ->setRaw($orderingCol, $orderingCol . ' + 1')
                ->where($orderingCol, '>=', $position)
                ->where($orderingCol, '<', $currentOrdering);

            $this->applyOrderingGroupConstraints($query);
            $query->execute();
        } else {
            // Moving down: shift records up
            $query = $this->getQuery()
                ->update($this->getTableName())
                ->setRaw($orderingCol, $orderingCol . ' - 1')
                ->where($orderingCol, '>', $currentOrdering)
                ->where($orderingCol, '<=', $position);

            $this->applyOrderingGroupConstraints($query);
            $query->execute();
        }

        // Update this record
        $this->set($orderingCol, $position);
        $this->getQuery()
            ->update($this->getTableName())
            ->set([$orderingCol => $position])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        $this->syncOriginalAttribute($orderingCol);

        return true;
    }

    /**
     * Set the ordering for a new record (appends to end)
     *
     * Call this before save() on new records.
     *
     * @return  self
     */
    public function setNewOrdering()
    {
        $maxOrdering = $this->getMaxOrdering();
        $this->set($this->getOrderingColumn(), $maxOrdering + 1);
        return $this;
    }

    /**
     * Get the maximum ordering value within the group
     *
     * @return  int
     */
    public function getMaxOrdering(): int
    {
        $orderingCol = $this->getOrderingColumn();

        $query = $this->getQuery()
            ->select('MAX(' . $orderingCol . ') as max_ordering')
            ->from($this->getTableName());

        $this->applyOrderingGroupConstraints($query);

        $result = $query->fetch('row');

        return $result ? (int) $result->max_ordering : 0;
    }

    /**
     * Reorder all records, compacting the ordering numbers
     *
     * This renumbers all records starting from 1 with no gaps.
     *
     * @param   array  $where  Optional WHERE conditions for grouping
     * @return  int    Number of records reordered
     */
    public static function reorderAll(array $where = []): int
    {
        $instance = new static();
        $orderingCol = $instance->getOrderingColumn();
        $pk = $instance->getPrimaryKey();
        $table = $instance->getTableName();

        // Get all records in current order
        $query = $instance->getQuery()
            ->select($pk)
            ->from($table)
            ->order($orderingCol, 'asc');

        foreach ($where as $col => $val) {
            $query->whereEquals($col, $val);
        }

        $records = $query->fetch('rows');

        if (empty($records)) {
            return 0;
        }

        // Renumber from 1
        $count = 0;
        foreach ($records as $i => $record) {
            $newOrdering = $i + 1;

            $instance->getQuery()
                ->update($table)
                ->set([$orderingCol => $newOrdering])
                ->whereEquals($pk, $record->{$pk})
                ->execute();

            $count++;
        }

        return $count;
    }

    /**
     * Reorder records based on an array of IDs
     *
     * @param   array  $ids    Array of primary key values in desired order
     * @param   array  $where  Optional WHERE conditions for scoping
     * @return  int    Number of records reordered
     */
    public static function reorderByIds(array $ids, array $where = []): int
    {
        if (empty($ids)) {
            return 0;
        }

        $instance = new static();
        $orderingCol = $instance->getOrderingColumn();
        $pk = $instance->getPrimaryKey();
        $table = $instance->getTableName();

        $count = 0;
        foreach ($ids as $i => $id) {
            $newOrdering = $i + 1;

            $query = $instance->getQuery()
                ->update($table)
                ->set([$orderingCol => $newOrdering])
                ->whereEquals($pk, $id);

            foreach ($where as $col => $val) {
                $query->whereEquals($col, $val);
            }

            $query->execute();
            $count++;
        }

        return $count;
    }

    /**
     * Apply ordering group constraints to a query
     *
     * @param   \Hubzero\Database\Query  $query  The query to constrain
     * @return  void
     */
    protected function applyOrderingGroupConstraints($query): void
    {
        $groupColumns = $this->getOrderingGroup();

        foreach ($groupColumns as $col) {
            $value = $this->get($col);
            if ($value !== null) {
                $query->whereEquals($col, $value);
            }
        }
    }

    /**
     * Boot the orderable trait
     *
     * Auto-set ordering for new records if not already set.
     *
     * @return  void
     */
    protected static function bootOrderable(): void
    {
        static::creating(function ($model) {
            $orderingCol = $model->getOrderingColumn();

            // Only set if not already set
            if (!$model->get($orderingCol)) {
                $model->setNewOrdering();
            }
        });
    }
}
