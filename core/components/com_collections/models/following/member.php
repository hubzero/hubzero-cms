<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects
namespace Components\Collections\Models\Following;

require_once __DIR__ . DS . 'base.php';
require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Model class for following a member
 */
class Member extends Base
{
    /**
     * \Hubzero\User\User
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
     * @var  string
     */
    private $baselink = null;

    /**
     * Constructor
     *
     * @param   integer  $id  Member ID
     * @return  void
     */
    public function __construct($oid = null)
    {
        $this->obj = \Components\Members\Models\Member::oneOrNew($oid);

        $this->baselink = $this->obj->link() . '&active=collections';
    }

    /**
     * Get the member's image
     *
     * @return  string
     */
    public function image()
    {
        if (!isset($this->image)) {
            $this->image = $this->obj->picture(0);
        }
        return $this->image;
    }

    /**
     * Get the member's username
     *
     * @return  string
     */
    public function alias()
    {
        return $this->obj->get('username');
    }

    /**
     * Get the member's name
     *
     * @return  string
     */
    public function title()
    {
        return $this->obj->get('name');
    }

    /**
     * Get the URL for this member
     *
     * @return  string
     */
    public function link($what = 'base')
    {
        switch (strtolower(trim($what))) {
            case 'follow':
                return $this->baselink . '&task=follow';
            break;

            case 'unfollow':
                return $this->baselink . '&task=unfollow';
            break;

            case 'base':
            default:
                return $this->baselink;
            break;
        }
    }
}
