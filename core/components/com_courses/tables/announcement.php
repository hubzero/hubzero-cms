<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use User;
use Date;
use Lang;

/**
 * Course announcement table class
 */
class Announcement extends Table
{
	/**
	 * Constructor method for Table class
	 *
	 * @param   object  database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_announcements', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(Lang::txt('Missing offering ID'));
			return false;
		}

		$this->content = trim($this->content);
		if (!$this->content)
		{
			$this->setError(Lang::txt('Missing content'));
			return false;
		}

		$this->priority = intval($this->priority);

		if ($this->publish_up && $this->publish_up != $this->_db->getNullDate())
		{
			// Does the date have the correct format?
			if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}[ ][0-9]{2}:[0-9]{2}:[0-9]{2}/", $this->publish_up))
			{
				// Date with no timestamp?
				if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $this->publish_up))
				{
					// Add timestamp
					$this->publish_up .= ' 00:00:00';
				}
				else
				{
					// Disregard any formats that don't match
					$this->publish_up = null;
				}
			}
		}

		if ($this->publish_down && $this->publish_down != $this->_db->getNullDate())
		{
			// Does the date have the correct format?
			if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}[ ][0-9]{2}:[0-9]{2}:[0-9]{2}/", $this->publish_down))
			{
				// Date with no timestamp?
				if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $this->publish_down))
				{
					// Add timestamp
					$this->publish_down .= ' 00:00:00';
				}
				else
				{
					// Disregard any formats that don't match
					$this->publish_down = null;
				}
			}
		}

		$this->sticky = intval($this->sticky);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query =  " FROM $this->_tbl AS a";

		$where = array();

		if (isset($filters['offering_id']) && $filters['offering_id'])
		{
			$where[] = "a.`offering_id` = " . $this->_db->quote(intval($filters['offering_id']));
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$where[] = "a.`section_id` = " . $this->_db->quote(intval($filters['section_id']));
		}
		if (isset($filters['state']) && $filters['state'])
		{
			$where[] = "a.`state` = " . $this->_db->quote(intval($filters['state']));
		}
		if (isset($filters['created_by']) && $filters['created_by'])
		{
			$where[] = "a.`created_by` = " . $this->_db->quote(intval($filters['created_by']));
		}
		if (isset($filters['priority']) && $filters['priority'])
		{
			$where[] = "a.`priority` = " . $this->_db->quote(intval($filters['priority']));
		}
		if (isset($filters['sticky']) && $filters['sticky'])
		{
			$where[] = "a.`sticky` = " . $this->_db->quote(intval($filters['sticky']));
		}

		if (isset($filters['published']))
		{
			$now = \Date::toSql();
			$where[] = "(a.`publish_up` IS NULL OR a.`publish_up` = '0000-00-00 00:00:00' OR a.`publish_up` <= " . $this->_db->quote($now) . ")";
			$where[] = "(a.`publish_down` IS NULL OR a.`publish_down` = '0000-00-00 00:00:00' OR a.`publish_down` >= " . $this->_db->quote($now) . ")";
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "a.`id`=" . $this->_db->quote(intval($filters['search']));
			}
			else
			{
				$where[] = "(LOWER(a.content) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(' AND ', $where);
		}

		return $query;
	}

	/**
	 * Get a count of records
	 *
	 * @param     array $filters
	 * @return    integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of records
	 *
	 * @param     array $filters
	 * @return    array
	 */
	public function find($filters = [])
	{
		$query  = "SELECT a.*";
		$query .= $this->_buildQuery($filters);

		$query .= " ORDER BY a.sticky DESC, a.created DESC";

		if (isset($filters['limit']) && $filters['limit'] > 0)
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}
}
