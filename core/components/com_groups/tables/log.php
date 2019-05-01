<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;

/**
 * Table class for logging group actions
 */
class Log extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->gidNumber) == '')
		{
			$this->setError(\Lang::txt('GROUPS_LOGS_MUST_HAVE_GROUP_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Find all logs matching filters
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

		// gidnumber
		if (isset($filters['gidNumber']))
		{
			$where[] = "gidNumber=" . $this->_db->quote($filters['gidNumber']);
		}

		// action
		if (isset($filters['action']))
		{
			$where[] = "action=" . $this->_db->quote($filters['action']);
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
