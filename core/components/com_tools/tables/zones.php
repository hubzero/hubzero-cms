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
 * Middleware zones table class
 */
class Zones extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
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
			$this->setError(Lang::txt('No zone provided'));
			return false;
		}
		if (!$this->title)
		{
			$this->title = $this->zone;
		}
		$this->master = trim($this->master);
		if (!$this->master)
		{
			$this->setError(Lang::txt('No master provided'));
			return false;
		}
		$this->state = strtolower(trim($this->state));
		if (!$this->state)
		{
			$this->setError(Lang::txt('No state provided.'));
			return false;
		}
		if (!in_array($this->state, array('up', 'down')))
		{
			$this->setError(Lang::txt('Invalid state provided.'));
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

		$location = new ZoneLocations($this->_db);
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
			$where[] = "c.`state`=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['master']) && $filters['master'] != '')
		{
			$where[] = "c.`master`=" . $this->_db->quote($filters['master']);
		}
		if (isset($filters['zone']) && $filters['zone'] != '')
		{
			$where[] = "c.`zone`=" . $this->_db->quote($filters['zone']);
		}
		if (isset($filters['id']))
		{
			if (!is_array($filters['id']))
			{
				$filters['id'] = array($filters['id']);
				$filters['id'] = array_map('intval', $filters['id']);
			}
			if (empty($filters['id']))
			{
				$filters['id'][] = 0;
			}
			//if (!empty($filters['id']))
			//{
				$where[] = "c.`id` IN (" . implode(',', $filters['id']) . ")";
			//}
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.`zone`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . " OR LOWER(c.`master`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		$query = "FROM $this->_tbl AS c";
		if (isset($filters['ip']) || isset($filters['ipFROM']) || isset($filters['ipTO'])
		 || isset($filters['continent']) || isset($filters['countrySHORT'])
		 || isset($filters['ipREGION']) || isset($filters['ipCITY']))
		{
			$query .= " JOIN `zone_locations` AS t ON c.`id`=t.`zone_id`";
			//$where[] = "t.`id` = " . $this->_db->quote($this->view->filters['location']);
			if (isset($filters['ipFROM']) && $filters['ipFROM'] != '')
			{
				$where[] = "t.`ipFROM`= INET_ATON(" . $this->_db->quote($filters['ipFROM']) . ")";
			}
			if (isset($filters['ipTO']) && $filters['ipTO'] != '')
			{
				$where[] = "t.`ipTO`= INET_ATON(" . $this->_db->quote($filters['ipTO']) . ")";
			}
			// If we just have an IP address
			if (isset($filters['ip']) && $filters['ip'] != '')
			{
				$where[] = "t.`ipFROM` <= INET_ATON(" . $this->_db->quote($filters['ip']) . ")";
				$where[] = "t.`ipTO` >= INET_ATON(" . $this->_db->quote($filters['ip']) . ")";
			}
			if (isset($filters['continent']) && $filters['continent'])
			{
				if (!is_array($filters['continent']))
				{
					$filters['continent'] = array($filters['continent']);
				}
				foreach ($filters['continent'] as $k => $v)
				{
					$filters['continent'][$k] = $this->_db->quote(strtolower($v));
				}
				$where[] = "LOWER(t.`continent`) IN (" . implode(',', $filters['continent']) . ")";
			}
			if (isset($filters['countrySHORT']) && $filters['countrySHORT'] != '')
			{
				$where[] = "LOWER(t.`countrySHORT`)=" . $this->_db->quote(strtolower($filters['countrySHORT']));
			}
			if (isset($filters['ipREGION']) && $filters['ipREGION'] != '')
			{
				$where[] = "LOWER(t.`ipREGION`)=" . $this->_db->quote(strtolower($filters['ipREGION']));
			}
			if (isset($filters['ipCITY']) && $filters['ipCITY'] != '')
			{
				$where[] = "LOWER(t.`ipCITY`)=" . $this->_db->quote(strtolower($filters['ipCITY']));
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
