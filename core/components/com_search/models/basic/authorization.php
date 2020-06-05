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
	private $uid = null, $super_admin = false, $groups = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if (\User::isGuest())
		{
			$this->groups = array();
			return;
		}

		$this->uid = \User::get('id');

		if (\User::get('usertype') == 'Super Administrator')
		{
			$this->super_admin = true;
		}
	}

	/**
	 * Is the user logged out?
	 *
	 * @return  bolean
	 */
	public function is_guest()
	{
		return is_null($this->uid);
	}

	/**
	 * Is the user a super admin?
	 *
	 * @return  boolean
	 */
	public function is_super_admin()
	{
		return $this->super_admin;
	}

	/**
	 * Get a user's groups
	 *
	 * @return  array
	 */
	public function get_groups()
	{
		if (is_null($this->groups))
		{
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
			foreach ($dbh->loadAssocList() as $row)
			{
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
	public function get_group_ids()
	{
		return array_keys($this->get_groups());
	}

	/**
	 * Get group names
	 *
	 * @return  array
	 */
	public function get_group_names()
	{
		return array_values($this->get_groups());
	}
}
