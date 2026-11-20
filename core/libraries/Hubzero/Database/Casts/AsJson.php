<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @param  array<mixed>       $attributes
     * @return array<mixed>|null
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
     * @param  array<mixed>       $attributes
     * @return string|null
     */
    public function set(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
