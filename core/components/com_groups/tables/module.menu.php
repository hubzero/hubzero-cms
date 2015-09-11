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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

/**
 * Groups Module table
 */
class ModuleMenu extends \JTable
{
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
	public function getMenu($filters = array())
	{
		if (!isset($filters['moduleid']))
		{
			return;
		}

		$sql = "SELECT * FROM {$this->_tbl} as m WHERE m.`moduleid`=" . $this->_db->quote($filters['moduleid']);
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}


	/**
	 * Create menus matching module ID
	 *
	 * @param      $menus    Array of menus
	 * @return     BOOL
	 */
	public function createMenus($moduleid, $pages = array())
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
	public function deleteMenus($moduleid)
	{
		// make sure we have a module id
		if (!$moduleid)
		{
			$this->setError('You must supply a module ID.');
			return false;
		}

		// delete any menu items matching module id
		$sql = "DELETE FROM {$this->_tbl} WHERE `moduleid`=" . $this->_db->quote($moduleid);
		$this->_db->setQuery($sql);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}

		// everything went smoothly
		return true;
	}
}