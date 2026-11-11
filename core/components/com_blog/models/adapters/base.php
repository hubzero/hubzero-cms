<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models\Adapters;

use Hubzero\Base\Obj;

/**
 * Abstract adapter class for a blog entry link
 */
abstract class Base extends Obj
{
    /**
     * The object the referenceid references
     *
     * @var  object
     */
    protected $item = null;

    /**
     * Script name
     *
     * @var  string
     */
    protected $base = 'index.php';

    /**
     * URL segments
     *
     * @var  string
     */
    protected $segments = [];

    /**
     * Constructor
     *
     * @param   integer  $scope_id  Scope ID (group, course, etc.)
     * @return  void
     */
    public function __construct($scope_id = 0) {}

    /**
     * Retrieve a property from the internal item object
     *
     * @param   string  $key      Property to retrieve
     * @param   mixed   $default
     * @return  string
     */
    public function item($key = '', $default = null)
    {
        if ($key && is_object($this->item)) {
            return $this->item->get($key, $default);
        }
        return $this->item;
    }

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param   string  $type    The type of link to return
     * @param   mixed   $params  Optional string or associative array of params to append
     * @return  string
     */
    public function link($type = '', $params = null)
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
     * Get the path to the storage location for
     * this blog's files
     *
     * @return  string
     * @since   1.3.1
     */
    public function filespace()
    {
        return PATH_APP . DS . trim($this->get('path', ''), DS);
    }
}
