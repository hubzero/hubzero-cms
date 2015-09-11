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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

/**
 * Table class for job employer
 */
class Employer extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_employers', 'id', $db);
	}

	/**
	 * Check if a user is an employer
	 *
	 * @param      string $uid Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function isEmployer($uid, $admin=0)
	{
		if ($uid === NULL)
		{
			return false;
		}

		$now = \Date::toSql();
		$query  = "SELECT e.id FROM $this->_tbl AS e  ";
		if (!$admin)
		{
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE e.uid = " . $this->_db->quote($uid) . " AND s.status=1";
			$query .= " AND s.expires > " . $this->_db->quote($now) . " ";
		}
		else
		{
			$query .= "WHERE e.uid = 1";
		}
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $uid User ID
	 * @return     boolean True upon success
	 */
	public function loadEmployer($uid=NULL)
	{
		if ($uid === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $this->_db->quote($uid));
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Get an employer
	 *
	 * @param      integer $uid              User ID
	 * @param      string  $subscriptionCode Subscription code
	 * @return     mixed False if errors, Array upon success
	 */
	public function getEmployer($uid = NULL, $subscriptionCode = NULL)
	{
		if ($uid === NULL or $subscriptionCode === NULL)
		{
			return false;
		}
		$query  = "SELECT e.* ";
		$query .= "FROM #__jobs_employers AS e  ";
		if ($subscriptionCode == 'admin')
		{
			$query .= "WHERE e.uid = 1";
		}
		else if ($subscriptionCode)
		{
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE s.code=" . $this->_db->quote($subscriptionCode);
		}
		else if ($uid)
		{
			$query .= "WHERE e.uid = " . $this->_db->quote($uid);
		}
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			return $result[0];
		}
		return false;
	}
}

