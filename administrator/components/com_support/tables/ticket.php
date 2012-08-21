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
 * Table class for support ticket
 */
class SupportTicket extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(3)  --  0 = closed, 1 = open
	 * 
	 * @var integer
	 */
	var $open      = NULL;

	/**
	 * int(3)  --  0 = new, 1 = accepted, 2 = waiting
	 * 
	 * @var integer
	 */
	var $status     = NULL;

	/**
	 * datetime
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * string(200)
	 * 
	 * @var string
	 */
	var $login      = NULL;

	/**
	 * string(30)
	 * 
	 * @var string
	 */
	var $severity   = NULL;

	/**
	 * string(50)
	 * 
	 * @var string
	 */
	var $owner      = NULL;

	/**
	 * string(50)
	 * 
	 * @var string
	 */
	var $category   = NULL;

	/**
	 * string(250)
	 * 
	 * @var string
	 */
	var $summary    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $report     = NULL;

	/**
	 * string(50)
	 * 
	 * @var string
	 */
	var $resolved   = NULL;

	/**
	 * string(200)
	 * 
	 * @var string
	 */
	var $email      = NULL;

	/**
	 * string(200)
	 * 
	 * @var string
	 */
	var $name       = NULL;

	/**
	 * string(50)
	 * 
	 * @var string
	 */
	var $os         = NULL;

	/**
	 * string(50)
	 * 
	 * @var string
	 */
	var $browser    = NULL;

	/**
	 * string(200)
	 * 
	 * @var string
	 */
	var $ip         = NULL;

	/**
	 * string(200)
	 * 
	 * @var string
	 */
	var $hostname   = NULL;

	/**
	 * string(250)
	 * 
	 * @var string
	 */
	var $uas        = NULL;

	/**
	 * string(250)
	 * 
	 * @var string
	 */
	var $referrer   = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $cookies    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $instances  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $section    = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $type       = NULL;

	/**
	 * string(250)
	 * 
	 * @var string
	 */
	var $group      = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_tickets', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->id) 
		{
			if (!trim($this->report))
			{
				$this->setError(JText::_('SUPPORT_ERROR_BLANK_REPORT'));
				return false;
			}
			
			if (!trim($this->summary))
			{
				$this->summary = substr($this->report, 0, 70);
				if (strlen($this->summary) >=70)
				{
					$this->summary .= '...';
				}
			}
		}
		
		if (!$this->id)
		{
			if (!$this->created || $this->created == '0000-00-00 00:00:00')
			{
				$this->created = date("Y-m-d H:i:s");
			}
		}

		// Set the status of the ticket
		if ($this->resolved)
		{
			if ($this->resolved == 1)
			{
				// "waiting user response"
				$this->status = 2;
				$this->open = 1;
			}
			else
			{
				// If there's a resolution, close the ticket
				$this->status = 0;
				$this->open = 0;
			}
		}
		else
		{
			$this->open = 1;
			$this->status = 1;
		}

		// Set the status to just "open" if no owner and no resolution
		if (!$this->owner && !$this->resolved)
		{
			$this->open = 1;
			$this->status = 1;
		}

		// If status is "open", ensure the resolution is empty
		if ($this->open == 1)
		{
			$this->resolved = '';
		}

		// All new tickets default to "new"
		if (!$this->id)
		{
			$this->status = 0;
		}

		return true;
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $admin   Admin access?
	 * @return     string SQL
	 */
	public function buildQuery($filters, $admin)
	{
		$filter = " WHERE report!=''";

		switch ($filters['status'])
		{
			case 'open':    $filter .= " AND open=1"; break;
			case 'closed':  $filter .= " AND open=0";               break;
			case 'all':     $filter .= "";                            break;
			case 'new':     $filter .= " AND open=1 AND status=0 AND (owner IS NULL OR owner='') AND (resolved IS NULL OR resolved='') AND ((SELECT COUNT(*) FROM #__support_comments AS k WHERE k.ticket=f.id) <= 0)"; break;
			case 'waiting': $filter .= " AND open=1 AND status=2";               break;
		}
		if (isset($filters['severity']) && $filters['severity'] != '') 
		{
			$filter .= " AND severity='".$filters['severity']."'";
		}
		if ($admin) 
		{
			switch ($filters['type'])
			{
				case '3': $filter .= " AND type=3"; break;
				case '2': $filter .= ""; break;
				case '1': $filter .= " AND type=1"; break;
				case '0':
				default:  $filter .= " AND type=0"; break;
			}
		} 
		else 
		{
			$filter .= " AND type=0";
		}
		if (isset($filters['category']) && $filters['category'] != '') 
		{
			$filter .= " AND category='" . $filters['category'] . "'";
		}
		if (isset($filters['owner']) && $filters['owner'] != '') 
		{
			$filter .= " AND ";
			if ($admin == false 
			 && (!isset($filters['owner']) || $filters['owner'] != '') 
			 && (!isset($filters['reportedby']) || $filters['reportedby'] != '')) 
			{
				$filter .= "(";
			}
			if (isset($filters['reportedby']) && $filters['reportedby'] != '') 
			{
				$filter .= "(";
			}
			if ($filters['owner'] == 'none') 
			{
				$filter .= "(owner='' OR owner IS NULL)";
			} 
			else 
			{
				$filter .= "owner='" . $filters['owner'] . "'";
			}
		}
		if (isset($filters['reportedby']) && $filters['reportedby'] != '') 
		{
			if (isset($filters['owner']) && $filters['owner'] != '') 
			{
				$filter .= " OR ";
			} 
			else 
			{
				$filter .= " AND ";
			}
			$filter .= "login='" . $filters['reportedby'] . "'";
			if (isset($filters['owner']) && $filters['owner'] != '') 
			{
				$filter .= ")";
			}
		}
		if (isset($filters['group']) && $filters['group'] != '') 
		{
			$filter .= " AND `group`='" . $filters['group'] . "'";
		}
		if ($admin == false && (!isset($filters['owner']) || $filters['owner'] != '') && (!isset($filters['reportedby']) || $filters['reportedby'] != '')) 
		{
			ximport('Hubzero_User_Helper');
			$juser =& JFactory::getUser();
			$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'members');
			$groups = '';
			if ($xgroups) 
			{
				$g = array();
				foreach ($xgroups as $xgroup)
				{
					$g[] = $xgroup->cn;
				}
				$groups = implode("','", $g);
			}
			$filter .= ($groups) ? " OR `group` IN ('$groups'))" : ")";
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$from = "(
						(SELECT f.id, f.summary, f.report, f.category, f.status, f.severity, f.resolved, f.owner, f.created, f.login, f.name, f.email, f.type, f.section, f.group 
							FROM $this->_tbl AS f ";
			if (isset($filters['tag']) && $filters['tag'] != '') 
			{
				$from .= ", #__tags_object AS st, #__tags as t ";
			}
			if (isset($filters['search']) && $filters['search'] != '') 
			{
				$from .= "WHERE ";
				$from .= "(LOWER(f.summary) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.report) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.owner) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.name) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.login) LIKE '%" . $filters['search'] . "%'";

				if (is_numeric($filters['search'])) 
				{
					$from .= " OR ";
					$from .= "id=" . $filters['search'];
				}
				$from .= ") ";
			}
			if (isset($filters['tag']) && $filters['tag'] != '') 
			{
				if (!isset($filters['search']) || $filters['search'] == '') 
				{
					$from .= "WHERE ";
				} 
				else 
				{
					$from .= " AND ";
				}
				$from .= "st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag='" . $filters['tag'] . "'";
			}
			$from .= ") UNION (
				SELECT g.id, g.summary, g.report, g.category, g.status, g.severity, g.resolved, g.owner, g.created, g.login, g.name, g.email, g.type, g.section, g.group
				FROM #__support_comments AS w, $this->_tbl AS g
				WHERE w.ticket=g.id";
			if (isset($filters['search']) && $filters['search'] != '') 
			{
				$from .= " AND LOWER(w.comment) LIKE '%" . $filters['search'] . "%'";
			}
			$from .= ")) AS h";
		} 
		else 
		{
			$from = "$this->_tbl AS f";
			if (isset($filters['tag']) && $filters['tag'] != '') 
			{
				$from .= ", #__tags_object AS st, #__tags as t";
			}
			if (isset($filters['tag']) && $filters['tag'] != '') 
			{
				$filter .= " AND st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag='" . $filters['tag'] . "'";
			}
		}

		$query = $from . " " . $filter;

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      string  $query Filters to build query from
	 * @return     integer
	 */
	public function getCount($query)
	{
		if (!$query)
		{
			$this->setError(JText::_('Missing conditions'));
			return 0;
		}
		$sql = "SELECT count(DISTINCT f.id) 
				FROM $this->_tbl AS f";
		if (strstr($query, 't.`tag`'))
		{
			$sql .= " LEFT JOIN #__tags_object AS st on st.objectid=f.id AND st.tbl='support' 
					LEFT JOIN #__tags AS t ON st.tagid=t.id";
		}
		$sql .= " WHERE " . $query;
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.owner) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.name) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.login) LIKE '%" . $filters['search'] . "%'";
			if (is_numeric($filters['search'])) 
			{
				$sql .= " OR id=" . $filters['search'];
			}
			$sql .= ") ";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a record count
	 * 
	 * @param      string  $query Filters to build query from
	 * @return     integer
	 */
	public function getRecords($query, $filters=array())
	{
		if (!$query)
		{
			$this->setError(JText::_('Missing conditions'));
			return array();
		}
		$sql = "SELECT DISTINCT f.`id`, f.`summary`, f.`report`, f.`category`, f.`open`, f.`status`, f.`severity`, f.`resolved`, f.`group`, f.`owner`, f.`created`, f.`login`, f.`name`, f.`email` 
				FROM $this->_tbl AS f";
		if (strstr($query, 't.`tag`'))
		{
			$sql .= " LEFT JOIN #__tags_object AS st on st.objectid=f.id AND st.tbl='support' 
					LEFT JOIN #__tags AS t ON st.tagid=t.id";
		}
		$sql .= " WHERE " . $query;
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.owner) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.name) LIKE '%" . $filters['search'] . "%' 
						OR LOWER(f.login) LIKE '%" . $filters['search'] . "%'";
			if (is_numeric($filters['search'])) 
			{
				$sql .= " OR id=" . $filters['search'];
			}
			$sql .= ") ";
		}

		$sql .= " ORDER BY " . $filters['sort'] . ' ' . $filters['sortdir'];
		$sql .= ($filters['limit']) ? " LIMIT " . $filters['start'] . "," . $filters['limit'] : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $admin   Admin access?
	 * @return     integer
	 */
	public function getTicketsCount($filters=array(), $admin=false)
	{
		$filter = $this->buildQuery($filters, $admin);

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$sql = "SELECT count(DISTINCT id) FROM $filter";
		} 
		else 
		{
			$sql = "SELECT count(DISTINCT f.id) FROM $filter";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $admin   Admin access?
	 * @return     array
	 */
	public function getTickets($filters=array(), $admin=false)
	{
		$filter = $this->buildQuery($filters, $admin);

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$sql = "SELECT DISTINCT `id`, `summary`, `report`, `category`, `open`, `status`, `severity`, `resolved`, `owner`, `created`, `login`, `name`, `email`, `group`";
		} 
		else 
		{
			$sql = "SELECT DISTINCT f.id, f.summary, f.report, f.category, f.open, f.status, f.severity, f.resolved, f.group, f.owner, f.created, f.login, f.name, f.email";
		}
		$sql .= " FROM $filter";
		$sql .= " ORDER BY ".$filters['sort'] . ' ' . $filters['sortdir'];
		$sql .= ($filters['limit']) ? " LIMIT " . $filters['start'] . "," . $filters['limit'] : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the next or previous ticket ID for a set of filters
	 * 
	 * @param      string  $which   prev or next
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $authorized Admin access?
	 * @return     integer
	 */
	public function getTicketId($which, $filters, $authorized=false)
	{
		$filter = $this->buildQuery($filters, $authorized);

		if ($which == 'prev') 
		{
			$filter .= " AND f.id < $this->id";
			$filters['sortby'] = "f.id DESC";
		} 
		elseif ($which == 'next') 
		{
			$filter .= " AND f.id > $this->id";
			$filters['sortby'] = "f.id ASC";
		}

		$this->_db->setQuery("SELECT f.id FROM $filter ORDER BY " . $filters['sortby'] . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of tickets opened for a given time period
	 * 
	 * @param      integer $type     Ticket type
	 * @param      string  $year     Year to calculate for
	 * @param      string  $month    Month to calculate for
	 * @param      string  $day      Day to calculate for
	 * @param      string  $group    Group to calculate data for
	 * @return     integer
	 */
	public function getCountOfTicketsOpened($type=0, $year='', $month='01', $day='01', $group=null)
	{
		$year = ($year) ? $year : date("Y");

		$sql = "SELECT count(*) 
				FROM $this->_tbl 
				WHERE report!='' 
				AND type='$type'";
		if (!$group) 
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND `group`='$group'";
		}
		$sql .= " AND created>='" . $year . "-" . $month . "-" . $day . " 00:00:00'";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of tickets closed for a given time period
	 * 
	 * @param      integer $type     Ticket type
	 * @param      string  $year     Year to calculate for
	 * @param      string  $month    Month to calculate for
	 * @param      string  $day      Day to calculate for
	 * @param      string  $username User to get count for
	 * @param      string  $group    Group to calculate data for
	 * @return     integer
	 */
	public function getCountOfTicketsClosed($type=0, $year='', $month='01', $day='01', $username=null, $group=null)
	{
		$year = ($year) ? $year : date("Y");

		$sql = "SELECT COUNT(DISTINCT k.ticket) 
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!='' 
				AND f.type='$type' 
				AND f.status=2 
				AND k.ticket=f.id 
				AND k.created>='" . $year . "-" . $month . "-" . $day . " 00:00:00'";
		if (!$group) 
		{
			$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND f.`group`='$group'";
		}
		if ($username) 
		{
			$sql .= " AND k.created_by='" . $username . "'";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of open tickets
	 * 
	 * @param      integer $type       Ticket type
	 * @param      boolean $unassigned Include unassigned tickets?
	 * @param      string  $group      Group to calculate data for
	 * @return     integer
	 */
	public function getCountOfOpenTickets($type=0, $unassigned=false, $group=null)
	{
		$sql = "SELECT count(*) 
				FROM $this->_tbl 
				WHERE report!='' 
				AND type='$type' 
				AND (status=0 OR status=1)";
		if (!$group) 
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND `group`='$group'";
		}
		if ($unassigned) 
		{
			$sql .= " AND (owner IS NULL OR owner='') AND (resolved IS NULL OR resolved='')";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Count the number of tickets closed in a given month
	 * 
	 * @param      integer $type  Ticket type
	 * @param      string  $year  Year to calculate for
	 * @param      string  $month Month to calculate for
	 * @param      string  $group Group to calculate data for
	 * @return     integer
	 */
	public function getCountOfTicketsClosedInMonth($type=0, $year='', $month='01', $group=null)
	{
		$year = ($year) ? $year : date("Y");

		$nextyear  = (intval($month) == 12) ? $year+1 : $year;
		$nextmonth = (intval($month) == 12) ? '01' : sprintf("%02d",intval($month)+1);

		$sql = "SELECT COUNT(DISTINCT k.ticket) 
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!='' 
				AND f.type='$type' 
				AND f.status=2 
				AND k.ticket=f.id 
				AND k.created>='" . $year . "-" . $month . "-01 00:00:00' 
				AND k.created<'" . $nextyear . "-" . $nextmonth . "-01 00:00:00'";
		if (!$group) 
		{
			$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND f.`group`='$group'";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Count the number of tickets opened in a given month
	 * 
	 * @param      integer $type  Ticket type
	 * @param      string  $year  Year to calculate for
	 * @param      string  $month Month to calculate for
	 * @param      string  $group Group to calculate data for
	 * @return     integer
	 */
	public function getCountOfTicketsOpenedInMonth($type=0, $year='', $month='01', $group=null)
	{
		$year = ($year) ? $year : date("Y");

		$nextyear  = (intval($month) == 12) ? $year+1 : $year;
		$nextmonth = (intval($month) == 12) ? '01' : sprintf("%02d",intval($month)+1);

		$sql = "SELECT count(*) 
				FROM $this->_tbl 
				WHERE report!='' 
				AND type=" . $type . " 
				AND created>='" . $year . "-" . $month . "-01 00:00:00' 
				AND created<'" . $nextyear . "-" . $nextmonth . "-01 00:00:00'";
		if (!$group) 
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND `group`='$group'";
		}

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get the average lifetime of a ticket
	 * 
	 * @param      integer $type  Ticket type
	 * @param      string  $year  Year to calculate for
	 * @param      string  $group Group to calculate data for
	 * @return     array
	 */
	public function getAverageLifeOfTicket($type=0, $year='', $group=null)
	{
		$year = ($year) ? $year : date("Y");

		$sql = "SELECT k.ticket, UNIX_TIMESTAMP(f.created) AS t_created, UNIX_TIMESTAMP(MAX(k.created)) AS c_created
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!='' 
				AND f.type='$type' 
				AND f.status=2 
				AND k.ticket=f.id 
				AND f.created>='" . $year . "-01-01 00:00:00'";
		if (!$group) 
		{
			$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		} 
		else 
		{
			$sql .= " AND f.`group`='$group'";
		}
		$sql .= " GROUP BY k.ticket";
		$this->_db->setQuery($sql);
		$times = $this->_db->loadObjectList();

		$lifetime = array();

		if ($times) 
		{
			$count = 0;
			$lt = 0;
			foreach ($times as $tim)
			{
				$lt += $tim->c_created - $tim->t_created;
				$count++;
			}
			$difference = ($lt / $count);
			if ($difference < 0) 
			{
				$difference = 0;
			}

			$days = floor($difference/60/60/24);
			$hours = floor(($difference - $days*60*60*24)/60/60);
			$minutes = floor(($difference - $days*60*60*24 - $hours*60*60)/60);

			$lifetime = array($days, $hours, $minutes);
		}

		return $lifetime;
	}
}

