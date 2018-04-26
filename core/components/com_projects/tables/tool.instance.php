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
 * Project Tool Instance class
 */
class ToolInstance extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_instances', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
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
	 * @param   string  $instance
	 * @return  mixed   object or false
	 */
	public function loadFromInstanceName($instance = null)
	{
		if ($instance === null)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl AS v WHERE v.instance=" . $this->_db->quote($instance) . " LIMIT 1";

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
	 * @param   string  $parent  Parent tool ID or name
	 * @return  mixed   object or false
	 */
	public function loadFromParent($parent = null, $version = 'dev')
	{
		if ($parent === null)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl AS v WHERE ";
		$query .= is_numeric($parent) ? "v.parent_id=" . $this->_db->quote($parent) : "v.parent_name=" . $this->_db->quote($parent);
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
	 * @param   integer  $includedev
	 * @return  object
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
	 * @param   string  $parent_name
	 * @param   int     $parent_id
	 * @param   int     $exclude_dev
	 * @return  mixed
	 */
	public function getInstances($parent_name = null, $parent_id = null, $exclude_dev = 0)
	{
		if ($parent_name === null)
		{
			$parent_name = $this->parent_name;
		}
		if ($parent_id === null)
		{
			$parent_id = $this->parent_id;
		}
		if (!$parent_id && !$parent_name)
		{
			return false;
		}

		$query  = "SELECT v.* ";
		$query .= "FROM $this->_tbl as v WHERE 1=1 AND ";
		$query .= $parent_id ? "v.parent_id=" . $this->_db->quote($parent_id)
			: "v.parent_name=" . $this->_db->quote($parent_name);
		$query .= $exclude_dev ? ' AND v.state != 3' : '';
		$query .= " ORDER BY v.revision DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get Dev Instance Property
	 *
	 * @param   string  $parent_name
	 * @param   string  $property
	 * @return  object
	 */
	public function getDevInstanceProperty($parent_name, $property)
	{
		$query  = "SELECT " . $this->_db->quote($property) . " FROM $this->_tbl
			WHERE parent_name=" . $this->_db->quote($parent_name) . " AND state=3 LIMIT 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Update Parent Name
	 *
	 * @param   string  $parent_id
	 * @param   string  $newname
	 * @return  mixed
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
}
