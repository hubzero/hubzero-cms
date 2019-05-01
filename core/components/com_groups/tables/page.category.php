<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Groups Pages Category table
 */
class PageCategory extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages_categories', 'id', $db);
	}

	/**
	 * Check method overload
	 *
	 * @return  void
	 */
	public function check()
	{
		// make sure we have a title
		if (!$this->gidNumber || $this->gidNumber == "")
		{
			$this->setError(Lang::txt('Category Must Contain Group ID Number'));
			return false;
		}

		// make sure we have a title
		if (!$this->title || $this->title == "")
		{
			$this->setError(Lang::txt('Category Must Contain Title'));
			return false;
		}

		return true;
	}

	/**
	 * Find all records matching filters
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of records matching filters
	 *
	 * @param   array  $filters
	 * @return  int
	 */
	public function count($filters = array())
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build query string for getting list or count of records
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters = array())
	{
		//vars
		$sql   = '';
		$where = array();

		// check for gidNumber
		if (isset($filters['gidNumber']))
		{
			$where[] = 'gidNumber=' . $this->_db->quote($filters['gidNumber']);
		}

		// did we have any conditions
		if (count($where) > 0)
		{
			$sql = ' WHERE ' . implode(' AND', $where);
		}

		// check for gidNumber
		if (isset($filters['orderby']))
		{
			$sql .= ' ORDER BY ' . $filters['orderby'];
		}

		return $sql;
	}

	/**
	 * Get categories for a group
	 *
	 * @param   object  $group
	 * @return  array
	 */
	public function getCategories($group)
	{
		$categories = array();

		// make sure we have a valid group
		if (!is_object($group) || $group->get('gidNumber') == '')
		{
			return $categories;
		}

		$sql = "SELECT * FROM {$this->_tbl} WHERE gidNumber=" . $this->_db->quote($group->get('gidNumber'));
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
