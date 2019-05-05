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
 * Table class for group page
 */
class Page extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean
	 */
	public function check()
	{
		// need group id
		if ($this->get('gidNumber') == null)
		{
			$this->setError(Lang::txt('Must provide group id.'));
			return false;
		}

		// need page title
		if ($this->get('title') == null)
		{
			$this->setError(Lang::txt('Must provide page title.'));
			return false;
		}

		// need page alias
		if ($this->get('alias') == null)
		{
			$this->setError(Lang::txt('Must provide page alias.'));
			return false;
		}

		return true;
	}

	/**
	 * Find all pages matching filters
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);

		if (isset($filters['returnas']) && $filters['returnas'] == 'array')
		{
			return $this->_db->loadAssocList();
		}
		else
		{
			return $this->_db->loadObjectList();
		}
	}

	/**
	 * Get count of pages matching filters
	 *
	 * @param   array   $filters
	 * @return  int
	 */
	public function count($filters = array())
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query string for getting list or count of pages
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// published
		if (isset($filters['gidNumber']))
		{
			$where[] = "gidNumber=" . $this->_db->quote($filters['gidNumber']);
		}

		// published
		if (isset($filters['state']) && is_array($filters['state']))
		{
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// category
		if (isset($filters['category']))
		{
			$where[] = "category=" . $this->_db->quote($filters['category']);
		}

		// home
		if (isset($filters['home']))
		{
			$where[] = "home=" . $this->_db->quote($filters['home']);
		}

		// parent
		if (isset($filters['depth']))
		{
			$where[] = "depth=" . $this->_db->quote($filters['depth']);
		}

		// left
		if (isset($filters['left']))
		{
			$where[] = "lft > " . $this->_db->quote($filters['left']);
		}

		// right
		if (isset($filters['right']))
		{
			$where[] = "rgt < " . $this->_db->quote($filters['right']);
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		return $sql;
	}
}
