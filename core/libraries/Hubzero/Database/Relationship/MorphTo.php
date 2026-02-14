<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

/**
 * Database morph to relationship (polymorphic belongs-to)
 *
 * This is the inverse of MorphOne/MorphMany. It allows a model to belong to
 * multiple other model types using a type column to identify which parent class
 * to use.
 *
 * ## Database Structure
 *
 * The child model's table should have two columns:
 * - `{name}_type` - stores the type identifier (e.g., 'post', 'video', 'photo')
 * - `{name}_id`   - stores the foreign key to the parent's primary key
 *
 * ## Example
 *
 * ```php
 * // In Comment model:
 * class Comment extends Relational
 * {
 *     public function commentable()
 *     {
 *         return $this->morphTo('commentable');
 *     }
 * }
 *
 * // Usage:
 * $comment = Comment::oneOrFail(1);
 * $parent = $comment->commentable;  // Returns Post, Video, or Photo model
 * ```
 *
 * ## Type Resolution
 *
 * By default, the type column stores a string identifier that maps to a class:
 * - 'post'  => Components\Blog\Models\Post
 * - 'video' => Components\Media\Models\Video
 *
 * Register mappings using the static morphMap:
 * ```php
 * Relational::morphMap([
 *     'post'  => Post::class,
 *     'video' => Video::class
 * ]);
 * ```
 */
class MorphTo extends Relationship
{
    /**
     * The name of the polymorphic relationship
     *
     * Used to derive the type and id column names.
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
     * The cached related model instance
     *
     * @var \Hubzero\Database\Relational|null
     */
    protected $cachedRelated = null;

    /**
     * Constructs a new polymorphic belongs-to relationship
     *
     * @param  \Hubzero\Database\Relational  $model      The model with the morph columns
     * @param  string                        $morphName  The name of the relationship
     * @param  string|null                   $morphType  The type column name (defaults to {name}_type)
     * @param  string|null                   $morphId    The id column name (defaults to {name}_id)
     */
    public function __construct($model, $morphName, $morphType = null, $morphId = null)
    {
        $this->model     = $model;
        $this->morphName = $morphName;
        $this->morphType = $morphType ?: $morphName . '_type';
        $this->morphId   = $morphId   ?: $morphName . '_id';

        // Related is resolved dynamically based on type column
        $this->related    = null;
        $this->localKey   = $this->morphId;
        $this->relatedKey = 'id';
    }

    /**
     * Fetch the related model (the polymorphic parent)
     *
     * @return \Hubzero\Database\Relational|null
     */
    public function rows()
    {
        if ($this->cachedRelated !== null) {
            return $this->cachedRelated;
        }

        $typeValue = $this->model->{$this->morphType};
        $idValue   = $this->model->{$this->morphId};

        if (empty($typeValue) || empty($idValue)) {
            return null;
        }

        $relatedClass = $this->resolveTypeToClass($typeValue);

        if (!$relatedClass) {
            return null;
        }

        $this->cachedRelated = $relatedClass::one($idValue);

        return $this->cachedRelated;
    }

    /**
     * Alias for rows() - returns single parent model
     *
     * @return \Hubzero\Database\Relational|null
     */
    public function row()
    {
        return $this->rows();
    }

    /**
     * Constrain query to fetch the related model
     *
     * @return \Hubzero\Database\Relational|null
     */
    public function constrain()
    {
        $typeValue = $this->model->{$this->morphType};
        $idValue   = $this->model->{$this->morphId};

        if (empty($typeValue) || empty($idValue)) {
            return null;
        }

        $relatedClass = $this->resolveTypeToClass($typeValue);

        if (!$relatedClass) {
            return null;
        }

        return $relatedClass::whereEquals('id', $idValue);
    }

