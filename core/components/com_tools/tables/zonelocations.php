<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Lang;

/**
 * Middleware zone locations table class
 */
class ZoneLocations extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
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
			$this->setError(Lang::txt('No zone ID provided'));
			return false;
		}

		if (strpos($this->ipFROM, '/') !== false)
		{
			$cidr         = explode('/', $this->ipFROM);
			$this->ipFROM = ip2long($cidr[0]) & ((-1 << (32 - (int)$cidr[1])));
			$this->ipTO   = ip2long($cidr[0]) + pow(2, (32 - (int)$cidr[1])) - 1;
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

		$query = 'DELETE FROM ' . $this->_db->quoteName($this->_tbl) .
				' WHERE zone_id = ' . $this->_db->quote($zone_id);
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
			$where[] = "c.`zone_id`=" . $this->_db->quote($filters['zone_id']);
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
