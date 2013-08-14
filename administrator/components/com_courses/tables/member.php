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
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

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
	var $course_id = NULL;

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
	var $section_id = NULL;

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
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $enrolled = NULL;

	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $student = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $first_visit = NULL;

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_members', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($uid=null, $cid=NULL, $oid=NULL, $sid=NULL, $student=NULL)
	{
		if ($uid === NULL) 
		{
			return false;
		}

		if (is_null($cid) && is_null($oid) && !is_null($sid) && is_numeric($sid))
		{
			return $this->loadBySection($uid, $sid);
		}
		else if ($cid === NULL)
		{
			return parent::load($uid);
		}

		$query = "SELECT * 
				FROM $this->_tbl 
				WHERE ";
		if ($student !== null)
		{
			$where[] = "`student`=" . $this->_db->Quote((int) $student);
		}
		$where[] = "`user_id`=" . $this->_db->Quote((int) $uid);
		$where[] = "`course_id`=" . $this->_db->Quote((int) $cid);
		if ($oid !== null)
		{
			//$where[] = "`offering_id` IN (0, " . $this->_db->Quote((int) $cid) . ")";
			$where[] = "`offering_id` IN (0," . (int) $oid . ")"; //$this->_db->Quote((int) $oid);
		}
		if ($sid !== null)
		{
			//$where[] = "`section_id` IN (0, " . $this->_db->Quote((int) $sid) . ")";
			$where[] = "`section_id` IN (0," . (int) $sid . ")"; //$this->_db->Quote((int) $sid);
		}
		$query .= implode(" AND ", $where) . " ORDER BY student ASC, section_id ASC, offering_id ASC LIMIT 1";

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
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function loadBySection($uid=null, $oid=NULL)
	{
		if ($uid === NULL || $oid === NULL) 
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `user_id`=" . $this->_db->Quote((int) $uid) . " AND `section_id`=" . $this->_db->Quote((int) $oid) . " LIMIT 1";

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
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function loadByOffering($uid=null, $oid=NULL)
	{
		if ($uid === NULL || $oid === NULL) 
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `user_id`=" . $this->_db->Quote((int) $uid) . " AND `offering_id`=" . $this->_db->Quote((int) $oid) . " LIMIT 1";

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
		$this->student = intval($this->student);

		if ($this->student)
		{
			$this->offering_id = intval($this->offering_id);
			if (!$this->offering_id)
			{
				$this->setError(JText::_('Missing offering ID'));
				return false;
			}

			$this->section_id = intval($this->section_id);
			if (!$this->section_id)
			{
				$this->setError(JText::_('Missing section ID'));
				return false;
			}
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(JText::_('Missing user ID'));
			return false;
		}

		$this->course_id = intval($this->course_id);
		if (!$this->course_id)
		{
			$this->setError(JText::_('Missing course ID'));
			return false;
		}

		$this->role_id = intval($this->role_id);

		if (!$this->id)
		{
			$this->enrolled = date('Y-m-d H:i:s', time());
		}

		return true;
	}

	/**
	 * Store data
	 * 
	 * @return     boolean True on success
	 */
	/*public function store()
	{
		return false;
	}*/

	/**
	 * Save data
	 * 
	 * @return     boolean True on success
	 */
	/*public function save()
	{
		$this->_db->setQuery("UPDATE $this->_tbl 
			SET `role_id`=" . $this->_db->Quote($this->role_id) . ", `permissions`=" . $this->_db->Quote($this->permissions) . " 
			WHERE `offering_id`=" . $this->_db->Quote($this->offering_id) . " 
			AND `user_id`=" . $this->_db->Quote($this->user_id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}*/

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
		if (isset($filters['course_id']) && $filters['course_id'])
		{
			$where[] = "m.`course_id`=" . $this->_db->Quote(intval($filters['course_id']));
		}
		if (isset($filters['offering_id']))
		{
			if (is_array($filters['section_id']))
			{
				$filters['offering_id'] = array_map('intval', $filters['offering_id']);
				$where[] = "m.`offering_id` IN (" . implode(",", $filters['offering_id']) . ")";
			}
			else
			{
				$where[] = "m.`offering_id`=" . $this->_db->Quote(intval($filters['offering_id']));
			}
		}
		if (isset($filters['section_id']))
		{
			if (is_array($filters['section_id']))
			{
				$filters['section_id'] = array_map('intval', $filters['section_id']);
				$where[] = "m.`section_id` IN (" . implode(",", $filters['section_id']) . ")";
			}
			else
			{
				$where[] = "m.`section_id`=" . $this->_db->Quote(intval($filters['section_id']));
			}
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
			if (substr($filters['role'], 0, 1) == '!')
			{
				$where[] = "r.`alias`!=" . $this->_db->Quote(ltrim($filters['role'], '!'));
			}
			else
			{
				$where[] = "r.`alias`=" . $this->_db->Quote($filters['role']);
			}
		}
		if (isset($filters['student']))
		{
			$where[] = "m.`student`=" . $this->_db->Quote(intval($filters['student']));
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
		$query  = "SELECT m.*, r.alias AS role_alias, r.title AS role_title, r.permissions AS role_permissions ";
		/*if (isset($filters['offering_id']))
		{
			$query .= "(SELECT cm.user_id FROM #__courses_managers AS cm JOIN #__courses_offerings AS co ON cm.course_id=co.course_id WHERE cm.user_id=m.user_id AND co.id=" . $this->_db->Quote(intval($filters['offering_id'])) . ")";
		}
		else
		{
			$query .= "NULL";
		}
		$query .= " AS course_manager ";*/
		$query .= $this->_buildquery($filters);

		if (isset($filters['sort']))
		{
			$query .= " ORDER BY " . $filters['sort'];
		}
		if (isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
			if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " " . $filters['sort_Dir'];
		}

		if ((isset($filters['start']) && is_numeric($filters['start']) && $filters['start'] >= 0) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}