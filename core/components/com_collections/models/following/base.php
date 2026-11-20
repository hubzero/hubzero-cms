<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Following;

use Hubzero\Base\Obj;

/**
 * Abstract model class for following
 *
 * @phpstan-consistent-constructor
 */
abstract class Base extends Obj
{
    /**
     * URL
     *
     * @var string
     */
    private $baselink = 'index.php';

    /**
     * Returns a reference to this object
     *
     * @param   integer|null  $oid  User ID
     * @return  static
     */
    public static function &getInstance($oid = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        if (!isset($instances[$oid])) {
            $instances[$oid] = new static($oid);
        }

        return $instances[$oid];
    }

    /**
     * Get the creator of this entry
     *
     * Accepts an optional property name. If provided
     * it will return that property value. Otherwise,
     * it returns the entire User object
     *
     * @return  mixed
     */
    public function creator()
    {
        return null;
    }

    /**
     * Get this item's image
     *
     * @return  string|null  None for this kind of item
     */
    public function image()
    {
        return null;
    }

    /**
     * Get this item's alias
     *
     * @return  string
     */
    public function alias()
    {
        return '';
    }

    /**
     * Get this item's title
     *
     * @return  string
     */
    public function title()
    {
        return '';
    }

    /**
     * Get the URL for this item
     *
     * @param   string  $what
     * @return  string
     */
    public function link($what = 'base')
    {
        return $this->baselink;
    }
}
