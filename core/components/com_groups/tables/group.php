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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

/**
 * Groups table
 */
class Group extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups', 'gidNumber', $db);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_groups.group.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   object   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 * @return  integer  The id of the asset's parent
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_groups'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Validate fields before store()
	 *
	 * @return  boolean  True if all fields are valid
	 */
	public function check()
	{
		if (trim($this->cn) == '')
		{
			$this->setError(\Lang::txt('COM_GROUPS_ERROR_EMPTY_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Save changes
	 *
	 * @param   string   $src
	 * @param   string   $orderingFilter
	 * @param   string   $ignore
	 * @return  boolean
	 */
	public function save($src, $orderingFilter ='', $ignore = '')
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Insert or Update the object
	 *
	 * @return  boolean
	 */
	public function store($updateNulls = false)
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 *
	 * @param   mixed    $oid  Unique ID or alias of object to retrieve
	 * @return  boolean  True on success
	 */
	public function load($oid = null, $reset = true)
	{
		if (empty($oid))
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$sql  = "SELECT * FROM $this->_tbl WHERE cn='$oid' LIMIT 1";
		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  database query
	 */
	private function _buildMembersQuery($filters=array())
	{
		/*$query = "FROM (
			(" . $this->_groupTable('invitee', 'i', $filters) . ") UNION
			(" . $this->_groupTable('applicant', 'a', $filters) . ") UNION
			(" . $this->_groupTable('member', 'm', $filters) . ") UNION
			(" . $this->_groupTable('manager', 'n', $filters) . ")
		) AS v";*/
		$query = "FROM (
			(" . $this->_groupTable('invitee', 'i', $filters) . ") UNION
			(" . $this->_groupTable('applicant', 'a', $filters) . ") UNION
			(" . $this->_groupTable('member', 'm', $filters) . ")
		) AS v";

		$where = array();

		if (isset($filters['status']) && $filters['status'])
		{
			$query = "FROM (
				(" . $this->_groupTable('invitee', 'i', $filters) . ") UNION
				(" . $this->_groupTable('applicant', 'a', $filters) . ") UNION
				(" . $this->_groupTable('member', 'm', $filters) . ") UNION
				(" . $this->_groupTable('manager', 'n', $filters) . ")
			) AS v";

			$where[] = "v.`role`=" . $this->_db->quote($filters['status']);
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "v.`uidNumber`=" . $this->_db->quote(intval($filters['search']));
			}
			else
			{
				$where[] = "(LOWER(v.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(v.username) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(v.email) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Build sub-query
	 *
	 * @param   string  $role     Role [member, invitee, applicant, manager]
	 * @param   string  $tbl      Table alias
	 * @param   array   $filters  Filters to build query from
	 * @return  string
	 */
	private function _groupTable($role='member', $tbl='m', $filters=array())
	{
		$query = "SELECT u.name, u.username, u.email, {$tbl}.uidNumber, '{$role}' AS role
					FROM `#__xgroups_{$role}s` AS {$tbl} JOIN `#__users` AS u ON {$tbl}.uidNumber=u.id";

		if (isset($filters['gidNumber']))
		{
			$query .= " AND {$tbl}.`gidNumber`=" . $this->_db->quote(intval($filters['gidNumber']));
		}

		return $query;
	}

	/**
	 * Get count of members
	 *
	 * @param   array    $filters
	 * @return  integer  Return course units
	 */
	public function countMembers($filters=array())
	{
		$query  = "SELECT COUNT(DISTINCT v.username) ";
		$query .= $this->_buildMembersQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of members
	 *
	 * @param   array   $filters
	 * @return  object  Return course units
	 */
	public function findMembers($filters=array())
	{
		$results = array();

		$query  = "SELECT v.* ";
		$query .= $this->_buildMembersQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'name';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (isset($filters['start']) && isset($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		$values = $this->_db->loadObjectList();
		if ($values)
		{
			foreach ($values as $value)
			{
				if (!isset($results[$value->uidNumber]))
				{
					$results[$value->uidNumber] = $value;
				}
				else
				{
					if ($value->role == 'manager')
					{
						$results[$value->uidNumber] = $value;
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Get a cn
	 *
	 * @param   string  $id  gid
	 * @return  object  Return cn
	 */
	public function getName($id)
	{
		$query = "SELECT cn FROM `#__xgroups` WHERE gidNumber = " . (int)$id . ";";
		$this->_db->setQuery($query);

		return $this->_db->loadResult();
	}
}
