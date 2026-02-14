<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Database\Rows;

/**
 * Database morph to many relationship (polymorphic many-to-many)
 *
 * This allows many-to-many relationships where one side can be multiple model types.
 * For example, both Posts and Videos can have Tags, using a single pivot table.
 *
 * ## Database Structure
 *
 * The pivot table should have:
 * - `{name}_type` - stores the type identifier (e.g., 'post', 'video')
 * - `{name}_id`   - stores the foreign key to the parent's primary key
 * - `{related}_id` - stores the foreign key to the related model
 *
 * ## Example
 *
 * ```php
 * // In Post model:
 * class Post extends Relational
 * {
 *     public function tags()
 *     {
 *         return $this->morphToMany(Tag::class, 'taggable');
 *         // Uses taggables table with: taggable_type, taggable_id, tag_id
 *     }
 * }
 *
 * // In Video model:
 * class Video extends Relational
 * {
 *     public function tags()
 *     {
 *         return $this->morphToMany(Tag::class, 'taggable');
 *     }
 * }
 *
 * // Usage:
 * $post = Post::oneOrFail(1);
 * $tags = $post->tags;  // Gets all tags for this post
 *
 * // Syncing tags:
 * $post->tags()->sync([1, 2, 3]);
 * ```
 *
 * ## Inverse Relationship (morphedByMany)
 *
 * The Tag model can define the inverse:
 * ```php
 * class Tag extends Relational
 * {
 *     public function posts()
 *     {
 *         return $this->morphedByMany(Post::class, 'taggable');
 *     }
 *
 *     public function videos()
 *     {
 *         return $this->morphedByMany(Video::class, 'taggable');
 *     }
 * }
 * ```
 */
