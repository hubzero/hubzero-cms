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

namespace Components\Services\Tables;

/**
 * Table class for services
 */
class Service extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_points_services', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->alias = trim($this->alias);
		if ($this->alias == '')
		{
			$this->setError(Lang::txt('Entry must have an alias.'));
		}

		$this->category = trim($this->category);
		if ($this->category == '')
		{
			$this->setError(Lang::txt('Entry must have a category.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $alias  Entry alias
	 * @param   integer  $id     Entry ID
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadService($alias=NULL, $id = NULL)
	{
		if ($alias === NULL && $id === NULL)
		{
			return false;
		}

		if ($alias)
		{
			return parent::load(array(
				'alias'   => $alias
			));
		}

		return parent::load($id);
	}

	/**
	 * Get a list of services
	 *
	 * @param   string   $category      Category
	 * @param   integer  $completeinfo  Get complete info?
	 * @param   integer  $active        Active?
	 * @param   string   $sortby        Sort field
	 * @param   string   $sortdir       Sort direction
	 * @param   string   $specialgroup  Special group name
	 * @param   integer  $admin         Is admin?
	 * @return  array
	 */
	public function getServices($category = NULL, $completeinfo = 0, $active = 1, $sortby = 'category', $sortdir = 'ASC', $specialgroup='', $admin = 0)
	{
		$services = array();

		$query  = "SELECT s.* ";
		$query .= $specialgroup ? " , m.gidNumber as ingroup " : "";
		$query .= "FROM $this->_tbl AS s ";

		// do we have special admin group
		if ($specialgroup)
		{
			$query .= "JOIN #__xgroups AS xg ON xg.cn=" . $this->_db->quote($specialgroup) . " ";
			$query .= " LEFT JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber AND m.uidNumber=" . $this->_db->quote(User::get('id')) . " ";
		}

		$query .= "WHERE 1=1 ";
		if ($category)
		{
			$query .= "AND s.category = " . $this->_db->quote($category) . " ";
		}
		if ($active)
		{
			$query .= "AND s.status = 1 ";
		}
		if (!$admin)
		{
			$query .= $specialgroup ? "AND (s.restricted = 0 or (s.restricted = 1 AND m.gidNumber IS NOT NULL )) " : " AND s.restricted = 0 ";
		}
		$query .= " ORDER BY $sortby $sortdir ";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadObjectList())
		{
			foreach ($result as $r)
			{
				if ($completeinfo)
				{
					$services[] = $r;
				}
				else
				{
					$services[$r->id] = $r->title;
				}
			}
		}

		return $services;
	}

	/**
	 * Get the cost for a service
	 *
	 * @param   integer  $id      Service ID
	 * @param   integer  $points  Load point cost?
	 * @return  mixed
	 */
	public function getServiceCost($id, $points = 0)
	{
		if ($id === NULL)
		{
			return false;
		}

		if ($points)
		{
			$this->_db->setQuery("SELECT pointsprice FROM $this->_tbl WHERE id=" . $this->_db->quote($id));
		}
		else
		{
			$this->_db->setQuery("SELECT unitprice FROM $this->_tbl WHERE id=" . $this->_db->quote($id));
		}
		return $this->_db->loadResult();
	}

	/**
	 * Load a service for a user
	 *
	 * @param   integer  $uid       User ID
	 * @param   string   $field     Field name
	 * @param   string   $category  Category
	 * @return  mixed
	 */
	public function getUserService($uid = NULL, $field = 'alias', $category = 'jobs')
	{
		if ($uid === NULL)
		{
			return false;
		}

		$field = $field ? 's.' . $field : 's.*';

		$query  = "SELECT $field ";
		$query .= "FROM $this->_tbl as s ";
		$query .= "JOIN #__users_points_subscriptions AS y ON s.id=y.serviceid  ";

		$query .= "WHERE s.category =" . $this->_db->quote($category) . " AND y.uid = " . $this->_db->quote($uid) . " ";
		$query .= " ORDER BY y.id DESC LIMIT 1 ";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

