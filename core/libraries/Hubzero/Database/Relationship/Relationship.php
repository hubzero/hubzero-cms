<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

/**
 * Database base relationship
 *
 * This is essentially the base relationship for 1-1 relationships.
 * Multiplicitous relationships will override all methods that are
 * otherwise singular in this class.
 *
 * ## Cascade Operations
 *
 * Relationships support automatic cascade operations that propagate changes
 * to related models. These are opt-in and configured per-relationship:
 *
 * ```php
 * public function comments()
 * {
 *     return $this->hasMany(Comment::class)
 *         ->cascadeOnDelete()   // Delete comments when post is deleted
 *         ->cascadeOnSave()     // Save unsaved comments when post saves
 *         ->orphanRemoval();    // Remove comments detached from post
 * }
 * ```
 *
 * | Method          | Description                                      |
 * |-----------------|--------------------------------------------------|
 * | cascadeOnDelete | Delete related when parent is destroyed          |
 * | cascadeOnSave   | Save dirty related when parent saves             |
 * | orphanRemoval   | Delete related that are no longer associated     |
 *
 * These operations are backwards-compatible: existing code continues to work
 * unchanged. You must explicitly opt-in to cascade behavior.
 */
class Relationship
{
    /**
     * The primary model
     *
     * @var  \Hubzero\Database\Relational|static
     **/
    protected $model = null;

    /**
     * The related model
     *
     * @var  \Hubzero\Database\Relational|static
     **/
    protected $related = null;

    /**
     * The local key (probably 'id')
     *
     * @var  string
     **/
    protected $localKey = null;

    /**
     * The related key (probably 'modelName_id')
     *
     * @var  string
     **/
    protected $relatedKey = null;

    /**
     * Whether to cascade delete to related models
     *
     * When true, deleting the parent model will automatically delete
     * all related models through this relationship.
     *
     * @var  bool
     **/
    protected $cascadeDelete = false;

    /**
     * Whether to use bulk delete for cascade operations
     *
     * When true, uses a single DELETE WHERE query instead of
     * deleting each related model individually. This is much faster
     * but does NOT fire model events (deleting/deleted) on related models.
     *
     * @var  bool
     **/
    protected $bulkCascadeDelete = false;

    /**
     * Whether to cascade save to related models
     *
     * When true, saving the parent model will automatically save
     * any dirty related models through this relationship.
     *
     * @var  bool
     **/
    protected $cascadeSave = false;

    /**
     * Whether to remove orphaned related models
     *
     * When true, related models that are no longer associated
     * with the parent will be deleted automatically.
     *
     * @var  bool
     **/
    protected $orphanRemoval = false;

    /**
     * Default conditions to apply when fetching related models
     *
     * Each condition is stored as a closure that receives the query.
     * Applied automatically in constrain() and eager loading.
     *
     * @var  array
     **/
    protected $defaultConditions = [];

    /**
     * Constructs a new object instance
     *
     * @param   \Hubzero\Database\Relational|static  $model       The primary model
     * @param   \Hubzero\Database\Relational|static  $related     The related model
     * @param   \Hubzero\Database\Relational|static  $localKey    The local key
     * @param   \Hubzero\Database\Relational|static  $relatedKey  The related key
     * @return  void
     **/
    public function __construct($model, $related, $localKey, $relatedKey)
    {
        $this->model      = $model;
        $this->related    = $related;
        $this->localKey   = $localKey;
        $this->relatedKey = $relatedKey;
    }

