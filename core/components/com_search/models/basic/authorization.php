<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	private $uid = NULL, $super_admin = false, $groups = NULL;

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

