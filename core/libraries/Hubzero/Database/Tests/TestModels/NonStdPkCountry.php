<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Country model with non-standard primary key ('code' instead of 'id')
 *
 * Used to test that belongsToOne() correctly defaults the parent key
 * to the parent model's primary key rather than the child model's.
 */
class NonStdPkCountry extends Relational
{
    /**
     * @var string
     */
    protected $table = 'nspk_countries';

    /**
     * @var string
     */
    protected $pk = 'code';

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
     * @return \Hubzero\Database\Relationship\OneToMany
     */
    public function cities()
    {
        return $this->oneToMany(NonStdPkCity::class, 'country_code');
    }
}
