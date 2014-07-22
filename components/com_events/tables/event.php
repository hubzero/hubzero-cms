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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Events table class for an event
 */
class EventsEvent extends JTable
{
	/**
	 * int(12) Primary key
	 *
	 * @var integer
	 */
	var $id               = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $catid            = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $calendar_id      = NULL;

	/**
	 * varchar(255)
	 *
	 * @var integer
	 */
	var $ical_uid         = NULL;

	/**
	 * varchar(100)
	 *
	 * @var integer
	 */
	var $scope            = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $scope_id         = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $title            = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $content          = NULL;

	/**
	 * varchar(120)
	 *
	 * @var string
	 */
	var $contact_info     = NULL;

	/**
	 * varchar(120)
	 *
	 * @var string
	 */
	var $adresse_info     = NULL;

	/**
	 * varchar(240)
	 *
	 * @var string
	 */
	var $extra_info       = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $state            = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $mask             = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by       = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $modified         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $modified_by      = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $publish_up       = NULL;

	/**
	 * varchar(5)
	 *
	 * @var string
	 */
	var $time_zone        = NULL;

	/**
	 * varchar(5)
	 *
	 * @var string
	 */
	var $repeating_rule   = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $publish_down     = NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $approved         = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $registerby       = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $params           = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $restricted       = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $email            = NULL;

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
			$this->setError(JText::_('EVENTS_MUST_HAVE_TITLE'));
			return false;
		}
		if (trim($this->catid) == '' || trim($this->catid) == 0)
		{
			if ($this->scope == 'event')
			{
				$this->setError(JText::_('EVENTS_MUST_HAVE_CATEGORY'));
				return false;
			}
		}
		return true;
	}

	/**
	 * Increase event hit count
	 *
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function hit($oid=NULL)
	{
		$k = $this->_tbl_key;
		if ($oid !== NULL)
		{
			$this->$k = intval($oid);
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET hits=(hits+1) WHERE id=" . $this->_db->Quote($this->id));
		$this->_db->query();
	}

	/**
	 * Set an event to published
	 *
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function publish($oid = NULL, $state = 1, $userId = 0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=1 WHERE id=" . $this->_db->Quote($oid));
		$this->_db->query();
	}

	/**
	 * Set an event to unpublished
	 *
	 * @param      integer $oid Event ID
	 * @return     void
	 */
	public function unpublish($oid=NULL)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET state=0 WHERE id=" . $this->_db->Quote($oid));
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
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] ) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] );
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] ) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] );
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
							$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] ) . ")";
						}
						else
						{
							$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] );
						}
					}

					//did we pass in a scope id filter
					if (isset($filters['scope_id']) && $filters['scope_id'] != '')
					{
						$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote( $filters['scope_id'] );
					}

					$sql .= "ORDER BY publish_up ASC";
			break;

			case 'day':
				$select_date = $filters['select_date'];

				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl
					WHERE $this->_tbl.catid = b.id AND
						((publish_up >= " . $this->_db->quote($select_date . ' 00:00:00') . " AND publish_up <= " . $this->_db->quote($select_date . ' 23:59:59') . ") 
						OR (publish_down >= " . $this->_db->quote($select_date . ' 00:00:00') . " AND publish_down <= " . $this->_db->quote($select_date . ' 23:59:59') . ") 
						OR (publish_up <= " . $this->_db->quote($select_date . ' 00:00:00') . " AND publish_down >= " . $this->_db->quote($select_date . ' 23:59:59') . ") 
						OR (publish_up >= " . $this->_db->quote($select_date . ' 00:00:00') . " AND publish_down <= " . $this->_db->quote($select_date . ' 23:59:59') . "))";

				$sql .= ($filters['category'] != 0) ? " AND b.id=" . $filters['category'] : "";
				$sql .= " AND $this->_tbl.state = '1'";

				//did we pass in a scope filter
				if (isset($filters['scope']) && $filters['scope'] != '')
				{
					if ($filters['scope'] == 'event')
					{
						$sql .= " AND ({$this->_tbl}.scope IS NULL OR {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] ) . ")";
					}
					else
					{
						$sql .= " AND {$this->_tbl}.scope=" . $this->_db->quote( $filters['scope'] );
					}
				}

				//did we pass in a scope id filter
				if (isset($filters['scope_id']) && $filters['scope_id'] != '')
				{
					$sql .= " AND {$this->_tbl}.scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
			$where[] = "a.scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
			$where[] = "a.scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
	public function find( $filters = array() )
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of events matching filters
	 *
	 * @param      array   $filters
	 * @return     int
	 */
	public function count( $filters = array() )
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query string for getting list or count of events
	 *
	 * @param      array   $filters
	 * @return     string
	 */
	private function _buildQuery( $filters = array() )
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// scope
		if (isset($filters['scope']))
		{
			$where[] = "scope=" . $this->_db->quote( $filters['scope'] );
		}

		// scope_id
		if (isset($filters['scope_id']))
		{
			$where[] = "scope_id=" . $this->_db->quote( $filters['scope_id'] );
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
				$where[] = "calendar_id=" . $this->_db->quote( $filters['calendar_id'] );
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
			$q  = "(publish_up >=" . $this->_db->quote( $filters['publish_up'] );
			$q .= " OR (publish_down <> '0000-00-00 00:00:00' AND publish_down <=" . $this->_db->quote( $filters['publish_down'] ) . ")";
			$q .= " OR (publish_up <= " . $this->_db->quote( $filters['publish_up'] ) . " AND publish_down >=" . $this->_db->quote( $filters['publish_down'] ) . "))";
			$where[] = $q;
		}

		// repeating event?
		if (isset($filters['repeating']))
		{
			$where[] = "(repeating_rule IS NOT NULL AND repeating_rule<>'')";
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

