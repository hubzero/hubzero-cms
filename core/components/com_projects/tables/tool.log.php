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

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project tool logs
 */
class ToolLog extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_logs', 'id', $db);
	}

	/**
	 * Get item history
	 *
	 *
	 * @param   string   $parent_name
	 * @param   integer  $parent_id
	 * @param   integer  $instance_id
	 * @param   array    $filters
	 * @return  object   list
	 */
	public function getHistory($parent_name = null, $parent_id = null, $instance_id = 0, $filters = array())
	{
		if ($parent_name === null)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === null)
		{
			$parent_id = $this->parent_id;
		}
		if ($instance_id === null)
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
	 * @param   integer  $instance_id
	 * @param   string   $parent_name
	 * @param   integer  $parent_id
	 * @param   integer  $status_changed
	 * @return  mixed    object or null
	 */
	public function getLastUpdate($instance_id = 0, $parent_name = null, $parent_id = null, $status_changed = 1)
	{
		if ($parent_name === null)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === null)
		{
			$parent_id = $this->parent_id;
		}
		if ($instance_id === null)
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
		return $result ? $result[0] : null;
	}

	/**
	 * Update parent name
	 *
	 * @param   string   $parent_id
	 * @param   string   $newname
	 * @return  boolean  True on success
	 */
	public function updateParentName($parent_id = null, $newname = null)
	{
		if ($newname === null || $parent_id === null)
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
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $aid     Activity id
	 * @return  mixed    Return string or null
	 */
	public function getLog($toolid = 0, $aid = 0)
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
