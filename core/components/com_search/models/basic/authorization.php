<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Authorization checker
 */
class Authorization
{
    /**
     * Description for 'uid'
     *
     * @var string
     */
    private $uid = null;
    /**
     * @var  bool
     */
    private $super_admin = false;
    private $groups = null;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        if (User::isGuest()) {
            $this->groups = [];
            return;
        }

        $this->uid = User::get('id');

        if (User::get('usertype') == 'Super Administrator') {
            $this->super_admin = true;
        }
    }

    /**
     * Is the user logged out?
     *
     * @return  boolean
     */
    public function isGuest()
    {
        return is_null($this->uid);
    }

    /**
     * Is the user a super admin?
     *
     * @return  boolean
     */
    public function isSuperAdmin()
    {
        return $this->super_admin;
    }

    /**
     * Get a user's groups
     *
     * @return  array
     */
    public function getGroups()
    {
        if (is_null($this->groups)) {
            $dbh = App::get('db');
            $dbh->setQuery(
                'SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_members` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid . '
				UNION
				SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_managers` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid,
            );

            $this->groups = [];
            foreach ($dbh->loadAssocList() as $row) {
                $this->groups[$row['gidNumber']] = $row['cn'];
            }
        }
        return $this->groups;
    }

    /**
     * Get group IDs
     *
     * @return  array
     */
    public function getGroupIds()
    {
        return array_keys($this->getGroups());
    }

    /**
     * Get group names
     *
     * @return  array
     */
    public function getGroupNames()
    {
        return array_values($this->getGroups());
    }
}
