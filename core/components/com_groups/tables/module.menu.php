<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;

/**
 * Groups Module table
 */
class ModuleMenu extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_modules_menu', 'id', $db);
	}

	/**
	 * Get pages of module menu
	 *
	 * @return  array
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
	 * @param   integer  $moduleid
	 * @param   array    $pages
	 * @return  boolean
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
	 * @param   integer  $moduleid  Module ID
	 * @return  boolean
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
