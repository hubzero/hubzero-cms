<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Date;

/**
 * Project Tool View class
 */
class ToolView extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_tool_views', 'id', $db);
	}

	/**
	 * Get last view
	 *
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $userid  User id
	 * @return  mixed    Return string or NULL
	 */
	public function loadView($toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl
				   WHERE parent_id=" . $this->_db->quote($toolid) . "
				   AND userid=" . $this->_db->quote($userid) . "
				   ORDER BY viewed DESC LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Check if page was viewed recently
	 *
	 * @param   integer  $toolid  Project tool id
	 * @param   integer  $userid  User id
	 * @return  mixed    Return string or NULL
	 */
	public function checkView($toolid = 0, $userid = 0)
	{
		if (!intval($toolid) || !intval($userid))
		{
			return false;
		}

		$now      = Date::toSql();
		$lastView = null;

		if ($this->loadView($toolid, $userid))
		{
			$lastView = $this->viewed;
		}
		else
		{
			$this->parent_id = $toolid;
			$this->userid    = $userid;
		}

		// Record new viewing time for future comparison
		$this->viewed = $now;
		$this->store();

		return $lastView;
	}
}
