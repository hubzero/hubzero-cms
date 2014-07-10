<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Supporttickets;

use Hubzero\Module\Module;

/**
 * Module class for com_support ticket data
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');

		$juser    = \JFactory::getUser();
		$database = \JFactory::getDBO();
		$jconfig  = \JFactory::getConfig();

		$st = new \SupportTicket($database);

		$sq = new \SupportQuery($database);
		$types = array(
			'common' => $sq->getCommon()
			//'mine'   => $sq->getMine()
		);
		// Loop through each grouping
		foreach ($types as $key => $queries)
		{
			if (!is_array($queries) || count($queries) <= 0)
			{
				$one = new \stdClass;
				$one->count = 0;
				$one->id    = 0;

				$two = new \stdClass;
				$two->count = 0;
				$two->id    = 0;

				$three = new \stdClass;
				$three->count = 0;
				$three->id    = 0;

				$types[$key] = $queries = array(
					$one,
					$two,
					$three
				);
			}
			// Loop through each query in a group
			foreach ($queries as $k => $query)
			{
				if ($query->id)
				{
					// Build the query from the condition set
					if (!$query->query)
					{
						$query->query = $sq->getQuery($query->conditions);
					}
					// Get a record count
					$types[$key][$k]->count = $st->getCount($query->query);
				}
			}
		}

		$this->topened = $types['common'];

		$this->offset = $jconfig->getValue('config.offset');

		$year  = \JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));

		$this->year = $year;
		$this->opened = array();
		$this->closed = array();

		// First ticket
		$sql = "SELECT YEAR(created)
				FROM `#__support_tickets`
				WHERE report!=''
				AND type='0' ORDER BY created ASC LIMIT 1";
		$database->setQuery($sql);
		$first = intval($database->loadResult());

		// Opened tickets
		$sql = "SELECT id, created, YEAR(created) AS `year`, MONTH(created) AS `month`, status, owner
				FROM `#__support_tickets`
				WHERE report!=''
				AND type=0 AND open=1";
		$sql .= " AND (`group`='' OR `group` IS NULL)";
		$sql .= " ORDER BY created ASC";
		$database->setQuery($sql);
		$openTickets = $database->loadObjectList();

		$open = array();
		$this->opened['open']       = 0;
		$this->opened['new']        = 0;
		$this->opened['unassigned'] = 0;
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

			$this->opened['open']++;

			if (!$o->status)
			{
				$this->opened['new']++;
			}
			if (!$o->owner)
			{
				$this->opened['unassigned']++;
			}
		}

		// Closed tickets
		$sql = "SELECT c.ticket, c.created_by, c.created, YEAR(c.created) AS `year`, MONTH(c.created) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(c.created) AS closed
				FROM `#__support_comments` AS c
				LEFT JOIN `#__support_tickets` AS t ON c.ticket=t.id
				WHERE t.report!=''
				AND type=0 AND open=0";
		$sql .= " AND (`group`='' OR `group` IS NULL)";
		$sql .= " ORDER BY c.created ASC";
		$database->setQuery($sql);
		$clsd = $database->loadObjectList();

		$this->opened['closed'] = 0;
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
		$this->closedTickets = $closedTickets;
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
			$this->opened['closed']++;
		}

		// Group data by year and gather some info for each user
		$y = date("Y");
		$y++;
		$this->closedmonths = array();
		$this->openedmonths = array();
		for ($k=$first, $n=$y; $k < $n; $k++)
		{
			$this->closedmonths[$k] = array();
			$this->openedmonths[$k] = array();

			for ($i = 1; $i <= 12; $i++)
			{
				if ($k == $year && $i > $month)
				{
					break;
				}
				else
				{
					$this->closedmonths[$k][$i] = (isset($closed[$k]) && isset($closed[$k][$i])) ? $closed[$k][$i] : 0;
					$this->openedmonths[$k][$i] = (isset($open[$k]) && isset($open[$k][$i]))     ? $open[$k][$i]   : 0;
				}
			}
		}

		// Get the view
		parent::display();
	}
}
