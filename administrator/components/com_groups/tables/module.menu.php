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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups Module table
 */
class GroupsTableModuleMenu extends JTable
{
	var $moduleid = null;
	var $pageid   = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */

	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_modules_menu', 'id', $db);
	}

	/**
	 * Get pages of module menu
	 *
	 * @return     array
	 */
	public function getMenu( $filters = array() )
	{
		if (!isset($filters['moduleid']))
		{
			return;
		}

		$sql = "SELECT * FROM {$this->_tbl} as m WHERE m.`moduleid`=" . $this->_db->quote( $filters['moduleid'] );
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}


	/**
	 * Create menus matching module ID
	 *
	 * @param      $menus    Array of menus
	 * @return     BOOL
	 */
	public function createMenus( $moduleid, $pages = array() )
	{
		// create array of values to insert
		$values = array();
		foreach ($pages as $pageid)
		{
			$values[] = '('.$this->_db->quote($moduleid).','.$this->_db->quote($pageid).')';
		}

		// make sure we have at least one menu item
		if (count($values) < 1)
		{
			$this->setError('Module must have at least one menu assignment.');
			return false;
		}

		// add menu items for each page
		$sql = "INSERT INTO {$this->_tbl}(`moduleid`,`pageid`) VALUES " . implode(',', $values);
		$this->_db->setQuery($sql);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}

		// everything went smoothly
		return true;
	}

	/**
	 * Delete menus matching module ID
	 *
	 * @param      $moduleid    Module ID
	 * @return     BOOL
	 */
	public function deleteMenus( $moduleid )
	{
		// make sure we have a module id
		if (!$moduleid)
		{
			$this->setError('You must supply a module ID.');
			return false;
		}

		// delete any menu items matching module id
		$sql = "DELETE FROM {$this->_tbl} WHERE `moduleid`=" . $this->_db->quote( $moduleid );
		$this->_db->setQuery( $sql );
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}

		// everything went smoothly
		return true;
	}
}