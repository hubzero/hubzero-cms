<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Soft delete trait for Relational models
 *
 * This trait provides soft delete functionality for models, allowing records
 * to be marked as deleted via a timestamp rather than being physically removed
 * from the database. This enables:
 * - Recovery of accidentally deleted records
 * - Audit trails and data retention
 * - Referential integrity preservation
 *
 * ## Usage
 *
 * Add the trait to your model and ensure your table has a `deleted_at` column:
 *
 * ```php
 * class Article extends Relational
 * {
 *     use \Hubzero\Database\Traits\SoftDeletes;
 *
 *     protected $table = '#__articles';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Your table needs a nullable timestamp column (default name: `deleted_at`):
 *
 * ```sql
 * ALTER TABLE #__articles ADD COLUMN deleted_at DATETIME NULL;
 * ```
 *
 * ### Custom Column Name
 *
 * Override the column name in your model if needed:
 *
 * ```php
 * protected $deletedAtColumn = 'removed_at';
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // Soft delete a record (sets deleted_at timestamp)
 * $article->destroy();
 *
 * // Check if record is soft deleted
 * $article->trashed();  // true
 *
 * // Restore a soft deleted record
 * $article->restore();
 *
 * // Permanently delete (bypass soft delete)
 * $article->forceDelete();
 * ```
 *
 * ## Querying with Soft Deletes
 *
 * By default, soft deleted records are excluded from all queries:
 *
 * ```php
 * // Only returns non-deleted articles
 * $articles = Article::all()->rows();
 * ```
 *
 * To include soft deleted records:
 *
 * ```php
 * // Include soft deleted records
 * $allArticles = Article::withTrashed()->rows();
 *
 * // Only soft deleted records
 * $trashedArticles = Article::onlyTrashed()->rows();
 * ```
 */
trait SoftDeletes
{
    /**
     * Indicates if the model is currently force deleting
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the soft delete trait for a model
     *
     * This is called automatically when the model boots. It registers
     * a global scope that filters out soft deleted records.
     *
     * @return  void
     */
    protected static function bootSoftDeletes()
    {
        static::addGlobalScope('softDeletes', function ($query) {
            // We need to get the column name from an instance
            $instance = new static();
            $column = $instance->getDeletedAtColumn();
            $table = $instance->getTableName();

            // Filter out soft deleted records (where deleted_at IS NULL)
            $query->whereIsNull($table . '.' . $column);
        });
    }

    /**
     * Register a "restoring" model event callback
     *
     * The callback receives the model instance and can return false to cancel the operation.
     * This event fires before a soft deleted record is restored.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     */
    public static function restoring(callable $callback)
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a "restored" model event callback
     *
     * This event fires after a soft deleted record has been restored.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     */
    public static function restored(callable $callback)
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Get the name of the "deleted at" column
     *
     * @return  string
     */
    public function getDeletedAtColumn()
    {
        return defined('static::DELETED_AT')
            ? static::DELETED_AT
            : (property_exists($this, 'deletedAtColumn') ? $this->deletedAtColumn : 'deleted_at');
    }

    /**
     * Get the fully qualified "deleted at" column
     *
     * @return  string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->getTableName() . '.' . $this->getDeletedAtColumn();
    }

    /**
     * Determine if the model is currently force deleting
     *
     * @return  bool
     */
    public function isForceDeleting()
    {
        return $this->forceDeleting;
    }

    /**
     * Determine if the model instance has been soft deleted
     *
     * @return  bool
     */
    public function trashed()
    {
        return !is_null($this->get($this->getDeletedAtColumn()));
    }

    /**
     * Soft delete the model (sets deleted_at timestamp)
     *
     * This overrides the default destroy() behavior. To permanently
     * delete a record, use forceDelete() instead.
     *
     * Fires lifecycle events:
     * - deleting (can cancel by returning false)
     * - [soft delete operation]
     * - deleted
     *
     * @return  bool
     */
    public function destroy()
    {
        // If force deleting, use the parent's destroy method
        if ($this->forceDeleting) {
            return $this->performForceDelete();
        }

        // If already trashed, do nothing
        if ($this->trashed()) {
            return false;
        }

        // Fire "deleting" event (can cancel)
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $result = $this->runSoftDelete();

        if ($result) {
            // Fire "deleted" event
            $this->fireModelEvent('deleted', false);
        }

        return $result;
    }

    /**
     * Perform the actual soft delete
     *
     * @return  bool
     */
    protected function runSoftDelete()
    {
        $column = $this->getDeletedAtColumn();

        $now = date('Y-m-d H:i:s');

        // Update the model's deleted_at attribute
        $this->set($column, $now);

        // Trigger event before soft delete
        \Event::trigger('system.onContentSoftDelete', array($this->getTableName(), $this));

        // Perform the update using getQuery() which always returns a fresh query
        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([$column => $now])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        // Sync original via the syncOriginalAttribute method if available,
        // otherwise set directly through method
        $this->syncOriginalAttribute($column);

        return (bool) $result;
    }

    /**
     * Force delete the model from the database
     *
     * This permanently removes the record, bypassing soft delete.
     *
     * @return  bool
     */
    public function forceDelete()
    {
        $this->forceDeleting = true;

        $result = $this->performForceDelete();

        $this->forceDeleting = false;

        return $result;
    }

    /**
     * Perform the force delete
     *
     * Fires lifecycle events:
     * - deleting (can cancel by returning false)
     * - [database delete]
     * - deleted
     *
     * @return  bool
     */
    protected function performForceDelete()
    {
        // Fire "deleting" event (can cancel)
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // If it has an associated asset entry, try deleting that first
        if ($this->hasAttribute('asset_id')) {
            if (!\Hubzero\Database\Asset::destroy($this)) {
                return false;
            }
        }

        $result = $this->getQuery()->remove(
            $this->getTableName(),
            $this->getPrimaryKey(),
            $this->getPkValue()
        );

        if ($result) {
            // Fire "deleted" event
            $this->fireModelEvent('deleted', false);

            // Existing HubZero system event
            \Event::trigger('system.onContentDestroy', array($this->getTableName(), $this));
        }

        // Cast to bool for consistent return type
        return (bool) $result;
    }

    /**
     * Restore a soft deleted model
     *
     * Fires lifecycle events:
     * - restoring (can cancel by returning false)
     * - [restore operation]
     * - restored
     *
     * @return  bool
     */
    public function restore()
    {
        // Can only restore if trashed
        if (!$this->trashed()) {
            return false;
        }

        // Fire "restoring" event (can cancel)
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $column = $this->getDeletedAtColumn();

        // Trigger existing HubZero event before restore
        \Event::trigger('system.onContentRestore', array($this->getTableName(), $this));

        // Update the model's deleted_at attribute to null
        $this->set($column, null);

        // Perform the update using getQuery() for a fresh query
        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([$column => null])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        // Sync original to prevent dirty state
        $this->syncOriginalAttribute($column);

        if ($result) {
            // Fire "restored" event
            $this->fireModelEvent('restored', false);
        }

        return (bool) $result;
    }

    /**
     * Include soft deleted records in the query
     *
     * @return  $this
     */
    public function withTrashed()
    {
        return $this->withoutGlobalScope('softDeletes');
    }

    /**
     * Only include soft deleted records in the query
     *
     * @return  $this
     */
    public function onlyTrashed()
    {
        $this->withoutGlobalScope('softDeletes');

        $column = $this->getQualifiedDeletedAtColumn();

        // Initialize query and apply the constraint
        $this->newQuery();
        $this->whereIsNotNull($column);

        return $this;
    }

    /**
     * Static method to include trashed records in query
     *
     * @return  static
     */
    public static function withTrashedQuery()
    {
        return static::blank()->withTrashed();
    }

    /**
     * Static method to only get trashed records
     *
     * @return  static
     */
    public static function onlyTrashedQuery()
    {
        return static::blank()->onlyTrashed();
    }
}
