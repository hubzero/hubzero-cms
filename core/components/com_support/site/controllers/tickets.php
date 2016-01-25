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

namespace Components\Support\Site\Controllers;

use Components\Support\Helpers\ACL;
use Components\Support\Helpers\Utilities;
use Components\Support\Models\Ticket;
use Components\Support\Models\Comment;
use Components\Support\Models\Tags;
use Components\Support\Tables;
use Hubzero\Component\SiteController;
use Hubzero\Browser\Detector;
use Hubzero\User\Profile;
use Hubzero\Content\Server;
use Hubzero\Utility\Validate;
use Filesystem;
use Exception;
use Request;
use Pathway;
use Config;
use Event;
use Route;
use Lang;
use User;
use Date;
use App;

include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'queryfolder.php');

/**
 * Manage support tickets
 */
class Tickets extends SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->acl = ACL::getACL();

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param   object  $ticket
	 * @return  void
	 */
	protected function _buildPathway($ticket=null)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COM_SUPPORT'),
				'index.php?option=' . $this->_option . '&controller=index'
			);
		}
		if (Pathway::count() == 1  && $this->_task)
		{
			$task = $this->_task;
			if (in_array($this->_task, array('ticket', 'new', 'display', 'save')))
			{
				$task = 'tickets';
			}
			if ($task == 'update')
			{
				$task = 'ticket';
			}
			Pathway::append(
				Lang::txt('COM_SUPPORT_' . strtoupper($task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $task
			);
			if ($this->_task == 'new')
			{
				Pathway::append(
					Lang::txt('COM_SUPPORT_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
		if (is_object($ticket) && $ticket->exists())
		{
			Pathway::append(
				'#' . $ticket->get('id'),
				$ticket->link()
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param   object  $ticket
	 * @return  void
	 */
	protected function _buildTitle($ticket=null)
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($this->_task)
		{
			if ($this->_task == 'new' || $this->_task == 'display')
			{
				$this->_title .= ': ' . Lang::txt('COM_SUPPORT_TICKETS');
			}
			if ($this->_task != 'display')
			{
				if ($this->_task == 'update')
				{
					$this->_title .= ': ' . Lang::txt('COM_SUPPORT_TiCKET');
				}
				else
				{
					$this->_title .= ': ' . Lang::txt('COM_SUPPORT_' . strtoupper($this->_task));
				}
			}
		}
		if (is_object($ticket) && $ticket->exists())
		{
			$this->_title .= ' #' . $ticket->get('id');
		}

		App::get('document')->setTitle($this->_title);
	}

	/**
	 * Displays a list of tickets
	 *
	 * @return	void
	 */
	public function statsTask()
	{
		// Check authorization
		if (User::isGuest())
		{
			$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		if (!$this->acl->check('read','tickets'))
		{
			$this->_return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets');
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		$type = Request::getWord('type', 'submitted');
		$this->view->type = ($type == 'automatic') ? 1 : 0;

		$this->view->group = preg_replace('/[^0-9a-zA-Z_\-]/', '', Request::getVar('group', '_none_'));

		// Set up some dates
		$this->offset = Config::get('offset');

		$year  = Request::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));

		$this->view->year = $year;
		$this->view->opened = array();
		$this->view->closed = array();

		$st = new Tables\Ticket($this->database);

		$sql = "SELECT DISTINCT(s.`group`), g.description
				FROM `#__support_tickets` AS s
				LEFT JOIN `#__xgroups` AS g ON g.cn=s.`group`
				WHERE s.`group` !='' AND s.`group` IS NOT NULL
				AND s.type=" . $this->view->type . "
				ORDER BY g.description ASC";
		$this->database->setQuery($sql);
		$this->view->groups = $this->database->loadObjectList();

		// Users
		$this->view->users = null;

		if ($this->view->group == '_none_')
		{
			$query = "SELECT DISTINCT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.id"
				. "\n WHERE a.block = '0' AND s.type=" . $this->view->type . " AND (s.group IS NULL OR s.group='')"
				. "\n ORDER BY a.name";
		}
		else if ($this->view->group)
		{
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a, #__xgroups AS g, #__xgroups_members AS gm"
				. "\n WHERE g.cn='".$this->view->group."' AND g.gidNumber=gm.gidNumber AND gm.uidNumber=a.id"
				. "\n ORDER BY a.name";
		}
		else
		{
			$query = "SELECT DISTINCT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.id"
				. "\n WHERE a.block = '0' AND s.type=" . $this->view->type . ""
				. "\n ORDER BY a.name";
		}

		$this->database->setQuery($query);
		$usrs = $this->database->loadObjectList();

		$users = array();
		if ($usrs)
		{
			foreach ($usrs as $j => $user)
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
				$users[$user->id] = $user;
			}
		}

		// Get avgerage lifetime
		//$this->view->lifetime = $st->getAverageLifeOfTicket($this->view->type, $year, $this->view->group);

		// First ticket
		$sql = "SELECT YEAR(created)
				FROM `#__support_tickets`
				WHERE report!=''
				AND type='{$this->view->type}' ORDER BY created ASC LIMIT 1";
		$this->database->setQuery($sql);
		$first = intval($this->database->loadResult());

		$startyear  = $first;
		$startmonth = 1;

		$this->view->start = Request::getVar('start', $first . '-01');
		if ($this->view->start != $first . '-01')
		{
			if (!preg_match("/^([0-9]{4})-([0-9]{2})$/", $this->view->start))
			{
				$this->view->start = $first . '-01';
			}

			if (preg_match("/([0-9]{4})-([0-9]{2})/", $this->view->start, $regs))
			{
				$startmonth = date("m", mktime(0, 0, 0, $regs[2], 1, $regs[1]));
				$startyear = $first = date("Y", mktime(0, 0, 0, $regs[2], 1, $regs[1]));
				//$end = $year . '-' . $month;
			}
		}

		$this->view->end   = Request::getVar('end', '');

		$endmonth = $month;
		$endyear = date("Y");
		$endyear++;

		$end = '';
		if ($this->view->end)
		{
			if (!preg_match("/^([0-9]{4})-([0-9]{2})$/", $this->view->end))
			{
				$this->view->end = '';
			}

			// We need to get the NEXT month. This is so that for a time range
			// of 2013-01 to 2013-12 will display data for all of 2013-12.
			// So, the actual time range is 2013-01-01 00:00:00 to 2014-01-01 00:00:00
			if (preg_match("/([0-9]{4})-([0-9]{2})/", $this->view->end, $regs))
			{
				$endmonth = intval($regs[2]);
				$endyear  = intval($regs[1]);
				$endyear++;

				$month = date("m", mktime(0, 0, 0, ($endmonth+1), 1, $regs[1]));
				$year  = date("Y", mktime(0, 0, 0, ($endmonth+1), 1, $regs[1]));
				$end   = $year . '-' . $month;
			}
		}
		else
		{
			$this->view->end = $year . '-' . $month;
		}

		// Opened tickets
		$sql = "SELECT id, created, YEAR(created) AS `year`, MONTH(created) AS `month`, open, status, owner
				FROM `#__support_tickets`
				WHERE report!=''
				AND type=" . $this->view->type; // . " AND open=1";
		if ($this->view->group == '_none_')
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else if ($this->view->group)
		{
			$sql .= " AND `group`='{$this->view->group}'";
		}
		if ($this->view->start && $end)
		{
			$sql .= " AND created>='" . $this->view->start . "-01 00:00:00' AND created<'" . $end . "-01 00:00:00'";
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

			if ($o->open)
			{
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
		}

		// Closed tickets
		$sql = "SELECT t.id AS ticket, t.owner AS created_by, t.closed AS created, YEAR(t.closed) AS `year`, MONTH(t.closed) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(t.closed) AS closed
				FROM `#__support_tickets` AS t
				WHERE t.report!=''
				AND t.type=" . $this->view->type . " AND t.open=0";
		if ($this->view->group == '_none_')
		{
			$sql .= " AND (t.`group`='' OR t.`group` IS NULL)";
		}
		else if ($this->view->group)
		{
			$sql .= " AND t.`group`='{$this->view->group}'";
		}
		if ($this->view->start && $end)
		{
			$sql .= " AND t.closed>='" . $this->view->start . "-01 00:00:00' AND t.closed<'" . $end . "-01 00:00:00'";
		}
		$sql .= " ORDER BY t.closed ASC";

		$this->database->setQuery($sql);
		$clsd = $this->database->loadObjectList();

		$this->view->opened['closed'] = 0;
		// First we need to loop through all the entries and remove some potential duplicates
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
		// Loop through info and divide into years/months
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

		$this->view->closedmonths = array();
		$this->view->openedmonths = array();

		for ($k=$startyear, $n=$endyear; $k < $n; $k++)
		{
			$this->view->closedmonths[$k] = array();
			$this->view->openedmonths[$k] = array();
			if ($k == $startyear && intval($startmonth) > 1)
			{
				$i = intval($startmonth) - 1;
			}
			else
			{
				$i = 1;
			}

			for ($i; $i <= 12; $i++)
			{
				if ($k == $year && $i > $endmonth)
				{
					break;
				}
				else
				{
					$this->view->closedmonths[$k][$i] = (isset($closed[$k]) && isset($closed[$k][$i])) ? $closed[$k][$i] : 0;
					$this->view->openedmonths[$k][$i] = (isset($open[$k]) && isset($open[$k][$i]))     ? $open[$k][$i]   : 0;
				}

				foreach ($users as $j => $user)
				{
					if (!isset($user->closed[$k]))
					{
						$user->closed[$k] = array();
					}

					/*if ($i <= "9"&preg_match("#(^[1-9]{1})#",$i))
					{
						$month = "0$i";
					}*/
					if ($k == $year && $i > $month)
					{
						$user->closed[$k][$i] = 'null';
					}
					else
					{
						$user->closed[$k][$i] = 0;
					}

					$users[$j] = $user;
				}
			}
		}

		foreach ($closedTickets as $c)
		{
			if (isset($users[$c->created_by]))
			{
				$y = intval($c->year);
				$m = intval($c->month);

				if (!$y && !$m)
				{
					continue;
				}

				if (!isset($users[$c->created_by]->closed[$y]))
				{
					$users[$c->created_by]->closed[$y] = array();
				}
				if (!isset($users[$c->created_by]->closed[$y][$m]))
				{
					$users[$c->created_by]->closed[$y][$m] = 0;
				}
				$users[$c->created_by]->closed[$y][$m]++;
				$users[$c->created_by]->total++;
				$users[$c->created_by]->tickets[] = $c;
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
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Displays a list of support tickets
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		if (User::isGuest())
		{
			$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		$this->view->database = $this->database;

		// Create a Ticket object
		$obj = new Tables\Ticket($this->database);

		$this->view->total = 0;
		$this->view->rows = array();

		$this->view->filters = $this->_getFilters();
		// Paging
		$this->view->filters['limit'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// Query to filter by
		$this->view->filters['show'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.show',
			'show',
			0,
			'int'
		);
		// Search
		$this->view->filters['search']       = urldecode(Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		// Get query list
		$sf = new Tables\QueryFolder($this->database);
		$sq = new Tables\Query($this->database);

		if (!$this->acl->check('read', 'tickets'))
		{
			$this->view->folders = $sf->find('list', array(
				'user_id'  => 0,
				'sort'     => 'ordering',
				'sort_Dir' => 'asc',
				'iscore'   => 2
			));

			$queries = $sq->find('list', array(
				'user_id'  => 0,
				'sort'     => 'ordering',
				'sort_Dir' => 'asc',
				'iscore'   => 4
			));
		}
		else
		{
			$this->view->folders = $sf->find('list', array(
				'user_id'  => User::get('id'),
				'sort'     => 'ordering',
				'sort_Dir' => 'asc'
			));

			// Does the user have any folders?
			if (!count($this->view->folders))
			{
				// Get all the default folders
				$this->view->folders = $sf->cloneCore(User::get('id'));
			}

			$queries = $sq->find('list', array(
				'user_id'  => User::get('id'),
				'sort'     => 'ordering',
				'sort_Dir' => 'asc'
			));
		}

		$this->view->filters['sort'] = 'id';
		$this->view->filters['sortdir'] = 'DESC';

		foreach ($queries as $query)
		{
			$filters = $this->view->filters;
			if ($query->id != $this->view->filters['show'])
			{
				$filters['search'] = '';
			}

			$query->query = $sq->getQuery($query->conditions);

			// Get a record count
			$query->count = $obj->getCount($query->query, $filters);

			foreach ($this->view->folders as $k => $v)
			{
				if (!isset($this->view->folders[$k]->queries))
				{
					$this->view->folders[$k]->queries = array();
				}
				if ($query->folder_id == $v->id)
				{
					$this->view->folders[$k]->queries[] = $query;
				}
			}

			if ($query->id == $this->view->filters['show'])
			{
				// Search
				$this->view->filters['search']       = urldecode(Request::getState(
					$this->_option . '.' . $this->_controller . '.search',
					'search',
					''
				));
				// Set the total for the pagination
				$this->view->total = ($this->view->filters['search']) ? $obj->getCount($query->query, $this->view->filters) : $query->count;

				// Incoming sort
				$this->view->filters['sort']         = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sort',
					'sort',
					$query->sort
				));

				$this->view->filters['sortdir']     = trim(Request::getState(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'sortdir',
					$query->sort_dir
				));
				// Get the records
				$this->view->rows  = $obj->getRecords($query->query, $this->view->filters);
			}
		}

		if (!$this->view->filters['show'])
		{
			// Jump back to the beginning of the folders list
			// and try to find the first query available
			// to make it the current "active" query
			reset($this->view->folders);
			foreach ($this->view->folders as $folder)
			{
				if (!empty($folder->queries))
				{
					$query = $folder->queries[0];
					$this->view->filters['show'] = $query->id;
					break;
				}
				else
				{	// for no custom queries.
					$query = new Tables\Query($this->database);
					$query->count = 0;
				}
			}
			//$folder = reset($this->view->folders);
			//$query = $folder->queries[0];
			// Search
			$this->view->filters['search'] = urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			));
			// Set the total for the pagination
			$this->view->total = ($this->view->filters['search']) ? $obj->getCount($query->query, $this->view->filters) : $query->count;

			// Incoming sort
			$this->view->filters['sort']   = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				$query->sort
			));
			$this->view->filters['sortdir'] = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				$query->sort_dir
			));
			// Get the records
			$this->view->rows = $obj->getRecords($query->query, $this->view->filters);
		}

		$watching = new Tables\Watching($this->database);
		$this->view->watch = array(
			'open' => $watching->count(array(
				'user_id' => User::get('id'),
				'open'    => 1
			)),
			'closed' => $watching->count(array(
				'user_id' => User::get('id'),
				'open'    => 0
			))
		);
		if ($this->view->filters['show'] < 0)
		{
			$records = $watching->find(array(
				'user_id' => User::get('id'),
				'open'    => ($this->view->filters['show'] == -1 ? 1 : 0)
			));
			if (count($records))
			{
				$ids = array();
				foreach ($records as $record)
				{
					$ids[] = $record->ticket_id;
				}
				$this->view->rows = $obj->getRecords("(f.id IN ('" . implode("','", $ids) . "'))", $this->view->filters);
			}
			else
			{
				$this->view->rows = array();
			}
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		$this->view->acl = $this->acl;

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Displays a form for creating a new support ticket
	 *
	 * @return  void
	 */
	public function newTask($row = null)
	{
		if (!($row instanceof Ticket))
		{
			$row = new Ticket();
			$row->set('open', 1)
				->set('status', 0)
				->set('ip', Request::ip())
				->set('uas', Request::getVar('HTTP_USER_AGENT', '', 'server'))
				->set('referrer', base64_encode(Request::getVar('HTTP_REFERER', NULL, 'server')))
				->set('cookies', (Request::getVar('sessioncookie', '', 'cookie') ? 1 : 0))
				->set('instances', 1)
				->set('section', 1)
				->set('tool', Request::getVar('tool', ''))
				->set('verified', 0);

			if (!User::isGuest())
			{
				$row->set('name', User::get('name'));
				$row->set('login', User::get('username'));
				$row->set('email', User::get('email'));
			}
		}

		$browser = new Detector();

		$row->set('os', $browser->platform())
			->set('osver', $browser->platformVersion())
			->set('browser', $browser->name())
			->set('browserver', $browser->version());

		if (!User::isGuest())
		{
			$profile = new Profile();
			$profile->load(User::get('id'));
			$emailConfirmed = $profile->get('emailConfirmed');
			if (($emailConfirmed == 1) || ($emailConfirmed == 3))
			{
				$row->set('verified', 1);
			}
		}

		// Output HTML
		$lists = array();

		if ($row->get('verified') && $this->acl->check('update', 'tickets') > 0)
		{
			if (trim($this->config->get('group')))
			{
				$lists['owner'] = $this->_userSelectGroup(
					'problem[owner]',
					'',
					1,
					'',
					trim($this->config->get('group'))
				);
			}
			else
			{
				$lists['owner'] = $this->_userSelect(
					'problem[owner]',
					'',
					1
				);
			}

			$lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

			$sr = new Tables\Resolution($this->database);
			$lists['resolutions'] = $sr->getResolutions();

			$sc = new Tables\Category($this->database);
			$lists['categories'] = $sc->find('list');
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->set('acl', $this->acl)
			->set('title', $this->_title)
			->set('file_types', $this->config->get('file_ext'))
			->set('lists', $lists)
			->set('row', $row)
			->set('captchas', Event::trigger('support.onGetComponentCaptcha'))
			->setLayout('new')
			->display();
	}

	/**
	 * Saves a trouble report as a ticket
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$live_site = rtrim(Request::base(), '/');

		// Trigger any events that need to be called before session stop
		Event::trigger('support.onPreTicketSubmission', array());

		// Incoming
		$no_html  = Request::getInt('no_html', 0);
		$verified = Request::getInt('verified', 0);
		if (!isset($_POST['reporter']) || !isset($_POST['problem']))
		{
			// This really, REALLY shouldn't happen.
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_MISSING_DATA'), 400);
		}
		$reporter = Request::getVar('reporter', array(), 'post', 'none', 2);
		$problem  = Request::getVar('problem', array(), 'post', 'none', 2);
		//$reporter = array_map('trim', $_POST['reporter']);
		//$problem  = array_map('trim', $_POST['problem']);

		// Normally calling Request::getVar calls _cleanVar, but b/c of the way this page processes the posts
		// (with array square brackets in the html names) against the $_POST collection, we explicitly
		// call the clean_var function on these arrays after fetching them
		//$reporter = array_map(array('Request', '_cleanVar'), $reporter);
		//$problem  = array_map(array('Request', '_cleanVar'), $problem);

		// [!] zooley - Who added this? Why?
		// Reporter login can only be for authenticated users -- ignore any form submitted login names
		//$reporterLogin = $this->_getUser();
		//$reporter['login'] = $reporterLogin['login'];

		// Probably redundant after the change to call Request::_cleanVar change above, It is a bit hard to
		// tell if the Joomla  _cleanvar function does enough to allow us to remove the purifyText call
		$reporter = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $reporter);
		//$problem  = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $problem);

		$reporter['name']  = trim($reporter['name']);
		$reporter['email'] = trim($reporter['email']);
		$problem['long']   = trim($problem['long']);

		// Make sure email address is valid
		$validemail = Validate::email($reporter['email']);

		// Set page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Trigger any events that need to be called
		$customValidation = true;
		$result = Event::trigger('support.onValidateTicketSubmission', array($reporter, $problem));
		$customValidation = (is_array($result) && !empty($result)) ? $result[0] : $customValidation;

		// Check for some required fields
		if (!$reporter['name']
		 || !$reporter['email']
		 || !$validemail
		 || !$problem['long']
		 || !$customValidation)
		{
			Request::setVar('task', 'new');
			// Output form with error messages
			$this->view->setError(2);
			return $this->newTask();
		}

		// Get the user's IP
		$ip = Request::ip();
		$hostname = gethostbyaddr(Request::getVar('REMOTE_ADDR','','server'));

		if (!$verified)
		{
			// Check CAPTCHA
			$validcaptchas = Event::trigger('support.onValidateCaptcha');
			if (count($validcaptchas) > 0)
			{
				foreach ($validcaptchas as $validcaptcha)
				{
					if (!$validcaptcha)
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_INVALID_CAPTCHA'));
					}
				}
			}
		}
		// Are they verified?
		if (!$verified)
		{
			// Quick spam filter
			$spam = $this->_detectSpam($problem['long'], $ip);
			if ($spam)
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_FLAGGED_AS_SPAM'));
				return;
			}
			// Quick bot check
			$botcheck = Request::getVar('botcheck', '');
			if ($botcheck)
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_INVALID_BOTCHECK'));
				return;
			}
		}

		// Check for errors
		// If any found, push back into the submission form view
		if ($this->getError())
		{
			if ($no_html)
			{
				// Output error messages (AJAX)
				$this->view->setLayout('error');
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}
				$this->view->display();
				return;
			}
			else
			{
				Request::setVar('task', 'new');
				$this->view->setError($this->getError());
				return $this->newTask();
			}
		}

		// Cut suggestion at 70 characters
		if (!$problem['short'] && $problem['long'])
		{
			$problem['short'] = substr($problem['long'], 0, 70);
			if (strlen($problem['short']) >= 70)
			{
				$problem['short'] .= '...';
			}
		}

		$group = Request::getVar('group', '');

		// Initiate class and bind data to database fields
		$row = new Ticket();
		$row->set('open', 1);
		$row->set('status', 0);
		$row->set('created', Date::toSql());
		$row->set('login', $reporter['login']);
		$row->set('severity', (isset($problem['severity']) ? $problem['severity'] : 'normal'));
		$row->set('owner', (isset($problem['owner']) ? $problem['owner'] : null));
		$row->set('category', (isset($problem['category']) ? $problem['category'] : ''));
		$row->set('summary', $problem['short']);
		$row->set('report', $problem['long']);
		$row->set('resolved', (isset($problem['resolved']) ? $problem['resolved'] : null));
		$row->set('email', $reporter['email']);
		$row->set('name', $reporter['name']);
		$row->set('os', $problem['os'] . ' ' . $problem['osver']);
		$row->set('browser', $problem['browser'] . ' ' . $problem['browserver']);
		$row->set('ip', $ip);
		$row->set('hostname', $hostname);
		$row->set('uas', Request::getVar('HTTP_USER_AGENT', '', 'server'));
		$row->set('referrer', base64_decode($problem['referer']));
		$row->set('cookies', (Request::getVar('sessioncookie', '', 'cookie') ? 1 : 0));
		$row->set('instances', 1);
		$row->set('section', 1);
		$row->set('group', $group);

		// check if previous ticket submitted is the same as this one.
		$ticket = new Tables\Ticket($this->database);
		$filters = array('status' => 'new', 'sort' => 'id' ,'sortdir' => 'DESC', 'limit' => '1', 'start' => 0);
		$prevSubmission = $ticket->getTickets($filters , false);

		// for the first ticket ever
		if (isset($prevSubmission[0]) && $prevSubmission[0]->report == $row->get('report') && (time() - strtotime($prevSubmission[0]->created) <= 15))
		{
			$this->setError(Lang::txt('COM_SUPPORT_TICKET_DUPLICATE_DETECTION'));
			return $this->newTask($row);
		}

		// Save the data
		if (!$row->store())
		{
			$this->setError($row->getError());
		}

		$attachment = $this->uploadTask($row->get('id'));

		// Save tags
		$row->set('tags', Request::getVar('tags', '', 'post'));
		$row->tag($row->get('tags'), User::get('id'), 1);

		// Get any set emails that should be notified of ticket submission
		$defs = explode(',', $this->config->get('emails', '{config.mailfrom}'));

		if ($defs)
		{
			$message = new \Hubzero\Mail\Message();
			$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_NEW_TICKET', $row->get('id')));
			$message->addFrom(
				Config::get('mailfrom'),
				Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
			);

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'ticket_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->ticket     = $row;
			$eview->config     = $this->config;
			$eview->delimiter  = '';

			$plain = $eview->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			$message->addPart($plain, 'text/plain');

			// HTML email
			$eview->setLayout('ticket_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			if (!$this->config->get('email_terse'))
			{
				foreach ($row->attachments() as $attachment)
				{
					if ($attachment->size() < 2097152)
					{
						if ($attachment->isImage())
						{
							$file = basename($attachment->link('filepath'));
							$html = preg_replace('/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i', '<img src="' . $message->getEmbed($attachment->link('filepath')) . '" alt="" />', $html);
						}
						else
						{
							$message->addAttachment($attachment->link('filepath'));
						}
					}
				}
			}

			$message->addPart($html, 'text/html');

			// Loop through the addresses
			foreach ($defs as $def)
			{
				$def = trim($def);

				// Check if the address should come from Joomla config
				if ($def == '{config.mailfrom}')
				{
					$def = Config::get('mailfrom');
				}
				// Check for a valid address
				if (Validate::email($def))
				{
					// Send e-mail
					$message->setTo(array($def));
					$message->send();
				}
			}
		}

		if (!User::isGuest() && $this->acl->check('update', 'tickets') > 0)
		{
			// Only do the following if a comment was posted
			// otherwise, we're only recording a changelog
			$old = new Ticket();
			$old->set('open', 1);
			$old->set('owner', 0);
			$old->set('status', 0);
			$old->set('tags', '');
			$old->set('severity', 'normal');

			$rowc = new Comment();
			$rowc->set('ticket', $row->get('id'));
			$rowc->set('created', Date::toSql());
			$rowc->set('created_by', User::get('id'));
			$rowc->set('access', 1);
			$rowc->set('comment', Lang::txt('COM_SUPPORT_TICKET_SUBMITTED'));

			// Compare fields to find out what has changed for this ticket and build a changelog
			$rowc->changelog()->diff($old, $row);

			$rowc->changelog()->cced(Request::getVar('cc', ''));

			// Were there any changes, CCs, or comments to record?
			if (count($rowc->changelog()->get('changes')) > 0 || count($rowc->changelog()->get('cc')) > 0)
			{
				// Save the data
				if (!$rowc->store())
				{
					throw new Exception($rowc->getError(), 500);
				}

				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}
				elseif ($row->get('group'))
				{
					$group = \Hubzero\User\Group::getInstance($row->get('group'));

					if ($group)
					{
						foreach ($group->get('managers') as $manager)
						{
							$manager = User::getInstance($manager);

							if (!$manager || !$manager->get('id'))
							{
								continue;
							}

							$rowc->addTo(array(
								'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_GROUPMANAGER'),
								'name'  => $manager->get('name'),
								'email' => $manager->get('email'),
								'id'    => $manager->get('id')
							));
						}
					}
				}

				// Add any CCs to the e-mail list
				foreach ($rowc->changelog()->get('cc') as $cc)
				{
					$rowc->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
				}

				// Check if the notify list has eny entries
				if (count($rowc->to()))
				{
					$allowEmailResponses = $this->config->get('email_processing');
					if ($this->config->get('email_terse'))
					{
						$allowEmailResponses = false;
					}
					if ($allowEmailResponses)
					{
						try
						{
							$encryptor = new \Hubzero\Mail\Token();
						}
						catch (Exception $e)
						{
							$allowEmailResponses = false;
						}
					}

					$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

					$from = array(
						'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
						'email'     => Config::get('mailfrom'),
						'multipart' => md5(date('U'))
					);

					$message = array();

					// Plain text email
					$eview = new \Hubzero\Mail\View(array(
						'name'   => 'emails',
						'layout' => 'comment_plain'
					));
					$eview->option     = $this->_option;
					$eview->controller = $this->_controller;
					$eview->comment    = $rowc;
					$eview->ticket     = $row;
					$eview->config     = $this->config;
					$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

					$message['plaintext'] = $eview->loadTemplate(false);
					$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

					// HTML email
					$eview->setLayout('comment_html');

					$message['multipart'] = $eview->loadTemplate();
					$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

					// Send e-mail to admin?
					foreach ($rowc->to('ids') as $to)
					{
						if ($allowEmailResponses)
						{
							// The reply-to address contains the token
							$token = $encryptor->buildEmailToken(1, 1, $to['id'], $row->get('id'));
							$from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
						}

						// Get the user's email address
						if (!Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
						{
							$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
						}
						$rowc->changelog()->notified(
							$to['role'],
							$to['name'],
							$to['email']
						);
					}

					foreach ($rowc->to('emails') as $to)
					{
						if ($allowEmailResponses)
						{
							$token = $encryptor->buildEmailToken(1, 1, -9999, $row->get('id'));

							$email = array(
								$to['email'],
								'htc-' . $token . strstr(Config::get('mailfrom'), '@')
							);

							// In this case each item in email in an array, 1- To, 2:reply to address
							Utilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
						}
						else
						{
							// email is just a plain 'ol string
							Utilities::sendEmail($to['email'], $subject, $message, $from);
						}

						$rowc->changelog()->notified(
							$to['role'],
							$to['name'],
							$to['email']
						);
					}
				}

				// Were there any changes?
				if (count($rowc->changelog()->get('notifications')) > 0
				 || count($rowc->changelog()->get('cc')) > 0
				 || count($rowc->changelog()->get('changes')) > 0)
				{
					// Save the data
					if (!$rowc->store())
					{
						$this->setError($rowc->getError());
					}
				}
			}
		}

		// Trigger any events that need to be called before session stop
		Event::trigger('support.onTicketSubmission', array($row));

		// Output Thank You message
		$this->view->ticket  = $row->get('id');
		$this->view->no_html = $no_html;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Attempts to detect if some text is spam
	 * Checks for blacklisted IPs, bad words, and overuse of links
	 *
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      unknown $ip Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _detectSpam($text, $ip)
	{
		// Spammer IPs (banned)
		$ips = $this->config->get('blacklist');
		if ($ips)
		{
			$bl = explode(',', $ips);
			array_map('trim', $bl);
		}
		else
		{
			$bl = array();
		}

		// Bad words
		$words = $this->config->get('badwords');
		if ($words)
		{
			$badwords = explode(',', $words);
			array_map('trim', $badwords);
		}
		else
		{
			$badwords = array();
		}

		// Build an array of patterns to check againts
		$patterns = array('/\[url=(.*?)\](.*?)\[\/url\]/s', '/\[url=(.*?)\[\/url\]/s');
		foreach ($badwords as $badword)
		{
			if (!empty($badword))
			{
				$patterns[] = '/(.*?)'.trim($badword).'(.*?)/s';
			}
		}

		// Set the splam flag
		$spam = false;

		// Check the text against bad words
		foreach ($patterns as $pattern)
		{
			preg_match_all($pattern, $text, $matches);
			if (count($matches[0]) >=1)
			{
				$spam = true;
			}
		}

		// Check the number of links in the text
		// Very unusual to have 5 or more - usually only spammers
		if (!$spam)
		{
			$num = substr_count($text, 'http://');
			if ($num >= 5) // too many links
			{
				$spam = true;
			}
		}

		// Check the user's IP against the blacklist
		if (in_array($ip, $bl))
		{
			$spam = true;
		}

		return $spam;
	}

	/**
	 * Display a ticket and associated comments
	 *
	 * @param   mixed  $comment
	 * @return  void
	 */
	public function ticketTask($comment = null)
	{
		// Get the ticket ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->controller . '&task=tickets'),
				Lang::txt('COM_SUPPORT_ERROR_MISSING_TICKET_ID'),
				'error'
			);
			return;
		}

		// Initiate database class and load info
		$this->view->row = Ticket::getInstance($id);
		if (!$this->view->row->exists())
		{
			App::abort(404, Lang::txt('COM_SUPPORT_ERROR_TICKET_NOT_FOUND'));
			return;
		}

		// Check authorization
		if (User::isGuest())
		{
			$return = base64_encode(Route::url($this->view->row->link(), false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Ensure the user is authorized to view this ticket
		if (!$this->view->row->access('read', 'tickets'))
		{
			App::abort(403, Lang::txt('COM_SUPPORT_ERROR_NOT_AUTH'));
			return;
		}

		$this->view->filters = array(
			// Paging
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Query to filter by
			'show' => Request::getState(
				$this->_option . '.' . $this->_controller . '.show',
				'show',
				0,
				'int'
			),
			// Search
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			))
		);

		if ($watch = Request::getWord('watch', ''))
		{
			// Already watching
			if ($this->view->row->isWatching(User::getRoot()))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$this->view->row->stopWatching(User::getRoot());
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					$this->view->row->watch(User::getRoot());
					if (!$this->view->row->isWatching(User::getRoot(), true))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		$this->view->lists = array();

		// Get resolutions
		$sr = new Tables\Resolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		$sc = new Tables\Category($this->database);
		$this->view->lists['categories'] = $sc->find('list');

		// Get messages
		$sm = new Tables\Message($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get severities
		$this->view->lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		// Populate the list of assignees based on if the ticket belongs to a group or not
		if (trim($this->view->row->get('group')))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]',
				$this->view->row->get('owner'),
				1,
				'',
				trim($this->view->row->get('group'))
			);
		}
		elseif (trim($this->config->get('group')))
		{
			$this->view->lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]',
				$this->view->row->get('owner'),
				1,
				'',
				trim($this->config->get('group'))
			);
		}
		else
		{
			$this->view->lists['owner'] = $this->_userSelect(
				'ticket[owner]',
				$this->view->row->get('owner'),
				1
			);
		}

		// Set the pathway
		$this->_buildPathway($this->view->row);

		// Set the page title
		$this->_buildTitle($this->view->row);

		$this->view->title = $this->_title;
		$this->view->database = $this->database;

		if (\Notify::any('support'))
		{
			foreach (\Notify::messages('support') as $error)
			{
				if ($error['type'] == 'error')
				{
					$this->view->setError($error['message']);
				}
			}
		}

		if (!$comment)
		{
			$comment = new Comment();
		}
		$this->view->comment = $comment;

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->setLayout('ticket')->display();
	}

	/**
	 * Updates a ticket with any changes and adds a new comment
	 *
	 * @return     void
	 */
	public function updateTask()
	{
		// Make sure we are still logged in
		if (User::isGuest())
		{
			$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0, 'post');
		if (!$id)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_MISSING_TICKET_ID'), 500);
		}

		$comment  = Request::getVar('comment', '', 'post', 'none', 2);
		$incoming = Request::getVar('ticket', array(), 'post');
		$incoming = array_map('trim', $incoming);

		// Load the old ticket so we can compare for the changelog
		$old = new Ticket($id);
		$old->set('tags', $old->tags('string'));

		// Initiate class and bind posted items to database fields
		$row = new Ticket($id);
		if (!$row->bind($incoming))
		{
			throw new Exception($row->getError(), 500);
		}

		$rowc = new Comment();
		$rowc->set('ticket', $id);

		// Check if changes were made inbetween the time the comment was started and posted
		$started = Request::getVar('started', Date::toSql(), 'post');
		$lastcomment = $row->comments('list', array(
			'sort'     => 'created',
			'sort_Dir' => 'DESC',
			'limit'    => 1,
			'start'    => 0,
			'ticket'   => $id
		))->first();
		if ($lastcomment && $lastcomment->created() > $started)
		{
			$rowc->set('comment', $comment);
			$this->setError(Lang::txt('Changes were made to this ticket in the time since you began commenting/making changes. Please review your changes before submitting.'));
			return $this->ticketTask($rowc);
		}

		// Update ticket status if necessary
		if ($id && isset($incoming['status']) && $incoming['status'] == 0)
		{
			$row->set('open', 0);
			$row->set('resolved', Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'));
		}

		$row->set('open', $row->status('open'));

		// Check content
		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}

		// If an existing ticket AND closed AND previously open
		if ($id && !$row->get('open') && $row->get('open') != $old->get('open'))
		{
			// Record the closing time
			$row->set('closed', Date::toSql());
		}

		// Incoming comment
		if ($comment)
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($row->isWaiting() && User::get('username') == $row->get('login'))
			{
				$row->open();
			}
		}

		// Store new content
		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}

		// Save the tags
		$row->tag(Request::getVar('tags', '', 'post'), User::get('id'), 1);
		$row->set('tags', $row->tags('string'));

		// Create a new support comment object and populate it
		$access = Request::getInt('access', 0);

		$rowc->set('ticket', $id);
		$rowc->set('comment', nl2br($comment));
		$rowc->set('created', Date::toSql());
		$rowc->set('created_by', User::get('id'));
		$rowc->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$rowc->changelog()->diff($old, $row);

		$rowc->changelog()->cced(Request::getVar('cc', ''));

		// Save the data
		if (!$rowc->store())
		{
			throw new Exception($rowc->getError(), 500);
		}

		Event::trigger('support.onTicketUpdate', array($row, $rowc));

		$attach = new Tables\Attachment($this->database);
		if ($tmp = Request::getInt('tmp_dir'))
		{
			$attach->updateCommentId($tmp, $rowc->get('id'));
		}

		$attachment = $this->uploadTask($row->get('id'), $rowc->get('id'));

		// Only do the following if a comment was posted
		// otherwise, we're only recording a changelog
		if ($rowc->get('comment')
		 || $row->get('owner') != $old->get('owner')
		 || $row->get('group') != $old->get('group')
		 || $rowc->attachments()->total() > 0)
		{
			// Send e-mail to ticket submitter?
			if (Request::getInt('email_submitter', 0) == 1)
			{
				// Is the comment private? If so, we do NOT send e-mail to the
				// submitter regardless of the above setting
				if (!$rowc->isPrivate())
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $row->submitter('name'),
						'email' => $row->submitter('email'),
						'id'    => $row->submitter('id')
					));
				}
			}

			// Send e-mail to ticket owner?
			if (Request::getInt('email_owner', 0) == 1)
			{
				if ($old->get('owner') && $row->get('owner') != $old->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_PRIOR_OWNER'),
						'name'  => $old->owner('name'),
						'email' => $old->owner('email'),
						'id'    => $old->owner('id')
					));
				}
				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}
				elseif ($row->get('group'))
				{
					$group = \Hubzero\User\Group::getInstance($row->get('group'));

					if ($group)
					{
						foreach ($group->get('managers') as $manager)
						{
							$manager = User::getInstance($manager);

							if (!$manager || !$manager->get('id'))
							{
								continue;
							}

							$rowc->addTo(array(
								'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_GROUPMANAGER'),
								'name'  => $manager->get('name'),
								'email' => $manager->get('email'),
								'id'    => $manager->get('id')
							));
						}
					}
				}
			}

			// Add any CCs to the e-mail list
			foreach ($rowc->changelog()->get('cc') as $cc)
			{
				$rowc->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}

			// Message people watching this ticket,
			// but ONLY if the comment was NOT marked private
			foreach ($row->watchers() as $watcher)
			{
				$this->acl->setUser($watcher->user_id);
				if (!$rowc->isPrivate() || ($rowc->isPrivate() && $this->acl->check('read', 'private_comments')))
				{
					$rowc->addTo($watcher->user_id, 'watcher');
				}
			}
			$this->acl->setUser(User::get('id'));

			if (count($rowc->to()))
			{
				$allowEmailResponses = $this->config->get('email_processing');
				if ($this->config->get('email_terse'))
				{
					$allowEmailResponses = false;
				}
				if ($allowEmailResponses)
				{
					try
					{
						$encryptor = new \Hubzero\Mail\Token();
					}
					catch (Exception $e)
					{
						$allowEmailResponses = false;
					}
				}

				// Build e-mail components
				$subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

				$from = array(
					'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
					'email'     => Config::get('mailfrom'),
					'multipart' => md5(date('U'))  // Html email
				);

				$message = array();

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => 'comment_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->comment    = $rowc;
				$eview->ticket     = $row;
				$eview->config     = $this->config;
				$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

				$message['plaintext'] = $eview->loadTemplate(false);
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('comment_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				$message['attachments'] = array();
				if (!$this->config->get('email_terse'))
				{
					foreach ($rowc->attachments() as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							$message['attachments'][] = $attachment->link('filepath');
						}
					}
				}

				foreach ($rowc->to('ids') as $to)
				{
					if ($allowEmailResponses)
					{
						// The reply-to address contains the token
						$token = $encryptor->buildEmailToken(1, 1, $to['id'], $id);
						$from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
					}

					// Get the user's email address
					if (!Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
					}

					// Watching should be anonymous
					if ($to['role'] == 'watcher')
					{
						continue;
					}
					$rowc->changelog()->notified(
						$to['role'],
						$to['name'],
						$to['email']
					);
				}

				foreach ($rowc->to('emails') as $to)
				{
					if ($allowEmailResponses)
					{
						$token = $encryptor->buildEmailToken(1, 1, -9999, $id);

						$email = array(
							$to['email'],
							'htc-' . $token . strstr(Config::get('mailfrom'), '@')
						);

						// In this case each item in email in an array, 1- To, 2:reply to address
						Utilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
					}
					else
					{
						// email is just a plain 'ol string
						Utilities::sendEmail($to['email'], $subject, $message, $from);
					}

					// Watching should be anonymous
					if ($to['role'] == 'watcher')
					{
						continue;
					}
					$rowc->changelog()->notified(
						$to['role'],
						$to['name'],
						$to['email']
					);
				}
			}
			else
			{
				// Force entry to private if no comment or attachment was made
				if (!$rowc->get('comment') && $rowc->attachments()->total() <= 0)
				{
					$rowc->set('access', 1);
				}
			}

			// Were there any changes?
			if (count($rowc->changelog()->get('notifications')) > 0 || $access != $rowc->get('access'))
			{
				if (!$rowc->store())
				{
					throw new Exception($rowc->getError(), 500);
				}
			}
		}

		// Display the ticket with changes, new comment
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $id),
			($this->getError() ? $this->getError() :  null),
			($this->getError() ? 'error' :  null)
		);
	}

	/**
	 * Removes a ticket and all associated records (tags, comments, etc.)
	 *
	 * @return	void
	 */
	public function deleteTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Check for an ID
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets')
			);
			return;
		}

		// Delete tags
		$tags = new Tags($id);
		$tags->removeAll();

		// Delete comments
		$comment = new Tables\Comment($this->database);
		$comment->deleteComments($id);

		$attach = new Tables\Attachment($this->database);
		if (!$attach->deleteAllForTicket($id))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets'),
				$attach->getError(),
				'error'
			);
			return;
		}

		// Delete ticket
		$ticket = new Tables\Ticket($this->database);
		$ticket->delete($id);

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets')
		);
	}

	/**
	 * Checks for a ticket and increases instance count if found
	 * Creates new ticket if not
	 *
	 * NOTE: This method is called by Rappture
	 * TODO: Create a proper API
	 *
	 *   option  = 'com_support';
	 *   task    = 'create';
	 *   no_html = 1;
	 *   type    = 1;
	 *   sesstoken (optional)
	 *
	 *   login    (optional) default: automated
	 *   severity (optional) default: normal
	 *   category (optional) default: Tools
	 *   summary  (optional) default: first 75 characters of report
	 *   report
	 *   email    (optional) default: supportemail
	 *   name     (optional) default: Automated Error Report
	 *   os       (optional)
	 *   browser  (optional)
	 *   ip       (optional)
	 *   hostname (optional)
	 *   uas      (optional)
	 *   referrer (optional)
	 *   cookies  (optional) default: 1 (since it's coming from rappture we assume they're already logged in and thus have cookies enabled)
	 *   section  (optional)
	 *   upload   (optional)
	 *
	 * @return  string
	 */
	public function createTask()
	{
		// trim and addslashes all posted items
		$incoming = array_map('trim', $_POST);
		$incoming = array_map('addslashes', $incoming);

		// initiate class and bind posted items to database fields
		$row = new Ticket();
		if (!$row->bind($incoming))
		{
			echo $row->getError();
			return;
		}
		$row->set('summary', $row->content('clean', 200));

		// Check for a session token
		$sessnum = '';
		if ($sess = Request::getVar('sesstoken', ''))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php');
			$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

			// retrieve the username and IP from session with this session token
			$query = "SELECT * FROM session WHERE session.sesstoken=" . $this->database->quote($sess) . " LIMIT 1";
			$mwdb->setQuery($query);
			$viewperms = $mwdb->loadObjectList();

			if ($viewperms)
			{
				foreach ($viewperms as $sinfo)
				{
					$row->set('login', $sinfo->username);
					$row->set('ip', $sinfo->remoteip);
					$sessnum = $sinfo->sessnum;
				}

				// get user's infor from login
				$user = User::getInstance($row->get('login'));
				$row->set('name', $user->get('name'));
				$row->set('email', $user->get('email'));
			}
		}

		$row->set('login', ($row->get('login') ? $row->get('login') : 'automated'));

		// check for an existing ticket with this report
		$summary = $row->get('summary');
		if (strstr($summary, '"') || strstr($summary, "'"))
		{
			$summary = str_replace("\'","\\\\\\\\\'", $summary);
			$summary = str_replace('\"','\\\\\\\\\"', $summary);
			$query = "SELECT id FROM `#__support_tickets` WHERE LOWER(summary) LIKE " . $this->database->quote('%' . strtolower($summary) . '%') . " AND type=1 LIMIT 1";
		}
		$query = "SELECT id FROM `#__support_tickets` WHERE LOWER(summary) LIKE " . $this->database->quote('%' . strtolower($summary) . '%') . " AND type=1 LIMIT 1";
		$this->database->setQuery($query);

		if ($ticket = $this->database->loadResult())
		{
			$changelog = '';

			// open existing ticket if closed
			$oldticket = new Ticket($ticket);
			$oldticket->set('instances', ($oldticket->get('instances') + 1));
			if (!$oldticket->isOpen())
			{
				$before = new Ticket($ticket);

				$oldticket->set('open', 1);
				$oldticket->set('status', 1);
				$oldticket->set('resolved', '');

				$rowc = new Comment();
				$rowc->set('ticket', $ticket);
				$rowc->set('comment', '');
				$rowc->set('created', Date::toSql());
				$rowc->set('created_by', User::get('id'));
				$rowc->set('access', 1);

				// Compare fields to find out what has changed for this ticket and build a changelog
				$rowc->changelog()->diff($before, $oldticket);

				if (!$rowc->store(true))
				{
					echo $rowc->getError();
					return;
				}
			}

			// store new content
			if (!$oldticket->store(true))
			{
				echo $oldticket->getError();
				return;
			}

			$status = $oldticket->status('text');
			$count  = $oldticket->get('instances');
		}
		else
		{
			// set some defaults
			$row->set('status', 0);
			$row->set('open', 1);
			$row->set('created', Date::toSql());
			$row->set('severity', ($row->get('severity') ? $row->get('severity') : 'normal'));
			$row->set('category', ($row->get('category') ? $row->get('category') : Lang::txt('COM_SUPPORT_CATEGORY_TOOLS')));
			$row->set('resolved', '');
			$row->set('email', ($row->get('email') ? $row->get('email') : $this->_data['supportemail']));
			$row->set('name', ($row->get('name') ? $row->get('name') : Lang::txt('COM_SUPPORT_AUTOMATED_REPORT')));
			$row->set('cookies', ($row->get('cookies') ? $row->get('cookies') : 1));
			$row->set('instances', 1);
			$row->set('section', ($row->get('section') ? $row->get('section') : 1));
			$row->set('type', 1);

			// store new content
			if (!$row->store(true))
			{
				echo $row->getError();
				return;
			}

			$row->tag($incoming['tags'], User::get('id'), 1);

			if ($attachment = $this->uploadTask($row->get('id')))
			{
				$row->set('report', $row->get('report') . "\n\n" . $attachment);
				if (!$row->store())
				{
					$this->setError($row->getError());
				}
			}

			$ticket = $row->get('id');
			$status = 'new';
			$count  = 1;
		}

		echo 'Ticket #' . $ticket . ' (' . $status . ') ' . $count . ' times';
	}

	/**
	 * Serves up files only after passing access checks
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Check logged in status
		if (User::isGuest())
		{
			$return = base64_encode(Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Get the ID of the file requested
		$id = Request::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new Tables\Attachment($this->database);
		$attach->load($id);
		if (!$attach->filename)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'), 404);
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$row = new Tables\Ticket($this->database);
		$row->load($attach->ticket);

		if (!$row->report)
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_TICKET_NOT_FOUND'), 404);
		}

		// Load ACL
		if ($row->login == User::get('username')
		 || $row->owner == User::get('id'))
		{
			if (!$this->acl->check('read', 'tickets'))
			{
				$this->acl->setAccess('read', 'tickets', 1);
			}
		}
		if ($this->acl->authorize($row->group))
		{
			$this->acl->setAccess('read', 'tickets', 1);
		}

		// Ensure the user is authorized to view this file
		if (!$this->acl->check('read', 'tickets'))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_NOT_AUTH'), 403);
		}

		// Ensure we have a path
		if (empty($file))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'), 404);
		}

		// Get the configured upload path
		$basePath = DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $attach->ticket;

		// Does the path start with a slash?
		$file = DS . ltrim($file, DS);
		// Does the beginning of the $attachment->path match the config path?
		if (substr($file, 0, strlen($basePath)) == $basePath)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$file = $basePath . $file;
		}

		// Add root path
		$filename = PATH_APP . $file;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND') . ' ' . $filename, 404);
		}

		// Initiate a new content server and serve up the file
		$xserver = new Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_SUPPORT_ERROR_SERVING_FILE'), 500);
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   string  $listdir  Directory to upload files to
	 * @return  string  A string that gets appended to messages
	 */
	public function uploadTask($listdir, $comment_id=0)
	{
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_MISSING_UPLOAD_DIRECTORY'));
			return '';
		}

		// Construct our file path
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $listdir;

		$row = new Tables\Attachment($this->database);

		// Rename temp directories
		if ($tmp = Request::getInt('tmp_dir'))
		{
			$tmpPath = PATH_APP . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $tmp;
			if (is_dir($tmpPath))
			{
				if (!\Filesystem::move($tmpPath, $path))
				{
					$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_MOVE_UPLOAD_PATH'));
					throw new Exception(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_MOVE_UPLOAD_PATH'), 500);
					return '';
				}
				$row->updateTicketId($tmp, $listdir);
			}
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!isset($file['name']) || !$file['name'])
		{
			//$this->setError(Lang::txt('SUPPORT_NO_FILE'));
			return '';
		}

		// Incoming
		$description = Request::getVar('description', '');

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(Filesystem::extension($file['name']));

		//make sure that file is acceptable type
		if (!in_array($ext, explode(',', $this->config->get('file_ext'))))
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
			return Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE');
		}

		$filename = Filesystem::name($file['name']);
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$finalfile = $path . DS . $filename . '.' . $ext;

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $finalfile))
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			if (!\Filesystem::isSafe($finalfile))
			{
				if (\Filesystem::delete($finalfile))
				{
					$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN'));
					return Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN');
				}
			}

			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$row->bind(array(
				'id'          => 0,
				'ticket'      => $listdir,
				'comment_id'  => $comment_id,
				'filename'    => $filename . '.' . $ext,
				'description' => $description
			));
			if (!$row->check())
			{
				$this->setError($row->getError());
			}
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
			if (!$row->id)
			{
				$row->getID();
			}

			return '{attachment#' . $row->id . '}';
		}
	}

	/**
	 * Parses incoming data for ticket filtering on the main ticket list
	 *
	 * @return  array  An array of filters to apply
	 */
	private function _getFilters()
	{
		// Query filters defaults
		$filters = array(
			'search'     => '',
			'status'     => 'open',
			'type'       => 0,
			'owner'      => '',
			'reportedby' => '',
			'severity'   => 'normal',
			'sort'       => trim(Request::getVar('filter_order', 'created')),
			'sortdir'    => trim(Request::getVar('filter_order_Dir', 'DESC')),
			'severity'   => ''
		);

		// Paging vars
		$filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$filters['start'] = Request::getInt('limitstart', 0);

		// Incoming
		$filters['_find'] = urldecode(trim(Request::getVar('find', '', 'post')));
		$filters['_show'] = urldecode(trim(Request::getVar('show', '', 'post')));

		if ($filters['_find'] != '' || $filters['_show'] != '')
		{
			$filters['start'] = 0;
		}
		else
		{
			$filters['_find'] = urldecode(trim(Request::getVar('find', '', 'get')));
			$filters['_show'] = urldecode(trim(Request::getVar('show', '', 'get')));
		}

		// Break it apart so we can get our filters
		// Starting string hsould look like "filter:option filter:option"
		if ($filters['_find'] != '')
		{
			$chunks = explode(' ', $filters['_find']);
			$filters['_show'] = '';
		}
		else
		{
			$chunks = explode(' ', $filters['_show']);
		}

		// Loop through each chunk (filter:option)
		foreach ($chunks as $chunk)
		{
			if (!strstr($chunk,':'))
			{
				if ((substr($chunk, 0, 1) == '"'
				 || substr($chunk, 0, 1) == "'")
				 && (substr($chunk, -1) == '"'
				 || substr($chunk, -1) == "'"))
				{
					$chunk = substr($chunk, 1, -1);  // Remove any surrounding quotes
				}

				$filters['search'] = $chunk;
				continue;
			}

			// Break each chunk into its pieces (filter, option)
			$pieces = explode(':', $chunk);

			// Find matching filters and ensure the vaule provided is valid
			switch ($pieces[0])
			{
				case 'q':
					$pieces[0] = 'search';
					if (isset($pieces[1])) {
						// Queries must be in quotes. If they're not, we ignore it
						if ((substr($pieces[1], 0, 1) == '"'
						|| substr($pieces[1], 0, 1) == "'")
						&& (substr($pieces[1], -1) == '"'
						|| substr($pieces[1], -1) == "'")) {
							$pieces[1] = substr($pieces[1], 1, -1);  // Remove any surrounding quotes
						}
					} else {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'status':
					$allowed = array('open', 'closed', 'all', 'new', 'waiting');
					if (!in_array($pieces[1],$allowed))
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array('submitted'=>0, 'automatic'=>1, 'none'=>2, 'tool'=>3);
					if (in_array($pieces[1],$allowed))
					{
						$pieces[1] = $allowed[$pieces[1]];
					}
					else
					{
						$pieces[1] = 0;
					}
				break;
				case 'owner':
					if (isset($pieces[1]) && $pieces[1] == 'me')
					{
						$pieces[1] = User::get('id');
					}
				break;
				case 'reportedby':
					if (isset($pieces[1]) && $pieces[1] == 'me')
					{
						$pieces[1] = User::get('username');
					}
				break;
				case 'severity':
					$allowed = array('critical', 'major', 'normal', 'minor', 'trivial');
					if (!in_array($pieces[1],$allowed))
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
			}

			$filters[$pieces[0]] = (isset($pieces[1])) ? $pieces[1] : '';
		}

		// Return the array
		return $filters;
	}

	/**
	 * Generates a select list of Super Administrator names
	 *
	 * @param  $name        Select element 'name' attribute
	 * @param  $active      Selected option
	 * @param  $nouser      Flag to set first option to 'No user'
	 * @param  $javascript  Any inline JS to attach to the element
	 * @param  $order       The sort order for items in the list
	 * @return string       HTML select list
	 */
	private function _userSelect($name, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$query = "SELECT a.id AS value, a.name AS text"
			. " FROM #__users AS a"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='user' AND aro.foreign_key = a.id"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;

		$this->database->setQuery($query);
		if ($nouser)
		{
			$users[] = \Html::select('option', '0', Lang::txt('COM_SUPPORT_NONE'), 'value', 'text');
			$users = array_merge($users, $this->database->loadObjectList());
		}
		else
		{
			$users = $this->database->loadObjectList();
		}

		$query = "SELECT a.id AS value, a.name AS text, aro.alias"
			. " FROM #__users AS a"
			. " INNER JOIN #__xgroups_members AS m ON m.uidNumber = a.id"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='group' AND aro.foreign_key = m.gidNumber"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;
		$this->database->setQuery($query);
		if ($results = $this->database->loadObjectList())
		{
			$groups = array();
			foreach ($results as $result)
			{
				if (!isset($groups[$result->alias]))
				{
					$groups[$result->alias] = array();
				}
				$groups[$result->alias][] = $result;
			}
			foreach ($groups as $gname => $gusers)
			{
				$users[] = \Html::select('optgroup', Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_GROUP') . ': ' . $gname);
				$users = array_merge($users, $gusers);
				$users[] = \Html::select('optgroup', Lang::txt('COM_SUPPORT_CHANGELOG_FIELD_GROUP') . ': ' . $gname);
			}
		}

		ksort($users);

		$users = \Html::select('genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Generates a select list of names based off group membership
	 *
	 * @param  $name        Select element 'name' attribute
	 * @param  $active      Selected option
	 * @param  $nouser      Flag to set first option to 'No user'
	 * @param  $javascript  Any inline JS to attach to the element
	 * @param  $group       The group to pull member names from
	 * @return string       HTML select list
	 */
	private function _userSelectGroup($name, $active, $nouser=0, $javascript=NULL, $group='')
	{
		$users = array();

		if (strstr($group, ','))
		{
			$groups = explode(',', $group);
			if (is_array($groups))
			{
				foreach ($groups as $g)
				{
					$hzg = \Hubzero\User\Group::getInstance(trim($g));

					if ($hzg->get('gidNumber'))
					{
						$members = $hzg->get('members');

						$users[] = \Html::select('optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u = User::getInstance($member);
							if (!is_object($u))
							{
								continue;
							}

							$m = new \stdClass();
							$m->value = $u->get('id');
							$m->text  = $u->get('name');
							$m->groupname = $g;

							$users[] = $m;
						}
						$users[] = \Html::select('option', '</OPTGROUP>');
					}
				}
			}
		}
		else
		{
			$hzg = \Hubzero\User\Group::getInstance($group);

			if ($hzg && $hzg->get('gidNumber'))
			{
				$members = $hzg->get('members');

				foreach ($members as $member)
				{
					$u = User::getInstance($member);
					if (!is_object($u))
					{
						continue;
					}

					$m = new \stdClass();
					$m->value = $u->get('id');
					$m->text  = $u->get('name');
					$m->groupname = $group;

					$names = explode(' ', $u->get('name'));
					$last = trim(end($names));

					$users[$last . ',' . $u->get('name')] = $m;
				}
			}

			ksort($users);
		}

		if ($nouser)
		{
			array_unshift($users, \Html::select('option', '0', Lang::txt('COM_SUPPORT_NONE'), 'value', 'text'));
		}

		$users = \Html::select('genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false);

		return $users;
	}
}
