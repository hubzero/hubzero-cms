<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;

/**
 * Interface for custom attribute casts
 *
 * Implement this interface to create custom type casters for model attributes.
 * Custom casts allow you to transform attribute values when getting/setting them.
 *
 * Usage in models:
 * ```php
 * protected $casts = [
 *     'options' => \App\Casts\Json::class,
 *     'address' => \App\Casts\AddressCast::class,
 * ];
 * ```
 *
 * Example implementation:
 * ```php
 * class Json implements CastsAttributes
 * {
 *     public function get($model, $key, $value, $attributes)
 *     {
 *         return json_decode($value, true);
 *     }
 *
 *     public function set($model, $key, $value, $attributes)
 *     {
 *         return json_encode($value);
 *     }
 * }
 * ```
 */
interface CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values
     *
     * Called when reading an attribute from the model.
     *
     * @param  Relational  $model       The model instance
     * @param  string      $key         The attribute key
     * @param  mixed       $value       The raw value from the database
     * @param  array       $attributes  All model attributes
     * @return mixed                    The transformed value
     */
    public function get(Relational $model, string $key, $value, array $attributes);

    /**
     * Transform the attribute to its underlying model values
     *
     * Called when setting an attribute on the model.
     *
     * @param  Relational  $model       The model instance
     * @param  string      $key         The attribute key
     * @param  mixed       $value       The value being set
     * @param  array       $attributes  All model attributes
     * @return mixed                    The value to store (can be string or array for multiple columns)
     */
    public function set(Relational $model, string $key, $value, array $attributes);
}
