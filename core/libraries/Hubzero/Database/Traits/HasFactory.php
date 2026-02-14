<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

use Hubzero\Database\Factory;

/**
 * HasFactory trait for Relational models
 *
 * This trait provides factory support to models, allowing easy creation
 * of test data. Add this trait to any model that needs factory support.
 *
 * ## Usage
 *
 * 1. Create a factory class for your model:
 *
 * ```php
 * namespace Components\Blog\Models;
 *
 * use Hubzero\Database\Factory;
 *
 * class EntryFactory extends Factory
 * {
 *     protected $model = Entry::class;
 *
 *     public function definition()
 *     {
 *         return [
 *             'title' => 'Blog Entry ' . self::sequence(),
 *             'content' => self::paragraph(),
 *             'state' => 1,
 *             'created_by' => 1,
 *         ];
 *     }
 *
 *     public function published()
 *     {
 *         return $this->state(['state' => 1, 'publish_up' => self::pastDate()]);
 *     }
 *
 *     public function draft()
 *     {
 *         return $this->state(['state' => 0]);
 *     }
 * }
 * ```
 *
 * 2. Add the trait to your model and implement `newFactory()`:
 *
 * ```php
 * namespace Components\Blog\Models;
 *
 * use Hubzero\Database\Relational;
 * use Hubzero\Database\Traits\HasFactory;
 *
 * class Entry extends Relational
 * {
 *     use HasFactory;
 *
 *     protected static function newFactory()
 *     {
 *         return EntryFactory::new();
 *     }
 * }
 * ```
 *
 * 3. Use the factory in your tests:
 *
 * ```php
 * // Create a single entry
 * $entry = Entry::factory()->create();
 *
 * // Create multiple entries
 * $entries = Entry::factory()->count(5)->create();
 *
 * // Use a state
 * $draft = Entry::factory()->draft()->create();
 *
 * // Override attributes
 * $custom = Entry::factory()->create(['title' => 'Custom Title']);
 * ```
 *
 * ## Inline Factory (No Separate Class)
 *
 * For simpler cases, you can define the factory inline:
 *
 * ```php
 * class SimpleModel extends Relational
 * {
 *     use HasFactory;
 *
 *     protected static function newFactory()
 *     {
 *         return new class extends Factory {
 *             protected $model = SimpleModel::class;
 *
 *             public function definition()
 *             {
 *                 return [
 *                     'name' => 'Item ' . self::sequence(),
 *                     'active' => true,
 *                 ];
 *             }
 *         };
 *     }
 * }
 * ```
 */
trait HasFactory
{
    /**
     * Get a new factory instance for the model
     *
     * @return Factory
     */
    public static function factory()
    {
        return static::newFactory();
    }

    /**
     * Create a new factory instance for the model
     *
     * Override this method to return your model's factory.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        // Try to find a factory by convention: ModelNameFactory in same namespace
        $modelClass = static::class;
        $factoryClass = $modelClass . 'Factory';

        if (class_exists($factoryClass)) {
            return $factoryClass::new();
        }

        // Try namespace\Factories\ModelNameFactory
        $namespace = substr($modelClass, 0, strrpos($modelClass, '\\'));
        $shortName = substr($modelClass, strrpos($modelClass, '\\') + 1);
        $factoryClass = $namespace . '\\Factories\\' . $shortName . 'Factory';

        if (class_exists($factoryClass)) {
            return $factoryClass::new();
        }

        throw new \RuntimeException(
            "No factory found for model [{$modelClass}]. " .
            "Either create a factory class or override the newFactory() method."
        );
    }
}
