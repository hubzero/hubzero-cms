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
	 * datetime
	 *
	 * @var string
	 */
	var $closed    = NULL;

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
	 * int(11)
	 *
	 * @var integer
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
				$this->setError(JText::_('COM_SUPPORT_ERROR_BLANK_REPORT'));
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
				$this->created = JFactory::getDate()->toSql();
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
				//$this->status = 0;
				$this->open = 0;
			}
		}
		/*else
		{
			$this->open = 1;
			$this->status = 1;
		}

		// Set the status to just "open" if no owner and no resolution
		if (!$this->owner && !$this->resolved)
		{
			$this->open = 1;
			$this->status = 1;
		}*/
		if ($this->owner && is_string($this->owner))
		{
			$owner = JUser::getInstance($this->owner);
			if ($owner && $owner->get('id'))
			{
				$this->owner = (int) $owner->get('id');
			}
		}

		// All new tickets default to "new"
		if (!$this->id)
		{
			$this->open   = 1;
			$this->status = 0;
			// If it has an owner, force it to just open, instead of new
			if ($this->owner)
			{
				$this->status = 1;
			}
		}

		// If status is "open", ensure the resolution is empty
		if ($this->open == 1)
		{
			$this->closed = '0000-00-00 00:00:00';
			$this->resolved = '';
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
			case 'new':     $filter .= " AND open=1 AND status=0 AND owner=0 AND (resolved IS NULL OR resolved='') AND ((SELECT COUNT(*) FROM #__support_comments AS k WHERE k.ticket=f.id) <= 0)"; break;
			case 'waiting': $filter .= " AND open=1 AND status=2";               break;
		}
		if (isset($filters['severity']) && $filters['severity'] != '')
		{
			$filter .= " AND severity=" . $this->_db->quote($filters['severity']);
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
			$filter .= " AND category=" . $this->_db->quote($filters['category']);
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
				$filter .= "owner=0";
			}
			else
			{
				$filter .= "owner=" . $this->_db->quote($filters['owner']);
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
			$filter .= "login=" . $this->_db->quote($filters['reportedby']);
			if (isset($filters['owner']) && $filters['owner'] != '')
			{
				$filter .= ")";
			}
		}

		if (isset($filters['opened']) && $filters['opened'])
		{
			if (is_array($filters['opened']))
			{
				$filter .= " AND (f.created >= " . $this->_db->Quote($filters['opened'][0]) . " AND f.created <= " . $this->_db->Quote($filters['opened'][1]) . ")";
			}
			else
			{
				$filter .= " AND f.created >= " . $this->_db->Quote($filters['opened']);
			}
		}
		if (isset($filters['closed']) && $filters['closed'])
		{
			if (is_array($filters['closed']))
			{
				$filter .= " AND (f.closed >= " . $this->_db->Quote($filters['closed'][0]) . " AND f.closed <= " . $this->_db->Quote($filters['closed'][1]) . ")";
			}
			else
			{
				$filter .= " AND f.closed >= " . $this->_db->Quote($filters['closed']);
			}
		}

		if (isset($filters['group']) && $filters['group'] != '')
		{
			$filter .= " AND `group`=" . $this->_db->quote($filters['group']);
		}
		if ($admin == false && (!isset($filters['owner']) || $filters['owner'] != '') && (!isset($filters['reportedby']) || $filters['reportedby'] != ''))
		{
			$juser = JFactory::getUser();
			$xgroups = \Hubzero\User\Helper::getGroups($juser->get('id'), 'members');
			$groups = '';
			if ($xgroups)
			{
				$g = array();
				foreach ($xgroups as $xgroup)
				{
					$g[] = $this->_db->quote($xgroup->cn);
				}
				$groups = implode(",", $g);
			}
			$filter .= ($groups) ? " OR `group` IN ($groups))" : ")";
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$from = "(
						(SELECT f.id, f.summary, f.report, f.category, f.status, f.severity, f.resolved, f.owner, f.created, f.closed, f.login, f.name, f.email, f.type, f.section, f.group, u.name AS owner_name, u.id AS owner_id
							FROM $this->_tbl AS f LEFT JOIN #__users AS u ON u.id=f.owner ";
			if (isset($filters['tag']) && $filters['tag'] != '')
			{
				$from .= ", #__tags_object AS st, #__tags as t ";
			}
			if (isset($filters['search']) && $filters['search'] != '')
			{
				$from .= "WHERE ";
				$from .= "(LOWER(f.summary) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.report) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(u.username) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.login) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');

				if (is_numeric($filters['search']))
				{
					$from .= " OR ";
					$from .= "id=" . intval($filters['search']);
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
				$from .= "st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag=" . $this->_db->quote($filters['tag']);
			}
			$from .= ") UNION (
				SELECT g.id, g.summary, g.report, g.category, g.status, g.severity, g.resolved, g.owner, g.created, g.closed, g.login, g.name, g.email, g.type, g.section, g.group, ug.name AS owner_name, ug.id AS owner_id
				FROM #__support_comments AS w, $this->_tbl AS g LEFT JOIN #__users AS ug ON ug.id=g.owner
				WHERE w.ticket=g.id";
			if (isset($filters['search']) && $filters['search'] != '')
			{
				$from .= " AND LOWER(w.comment) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
			}
			$from .= ")) AS h";
		}
		else
		{
			$from = "$this->_tbl AS f
					LEFT JOIN #__users AS u ON u.id=f.owner";
			if (isset($filters['tag']) && $filters['tag'] != '')
			{
				$from .= ", #__tags_object AS st, #__tags as t";
			}
			if (isset($filters['tag']) && $filters['tag'] != '')
			{
				$filter .= " AND st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag=" . $this->_db->quote($filters['tag']);
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
	public function getCount($query, $filters=array())
	{
		if (!$query)
		{
			$this->setError(JText::_('Missing conditions'));
			return 0;
		}
		$having = '';
		if (preg_match('/GROUP BY f.id HAVING uniques=\'\d\'/i', $query, $matches) || preg_match('/GROUP BY f.id/i', $query, $matches))
		{
			$having = $matches[0];
			$query = str_replace($matches[0], '', $query);

			$sql = "SELECT f.id, COUNT(DISTINCT t.tag) AS uniques ";
		}
		else
		{
			$sql = "SELECT count(DISTINCT f.id) ";
		}

		$sql .= "FROM $this->_tbl AS f";
		if (strstr($query, 't.`tag`') || (isset($filters['tag']) && $filters['tag'] != ''))
		{
			$sql .= " LEFT JOIN #__tags_object AS st on st.objectid=f.id AND st.tbl='support'
					LEFT JOIN #__tags AS t ON st.tagid=t.id";
		}

		$sql .= $this->parseFind($filters) . " AND " . $query;
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.login) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
			if (is_numeric($filters['search']))
			{
				$sql .= " OR id=" . $filters['search'];
			}
			$sql .= ") ";
		}
		$sql .= $having;

		if ($having)
		{
			$this->_db->setQuery($sql);
			$results = $this->_db->loadObjectList();
			return count($results);
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
		$having = '';
		if (preg_match('/GROUP BY f.id HAVING uniques=\'\d\'/i', $query, $matches))
		{
			$having = $matches[0];
			$query = str_replace($matches[0], '', $query);
		}

		$sql = "SELECT DISTINCT f.`id`, f.`summary`, f.`report`, f.`category`, f.`open`, f.`status`, f.`severity`, f.`resolved`, f.`group`, f.`owner`, f.`created`, f.`login`, f.`name`, f.`email` ";
		if ($having)
		{
			$sql .= ", COUNT(DISTINCT t.tag) AS uniques ";
		}
		$sql .= "FROM $this->_tbl AS f";
		if (strstr($query, 't.`tag`') || (isset($filters['tag']) && $filters['tag'] != ''))
		{
			$sql .= " LEFT JOIN #__tags_object AS st on st.objectid=f.id AND st.tbl='support'
					LEFT JOIN #__tags AS t ON st.tagid=t.id";
		}
		$sql .= $this->parseFind($filters) . " AND " . $query;
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$sql .= " AND ";
			$sql .= "(
						LOWER(f.report) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
						OR LOWER(f.login) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
			if (is_numeric($filters['search']))
			{
				$sql .= " OR id=" . $filters['search'];
			}
			$sql .= ") ";
		}
		$sql .= $having;

		if ($filters['sort'] == 'severity')
		{
			$sql .= " ORDER BY CASE severity ";
			$sql .= " WHEN 'critical' THEN 5";
			$sql .= " WHEN 'major'    THEN 4";
			$sql .= " WHEN 'normal'   THEN 3";
			$sql .= " WHEN 'minor'    THEN 2";
			$sql .= " WHEN 'trivial'  THEN 1";
			$sql .= " END " . $filters['sortdir'];
		}
		else
		{
			$sql .= " ORDER BY `" . $filters['sort'] . '` ' . $filters['sortdir'];
		}

		$sql .= ($filters['limit']) ? " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']) : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Add tag and group filters previously supported in ticket system
	 * (ex: when clicking a tag within the ticket system)
	 *
	 * @param      array   $filters Filters to build query from
	 * @return     string SQL
	 */
	public function parseFind($filters)
	{
		$filter = " WHERE report!=''";

		if (isset($filters['group']) && $filters['group'] != '')
		{
			$filter .= " AND `group`=" . $this->_db->quote($filters['group']);
		}

		if (isset($filters['tag']) && $filters['tag'] != '')
		{
			$filter .= " AND st.objectid=f.id AND st.tbl='support' AND st.tagid=t.id AND t.tag=" . $this->_db->quote($filters['tag']);
		}

		return $filter;
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
			$sql = "SELECT DISTINCT `id`, `summary`, `report`, `category`, `open`, `status`, `severity`, `resolved`, `owner`, `created`, `closed`, `login`, `name`, `email`, `group`, owner_name, owner_id";
		}
		else
		{
			$sql = "SELECT DISTINCT f.id, f.summary, f.report, f.category, f.open, f.status, f.severity, f.resolved, f.group, f.owner, f.created, f.closed, f.login, f.name, f.email, u.name AS owner_name, u.id AS owner_id";
		}
		$sql .= " FROM $filter";
		$sql .= " ORDER BY ".$filters['sort'] . ' ' . $filters['sortdir'];
		$sql .= ($filters['limit']) ? " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'] : "";

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
			$filter .= " AND id < $this->id";
			$filters['sortby'] = "id DESC";
		}
		elseif ($which == 'next')
		{
			$filter .= " AND id > $this->id";
			$filters['sortby'] = "id ASC";
		}

		$this->_db->setQuery("SELECT id FROM $filter ORDER BY " . str_replace('f.', '', $filters['sortby']) . " LIMIT 1");
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
		$year = ($year) ? $year : JFactory::getDate()->format("Y");
		$endyear = intval($year) + 1;

		$sql = "SELECT count(*)
				FROM $this->_tbl
				WHERE report!=''
				AND type=" . $this->_db->Quote($type);
		if (!$group)
		{
			//$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else
		{
			$sql .= " AND `group`=" . $this->_db->quote($group);
		}
		$sql .= " AND created BETWEEN " . $this->_db->quote($year . "-" . $month . "-" . $day . " 00:00:00") . " AND " . $this->_db->quote($endyear . "-" . $month . "-" . $day . " 00:00:00");

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
		$year = ($year) ? $year : JFactory::getDate()->format("Y");
		$endyear = intval($year) + 1;

		$sql = "SELECT COUNT(DISTINCT k.ticket)
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!=''
				AND f.type='$type'
				AND f.open=0
				AND k.ticket=f.id
				AND k.created BETWEEN " . $this->_db->quote($year . "-" . $month . "-" . $day . " 00:00:00") . " AND " . $this->_db->quote($endyear . "-" . $month . "-" . $day . " 00:00:00");
		if (!$group)
		{
			//$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		}
		else
		{
			$sql .= " AND f.`group`=" . $this->_db->Quote($group);
		}
		if ($username)
		{
			if (is_array($username))
			{
				// If username is an array, we'll use that to grab the 'everybody else' list
				// Start by impoloding the array
				$usernames = implode("','", $username);
				$sql .= " AND (";
				$sql .= "f.owner NOT IN ('" . $usernames . "')";
				// Include unassigned tickets in this number
				$sql .= " OR f.owner=0";
				$sql .= ")";
			}
			else
			{
				$sql .= " AND f.owner=" . $this->_db->Quote($username);
			}
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
				AND type=" . $this->_db->Quote($type) . "
				AND open=1";
		if (!$group)
		{
			//$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else
		{
			$sql .= " AND `group`=" . $this->_db->Quote($group);
		}
		if ($unassigned)
		{
			$sql .= " AND owner=0 AND (resolved IS NULL OR resolved='')";
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
	public function getCountOfTicketsClosedInMonth($type=0, $year='', $month='01', $group=null, $username=null)
	{
		$year = ($year) ? $year : JFactory::getDate()->format("Y");

		$nextyear  = (intval($month) == 12) ? $year+1 : $year;
		$nextmonth = (intval($month) == 12) ? '01' : sprintf("%02d",intval($month)+1);

		$sql = "SELECT COUNT(DISTINCT k.ticket)
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!=''
				AND f.type=" . $this->_db->Quote($type) . "
				AND f.open=0
				AND k.ticket=f.id
				AND k.created>=" . $this->_db->quote($year . "-" . $month . "-01 00:00:00") . "
				AND k.created<" . $this->_db->quote($nextyear . "-" . $nextmonth . "-01 00:00:00");
		if (!$group)
		{
			//$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		}
		else
		{
			$sql .= " AND f.`group`=" . $this->_db->Quote($group);
		}
		if ($username)
		{
			$sql .= " AND k.created_by=" . $this->_db->Quote(JUser::getInstance($username)->get('id'));
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
		$year = ($year) ? $year : JFactory::getDate()->format("Y");

		$nextyear  = (intval($month) == 12) ? $year+1 : $year;
		$nextmonth = (intval($month) == 12) ? '01' : sprintf("%02d",intval($month)+1);

		$sql = "SELECT count(*)
				FROM $this->_tbl
				WHERE report!=''
				AND type=" . $this->_db->Quote($type) . "
				AND created>=" . $this->_db->quote($year . "-" . $month . "-01 00:00:00") . "
				AND created<" . $this->_db->quote($nextyear . "-" . $nextmonth . "-01 00:00:00");
		if (!$group)
		{
			//$sql .= " AND (`group`='' OR `group` IS NULL)";
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
		$year = ($year) ? $year : JFactory::getDate()->format("Y");

		$sql = "SELECT k.ticket, UNIX_TIMESTAMP(f.created) AS t_created, UNIX_TIMESTAMP(MAX(k.created)) AS c_created
				FROM #__support_comments AS k, $this->_tbl AS f
				WHERE f.report!=''
				AND f.type=" . $this->_db->Quote($type) . "
				AND f.open=0
				AND k.ticket=f.id
				AND f.created>=" . $this->_db->quote($year . "-01-01 00:00:00");
		if (!$group)
		{
			//$sql .= " AND (f.`group`='' OR f.`group` IS NULL)";
		}
		else
		{
			$sql .= " AND f.`group`=" . $this->_db->Quote($group);
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

			$days     = floor($difference/60/60/24);
			$hours    = floor(($difference - $days*60*60*24)/60/60);
			$minutes  = floor(($difference - $days*60*60*24 - $hours*60*60)/60);

			$lifetime = array($days, $hours, $minutes);
		}

		return $lifetime;
	}
}

