<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups table
 */
class GroupsGroup extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
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
	 *
	 * @since   11.1
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
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 *
	 * @return  integer  The id of the asset's parent
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query	= $db->getQuery(true);
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
	 * @return     boolean True if all fields are valid
	 */
	public function check()
	{
		if (trim($this->cn) == '')
		{
			$this->setError(JText::_('COM_GROUPS_ERROR_EMPTY_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Save changes
	 *
	 * @return     boolean
	 */
	public function save($src, $orderingFilter ='', $ignore = '')
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Insert or Update the object
	 *
	 * @return     boolean
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
	 * @param      mixed $oid Unique ID or alias of object to retrieve
	 * @return     boolean True on success
	 */
	public function load($oid = NULL, $reset = true)
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
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildMembersQuery($filters=array())
	{
		$query = "FROM (
			(" . $this->_groupTable('invitee', 'i', $filters) . ") UNION
			(" . $this->_groupTable('applicant', 'a', $filters) . ") UNION
			(" . $this->_groupTable('member', 'm', $filters) . ") UNION
			(" . $this->_groupTable('manager', 'n', $filters) . ")
		) AS v";

		$where = array();

		if (isset($filters['status']) && $filters['status'])
		{
			$where[] = "v.`role`=" . $this->_db->Quote($filters['status']);
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "v.`uidNumber`=" . $this->_db->Quote(intval($filters['search']));
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
	 * @param  string $role    Role [member, invitee, applicant, manager]
	 * @param  string $tbl     Table alias
	 * @param  array  $filters Filters to build query from
	 * @return string
	 */
	private function _groupTable($role='member', $tbl='m', $filters=array())
	{
		$query = "SELECT u.name, u.username, u.email, {$tbl}.uidNumber, '{$role}' AS role
					FROM #__xgroups_{$role}s AS {$tbl} JOIN #__users AS u ON {$tbl}.uidNumber=u.id";

		if (isset($filters['gidNumber']))
		{
			$query .= " AND {$tbl}.`gidNumber`=" . $this->_db->Quote(intval($filters['gidNumber']));
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function countMembers($filters=array())
	{
		$query  = "SELECT COUNT(DISTINCT v.username) ";
		$query .= $this->_buildMembersQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
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
		if (!empty($filters['start']) && !empty($filters['limit']))
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
	 * @param  string gid
	 * @return object Return cn
	 */
	public function getName($id)
	{
		$query = "SELECT cn from #__xgroups WHERE gidNumber = " . $id . ";";
		$this->_db->setQuery($query);
		$cn = $this->_db->loadResult();

		return $cn;
	}
}

