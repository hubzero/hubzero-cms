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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Middleware venue location table class
 */
class MwVenueLocation extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $venue_id;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $location;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('venue_locations', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		$this->location = trim($this->location);
		if (!$this->location) 
		{
			$this->setError(JText::_('No location provided'));
			return false;
		}
		$this->venue_id = intval($this->venue_id);
		if (!$this->venue_id) 
		{
			$this->setError(JText::_('No venue ID provided'));
			return false;
		}

		return true;
	}

	/**
	 * Delete one or more records by venue ID
	 *
	 * @param      integer $venue_id Venue ID
	 * @return     boolean True if successful otherwise returns and error message
	 */
	public function deleteByVenue($venue_id=null)
	{
		$venue_id = intval($venue_id);
		if (!$venue_id)
		{
			$venue_id = $this->venue_id;
		}

		$query = 'DELETE FROM ' . $this->_db->nameQuote($this->_tbl) .
				' WHERE venue_id = ' . $this->_db->Quote($venue_id);
		$this->_db->setQuery($query);

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Construct an SQL statement based on the array of filters passed
	 * 
	 * @param      array $filters Filters to build SQL from
	 * @return     string SQL
	 */
	private function _buildQuery($filters=array())
	{
		$where = array();

		if (isset($filters['location']) && $filters['location'] != '') 
		{
			$where[] = "c.`location`=" . $this->_db->Quote($filters['location']);
		}
		if (isset($filters['venue_id']) && $filters['venue_id'] != '') 
		{
			$where[] = "c.`venue_id`=" . $this->_db->Quote($filters['venue_id']);
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.location) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "')";
		}
		if (isset($filters['venue']) && $filters['venue']) 
		{
			$where[] = "t.venue = " . $mwdb->Quote($this->view->filters['venue']);
		}

		$query = "FROM $this->_tbl AS c";
		$query .= " JOIN venue AS t ON c.venue_id=t.id";

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
	 * @param      array $filters Filters to build SQL from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 * 
	 * @param      array $filters Filters to build SQL from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*, t.venue " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'location';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY c.venue_id, " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
