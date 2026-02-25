<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Iterator;
use Countable;
use Hubzero\Facades\User;

/**
 * Database Iterable class
 */
class Rows implements Iterator, Countable
{
    /*
     * Errors trait for error handling
     **/
    use Traits\ErrorBag;

    public function __construct($rows = array())
    {
        // While arrays are traversable with foreach,
        // they will not return true as an instance of Traverable
        if (is_array($rows) || $rows instanceof \Traversable) {
            foreach ($rows as $row) {
                $this->push($row);
            }
        }
    }

    /**
     * Internal array of iterable data
     *
     * @public array
     **/
    private $rows = array();

    /**
     * Order by used to retrieve these rows
     *
     * @public string
     **/
    public $orderBy = 'id';

    /**
     * Order direction used to retrieve these rows
     *
     * @public string
     **/
    public $orderDir = 'asc';

    /**
     * The pagination object based on these rows
     *
     * @public \Hubzero\Database\Pagination
     **/
    public $pagination = null;

    /**
     * Calls the given array function on the rows object and attaches itself to the model
     *
     * @param   string $function
     * @return  mixed
     **/
    private function callArrayFunc($function)
    {
        $row = $function($this->rows);

        return ($row) ? $row->setIterator($this) : $row;
    }

    /**
     * Pushes a new model on to the stack
     *
     * @param   \Hubzero\Database\Relational|static  $model  The model to add
     * @return  void
     **/
    public function push(Relational $model)
    {
        // Index by primary key if possible, otherwise plain incremental array
        // Also check to see if that key already exists.  If so, we'll just start
        // appending items to the array.  This will result in a mixed array and
        // subsequent items will not be seekable.
        if ($model->getPkValue() && (!is_array($this->rows) || !array_key_exists($model->getPkValue(), $this->rows))) {
            $this->rows[$model->getPkValue()] = $model;
        } else {
            $this->rows[] = $model;
        }
    }

    /**
     * Removes model from the stack
     *
     * @param   int $key
     * @return  void
     **/
    public function drop($key)
    {
        unset($this->rows[$key]);
    }

    /**
     * Clears out any existing rows
     *
     * @return  void
     **/
    public function clear()
    {
        $this->rows = array();
    }

    /**
     * Selects a number of randomly selected rows
     *
     * @param   int  $n  The number of rows to randomly select
     * @return  Rows
     **/
    public function pickRandom($n)
    {
        $rows = $this->rows;

        shuffle($rows);
        $randomRows = array_slice($rows, 0, $n);
        $rowsObject = new self($randomRows);

        return $rowsObject;
    }

    /**
     * Transforms rows into a JSON array
     *
     * @return  array
     **/
    public function toJson()
    {
        return $this->to('json');
    }

    /**
     * Transforms rows into an object array
     *
     * @return  array
     **/
    public function toObject()
    {
        return $this->to('object');
    }

    /**
     * Transforms rows into an array of arrays
     *
     * @return  array
     **/
    public function toArray()
    {
        return $this->to();
    }

    /**
     * Outputs rows as given type
     *
     * @return  array
     **/
    public function to($type = 'array')
    {
        $rows = [];

        if ($this->rows && $this->count()) {
            foreach ($this->rows as $row) {
                $method = 'to' . ucfirst($type);
                $rows[] = $row->$method();
            }
        }

        return $rows;
    }

    /**
     * Grabs the raw rows out of the iterator
     *
     * @return  array
     **/
    public function raw()
    {
        return $this->rows;
    }

