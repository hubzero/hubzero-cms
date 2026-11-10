<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Account\Tests;

/**
 * A member whose username is all the plugin needs to build a path
 */
class TestMember
{
    /**
     * Member username
     *
     * @var  string
     */
    private $username = null;

    /**
     * Constructor
     *
     * @param   string  $username
     * @return  void
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Get a property
     *
     * @param   string  $property
     * @return  mixed
     */
    public function get($property)
    {
        return ($property == 'username' ? $this->username : null);
    }
}
