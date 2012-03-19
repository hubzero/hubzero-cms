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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourcesContributorRole'
 * 
 * Long description (if any) ...
 */
class ResourcesContributorRoleType extends JTable
{
	/**
	 * Description for 'subid'
	 * 
	 * @var unknown
	 */
	var $id    = NULL;  // @var int(11) Primary Key

	/**
	 * Description for 'authorid'
	 * 
	 * @var unknown
	 */
	var $role_id = NULL;  // @var int(11) Primary Key

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
	var $type_id = NULL;  // @var int(11)

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__author_role_types', 'id', $db);
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (!$this->role_id) 
		{
			$this->setError(JText::_('Please provide a role ID.'));
			return false;
		}

		if (!$this->type_id) 
		{
			$this->setError(JText::_('Please provide a type ID.'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
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
					FROM #__author_roles AS r
					LEFT JOIN #__author_role_types AS rt ON r.id=rt.role_id
					WHERE rt.type_id=$type_id
					ORDER BY r.title ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getTypesForRole($role_id=null)
	{
		if (!$role_id) 
		{
			$this->setError(JText::_('Missing argument'));
			return false;
		}
		
		$role_id = intval($role_id);
		
		$query = "SELECT r.id, r.type, r.alias 
					FROM #__resource_types AS r
					LEFT JOIN #__author_role_types AS rt ON r.id=rt.type_id
					WHERE rt.role_id=$role_id
					ORDER BY r.type ASC";
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function setTypesForRole($role_id=null, $current=null)
	{
		if (!$role_id) 
		{
			$this->setError(JText::_('Missing argument'));
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
		$add = array();
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
			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE role_id='$role_id' AND type_id IN ($remove)");
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
				$rt = new ResourcesContributorRoleType($this->_db);
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
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteForRole($role_id=null)
	{
		if ($role_id === null)
		{
			$role_id = $this->role_id;
		}
		
		if (!$role_id) 
		{
			$this->setError(JText::_('Missing argument'));
			return false;
		}
		$role_id = intval($role_id);
		
		// Remove any types in the remove list
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE role_id='$role_id'");
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteForType($type_id=null)
	{
		if ($type_id === null)
		{
			$type_id = $this->type_id;
		}
		
		if (!$type_id) 
		{
			$this->setError(JText::_('Missing argument'));
			return false;
		}
		$type_id = intval($type_id);
		
		// Remove any types in the remove list
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE type_id='$type_id'");
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

