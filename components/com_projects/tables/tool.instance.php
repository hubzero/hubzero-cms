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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Tables;

/**
 * Project Tool Instance class
 *
 */
class ToolInstance extends  \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_instances', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->id && trim($this->parent_name) == '')
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_NO_INSTANCE_PARENT_NAME'));
			return false;
		}

		if (!$this->id && trim($this->instance) == '')
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_NO_INSTANCE'));
			return false;
		}

		return true;
	}

	/**
	 * Load from instance
	 *
	 * @param      string $instance
	 * @return     object or false
	 */
	public function loadFromInstanceName($instance = NULL)
	{
		if ($instance === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl AS v WHERE v.instance=" . $this->_db->Quote($instance) . " LIMIT 1";

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
	 * Load from parent
	 *
	 * @param      string $parent    Parent tool ID or name
	 * @return     object or false
	 */
	public function loadFromParent($parent = NULL, $version = 'dev')
	{
		if ($parent === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl AS v WHERE ";
		$query .= is_numeric($parent) ? "v.parent_id=" . $this->_db->Quote($parent) : "v.parent_name=" . $this->_db->Quote($parent);
		if ($version == 'dev')
		{
			$query .= " AND v.state=3 ";
		}
		if ($version == 'published')
		{
			$query .= " AND v.state=1 ORDER BY v.id DESC ";
		}
		elseif (is_numeric($version))
		{
			$query .= " AND v.revision=" . $version;
		}
		else
		{
			// Get latest
			$query .= " ORDER BY v.id DESC";
		}
		$query .= " LIMIT 1";

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
	 * Get all instances
	 *
	 * @param      integer $includedev
	 * @return     object
	 */
	public function getAll($includedev = 1)
	{
		$sql = "SELECT * FROM $this->_tbl";
		if (!$includedev)
		{
			$sql.= " WHERE state!='3'";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get instances
	 *
	 * @param      string 	$parent_name
	 * @param      int 		$parent_id
	 * @param      int 		$exclude_dev
	 * @return     mixed
	 */
	public function getInstances($parent_name = NULL, $parent_id = NULL, $exclude_dev = 0 )
	{
		if ($parent_name === NULL)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === NULL)
		{
			$parent_id = $this->parent_id;
		}
		if (!$parent_id && !$parent_name)
		{
			return false;
		}

		$query  = "SELECT v.* ";
		$query .= "FROM $this->_tbl as v WHERE 1=1 AND ";
		$query .= $parent_id ? "v.parent_id=" . $this->_db->Quote($parent_id)
			: "v.parent_name=" . $this->_db->Quote($parent_name);
		$query .= $exclude_dev ? ' AND v.state != 3' : '';
		$query .= " ORDER BY v.revision DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get Dev Instance Property
	 *
	 * @param      string $parent_name
	 * @param      string $property
	 * @return     object
	 */
	public function getDevInstanceProperty($parent_name, $property)
	{
		$query  = "SELECT " . $this->_db->Quote($property) . " FROM $this->_tbl
			WHERE parent_name=" . $this->_db->Quote($parent_name) . " AND state=3 LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Update Parent Name
	 *
	 * @param      string $parent_id
	 * @param      string $newname
	 * @return     mixed
	 */
	public function updateParentName($parent_id = NULL, $newname = NULL)
	{
		if ($newname === NULL || $parent_id === NULL )
		{
			return false;
		}

		$query = "UPDATE $this->_tbl SET parent_name =" . $this->_db->Quote($newname)
				. " WHERE parent_id =" . $this->_db->Quote($parent_id);
		$this->_db->setQuery( $query );
		if ($this->_db->query())
		{
			return true;
		}

		return false;
	}
}
