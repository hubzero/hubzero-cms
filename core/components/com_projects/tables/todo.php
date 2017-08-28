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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	 If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	  hubzero-cms
 * @author	  Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	  http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

/**
 * Table class for project to-do's
 */
class Todo extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_todo', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $projectid
	 * @param   integer  $id
	 * @return  boolean  False or object
	 */
	public function loadTodo($projectid = null, $id = 0)
	{
		if ($projectid == null or $id == 0)
		{
			return false;
		}

		$this->_db->setQuery(
			"SELECT * FROM $this->_tbl WHERE projectid=" . $this->_db->quote($projectid) . " AND id=" . $this->_db->quote($id) . " LIMIT 1"
		);

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
	 * Get records
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @param   integer  $id
	 * @return  mixed    integer or null
	 */
	public function getTodos($projectid = null, $filters = array(), $id = 0)
	{
		$projects = isset($filters['projects']) ? $filters['projects'] : array($projectid);

		if (empty($projects))
		{
			return false;
		}

		$count      = isset($filters['count']) ? $filters['count'] : 0;
		$limit      = isset($filters['limit']) ? $filters['limit'] : 0;
		$limitstart = isset($filters['start']) ? $filters['start'] : 0;
		$color      = isset($filters['todolist']) ? $filters['todolist'] : '';
		$assignedto = isset($filters['assignedto']) ? $filters['assignedto'] : '';
		$state      = isset($filters['state']) ? intval($filters['state']) : 0;
		$activityid = isset($filters['activityid']) ? intval($filters['activityid']) : 0;

		$query  = "SELECT ";
		$query .= $count ? " COUNT(*) " : "p.*, xp.name AS authorname, xpp.name AS assignedname, xppp.name AS closedbyname, IF (p.duedate ='0000-00-00 00:00:00' OR p.duedate IS null, 0, 1) as due ";
		if (!$count)
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_comments as c WHERE c.itemid=p.id AND c.tbl='todo' AND c.state!=2) as comments ";
		}
		$query .= "FROM $this->_tbl AS p  ";
		if (!$count)
		{
			$query .= "JOIN #__users AS xp ON xp.id=p.created_by ";
			$query .= "LEFT JOIN #__users AS xpp ON xpp.id=p.assigned_to ";
			$query .= "LEFT JOIN #__users AS xppp ON xppp.id=p.closed_by ";
		}

		foreach ($projects as $k => $project)
		{
			$projects[$k] = $this->_db->quote($project);
		}
		$query .= " WHERE p.projectid IN (" . implode(',', $projects) . ") ";

		if ($id)
		{
			$query .= " AND p.id = " . $this->_db->quote($id) ." ";
		}
		else
		{
			$query .= $color ? " AND p.color=" . $this->_db->quote($color) : " ";
			$query .= $assignedto ? " AND p.assigned_to=" . $this->_db->quote($assignedto) : " ";
			$query .= " AND p.state=" . $this->_db->quote($state);
			if ($activityid)
			{
				$query .= " AND p.activityid=" . $this->_db->quote($activityid);
			}
		}

		if (!$count)
		{
			$sort = '';
			$sortby  = isset($filters['sortby']) ? $filters['sortby'] : 'priority';
			$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

			switch ($sortby)
			{
				case 'due':
					$sort .= 'due DESC, p.duedate ' . $sortdir;
					break;

				case 'complete':
					$sort .= 'p.closed ' . $sortdir;
					break;

				case 'list':
					$sort .= 'p.color ' . $sortdir;
					break;

				case 'content':
					$sort .= 'p.content ' . $sortdir;
					break;

				case 'project':
					$sort .= 'p.projectid ' . $sortdir;
					break;

				case 'priority':
				default:
					$sort .= 'p.priority ' . $sortdir;
					break;
			}

			$query .= "ORDER BY $sort ";
			if (isset ($limit) && $limit!=0)
			{
				$query .= " LIMIT " . $limitstart . ", " . $limit;
			}
		}

		$this->_db->setQuery($query);
		return $count ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}

	/**
	 * Get lists
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @return  mixed    object or null
	 */
	public function getTodoLists($projectid = null, $filters = array())
	{
		if ($projectid == null)
		{
			return false;
		}

		$query  = "SELECT ";
		$query .= isset($filters['count']) && $filters['count'] == 1 ? " COUNT(*) " : "DISTINCT todolist, color ";
		$query .= "FROM $this->_tbl ";
		$query .= "WHERE projectid =" . $this->_db->quote($projectid) . " AND todolist IS NOT null AND color IS NOT null AND todolist != '' AND color != '' ";
		$query .= "ORDER BY todolist";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get list name by color
	 *
	 * @param   integer  $projectid
	 * @param   string   $color
	 * @return  mixed    string or null
	 */
	public function getListName($projectid = null, $color = '')
	{
		if ($projectid == null or $color == '')
		{
			return false;
		}

		$query  = "SELECT todolist FROM $this->_tbl ";
		$query .= "WHERE projectid =" . $this->_db->quote($projectid) . " AND color = " . $this->_db->quote($color);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete list
	 *
	 * @param   integer  $projectid
	 * @param   string   $color
	 * @param   boolean  $all
	 * @param   boolean  $permanent
	 * @return  boolean  True on success
	 */
	public function deleteList($projectid = null, $color = '', $all = 0, $permanent = 0)
	{
		if ($projectid == null or $color == '')
		{
			return false;
		}
		if ($all)
		{
			$query = "DELETE FROM $this->_tbl WHERE projectid =" . $this->_db->quote($projectid) . " AND color = " . $this->_db->quote($color);
		}
		else
		{
			$query = "UPDATE $this->_tbl SET color = '', todolist = '' WHERE projectid =" . $this->_db->quote($projectid) . " AND color =" . $this->_db->quote($color);
		}

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get last item order
	 *
	 * @param   integer  $projectid
	 * @return  integer
	 */
	public function getLastOrder($projectid = null)
	{
		if ($projectid === null)
		{
			return false;
		}

		$query  = "SELECT priority FROM $this->_tbl ";
		$query .= "WHERE projectid =" . $this->_db->quote($projectid) . " ORDER BY priority DESC LIMIT 1 ";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete items
	 *
	 * @param   integer  $projectid
	 * @param   string   $todolist
	 * @param   boolean  $permanent
	 * @return  void
	 */
	public function deleteTodos($projectid, $todolist = '', $permanent = 0)
	{
		if ($projectid == null)
		{
			return false;
		}
		if ($permanent)
		{
			$query = "DELETE FROM $this->_tbl WHERE projectid=" . $this->_db->quote($projectid);
		}
		else
		{
			$query = "UPDATE $this->_tbl SET state = 2 WHERE projectid =" . $this->_db->quote($projectid);
		}
		$query .= $todolist ? " AND color=" . $this->_db->quote($todolist) : "";

		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Delete item
	 *
	 * @param   integer  $projectid
	 * @param   integer  $todoid
	 * @param   boolean  $permanent
	 * @return  boolean  True if success
	 */
	public function deleteTodo($projectid, $todoid = 0, $permanent = 0)
	{
		if ($projectid == null)
		{
			return false;
		}

		if ($permanent)
		{
			$query = "DELETE FROM $this->_tbl WHERE projectid=" . $this->_db->quote($projectid);
		}
		else
		{
			$query = "UPDATE $this->_tbl SET state = 2 WHERE projectid =" . $this->_db->quote($projectid);
		}

		$query .= " AND id=" . $this->_db->quote($todoid);
		$this->_db->setQuery($query);
		$this->_db->query();
		return true;
	}
}
