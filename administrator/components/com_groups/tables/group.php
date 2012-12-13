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
	public function save()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}
	
	/**
	 * Insert or Update the object
	 * 
	 * @return     boolean
	 */
	public function store()
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
	public function load($oid=NULL)
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
		$query = " FROM #__xgroups_members AS m 
				LEFT JOIN #__users AS u ON u.id=m.uidNumber
				LEFT JOIN #__xgroups_managers AS mg on mg.uidNumber=m.uidNumber AND mg.gidNumber=m.gidNumber
				LEFT JOIN #__xgroups_applicants AS ma on ma.uidNumber=m.uidNumber AND ma.gidNumber=m.gidNumber
				LEFT JOIN #__xgroups_invitees AS mi on mi.uidNumber=m.uidNumber AND mi.gidNumber=m.gidNumber";

		$where = array();
		if (isset($filters['gidNumber']))
		{
			$where[] = "m.`gidNumber`=" . $this->_db->Quote(intval($filters['gidNumber']));
		}
		if (isset($filters['uidNumber']))
		{
			$where[] = "m.`uidNumber`=" . $this->_db->Quote(intval($filters['uidNumber']));
		}
		if (isset($filters['status']))
		{
			switch ($filters['status'])
			{
				case 'manager':
					$where[] = "mg.`uidNumber` > 0";
				break;

				case 'applicant':
					$where[] = "ma.`uidNumber` > 0";
				break;

				case 'invitee':
					$where[] = "mi.`uidNumber` > 0";
				break;

				default:
				break;
			}
		}
		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "m.`uidNumber`=" . $this->_db->Quote(intval($filters['search']));
			}
			else
			{
				$where[] = "(LOWER(u.name) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' 
						OR LOWER(u.username) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%'
						OR LOWER(u.email) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
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
		$query  = "SELECT COUNT(*) ";
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
		$query  = "SELECT DISTINCT m.*, u.name, u.username, u.email, mg.uidNumber AS manager, ma.uidNumber AS applicant, mi.uidNumber AS invitee ";
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
		return $this->_db->loadObjectList();
	}
}