    /**
     * Handles calls to undefined methods, assuming they should be passed up to the model
     *
     * @param   string  $name       The method name being called
     * @param   array   $arguments  The method arguments provided
     * @return  mixed
     **/
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->constrain(), $name), $arguments);
    }

    /**
     * Returns the key name of the primary table
     *
     * @return  string
     **/
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * Returns the key name of the related table
     *
     * @return  string
     **/
    public function getRelatedKey()
    {
        return $this->relatedKey;
    }

    /**
     * Fetch results of relationship
     *
     * @return  \Hubzero\Database\Relational
     **/
    public function rows()
    {
        return $this->constrain()->row();
    }

    /**
     * Constrains the relationship content to the applicable rows on the related model
     *
     * This method applies both the base relationship constraint (foreign key match)
     * and any default conditions defined via onCondition().
     *
     * @return  object
     **/
    public function constrain()
    {
        $query = $this->related->whereEquals($this->relatedKey, $this->model->{$this->localKey});

        // Apply any default conditions
        return $this->applyDefaultConditions($query);
    }

    /**
     * Gets keys based on a given constraint
     *
     * @param   closure  $constraint  The constraint function to apply
     * @return  array
     **/
    public function getConstrainedKeys($constraint)
    {
        $this->related->select($this->relatedKey);

        return $this->getConstrained($constraint)->fieldsByKey($this->relatedKey);
    }

    /**
     * Gets rows based on given constraint
     *
     * @param   closure  $constraint  The constraint function to apply
     * @return  \Hubzero\Database\Rows
     **/
    public function getConstrainedRows($constraint)
    {
        $this->related->select($this->related->getQualifiedFieldName('*'));

        return $this->getConstrained($constraint);
    }

    /**
     * Gets the constrained count
     *
     * @param   int     $count     The count to limit by
     * @param   string  $operator  The comparison operator used between the column and the count
     * @return  array
     **/
    public function getConstrainedKeysByCount($count, $operator = '>=')
    {
        $relatedKey = $this->relatedKey;

        return $this->getConstrainedKeys(function ($related) use ($count, $relatedKey, $operator) {
            $related->group($relatedKey)->having('COUNT(*)', $operator, $count);
        });
    }

    /**
     * Gets the constrained items
     *
     * @param   closure  $constraint  The constraint function to apply
     * @return  \Hubzero\Database\Rows
     **/
    protected function getConstrained($constraint)
    {
        call_user_func_array($constraint, array($this->related));

        // Note that rows is called on the base relational model, not on this relationship,
        // thus it is not calling the constrain method...which is how we want it to work.
        // Constraining here would not make sense as that would limit our result to 1 entry.
        return $this->related->rows();
    }

    /**
     * Get related keys from a given row set
     *
     * @param   \Hubzero\Database\Rows  $rows  The rows from which to grab the related keys
     * @return  array
     **/
    public function getRelatedKeysFromRows($rows)
    {
        return $rows->fieldsByKey($this->getRelatedKey());
    }

    /**
     * Joins the related table together for the pending query
     *
     * @return  $this
     **/
    public function join()
    {
        // We do a left outer join here because we're not trying to limit the primary table's results
        // This function is primarily used when needing to sort by a field in the joined table
        $this->model->select($this->model->getQualifiedFieldName('*'))
                    ->join(
                        $this->related->getTableName(),
                        $this->model->getQualifiedFieldName($this->localKey),
                        $this->related->getQualifiedFieldName($this->relatedKey),
                        'LEFT OUTER'
                    );

        return $this;
    }

    /**
     * Associates the model provided back to the model by way of their proper keys
     *
     * Because this is a singular relationship, we never expect to have more than one
     * model at at time.
     *
     * @param   object   $model     The model to associate
     * @param   closure  $callback  A callback to potentially append additional data
     * @return  object
     **/
    public function associate($model, $callback = null)
    {
        $model->set($this->relatedKey, $this->model->getPkValue());

        if (isset($callback) && is_callable($callback)) {
            call_user_func_array($callback, [$model]);
        }

        return $model;
    }

    /**
     * Saves a new related model with the given data
     *
     * @param   array  $data  The data being saved on the new model
     * @return  bool
     **/
    public function save($data)
    {
        $related = $this->related;
        $model   = $related::newFromResults($data);

        return $this->associate($model)->save();
    }

    /**
     * Loads the relationship content with the provided data
     *
     * @param   array   $rows  The rows that we'll be seeding
     * @param   string  $data  The data to seed
     * @param   string  $name  The name of the relationship
     * @return  object
     **/
    public function seedWithData($rows, $data, $name)
    {
        return $this->seed($rows, $data, $name);
    }

    /**
     * Loads the relationship content, and sets it on the related model
     *
     * This is used when pre-loading relationship content
     * via ({@link \Hubzero\Database\Relational::with()})
     *
     * @param   array    $rows        The rows that we'll be seeding
     * @param   string   $name        The relationship name that we'll use to attach to the rows
     * @param   closure  $constraint  The constraint function to limit related items
     * @param   string   $subs        The nested relationships that should be passed on to the child
     * @return  object
     **/
    public function seedWithRelation($rows, $name, $constraint = null, $subs = null)
    {
        if (!$keys = $rows->fieldsByKey($this->localKey)) {
            return $rows;
        }

        $relations = $this->getRelations($keys, $constraint);

        if (isset($subs)) {
            $relations->with($subs);
        }

        $resultsByRelatedKey = $this->getResultsByRelatedKey($relations);

        return $this->seed($rows, $resultsByRelatedKey, $name);
    }

    /**
     * Gets the relations that will be seeded on to the provided rows
     *
     * This method applies default conditions (via onCondition()) in addition
     * to any constraint closure provided during eager loading.
     *
     * @param   array    $keys        The keys for which to fetch related items
     * @param   closure  $constraint  The constraint function to limit related items
     * @return  array
     **/
    protected function getRelations($keys, $constraint = null)
    {
        // Apply default conditions first
        $this->applyDefaultConditions($this->related);

        // Then apply any eager loading constraint
        if (isset($constraint)) {
            call_user_func_array($constraint, array($this->related));
        }

        return $this->related->whereIn($this->relatedKey, array_unique($keys));
    }

    /**
     * Sorts the relations into arrays keyed by the related key
     *
     * @param   array  $relations  The relations to sort
     * @return  array
     **/
    protected function getResultsByRelatedKey($relations)
    {
        return $relations->rows();
    }

    /**
     * Seeds the given rows with data
     *
     * @param   \Hubzero\Database\Rows  $rows  The rows to seed on to
     * @param   \Hubzero\Database\Rows  $data  The data from which to seed
     * @param   string                  $name  The relationship name
     * @return  array
     **/
    protected function seed($rows, $data, $name)
    {
        foreach ($rows as $row) {
            if ($related = $data->seek($row->{$this->localKey})) {
                $row->addRelationship($name, $related);
            } else {
                $related = $this->related;
                $row->addRelationship($name, $related::blank());
            }
        }

        return $rows;
    }

    // =========================================================================
    // Cascade Operations
    // =========================================================================

    /**
     * Enable cascade delete for this relationship
     *
     * When enabled, deleting the parent model will automatically delete
     * all related models through this relationship.
     *
     * **Performance modes:**
     * - `bulk: false` (default) - Deletes each model individually, firing
     *   deleting/deleted events on each. Safe but slow for large datasets.
     * - `bulk: true` - Uses a single DELETE WHERE query. Much faster but
     *   does NOT fire model events on related models.
     *
     * Example:
     * ```php
     * public function comments()
     * {
     *     // Individual deletes with events (default)
     *     return $this->oneToMany(Comment::class)->cascadeOnDelete();
     * }
     *
     * public function logs()
     * {
     *     // Bulk delete for performance (no events)
     *     return $this->oneToMany(ActivityLog::class)->cascadeOnDelete(bulk: true);
     * }
     *
     * // Now deleting a post also deletes its comments:
     * $post->destroy(); // Comments are deleted automatically
     * ```
     *
     * @param   bool  $enabled  Whether to enable (default: true)
     * @param   bool  $bulk     Use bulk DELETE query instead of individual deletes (default: false)
     * @return  $this
     **/
    public function cascadeOnDelete($enabled = true, $bulk = false)
    {
        $this->cascadeDelete = $enabled;
        $this->bulkCascadeDelete = $bulk;

        return $this;
    }

    /**
     * Enable cascade save for this relationship
     *
     * When enabled, saving the parent model will automatically save
     * any dirty (unsaved) related models through this relationship.
     *
     * Example:
     * ```php
     * public function profile()
     * {
     *     return $this->hasOne(Profile::class)->cascadeOnSave();
     * }
     *
     * // Changes to profile are saved when user is saved:
     * $user->profile->set('bio', 'New bio');
     * $user->save(); // Profile is also saved
     * ```
     *
     * @param   bool  $enabled  Whether to enable (default: true)
     * @return  $this
     **/
    public function cascadeOnSave($enabled = true)
    {
        $this->cascadeSave = $enabled;

        return $this;
    }

    /**
     * Enable orphan removal for this relationship
     *
     * When enabled, related models that are no longer associated with
     * the parent will be automatically deleted (not just unlinked).
     *
     * This is particularly useful when the related entity has no meaning
     * outside of its parent context (e.g., order items without an order).
     *
     * Example:
     * ```php
     * public function items()
     * {
     *     return $this->hasMany(OrderItem::class)->orphanRemoval();
     * }
     *
     * // Removing an item from the collection deletes it:
     * $order->items->remove($item);
     * $order->save(); // $item is deleted from database
     * ```
     *
     * @param   bool  $enabled  Whether to enable (default: true)
     * @return  $this
     **/
    public function orphanRemoval($enabled = true)
    {
        $this->orphanRemoval = $enabled;

        return $this;
    }

    /**
     * Check if cascade delete is enabled
     *
     * @return  bool
     **/
    public function shouldCascadeOnDelete()
    {
        return $this->cascadeDelete;
    }

    /**
     * Check if bulk cascade delete mode is enabled
     *
     * When true, cascade deletes should use a single DELETE WHERE query
     * instead of iterating over each related model.
     *
     * @return  bool
     **/
    public function shouldBulkCascadeDelete()
    {
        return $this->bulkCascadeDelete;
    }

    /**
     * Check if cascade save is enabled
     *
     * @return  bool
     **/
    public function shouldCascadeOnSave()
    {
        return $this->cascadeSave;
    }

    /**
     * Check if orphan removal is enabled
     *
     * @return  bool
     **/
    public function shouldRemoveOrphans()
    {
        return $this->orphanRemoval;
    }

    /**
     * Get the related model instance
     *
     * @return  \Hubzero\Database\Relational|static
     **/
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Get the parent model instance
     *
     * @return  \Hubzero\Database\Relational|static
     **/
    public function getModel()
    {
        return $this->model;
    }

    // =========================================================================
    // Default Conditions (onCondition)
    // =========================================================================

    /**
     * Add a default condition to this relationship
     *
     * Default conditions are automatically applied every time the relationship
     * is accessed or eager loaded. This is useful for relationships that should
     * always return a filtered subset of related models.
     *
     * **Multiple calling styles:**
     * ```php
     * // Simple equality: status = 1
     * ->onCondition('status', 1)
     *
     * // With operator: created_at > '2024-01-01'
     * ->onCondition('created_at', '>', '2024-01-01')
     *
     * // With closure for complex conditions
     * ->onCondition(function($query) {
     *     $query->whereEquals('status', 1)
     *           ->whereIn('type', ['article', 'post']);
     * })
     * ```
     *
     * **Example usage in model:**
     * ```php
     * public function activeComments()
     * {
     *     return $this->oneToMany(Comment::class, 'post_id')
     *         ->onCondition('status', Comment::STATUS_ACTIVE);
     * }
     *
     * public function recentComments()
     * {
     *     return $this->oneToMany(Comment::class, 'post_id')
     *         ->onCondition('created_at', '>', Date::of('-30 days'))
     *         ->onCondition('status', 1);
     * }
     *
     * // Then simply:
     * $post->activeComments()->rows();  // Always returns active comments
     * $post->recentComments()->rows();  // Always returns recent active comments
     * ```
     *
     * Conditions can be chained and are applied in order.
     *
     * @param   string|\Closure  $column    Column name, or closure for complex conditions
     * @param   mixed            $operator  Operator (or value if only 2 args)
     * @param   mixed            $value     The value to compare against
     * @return  $this
     **/
    public function onCondition($column, $operator = null, $value = null)
    {
        // Handle closure
        if ($column instanceof \Closure) {
            $this->defaultConditions[] = $column;
            return $this;
        }

        // Handle 2-argument form: onCondition('column', 'value')
        if ($value === null && $operator !== null) {
            $value = $operator;
            $operator = '=';
        }

        // Store as closure for uniform handling
        $this->defaultConditions[] = function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        };

        return $this;
    }

    /**
     * Add a default condition with OR logic
     *
     * Similar to onCondition() but joins with OR instead of AND.
     *
     * ```php
     * // WHERE (post_id = ?) AND (status = 1 OR featured = 1)
     * ->onCondition('status', 1)
     * ->orOnCondition('featured', 1)
     * ```
     *
     * @param   string|\Closure  $column    Column name, or closure for complex conditions
     * @param   mixed            $operator  Operator (or value if only 2 args)
     * @param   mixed            $value     The value to compare against
     * @return  $this
     **/
    public function orOnCondition($column, $operator = null, $value = null)
    {
        // Handle closure
        if ($column instanceof \Closure) {
            // Wrap to use OR logic for first condition in closure
            $this->defaultConditions[] = function ($query) use ($column) {
                // Note: The closure needs to handle its own OR logic
                call_user_func($column, $query);
            };
            return $this;
        }

        // Handle 2-argument form
        if ($value === null && $operator !== null) {
            $value = $operator;
            $operator = '=';
        }

        // Store as closure with OR logic
        $this->defaultConditions[] = function ($query) use ($column, $operator, $value) {
            $query->orWhere($column, $operator, $value);
        };

        return $this;
    }

    /**
     * Add a whereIn condition to this relationship
     *
     * Convenience method for common IN conditions.
     *
     * ```php
     * ->onConditionIn('status', [1, 2, 3])
     * ```
     *
     * @param   string  $column  Column name
     * @param   array   $values  Array of values
     * @return  $this
     **/
    public function onConditionIn($column, array $values)
    {
        $this->defaultConditions[] = function ($query) use ($column, $values) {
            $query->whereIn($column, $values);
        };

        return $this;
    }

    /**
     * Add a whereNotIn condition to this relationship
     *
     * ```php
     * ->onConditionNotIn('status', [0, -1])
     * ```
     *
     * @param   string  $column  Column name
     * @param   array   $values  Array of values
     * @return  $this
     **/
    public function onConditionNotIn($column, array $values)
    {
        $this->defaultConditions[] = function ($query) use ($column, $values) {
            $query->whereNotIn($column, $values);
        };

        return $this;
    }

    /**
     * Add a whereNull condition to this relationship
     *
     * ```php
     * ->onConditionNull('deleted_at')
     * ```
     *
     * @param   string  $column  Column name
     * @return  $this
     **/
    public function onConditionNull($column)
    {
        $this->defaultConditions[] = function ($query) use ($column) {
            $query->whereIsNull($column);
        };

        return $this;
    }

    /**
     * Add a whereNotNull condition to this relationship
     *
     * ```php
     * ->onConditionNotNull('published_at')
     * ```
     *
     * @param   string  $column  Column name
     * @return  $this
     **/
    public function onConditionNotNull($column)
    {
        $this->defaultConditions[] = function ($query) use ($column) {
            $query->whereIsNotNull($column);
        };

        return $this;
    }

    /**
     * Apply all default conditions to a query
     *
     * Called internally by constrain() and getRelations().
     *
     * @param   object  $query  The query/model to apply conditions to
     * @return  object
     **/
    protected function applyDefaultConditions($query)
    {
        foreach ($this->defaultConditions as $condition) {
            call_user_func($condition, $query);
        }

        return $query;
    }

    /**
     * Check if this relationship has default conditions
     *
     * @return  bool
     **/
    public function hasDefaultConditions()
    {
        return !empty($this->defaultConditions);
    }

    /**
     * Get the number of default conditions
     *
     * @return  int
     **/
    public function getDefaultConditionCount()
    {
        return count($this->defaultConditions);
    }

    /**
     * Clear all default conditions
     *
     * Useful if you need to temporarily bypass the default conditions.
     *
     * @return  $this
     **/
    public function withoutDefaultConditions()
    {
        $this->defaultConditions = [];

        return $this;
    }
}
