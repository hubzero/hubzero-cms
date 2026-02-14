<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

use Hubzero\Database\Factory;

/**
 * UUID trait for Relational models
 *
 * This trait provides UUID support for models, including automatic generation
 * on creation and convenient lookup methods. UUIDs (Universally Unique Identifiers)
 * are useful for:
 * - Public-facing identifiers (hiding auto-increment IDs)
 * - Distributed systems where IDs need to be generated independently
 * - API resources that need stable, portable identifiers
 * - Cross-database record synchronization
 *
 * ## Usage
 *
 * Add the trait to your model and ensure your table has a UUID column:
 *
 * ```php
 * class Article extends Relational
 * {
 *     use \Hubzero\Database\Traits\HasUuid;
 *
 *     protected $table = '#__articles';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Your table needs a UUID column (default name: `uuid`):
 *
 * ```php
 * // Using SchemaBuilder
 * $schema->addColumn('#__articles', 'uuid')->uuid()->unique();
 *
 * // Or using TableBuilder
 * $builder->uuid('uuid')->unique();
 * ```
 *
 * ### Custom Column Name
 *
 * Override the column name in your model if needed:
 *
 * ```php
 * protected $uuidColumn = 'public_id';
 * ```
 *
 * ### UUID as Primary Key
 *
 * To use UUID as the primary key instead of auto-increment:
 *
 * ```php
 * class Token extends Relational
 * {
 *     use \Hubzero\Database\Traits\HasUuid;
 *
 *     protected $pk = 'uuid';
 *     protected $uuidAsPrimaryKey = true;
 * }
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // UUID is auto-generated on save
 * $article = new Article(['title' => 'My Article']);
 * $article->save();
 * echo $article->uuid;  // "550e8400-e29b-41d4-a716-446655440000"
 *
 * // Find by UUID
 * $article = Article::findByUuid('550e8400-e29b-41d4-a716-446655440000');
 *
 * // Find by UUID or fail
 * $article = Article::findByUuidOrFail('550e8400-e29b-41d4-a716-446655440000');
 *
 * // Check if model has UUID
 * $article->hasUuid();  // true
 *
 * // Manually generate a new UUID
 * $uuid = Article::generateUuid();
 * ```
 *
 * ## Querying by UUID
 *
 * ```php
 * // Via scope
 * $article = Article::whereUuid('550e8400-...')->row();
 *
 * // Multiple UUIDs
 * $articles = Article::whereUuidIn(['uuid1', 'uuid2'])->rows();
 * ```
 */
trait HasUuid
{
    /**
     * Boot the UUID trait for a model
     *
     * This is called automatically when the model boots. It registers
     * a creating event to auto-generate the UUID.
     *
     * @return  void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $column = $model->getUuidColumn();

            // Only generate if not already set
            if (!$model->get($column)) {
                $model->set($column, static::generateUuid());
            }
        });
    }

    /**
     * Get the name of the UUID column
     *
     * @return  string
     */
    public function getUuidColumn(): string
    {
        return defined('static::UUID_COLUMN')
            ? static::UUID_COLUMN
            : (property_exists($this, 'uuidColumn') ? $this->uuidColumn : 'uuid');
    }

    /**
     * Get the fully qualified UUID column name
     *
     * @return  string
     */
    public function getQualifiedUuidColumn(): string
    {
        return $this->getTableName() . '.' . $this->getUuidColumn();
    }

    /**
     * Determine if this model uses UUID as the primary key
     *
     * @return  bool
     */
    public function usesUuidAsPrimaryKey(): bool
    {
        return property_exists($this, 'uuidAsPrimaryKey') && $this->uuidAsPrimaryKey === true;
    }

    /**
     * Get the UUID value for the model
     *
     * @return  string|null
     */
    public function getUuid(): ?string
    {
        return $this->get($this->getUuidColumn());
    }

    /**
     * Check if the model has a UUID set
     *
     * @return  bool
     */
    public function hasUuid(): bool
    {
        $uuid = $this->getUuid();
        return !empty($uuid);
    }

    /**
     * Generate a new UUID v4
     *
     * @return  string
     */
    public static function generateUuid(): string
    {
        return Factory::uuid();
    }

    /**
     * Find a model by its UUID
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   string|null  $uuid  The UUID to search for
     * @return  static|null
     */
    public static function findByUuid(?string $uuid = null)
    {
        // Handle introspection calls (no argument provided)
        if ($uuid === null) {
            return null;
        }

        $instance = new static();
        $column = $instance->getUuidColumn();

        $result = $instance->whereEquals($column, $uuid)->row();

        // Return null if not found (row() returns blank model)
        if (!$result || !$result->get($instance->getPrimaryKey())) {
            return null;
        }

        return $result;
    }

