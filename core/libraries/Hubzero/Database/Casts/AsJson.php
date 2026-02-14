<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;

/**
 * Cast attribute to/from JSON
 *
 * Usage:
 * ```php
 * protected $casts = [
 *     'options' => AsJson::class,
 * ];
 * ```
 */
class AsJson implements CastsAttributes
{
    /**
     * Cast to JSON array/object when reading
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return array|null
     */
    public function get(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return json_decode($value, true);
    }

    /**
     * Cast to JSON string when storing
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return string|null
     */
    public function set(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
