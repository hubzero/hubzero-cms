<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cron\Tables;

use Date;
use Lang;
use User;

/**
 * CRON table class for jobs
 */
class Job extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__cron_jobs', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(Lang::txt('COM_CRON_ERROR_EMPTY_TITLE'));
			return false;
		}

		if (!$this->recurrence)
		{
			$this->setError(Lang::txt('COM_CRON_ERROR_EMPTY_RECURRENCE'));
			return false;
		}

		$this->recurrence = preg_replace('/[\s]{2,}/', ' ', $this->recurrence);

		if (preg_match('/[^-,*\/ \\d]/', $this->recurrence) !== 0)
		{
			$this->setError(Lang::txt('Cron String contains invalid character.'));
			return false;
		}

		if (strstr($this->event, '::'))
		{
			$parts = explode('::', $this->event);
			$this->plugin = trim($parts[0]);
			$this->event  = trim($parts[1]);
		}
		if (!$this->event)
		{
			$this->setError(Lang::txt('Missing plugin.'));
			return false;
		}

		$bits = @explode(' ', $this->recurrence);
		if (count($bits) != 5)
		{
			$this->setError(Lang::txt('Cron string is invalid. Too many or too little sections.'));
			return false;
		}

		if (!$this->id)
		{
			$this->created = Date::toSql();
			$this->created_by = User::get('id');
		}
		else
		{
			$this->modified = Date::toSql();
			$this->modified_by = User::get('id');
		}

		if (!$this->publish_up)
		{
			$this->publish_up = '0000-00-00 00:00:00';
		}

		if (!$this->publish_down)
		{
			$this->publish_down = '0000-00-00 00:00:00';
		}

		return true;
	}

	/**
	 * Build a query
	 *
	 * @param   array   $filters  Parameters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";

		$where = array();

		if (isset($filters['state']))
		{
			$where[] = "c.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['next_run']) && $filters['next_run'] != '')
		{
			$where[] = "c.next_run <= " . $this->_db->quote($filters['next_run']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
		}

		if (isset($filters['available']) && $filters['available'])
		{
			$now = Date::toSql();

			$where[] = "(c.publish_up = '0000-00-00 00:00:00' OR c.publish_up <= " . $this->_db->quote($now) . ")";
			$where[] = "(c.publish_down = '0000-00-00 00:00:00' OR c.publish_down > " . $this->_db->quote($now) . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Parameters to build query from
	 * @return     integer
	 */
	public function count($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Parameters to build query from
	 * @return     array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT c.*";
		$query .= " " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'ordering';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Parameters to build query from
	 * @return     array
	 */
	public function getJobs($filters=array())
	{
		$query  = "SELECT c.* FROM $this->_tbl AS c";

		$where = array();

		if (isset($filters['state']))
		{
			$where[] = "c.state=" . $this->_db->quote($filters['state']);
		}

		if (isset($filters['next_run']) && $filters['next_run'] != '')
		{
			$where[] = "c.next_run <= " . $this->_db->quote($filters['next_run']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$query .= " ORDER BY c.ordering ASC, c.next_run DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

