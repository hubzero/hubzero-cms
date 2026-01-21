<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

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
    private $super_admin = false;
    private $groups = null;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        if (\User::isGuest()) {
            $this->groups = array();
            return;
        }

        $this->uid = \User::get('id');

        if (\User::get('usertype') == 'Super Administrator') {
            $this->super_admin = true;
        }
    }

    /**
     * Is the user logged out?
     *
     * @return  bolean
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function is_guest()
    {
        return is_null($this->uid);
    }

    /**
     * Is the user a super admin?
     *
     * @return  boolean
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function is_super_admin()
    {
        return $this->super_admin;
    }

    /**
     * Get a user's groups
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_groups()
    {
        if (is_null($this->groups)) {
            $dbh = \App::get('db');
            $dbh->setQuery(
                'SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_members` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid . '
				UNION
				SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_managers` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid
            );

            $this->groups = array();
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
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_group_ids()
    {
        return array_keys($this->get_groups());
    }

    /**
     * Get group names
     *
     * @return  array
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_group_names()
    {
        return array_values($this->get_groups());
    }
}
