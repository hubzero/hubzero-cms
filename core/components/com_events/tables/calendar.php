<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for event pages
 */
class Calendar extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_calendars', 'id', $db);
	}

	/**
	 * Check Method for saving
	 *
	 * @return  bool
	 */
	public function check()
	{
		if (!isset($this->title) || $this->title == '')
		{
			$this->setError(Lang::txt('COM_EVENTS_CALENDAR_MUST_HAVE_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Find all calendars matching filters
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
	 * Get count of calendars matching filters
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
	 * Build query string for getting list or count of calendars
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// scope
		if (isset($filters['scope']))
		{
			$where[] = "scope=" . $this->_db->quote($filters['scope']);
		}

		// scope_id
		if (isset($filters['scope_id']))
		{
			$where[] = "scope_id=" . $this->_db->quote($filters['scope_id']);
		}

		// readonly
		if (isset($filters['readonly']))
		{
			$where[] = "readonly=" . $this->_db->quote($filters['readonly']);
		}

		// published
		if (isset($filters['published']) && is_array($filters['published']))
		{
			$where[] = "published IN (" . implode(',', $filters['published']) . ")";
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		return $sql;
	}
}
