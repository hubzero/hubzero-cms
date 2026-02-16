<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;
use ArrayObject;

/**
 * Cast attribute to/from Collection (ArrayObject)
 *
 * Usage:
 * ```php
 * protected $casts = [
 *     'tags' => AsCollection::class,
 * ];
 * ```
 */
class AsCollection implements CastsAttributes
{
    /**
     * Cast to ArrayObject when reading
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return ArrayObject|null
     */
    public function get(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return new ArrayObject([]);
        }

        $data = json_decode($value, true);

        return new ArrayObject($data ?: []);
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

        if ($value instanceof ArrayObject) {
            $value = $value->getArrayCopy();
        } elseif (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
