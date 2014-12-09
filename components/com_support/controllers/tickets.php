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

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');

/**
 * Manage support tickets
 */
class SupportControllerTickets extends \Hubzero\Component\SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->acl = SupportACL::getACL();

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param      object $ticket SupportTicket
	 * @return     void
	 */
	protected function _buildPathway($ticket=null)
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_('COM_SUPPORT'),
				'index.php?option=' . $this->_option . '&controller=index'
			);
		}
		if (count($pathway->getPathWay()) == 1  && $this->_task)
		{
			$task = $this->_task;
			if ($this->_task == 'ticket' || $this->_task == 'new' || $this->_task == 'display')
			{
				$task = 'tickets';
			}
			$pathway->addItem(
				JText::_('COM_SUPPORT_' . strtoupper($task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $task
			);
			if ($this->_task == 'new')
			{
				$pathway->addItem(
					JText::_('COM_SUPPORT_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
		if (is_object($ticket) && $ticket->exists())
		{
			$pathway->addItem(
				'#' . $ticket->get('id'),
				$ticket->link()
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param      object $ticket SupportTicket
	 * @return     void
	 */
	protected function _buildTitle($ticket=null)
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task)
		{
			if ($this->_task == 'new' || $this->_task == 'display')
			{
				$this->_title .= ': ' . JText::_('COM_SUPPORT_TICKETS');
			}
			if ($this->_task != 'display')
			{
				$this->_title .= ': ' . JText::_('COM_SUPPORT_' . strtoupper($this->_task));
			}
		}
		if (is_object($ticket) && $ticket->exists())
		{
			$this->_title .= ' #' . $ticket->get('id');
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Displays a list of tickets
	 *
	 * @return	void
	 */
	public function statsTask()
	{
		// Check authorization
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		if (!$this->acl->check('read','tickets'))
		{
			$this->_return = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets');
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		$type = JRequest::getVar('type', 'submitted');
		$this->view->type = ($type == 'automatic') ? 1 : 0;

		$this->view->group = JRequest::getVar('group', '_none_');

		// Set up some dates
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
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.id"	// map user to aro
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
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.id"	// map user to aro
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
				FROM #__support_tickets
				WHERE report!=''
				AND type='{$this->view->type}' ORDER BY created ASC LIMIT 1";
		$this->database->setQuery($sql);
		$first = intval($this->database->loadResult());

		$startyear  = $first;
		$startmonth = 1;

		$this->view->start = JRequest::getVar('start', $first . '-01');
		if ($this->view->start != $first . '-01')
		{
			if (preg_match("/([0-9]{4})-([0-9]{2})/", $this->view->start, $regs))
			{
				$startmonth = date("m", mktime(0, 0, 0, $regs[2], 1, $regs[1]));
				$startyear = $first = date("Y", mktime(0, 0, 0, $regs[2], 1, $regs[1]));
				//$end = $year . '-' . $month;
			}
		}

		$this->view->end   = JRequest::getVar('end', '');

		$endmonth = $month;
		$endyear = date("Y");
		$endyear++;

		$end = '';
		if ($this->view->end)
		{
			// We need to get the NEXT month. This is so that for a time range
			// of 2013-01 to 2013-12 will display data for all of 2013-12.
			// So, the actual time range is 2013-01-01 00:00:00 to 2014-01-01 00:00:00
			if (preg_match("/([0-9]{4})-([0-9]{2})/", $this->view->end, $regs))
			{
				$endmonth = intval($regs[2]);
				$endyear  = intval($regs[1]);
				$endyear++;

				$month = date("m", mktime(0, 0, 0, ($endmonth+1), 1, $regs[1]));
				$year = date("Y", mktime(0, 0, 0, ($endmonth+1), 1, $regs[1]));
				$end = $year . '-' . $month;
			}
		}
		else
		{
			$this->view->end = $year . '-' . $month;
		}

		// Opened tickets
		$sql = "SELECT id, created, YEAR(created) AS `year`, MONTH(created) AS `month`, open, status, owner
				FROM #__support_tickets
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
				FROM #__support_tickets AS t
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
		// First we need to loop through all the entries and reove some potential duplicates
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
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		$this->view->database = $this->database;

		// Create a Ticket object
		$obj = new SupportTicket($this->database);

		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$this->view->total = 0;
		$this->view->rows = array();

		$this->view->filters = $this->_getFilters();
		// Paging
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// Query to filter by
		$this->view->filters['show'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.show',
			'show',
			0,
			'int'
		);
		// Search
		$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		$sq = new SupportQuery($this->database);
		$this->view->queries = array();
		if ($this->acl->check('read', 'tickets'))
		{
			$this->view->queries['common'] = $sq->getCommon();
			if (!$this->view->queries['common'] || count($this->view->queries['common']) <= 0)
			{
				$this->view->queries['common'] = $sq->populateDefaults('common');
			}
		}
		else
		{
			$this->view->queries['common'] = $sq->getCommonNotInACL();
			if (!$this->view->queries['common'] || count($this->view->queries['common']) <= 0)
			{
				$this->view->queries['common'] = $sq->populateDefaults('commonnotacl');
			}
		}
		$this->view->queries['mine']   = $sq->getMine();
		$this->view->queries['custom'] = $sq->getCustom($this->juser->get('id'));

		if (!$this->view->queries['mine'] || count($this->view->queries['mine']) <= 0)
		{
			$this->view->queries['mine'] = $sq->populateDefaults('mine');
		}
		// If no query is set, default to the first one in the list
		if (!$this->view->filters['show'])
		{
			$this->view->filters['show'] = $this->view->queries['common'][0]->id;
			/*if ($this->acl->check('read', 'tickets'))
			{
				$this->view->filters['show'] = $this->view->queries['common'][0]->id;
			}
			else
			{
				$this->view->filters['show'] = $this->view->queries['mine'][0]->id;
			}*/
		}

		// Loop through each grouping
		foreach ($this->view->queries as $key => $queries)
		{
			// Loop through each query in a group
			foreach ($queries as $k => $query)
			{
				$filters = $this->view->filters;

				// Build the query from the condition set
				//if (!$query->query)
				//{
					$query->query = $sq->getQuery($query->conditions);
				//}
				if ($query->id != $this->view->filters['show'])
				{
					$filters['search'] = '';
				}
				// Get a record count
				$this->view->queries[$key][$k]->count = $obj->getCount($query->query, $filters);
				// The query is the current active query
				// get records
				if ($query->id == $this->view->filters['show'])
				{
					// Set the total for the pagination
					$this->view->total = $this->view->queries[$key][$k]->count;
					// Incoming sort
					$this->view->filters['sort']    = trim($app->getUserStateFromRequest(
						$this->_option . '.' . $this->_controller . '.sort',
						'sort',
						$query->sort
					));
					$this->view->filters['sortdir'] = trim($app->getUserStateFromRequest(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'sortdir',
						$query->sort_dir
					));
					// Get the records
					$this->view->rows  = $obj->getRecords($query->query, $this->view->filters);
				}
			}
		}

		$watching = new SupportTableWatching($this->database);
		$this->view->watchcount = $watching->count(array(
			'user_id'   => $this->juser->get('id')
		));
		if ($this->view->filters['show'] == -1)
		{
			$records = $watching->find(array(
				'user_id'   => $this->juser->get('id')
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

		// Initiate paging class
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		$this->view->acl = $this->acl;

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

	/**
	 * Displays a form for creating a new support ticket
	 *
	 * @return     void
	 */
	public function newTask()
	{
		// Get browser info
		$browser = new \Hubzero\Browser\Detector();

		$problem = array(
			'os'         => $browser->platform(),
			'osver'      => $browser->platformVersion(),
			'browser'    => $browser->name(),
			'browserver' => $browser->version(),
			'topic'      => '',
			'short'      => '',
			'long'       => '',
			'referer'    => base64_encode(JRequest::getVar('HTTP_REFERER', NULL, 'server')),
			'tool'       => JRequest::getVar('tool', '')
		);

		// Generate a CAPTCHA
		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();
		$this->view->captchas = $dispatcher->trigger('onGetComponentCaptcha');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->verified   = $this->_isVerified();
		if ($this->view->verified && $this->acl->check('update', 'tickets') > 0)
		{
			$this->view->lists = array();
			if (trim($this->config->get('group')))
			{
				$this->view->lists['owner'] = $this->_userSelectGroup(
					'problem[owner]',
					'',
					1,
					'',
					trim($this->config->get('group'))
				);
			}
			else
			{
				$this->view->lists['owner'] = $this->_userSelect(
					'problem[owner]',
					'',
					1
				);
			}
			$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));
			// Get resolutions
			$sr = new SupportResolution($this->database);
			$this->view->lists['resolutions'] = $sr->getResolutions();

			$sc = new SupportCategory($this->database);
			$this->view->lists['categories'] = $sc->find('list');
		}
		$this->view->acl        = $this->acl;
		$this->view->title      = $this->_title;
		$this->view->reporter   = $this->_getUser();
		$this->view->problem    = $problem;
		$this->view->file_types = $this->config->get('file_ext');
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Checks if the current user session has a verified account
	 *
	 * @return     boolean True if user is verified
	 */
	private function _isVerified()
	{
		if (!$this->juser->get('guest'))
		{
			$profile = new \Hubzero\User\Profile();
			$profile->load($this->juser->get('id'));
			$emailConfirmed = $profile->get('emailConfirmed');
			if (($emailConfirmed == 1) || ($emailConfirmed == 3))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets some basic info of the current user session
	 *
	 * @return     array
	 */
	private function _getUser()
	{
		$user = array();
		$user['login'] = '';
		$user['name']  = '';
		$user['org']   = '';
		$user['email'] = '';
		$user['uid']   = '';

		if (!$this->juser->get('guest'))
		{
			$profile = new \Hubzero\User\Profile();
			$profile->load($this->juser->get('id'));

			if (is_object($profile))
			{
				$user['login'] = $profile->get('username');
				$user['name']  = $profile->get('name');
				$user['org']   = $profile->get('organization');
				$user['email'] = $profile->get('email');
				$user['uid']   = $profile->get('uidNumber');
			}
		}
		return $user;
	}

	/**
	 * Saves a trouble report as a ticket
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$live_site = rtrim(JURI::base(), '/');

		// Get plugins
		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onPreTicketSubmission', array());

		// Incoming
		$no_html  = JRequest::getInt('no_html', 0);
		$verified = JRequest::getInt('verified', 0);
		if (!isset($_POST['reporter']) || !isset($_POST['problem']))
		{
			// This really, REALLY shouldn't happen.
			JError::raiseError(400, JText::_('COM_SUPPORT_ERROR_MISSING_DATA'));
			return;
		}
		$reporter = JRequest::getVar('reporter', array(), 'post', 'none', 2);
		$problem  = JRequest::getVar('problem', array(), 'post', 'none', 2);
		//$reporter = array_map('trim', $_POST['reporter']);
		//$problem  = array_map('trim', $_POST['problem']);

		// Normally calling JRequest::getVar calls _cleanVar, but b/c of the way this page processes the posts
		// (with array square brackets in the html names) against the $_POST collection, we explicitly
		// call the clean_var function on these arrays after fetching them
		//$reporter = array_map(array('JRequest', '_cleanVar'), $reporter);
		//$problem  = array_map(array('JRequest', '_cleanVar'), $problem);

		// Reporter login can only be for authenticated users -- ignore any form submitted login names
		$reporterLogin = $this->_getUser();
		$reporter['login'] = $reporterLogin['login'];

		// Probably redundant after the change to call JRequest::_cleanVar change above, It is a bit hard to
		// tell if the Joomla  _cleanvar function does enough to allow us to remove the purifyText call
		$reporter = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $reporter);
		//$problem  = array_map(array('\\Hubzero\\Utility\\Sanitize', 'stripAll'), $problem);

		$reporter['name']  = trim($reporter['name']);
		$reporter['email'] = trim($reporter['email']);
		$problem['long']   = trim($problem['long']);

		// Make sure email address is valid
		$validemail = \Hubzero\Utility\Validate::email($reporter['email']);

		// Set page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Trigger any events that need to be called
		$customValidation = true;
		$result = $dispatcher->trigger('onValidateTicketSubmission', array($reporter, $problem));
		$customValidation = (is_array($result) && !empty($result)) ? $result[0] : $customValidation;

		// Check for some required fields
		if (!$reporter['name']
		 || !$reporter['email']
		 || !$validemail
		 || !$problem['long']
		 || !$customValidation)
		{
			JRequest::setVar('task', 'new');
			// Output form with error messages
			$this->view->setLayout('new');
			$this->view->task       = 'new';
			$this->view->acl        = $this->acl;
			$this->view->reporter   = $reporter;
			$this->view->problem    = $problem;
			$this->view->verified   = $verified;


			if ($this->view->verified && $this->acl->check('update', 'tickets') > 0)
			{
				$this->view->lists = array();
				if (trim($this->config->get('group')))
				{
					$this->view->lists['owner'] = $this->_userSelectGroup(
						'problem[owner]',
						$problem['owner'],
						1,
						'',
						trim($this->config->get('group'))
					);
				}
				else
				{
					$this->view->lists['owner'] = $this->_userSelect(
						'problem[owner]',
						$problem['owner'],
						1
					);
				}
				$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));
				// Get resolutions
				$sr = new SupportResolution($this->database);
				$this->view->lists['resolutions'] = $sr->getResolutions();
			}

			$this->view->captchas   = $dispatcher->trigger('onGetComponentCaptcha');
			$this->view->file_types = $this->config->get('file_ext');
			$this->view->setError(2);
			$this->view->display();
			return;
		}

		// Get the user's IP
		$ip = JRequest::ip();
		$hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

		if (!$verified)
		{
			// Check CAPTCHA
			$validcaptchas = $dispatcher->trigger('onValidateCaptcha');
			if (count($validcaptchas) > 0)
			{
				foreach ($validcaptchas as $validcaptcha)
				{
					if (!$validcaptcha)
					{
						$this->setError(JText::_('COM_SUPPORT_ERROR_INVALID_CAPTCHA'));
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
				$this->setError(JText::_('COM_SUPPORT_ERROR_FLAGGED_AS_SPAM'));
				return;
			}
			// Quick bot check
			$botcheck = JRequest::getVar('botcheck', '');
			if ($botcheck)
			{
				$this->setError(JText::_('COM_SUPPORT_ERROR_INVALID_BOTCHECK'));
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
				JRequest::setVar('task', 'new');
				// Output form with error messages
				$this->view->setLayout('new');
				$this->view->task       = 'new';
				$this->view->reporter   = $reporter;
				$this->view->problem    = $problem;
				$this->view->verified   = $verified;
				$this->view->captchas   = $dispatcher->trigger('onGetComponentCaptcha');
				$this->view->file_types = $this->config->get('file_ext');
				$this->view->setError(3);
				$this->view->display();
				return;
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

		$group = JRequest::getVar('group', '');

		// Initiate class and bind data to database fields
		$row = new SupportModelTicket();
		$row->set('open', 1);
		$row->set('status', 0);
		$row->set('created', JFactory::getDate()->toSql());
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
		$row->set('uas', JRequest::getVar('HTTP_USER_AGENT', '', 'server'));
		$row->set('referrer', base64_decode($problem['referer']));
		$row->set('cookies', (JRequest::getVar('sessioncookie', '', 'cookie') ? 1 : 0));
		$row->set('instances', 1);
		$row->set('section', 1);
		$row->set('group', $group);

		// Save the data
		if (!$row->store())
		{
			$this->setError($row->getError());
		}

		$attachment = $this->uploadTask($row->get('id'));

		// Save tags
		$row->set('tags', JRequest::getVar('tags', '', 'post'));
		$row->tag($row->get('tags'), $this->juser->get('id'), 1);

		// Get some email settings
		$jconfig = JFactory::getConfig();

		// Get any set emails that should be notified of ticket submission
		$defs = str_replace("\r", '', $this->config->get('emails', '{config.mailfrom}'));
		$defs = explode("\n", $defs);

		if ($defs)
		{
			$message = new \Hubzero\Mail\Message();
			$message->setSubject($jconfig->getValue('config.sitename') . ' ' . JText::sprintf('COM_SUPPORT_EMAIL_SUBJECT_NEW_TICKET', $row->get('id')));
			$message->addFrom(
				$jconfig->getValue('config.mailfrom'),
				$jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_option))
			);

			// Plain text email
			$eview = new \Hubzero\Component\View(array(
				'name'   => 'emails',
				'layout' => 'ticket_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->ticket     = $row;
			$eview->delimiter  = '';

			$plain = $eview->loadTemplate();
			$plain = str_replace("\n", "\r\n", $plain);

			$message->addPart($plain, 'text/plain');

			// HTML email
			$eview->setLayout('ticket_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

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

			$message->addPart($html, 'text/html');

			// Loop through the addresses
			foreach ($defs As $def)
			{
				$def = trim($def);

				// Check if the address should come from Joomla config
				if ($def == '{config.mailfrom}')
				{
					$def = $jconfig->getValue('config.mailfrom');
				}
				// Check for a valid address
				if (\Hubzero\Utility\Validate::email($def))
				{
					// Send e-mail
					$message->setTo(array($def));
					$message->send();
				}
			}
		}

		if (!$this->juser->get('guest') && $this->acl->check('update', 'tickets') > 0)
		{
			// Only do the following if a comment was posted
			// otherwise, we're only recording a changelog
			$old = new SupportModelTicket();
			$old->set('open', 1);
			$old->set('tags', '');

			$rowc = new SupportModelComment();
			$rowc->set('ticket', $row->get('id'));
			$rowc->set('created', JFactory::getDate()->toSql());
			$rowc->set('created_by', $this->juser->get('id'));
			$rowc->set('access', 1);
			$rowc->set('comment', JText::_('COM_SUPPORT_TICKET_SUBMITTED'));

			// Compare fields to find out what has changed for this ticket and build a changelog
			$rowc->changelog()->diff($old, $row);

			$rowc->changelog()->cced(JRequest::getVar('cc', ''));

			// Save the data
			if (!$rowc->store())
			{
				JError::raiseError(500, $rowc->getError());
				return;
			}

			if ($row->get('owner'))
			{
				$rowc->addTo(array(
					'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
					'name'  => $row->owner('name'),
					'email' => $row->owner('email'),
					'id'    => $row->owner('id')
				));
			}

			// Add any CCs to the e-mail list
			foreach ($rowc->changelog()->get('cc') as $cc)
			{
				$rowc->addTo($cc, JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
			}

			// Check if the notify list has eny entries
			if (count($rowc->to()))
			{
				$allowEmailResponses = $this->config->get('email_processing');
				if (!file_exists("/etc/hubmail_gw.conf"))
				{
					$allowEmailResponses = false;
				}

				if ($allowEmailResponses)
				{
					$encryptor = new \Hubzero\Mail\Token();
				}

				$subject = JText::sprintf('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

				$from = array(
					'name'      => JText::sprintf('COM_SUPPORT_EMAIL_FROM', $jconfig->getValue('config.sitename')),
					'email'     => $jconfig->getValue('config.mailfrom'),
					'multipart' => md5(date('U'))
				);

				$message = array();

				// Plain text email
				$eview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'comment_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->comment    = $rowc;
				$eview->ticket     = $row;
				$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

				$message['plaintext'] = $eview->loadTemplate();
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('comment_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				// Send e-mail to admin?
				JPluginHelper::importPlugin('xmessage');
				$dispatcher = JDispatcher::getInstance();

				foreach ($rowc->to('ids') as $to)
				{
					if ($allowEmailResponses)
					{
						// The reply-to address contains the token
						$token = $encryptor->buildEmailToken(1, 1, $to['id'], $row->get('id'));
						$from['replytoemail'] = 'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@');
					}

					// Get the user's email address
					if (!$dispatcher->trigger('onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
					{
						$this->setError(JText::sprintf('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
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
							'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@')
						);

						// In this case each item in email in an array, 1- To, 2:reply to address
						SupportUtilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
					}
					else
					{
						// email is just a plain 'ol string
						SupportUtilities::sendEmail($to['email'], $subject, $message, $from);
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

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onTicketSubmission', array($row));

		// Output Thank You message
		$this->view->ticket  = $row->get('id');
		$this->view->no_html = $no_html;
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
	 * @return     void
	 */
	public function ticketTask()
	{
		// Get the ticket ID
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->controller . '&task=tickets'),
				JText::_('COM_SUPPORT_ERROR_MISSING_TICKET_ID'),
				'error'
			);
			return;
		}

		// Initiate database class and load info
		$this->view->row = SupportModelTicket::getInstance($id);
		if (!$this->view->row->exists())
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_TICKET_NOT_FOUND'));
			return;
		}

		// Check authorization
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRoute::_($this->view->row->link(), false, true));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Incoming
		$config = JFactory::getConfig();
		$app    = JFactory::getApplication();

		$this->view->filters = array();
		// Paging
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// Query to filter by
		$this->view->filters['show'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.show',
			'show',
			0,
			'int'
		);
		// Search
		$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		// Ensure the user is authorized to view this ticket
		if (!$this->view->row->access('read', 'tickets'))
		{
			JError::raiseError(403, JText::_('COM_SUPPORT_ERROR_NOT_AUTH'));
			return;
		}

		if ($watch = JRequest::getWord('watch', ''))
		{
			// Already watching
			if ($this->view->row->isWatching($this->juser))
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$this->view->row->stopWatching($this->juser);
				}
			}
			// Not already watching
			else
			{
				// Start watching?
				if ($watch == 'start')
				{
					$this->view->row->watch($this->juser);
					if (!$this->view->row->isWatching($this->juser, true))
					{
						$this->setError(JText::_('COM_SUPPORT_ERROR_FAILED_TO_WATCH'));
					}
				}
			}
		}

		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		$sc = new SupportCategory($this->database);
		$this->view->lists['categories'] = $sc->find('list');

		// Get messages
		$sm = new SupportMessage($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get severities
		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

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

		if ($this->getComponentMessage())
		{
			foreach ($this->getComponentMessage() as $error)
			{
				if ($error['type'] == 'error')
				{
					$this->view->setError($error['message']);
				}
			}
		}

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

	/**
	 * Updates a ticket with any changes and adds a new comment
	 *
	 * @return     void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Make sure we are still logged in
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('id', 0, 'post');
		if (!$id)
		{
			JError::raiseError(500, JText::_('COM_SUPPORT_ERROR_MISSING_TICKET_ID'));
			return;
		}

		$incoming = JRequest::getVar('ticket', array(), 'post');
		$incoming = array_map('trim', $incoming);

		// Load the old ticket so we can compare for the changelog
		$old = new SupportModelTicket($id);
		$old->set('tags', $old->tags('string'));

		// Initiate class and bind posted items to database fields
		$row = new SupportModelTicket($id);
		if (!$row->bind($incoming))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if ($id && isset($incoming['status']) && $incoming['status'] == 0)
		{
			$row->set('open', 0);
			$row->set('resolved', JText::_('COM_SUPPORT_COMMENT_OPT_CLOSED'));
		}

		// Check content
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// If an existing ticket AND closed AND previously open
		if ($id && !$row->get('open') && $row->get('open') != $old->get('open'))
		{
			// Record the closing time
			$row->set('closed', JFactory::getDate()->toSql());
		}

		// Incoming comment
		$comment = JRequest::getVar('comment', '', 'post', 'none', 2);
		if ($comment)
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($row->isWaiting() && $this->juser->get('username') == $row->get('login'))
			{
				$row->open();
			}
		}

		// Store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Save the tags
		$row->tag(JRequest::getVar('tags', '', 'post'), $this->juser->get('id'), 1);
		$row->set('tags', $row->tags('string'));

		// Create a new support comment object and populate it
		$access = JRequest::getInt('access', 0);

		$rowc = new SupportModelComment();
		$rowc->set('ticket', $id);
		$rowc->set('comment', nl2br($comment));
		$rowc->set('created', JFactory::getDate()->toSql());
		$rowc->set('created_by', $this->juser->get('id'));
		$rowc->set('access', $access);

		// Compare fields to find out what has changed for this ticket and build a changelog
		$rowc->changelog()->diff($old, $row);

		$rowc->changelog()->cced(JRequest::getVar('cc', ''));

		// Save the data
		if (!$rowc->store())
		{
			JError::raiseError(500, $rowc->getError());
			return;
		}

		JPluginHelper::importPlugin('support');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onTicketUpdate', array($row, $rowc));

		$attach = new SupportAttachment($this->database);
		if ($tmp = JRequest::getInt('tmp_dir'))
		{
			$attach->updateCommentId($tmp, $rowc->get('id'));
		}

		$attachment = $this->uploadTask($row->get('id'), $rowc->get('id'));

		// Only do the following if a comment was posted
		// otherwise, we're only recording a changelog
		if ($rowc->get('comment') || $row->get('owner') != $old->get('owner') || $rowc->attachments()->total() > 0)
		{
			// Send e-mail to ticket submitter?
			if (JRequest::getInt('email_submitter', 0) == 1)
			{
				// Is the comment private? If so, we do NOT send e-mail to the
				// submitter regardless of the above setting
				if (!$rowc->isPrivate())
				{
					$rowc->addTo(array(
						'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
						'name'  => $row->submitter('name'),
						'email' => $row->submitter('email'),
						'id'    => $row->submitter('id')
					));
				}
			}

			// Send e-mail to ticket owner?
			if (JRequest::getInt('email_owner', 0) == 1)
			{
				if ($row->get('owner'))
				{
					$rowc->addTo(array(
						'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
						'name'  => $row->owner('name'),
						'email' => $row->owner('email'),
						'id'    => $row->owner('id')
					));
				}
			}

			// Add any CCs to the e-mail list
			foreach ($rowc->changelog()->get('cc') as $cc)
			{
				$rowc->addTo($cc, JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
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
			$this->acl->setUser($this->juser->get('id'));

			if (count($rowc->to()))
			{
				$allowEmailResponses = $this->config->get('email_processing');
				if (!file_exists("/etc/hubmail_gw.conf"))
				{
					$allowEmailResponses = false;
				}
				if ($allowEmailResponses)
				{
					$encryptor = new \Hubzero\Mail\Token();
				}

				$jconfig = JFactory::getConfig();

				// Build e-mail components
				$subject = JText::sprintf('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $row->get('id'));

				$from = array(
					'name'      => JText::sprintf('COM_SUPPORT_EMAIL_FROM', $jconfig->getValue('config.sitename')),
					'email'     => $jconfig->getValue('config.mailfrom'),
					'multipart' => md5(date('U'))  // Html email
				);

				$message = array();

				// Plain text email
				$eview = new \Hubzero\Component\View(array(
					'name'   => 'emails',
					'layout' => 'comment_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->comment    = $rowc;
				$eview->ticket     = $row;
				$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

				$message['plaintext'] = $eview->loadTemplate();
				$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('comment_html');

				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				$message['attachments'] = array();
				foreach ($rowc->attachments() as $attachment)
				{
					if ($attachment->size() < 2097152)
					{
						$message['attachments'][] = $attachment->link('filepath');
					}
				}

				JPluginHelper::importPlugin('xmessage');

				foreach ($rowc->to('ids') as $to)
				{
					if ($allowEmailResponses)
					{
						// The reply-to address contains the token
						$token = $encryptor->buildEmailToken(1, 1, $to['id'], $id);
						$from['replytoemail'] = 'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@');
					}

					// Get the user's email address
					if (!$dispatcher->trigger('onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $this->_option)))
					{
						$this->setError(JText::sprintf('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
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
							'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@')
						);

						// In this case each item in email in an array, 1- To, 2:reply to address
						SupportUtilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
					}
					else
					{
						// email is just a plain 'ol string
						SupportUtilities::sendEmail($to['email'], $subject, $message, $from);
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
					JError::raiseError(500, $rowc->getError());
					return;
				}
			}
		}

		// Display the ticket with changes, new comment
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $id),
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
		$id = JRequest::getInt('id', 0);

		// Check for an ID
		if (!$id)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets')
			);
			return;
		}

		// Delete tags
		$tags = new SupportTags($this->database);
		$tags->remove_all_tags($id);

		// Delete comments
		$comment = new SupportComment($this->database);
		$comment->deleteComments($id);

		$attach = new SupportAttachment($this->database);
		if (!$attach->deleteAllForTicket($id))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets'),
				$attach->getError(),
				'error'
			);
			return;
		}

		// Delete ticket
		$ticket = new SupportTicket($this->database);
		$ticket->delete($id);

		// Output messsage and redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets')
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
		$row = new SupportModelTicket();
		if (!$row->bind($incoming))
		{
			echo $row->getError();
			return;
		}
		$row->set('summary', $row->content('clean', 200));

		// Check for a session token
		$sessnum = '';
		if ($sess = JRequest::getVar('sesstoken', ''))
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
			$mwdb = MwUtils::getMWDBO();

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
				$juser = JUser::getInstance($row->get('login'));
				$row->set('name', $juser->get('name'));
				$row->set('email', $juser->get('email'));
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
			$oldticket = new SupportModelTicket($ticket);
			$oldticket->set('instances', ($oldticket->get('instances') + 1));
			if (!$oldticket->isOpen())
			{
				$before = new SupportModelTicket($ticket);

				$oldticket->set('open', 1);
				$oldticket->set('status', 1);
				$oldticket->set('resolved', '');

				$rowc = new SupportModelComment();
				$rowc->set('ticket', $ticket);
				$rowc->set('comment', '');
				$rowc->set('created', JFactory::getDate()->toSql());
				$rowc->set('created_by', $this->juser->get('id'));
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
			$row->set('created', JFactory::getDate()->toSql());
			$row->set('severity', ($row->get('severity') ? $row->get('severity') : 'normal'));
			$row->set('category', ($row->get('category') ? $row->get('category') : JText::_('COM_SUPPORT_CATEGORY_TOOLS')));
			$row->set('resolved', '');
			$row->set('email', ($row->get('email') ? $row->get('email') : $this->_data['supportemail']));
			$row->set('name', ($row->get('name') ? $row->get('name') : JText::_('COM_SUPPORT_AUTOMATED_REPORT')));
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

			$row->tag($incoming['tags'], $this->juser->get('id'), 1);

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
	 * @return	void
	 */
	public function downloadTask()
	{
		// Check logged in status
		if ($this->juser->get('guest'))
		{
			$return = base64_encode(JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true), 'server'));
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}

		// Get the ID of the file requested
		$id = JRequest::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new SupportAttachment($this->database);
		$attach->load($id);
		if (!$attach->filename)
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$row = new SupportTicket($this->database);
		$row->load($attach->ticket);

		if (!$row->report)
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_TICKET_NOT_FOUND'));
			return;
		}

		// Load ACL
		if ($row->login == $this->juser->get('username')
		 || $row->owner == $this->juser->get('id'))
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
			JError::raiseError(403, JText::_('COM_SUPPORT_ERROR_NOT_AUTH'));
			return;
		}

		// Ensure we have a path
		if (empty($file))
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND'));
			return;
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

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $file;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_SUPPORT_ERROR_SERVING_FILE'));
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
	 * @param      string $listdir Directory to upload files to
	 * @return     string A string that gets appended to messages
	 */
	public function uploadTask($listdir, $comment_id=0)
	{
		// Check if they are logged in
		/*if ($this->juser->get('guest'))
		{
			return '';
		}*/

		if (!$listdir)
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_MISSING_UPLOAD_DIRECTORY'));
			return '';
		}

		// Construct our file path
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $listdir;

		$row = new SupportAttachment($this->database);

		// Rename temp directories
		if ($tmp = JRequest::getInt('tmp_dir'))
		{
			$tmpPath = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $tmp;
			if (is_dir($tmpPath))
			{
				if (!JFolder::move($tmpPath, $path))
				{
					$this->setError(JText::_('COM_SUPPORT_ERROR_UNABLE_TO_MOVE_UPLOAD_PATH'));
					JError::raiseError(500, JText::_('COM_SUPPORT_ERROR_UNABLE_TO_MOVE_UPLOAD_PATH'));
					return '';
				}
				$row->updateTicketId($tmp, $listdir);
			}
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!isset($file['name']) || !$file['name'])
		{
			//$this->setError(JText::_('SUPPORT_NO_FILE'));
			return '';
		}

		// Incoming
		$description = JRequest::getVar('description', '');

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_SUPPORT_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		//make sure that file is acceptable type
		if (!in_array($ext, explode(',', $this->config->get('file_ext'))))
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
			return JText::_('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE');
		}

		$filename = JFile::stripExt($file['name']);
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$finalfile = $path . DS . $filename . '.' . $ext;

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $finalfile))
		{
			$this->setError(JText::_('COM_SUPPORT_ERROR_UPLOADING'));
			return '';
		}
		else
		{
			// Scan for viruses
			//$path = $path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $finalfile", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($finalfile))
				{
					$this->setError(JText::_('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN'));
					return JText::_('COM_SUPPORT_ERROR_FAILED_VIRUS_SCAN');
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
	 * @return     array An array of filters to apply
	 */
	private function _getFilters()
	{
		// Query filters defaults
		$filters = array();
		$filters['search']     = '';
		$filters['status']     = 'open';
		$filters['type']       = 0;
		$filters['owner']      = '';
		$filters['reportedby'] = '';
		$filters['severity']   = 'normal';
		$filters['sort']       = trim(JRequest::getVar('filter_order', 'created'));
		$filters['sortdir']    = trim(JRequest::getVar('filter_order_Dir', 'DESC'));
		$filters['severity']   = '';

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging vars
		$filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);

		// Incoming
		$filters['_find'] = urldecode(trim(JRequest::getVar('find', '', 'post')));
		$filters['_show'] = urldecode(trim(JRequest::getVar('show', '', 'post')));

		if ($filters['_find'] != '' || $filters['_show'] != '')
		{
			$filters['start'] = 0;
		}
		else
		{
			$filters['_find'] = urldecode(trim(JRequest::getVar('find', '', 'get')));
			$filters['_show'] = urldecode(trim(JRequest::getVar('show', '', 'get')));
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
						$pieces[1] = $this->juser->get('id');
					}
				break;
				case 'reportedby':
					if (isset($pieces[1]) && $pieces[1] == 'me')
					{
						$pieces[1] = $this->juser->get('username');
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
			$users[] = JHTML::_('select.option', '0', JText::_('COM_SUPPORT_NONE'), 'value', 'text');
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
				$users[] = JHTML::_('select.optgroup', JText::_('COM_SUPPORT_CHANGELOG_FIELD_GROUP') . ': ' . $gname);
				$users = array_merge($users, $gusers);
			}
		}

		ksort($users);

		$users = JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

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

						$users[] = JHTML::_('select.optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u = JUser::getInstance($member);
							if (!is_object($u))
							{
								continue;
							}

							$m = new stdClass();
							$m->value = $u->get('id');
							$m->text  = $u->get('name');
							$m->groupname = $g;

							$users[] = $m;
						}
						$users[] = JHTML::_('select.option', '</OPTGROUP>');
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
					$u = JUser::getInstance($member);
					if (!is_object($u))
					{
						continue;
					}

					$m = new stdClass();
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
			array_unshift($users, JHTML::_('select.option', '0', JText::_('COM_SUPPORT_NONE'), 'value', 'text'));
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false);

		return $users;
	}
}
