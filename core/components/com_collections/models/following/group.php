<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects
namespace Components\Collections\Models\Following;

require_once __DIR__ . DS . 'base.php';

/**
 * Model class for following a group
 */
class Group extends Base
{
    /**
     * \Hubzero\User\Group
     *
     * @var  object
     */
    private $obj = null;

    /**
     * File path
     *
     * @var  string
     */
    private $image = null;

    /**
     * URL
     *
     * @var string
     */
    private $baselink = null;

    /**
     * Constructor
     *
     * @param   integer  $id  Group ID
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->obj = \Hubzero\User\Group::getInstance($oid);

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
            $config = \Component::params('com_groups');
            if ($this->obj->get('logo')) {
                $this->image = DS . trim($config->get('uploadpath', '/site/groups'), DS)
                    . DS . $this->obj->get('gidNumber') . DS . $this->obj->get('logo');
            } else {
                $this->image = '/core/components/com_groups/site/assets/img/group_default_logo.png';
            }
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
            break;

            case 'unfollow':
                return $this->baselink . '&scope=unfollow';
            break;

            case 'base':
            default:
                return $this->baselink;
            break;
        }
    }
}
