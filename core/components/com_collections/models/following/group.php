<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Following;

/**
 * Model class for following a group
 */
class Group extends Base
{
    /**
     * \Hubzero\User\Group
     *
     * @var  \Hubzero\User\Group
     */
    private $obj;

    /**
     * File path
     *
     * @var  string|null
     */
    private $image = null;

    /**
     * URL
     *
     * @var string
     */
    private $baselink;

    /**
     * Constructor
     *
     * @param   integer|null  $oid  Group ID
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->obj = \Hubzero\User\Group::getInstance($oid) ?: new \Hubzero\User\Group();

        $this->baselink = 'index.php?option=com_groups&cn=' . $this->obj->get('cn') . '&active=collections';
    }

    /**
     * Get the group's image
     *
     * @return  string
     */
    public function image()
    {
        if (!isset($this->image)) {
            // The group knows where its logo is (under app/, in uploads/), when
            // to fall back to the default, and when not to show it at all; the
            // path built here lacked both parts and every logo was broken
            $this->image = $this->obj->getLogo();
        }
        return $this->image;
    }

    /**
     * Get the group's alias
     *
     * @return  string
     */
    public function alias()
    {
        return $this->obj->get('cn');
    }

    /**
     * Get the group's title
     *
     * @return  string
     */
    public function title()
    {
        return $this->obj->get('description');
    }

    /**
     * Get the URL for this group
     *
     * @return  string
     */
    public function link($what = 'base')
    {
        switch (strtolower(trim($what))) {
            case 'follow':
                return $this->baselink . '&scope=follow';

            case 'unfollow':
                return $this->baselink . '&scope=unfollow';

            case 'base':
            default:
                return $this->baselink;
        }
    }
}
