<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

use Hubzero\Facades\Date;

/**
 * Prunable trait for automatic old record cleanup
 *
 * This trait provides Laravel-compatible model pruning functionality,
 * allowing models to define which records should be automatically deleted.
 * Records are deleted one at a time, firing model events for each deletion.
 *
 * Use this trait when:
 * - You need model events (deleting/deleted) to fire for each record
 * - You have cleanup logic in the `pruningCleanup()` method
 * - You need to respect soft deletes and other model behaviors
 *
 * For faster bulk deletion without events, use `MassPrunable` instead.
 *
 * ## Usage
 *
 * Add the trait to your model and define the `prunable()` method:
 *
 * ```php
 * class Order extends Relational
 * {
 *     use \Hubzero\Database\Traits\Prunable;
 *
 *     /**
 *      * Get the prunable query
 *      *
 *      * @return static
 *      * /
 *     protected function prunable()
 *     {
 *         // Delete orders older than 1 year that are completed
 *         return static::all()
 *             ->where('created', '<', Date::of('-1 year')->toSql())
 *             ->whereEquals('status', 'completed');
 *     }
 * }
 * ```
 *
 * ### Optional Cleanup Before Deletion
 *
 * Override the `pruningCleanup()` method to perform cleanup before each model is deleted:
 *
 * ```php
 * protected function pruningCleanup()
 * {
 *     // Delete associated files
 *     foreach ($this->attachments as $attachment) {
 *         @unlink($attachment->path);
 *     }
 * }
 * ```
 *
 * ## Triggering Pruning
 *
 * ### Programmatically
 *
 * ```php
 * // Prune all matching records
 * $count = Order::pruneAll();
 * echo "Pruned {$count} orders";
 *
 * // Prune with a custom chunk size
 * $count = Order::pruneAll(500);
 * ```
 *
 * ### Via Cron Plugin
 *
 * ```php
 * // In your cron plugin
 * public function onCronOrders()
 * {
 *     $pruned = \Components\Orders\Models\Order::pruneAll();
 *     $this->log("Pruned {$pruned} old orders");
 * }
 * ```
 *
 * ## Events
 *
 * The standard model events fire for each pruned record:
 * - `deleting` (can cancel by returning false)
 * - `deleted` (after successful deletion)
 *
 * If the model uses `SoftDeletes`, soft deletion behavior is respected.
 *
 * To add pruning-specific behavior, use the deleting event:
 * ```php
 * Order::deleting(function ($order) {
 *     // Cleanup before deletion
 * });
 * ```
 */
trait Prunable
{
    /**
     * Static recursion guard to prevent infinite loops during introspection
     *
     * @var bool
     */
    protected static $isPruning = false;

    /**
     * Prune all prunable models from the database
     *
     * This method retrieves records in chunks and deletes them one by one,
     * firing model events for each deletion.
     *
     * @param   int  $chunkSize  Number of records to process at a time (default: 1000)
     * @return  int  The total number of records pruned
     */
    public static function pruneAll(int $chunkSize = 1000): int
    {
        // Guard against recursion from introspectRelationships
        if (static::$isPruning) {
            return 0;
        }

        static::$isPruning = true;

        try {
            $instance = new static();
            $total = 0;

            // Get the prunable query
            $query = $instance->prunable();

            if (!$query) {
                return 0;
            }

            // Process in chunks using chunkById for better performance on large datasets
            $query->chunkById($chunkSize, function ($models) use (&$total) {
                foreach ($models as $model) {
                    if ($model->prune()) {
                        $total++;
                    }
                }
            });

            return $total;
        } finally {
            static::$isPruning = false;
        }
    }

    /**
     * Prune the model from the database
     *
     * This method handles the pruning of a single model instance,
     * calling the pruningCleanup() method if defined.
     *
     * @return  bool  True if the model was pruned, false otherwise
     */
    public function prune(): bool
    {
        // Guard: Only prune models that have been persisted (have a primary key)
        // This prevents infinite recursion when introspectRelationships() invokes
        // this method on a blank instance during cascade delete operations
        $pk = $this->getPrimaryKey();
        if (!$this->get($pk)) {
            return false;
        }

        // Call the optional pruningCleanup() method for cleanup before deletion
        if (method_exists($this, 'pruningCleanup')) {
            $this->pruningCleanup();
        }

        // Delete the model - this fires deleting/deleted events naturally
        $result = $this->destroy();

        return (bool) $result;
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
