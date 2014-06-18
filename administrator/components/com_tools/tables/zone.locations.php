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
 * Middleware zone locations table class
 */
class MwZoneLocations extends JTable
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
	var $zone_id;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipFROM;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipTO;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $continent;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $countrySHORT;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $countryLONG;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipREGION;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipCity;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipLATITUDE;


	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $ipLONGITUDE;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('zone_locations', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		$this->zone_id = intval($this->zone_id);
		if (!$this->zone_id)
		{
			$this->setError(JText::_('No zone ID provided'));
			return false;
		}
		if (strstr($this->ipFROM, '.'))
		{
			$this->ipFROM = ip2long($this->ipFROM);
		}
		if (strstr($this->ipTO, '.'))
		{
			$this->ipTO = ip2long($this->ipTO);
		}

		return true;
	}

	/**
	 * Delete one or more records by zone ID
	 *
	 * @param      integer $zone_id Zone ID
	 * @return     boolean True if successful otherwise returns and error message
	 */
	public function deleteByZone($zone_id=null)
	{
		$zone_id = intval($zone_id);
		if (!$zone_id)
		{
			$zone_id = $this->zone_id;
		}

		$query = 'DELETE FROM ' . $this->_db->nameQuote($this->_tbl) .
				' WHERE zone_id = ' . $this->_db->Quote($zone_id);
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

		if (isset($filters['zone_id']) && $filters['zone_id'] != '')
		{
			$where[] = "c.`zone_id`=" . $this->_db->Quote($filters['zone_id']);
		}

		$query  = "FROM $this->_tbl AS c";
		$query .= " JOIN zones AS t ON c.zone_id=t.id";

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

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
				$query  = "SELECT c.*, t.zone " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'c.zone_id';
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