    /**
     * Gets current row in array of rows
     *
     * @return  mixed
     **/

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->callArrayFunc('current');
    }

    /**
     * Gets the current key
     *
     * @return  mixed
     **/

    #[\ReturnTypeWillChange]
    public function key()
    {
        if (isset($this->rows)) {
            return key($this->rows);
        }
        return null;
    }

    /**
     * Returns the result keys for the current dataset
     *
     * @param   string  $key  The key for which to pull all values
     * @return  array
     **/
    public function fieldsByKey($key)
    {
        $keys = array();

        if ($this->rows && $this->count()) {
            foreach ($this->rows as $row) {
                $keys[] = $row->$key;
            }
        }

        return $keys;
    }

    /**
     * Gets first item from rows property, if set
     *
     * @return  mixed
     **/
    public function first()
    {
        return $this->callArrayFunc('reset');
    }

    /**
     * Gets previous item in iterable list
     *
     * @return  mixed
     **/
    public function prev()
    {
        return $this->callArrayFunc('prev');
    }

    /**
     * Gets next item in iterable list
     *
     * @return  mixed
     **/

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->callArrayFunc('next');
    }

    /**
     * Rewinds rows back to start
     *
     * @return  void
     **/

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        if (isset($this->rows)) {
            reset($this->rows);
        }
    }

    /**
     * Fast-forwards to the end of the iterable list
     *
     * @return  void
     **/
    public function last()
    {
        return $this->callArrayFunc('end');
    }

    /**
     * Checks to see if the current item is the first in the list
     *
     * @param   int  $key  The key to check against
     * @return  bool
     **/
    public function isFirst($key)
    {
        if ($this->rows && $this->count()) {
            return $key == array_slice($this->rows, 0, 1)[0]->getPkValue();
        }

        return false;
    }

    /**
     * Checks to see if the current item is the last in the list
     *
     * @param   int  $key  The key to check against
     * @return  bool
     **/
    public function isLast($key)
    {
        if ($this->rows && $this->count()) {
            return $key == array_slice($this->rows, -1, 1)[0]->getPkValue();
        }

        return false;
    }

    /**
     * Validates current key
     *
     * @return  bool
     **/

    #[\ReturnTypeWillChange]
    public function valid()
    {
        $valid = false;

        if ($this->rows && $this->count()) {
            $key   = key($this->rows);
            $valid = ($key !== null && $key !== false);
        }

        return $valid;
    }

    /**
     * Counts the number of rows
     *
     * @return  int  number of rows
     **/

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Seeks to the given key
     *
     * @param   mixed  $offset
     * @return  mixed
     **/
    public function seek($offset)
    {
        return isset($this->rows[$offset]) ? $this->rows[$offset] : false;
    }

    /**
     * Search for the given key/value pair, returning false if not found
     *
     * @param   mixed $key
     * @param   mixed $value
     * @return  mixed
     **/
    public function search($key, $value)
    {
        foreach ($this->rows as $row) {
            if ($row->$key == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sorts the rows by a given field
     *
     * @param   string  $field  The field to sort by
     * @param   bool    $asc    True if sort direction is ascending, false for descending
     * @return  $this
     **/
    public function sort($field, $asc = true)
    {
        usort($this->rows, function ($a, $b) use ($field, $asc) {
            $result = strcmp($a->$field, $b->$field);

            return ($asc) ? $result : $result * -1;
        });

        return $this;
    }

    /**
     * Retrieves only the most recent applicable row
     *
     * @param   string  $limiter  The column name to use to determine the latest row
     * @return  \Hubzero\Database\Relational|static
     **/
    public function latest($limiter = 'created')
    {
        return $this->sort($limiter, false)->first();
    }

    /**
     * Saves a collection of models
     *
     * @return  bool
     **/
    public function save()
    {
        if ($this->count()) {
            foreach ($this->rows as $model) {
                if (!$model->save()) {
                    $this->setErrors($model->getErrors());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Deletes all models in this collection
     *
     * @return  bool
     **/
    public function destroyAll()
    {
        // @FIXME: could make this a single query...
        if ($this->count()) {
            foreach ($this->rows as $model) {
                if (!$model->destroy()) {
                    $this->setErrors($model->getErrors());
                    return false;
                }
            }
        }

        return true;
    }

    // =========================================================================
    // Lazy Eager Loading
    // =========================================================================
    //
    // These methods allow loading relationships on an existing collection of
    // models, after they've been retrieved from the database. This is useful
    // when models come from a cache, another method, or when you need to
    // conditionally load relationships based on runtime logic.
    //
    // The loading uses the same efficient bulk-loading mechanism as with(),
    // executing just one query per relationship regardless of collection size.
    //
    // Example:
    // ```php
    // $users = User::all()->rows();  // Collection with no relationships
    //
    // // Conditionally load relationships
    // if ($needsPosts) {
    //     $users->load('posts.comments');  // Bulk loads for ALL users
    // }
    //
    // // Load multiple relationships
    // $users->load('profile', 'settings', 'roles');
    //
    // // Only load if not already loaded
    // $users->loadMissing('profile');
    // ```
    // =========================================================================

    /**
     * Lazy eager load relationships on all models in this collection
     *
     * Loads relationships AFTER the models have been retrieved from the database.
     * Uses the same efficient bulk-loading mechanism as `with()`, executing just
     * one query per relationship regardless of how many models are in the collection.
     *
     * **Simple usage:**
     * ```php
     * $users = User::all()->rows();
     * $users->load('posts', 'profile');
     * // All users now have posts and profile loaded
     * ```
     *
     * **Nested relationships:**
     * ```php
     * $users->load('posts.comments.author');
     * ```
     *
     * **With constraints (closure):**
     * ```php
     * $users->load(['posts', function($query) {
     *     $query->whereEquals('published', 1);
     * }]);
     * ```
     *
     * **With constraints (array - no closures):**
     * ```php
     * $users->load([
     *     'posts' => [
     *         'conditions' => ['published' => 1],
     *         'order' => ['created' => 'desc'],
     *         'limit' => 5
     *     ]
     * ]);
     * ```
     *
     * @param   mixed  ...$relations  Relationship names to load (same format as with())
     * @return  self
     **/
    public function load()
    {
        $relations = func_get_args();

        if (empty($relations) || $this->count() === 0) {
            return $this;
        }

        // Get the first model to use for calling relationship methods
        // All models in the collection should be the same type
        $model = $this->first();

        if (!$model) {
            return $this;
        }

        // Process each relationship through seedWithRelation
        foreach ($relations as $relation) {
            $this->loadRelation($model, $relation);
        }

        return $this;
    }

    /**
     * Lazy eager load relationships that haven't been loaded yet
     *
     * Same as `load()` but only loads relationships that aren't already
     * present on ALL models in the collection. This is useful when you want
     * to ensure a relationship is available without re-loading it.
     *
     * ```php
     * // Only loads profile if not already loaded
     * $users->loadMissing('profile');
     *
     * // Load multiple, skipping any already loaded
     * $users->loadMissing('posts', 'comments', 'profile');
     * ```
     *
     * @param   mixed  ...$relations  Relationship names to load if missing
     * @return  self
     **/
    public function loadMissing()
    {
        $relations = func_get_args();

        if (empty($relations) || $this->count() === 0) {
            return $this;
        }

        // Get the first model to check for loaded relationships
        $model = $this->first();

        if (!$model) {
            return $this;
        }

        // Filter to only relations that aren't already loaded
        $toLoad = [];
        foreach ($relations as $relation) {
            $relationName = $this->extractRelationName($relation);

            // Check if ANY model in the collection is missing this relationship
            // For simplicity, we check the first model - if it's loaded there,
            // we assume it's loaded on all (which is true if load() was used)
            if ($model->getRelationship($relationName) === null) {
                $toLoad[] = $relation;
            }
        }

        if (!empty($toLoad)) {
            call_user_func_array([$this, 'load'], $toLoad);
        }

        return $this;
    }

    /**
     * Load a single relation definition on this collection
     *
     * @param   Relational  $model     A model instance to call relationship methods on
     * @param   mixed       $relation  The relation definition
     * @return  void
     **/
    private function loadRelation(Relational $model, $relation)
    {
        $subs = null;
        $constraint = null;

        // Check if we have an associative array (new format)
        if (is_array($relation) && $this->isAssociativeArray($relation)) {
            $this->loadAssociativeRelation($model, $relation);
            return;
        }

        // Existing behavior: simple string or [name, closure] array
        $relationship = $relation;

        // Check for array with constraint
        if (is_array($relationship)) {
            list($relationship, $constraint) = $relationship;
        }

        // Parse for nested relationships
        if (strpos($relationship, '.')) {
            list($relationship, $subs) = explode('.', $relationship, 2);
        }

        // If we have subs and a constraint, the constraint applies to the subs
        if (isset($subs) && isset($constraint)) {
            $subs = [$subs, $constraint];
            $constraint = null;
        }

        // Load the relationship using seedWithRelation
        // seedWithRelation modifies the models in the Rows directly
        $model->$relationship()->seedWithRelation($this, $relationship, $constraint, $subs);
    }

    /**
     * Load associative relation definitions on this collection
     *
     * @param   Relational  $model     A model instance
     * @param   array       $includes  Associative array of relation => config
     * @return  void
     **/
    private function loadAssociativeRelation(Relational $model, array $includes)
    {
        foreach ($includes as $relationName => $config) {
            $subs = null;
            $constraint = null;

            // Numeric key with string value: simple relation name
            if (is_numeric($relationName)) {
                $relationName = $config;
                $config = null;
            }

            // Config is an array with constraints
            if (is_array($config)) {
                $constraint = $model->buildEagerConstraint($config);
            } elseif ($config instanceof \Closure) {
                $constraint = $config;
            }

            // Parse for nested relationships
            if (strpos($relationName, '.')) {
                list($relationName, $subs) = explode('.', $relationName, 2);
            }

            // If we have subs and a constraint, the constraint applies to the subs
            if (isset($subs) && isset($constraint)) {
                $subs = [$subs, $constraint];
                $constraint = null;
            }

            // Load the relationship
            $model->$relationName()->seedWithRelation($this, $relationName, $constraint, $subs);
        }
    }

    /**
     * Extract the base relationship name from various formats
     *
     * @param   mixed   $relation  The relation in various formats
     * @return  string  The base relationship name
     **/
    private function extractRelationName($relation)
    {
        // Simple string
        if (is_string($relation)) {
            // Handle nested: 'posts.comments' -> 'posts'
            if (strpos($relation, '.') !== false) {
                return explode('.', $relation, 2)[0];
            }
            return $relation;
        }

        // Array format
        if (is_array($relation)) {
            // Associative array: ['posts' => [...]]
            if ($this->isAssociativeArray($relation)) {
                $keys = array_keys($relation);
                foreach ($keys as $key) {
                    if (is_string($key)) {
                        return strpos($key, '.') !== false
                            ? explode('.', $key, 2)[0]
                            : $key;
                    }
                }
                // Fallback
                $first = reset($relation);
                if (is_string($first)) {
                    return strpos($first, '.') !== false
                        ? explode('.', $first, 2)[0]
                        : $first;
                }
            }

            // Indexed array: ['posts', fn() => ...]
            if (isset($relation[0]) && is_string($relation[0])) {
                $name = $relation[0];
                return strpos($name, '.') !== false
                    ? explode('.', $name, 2)[0]
                    : $name;
            }
        }

        return (string) $relation;
    }

    /**
     * Checks if an array is associative (has string keys)
     *
     * @param   array  $array  The array to check
     * @return  bool
     **/
    private function isAssociativeArray($array)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }
}
