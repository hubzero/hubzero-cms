<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Time - records database class
 */
Class TimeRecords extends JTable
{
	/**
	 * id, primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * task id
	 *
	 * @var int(11)
	 */
	var $task_id = null;

	/**
	 * user id
	 *
	 * @var int(11)
	 */
	var $user_id = null;

	/**
	 * time
	 *
	 * @var int(11)
	 */
	var $time = null;

	/**
	 * date
	 *
	 * @var date
	 */
	var $date = null;

	/**
	 * description
	 *
	 * @var longtext
	 */
	var $description = null;

	/**
	 * billed
	 *
	 * @var int(1)
	 */
	var $billed = null;

	/**
	 * Constructor
	 *
	 * @param   database object
	 * @return  void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_records', 'id', $db );
	}

	/**
	 * Override check function to perform validation
	 *
	 * @return boolean true if all checks pass, else false
	 */
	public function check()
	{
		// If task is empty return an error
		if(empty($this->task_id))
		{
			$this->setError(JText::_('PLG_TIME_RECORDS_NO_TASK_CHOSEN'));
			return false;
		}

		// If no time is given return an error
		if($this->time == "0.0")
		{
			$this->setError(JText::_('PLG_TIME_RECORDS_NO_TIME_CHOSEN'));
			return false;
		}

		// Everything passed, return true
		return true;
	}

	/**
	 * Build query function
	 *
	 * @return $query
	 */
	public function buildQuery()
	{
		// Do all of our joins
		// @FIXME: we could decrease the # of joins by only doing them when certain filters are set
		$query = " FROM $this->_tbl AS r";
		$query .= " LEFT JOIN #__users AS u ON u.id = r.user_id";
		$query .= " LEFT JOIN #__time_tasks AS p ON p.id = r.task_id";
		$query .= " LEFT JOIN #__time_hubs AS h ON h.id = p.hub_id";

		return $query;
	}

	/**
	 * Get count of records, mainly used for pagination
	 *
	 * @param  $filters of rows to return (filters: pid, startdate, enddate, id_range, orderby, orderdir, start, limit, user, task, query)
	 * @return result number of records
	 */
	public function getCount($filters = array())
	{
		$query  = "SELECT COUNT(r.id)";
		$query .= $this->buildquery();

		// Filters
		if(!empty($filters['user']) || !empty($filters['task']) || !empty($filters['search']) || !empty($filters['q']))
		{
			$first = true;

			if(!empty($filters['user']))
			{
				$query .= ($first) ? " WHERE " : " AND ";
				$query .= "u.id = " . $this->_db->quote($filters['user']);

				$first = false;
			}
			if(!empty($filters['task']))
			{
				$query .= ($first) ? " WHERE " : " AND ";
				$query .= "p.id = " . $this->_db->quote($filters['task']);

				$first = false;
			}
			if(!empty($filters['search']))
			{
				foreach($filters['search'] as $arg)
				{
					$query .= ($first) ? " WHERE " : " AND ";
					$query .= "(LOWER(r.description) LIKE '%" . $this->_db->getEscaped(strtolower($arg)) . "%'";
					$query .= " OR LOWER(p.name) LIKE '%" . $this->_db->getEscaped(strtolower($arg)) . "%')";

					$first = false;
				}
			}
			if(!empty($filters['q']))
			{
				foreach($filters['q'] as $arg)
				{
					if($arg['value'] !== NULL)
					{
						$query .= ($first) ? " WHERE " : " AND ";
						$query .= $arg['column'] . ' ' . $arg['o'] . ' ' . $this->_db->Quote($arg['value']);

						$first = false;
					}
				}
			}
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get list of records
	 *
	 * @param  $filters of rows to return (filters: pid, startdate, enddate, id_range, orderby, orderdir, start, limit, user, task, query)
	 * @return object list of collections
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT r.*, u.name as uname, u.id as uid, p.id as pid, p.name as pname";
		$query .= $this->buildquery();

		// This is used when creating bill for a given task and id range
		if(!empty($filters['pid']) && !empty($filters['startdate']) && !empty($filters['enddate']))
		{
			$query .= " WHERE p.id = ".$this->_db->quote($filters['pid']);
			$query .= " AND billed = 0";
			$query .= " AND r.date BETWEEN ".$this->_db->quote($filters['startdate'])." AND ".$this->_db->quote($filters['enddate']);
		}
		// This is used when we're pulling records to display a report
		elseif(!empty($filters['id_range']))
		{
			$query .= " WHERE r.id in (".$filters['id_range'].")";
		}
		// This is for Mike M.
		elseif(!empty($filters['startdate']) && !empty($filters['enddate']))
		{
			$query .= " WHERE r.date BETWEEN ".$this->_db->quote($filters['startdate'])." AND ".$this->_db->quote($filters['enddate']);
		}
		// Filter by user and/or task on general records view
		elseif(!empty($filters['user']) || !empty($filters['task']) || !empty($filters['search']) || !empty($filters['q']))
		{
			$first = true;

			if(!empty($filters['user']))
			{
				$query .= ($first) ? " WHERE " : " AND ";
				$query .= "u.id = " . $this->_db->quote($filters['user']);

				$first = false;
			}
			if(!empty($filters['task']))
			{
				$query .= ($first) ? " WHERE " : " AND ";
				$query .= "p.id = " . $this->_db->quote($filters['task']);

				$first = false;
			}
			if(!empty($filters['search']))
			{
				foreach($filters['search'] as $arg)
				{
					$query .= ($first) ? " WHERE " : " AND ";
					$query .= "(LOWER(r.description) LIKE '%" . $this->_db->getEscaped(strtolower($arg)) . "%'";
					$query .= " OR LOWER(p.name) LIKE '%" . $this->_db->getEscaped(strtolower($arg)) . "%')";

					$first = false;
				}
			}
			if(!empty($filters['q']))
			{
				foreach($filters['q'] as $arg)
				{
					if($arg['value'] !== NULL)
					{
						$query .= ($first) ? " WHERE " : " AND ";
						$query .= $arg['column'] . ' ' . $arg['o'] . ' ' . $this->_db->Quote($arg['value']);

						$first = false;
					}
				}
			}
		}
		// This is used in our records display for sorting
		if(!empty($filters['orderby']) && !empty($filters['orderdir']))
		{
			if (!in_array(strtoupper($filters['orderdir']), array('ASC', 'DESC')))
			{
				$filters['orderdir'] = 'DESC';
			}
			$query .= " ORDER BY ".$filters['orderby']." ".$filters['orderdir'];
		}
		else
		{
			// Even if orderby and orderdir aren't set, we should set some defaults (used before state is established for these variables)
			$query .= " ORDER BY r.id DESC";
		}
		// Set limit and start for pagination
		$query .= " LIMIT ".intval($filters['start']).",".intval($filters['limit']);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get single record (not just using load because we need to do some joins)
	 *
	 * @param  $id of row to return
	 * @return single result
	 */
	public function getRecord($id)
	{
		$query  = "SELECT r.*, u.name as uname, h.name as hname, h.id as hid, p.active as pactive, p.name as pname";
		$query .= $this->buildquery();
		$query .= " WHERE r.id = ".$this->_db->Quote($id);

		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();

		// Check if we have a result before returning
		if($result)
		{
			// Return the result
			return $result;
		}
		else
		{
			// No result, so we must be creating a new record
			// We need to esentially instanciate an empty object
			// @FIXME: is there a better way to do this?
			$row              = new stdClass;
			$row->id          = '';
			$row->time        = '';
			$row->hid         = '';
			$row->task_id     = '';
			$row->description = '';
			$row->pactive     = '1';
			$row->billed      = '0';

			// Return the empty object
			return $row;
		}
	}

	/**
	 * Get summary hours for overview column chart
	 *
	 * @param  $limit of rows to return
	 * @return object list of 'task=>hours'
	 */
	public function getSummaryHours($limit, $uid='')
	{
		$query  = "SELECT p.name AS pname, sum(time) as hours";
		$query .= $this->buildquery();
		if(!empty($uid))
		{
			$query .= " WHERE u.id = " . $this->_db->Quote($uid);
		}
		$query .= " GROUP BY task_id";
		$query .= " ORDER BY hours DESC";
		$query .= " LIMIT " . $limit;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get summary hours by hub for overview pie chart
	 *
	 * @param  $limit of rows to return
	 * @return object list of 'task=>hours'
	 */
	public function getSummaryHoursByHub($limit, $hub=0)
	{
		$query  = "SELECT h.name AS hname, sum(time) as hours";
		$query .= $this->buildquery();
		if(!empty($hub))
		{
			$query .= " WHERE h.id = " . $this->_db->Quote($hub);
		}
		$query .= " GROUP BY hname";
		$query .= " ORDER BY hours DESC";
		$query .= " LIMIT ".$limit;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get summary hours by hub for overview pie chart
	 *
	 * @param  $limit of rows to return
	 * @return object list of 'task=>hours'
	 */
	public function getSummaryEntries($date)
	{
		$query  = "SELECT u.name as name, count(r.id) as entries";
		$query .= $this->buildquery();
		if(!empty($date['start']) && !empty($date['end']))
		{
			$query .= " WHERE `date` >= " . $this->_db->Quote($date['start']) . " AND `date` <= " . $this->_db->Quote($date['end']);
		}
		$query .= " GROUP BY name";
		$query .= " ORDER BY entries DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get total hours for overview chart and reports
	 *
	 * @param  $filters of rows to return (filters: id_range)
	 * @return result for total hours
	 */
	public function getTotalHours($filters=array())
	{
		$query  = "SELECT sum(time) as hours";
		$query .= $this->buildquery();

		// If we have an id range, we'll only sum hours for that range (i.e. when viewing a report)
		if(!empty($filters['id_range']) || !empty($filters['user_id']))
		{
			if(!empty($filters['id_range']) && !empty($filters['user_id']))
			{
				$query .= " WHERE r.id in (".$filters['id_range'].")";
				$query .= " AND r.user_id = " . $this->_db->Quote($filters['user_id']);
			}
			elseif(!empty($filters['id_range']))
			{
				$query .= " WHERE r.id in (".$filters['id_range'].")";
			}
			elseif(!empty($filters['user_id']))
			{
				$query .= " WHERE r.user_id = " . $this->_db->Quote($filters['user_id']);
			}
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}