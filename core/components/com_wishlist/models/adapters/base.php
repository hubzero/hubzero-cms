<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models\Adapters;

use Hubzero\Base\Obj;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Lang;

/**
 * Abstract adapter class for a wishlist
 */
abstract class Base extends Obj
{
    /**
     * The object the referenceid references
     *
     * @var  object|null
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
     * @var  array<mixed>
     */
    protected $segments = [];

    /**
     * Scope name
     *
     * @var  string
     */
    protected $scope = '';

    /**
     * Constructor
     *
     * @param   integer  $referenceid  Scope ID (group, course, etc.)
     * @return  void
     */
    public function __construct($referenceid = 0)
    {
        $this->set('referenceid', $referenceid);
    }

    /**
     * Can this adapter handle the provided scope?
     *
     * @param   mixed  $scope
     * @return  bool
     */
    public function handles($scope)
    {
        return ($this->scope && $this->scope == $scope);
    }

    /**
     * Get owners
     *
     * @return  array<mixed>
     */
    public function owners()
    {
        return [];
    }

    /**
     * Get groups
     *
     * @return  array<mixed>
     */
    public function groups()
    {
        return [];
    }

    /**
     * Generate and return the title for this wishlist
     *
     * @return  string
     */
    public function title()
    {
        return Lang::txt('COM_WISHLIST');
    }

    /**
     * Retrieve a property from the internal item object
     *
     * @param   string|mixed  $key  Property to retrieve
     * @return  mixed
     */
    public function item($key = '')
    {
        if ($key && is_object($this->item)) {
            return $this->item->$key;
        }
        return $this->item;
    }

    /**
     * Does the item exists?
     *
     * @return  boolean
     */
    public function exists()
    {
        if ($this->item() && $this->item('id')) {
            return true;
        }
        return false;
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
     * @param   array<mixed>   $segments  An associative array of querystring bits
     * @return  string
     */
    protected function toQueryString(array $segments)
    {
        $bits = [];
        foreach ($segments as $key => $param) {
            if ($param) {
                $bits[] = $key . '=' . $param;
            }
        }
        return implode('&', $bits);
    }

    /**
     * Append an item to the breadcrumb trail.
     * If no item is provided, it will build the trail up to the list
     *
     * @param   string|null  $title  Breadcrumb title
     * @param   string|null  $url    Breadcrumb URL
     * @return  string
     */
    public function pathway($title = null, $url = null)
    {
        if (!$title) {
            Pathway::append(
                Lang::txt(strtoupper($this->get('option'))),
                'index.php?option=' . $this->get('option'),
            );
        } else {
            Pathway::append(
                $title,
                (string) $url,
            );
        }

        return $this;
    }
}
