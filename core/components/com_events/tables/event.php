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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

/**
 * Events table class for an event
 */
class Event extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(Lang::txt('EVENTS_MUST_HAVE_TITLE'));
		}
		if (trim($this->catid) == '' || trim($this->catid) == 0)
		{
			if ($this->scope == 'event')
			{
				$this->setError(Lang::txt('EVENTS_MUST_HAVE_CATEGORY'));
			}
		}

		if ($this->getError())
		{
			return false;
		}
		return true;
	}

	/**
	 * Set an event to published
	 *
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function publish($oid = null, $state = 1, $userId = 0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=1 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Set an event to unpublished
	 *
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function unpublish($oid=null)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=0 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Get the newest event date
	 *
	 * @return     string
	 */
	public function getFirst()
	{
		$this->_db->setQuery("SELECT publish_up FROM $this->_tbl ORDER BY publish_up ASC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the oldest event date
	 *
	 * @return     string
	 */
	public function getLast()
	{
		$this->_db->setQuery("SELECT publish_down FROM $this->_tbl ORDER BY publish_down DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get events for a date range
	 *
	 * @param      string $period  Date range [month, year, week, day]
	 * @param      array  $filters Extra filters to apply to query
	 * @return     array
	 */
	public function getEvents($period='month', $filters=array())
	{
		$gid = (isset($filters['gid'])) ? intval($filters['gid']) : 0;

		// Build the query
		switch ($period)
		{
			case 'month':
				$select_date = $filters['select_date'];
				$select_date_fin = $filters['select_date_fin'];

				$sql = "SELECT $this->_tbl.*
					FROM #__categories AS b, $this->_tbl
					WHERE $this->_tbl.catid = b.id
					AND (((publish_up >= " . $this->_db->quote($select_date . '%') . " AND publish_up <= " . $this->_db->quote($select_date_fin . '%') . " AND publish_down <> '0000-00-00 00:00:00')
						OR (publish_down >= " . $this->_db->quote($select_date . '%') . " AND publish_down <= " . $this->_db->quote($select_date_fin . '%') . " AND publish_down <> '0000-00-00 00:00:00')
						OR (publish_up >= " . $this->_db->quote($select_date . '%') . " AND publish_down <= " . $this->_db->quote($select_date_fin . '%') . " AND publish_down <> '0000-00-00 00:00:00')
						OR (publish_up <= " . $this->_db->quote($select_date . '%') . " AND publish_down >= " . $this->_db->quote($select_date_fin . '%') . " AND publish_down <> '0000-00-00 00:00:00'))
						AND $this->_tbl.state = '1'";

				$sql .= ($filters['category'] != 0) ? " AND b.id=" . intval($filters['category']) . ")" : ")";

				//did we pass in a scope filter
				if (isset($filters['scope']) && $filters['scope'] != '')
				{
					if ($filters['scope'] == 'event')
					{
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']);
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote($filters['scope_id']);
				}

				$sql .= " ORDER BY publish_up ASC";
			break;

			case 'year':
				$year = $filters['year'];

				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl
					WHERE $this->_tbl.catid = b.id
					AND publish_up LIKE " . $this->_db->quote($year . '%') . " AND (publish_down >= " . $this->_db->quote($year . '%') . " OR publish_down = '0000-00-00 00:00:00')
					AND $this->_tbl.state = '1'";

				//did we pass in a scope filter
				if (isset($filters['scope']) && $filters['scope'] != '')
				{
					if ($filters['scope'] == 'event')
					{
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']);
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote($filters['scope_id']);
				}

				$sql .= ($filters['category'] != 0) ? " AND b.id=" . intval($filters['category']) : "";
				$sql .= " ORDER BY publish_up ASC";
				//$sql .= " LIMIT ".$filters['start'].", ".$filters['limit'];
			break;

			case 'week':
				$startdate = $filters['startdate'];
				$enddate = $filters['enddate'];

				$sql = "SELECT * FROM $this->_tbl
					WHERE ((publish_up >= " . $this->_db->quote($startdate . '%') . " AND publish_up <= " . $this->_db->quote($enddate . '%') . ")
					OR (publish_down >= " . $this->_db->quote($startdate . '%') . " AND publish_down <= " . $this->_db->quote($enddate . '%') . ")
					OR (publish_up >= " . $this->_db->quote($startdate . '%') . " AND publish_down <= " . $this->_db->quote($enddate . '%') . ")
					OR (publish_down >= " . $this->_db->quote($enddate . '%') . " AND publish_up <= " . $this->_db->quote($startdate . '%') . "))
					AND state = '1'";

					//did we pass in a scope filter
					if (isset($filters['scope']) && $filters['scope'] != '')
					{
						if ($filters['scope'] == 'event')
						{
							$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']) . ")";
						}
						else
						{
							$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']);
						}
					}

					//did we pass in a scope id filter
					if (isset($filters['scope_id']) && $filters['scope_id'] != '')
					{
						$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote($filters['scope_id']);
					}

					$sql .= "ORDER BY publish_up ASC";
			break;

			case 'day':
				$select_date     = $filters['select_date'];
				$select_date_fin = $filters['select_date_fin'];

				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl
					WHERE $this->_tbl.catid = b.id AND
						((publish_up >= " . $this->_db->quote($select_date) . " AND publish_up <= " . $this->_db->quote($select_date_fin) . ") 
						OR (publish_down >= " . $this->_db->quote($select_date) . " AND publish_down <= " . $this->_db->quote($select_date_fin) . ") 
						OR (publish_up <= " . $this->_db->quote($select_date) . " AND publish_down >= " . $this->_db->quote($select_date_fin) . ") 
						OR (publish_up >= " . $this->_db->quote($select_date) . " AND publish_down <= " . $this->_db->quote($select_date_fin) . "))";

				$sql .= ($filters['category'] != 0) ? " AND b.id=" . $filters['category'] : "";
				$sql .= " AND $this->_tbl.state = '1'";

				//did we pass in a scope filter
				if (isset($filters['scope']) && $filters['scope'] != '')
				{
					if ($filters['scope'] == 'event')
					{
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote($filters['scope']);
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote($filters['scope_id']);
				}

				$sql .= " ORDER BY publish_up ASC";
			break;
		}

		if (isset($filters['limit']))
		{
			if (isset($filters['start']))
			{
				$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
			}
			else
			{
				$sql .= " LIMIT " . $filters['limit'];
			}
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT count(*) FROM $this->_tbl AS a";
		$where = array();
		if ($filters['catid'] > 0)
		{
			$where[] = "a.catid='" . intval($filters['catid']) . "'";
		}
		if ($filters['search'])
		{
			$where[] = "LOWER(a.title) LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		//did we pass in a scope id filter
		if (isset($filters['scope_id']) && $filters['scope_id'] != '' && $filters['scope_id'] != 0)
		{
			$where[] = "a.scope=" . $this->_db->quote('group');
			$where[] = "a.scope_id=" . $this->_db->quote($filters['scope_id']);
		}

		$query .= (count($where)) ? " WHERE " . implode(' AND ', $where) : "";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT a.*, cc.title AS category, u.name AS editor, 'Public' as groupname
			FROM $this->_tbl AS a
			LEFT JOIN #__users AS u ON u.id = a.checked_out
			LEFT JOIN #__categories AS cc ON a.catid=cc.id";

		$where = array();
		if ($filters['catid'] > 0)
		{
			$where[] = "a.catid='" . intval($filters['catid']) . "'";
		}
		if ($filters['search'])
		{
			$where[] = "LOWER(a.title) LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}

		//did we pass in a scope id filter
		if (isset($filters['scope_id']) && $filters['scope_id'] != '' && $filters['scope_id'] != 0)
		{
			$where[] = "a.scope=" . $this->_db->quote('group');
			$where[] = "a.scope_id=" . $this->_db->quote($filters['scope_id']);
		}

		$query .= (count($where)) ? " WHERE " . implode(' AND ', $where) : "";
		$query .= " ORDER BY a.publish_up DESC";

		if (isset($filters['limit']))
		{
			$filters['limit'] = intval($filters['limit']);
			if ($filters['limit'] > 0)
			{
				if (!isset($filters['start']))
				{
					$filters['start'] = 0;
				}
				$filters['start'] = intval($filters['start']);

				$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
			}
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Find all events matching filters
	 *
	 * @param      array   $filters
	 * @return     array
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of events matching filters
	 *
	 * @param      array   $filters
	 * @return     int
	 */
	public function count($filters = array())
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query string for getting list or count of events
	 *
	 * @param      array   $filters
	 * @return     string
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// scope
		if (isset($filters['scope']))
		{
			$where[] = "scope=" . $this->_db->quote($filters['scope']);
		}

		// scope_id
		if (isset($filters['scope_id']))
		{
			$where[] = "scope_id=" . $this->_db->quote($filters['scope_id']);
		}

		// calendar_id
		if (isset($filters['calendar_id']))
		{
			if ($filters['calendar_id'] == '0')
			{
				$where[] = "(calendar_id IS NULL OR calendar_id=0)";
			}
			else
			{
				$where[] = "calendar_id=" . $this->_db->quote($filters['calendar_id']);
			}
		}

		// published
		if (isset($filters['state']) && is_array($filters['state']))
		{
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// publish up/down
		if (isset($filters['publish_up']) && isset($filters['publish_down']))
		{
			$q  = "(publish_up >=" . $this->_db->quote($filters['publish_up']);
			$q .= " OR (publish_down <> '0000-00-00 00:00:00' AND publish_down <=" . $this->_db->quote($filters['publish_down']) . ")";
			$q .= " OR (publish_up <= " . $this->_db->quote($filters['publish_up']) . " AND publish_down >=" . $this->_db->quote($filters['publish_down']) . "))";
			$where[] = $q;
		}

		// repeating event?
		if (isset($filters['repeating']))
		{
			$where[] = "(repeating_rule IS NOT NULL AND repeating_rule<>'')";
		}

		// only non-repeating events?
		if (isset($filters['non_repeating']))
		{
			$where[] = "(repeating_rule IS NULL OR repeating_rule = '')";
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		// specify order?
		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		// limit and start
		if (isset($filters['limit']))
		{
			$start = (isset($filters['start'])) ? $filters['start'] : 0;
			$sql .= " LIMIT " . $start . ", " . $filters['limit'];
		}

		return $sql;
	}
}
