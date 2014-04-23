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
 * Middleware zones table class
 */
class MwZones extends JTable
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
	var $zone;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $title;

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
	 * varchar(20)
	 * 
	 * @var string
	 */
	var $mw_version;

	/**
	 * varchar(20)
	 * 
	 * @var string
	 */
	var $ssh_key_path;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $picture;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('zones', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		$this->zone = preg_replace("/[^A-Za-z0-9\-\_\.]/", '', $this->zone);
		if (!$this->zone) 
		{
			$this->setError(JText::_('No zone provided'));
			return false;
		}
		if (!$this->title) 
		{
			$this->title = $this->zone;
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
	 * Delete a record and any associated records in the #__zone_locations table
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

		$location = new MwZoneLocations($this->_db);
		if (!$location->deleteByZone($oid))
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
			$where[] = "c.`state`=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['master']) && $filters['master'] != '') 
		{
			$where[] = "c.`master`=" . $this->_db->Quote($filters['master']);
		}
		if (isset($filters['zone']) && $filters['zone'] != '') 
		{
			$where[] = "c.`zone`=" . $this->_db->Quote($filters['zone']);
		}
		if (isset($filters['id'])) 
		{
			if (!is_array($filters['id']))
			{
				$filters['id'] = array($filters['id']);
				$filters['id'] = array_map('intval', $filters['id']);
			}
			if (!empty($filters['id']))
			{
				$where[] = "c.`id` IN (" . implode(',', $filters['id']) . ")";
			}
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.`zone`) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' OR LOWER(c.`master`) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
		}

		$query = "FROM $this->_tbl AS c";
		if (isset($filters['ip']) || isset($filters['ipFROM']) || isset($filters['ipTO']) 
		 || isset($filters['continent']) || isset($filters['countrySHORT']) 
		 || isset($filters['ipREGION']) || isset($filters['ipCITY'])) 
		{
			$query .= " JOIN `zone_locations` AS t ON c.`id`=t.`zone_id`";
			//$where[] = "t.`id` = " . $this->_db->Quote($this->view->filters['location']);
			if (isset($filters['ipFROM']) && $filters['ipFROM'] != '') 
			{
				$where[] = "t.`ipFROM`= INET_ATON(" . $this->_db->Quote($filters['ipFROM']) . ")";
			}
			if (isset($filters['ipTO']) && $filters['ipTO'] != '') 
			{
				$where[] = "t.`ipTO`= INET_ATON(" . $this->_db->Quote($filters['ipTO']) . ")";
			}
			// If we just have an IP address
			if (isset($filters['ip']) && $filters['ip'] != '') 
			{
				$where[] = "t.`ipFROM` <= INET_ATON(" . $this->_db->Quote($filters['ip']) . ")";
				$where[] = "t.`ipTO` >= INET_ATON(" . $this->_db->Quote($filters['ip']) . ")";
			}
			if (isset($filters['continent']) && $filters['continent'] != '') 
			{
				$where[] = "LOWER(t.`continent`)=" . $this->_db->Quote(strtolower($filters['continent']));
			}
			if (isset($filters['countrySHORT']) && $filters['countrySHORT'] != '') 
			{
				$where[] = "LOWER(t.`countrySHORT`)=" . $this->_db->Quote(strtolower($filters['countrySHORT']));
			}
			if (isset($filters['ipREGION']) && $filters['ipREGION'] != '') 
			{
				$where[] = "LOWER(t.`ipREGION`)=" . $this->_db->Quote(strtolower($filters['ipREGION']));
			}
			if (isset($filters['ipCITY']) && $filters['ipCITY'] != '') 
			{
				$where[] = "LOWER(t.`ipCITY`)=" . $this->_db->Quote(strtolower($filters['ipCITY']));
			}
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}
		$query .= " GROUP BY `zone` ";

		return $query;
	}

	/**
	 * Get a list of records
	 * 
	 * @param      string $what    Data to return
	 * @param      array  $filters Filters to build SQL from
	 * @return     mixed
	 */
	public function find($what='list', $filters=array())
	{
		switch ($what)
		{
			case 'count':
				$filters['limit'] = 0;

				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'all':
				$filters['limit'] = 0;
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query  = "SELECT c.* " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort']) 
				{
					$filters['sort'] = 'zone';
				}
				if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] != 0) 
				{
					$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}
}
