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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Tables\Contributor;

/**
 * Resources class for role type
 */
class RoleType extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__author_role_types', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (!$this->role_id)
		{
			$this->setError(\Lang::txt('Please provide a role ID.'));
		}

		if (!$this->type_id)
		{
			$this->setError(\Lang::txt('Please provide a type ID.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get all roles for a specific type
	 *
	 * @param   integer  $type_id  Type ID
	 * @return  array
	 */
	public function getRolesForType($type_id=null)
	{
		if ($type_id === null)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}

		$type_id = intval($type_id);

		$query = "SELECT r.id, r.title, r.alias
					FROM `#__author_roles` AS r
					JOIN `#__author_role_types` AS rt ON r.id=rt.role_id
					WHERE rt.type_id=" . $this->_db->quote($type_id) . "
					ORDER BY r.title ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all types for a specific role
	 *
	 * @param      integer $role_id Role ID
	 * @return     array
	 */
	public function getTypesForRole($role_id=null)
	{
		if (!$role_id)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}

		$role_id = intval($role_id);

		$query = "SELECT r.id, r.type, r.alias
					FROM `#__resource_types` AS r
					LEFT JOIN `#__author_role_types` AS rt ON r.id=rt.type_id
					WHERE rt.role_id=" . $this->_db->quote($role_id) . "
					ORDER BY r.type ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Set types for a role
	 *
	 * @param   integer  $role_id  Role ID
	 * @param   array    $current  List of types assigned to role
	 * @return  boolean  True on success, False on errors
	 */
	public function setTypesForRole($role_id=null, $current=null)
	{
		if (!$role_id)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}
		$role_id = intval($role_id);

		// Get an array of all the previous types
		$old = array();
		$types = $this->getTypesForRole($role_id);
		if ($types)
		{
			foreach ($types as $item)
			{
				$old[] = $item->id;
			}
		}

		// Run through the $current array and determine if
		// each item is new or not
		$keep = array();
		$add  = array();
		if (is_array($current))
		{
			foreach ($current as $bit)
			{
				if (!in_array($bit, $old))
				{
					$add[] = intval($bit);
				}
				else
				{
					$keep[] = intval($bit);
				}
			}
		}

		$remove = array_diff($old, $keep);

		// Remove any types in the remove list
		if (count($remove) > 0)
		{
			$remove = implode(',', $remove);
			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE role_id=" . $this->_db->quote($role_id) . " AND type_id IN ($remove)");
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// Add any types not in the OLD list
		if (count($add) > 0)
		{
			foreach ($add as $type)
			{
				$rt = new self($this->_db);
				$rt->role_id = $role_id;
				$rt->type_id = $type;
				if ($rt->check())
				{
					$rt->store();
				}
			}
		}

		return true;
	}

	/**
	 * Delete entries for a specific role
	 *
	 * @param   integer  $role_id  Role ID
	 * @return  boolean  True on success, False on error
	 */
	public function deleteForRole($role_id=null)
	{
		if ($role_id === null)
		{
			$role_id = $this->role_id;
		}

		if (!$role_id)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}
		$role_id = intval($role_id);

		// Remove any types in the remove list
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE role_id=" . $this->_db->quote($role_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete entries for a specific type
	 *
	 * @param   integer  $type_id  Type ID
	 * @return  boolean  True on success, False on error
	 */
	public function deleteForType($type_id=null)
	{
		if ($type_id === null)
		{
			$type_id = $this->type_id;
		}

		if (!$type_id)
		{
			$this->setError(\Lang::txt('Missing argument'));
			return false;
		}
		$type_id = intval($type_id);

		// Remove any types in the remove list
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE type_id=" . $this->_db->quote($type_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

