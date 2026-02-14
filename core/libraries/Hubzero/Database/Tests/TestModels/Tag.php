<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Tag test model for multi-database testing
 */
class Tag extends Relational
{
    /**
     * The table to which the class pertains
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * Table name storage for runtime configuration
     *
     * @var string|null
     */
    protected static $runtimeTableName = null;

    /**
     * Associative table name for posts relationship
     *
     * @var string|null
     */
    protected static $postsAssocTable = null;

    /**
     * Configure the table name at runtime
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
     * Configure the posts associative table name
     *
     * @param  string|null  $name  The table name (null to skip)
     * @return void
     */
    public static function usePostsAssocTable(?string $name = null): void
    {
        if ($name !== null) {
            self::$postsAssocTable = $name;
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
            self::$postsAssocTable = null;
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
     * Many to many relationship with posts
     *
     * @return \Hubzero\Database\Relationship\ManyToMany
     */
    public function posts()
    {
        $assocTable = self::$postsAssocTable ?: 'post_tags';
        return $this->manyToMany(Post::class, $assocTable, 'tag_id', 'post_id');
    }
}
