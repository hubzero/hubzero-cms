<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * User test model for multi-database testing
 */
class User extends Relational
{
    /**
     * The table to which the class pertains
     *
     * This can be overridden by calling setTableName() before using the model
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Table name storage for runtime configuration
     *
     * @var string|null
     */
    protected static $runtimeTableName = null;

    /**
     * Configure the table name at runtime (for database-specific tests)
     *
     * Parameter is optional to avoid errors when Relational invokes methods
     * during relationship discovery.
     *
     * @param  string|null  $name  The table name (null to skip)
     * @return void
     */
    public static function useTable(?string $name = null): void
    {
        if ($name !== null) {
            self::$runtimeTableName = $name;
        }
    }

    /**
     * Reset the table name to default
     *
     * Takes an optional dummy parameter to prevent Relational's
     * method discovery from accidentally invoking this.
     *
     * @param  bool  $reset  Set to true to actually reset
     * @return void
     */
    public static function useDefaultTable(bool $reset = false): void
    {
        if ($reset) {
            self::$runtimeTableName = null;
        }
    }

    /**
     * Get the table name
     *
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
     * One to many relationship with posts
     *
     * @return \Hubzero\Database\Relationship\OneToMany
     */
    public function posts()
    {
        return $this->oneToMany(Post::class, 'user_id');
    }

    /**
     * Splits name and returns the first part
     *
     * @return string
     */
    public function helperGetFirstName()
    {
        $name = $this->get('name', '');
        return (strpos($name, ' ') !== false) ? explode(' ', $name)[0] : $name;
    }

    /**
     * Transforms name to a silly nickname
     *
     * @return string
     */
    public function transformNickname()
    {
        return $this->getFirstName() . 'er';
    }
}
