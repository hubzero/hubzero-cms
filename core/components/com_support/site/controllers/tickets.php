<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Components\Support\Helpers\ACL;
use Components\Support\Helpers\Utilities;
use Components\Support\Models\Ticket;
use Components\Support\Models\Comment;
use Components\Support\Models\Tags;
use Components\Support\Models\Attachment;
use Components\Support\Models\QueryFolder;
use Components\Support\Models\Query;
use Components\Support\Models\Watching;
use Components\Support\Models\Category;
use Components\Support\Models\Message;
use Hubzero\Component\SiteController;
use Hubzero\Browser\Detector;
use Hubzero\Content\Server;
use Hubzero\Utility\Validate;
use Hubzero\Utility\Arr;
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

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';

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
		if (is_object($ticket) && $ticket->get('id'))
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
		if (is_object($ticket) && $ticket->get('id'))
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
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		if (!$this->acl->check('read', 'tickets'))
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

		$this->view->group = preg_replace('/[^0-9a-zA-Z_\-]/', '', Request::getString('group', '_none_'));

		// Set up some dates
		$date = new \Hubzero\Utility\Date();

		$year  = Request::getInt('year', $date->toLocal('Y'));
		$month = strftime("%m", $date->toLocal('m'));

		$this->view->year = $year;
		$this->view->opened = array();
		$this->view->closed = array();

		$sql = "SELECT DISTINCT(g.`cn`), g.description
				FROM `#__support_tickets` AS s
				LEFT JOIN `#__xgroups` AS g ON g.gidNumber=s.`group_id`
				WHERE s.`group_id` > 0
				AND s.type=" . $this->database->quote($this->view->type) . "
				ORDER BY g.description ASC";
		$this->database->setQuery($sql);
		$this->view->groups = $this->database->loadObjectList();

		// Users
		$this->view->users = null;

		if ($this->view->group == '_none_')
		{
			$query = "SELECT DISTINCT a.username, a.name, a.id
					FROM `#__users` AS a
					INNER JOIN `#__support_tickets` AS s ON s.owner = a.id
					WHERE a.block = '0' AND s.type=" . $this->database->quote($this->view->type) . " AND s.group_id=0
					ORDER BY a.name";
		}
		else if ($this->view->group)
		{
			$query = "SELECT a.username, a.name, a.id
					FROM `#__users` AS a
					INNER JOIN `#__xgroups_members` AS gm ON gm.uidNumber=a.id
					INNER JOIN `#__xgroups` AS g ON g.gidNumber=gm.gidNumber
					WHERE g.cn=" . $this->database->quote($this->view->group) ."
					ORDER BY a.name";
		}
		else
		{
			$query = "SELECT DISTINCT a.username, a.name, a.id
					FROM `#__users` AS a
					INNER JOIN `#__support_tickets` AS s ON s.owner = a.id
					WHERE a.block = '0' AND s.type=" . $this->database->quote($this->view->type) . "
					ORDER BY a.name";
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

		$this->view->start = Request::getString('start', $first . '-01');
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

		$this->view->end = Request::getString('end', '');

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
			$sql .= " AND `group_id`=0";
		}
		else if ($this->view->group)
		{
			$gidNumber = 0;
			if ($group = \Hubzero\User\Group::getInstance($this->view->group))
			{
				$gidNumber = $group->get('gidNumber');
			}
			$sql .= " AND `group_id`=" . $this->database->quote($gidNumber);
		}
		if ($this->view->start && $end)
		{
			$sql .= " AND created >= " . $this->database->quote($this->view->start . '-01 00:00:00') . " AND created < " . $this->database->quote($end . '-01 00:00:00');
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
				AND t.type=" . $this->database->quote($this->view->type) . " AND t.open=0";
		if ($this->view->group == '_none_')
		{
			$sql .= " AND t.`group_id`=0";
		}
		else if ($this->view->group)
		{
			$gidNumber = 0;
			if ($group = \Hubzero\User\Group::getInstance($this->view->group))
			{
				$gidNumber = $group->get('gidNumber');
			}
			$sql .= " AND t.`group_id`=" . $this->database->quote($gidNumber);
		}
		if ($this->view->start && $end)
		{
			$sql .= " AND t.closed >= " . $this->database->quote($this->view->start . '-01 00:00:00') . " AND t.closed < " . $this->database->quote($end . '-01 00:00:00');
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

		for ($k = $startyear, $n = $endyear; $k < $n; $k++)
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
		$this->view->users = $u;

		$this->view
			->set('config', $this->config)
			->set('first', $first)
			->set('month', $month)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Displays a list of support tickets
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (User::isGuest())
		{
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		$filters = $this->_getFilters();

		// Paging
		$filters['limit'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// Query to filter by
		$filters['show'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.show',
			'show',
			0,
			'int'
		);
		// Search
		$search       = urldecode(Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		// Get query list
		if (!$this->acl->check('read', 'tickets'))
		{
			$folders = QueryFolder::all()
				->whereEquals('user_id', 0)
				->whereEquals('iscore', 2)
				->order('ordering', 'asc')
				->rows();
		}
		else
		{
			$folders = QueryFolder::all()
				->whereEquals('user_id', User::get('id'))
				->order('ordering', 'asc')
				->rows();

			// Does the user have any folders?
			if (!count($folders))
			{
				// Get all the default folders
				$folders = QueryFolder::cloneCore(User::get('id'));
			}
		}

		$filters['search']  = '';
		$filters['sort']    = 'id';
		$filters['sortdir'] = 'DESC';

		$matchQuery = false;

		foreach ($folders as $folder)
		{
			foreach ($folder->queries->sort('ordering') as $query)
			{
				if ($query->get('id') != $filters['show'])
				{
					$filters['search'] = '';
				}

				$query->set('count', Ticket::countWithQuery($query, $filters));

				if ($query->get('id') == $filters['show'])
				{
					$matchQuery = true;
					if ($search)
					{
						$filters['search'] = $search;
					}

					$total = ($search) ? Ticket::countWithQuery($query, $filters) : $query->get('count');

					// Incoming sort
					$filters['sort']         = trim(Request::getState(
						$this->_option . '.' . $this->_controller . '.sort',
						'sort',
						$query->get('sort')
					));

					$filters['sortdir']     = trim(Request::getState(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'sortdir',
						$query->get('sort_dir')
					));

					// Get the records
					$tickets = Ticket::allWithQuery($query, $filters);
				}
			}
		}

		$watching = Watching::all()
			->whereEquals('user_id', User::get('id'))
			->rows()
			->fieldsByKey('ticket_id');

		$watch = array(
			'open'   => Ticket::all()->whereEquals('open', 1)->whereIn('id', $watching)->total(),
			'closed' => Ticket::all()->whereEquals('open', 0)->whereIn('id', $watching)->total()
		);

		$filters['search'] = $search;

		if ($filters['show'] < 0)
		{
			$total = $watch[($filters['show'] == -1 ? 'open' : 'closed')];

			$tickets = Ticket::all()
				->whereEquals('open', ($filters['show'] == -1 ? 1 : 0))
				->whereIn('id', $watching)
				->order('created', 'desc')
				->start($filters['start'])
				->limit($filters['limit'])
				->rows();
		}

		if (!$filters['show'] || ($filters['show'] > 0 && !$matchQuery))
		{
			// Jump back to the beginning of the folders list
			// and try to find the first query available
			// to make it the current "active" query
			foreach ($folders as $folder)
			{
				if (count($folder->queries) > 0)
				{
					$query = $folder->queries->first();
					$filters['show'] = $query->get('id');
					break;
				}
				else
				{	// for no custom queries.
					// TODO - not sure this works, pretty edge case though
					$query = Query::blank();
					$query->set('count', 0);
					$query->set('sort', 'created');
					$query->set('sort_dir', 'desc');
				}
			}

			// Set the total for the pagination
			$total = ($search) ? Ticket::countWithQuery($query, $filters) : $query->get('count');

			// Incoming sort
			$filters['sort']   = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				$query->sort
			));
			$filters['sortdir'] = trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				$query->sort_dir
			));

			// Get the records
			$tickets = Ticket::allWithQuery($query, $filters);
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view
			->set('watch', $watch)
			->set('title', $this->_title)
			->set('acl', $this->acl)
			->set('rows', $tickets)
			->set('total', $total)
			->set('filters', $filters)
			->set('folders', $folders)
			->display();
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
			$row = Ticket::blank();
			$row->set('open', 1)
				->set('status', 0)
				->set('ip', Request::ip())
				->set('uas', Request::getString('HTTP_USER_AGENT', '', 'server'))
				->set('referrer', base64_encode(Request::getString('HTTP_REFERER', null, 'server')))
				->set('cookies', (Request::getString('sessioncookie', '', 'cookie') ? 1 : 0))
				->set('instances', 1)
				->set('section', 1)
				->set('tool', Request::getString('tool', ''))
				->set('verified', 0);

			if ($referrer = Request::getString('referrer'))
			{
				// Rough test to see if it's already base64 encoded
				if (!Utilities::isBase64($referrer))
				{
					$referrer = base64_encode($referrer);
				}
				$row->set('referrer', $referrer);
			}

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
			$emailConfirmed = User::get('activation');
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

			$lists['categories'] = Category::all()->rows();
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view
			->set('acl', $this->acl)
			->set('title', $this->_title)
			->set('file_types', $this->config->get('file_ext'))
			->set('lists', $lists)
			->set('row', $row)
			->set('captchas', Event::trigger('captcha.onDisplay'))
			->setLayout('new')
			->setErrors($this->getErrors())
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
		$verified = ($verified > 0 ? 1 : 0);
		if (!isset($_POST['reporter']) || !isset($_POST['problem']))
		{
			// This really, REALLY shouldn't happen.
			App::abort(400, Lang::txt('COM_SUPPORT_ERROR_MISSING_DATA'));
		}
		$reporter = Request::getArray('reporter', array(), 'post');
		$problem  = Request::getArray('problem', array(), 'post');

		foreach ($reporter as $key => $field)
		{
			if (is_array($field))
			{
				$reporter[$key] = Arr::toString($field);
			}
		}

		foreach ($problem as $key => $field)
		{
			if (is_array($field))
			{
				$problem[$key] = Arr::toString($field);
			}
		}

		$reporter = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $reporter);

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
			if (!$reporter['name']
			 || !$reporter['email']
			 || !$problem['long'])
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_MISSING_DATA'));
			}

			if (!$validemail)
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_INVALID_EMAIL'));
			}

			if (!$customValidation)
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_INVALID_DATA'));
			}

			return $this->newTask();
		}

		// Get the user's IP
		$ip = Request::ip();
		$hostname = gethostbyaddr(Request::getString('REMOTE_ADDR', '', 'server'));

		if (!$verified)
		{
			// Check CAPTCHA
			$validcaptchas = Event::trigger('captcha.onCheckAnswer');
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
			$botcheck = Request::getString('botcheck', '');
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
				$this->view
					->setErrors($this->getErrors())
					->setLayout('error')
					->display();
				return;
			}
			else
			{
				Request::setVar('task', 'new');

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

		$group = isset($problem['group']) ? $problem['group'] : '';
		if (!is_numeric($group))
		{
			if ($g = \Hubzero\User\Group::getInstance($group))
			{
				$group = $g->get('gidNumber');
			}
		}

		// Initiate class and bind data to database fields
		$row = Ticket::blank();
		$row->set('open', 1);
		$row->set('status', 0);
		$row->set('created', Date::toSql());
		$row->set('login', $reporter['login']);
		$row->set('severity', (isset($problem['severity']) ? $problem['severity'] : 'normal'));
		$row->set('owner', (isset($problem['owner']) ? $problem['owner'] : 0));
		$row->set('category', (isset($problem['category']) ? $problem['category'] : ''));
		$row->set('summary', $problem['short']);
		$row->set('report', $problem['long']);
		$row->set('email', $reporter['email']);
		$row->set('name', $reporter['name']);
		$row->set('ip', $ip);
		$row->set('hostname', $hostname);
		$row->set('uas', Request::getString('HTTP_USER_AGENT', '', 'server'));
		$row->set('referrer', base64_decode($problem['referer']));
		$row->set('cookies', (Request::getString('sessioncookie', '', 'cookie') ? 1 : 0));
		$row->set('instances', 1);
		$row->set('section', 1);
		$row->set('group_id', (int)$group);

		$browser = new Detector();

		$row->set('os', $browser->platform() . ' ' . $browser->platformVersion());
		$row->set('browser', $browser->name() . ' ' . $browser->version());

		if (isset($incoming['target_date']))
		{
			if (!$incoming['target_date'])
			{
				$row->set('target_date', null);
			}
			else
			{
				$row->set('target_date', Date::of($incoming['target_date'], Config::get('offset'))->toSql());
			}
		}

		// check if previous ticket submitted is the same as this one.
		$prevSubmission = Ticket::all()
			->whereEquals('status', 0)
			->whereEquals('open', 1)
			->order('id', 'desc')
			->limit(1)
			->start(0)
			->row();

		// for the first ticket ever
		if ($prevSubmission->get('report') == $row->get('report') && (time() - strtotime($prevSubmission->get('created')) <= 15))
		{
			$this->setError(Lang::txt('COM_SUPPORT_TICKET_DUPLICATE_DETECTION'));
			return $this->newTask($row);
		}

		// Save the data
		if (!$row->save())
		{
			$this->setError($row->getError());
		}

		$attachment = $this->uploadTask($row->get('id'));

		// Save tags
		$row->tag(Request::getString('tags', '', 'post'), User::get('id'));

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
				foreach ($row->attachments as $attachment)
				{
					if ($attachment->size() < 2097152)
					{
						if ($attachment->isImage())
						{
							$file = basename($attachment->path());
							$html = preg_replace('/<a class="img" data\-filename="' . str_replace('.', '\.', $file) . '" href="(.*?)"\>(.*?)<\/a>/i', '<img src="' . $message->getEmbed($attachment->path()) . '" alt="" />', $html);
						}
						else
						{
							$message->addAttachment($attachment->path());
						}
					}
				}
			}

			$message->addPart($html, 'text/html');

			// Loop through the addresses
			foreach ($defs as $def)
			{
				$def = trim($def);

				// Check if the address should come from site config
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

		// Log activity
		$creator = User::getInstance($row->get('login'));

		if ($creator && $creator->get('id'))
		{
			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'created',
					'scope'       => 'support.ticket',
					'scope_id'    => $row->get('id'),
					'description' => Lang::txt('COM_SUPPORT_ACTIVITY_TICKET_CREATED', '<a href="' . Route::url($row->link()) . '">#' . $row->get('id') . ' - ' . $row->get('summary') . '</a>'),
					'details'     => array(
						'id'      => $row->get('id'),
						'summary' => $row->get('summary'),
						'url'     => Route::url($row->link())
					)
				],
				'recipients' => [
					['support.tickets', 1],
					['user', $creator->get('id')]
				]
			]);
		}

		if (!User::isGuest() && $this->acl->check('update', 'tickets') > 0)
		{
			// Only do the following if a comment was posted
			// otherwise, we're only recording a changelog
			$old = Ticket::blank();
			$old->set('open', 1);
			$old->set('owner', 0);
			$old->set('status', 0);
			$old->set('tags', '');
			$old->set('severity', 'normal');

			$rowc = Comment::blank();
			$rowc->set('ticket', $row->get('id'));
			$rowc->set('created', Date::toSql());
			$rowc->set('created_by', User::get('id'));
			$rowc->set('access', 1);
			$rowc->set('comment', Lang::txt('COM_SUPPORT_TICKET_SUBMITTED'));

			// Compare fields to find out what has changed for this ticket and build a changelog
			$rowc->changelog()->diff($old, $row);

			$rowc->changelog()->cced(Request::getString('cc', ''));

			// Were there any changes, CCs, or comments to record?
			if (count($rowc->changelog()->get('changes')) > 0 || count($rowc->changelog()->get('cc')) > 0)
			{
				// Save the data
				if (!$rowc->save())
				{
					App::abort(500, $rowc->getError());
				}

				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->assignee->get('name'),
						'email' => $row->assignee->get('email'),
						'id'    => $row->assignee->get('id')
					));
				}
				elseif ($row->get('group_id'))
				{
					$group = \Hubzero\User\Group::getInstance($row->get('group_id'));

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

				$recipients = array(
					['support.tickets', 1]
				);

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
						$recipients[] = ['user', $to['id']];

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
					if (!$rowc->save())
					{
						$this->setError($rowc->getError());
					}
				}

				// Record the activity
				if (!$rowc->isPrivate() && $creator->get('id'))
				{
					$recipients[] = ['user', $creator->get('id')];
				}

				$desc = Lang::txt('COM_SUPPORT_ACTIVITY_TICKET_UPDATED', '<a href="' . Route::url($row->link()) . '">#' . $row->get('id') . ' - ' . $row->get('summary') . '</a>');
				if ($rowc->get('comment'))
				{
					$desc = Lang::txt('COM_SUPPORT_ACTIVITY_COMMENT_CREATED', $rowc->get('id'), '<a href="' . Route::url($row->link()) . '">#' . $row->get('id') . ' - ' . $row->get('summary') . '</a>');
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'created',
						'scope'       => 'support.ticket.comment',
						'scope_id'    => $rowc->get('id'),
						'description' => $desc,
						'details'     => array(
							'id'      => $row->get('id'),
							'summary' => $row->get('summary'),
							'url'     => Route::url($row->link()),
							'comment' => $rowc->get('id')
						)
					],
					'recipients' => $recipients
				]);
			}
		}

		// Trigger any events that need to be called
		Event::trigger('support.onTicketSubmission', array($row));

		// Output Thank You message
		$this->view
			->set('ticket', $row->get('id'))
			->set('no_html', $no_html)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Attempts to detect if some text is spam
	 * Checks for blacklisted IPs, bad words, and overuse of links
	 *
	 * @param   string  $text
	 * @param   string  $ip
	 * @return  boolean
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

		// Build an array of patterns to check against
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
		$row = Ticket::oneOrFail($id);

		// Check authorization
		if (User::isGuest())
		{
			$return = base64_encode(Route::url($row->link(), false, true));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Ensure the user is authorized to view this ticket
		if (!$row->access('read', 'tickets'))
		{
			App::abort(403, Lang::txt('COM_SUPPORT_ERROR_NOT_AUTH'));
		}

		$filters = array(
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
			if ($row->isWatching(User::get('id')))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$row->stopWatching(User::get('id'));
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					$row->watch(User::get('id'));

					if (!$row->isWatching(User::get('id'), true))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		$lists = array();

		$lists['categories'] = Category::all()->rows();

		// Get messages
		$lists['messages'] = Message::all()->rows();

		// Get severities
		$lists['severities'] = Utilities::getSeverities($this->config->get('severities'));

		// Populate the list of assignees based on if the ticket belongs to a group or not
		if (trim($row->get('group_id')))
		{
			$lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]',
				$row->get('owner'),
				1,
				'',
				$row->get('group_id')
			);
		}
		elseif (trim($this->config->get('group')))
		{
			$lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]',
				$row->get('owner'),
				1,
				'',
				trim($this->config->get('group'))
			);
		}
		else
		{
			$lists['owner'] = $this->_userSelect(
				'ticket[owner]',
				$row->get('owner'),
				1
			);
		}

		// Set the pathway
		$this->_buildPathway($row);

		// Set the page title
		$this->_buildTitle($row);

		if (\Notify::any('support'))
		{
			foreach (\Notify::messages('support') as $error)
			{
				if ($error['type'] == 'error')
				{
					$this->setError($error['message']);
				}
			}
		}

		if (!$comment)
		{
			$comment = Comment::blank();
		}

		// Output HTML
		$this->view
			->set('row', $row)
			->set('filters', $filters)
			->set('title', $this->_title)
			->set('lists', $lists)
			->set('config', $this->config)
			->set('comment', $comment)
			->setLayout('ticket')
			->setErrors($this->getErrors())
			->display();
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
			App::abort(500, Lang::txt('COM_SUPPORT_ERROR_MISSING_TICKET_ID'));
		}

		$comment  = Request::getString('comment', '', 'post');
		$incoming = Request::getArray('ticket', array(), 'post');
		$incoming = array_map('trim', $incoming);

		if (isset($incoming['target_date']))
		{
			if (!$incoming['target_date'])
			{
				$incoming['target_date'] = null;
			}
			else
			{
				$incoming['target_date'] = Date::of($incoming['target_date'], Config::get('offset'))->toSql();
			}
		}

		// Load the old ticket so we can compare for the changelog
		$old = Ticket::oneOrNew($id);
		$old->set('tags', $old->tags('string'));

		// Initiate class and bind posted items to database fields
		$row = Ticket::oneOrNew($id)->set($incoming);

		$rowc = Comment::blank();
		$rowc->set('ticket', $id);

		// Check if changes were made within the time the comment was started and posted
		$started = Request::getString('started', Date::toSql(), 'post');

		$lastcomment = $row->comments()
			->order('created', 'DESC')
			->limit(1)
			->start(0)
			->row();

		if ($lastcomment && $lastcomment->get('created') > $started)
		{
			$rowc->set('comment', $comment);
			$this->setError(Lang::txt('Changes were made to this ticket in the time since you began commenting/making changes. Please review your changes before submitting.' . $lastcomment->get('created'). ' > ' . $started));
			return $this->ticketTask($rowc);
		}

		// Update ticket status if necessary
		$row->set('open', $row->status->get('open', 1));

		if ($id && isset($incoming['status']) && $incoming['status'] == 0)
		{
			$row->set('open', 0);
			$row->set('resolved', Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'));
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
			// If a comment was posted always change status to open.  This is the typical expected behavior. 
			// Preventing new comments from re-opening a ticket can result in comments that are never responded to.
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ((!$row->isOpen()) && $row->get('open') == $old->get('open') || $row->isWaiting() && User::get('username') == $row->get('login'))
			{
				$row->open();
			}
		}

		// Store new content
		if (!$row->save())
		{
			App::abort(500, $row->getError());
		}

		// Save the tags
		$row->tag(Request::getString('tags', '', 'post'), User::get('id'));
		$row->set('tags', $row->tags('string'));

		// Create a new support comment object and populate it
		$access = Request::getInt('access', 0);

		$rowc->set('ticket', $id);
		$rowc->set('comment', $comment);
		$rowc->set('created', Date::toSql());
		$rowc->set('created_by', User::get('id'));
		$rowc->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$rowc->changelog()->diff($old, $row);

		$rowc->changelog()->cced(Request::getString('cc', ''));

		// Save the data
		if (!$rowc->save())
		{
			App::abort(500, $rowc->getError());
		}

		Event::trigger('support.onTicketUpdate', array($row, $rowc));

		if ($tmp = Request::getInt('tmp_dir'))
		{
			$attachments = Attachment::all()
				->whereEquals('comment_id', $tmp)
				->rows();

			foreach ($attachments as $attach)
			{
				$attach->set('comment_id', $rowc->get('id'));
				$attach->save();
			}
		}

		$attachment = $this->uploadTask($row->get('id'), $rowc->get('id'));

		// Only do the following if a comment was posted
		// otherwise, we're only recording a changelog
		if ($rowc->get('comment')
		 || $row->get('owner') != $old->get('owner')
		 || $row->get('group_id') != $old->get('group_id')
		 || $rowc->attachments->count() > 0)
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
						'name'  => $row->submitter->get('name'),
						'email' => $row->submitter->get('email'),
						'id'    => $row->submitter->get('id')
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
						'name'  => $old->assignee->get('name'),
						'email' => $old->assignee->get('email'),
						'id'    => $old->assignee->get('id')
					));
				}
				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->assignee->get('name'),
						'email' => $row->assignee->get('email'),
						'id'    => $row->assignee->get('id')
					));
				}
				elseif ($row->get('group_id'))
				{
					$group = \Hubzero\User\Group::getInstance($row->get('group_id'));

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
			foreach ($row->watchers as $watcher)
			{
				$this->acl->setUser($watcher->user_id);
				if (!$rowc->isPrivate() || ($rowc->isPrivate() && $this->acl->check('read', 'private_comments')))
				{
					$rowc->addTo($watcher->user_id, 'watcher');
				}
			}
			$this->acl->setUser(User::get('id'));

			$recipients = array(
				['support.tickets', 1]
			);

			if (count($rowc->to()))
			{
				$this->config->set('email_terse', Request::getInt('email_terse', 0));

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
					foreach ($rowc->attachments as $attachment)
					{
						if ($attachment->size() < 2097152)
						{
							$message['attachments'][] = $attachment->path();
						}
					}
				}

				foreach ($rowc->to('ids') as $to)
				{
					$recipients[] = ['user', $to['id']];

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
				if (!$rowc->get('comment') && $rowc->attachments->count() <= 0)
				{
					$rowc->set('access', 1);
				}
			}

			// Were there any changes?
			if (count($rowc->changelog()->get('notifications')) > 0 || $access != $rowc->get('access'))
			{
				if (!$rowc->save())
				{
					App::abort(500, $rowc->getError());
				}
			}

			$desc = Lang::txt('COM_SUPPORT_ACTIVITY_TICKET_UPDATED', '<a href="' . Route::url($row->link()) . '">#' . $row->get('id') . ' - ' . $row->get('summary') . '</a>');
			if ($rowc->get('comment'))
			{
				$desc = Lang::txt('COM_SUPPORT_ACTIVITY_COMMENT_CREATED', $rowc->get('id'), '<a href="' . Route::url($row->link()) . '">#' . $row->get('id') . ' - ' . $row->get('summary') . '</a>');
			}

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'created',
					'scope'       => 'support.ticket.comment',
					'scope_id'    => $rowc->get('id'),
					'description' => $desc,
					'details'     => array(
						'id'      => $row->get('id'),
						'summary' => $row->get('summary'),
						'url'     => Route::url($row->link()),
						'comment' => $rowc->get('id')
					)
				],
				'recipients' => $recipients
			]);
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

		// Load the record
		$ticket = Ticket::oneOrFail($id);

		$description = Lang::txt('COM_SUPPORT_ACTIVITY_TICKET_DELETED', '<a href="' . Route::url($ticket->link()) . '">#' . $ticket->get('id') . ' - ' . $ticket->get('summary') . '</a>');

		// Delete ticket
		if (!$ticket->destroy())
		{
			Notify::error($ticket->getError());
		}

		// Log the activity
		$recipients = array(
			['support.tickets', 1]
		);

		$creator = User::getInstance($ticket->get('login'));
		if ($creator && $creator->get('id'))
		{
			$recipients[] = ['user', $creator->get('id')];
		}
		if ($ticket->get('owner'))
		{
			$recipients[] = ['user', $ticket->assignee->get('id')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'support.ticket',
				'scope_id'    => $id,
				'description' => $description,
				'details'     => array(
					'id'      => $id
				)
			],
			'recipients' => $recipients
		]);

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
		$row = Ticket::blank();
		if (!$row->set($incoming))
		{
			echo $row->getError();
			return;
		}

		// Check for a session token
		$sessnum = '';
		if ($sess = Request::getString('sesstoken', ''))
		{
			include_once Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php';

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
			$summary = str_replace("\'", "\\\\\\\\\'", $summary);
			$summary = str_replace('\"', '\\\\\\\\\"', $summary);
			$query = "SELECT id FROM `#__support_tickets` WHERE LOWER(summary) LIKE " . $this->database->quote('%' . strtolower($summary) . '%') . " AND type=1 LIMIT 1";
		}
		$query = "SELECT id FROM `#__support_tickets` WHERE LOWER(summary) LIKE " . $this->database->quote('%' . strtolower($summary) . '%') . " AND type=1 LIMIT 1";
		$this->database->setQuery($query);

		if ($ticket = $this->database->loadResult())
		{
			$changelog = '';

			// open existing ticket if closed
			$oldticket = Ticket::oneOrNew($ticket);
			$oldticket->set('instances', ($oldticket->get('instances') + 1));
			if (!$oldticket->isOpen())
			{
				$before = Ticket::oneOrNew($ticket);

				$oldticket->set('open', 1);
				$oldticket->set('status', 1);
				$oldticket->set('resolved', '');

				$rowc = Comment::blank();
				$rowc->set('ticket', $ticket);
				$rowc->set('comment', '');
				$rowc->set('created', Date::toSql());
				$rowc->set('created_by', User::get('id'));
				$rowc->set('access', 1);

				// Compare fields to find out what has changed for this ticket and build a changelog
				$rowc->changelog()->diff($before, $oldticket);

				if (!$rowc->save())
				{
					echo $rowc->getError();
					return;
				}
			}

			// store new content
			if (!$oldticket->save())
			{
				echo $oldticket->getError();
				return;
			}

			$status = $oldticket->status->get('title');
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
			if (!$row->save())
			{
				echo $row->getError();
				return;
			}

			$row->tag($incoming['tags'], User::get('id'));

			$this->uploadTask($row->get('id'));

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
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Get the ID of the file requested
		$id = Request::getInt('id', 0);

		// Instantiate an attachment object
		$attach = Attachment::oneOrFail($id);

		// Get the parent ticket the file is attached to
		$row = Ticket::oneOrFail($attach->ticket);

		// Load ACL
		if ($row->login == User::get('username')
		 || $row->owner == User::get('id'))
		{
			if (!$this->acl->check('read', 'tickets'))
			{
				$this->acl->setAccess('read', 'tickets', 1);
			}
		}
		if ($this->acl->authorize($row->group_id))
		{
			$this->acl->setAccess('read', 'tickets', 1);
		}

		// Ensure the user is authorized to view this file
		if (!$this->acl->check('read', 'tickets'))
		{
			App::abort(403, Lang::txt('COM_SUPPORT_ERROR_NOT_AUTH'));
		}

		// Ensure we have a path
		if (empty($attach->get('filename')))
		{
			App::abort(404, Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND'));
		}

		// Add root path
		$filename = $attach->path();

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('COM_SUPPORT_ERROR_FILE_NOT_FOUND') . ' ' . $filename);
		}

		// Initiate a new content server and serve up the file
		$xserver = new Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_SUPPORT_ERROR_SERVING_FILE'));
		}

		exit;
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   itneger  $listdir     Directory to upload files to
	 * @param   integer  $comment_id
	 * @return  string   A string that gets appended to messages
	 */
	public function uploadTask($listdir, $comment_id=0)
	{
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_MISSING_UPLOAD_DIRECTORY'));
			return '';
		}

		$row = Attachment::blank();

		// Construct our file path
		$path = $row->rootPath() . DS . $listdir;

		// Rename temp directories
		if ($tmp = Request::getInt('tmp_dir'))
		{
			$tmpPath = $row->rootPath() . DS . $tmp;

			if (is_dir($tmpPath))
			{
				if (!Filesystem::move($tmpPath, $path))
				{
					$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_MOVE_UPLOAD_PATH'));
					return '';
				}

				$attachments = Attachment::all()
					->whereEquals('ticket', $tmp)
					->rows();

				foreach ($attachments as $attachment)
				{
					$attachment->set('ticket', $listdir);
					if (!$attachment->save())
					{
						$this->setError($attachment->getError());
					}
				}
			}
		}

		// Incoming file
		$file = Request::getArray('upload', array(), 'files');
		if (!isset($file['name']) || !$file['name'])
		{
			//$this->setError(Lang::txt('SUPPORT_NO_FILE'));
			return '';
		}

		// Incoming
		$description = Request::getString('description', '');

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		$mediaConfig = Component::params('com_media');

		$sizeLimit = $this->config->get('maxAllowed');
		if (!$sizeLimit)
		{
			// Size limit is in MB, so we need to turn it into just B
			$sizeLimit = $mediaConfig->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;
		}

		$exts = $this->config->get('file_ext');
		$exts = $exts ?: $mediaConfig->get('upload_extensions');
		$allowed = array_values(array_filter(explode(',', $exts)));

		foreach ($file['name'] as $i => $name)
		{
			if (!trim($name))
			{
				continue;
			}

			if ($file['size'][$i] > $sizeLimit)
			{
				$this->setError(Lang::txt('File is too large. Max file upload size is %s', Number::formatBytes($sizeLimit)));
				continue;
			}

			// Make the filename safe
			$name = Filesystem::clean($name);
			$name = str_replace(' ', '_', $name);
			$ext = strtolower(Filesystem::extension($name));

			// Make sure that file is acceptable type
			if (!in_array($ext, $allowed))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
				continue;
				//return Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE');
			}

			$filename = Filesystem::name($name);
			while (file_exists($path . DS . $filename . '.' . $ext))
			{
				$filename .= rand(10, 99);
			}

			$finalfile = $path . DS . $filename . '.' . $ext;

			// Perform the upload
			if (!Filesystem::upload($file['tmp_name'][$i], $finalfile))
			{
				$this->setError(Lang::txt('COM_SUPPORT_ERROR_UPLOADING'));
				//return '';
			}
			else
			{
				// Scan for viruses
				if (!Filesystem::isSafe($finalfile))
				{
					if (Filesystem::delete($finalfile))
					{
						$this->setError(Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN'));
						//return Lang::txt('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN');
					}
				}

				// File was uploaded
				// Create database entry
				$description = htmlspecialchars($description);

				$row = Attachment::blank();
				$row->set(array(
					'id'          => 0,
					'ticket'      => $listdir,
					'comment_id'  => $comment_id,
					'filename'    => $filename . '.' . $ext,
					'description' => $description
				));
				if (!$row->save())
				{
					$this->setError($row->getError());
				}

				//return '{attachment#' . $row->get('id') . '}';
			}
		}

		return '';
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
			'sort'       => trim(Request::getString('filter_order', 'created')),
			'sortdir'    => trim(Request::getString('filter_order_Dir', 'DESC')),
			'severity'   => ''
		);

		// Paging vars
		$filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$filters['start'] = Request::getInt('limitstart', 0);

		// Incoming
		$filters['_find'] = urldecode(trim(Request::getString('find', '', 'post')));
		$filters['_show'] = urldecode(trim(Request::getString('show', '', 'post')));

		if ($filters['_find'] != '' || $filters['_show'] != '')
		{
			$filters['start'] = 0;
		}
		else
		{
			$filters['_find'] = urldecode(trim(Request::getString('find', '', 'get')));
			$filters['_show'] = urldecode(trim(Request::getString('show', '', 'get')));
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
			if (!strstr($chunk, ':'))
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
					if (!in_array($pieces[1], $allowed))
					{
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array('submitted' => 0, 'automatic' => 1, 'none' => 2, 'tool' => 3);
					if (in_array($pieces[1], $allowed))
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
					if (!in_array($pieces[1], $allowed))
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
	 * @param   string   $name        Select element 'name' attribute
	 * @param   itneger  $active      Selected option
	 * @param   integer  $nouser      Flag to set first option to 'No user'
	 * @param   string   $javascript  Any inline JS to attach to the element
	 * @param   string   $order       The sort order for items in the list
	 * @return  string   HTML select list
	 */
	private function _userSelect($name, $active, $nouser=0, $javascript=null, $order='a.name')
	{
		$query = "SELECT a.id AS value, a.name AS text"
			. " FROM `#__users` AS a"
			. " INNER JOIN `#__support_acl_aros` AS aro ON aro.model='user' AND aro.foreign_key = a.id"
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
			. " FROM `#__users` AS a"
			. " INNER JOIN `#__xgroups_members` AS m ON m.uidNumber = a.id"
			. " INNER JOIN `#__support_acl_aros` AS aro ON aro.model='group' AND aro.foreign_key = m.gidNumber"
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
	private function _userSelectGroup($name, $active, $nouser=0, $javascript=null, $group='')
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
							if (!(is_object($u) && $u->get('block') == '0'))
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
					if (!(is_object($u) && $u->get('block') == '0'))
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
