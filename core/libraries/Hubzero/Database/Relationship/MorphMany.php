<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Database\Rows;

/**
 * Database morph many relationship (polymorphic one-to-many)
 *
 * This allows one model to have a one-to-many relationship with multiple
 * model types. The related models have type and id columns that point back
 * to different parent types.
 *
 * ## Database Structure
 *
 * The related model's table should have:
 * - `{name}_type` - stores the type identifier (e.g., 'post', 'video')
 * - `{name}_id`   - stores the foreign key to the parent's primary key
 *
 * ## Example
 *
 * ```php
 * // In Post model:
 * class Post extends Relational
 * {
 *     public function comments()
 *     {
 *         return $this->morphMany(Comment::class, 'commentable');
 *     }
 * }
 *
 * // In Video model:
 * class Video extends Relational
 * {
 *     public function comments()
 *     {
 *         return $this->morphMany(Comment::class, 'commentable');
 *     }
 * }
 *
 * // Comments table has: commentable_type, commentable_id columns
 *
 * // Usage:
 * $post = Post::oneOrFail(1);
 * $comments = $post->comments;  // Gets all comments for this post
 * ```
 *
 * ## Type Registration
 *
 * For cleaner type identifiers, register a morph map:
 * ```php
 * Relational::morphMap([
 *     'post'  => Post::class,
 *     'video' => Video::class
 * ]);
 * ```
 */
class MorphMany extends MorphOne
{
    /**
     * Fetch all related models
     *
     * @return \Hubzero\Database\Rows
     */
    public function rows()
    {
        return $this->constrain()->rows();
    }

    /**
     * Associate multiple models with this parent
     *
     * @param  array|\Hubzero\Database\Rows  $models    The models to associate
     * @param  \Closure|null                 $callback  Optional callback
     * @return array|\Hubzero\Database\Rows
     */
    public function associate($models, $callback = null)
    {
        if (is_array($models) || $models instanceof Rows) {
            foreach ($models as $model) {
                parent::associate($model, $callback);
            }
        } else {
            parent::associate($models, $callback);
        }

        return $models;
    }

    /**
     * Save new related models with the given data
     *
     * @param  array  $data  An array of datasets for new models
     * @return bool
     */
    public function save($data)
    {
        if (!is_array($data)) {
            return false;
        }

        // Check if this is an array of arrays (multiple records)
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as $d) {
                if (!parent::save($d)) {
                    return false;
                }
            }
        } else {
            // Single record
            if (!parent::save($data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save all provided models
     *
     * @param  array  $models  The models to associate and save
     * @return bool
     */
    public function saveAll($models)
    {
        foreach ($models as $model) {
            if (!$this->associate($model)->save()) {
                return false;
            }
        }

        return true;
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

        parent::associate($model)->save();

        return $model;
    }

    /**
     * Create many related models
     *
     * @param  array  $records  Array of attribute arrays
     * @return array
     */
    public function createMany(array $records)
    {
        $created = [];

        foreach ($records as $attributes) {
            $created[] = $this->create($attributes);
        }

        return $created;
    }

    /**
     * Delete all related models
     *
     * @return bool
     */
    public function destroyAll()
    {
        foreach ($this->rows() as $model) {
            if (!$model->destroy()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get constrained keys for whereHas queries
     *
     * @param  \Closure  $constraint  The constraint function
     * @return array
     */
    public function getConstrainedKeys($constraint)
    {
        $this->related->select($this->morphId);

        return $this->getConstrained($constraint)->fieldsByKey($this->morphId);
    }

    /**
     * Get the constrained results
     *
     * @param  \Closure  $constraint  The constraint function
     * @return \Hubzero\Database\Rows
     */
    protected function getConstrained($constraint)
    {
        // Apply type constraint first
        $this->related->whereEquals($this->morphType, $this->getTypeIdentifier());

        call_user_func($constraint, $this->related);

        return $this->related->rows();
    }

    /**
     * Get constrained keys by count (for has() queries)
     *
     * @param  int     $count     The minimum count
     * @param  string  $operator  The comparison operator
     * @return array
     */
    public function getConstrainedKeysByCount($count, $operator = '>=')
    {
        $morphId   = $this->morphId;
        $morphType = $this->morphType;
        $type      = $this->getTypeIdentifier();

        return $this->getConstrainedKeys(function ($related) use ($count, $morphId, $morphType, $type, $operator) {
            $related
                ->whereEquals($morphType, $type)
                ->group($morphId)
                ->having('COUNT(*)', $operator, $count);
        });
    }

    /**
     * Seed rows with related models (eager loading)
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

        // Group by morph_id for efficient seeding
        $byId = [];
        foreach ($relations->rows() as $relation) {
            $key = $relation->{$this->morphId};

            if (!isset($byId[$key])) {
                $byId[$key] = new Rows();
            }

            $byId[$key]->push($relation);
        }

        // Seed each row
        foreach ($rows as $row) {
            $key = $row->{$this->localKey};

            if (isset($byId[$key])) {
                $row->addRelationship($name, $byId[$key]);
            } else {
                $row->addRelationship($name, new Rows());
            }
        }

        return $rows;
    }

    /**
     * Load relationship content with provided data
     *
     * @param  \Hubzero\Database\Rows  $rows  The rows to seed
     * @param  mixed                   $data  The data to seed with
     * @param  string                  $name  The relationship name
     * @return \Hubzero\Database\Rows
     */
    public function seedWithData($rows, $data, $name)
    {
        $byId = $this->getResultsByRelatedKey($data);

        return $this->seed($rows, $byId, $name);
    }

    /**
     * Group results by related key
     *
     * @param  \Hubzero\Database\Rows  $relations  The relations to group
     * @return array
     */
    protected function getResultsByRelatedKey($relations)
    {
        $byId = [];

        foreach ($relations as $relation) {
            $key = $relation->{$this->morphId};

            if (!isset($byId[$key])) {
                $byId[$key] = new Rows();
            }

            $byId[$key]->push($relation);
        }

        return $byId;
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
}
