<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Facades\User;

/**
 * Database morph one relationship (polymorphic one-to-one)
 *
 * This allows one model to have a one-to-one relationship with multiple
 * model types. The related model has type and id columns that point back
 * to the parent.
 *
 * ## Database Structure
 *
 * The related model's table should have:
 * - `{name}_type` - stores the type identifier (e.g., 'user', 'post')
 * - `{name}_id`   - stores the foreign key to the parent's primary key
 *
 * ## Example
 *
 * ```php
 * // In User model:
 * class User extends Relational
 * {
 *     public function image()
 *     {
 *         return $this->morphOne(Image::class, 'imageable');
 *     }
 * }
 *
 * // In Post model:
 * class Post extends Relational
 * {
 *     public function image()
 *     {
 *         return $this->morphOne(Image::class, 'imageable');
 *     }
 * }
 *
 * // Images table has: imageable_type, imageable_id columns
 *
 * // Usage:
 * $user = User::oneOrFail(1);
 * $image = $user->image;  // Gets the user's image
 * ```
 */
class MorphOne extends Relationship
{
    /**
     * The name of the polymorphic relationship
     *
     * @var string
     */
    protected $morphName;

    /**
     * The column storing the type identifier
     *
     * @var string
     */
    protected $morphType;

    /**
     * The column storing the foreign key
     *
     * @var string
     */
    protected $morphId;

    /**
     * Constructs a new polymorphic one-to-one relationship
     *
     * @param  \Hubzero\Database\Relational  $model      The parent model
     * @param  \Hubzero\Database\Relational  $related    The related model
     * @param  string                        $morphName  The name of the relationship
     * @param  string|null                   $morphType  The type column name
     * @param  string|null                   $morphId    The id column name
     * @param  string|null                   $localKey   The local key (defaults to primary key)
     */
    public function __construct($model, $related, $morphName, $morphType = null, $morphId = null, $localKey = null)
    {
        $this->model     = $model;
        $this->related   = $related;
        $this->morphName = $morphName;
        $this->morphType = $morphType ?: $morphName . '_type';
        $this->morphId   = $morphId   ?: $morphName . '_id';
        $this->localKey  = $localKey  ?: $model->getPrimaryKey();

        // Related key is the morph id column
        $this->relatedKey = $this->morphId;
    }

    /**
     * Fetch the related model
     *
     * @return \Hubzero\Database\Relational|null
     */
    public function rows()
    {
        return $this->constrain()->row();
    }

    /**
     * Constrain the query to the parent model
     *
     * @return \Hubzero\Database\Relational
     */
    public function constrain()
    {
        $query = $this->related
            ->whereEquals($this->morphId, $this->model->{$this->localKey})
            ->whereEquals($this->morphType, $this->getTypeIdentifier());

        return $this->applyDefaultConditions($query);
    }

    /**
     * Get the type identifier for the parent model
     *
     * @return string
     */
    protected function getTypeIdentifier()
    {
        $morphMap = \Hubzero\Database\Relational::getMorphMap();
        $class    = get_class($this->model);

        // Check if this class is in the morph map
        $type = array_search($class, $morphMap, true);

        if ($type !== false) {
            return $type;
        }

        // Default to lowercase model name
        return strtolower($this->model->getModelName());
    }

    /**
     * Associate a related model with this parent
     *
     * @param  \Hubzero\Database\Relational  $model     The related model
     * @param  \Closure|null                 $callback  Optional callback
     * @return \Hubzero\Database\Relational
     */
    public function associate($model, $callback = null)
    {
        $model->set($this->morphId, $this->model->getPkValue());
        $model->set($this->morphType, $this->getTypeIdentifier());

        if ($callback && is_callable($callback)) {
            call_user_func($callback, $model);
        }

        return $model;
    }

    /**
     * Save a new related model with the given data
     *
     * @param  array  $data  The data for the new model
     * @return bool
     */
    public function save($data)
    {
        $related = $this->related;
        $model   = $related::newFromResults($data);

        return $this->associate($model)->save();
    }

    /**
     * Create and save a new related model
     *
     * @param  array  $attributes  The attributes for the new model
     * @return \Hubzero\Database\Relational
     */
    public function create(array $attributes = [])
    {
        $related = $this->related;
        $model   = $related::blank();

        foreach ($attributes as $key => $value) {
            $model->set($key, $value);
        }

        $this->associate($model)->save();

        return $model;
    }

    /**
     * Get the relations for eager loading
     *
     * @param  array          $keys        The parent keys to fetch for
     * @param  \Closure|null  $constraint  Optional constraint
     * @return \Hubzero\Database\Relational
     */
    protected function getRelations($keys, $constraint = null)
    {
        $this->applyDefaultConditions($this->related);

        if ($constraint) {
            call_user_func($constraint, $this->related);
        }

        return $this->related
            ->whereIn($this->morphId, array_unique($keys))
            ->whereEquals($this->morphType, $this->getTypeIdentifier());
    }

    /**
     * Seed rows with related models
     *
     * @param  \Hubzero\Database\Rows  $rows        The rows to seed
     * @param  string                  $name        The relationship name
     * @param  \Closure|null           $constraint  Optional constraint
     * @param  string|null             $subs        Nested relationships
     * @return \Hubzero\Database\Rows
     */
    public function seedWithRelation($rows, $name, $constraint = null, $subs = null)
    {
        $keys = $rows->fieldsByKey($this->localKey);

        if (!$keys) {
            return $rows;
        }

        $relations = $this->getRelations($keys, $constraint);

        if ($subs) {
            $relations->including($subs);
        }

        // Index by morph_id for quick lookup
        $byId = [];
        foreach ($relations->rows() as $relation) {
            $byId[$relation->{$this->morphId}] = $relation;
        }

        // Seed each row
        foreach ($rows as $row) {
            $key = $row->{$this->localKey};

            if (isset($byId[$key])) {
                $row->addRelationship($name, $byId[$key]);
            } else {
                $related = $this->related;
                $row->addRelationship($name, $related::blank());
            }
        }

        return $rows;
    }

    /**
     * Join the related table
     *
     * @return $this
     */
    public function join()
    {
        $this->model
            ->select($this->model->getQualifiedFieldName('*'))
            ->join(
                $this->related->getTableName(),
                $this->model->getQualifiedFieldName($this->localKey),
                $this->related->getQualifiedFieldName($this->morphId),
                'LEFT OUTER'
            )
            ->whereEquals(
                $this->related->getQualifiedFieldName($this->morphType),
                $this->getTypeIdentifier()
            );

        return $this;
    }

    /**
     * Get the morph type column name
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the morph id column name
     *
     * @return string
     */
    public function getMorphId()
    {
        return $this->morphId;
    }

    /**
     * Get the morph name
     *
     * @return string
     */
    public function getMorphName()
    {
        return $this->morphName;
    }
}
