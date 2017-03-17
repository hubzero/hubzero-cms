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

namespace Modules\Supporttickets;

use Components\Support\Tables\Query;
use Components\Support\Tables\Ticket;
use Hubzero\Module\Module;
use Component;
use stdClass;
use Request;
use Config;
use App;

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
		if (!App::isAdmin())
		{
			return;
		}

		include_once(Component::path('com_support') . DS . 'tables' . DS . 'query.php');
		include_once(Component::path('com_support') . DS . 'tables' . DS . 'ticket.php');

		$database = App::get('db');

		$st = new Ticket($database);

		$sq = new Query($database);
		$types = array(
			'common' => $sq->getCommon()
			//'mine'   => $sq->getMine()
		);
		// Loop through each grouping
		foreach ($types as $key => $queries)
		{
			if (!is_array($queries) || count($queries) <= 0)
			{
				$one = new stdClass;
				$one->count = 0;
				$one->id    = 0;

				$two = new stdClass;
				$two->count = 0;
				$two->id    = 0;

				$three = new stdClass;
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

		$this->offset = Config::get('offset');

		$year  = Request::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
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
				AND type=0";
		//$sql .= " AND group_id=0";
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
		/*$sql = "SELECT c.ticket, c.created_by, c.created, YEAR(c.created) AS `year`, MONTH(c.created) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(c.created) AS closed
				FROM `#__support_comments` AS c
				LEFT JOIN `#__support_tickets` AS t ON c.ticket=t.id
				WHERE t.report!=''
				AND type=0 AND open=0";*/
		$sql = "SELECT t.id AS ticket, t.closed, YEAR(t.closed) AS `year`, MONTH(t.closed) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(t.closed) AS closed
				FROM `#__support_tickets` AS t
				WHERE t.report!=''
				AND t.type=0 AND t.open=0";
		//$sql .= " AND group_id=0";
		$sql .= " ORDER BY t.created ASC";
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
