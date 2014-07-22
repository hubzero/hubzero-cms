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
 * Time - tasks database class
 */
Class TimeTasks extends JTable
{
	/**
	 * id, primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * task name
	 *
	 * @var varchar(255)
	 */
	var $name = null;

	/**
	 * hub id
	 *
	 * @var int(11)
	 */
	var $hub_id = null;

	/**
	 * start date
	 *
	 * @var date
	 */
	var $start_date = null;

	/**
	 * end date
	 *
	 * @var date
	 */
	var $end_date = null;

	/**
	 * active
	 *
	 * @var int(1)
	 */
	var $active = null;

	/**
	 * description
	 *
	 * @var blob
	 */
	var $description = null;

	/**
	 * priority
	 *
	 * @var int(1)
	 */
	var $priority = null;

	/**
	 * assignee
	 *
	 * @var int(11)
	 */
	var $assignee = null;

	/**
	 * liaison
	 *
	 * @var int(11)
	 */
	var $liaison = null;

	/**
	 * Constructor
	 *
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	function __construct( &$db )
	{
		parent::__construct('#__time_tasks', 'id', $db );
	}

	/**
	 * Override check function to perform validation
	 *
	 * @return boolean Return true if all checks pass, else false
	 */
	public function check()
	{
		$this->name   = trim($this->name);
		$this->hub_id = trim($this->hub_id);

		// Make sure the task name isn't empty
		if (empty($this->name))
		{
			$this->setError(JText::_('COM_TIME_TASKS_NO_NAME'));
			return false;
		}

		// Make sure a hub was selected
		if (empty($this->hub_id))
		{
			$this->setError(JText::_('COM_TIME_TASKS_NO_HUB'));
			return false;
		}

		return true;
	}

	/**
	 * Build query
	 *
	 * @return $query
	 */
	public function buildQuery()
	{
		$query  = " FROM $this->_tbl AS p";
		$query .= " LEFT JOIN #__time_hubs AS h ON h.id = p.hub_id";
		$query .= " LEFT JOIN #__users AS ua ON ua.id = p.assignee";
		$query .= " LEFT JOIN #__users AS ul ON ul.id = p.liaison";

		return $query;
	}

	/**
	 * Get count of tasks, mainly used for pagination
	 *
	 * @return query result: number of tasks
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(p.id)";
		$query .= $this->buildquery();

		// Filters
		if (!empty($filters['search']) || !empty($filters['q']))
		{
			$first = true;

			if (!empty($filters['search']))
			{
				foreach ($filters['search'] as $arg)
				{
					$query .= ($first) ? " WHERE " : " AND ";
					$query .= "LOWER(p.name) LIKE " . $this->_db->quote('%' . strtolower($arg) . '%');

					$first = false;
				}
			}
			if (!empty($filters['q']))
			{
				foreach ($filters['q'] as $arg)
				{
					if ($arg['value'] !== NULL)
					{
						$query .= ($first) ? " WHERE " : " AND ";
						$query .= "p." . $arg['column'] . ' ' . $arg['o'] . ' ' . $this->_db->Quote($arg['value']);

						$first = false;
					}
				}
			}
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get list of tasks
	 *
	 * @param  $filters start and limit
	 * @return object list of collections
	 */
	public function getTasks($filters=array())
	{
		$query  = "SELECT p.*, h.name as hname, ua.name as aname, ul.name as lname";
		$query .= $this->buildquery();

		// Filters
		if (!empty($filters['search']) || !empty($filters['q']))
		{
			$first = true;

			if (!empty($filters['search']))
			{
				foreach ($filters['search'] as $arg)
				{
					$query .= ($first) ? " WHERE " : " AND ";
					$query .= "LOWER(p.name) LIKE " . $this->_db->quote('%' . strtolower($arg) . '%');

					$first = false;
				}
			}
			if (!empty($filters['q']))
			{
				foreach ($filters['q'] as $arg)
				{
					if ($arg['value'] !== NULL)
					{
						$query .= ($first) ? " WHERE " : " AND ";
						$query .= "p." . $arg['column'] . ' ' . $arg['o'] . ' ' . $this->_db->Quote($arg['value']);

						$first = false;
					}
				}
			}
		}

		if (!empty($filters['orderby']) && !empty($filters['orderdir']))
		{
			if (!in_array(strtoupper($filters['orderdir']), array('ASC', 'DESC')))
			{
				$filters['orderdir'] = 'DESC';
			}
			$query .= " ORDER BY ".$filters['orderby']." ".$filters['orderdir'];
		}
		else
		{
			$query .= " ORDER BY p.name ASC";
		}
		if (isset($filters['start']) && isset($filters['limit']) && $filters['limit'] > 0)
		{
			$query .= " LIMIT ".intval($filters['start']).",".intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}