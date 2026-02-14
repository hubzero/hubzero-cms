<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;
use Hubzero\Base\ItemList;

/**
 * Cast attribute to/from Collection (ItemList)
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
     * Cast to ItemList when reading
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return ItemList|null
     */
    public function get(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return new ItemList([]);
        }

        $data = json_decode($value, true);

        return new ItemList($data ?: []);
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

        // Handle ItemList/array-like objects
        if ($value instanceof ItemList) {
            // ItemList doesn't have toArray(), convert via iteration
            $arr = [];
            foreach ($value as $item) {
                $arr[] = $item;
            }
            $value = $arr;
        } elseif (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
