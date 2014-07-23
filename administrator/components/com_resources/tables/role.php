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
 * Table class for resource contributor role
 */
class ResourcesContributorRole extends JTable
{
	/**
	 * int(11) Primary Key
	 *
	 * @var integer
	 */
	var $id    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $alias = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $title = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $state  = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $modified   = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $modified_by = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__author_roles', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);

		if (!$this->title)
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

		$juser = JFactory::getUser();
		if (!$this->id)
		{
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
		}
		else
		{
			$this->modified = JFactory::getDate()->toSql();
			$this->modified_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      mixed $oid String (alias) or integer (ID)
	 * @return     boolean True on success
	 */
	public function load($oid=NULL, $reset = true)
	{
		if ($oid === NULL)
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$oid = trim($oid);
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid));
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
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT r.* " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS r";

		$where = array();
		if (isset($filters['state']))
		{
			$where[] = "r.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(r.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
				OR LOWER(r.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get all the roles associated with a type
	 *
	 * @param      integer $type_id Type ID
	 * @return     array
	 */
	public function getRolesForType($type_id=null)
	{
		if ($type_id === null)
		{
			$this->setError(JText::_('Missing argument'));
			return false;
		}

		$type_id = intval($type_id);

		$query = "SELECT r.id, r.title, r.alias
					FROM $this->_tbl AS r
					JOIN #__author_role_types AS rt ON r.id=rt.role_id AND rt.type_id=" . $this->_db->Quote($type_id) . "
					ORDER BY r.title ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all the types associated with a role
	 *
	 * @param      integer $role_id Role ID
	 * @return     array
	 */
	public function getTypesForRole($role_id=null)
	{
		if ($role_id === null)
		{
			$role_id = $this->id;
		}

		if (!$role_id)
		{
			$this->setError(JText::_('Missing argument'));
			return false;
		}

		$role_id = intval($role_id);

		$query = "SELECT r.id, r.type, r.alias
					FROM #__resource_types AS r
					LEFT JOIN #__author_role_types AS rt ON r.id=rt.type_id
					WHERE rt.role_id=" . $this->_db->Quote($role_id) . "
					ORDER BY r.type ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Associated types with a role
	 *
	 * @param      integer $role_id Role ID
	 * @param      array   $current Current types associated
	 * @return     boolean True on success
	 */
	public function setTypesForRole($role_id=null, $current=null)
	{
		if ($role_id === null)
		{
			$role_id = $this->id;
		}

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'role.type.php');

		$rt = new ResourcesContributorRoleType($this->_db);

		return $rt->setTypesForRole($role_id, $current);
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $oid Record to delete
	 * @return     boolean True on success
	 */
	public function delete($oid=null)
	{
		if ($oid === null)
		{
			$oid = $this->id;
		}

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'role.type.php');

		$rt = new ResourcesContributorRoleType($this->_db);
		if (!$rt->deleteForRole($oid))
		{
			$this->setError($rt->getError());
			return false;
		}

		return parent::delete($oid);
	}
}