    /**
     * Resolve a type identifier to its class name
     *
     * Checks the morph map first, then tries to use the type as a class name directly.
     *
     * @param  string  $type  The type identifier
     * @return string|null    The fully qualified class name, or null if not found
     */
    protected function resolveTypeToClass($type)
    {
        // Check the morph map first
        $morphMap = \Hubzero\Database\Relational::getMorphMap();

        if (isset($morphMap[$type])) {
            return $morphMap[$type];
        }

        // If type looks like a class name, use it directly
        if (class_exists($type)) {
            return $type;
        }

        // Try common namespace patterns
        $commonPatterns = [
            'Components\\' . ucfirst($type) . '\\Models\\' . ucfirst($type),
            'Hubzero\\' . ucfirst($type) . '\\' . ucfirst($type),
        ];

        foreach ($commonPatterns as $pattern) {
            if (class_exists($pattern)) {
                return $pattern;
            }
        }

        return null;
    }

    /**
     * Associate this model with a parent
     *
     * @param  \Hubzero\Database\Relational  $model     The parent model to associate
     * @param  \Closure|null                 $callback  Optional callback (unused for morphTo)
     * @return $this
     */
    public function associate($model, $callback = null)
    {
        $this->model->set($this->morphType, $this->resolveClassToType($model));
        $this->model->set($this->morphId, $model->getPkValue());

        $this->cachedRelated = $model;

        return $this;
    }

    /**
     * Dissociate from the current parent
     *
     * @return $this
     */
    public function dissociate()
    {
        $this->model->set($this->morphType, null);
        $this->model->set($this->morphId, null);

        $this->cachedRelated = null;

        return $this;
    }

    /**
     * Resolve a model class to its type identifier
     *
     * Checks the morph map for a matching class, otherwise uses the model name.
     *
     * @param  \Hubzero\Database\Relational  $model  The model to resolve
     * @return string
     */
    protected function resolveClassToType($model)
    {
        $morphMap = \Hubzero\Database\Relational::getMorphMap();
        $class    = get_class($model);

        // Check if this class is in the morph map
        $type = array_search($class, $morphMap, true);

        if ($type !== false) {
            return $type;
        }

        // Default to lowercase model name
        return strtolower($model->getModelName());
    }

    /**
     * Get the type column name
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the id column name
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
     * Seed rows with related polymorphic parents
     *
     * This handles eager loading of polymorphic parents, grouping by type
     * for efficient querying.
     *
     * @param  \Hubzero\Database\Rows  $rows        The rows to seed
     * @param  string                  $name        The relationship name
     * @param  \Closure|null           $constraint  Optional constraint
     * @param  string|null             $subs        Nested relationships
     * @return \Hubzero\Database\Rows
     */
    public function seedWithRelation($rows, $name, $constraint = null, $subs = null)
    {
        // Group rows by type
        $byType = [];

        foreach ($rows as $row) {
            $type = $row->{$this->morphType};
            $id   = $row->{$this->morphId};

            if ($type && $id) {
                if (!isset($byType[$type])) {
                    $byType[$type] = [];
                }
                $byType[$type][$id] = true;
            }
        }

        // Fetch related models by type
        $related = [];

        foreach ($byType as $type => $ids) {
            $class = $this->resolveTypeToClass($type);

            if (!$class) {
                continue;
            }

            $query = $class::whereIn('id', array_keys($ids));

            if ($constraint) {
                call_user_func($constraint, $query);
            }

            foreach ($query->rows() as $model) {
                $related[$type . ':' . $model->getPkValue()] = $model;
            }
        }

        // Seed the rows
        foreach ($rows as $row) {
            $type = $row->{$this->morphType};
            $id   = $row->{$this->morphId};
            $key  = $type . ':' . $id;

            if (isset($related[$key])) {
                $parent = $related[$key];

                if ($subs) {
                    $parent->including($subs);
                }

                $row->addRelationship($name, $parent);
            } else {
                $row->addRelationship($name, null);
            }
        }

        return $rows;
    }

    /**
     * Join is not supported for polymorphic relationships
     *
     * @throws \RuntimeException
     */
    public function join()
    {
        throw new \RuntimeException(
            'Join is not supported for polymorphic MorphTo relationships. ' .
            'The parent table cannot be determined at query time.'
        );
    }
}
