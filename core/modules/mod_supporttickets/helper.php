<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Supporttickets;

use Components\Support\Models\Query;
use Components\Support\Models\Ticket;
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

		include_once Component::path('com_support') . DS . 'models' . DS . 'ticket.php';

		$database = App::get('db');

		$st = new Ticket($database);

		$types = array(
			'common' => array()
			//'mine'   => $sq->getMine()
		);
		$queries = Query::allCommon()->rows();

		if (count($queries) <= 0)
		{
			$one = new stdClass;
			$one->count = 0;
			$one->id    = 0;

			$types['common'][] = $one;

			$two = new stdClass;
			$two->count = 0;
			$two->id    = 0;

			$types['common'][] = $two;

			$three = new stdClass;
			$three->count = 0;
			$three->id    = 0;

			$types['common'][] = $three;
		}
		else
		{
			// Loop through each query in a group
			foreach ($queries as $k => $query)
			{
				if ($query->id)
				{
					// Get a record count
					$query->set('count', Ticket::countWithQuery($query));
				}

				$types['common'][] = $query;
			}
		}

		$this->topened = $types['common'];

		$date = new \Hubzero\Utility\Date();
		$year = $date->toLocal('Y');
		$month = $date->toLocal('m');

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
