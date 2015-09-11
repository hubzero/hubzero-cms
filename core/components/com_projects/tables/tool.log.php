<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Tables;

/**
 * Table class for project tool logs
 */
class ToolLog extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_logs', 'id', $db);
	}

	/**
	 * Get item history
	 *
	 *
	 * @param      string $parent_name
	 * @param      string $parent_id
	 * @param      string $instance_id
	 * @param      string $status_changed
	 * @param      string $sortby
	 * @param      string $sortdir
	 * @return     object list
	 */
	public function getHistory($parent_name = NULL,
		$parent_id = NULL, $instance_id = 0, $filters = array())
	{
		if ($parent_name === NULL)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === NULL)
		{
			$parent_id = $this->parent_id;
		}
		if ($instance_id === NULL)
		{
			$instance_id = $this->instance_id;
		}
		if (!$parent_id && !$parent_name && !$instance_id)
		{
			return false;
		}
		$status_changed = isset($filters['status_changed']) ? $filters['status_changed'] : 0;
		$sortby         = !empty($filters['sortby']) ? $filters['sortby'] : 'L.recorded';
		$sortdir        = !empty($filters['sortdir']) ? $filters['sortdir'] : 'DESC';
		$admin          = isset($filters['admin']) ? $filters['admin'] : 0;

		$query  = "SELECT L.*, x.name as actor_name, x.username as username, x.picture ";
		$query .= "FROM $this->_tbl as L ";
		$query .= "JOIN #__xprofiles as x ON x.uidNumber=L.actor ";
		$query .= "WHERE ";
		if ($instance_id)
		{
			$query .= "L.instance_id=" . $this->_db->quote($instance_id);
		}
		elseif ($parent_name)
		{
			$query .= "L.parent_name=" . $this->_db->quote($parent_name);
		}
		elseif ($parent_id)
		{
			$query .= "L.parent_id=" . $this->_db->quote($parent_id);
		}

		$query .= $status_changed ? ' AND L.status_changed = 1' : '';
		$query .= $admin
			? ' AND (L.admin = 1 OR (L.admin = 0 AND access = 0 )) '
			: ' AND (L.admin = 0 OR (L.admin = 1 AND access = 0 )) ';
		$query .= " ORDER BY " . $sortby . " " . $sortdir;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get last update
	 *
	 * @param      string $instance_id
	 * @param      string $parent_name
	 * @param      string $parent_id
	 * @param      string $status_changed
	 * @return     object or NULL
	 */
	public function getLastUpdate(
		$instance_id = 0, $parent_name = NULL,
		$parent_id = NULL, $status_changed = 1)
	{
		if ($parent_name === NULL)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === NULL)
		{
			$parent_id = $this->parent_id;
		}
		if ($instance_id === NULL)
		{
			$instance_id = $this->instance_id;
		}

		$query  = "SELECT L.*, x.name as actor_name, x.username as username ";
		$query .= "FROM $this->_tbl as L ";
		$query .= "JOIN #__xprofiles as x ON x.uidNumber=L.actor ";
		$query .= "WHERE ";
		if ($instance_id)
		{
			$query .= "L.instance_id=" . $this->_db->quote($instance_id);
		}
		elseif ($parent_name)
		{
			$query .= "L.parent_name=" . $this->_db->quote($parent_name);
		}
		elseif ($parent_id)
		{
			$query .= "L.parent_id=" . $this->_db->quote($parent_id);
		}

		$query .= $status_changed ? ' AND L.status_changed = 1' : '';
		$query .= " ORDER BY L.recorded DESC LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : NULL;
	}

	/**
	 * Update parent name
	 *
	 * @param      string $parent_id
	 * @param      string $newname
	 * @return     True on success
	 */
	public function updateParentName($parent_id = NULL, $newname = NULL)
	{
		if ($newname === NULL || $parent_id === NULL )
		{
			return false;
		}

		$query = "UPDATE $this->_tbl SET parent_name =" . $this->_db->quote($newname)
				. " WHERE parent_id =" . $this->_db->quote($parent_id);
		$this->_db->setQuery( $query );
		if ($this->_db->query())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get log
	 *
	 * @param      integer $toolid 	Project tool id
	 * @param      integer $aid		Activity id
	 * @return     mixed Return string or NULL
	 */
	public function getLog( $toolid = 0, $aid = 0)
	{
		if (!intval($toolid) || !intval($aid))
		{
			return false;
		}

		$query  = "SELECT log ";
		$query .= "FROM $this->_tbl ";
		$query .= "WHERE parent_id=" . $this->_db->quote($toolid) . " AND project_activity_id=" . $this->_db->quote($aid);

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
