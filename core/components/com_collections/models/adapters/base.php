<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Abstract adapter class for a blog entry link
 */
abstract class Base extends Obj
{
    /**
     * Script name
     *
     * @var string
     */
    protected $base = 'index.php';

    /**
     * URL segments
     *
     * @var array
     */
    protected $segments = [];

    /**
     * Constructor
     *
     * Does nothing here; each adapter has its own. It is kept so that the
     * parent's constructor does not take the adapter's arguments as properties.
     *
     * @return  void
     */
    public function __construct() {}

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param   string  $type    The type of link to return
     * @param   mixed   $params  Optional string or associative array of params to append
     * @return  string
     */
    public function build($type = '', $params = null)
    {
        return $this->base;
    }

    /**
     * Flatten array of segments into querystring
     *
     * @param   array   $segments  An associative array of querystring bits
     * @return  string
     */
    protected function toQueryString(array $segments)
    {
        $bits = [];
        foreach ($segments as $key => $param) {
            $bits[] = $key . '=' . $param;
        }
        return implode('&', $bits);
    }

    /**
     * Check if a user has access
     *
     * @param   integer  $user_id
     * @return  boolean
     */
    public function canAccess($user_id)
    {
        return true;
    }
}
