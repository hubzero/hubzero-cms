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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Course asset groups table class
 */
class CoursesTableMember extends JTable
{
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $offering_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $role_id = NULL;

	/**
	 * mediumtext
	 * 
	 * @var string
	 */
	var $permissions = NULL;

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offering_members', 'offering_id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($uid=null, $oid=NULL)
	{
		if ($uid === NULL || $oid === NULL) 
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `user_id`=" . $this->_db->Quote((int) $uid) . " AND `offering_id`=" . $this->_db->Quote((int) $oid);

		$this->_db->setQuery($query);
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
	 * Validate data
	 * 
	 * @return     boolean True on success
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(JText::_('Missing offering ID'));
			return false;
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(JText::_('Missing user ID'));
			return false;
		}

		$this->role_id = intval($this->role_id);

		return true;
	}

	/**
	 * Store data
	 * 
	 * @return     boolean True on success
	 */
	public function store()
	{
		return false;
	}

	/**
	 * Save data
	 * 
	 * @return     boolean True on success
	 */
	public function save()
	{
		return false;
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS m 
				LEFT JOIN #__courses_roles AS r ON r.id=m.role_id";

		$where = array();
		if (isset($filters['offering_id']))
		{
			$where[] = "m.`offering_id`=" . $this->_db->Quote(intval($filters['offering_id']));
		}
		if (isset($filters['user_id']))
		{
			$where[] = "m.`user_id`=" . $this->_db->Quote(intval($filters['user_id']));
		}
		if (isset($filters['role_id']))
		{
			$where[] = "m.`role_id`=" . $this->_db->Quote(intval($filters['role_id']));
		}
		if (isset($filters['role']))
		{
			$where[] = "r.`alias`=" . $this->_db->Quote($filters['role']);
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
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*) ";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT m.*, r.alias AS role_alias, r.title AS role, r.permissions AS role_permissions ";
		$query .= $this->_buildquery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}