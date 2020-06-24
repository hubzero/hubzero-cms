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
 * Groups Module table
 */
class Module extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_modules', 'id', $db);
	}

	/**
	 * Overload check method to make sure we have needed vars
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

		// need module title
		if ($this->get('title') == null)
		{
			$this->setError(Lang::txt('Must provide module title.'));
			return false;
		}

		// need module content
		if ($this->get('content') == null)
		{
			$this->setError(Lang::txt('Must provide module content.'));
			return false;
		}

		return true;
	}

	/**
	 * Find all modules matching filters
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of modules matching filters
	 *
	 * @param   array  $filters
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
	 * Build query string for getting list or count of records
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

		// title
		if (isset($filters['title']))
		{
			$where[] = "title=" . $this->_db->quote($filters['title']);
		}

		// position
		if (isset($filters['position']))
		{
			$where[] = "position=" . $this->_db->quote($filters['position']);
		}

		// state
		if (isset($filters['state']) && is_array($filters['state']))
		{
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// approved
		if (isset($filters['approved']) && is_array($filters['approved']))
		{
			$where[] = "approved IN (" . implode(',', $filters['approved']) . ")";
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