class MorphToMany extends OneToManyThrough
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
     * Whether this is an inverse relationship (morphedByMany)
     *
     * @var bool
     */
    protected $inverse = false;

    /**
     * Constructs a new polymorphic many-to-many relationship
     *
     * @param  \Hubzero\Database\Relational  $model               The parent model
     * @param  \Hubzero\Database\Relational  $related             The related model
     * @param  string                        $morphName           The name of the relationship
     * @param  string|null                   $associativeTable    The pivot table name
     * @param  string|null                   $morphType           The type column name
     * @param  string|null                   $morphId             The id column name
     * @param  string|null                   $relatedKey          The related key on pivot
     * @param  bool                          $inverse             Whether this is inverse
     */
    public function __construct(
        $model,
        $related,
        $morphName,
        $associativeTable = null,
        $morphType = null,
        $morphId = null,
        $relatedKey = null,
        $inverse = false
    ) {
        $this->morphName = $morphName;
        $this->morphType = $morphType ?: $morphName . '_type';
        $this->morphId   = $morphId   ?: $morphName . '_id';
        $this->inverse   = $inverse;

        // Default pivot table name: {morphName}s (e.g., 'taggables')
        $pivotTable = $associativeTable ?: '#__' . $morphName . 's';

        // Default related key
        $relatedKey = $relatedKey ?: strtolower($related->getModelName()) . '_id';

        // For inverse relationships, swap the keys
        if ($inverse) {
            parent::__construct($model, $related, $pivotTable, $relatedKey, $this->morphId);
        } else {
            parent::__construct($model, $related, $pivotTable, $this->morphId, $relatedKey);
        }
    }

    /**
     * Constrain the query to the parent model
     *
     * @return \Hubzero\Database\Relational
     */
    public function constrain()
    {
        $this->mediate();

        if ($this->inverse) {
            // Inverse: filter by related type
            $this->related
                ->whereEquals($this->associativeTable . '.' . $this->morphType, $this->getTypeIdentifier())
                ->whereEquals($this->associativeTable . '.' . $this->associativeLocal, $this->model->getPkValue());
        } else {
            // Normal: filter by parent type
            $this->related
                ->whereEquals($this->associativeTable . '.' . $this->morphType, $this->getTypeIdentifier())
                ->whereEquals($this->associativeTable . '.' . $this->associativeLocal, $this->model->getPkValue());
        }

        return $this->applyDefaultConditions($this->related);
    }

    /**
     * Get the type identifier for the parent model
     *
     * @return string
     */
    protected function getTypeIdentifier()
    {
        $morphMap = \Hubzero\Database\Relational::getMorphMap();

        if ($this->inverse) {
            // For inverse, we want the related model's type
            $class = get_class($this->related);
        } else {
            // For normal, we want the parent model's type
            $class = get_class($this->model);
        }

        $type = array_search($class, $morphMap, true);

        if ($type !== false) {
            return $type;
        }

        if ($this->inverse) {
            return strtolower($this->related->getModelName());
        }

        return strtolower($this->model->getModelName());
    }

    /**
     * Join through the pivot table with type constraint
     *
     * @return $this
     */
    public function mediate()
    {
        parent::mediate();

        // Add the type column to the select
        $this->related->select($this->associativeTable . '.' . $this->morphType);

        return $this;
    }

    /**
     * Connect related models through the pivot table
     *
     * @param  array  $ids  The IDs to connect
     * @return $this
     */
    public function connect($ids)
    {
        if (is_array($ids) && count($ids) > 0) {
            $type = $this->getTypeIdentifier();

            foreach ($ids as $id => $pivot) {
                $id   = is_array($pivot) ? $id : $pivot;
                $data = $this->getConnectionData();
                $data[$this->morphType] = $type;

                if ($this->inverse) {
                    $data[$this->associativeRelated] = $id;
                } else {
                    $data[$this->associativeRelated] = $id;
                }

                if (is_array($pivot)) {
                    $data = array_merge($data, $pivot);
                }

                $this->model->getQuery()->push($this->associativeTable, $data, true);
            }
        }

        return $this;
    }

    /**
     * Get the connection data for the pivot table
     *
     * @return array
     */
    protected function getConnectionData()
    {
        return [$this->associativeLocal => $this->model->getPkValue()];
    }

    /**
     * Disconnect related models from the pivot table
     *
     * @param  array          $ids         The IDs to disconnect
     * @param  \Closure|null  $constraint  Additional constraint
     * @return $this
     */
    public function disconnect($ids, $constraint = null)
    {
        if (is_array($ids) && count($ids) > 0) {
            $query = $this->model->getQuery();
            $query->delete($this->associativeTable)
                ->whereEquals($this->associativeLocal, $this->model->getPkValue())
                ->whereEquals($this->morphType, $this->getTypeIdentifier())
                ->whereIn($this->associativeRelated, $ids);

            if ($constraint && is_callable($constraint)) {
                call_user_func($constraint, $query);
            }

            $query->execute();
        }

        return $this;
    }

    /**
     * Sync related models (add missing, remove extra)
     *
     * @param  array  $ids  The IDs that should be connected
     * @return $this
     */
    public function sync($ids)
    {
        if (is_array($ids)) {
            $query = $this->model->getQuery();
            $type  = $this->getTypeIdentifier();

            // Get existing connections
            $existing = $query->select($this->associativeRelated)
                ->from($this->associativeTable)
                ->whereEquals($this->associativeLocal, $this->model->getPkValue())
                ->whereEquals($this->morphType, $type)
                ->fetch('column');

            // Find IDs to delete
            $deletes = array_diff($existing, $ids);
            if (!empty($deletes)) {
                $this->disconnect($deletes);
            }

            // Find IDs to add
            $inserts = array_diff($ids, $existing);
            $this->connect($inserts);
        }

        return $this;
    }

    /**
     * Get relations for eager loading
     *
     * @param  array          $keys        The parent keys
     * @param  \Closure|null  $constraint  Optional constraint
     * @return \Hubzero\Database\Relational
     */
    protected function getRelations($keys, $constraint = null)
    {
        $this->mediate();

        $this->applyDefaultConditions($this->related);

        if ($constraint) {
            call_user_func($constraint, $this->related);
        }

        return $this->related
            ->whereIn($this->associativeTable . '.' . $this->associativeLocal, array_unique($keys))
            ->whereEquals($this->associativeTable . '.' . $this->morphType, $this->getTypeIdentifier());
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

        $resultsByKey = $this->getResultsByRelatedKey($relations->rows());

        return $this->seed($rows, $resultsByKey, $name);
    }

    /**
     * Group results by the associative local key
     *
     * @param  \Hubzero\Database\Rows  $relations  The relations to group
     * @return array
     */
    protected function getResultsByRelatedKey($relations)
    {
        $byKey = [];

        foreach ($relations as $relation) {
            $key = $relation->{$this->associativeLocal};

            if (!isset($byKey[$key])) {
                $byKey[$key] = new Rows();
            }

            $byKey[$key]->push($relation);
        }

        return $byKey;
    }

    /**
     * Seed rows with data
     *
     * @param  \Hubzero\Database\Rows  $rows  The rows to seed
     * @param  array                   $data  The data indexed by key
     * @param  string                  $name  The relationship name
     * @return \Hubzero\Database\Rows
     */
    protected function seed($rows, $data, $name)
    {
        foreach ($rows as $row) {
            $key = $row->{$this->localKey};

            if (isset($data[$key])) {
                $row->addRelationship($name, $data[$key]);
            } else {
                $row->addRelationship($name, new Rows());
            }
        }

        return $rows;
    }

    /**
     * Join the related table through the pivot
     *
     * @return $this
     */
    public function join()
    {
        $this->model
            ->select($this->model->getQualifiedFieldName('*'))
            ->select($this->related->getQualifiedFieldName('*'))
            ->join(
                $this->associativeTable,
                $this->model->getQualifiedFieldName($this->localKey),
                $this->associativeLocal,
                'LEFT OUTER'
            )
            ->join(
                $this->related->getTableName(),
                $this->associativeRelated,
                $this->related->getQualifiedFieldName($this->relatedKey),
                'LEFT OUTER'
            )
            ->whereEquals(
                $this->associativeTable . '.' . $this->morphType,
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

    /**
     * Check if this is an inverse relationship
     *
     * @return bool
     */
    public function isInverse()
    {
        return $this->inverse;
    }
}
