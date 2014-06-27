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
 * Support controller class for ticket stats
 */
class SupportControllerStats extends \Hubzero\Component\AdminController
{
	/**
	 * Displays some overview stats of tickets
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$this->view->title = JText::_(strtoupper($this->_name));

		$type = JRequest::getVar('type', 'submitted');
		$this->view->type = ($type == 'automatic') ? 1 : 0;

		$this->view->group = JRequest::getVar('group', '');

		$this->view->sort = JRequest::getVar('sort', 'name');

		$jconfig = JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		$year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));

		$this->view->year = $year;
		$this->view->opened = array();
		$this->view->closed = array();

		$st = new SupportTicket($this->database);

		$sql = "SELECT DISTINCT(s.`group`), g.description
				FROM #__support_tickets AS s
				LEFT JOIN #__xgroups AS g ON g.cn=s.`group`
				WHERE s.`group` !='' AND s.`group` IS NOT NULL
				AND s.type=" . $this->database->quote($this->view->type) . "
				ORDER BY g.description ASC";
		$this->database->setQuery($sql);
		$this->view->groups = $this->database->loadObjectList();

		// Users
		$this->view->users = null;

		if ($this->view->group)
		{
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a, #__xgroups AS g, #__xgroups_members AS gm"
				. "\n WHERE g.cn=" . $this->database->quote($this->view->group) . " AND g.gidNumber=gm.gidNumber AND gm.uidNumber=a.id"
				. "\n ORDER BY a.name";
		}
		else
		{
			$query = "SELECT DISTINCT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.id"	// map user to aro
				. "\n WHERE a.block = '0' AND s.type=" . $this->database->quote($this->view->type)
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$users = $this->database->loadObjectList();

		// First ticket
		$sql = "SELECT YEAR(created)
				FROM #__support_tickets
				WHERE report!=''
				AND type=" . $this->database->quote($this->view->type) . " ORDER BY created ASC LIMIT 1";
		$this->database->setQuery($sql);
		$first = intval($this->database->loadResult());

		// Opened tickets
		$sql = "SELECT id, created, YEAR(created) AS `year`, MONTH(created) AS `month`, status, owner
				FROM #__support_tickets
				WHERE report!=''
				AND type=" . $this->database->quote($this->view->type) . " AND open=1";
		if (!$this->view->group)
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else
		{
			$sql .= " AND `group`=" . $this->database->quote($this->view->group);
		}
		$sql .= " ORDER BY created ASC";
		$this->database->setQuery($sql);
		$openTickets = $this->database->loadObjectList();

		$owners = array();

		$open = array();
		$this->view->opened['open'] = 0;
		$this->view->opened['new'] = 0;
		$this->view->opened['unassigned'] = 0;
		foreach ($openTickets as $o)
		{
			if (!isset($open[$o->year]))
			{
				$open[$o->year] = array();
			}
			if (!isset($open[$o->year][$o->month]))
			{
				$open[$o->year][$o->month] = 0;
			}
			$open[$o->year][$o->month]++;

			$this->view->opened['open']++;

			if (!$o->status)
			{
				$this->view->opened['new']++;
			}
			if (!$o->owner)
			{
				$this->view->opened['unassigned']++;
			}
			else
			{
				if (!isset($owners[$o->owner]))
				{
					$owners[$o->owner] = 0;
				}
				$owners[$o->owner]++;
			}
		}

		// Closed tickets
		$sql = "SELECT t.id AS ticket, t.owner AS created_by, t.closed AS created, YEAR(t.closed) AS `year`, MONTH(t.closed) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(t.closed) AS closed
				FROM #__support_tickets AS t
				WHERE t.report!=''
				AND t.type=" . $this->database->Quote($this->view->type) . " AND t.open=0";
		if (!$this->view->group || $this->view->group == '_none_')
		{
			$sql .= " AND (t.`group`='' OR t.`group` IS NULL)";
		}
		else if ($this->view->group)
		{
			$sql .= " AND t.`group`=" . $this->database->Quote($this->view->group);
		}
		$sql .= " ORDER BY t.closed ASC";

		$this->database->setQuery($sql);
		$clsd = $this->database->loadObjectList();

		$this->view->opened['closed'] = 0;
		$closedTickets = array();
		foreach ($clsd as $closed)
		{
			if (!isset($closedTickets[$closed->ticket]))
			{
				$closedTickets[$closed->ticket] = $closed;
			}
			else
			{
				if ($closedTickets[$closed->ticket]->created < $closed->created)
				{
					$closedTickets[$closed->ticket] = $closed;
				}
			}
		}
		$this->view->closedTickets = $closedTickets;
		$closed = array();
		foreach ($closedTickets as $o)
		{
			if (!isset($closed[$o->year]))
			{
				$closed[$o->year] = array();
			}
			if (!isset($closed[$o->year][$o->month]))
			{
				$closed[$o->year][$o->month] = 0;
			}
			$closed[$o->year][$o->month]++;
			$this->view->opened['closed']++;
		}

		// Group data by year and gather some info for each user
		$y = date("Y");
		$y++;
		$this->view->closedmonths = array();
		$this->view->openedmonths = array();
		for ($k=$first, $n=$y; $k < $n; $k++)
		{
			$this->view->closedmonths[$k] = array();
			$this->view->openedmonths[$k] = array();

			for ($i = 1; $i <= 12; $i++)
			{
				if ($k == $year && $i > $month)
				{
					break;
					//$this->view->closedmonths[$k][$i] = 'null';
					//$this->view->openedmonths[$k][$i] = 'null';
				}
				else
				{
					$this->view->closedmonths[$k][$i] = (isset($closed[$k]) && isset($closed[$k][$i])) ? $closed[$k][$i] : 0; //$st->getCountOfTicketsClosedInMonth($this->view->type, $k, sprintf("%02d",$i), $this->view->group);
					$this->view->openedmonths[$k][$i] = (isset($open[$k]) && isset($open[$k][$i]))     ? $open[$k][$i]   : 0; //$st->getCountOfTicketsOpenedInMonth($this->view->type, $k, sprintf("%02d",$i), $this->view->group);
				}

				foreach ($users as $j => $user)
				{
					if (!isset($user->total))
					{
						$user->total = 0;
					}
					if (!isset($user->tickets))
					{
						$user->tickets = array();
					}
					if (!isset($user->closed))
					{
						$user->closed = array();
					}
					if (!isset($user->closed[$k]))
					{
						$user->closed[$k] = array();
					}

					if ($i <= "9"&preg_match("#(^[1-9]{1})#",$i))
					{
						$month = "0$i";
					}
					if ($k == $year && $i > $month)
					{
						$user->closed[$k][$i] = 'null';
					}
					else
					{
						$user->closed[$k][$i] = 0;
						foreach ($clsd as $c)
						{
							if (intval($c->year) == intval($k) && intval($c->month) == intval($i))
							{
								if ($c->created_by == $user->id)
								{
									$user->closed[$k][$i]++;
									$user->total++;
									$user->tickets[] = $c;
								}
							}
						}
					}

					$users[$j] = $user;
				}
			}
		}

		// Sort users by number of tickets closed
		$u = array();
		foreach ($users as $k => $user)
		{
			$user->assigned = 0;
			if (isset($owners[$user->id]))
			{
				$user->assigned = $owners[$user->id];
			}

			$key = (string) $user->total;
			if (isset($u[$key]))
			{
				$key .= '.' . $k;
			}
			$u[$key] = $user;
		}
		krsort($u);
		$this->view->users  = $u;//$users;

		// Set the config
		$this->view->config = $this->config;
		$this->view->first  = $first;
		$this->view->month  = $month;

		// Output HTML
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}
}
