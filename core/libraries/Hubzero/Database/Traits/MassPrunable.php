<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

use Hubzero\Facades\Date;

/**
 * Mass Prunable trait for bulk old record cleanup
 *
 * This trait provides high-performance bulk deletion for models that need
 * to prune large numbers of records. Unlike the `Prunable` trait, this
 * uses bulk DELETE queries rather than loading and deleting models
 * one by one.
 *
 * Use this trait when:
 * - You have large numbers of records to prune (thousands or more)
 * - You don't need model events (deleting/deleted) to fire
 * - You don't have cleanup logic that needs to run per-record
 * - Performance is more important than event handling
 *
 * For smaller datasets or when events are needed, use `Prunable` instead.
 *
 * ## Important Notes
 *
 * - Model events (deleting, deleted) are NOT fired
 * - The `pruningCleanup()` method is NOT called
 * - Soft deletes are NOT automatically respected (records are hard deleted)
 * - Related records with cascade deletes at the database level WILL be deleted
 * - Related records WITHOUT cascade deletes will NOT be automatically cleaned up
 *
 * ## Usage
 *
 * Add the trait to your model and define the `prunable()` method:
 *
 * ```php
 * class LogEntry extends Relational
 * {
 *     use \Hubzero\Database\Traits\MassPrunable;
 *
 *     /**
 *      * Get the prunable query
 *      *
 *      * @return static
 *      * /
 *     protected function prunable()
 *     {
 *         // Delete log entries older than 30 days
 *         return static::all()
 *             ->where('created', '<', Date::of('-30 days')->toSql());
 *     }
 * }
 * ```
 *
 * ## Triggering Pruning
 *
 * ### Programmatically
 *
 * ```php
 * // Prune all matching records in batches
 * $count = LogEntry::pruneAll();
 * echo "Pruned {$count} log entries";
 * ```
 *
 * ### Via Cron Plugin
 *
 * ```php
 * // In your cron plugin
 * public function onCronLogs()
 * {
 *     $pruned = \Components\Logs\Models\Entry::pruneAll();
 *     $this->log("Pruned {$pruned} old log entries");
 * }
 * ```
 *
 * ## Performance Comparison
 *
 * For 10,000 records:
 * - `Prunable`: ~10,000 individual DELETE queries, model events fired
 * - `MassPrunable`: Batched DELETE queries, no model events
 *
 * ## Soft Deletes Warning
 *
 * If your model uses `SoftDeletes`, `MassPrunable` will perform HARD deletes
 * (permanent deletion), not soft deletes. If you need soft delete behavior
 * during pruning, use the `Prunable` trait instead.
 */
trait MassPrunable
{
    /**
     * Static recursion guard to prevent infinite loops during introspection
     *
     * @var bool
     */
    protected static $isMassPruning = false;

    /**
     * Prune all prunable models from the database using bulk deletes
     *
     * This method collects IDs in batches and executes bulk DELETE queries,
     * providing much better performance than the regular `Prunable` trait
     * for large datasets.
     *
     * WARNING: This does NOT fire model events and does NOT respect soft deletes.
     *
     * @param   int  $chunkSize  Number of IDs to delete per batch (default: 1000)
     * @return  int  The total number of records deleted
     */
    public static function pruneAll(int $chunkSize = 1000): int
    {
        // Guard against recursion from introspectRelationships
        if (static::$isMassPruning) {
            return 0;
        }

        static::$isMassPruning = true;

        try {
            $instance = new static();

            // Get the prunable query
            $query = $instance->prunable();

            if (!$query) {
                return 0;
            }

            // Fire a single "mass-pruning" system event (optional hook point)
            \Hubzero\Facades\Event::trigger('system.onMassPrune', array($instance->getTableName(), $query));

            // Execute the bulk delete in batches and return count
            return $instance->pruneInBatches($query, $chunkSize);
        } finally {
            static::$isMassPruning = false;
        }
    }

    /**
     * Delete records in batches by collecting IDs
     *
     * @param   mixed  $query      The query with prunable records
     * @param   int    $chunkSize  Batch size for deletion
     * @return  int    Number of records deleted
     */
    protected function pruneInBatches($query, int $chunkSize): int
    {
        $table = $this->getTableName();
        $pk = $this->getPrimaryKey();
        $total = 0;

        // Process in chunks to avoid memory issues with large datasets
        $query->chunkById($chunkSize, function ($models) use ($table, $pk, &$total) {
            if ($models->count() === 0) {
                return false; // Stop chunking
            }

            // Collect IDs from this batch
            $ids = [];
            foreach ($models as $model) {
                $ids[] = $model->get($pk);
            }

            if (empty($ids)) {
                return true; // Continue to next chunk
            }

            // Build and execute the batch DELETE using the Query class
            $deleteQuery = $this->getQuery();
            $deleteQuery->delete($table);

            // Add WHERE IN clause for the IDs
            $deleteQuery->whereIn($pk, $ids);

            // Execute the delete
            $deleteQuery->execute();

            $total += count($ids);

            return true; // Continue to next chunk
        });

        return $total;
    }

    /**
     * Get the prunable model query
     *
     * This method must be implemented by the model to define which records
     * should be pruned. Return a query that selects records for deletion.
     *
     * NOTE: This method is intentionally protected to avoid being invoked
     * by the relationship introspection in Relational::introspectRelationships(),
     * which invokes all public methods to discover relationships.
     *
     * @return  static  A query builder instance with prunable records
     */
    abstract protected function prunable();
}
