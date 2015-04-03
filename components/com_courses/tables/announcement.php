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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Tables;

/**
 * Course announcement table class
 */
class Announcement extends \JTable
{
	/**
	 * Constructor method for JTable class
	 *
	 * @param  database object
	 * @return void
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
			$this->created    = \JFactory::getDate()->toSql();
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
			$where[] = "a.`offering_id` = " . $this->_db->Quote(intval($filters['offering_id']));
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$where[] = "a.`section_id` = " . $this->_db->Quote(intval($filters['section_id']));
		}
		if (isset($filters['state']) && $filters['state'])
		{
			$where[] = "a.`state` = " . $this->_db->Quote(intval($filters['state']));
		}
		if (isset($filters['created_by']) && $filters['created_by'])
		{
			$where[] = "a.`created_by` = " . $this->_db->Quote(intval($filters['created_by']));
		}
		if (isset($filters['priority']) && $filters['priority'])
		{
			$where[] = "a.`priority` = " . $this->_db->Quote(intval($filters['priority']));
		}
		if (isset($filters['sticky']) && $filters['sticky'])
		{
			$where[] = "a.`sticky` = " . $this->_db->Quote(intval($filters['sticky']));
		}

		if (isset($filters['published']))
		{
			$now = \JFactory::getDate()->toSql();
			$where[] = "(a.`publish_up` = '0000-00-00 00:00:00' OR a.`publish_up` <= " . $this->_db->Quote($now) . ")";
			$where[] = "(a.`publish_down` = '0000-00-00 00:00:00' OR a.`publish_down` >= " . $this->_db->Quote($now) . ")";
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "a.`id`=" . $this->_db->Quote(intval($filters['search']));
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
	public function find($filters=array())
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