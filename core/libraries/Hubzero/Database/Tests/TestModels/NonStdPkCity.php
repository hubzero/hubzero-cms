<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * City model that belongs to a Country with non-standard primary key
 *
 * The belongsToOne relationship does NOT pass an explicit $parentKey,
 * so it relies on the ORM correctly defaulting to the parent model's
 * primary key ('code') rather than this model's primary key ('id').
 */
class NonStdPkCity extends Relational
{
    /**
     * @var string
     */
    protected $table = 'nspk_cities';

    /**
     * Table name storage for runtime configuration
     *
     * @var string|null
     */
    protected static $runtimeTableName = null;

    /**
     * @param  string|null  $name
     * @return void
     */
    public static function useTable(?string $name = null): void
    {
        if ($name !== null) {
            self::$runtimeTableName = $name;
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        if (self::$runtimeTableName !== null) {
            return self::$runtimeTableName;
        }
        return parent::getTableName();
    }

    /**
     * Belongs to a country — intentionally omits $parentKey to exercise
     * the default key resolution in Relational::belongsToOne().
     *
     * @return \Hubzero\Database\Relationship\BelongsToOne
     */
    public function country()
    {
        return $this->belongsToOne(NonStdPkCountry::class, 'country_code');
    }
}
