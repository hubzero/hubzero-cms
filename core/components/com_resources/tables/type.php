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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Tables;

/**
 * Table class for resource type
 */
class Type extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_types', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->type = trim($this->type);
		if ($this->type == '')
		{
			$this->setError(\Lang::txt('Your resource type must contain text.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = $this->type;
		}
		$this->alias = $this->normalize($this->alias);

		$this->contributable = $this->contributable ?: 0;

		return true;
	}

	/**
	 * Returns a reference to this object
	 *
	 * @param   integer  $id  The record to load
	 * @return  object
	 */
	public static function getRecordInstance($id)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$id]))
		{
			$db = \App::get('db');

			$tbl = new self($db);
			$tbl->load($id);

			$instances[$id] = $tbl;
		}

		return $instances[$id];
	}

	/**
	 * Strip disallowed characters and make lowercase
	 *
	 * @param   string  $txt
	 * @return  string
	 */
	public function normalize($txt)
	{
		return preg_replace("/[^a-zA-Z0-9\-_]/", '', strtolower($txt));
	}

	/**
	 * Get all the major types
	 *
	 * @return  array
	 */
	public function getMajorTypes()
	{
		return $this->getTypes(27);
	}

	/**
	 * Get a count of all records for a specific category (optional)
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  integer
	 */
	public function getAllCount($filters=array())
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		if (isset($filters['category']) && $filters['category'])
		{
			$query .= " WHERE category=" . $this->_db->quote($filters['category']);
		}
		else
		{
			$query .= " WHERE category!=0 ";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get all records for a specific category (optional)
	 * Different from the method below in that it adds limit for paging
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getAllTypes($filters=array())
	{
		$query  = "SELECT * FROM $this->_tbl ";
		if (isset($filters['category']) && $filters['category'])
		{
			$query .= "WHERE category=" . $this->_db->quote($filters['category']) . " ";
		}
		else
		{
			$query .= "WHERE category!=0 ";
		}
		$query .= "ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'] . " ";
		$query .= "LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all records for a specific category
	 *
	 * @param   integer  $cat  Category ID
	 * @return  array
	 */
	public function getTypes($cat='0')
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE category=" . $this->_db->quote($cat) . " ORDER BY type");
		return $this->_db->loadObjectList();
	}

	/**
	 * Check if a type is being used
	 *
	 * @param   integer  $id  Record ID
	 * @return  integer
	 */
	public function checkUsage($id=NULL)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			return false;
		}

		include_once(__DIR__ . DS . 'resource.php');

		$r = new Resource($this->_db);

		$this->_db->setQuery("SELECT count(*) FROM $r->_tbl WHERE type=" . $this->_db->quote($id) . " OR logical_type=" . $this->_db->quote($id));
		return $this->_db->loadResult();
	}

	/**
	 * Get all the roles associated with a specific type
	 *
	 * @param   integer  $type_id  Type ID
	 * @return  array
	 */
	public function getRolesForType($type_id=null)
	{
		if ($type_id === null)
		{
			$type_id = $this->id;
		}

		if ($type_id === null)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}

		$type_id = intval($type_id);

		$query = "SELECT r.id, r.title, r.alias
					FROM `#__author_roles` AS r
					JOIN `#__author_role_types` AS rt ON r.id=rt.role_id AND rt.type_id=" . $this->_db->quote($type_id) . "
					ORDER BY r.title ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $oid  Record ID
	 * @return  boolean  True on success
	 */
	public function delete($oid=null)
	{
		if ($oid === null)
		{
			$oid = $this->id;
		}

		include_once(__DIR__ . DS . 'contributor' . DS . 'roletype.php');

		$rt = new Contributor\RoleType($this->_db);
		if (!$rt->deleteForType($oid))
		{
			$this->setError($rt->getError());
			return false;
		}

		return parent::delete($oid);
	}
}