    /**
     * Find a model by its UUID or throw an exception
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   string|null  $uuid  The UUID to search for
     * @return  static
     * @throws  \RuntimeException  If model not found or uuid not provided
     */
    public static function findByUuidOrFail(?string $uuid = null)
    {
        // Handle introspection calls (no argument provided)
        if ($uuid === null) {
            throw new \RuntimeException("UUID is required");
        }

        $model = static::findByUuid($uuid);

        if ($model === null) {
            $className = static::class;
            throw new \RuntimeException(
                "No model [{$className}] found for UUID [{$uuid}]"
            );
        }

        return $model;
    }

    /**
     * Find multiple models by their UUIDs
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   array|null  $uuids  Array of UUIDs to search for
     * @return  \Hubzero\Database\Rows
     */
    public static function findManyByUuid(?array $uuids = null)
    {
        if ($uuids === null || empty($uuids)) {
            return new \Hubzero\Database\Rows();
        }

        $instance = new static();
        $column = $instance->getUuidColumn();

        return $instance->whereIn($column, $uuids)->rows();
    }

    /**
     * Scope query to a specific UUID
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   string|null  $uuid  The UUID to filter by
     * @return  $this
     */
    public function whereUuid(?string $uuid = null)
    {
        if ($uuid === null) {
            return $this;
        }
        return $this->whereEquals($this->getUuidColumn(), $uuid);
    }

    /**
     * Scope query to multiple UUIDs
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   array|null  $uuids  Array of UUIDs to filter by
     * @return  $this
     */
    public function whereUuidIn(?array $uuids = null)
    {
        if ($uuids === null) {
            return $this;
        }
        return $this->whereIn($this->getUuidColumn(), $uuids);
    }

    /**
     * Scope query to exclude specific UUIDs
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   array|null  $uuids  Array of UUIDs to exclude
     * @return  $this
     */
    public function whereUuidNotIn(?array $uuids = null)
    {
        if ($uuids === null) {
            return $this;
        }
        return $this->whereNotIn($this->getUuidColumn(), $uuids);
    }

    /**
     * Regenerate the UUID for this model
     *
     * This creates a new UUID but does not save the model.
     *
     * @return  string  The new UUID
     */
    public function regenerateUuid(): string
    {
        $uuid = static::generateUuid();
        $this->set($this->getUuidColumn(), $uuid);
        return $uuid;
    }

    /**
     * Get the route key value for the model (useful for route model binding)
     *
     * When using UUID as the route key, this returns the UUID.
     * Override getRouteKeyName() to use UUID for route binding.
     *
     * @return  string|null
     */
    public function getRouteKeyValue()
    {
        if ($this->getRouteKeyName() === $this->getUuidColumn()) {
            return $this->getUuid();
        }

        return $this->get($this->getPrimaryKey());
    }

    /**
     * Get the route key name for route model binding
     *
     * Override this in your model to use UUID for route binding:
     *
     * ```php
     * public function getRouteKeyName(): string
     * {
     *     return $this->getUuidColumn();
     * }
     * ```
     *
     * @return  string
     */
    public function getRouteKeyName(): string
    {
        return $this->getPrimaryKey();
    }

    /**
     * Validate that a string is a valid UUID v4 format
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   string|null  $uuid  The string to validate
     * @return  bool
     */
    public static function isValidUuid(?string $uuid = null): bool
    {
        if ($uuid === null) {
            return false;
        }

        // UUID v4 format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
        // where y is one of 8, 9, a, or b
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        return (bool) preg_match($pattern, $uuid);
    }

    /**
     * Check if a UUID already exists in the database
     *
     * Note: The default value allows this method to be called during
     * relationship introspection without causing ArgumentCountError.
     *
     * @param   string|null  $uuid  The UUID to check
     * @param   int|null     $excludeId  Optional ID to exclude (for updates)
     * @return  bool
     */
    public static function uuidExists(?string $uuid = null, $excludeId = null): bool
    {
        if ($uuid === null) {
            return false;
        }

        $instance = new static();
        $column = $instance->getUuidColumn();

        $query = $instance->whereEquals($column, $uuid);

        if ($excludeId !== null) {
            $query->where($instance->getPrimaryKey(), '!=', $excludeId);
        }

        return $query->total() > 0;
    }
}
