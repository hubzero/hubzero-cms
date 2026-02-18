<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Runtime relationship registry for Relational models.
 *
 * Stores dynamic relationship callbacks keyed by model class and relationship
 * name so runtime registrations are isolated per model class.
 */
class RelationshipRegistry
{
    /**
     * Registered relationships keyed by model class then relationship name.
     *
     * @var array<string,array<string,callable>>
     */
    private array $relationshipsByClass = [];

    /**
     * Register a runtime relationship callback for a model class.
     *
     * @param   string    $modelClass
     * @param   string    $name
     * @param   callable  $resolver
     * @return  void
     */
    public function register(string $modelClass, string $name, callable $resolver): void
    {
        if (!isset($this->relationshipsByClass[$modelClass])) {
            $this->relationshipsByClass[$modelClass] = [];
        }

        $this->relationshipsByClass[$modelClass][$name] = $resolver;
    }

    /**
     * Determine whether a runtime relationship exists for model class.
     *
     * @param   string  $modelClass
     * @param   string  $name
     * @return  bool
     */
    public function has(string $modelClass, string $name): bool
    {
        return isset($this->relationshipsByClass[$modelClass][$name]);
    }

    /**
     * Get a runtime relationship callback for model class.
     *
     * @param   string  $modelClass
     * @param   string  $name
     * @return  callable|null
     */
    public function get(string $modelClass, string $name): ?callable
    {
        return $this->relationshipsByClass[$modelClass][$name] ?? null;
    }

    /**
     * Get all runtime relationships for model class.
     *
     * @param   string  $modelClass
     * @return  array<string,callable>
     */
    public function all(string $modelClass): array
    {
        return $this->relationshipsByClass[$modelClass] ?? [];
    }

    /**
     * Clear runtime relationships.
     *
     * @param   string|null  $modelClass  Optional class to clear; null clears all.
     * @return  void
     */
    public function clear(?string $modelClass = null): void
    {
        if ($modelClass === null) {
            $this->relationshipsByClass = [];
            return;
        }

        unset($this->relationshipsByClass[$modelClass]);
    }
}
