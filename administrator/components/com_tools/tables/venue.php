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
 * Middleware venue table class
 */
class MwVenue extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $venue;

	/**
	 * varchar(20)
	 * 
	 * @var string
	 */
	var $state;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $master;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('venue', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		$this->venue = trim($this->venue); //preg_replace("/[^A-Za-z0-9-.]/", '', $this->venue);
		if (!$this->venue) 
		{
			$this->setError(JText::_('No venue provided'));
			return false;
		}
		$this->master = trim($this->master);
		if (!$this->master) 
		{
			$this->setError(JText::_('No master provided'));
			return false;
		}
		$this->state = strtolower(trim($this->state));
		if (!$this->state) 
		{
			$this->setError(JText::_('No state provided.'));
			return false;
		}
		if (!in_array($this->state, array('up', 'down')))
		{
			$this->setError(JText::_('Invalid state provided.'));
			return false;
		}

		return true;
	}

	/**
	 * Delete a record and any associated records in the #__venue_locations table
	 *
	 * @param      integer $oid Record ID
	 * @return     boolean True if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = $oid;
		}

		$location = new MwVenueLocation($this->_db);
		if (!$location->deleteByVenue($oid))
		{
			$this->setError($location->getError());
			return false;
		}

		return parent::delete($oid);
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

		if (isset($filters['state']) && $filters['state'] != '') 
		{
			$where[] = "c.`state`='" . $filters['state'] . "'";
		}
		if (isset($filters['master']) && $filters['master'] != '') 
		{
			$where[] = "c.`master`='" . $filters['master'] . "'";
		}
		if (isset($filters['venue']) && $filters['venue'] != '') 
		{
			$where[] = "c.`venue`='" . $filters['venue'] . "'";
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.venue) LIKE '%" . strtolower($filters['search']) . "%' OR LOWER(c.master) LIKE '%" . strtolower($filters['search']) . "%')";
		}

		$query = "FROM $this->_tbl AS c";
		if (isset($filters['location']) && $filters['location']) 
		{
			$query .= " JOIN venue_locations AS t ON c.id=t.venue_id";
			$where[] = "t.location = " . $mwdb->Quote($this->view->filters['location']);
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
		$query  = "SELECT c.* " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'venue';
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
}
