<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;
use DateTime;
use DateTimeInterface;

/**
 * Cast attribute to/from DateTime object
 *
 * Usage:
 * ```php
 * protected $casts = [
 *     'expires_at' => AsDateTime::class,
 * ];
 * ```
 */
class AsDateTime implements CastsAttributes
{
    /**
     * The default format for storing dates
     *
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    /**
     * Cast to DateTime when reading
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array<mixed>       $attributes
     * @return DateTime|null
     */
    public function get(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTime::createFromInterface($value);
        }

        return new DateTime($value);
    }

    /**
     * Cast to string when storing
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

        if ($value instanceof DateTimeInterface) {
            return $value->format($this->format);
        }

        if (is_numeric($value)) {
            return new DateTime('@' . $value)->format($this->format);
        }

        if (is_string($value)) {
            return new DateTime($value)->format($this->format);
        }

        return null;
    }
}
