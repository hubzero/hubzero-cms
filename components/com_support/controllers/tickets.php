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

ximport('Hubzero_Controller');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');
include_once(JPATH_ROOT . DS . 'libraries' . DS . 'Hubzero' . DS . 'EmailToken.php');

/**
 * Manage support tickets
 */
class SupportControllerTickets extends Hubzero_Controller
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
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
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
				JText::_(strtoupper($task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $task
			);
			if ($this->_task == 'new') 
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
		if (is_object($ticket) && $ticket->id) 
		{
			$pathway->addItem(
				'#' . $ticket->id,
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id='.$ticket->id
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
		$this->_title = JText::_(strtoupper($this->_name));
		if ($this->_task) 
		{
			if ($this->_task == 'new' || $this->_task == 'display') 
			{
				$this->_title .= ': ' . JText::_(strtoupper('tickets'));
			}
			if ($this->_task != 'display') 
			{
				$this->_title .= ': ' . JText::_(strtoupper($this->_task));
			}
		}
		if (is_object($ticket) && $ticket->id) 
		{
			$this->_title .= ' #' . $ticket->id;
		}
		$document =& JFactory::getDocument();
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
				JRoute::_('index.php?option=com_user&view=login&return=' . $return)
			);
			return;
		}

		//$this->view->authorized = $this->_authorize();

		if (!$this->acl->check('read','tickets')) 
		{
			$this->_return = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=tickets');
		}

		// Set the page title
		$this->_buildTitle();

		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

		$type = JRequest::getVar('type', 'submitted');
		$this->view->type = ($type == 'automatic') ? 1 : 0;

		$this->view->group = JRequest::getVar('group', '_none_');

		//$this->view->sort = JRequest::getVar('sort', 'name');

		// Set up some dates
		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		$year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));

		/*$day   = strftime("%d", time()+($this->offset*60*60));
		if ($day <= "9"&preg_match("#(^[1-9]{1})#",$day)) 
		{
			$day = "0$day";
		}
		if ($month <= "9"&preg_match("#(^[1-9]{1})#",$month)) 
		{
			$month = "0$month";
		}

		$startday = 0;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1) 
		{
			$numday = 6;
		}
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year);
		$week = strftime("%d", $week_start);*/

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
			/*$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";*/
			$query = "SELECT DISTINCT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.username"	// map user to aro
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
				. "\n INNER JOIN #__support_tickets AS s ON s.owner = a.username"	// map user to aro
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
				$users[$user->username] = $user;
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
		$sql = "SELECT c.ticket, c.created_by, c.created, YEAR(c.created) AS `year`, MONTH(c.created) AS `month`, UNIX_TIMESTAMP(t.created) AS opened, UNIX_TIMESTAMP(c.created) AS closed
				FROM #__support_comments AS c 
				LEFT JOIN #__support_tickets AS t ON c.ticket=t.id
				WHERE t.report!=''
				AND type=" . $this->view->type . " AND open=0";
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
			$sql .= " AND c.created>='" . $this->view->start . "-01 00:00:00' AND c.created<'" . $end . "-01 00:00:00'";
		}
		$sql .= " ORDER BY c.created ASC";
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
			if (isset($owners[$user->username]))
			{
				$user->assigned = $owners[$user->username];
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
				JRoute::_('index.php?option=com_user&view=login&return=' . $return)
			);
			return;
		}

		$this->view->database = $this->database;

		// Create a Ticket object
		$obj = new SupportTicket($this->database);

		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

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
				if (!$query->query)
				{
					$query->query = $sq->getQuery($query->conditions);
				}
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

		// Get some needed styles
		$this->_getStyles();

		// Get some needed scripts
		$this->_getScripts('assets/js/' . $this->_name);
		Hubzero_Document::addSystemScript('jquery.hoverIntent');

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
		ximport('Hubzero_Browser');
		$browser = new Hubzero_Browser();

		$problem = array(
			'os'         => $browser->getOs(),
			'osver'      => $browser->getOsVersion(),
			'browser'    => $browser->getBrowser(),
			'browserver' => $browser->getBrowserVersion(),
			'topic'      => '',
			'short'      => '',
			'long'       => '',
			'referer'    => JRequest::getVar('HTTP_REFERER', NULL, 'server'),
			'tool'       => JRequest::getVar('tool', '')
		);

		// Generate a CAPTCHA
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();
		$this->view->captchas = $dispatcher->trigger('onGetComponentCaptcha');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

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
			ximport('Hubzero_User_Profile');
			$profile = new Hubzero_User_Profile();
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
			ximport('Hubzero_User_Profile');

			$profile = new Hubzero_User_Profile();
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
		$live_site = rtrim(JURI::base(), '/');

		// Get plugins
		JPluginHelper::importPlugin('support');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger any events that need to be called before session stop
		$dispatcher->trigger('onPreTicketSubmission', array());

		// Incoming
		$no_html  = JRequest::getInt('no_html', 0);
		$verified = JRequest::getInt('verified', 0);
		if (!isset($_POST['reporter']) || !isset($_POST['problem']))
		{
			// This really, REALLY shouldn't happen.
			JError::raiseError(500, JText::_('No data submitted'));
			return;
		}
		$reporter = JRequest::getVar('reporter', array(), 'post', 'none', 2);
		$problem = JRequest::getVar('problem', array(), 'post', 'none', 2);
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
		ximport('Hubzero_View_Helper_Html');
		$reporter = array_map(array('Hubzero_View_Helper_Html', 'purifyText'), $reporter);
		//$problem  = array_map(array('Hubzero_View_Helper_Html','purifyText'), $problem);

		// Make sure email address is valid
		$validemail = $this->_isValidEmail($reporter['email']);

		// Set page title
		$this->_buildTitle();
		
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();

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
		ximport('Hubzero_Environment');
		$ip = Hubzero_Environment::ipAddress();
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
						$this->setError(JText::_('Error: Invalid CAPTCHA response.'));
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
				$this->setError(JText::_('Error: Message flagged as spam.'));
				return;
			}
			// Quick bot check
			$botcheck = JRequest::getVar('botcheck', '');
			if ($botcheck) 
			{
				$this->setError(JText::_('Error: Invalid botcheck response.'));
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

		// Get user's city, region and location based on ip
		$source_city    = 'unknown';
		$source_region  = 'unknown';
		$source_country = 'unknown';

		ximport('Hubzero_Geo');
		$gdb =& Hubzero_Geo::getGeoDBO();
		if (is_object($gdb)) 
		{
			$gdb->setQuery("SELECT countrySHORT, countryLONG, ipREGION, ipCITY FROM ipcitylatlong WHERE INET_ATON('$ip') BETWEEN ipFROM and ipTO");
			$rows = $gdb->loadObjectList();
			if ($rows && count($rows) > 0) 
			{
				$source_city    = $rows[0]->ipCITY;
				$source_region  = $rows[0]->ipREGION;
				$source_country = $rows[0]->countryLONG;
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

		$tool = $this->_getTool($problem['referer']);
		$groupID = JRequest::getVar('group', '');
		if ($groupID) 
		{
			$group = $groupID;
		}
		elseif ($tool) 
		{
			$group = $this->_getTicketGroup(trim($tool));
		} 
		else 
		{
			$group = '';
		}

		// Build an array of ticket data
		$data = array();
		$data['id']        = NULL;
		$data['status']    = 0;
		$data['created']   = date("Y-m-d H:i:s");
		$data['login']     = $reporter['login'];
		$data['severity']  = (isset($problem['severity'])) ? $problem['severity'] : 'normal';
		$data['owner']     = (isset($problem['owner'])) ? $problem['owner'] : null;
		$data['category']  = (isset($problem['topic'])) ? $problem['topic'] : '';
		//$data['summary']   = htmlentities($problem['short'], ENT_COMPAT, 'UTF-8');
		//$data['report']    = htmlentities($problem['long'], ENT_COMPAT, 'UTF-8');
		$data['summary']   = $problem['short'];
		$data['report']    = $problem['long'];
		$data['resolved']  = (isset($problem['resolved'])) ? $problem['resolved'] : null;
		$data['email']     = $reporter['email'];
		$data['name']      = $reporter['name'];
		$data['os']        = $problem['os'] . ' ' . $problem['osver'];
		$data['browser']   = $problem['browser'] . ' ' . $problem['browserver'];
		$data['ip']        = $ip;
		$data['hostname']  = $hostname;
		$data['uas']       = JRequest::getVar('HTTP_USER_AGENT', '', 'server');
		$data['referrer']  = $problem['referer'];
		$data['cookies']   = (JRequest::getVar('sessioncookie', '', 'cookie')) ? 1 : 0;
		$data['instances'] = 1;
		$data['section']   = 1;
		$data['group']     = $group;

		// Initiate class and bind data to database fields
		$row = new SupportTicket($this->database);
		if (!$row->bind($data)) 
		{
			$this->setError($row->getError());
		}
		// Check the data
		if (!$row->check()) 
		{
			$this->setError($row->getError());
		}
		// Save the data
		if (!$row->store()) 
		{
			$this->setError($row->getError());
		}
		// Retrieve the ticket ID
		if (!$row->id) 
		{
			$row->getId();
		}

		$attachment = $this->uploadTask($row->id);
		$row->report .= ($attachment) ? "\n\n" . $attachment : '';
		$problem['long'] .= ($attachment) ? "\n\n" . $attachment : '';

		$tags = trim(JRequest::getVar('tags', '', 'post'));
		if ($tags)
		{
			$st = new SupportTags($this->database);
			$st->tag_object($this->juser->get('id'), $row->id, $tags, 0, true);
		}

		// Save the data
		if (!$row->store()) 
		{
			$this->setError($row->getError());
		}

		// Get some email settings
		$jconfig =& JFactory::getConfig();
		//$admin   = $jconfig->getValue('config.mailfrom');
		$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT_SUPPORT') . ', ' . JText::sprintf('COM_SUPPORT_TICKET_NUMBER', $row->id);
		
		//$from    = $jconfig->getValue('config.sitename') . ' web-robot';
		//$hub     = array('email' => $reporter['email'], 'name' => $from);
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Parse comments for attachments
		$attach = new SupportAttachment($this->database);
		$attach->webpath = $live_site . $this->config->get('webpath') . DS . $row->id;
		$attach->uppath  = JPATH_ROOT . $this->config->get('webpath') . DS . $row->id;
		$attach->output  = 'email';

		// Generate e-mail message
		$message = array();
		$message['plaintext']  = (!$this->juser->get('guest')) ? JText::_('COM_SUPPORT_VERIFIED_USER')."\r\n\r\n" : '';
		$message['plaintext'] .= ($reporter['login']) ? JText::_('COM_SUPPORT_USERNAME').': '. $reporter['login'] ."\r\n" : '';
		$message['plaintext'] .= JText::_('COM_SUPPORT_NAME').': '. $reporter['name'] ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_AFFILIATION').': '. $reporter['org'] ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_EMAIL').': '. $reporter['email'] ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_IP_HOSTNAME').': '. $ip .' ('.$hostname.')' ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_REGION').': '.$source_city.', '.$source_region.', '.$source_country ."\r\n\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_OS').': '. $problem['os'] .' '. $problem['osver'] ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_BROWSER').': '. $problem['browser'] .' '. $problem['browserver'] ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_UAS').': '. JRequest::getVar('HTTP_USER_AGENT','','server') ."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_COOKIES').': ';
		$message['plaintext'] .= (JRequest::getVar('sessioncookie','','cookie')) ? JText::_('COM_SUPPORT_COOKIES_ENABLED')."\r\n" : JText::_('COM_SUPPORT_COOKIES_DISABLED')."\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_REFERRER').': '. $problem['referer'] ."\r\n";
		$message['plaintext'] .= ($problem['tool']) ? JText::_('COM_SUPPORT_TOOL').': '. $problem['tool'] ."\r\n\r\n" : "\r\n";
		$message['plaintext'] .= JText::_('COM_SUPPORT_PROBLEM_DETAILS').': '. $attach->parse(stripslashes($problem['long'])) ."\r\n\r\n";

		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $row->id);
		if (substr($sef,0,1) == '/') 
		{
			$sef = substr($sef, 1, strlen($sef));
		}
		$message['plaintext'] .= $juri->base() . $sef . "\r\n";


		// Html email
		$from['multipart'] = md5(date('U'));

		$eview = new JView(array(
			'name'   => 'emails', 
			'layout' => 'ticket'
		));
		$eview->option     = $this->_option;
		$eview->controller = $this->_controller;
		$eview->ticket     = $row;
		$eview->delimiter  = '';
		/*if ($allowEmailResponses)
		{
			$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
		}*/
		$eview->boundary   = $from['multipart'];
		$eview->attach     = $attach;

		$message['multipart'] = $eview->loadTemplate();
		$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

		// Load the support config
		//$params = JComponentHelper::getParams('com_support');

		// Get any set emails that should be notified of ticket submission
		$defs = str_replace("\r", '', $this->config->get('emails', '{config.mailfrom}'));
		$defs = explode("\n", $defs);

		if ($defs)
		{
			// Import our mailer
			ximport('Hubzero_Toolbox');

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
				if ($this->_isValidEmail($def))
				{
					// Send e-mail
					SupportUtilities::sendEmail($def, $subject, $message, $from);
					//Hubzero_Toolbox::send_email($def, $subject, $message);
				}
			}
		}

		// Only do the following if a comment was posted
		// otherwise, we're only recording a changelog
		if ($row->owner) 
		{
			$jconfig =& JFactory::getConfig();

			// Parse comments for attachments
			/*$attach = new SupportAttachment($this->database);
			$attach->webpath = $live_site . $this->config->get('webpath') . DS . $id;
			$attach->uppath  = JPATH_ROOT . $this->config->get('webpath') . DS . $id;
			$attach->output  = 'email';*/
			$live_site = rtrim(JURI::base(), '/');

			// Build e-mail components
			$admin_email = $jconfig->getValue('config.mailfrom');
			$allowEmailResponses = false;

			$subject = JText::_(strtoupper($this->_name)).', '.JText::_('TICKET').' #'.$row->id.' comment ';

			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$rowc = new SupportComment($this->database);
			$rowc->ticket     = $row->id;
			$rowc->created    = date('Y-m-d H:i:s', time());
			$rowc->created_by = $this->juser->get('id');
			$rowc->access     = 1;

			$log = array(
				'changes'       => array(),
				'notifications' => array(),
				'cc'            => array()
			);
			if ($tags) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_TAGS'),
					'before' => JText::_('BLANK'),
					'after'  => $tags
				);
			}
			if ($row->group) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_GROUP'),
					'before' => '',
					'after'  => $row->group
				);
			}
			if ($row->severity != 'normal') 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_SEVERITY'),
					'before' => 'normal',
					'after'  => $row->severity
				);
			}
			if ($row->owner) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_OWNER'),
					'before' => '',
					'after'  => $row->owner
				);
			}
			if ($row->resolved)
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_RESOLUTION'),
					'before' => '[unresolved]',
					'after'  => $row->resolved
				);
			}
			$log['changes'][] = array(
				'field'  => JText::_('TICKET_FIELD_STATUS'),
				'before' => SupportHtml::getStatus(1, 0),
				'after'  => SupportHtml::getStatus($row->open, $row->status)
			);

			$message = array();
			$message['plaintext']  = '----------------------------'."\r\n";
			$message['plaintext'] .= strtoupper(JText::_('TICKET')).': '.$row->id."\r\n";
			$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary)."\r\n";
			$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created."\r\n";
			$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name."\r\n";
			$message['plaintext'] .= strtoupper(JText::_('TICKET_FIELD_STATUS')).': '.SupportHtml::getStatus($row->status)."\r\n";
			$message['plaintext'] .= ($row->login) ? ' ('.$row->login.')'."\r\n" : "\r\n";
			$message['plaintext'] .= '----------------------------'."\r\n\r\n";
			$message['plaintext'] .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by."\r\n";
			$message['plaintext'] .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created."\r\n\r\n";
			$message['plaintext'] .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'."\r\n\r\n";
			//$message .= $attach->parse($comment)."\r\n\r\n";

			// Prepare message to allow email responses to be parsed and added to the ticket
			if ($this->config->get('email_processing') and file_exists("/etc/hubmail_gw.conf"))
			{
				$allowEmailResponses = true;

				$ticketURL = $live_site . JRoute::_('index.php?option=' . $this->option);

				$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
				$prependtext .= "You can reply to this message, just include your reply text above this area\r\n" ;
				$prependtext .= "Attachments (up to 2MB each) are permitted\r\n" ;
				$prependtext .= "Message from " . $live_site . " / Ticket #" . $row->id . "\r\n";

				$message['plaintext'] = $prependtext . "\r\n\r\n" . $message['plaintext'];
			}

			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $row->id);

			$message['plaintext'] .= $juri->base() . ltrim($sef, DS) . "\r\n";

			//-------
			$from['multipart'] = md5(date('U'));

			$rowc->changelog = $log;

			$eview = new JView(array(
				'name'   => 'emails', 
				'layout' => 'comment'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->comment    = $rowc;
			$eview->ticket     = $row;
			$eview->delimiter  = '';
			if ($allowEmailResponses)
			{
				$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
			}
			$eview->boundary   = $from['multipart'];
			$eview->attach     = $attach;

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);
			//-------

			// Send e-mail to admin?
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			// Send e-mail to ticket owner?
			$juser =& JUser::getInstance($row->owner);

			// Only put tokens in if component is configured to allow email responses to tickets and ticket comments
			if ($this->config->get('email_processing') and file_exists("/etc/hubmail_gw.conf"))
			{
				$encryptor = new Hubzero_EmailToken();
				// The reply-to address contains the token 
				$token = $encryptor->buildEmailToken(1, 1, $juser->get('id'), $row->id);
				$from['replytoemail'] = 'htc-' . $token;
			}

			if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option))) 
			{
				$this->setError(JText::_('Failed to message ticket owner.'));
			} 
			else 
			{
				$log['notifications'][] = array(
					'role'    => JText::_('COMMENT_SEND_EMAIL_OWNER'),
					'name'    => $juser->get('name'),
					'address' => $juser->get('email')
				);
			}

			// Were there any changes?
			if (count($log['notifications']) > 0) 
			{
				$rowc->changelog  = json_encode($log);

				if (!$rowc->check()) 
				{
					$this->setError($rowc->getError());
				}
				else
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
		$dispatcher->trigger('onTicketSubmission', array($row));

		if (!$no_html)
		{
			$this->_getStyles();
		}
		// Output Thank You message
		$this->view->ticket  = $row->id;
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
	 * Checks if a login (username) is valid
	 * 
	 * @param      string  $login Login to validate
	 * @return     boolean True if valid
	 */
	private function _isValidLogin($login)
	{
		if (preg_match("#^[_0-9a-zA-Z]+$#i", $login)) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if an email address is valid
	 * 
	 * @param      string  $email Address to validate
	 * @return     boolean True if valid
	 */
	private function _isValidEmail($email)
	{
		if (preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $email)) 
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Attempts to retrieve the tool name from a referrer string
	 * 
	 * @param      string $referrer Referrer URL
	 * @return     string Tool name
	 */
	private function _getTool($referrer)
	{
		$tool = '';

		if (!$referrer) 
		{
			return $tool;
		}

		if (substr($referrer, 0, 3) == '/mw') 
		{
			$bits = explode('/', $referrer);
			if ($bits[2] == 'invoke') 
			{
				$longbits = explode('?', $bits[3]);
				if (is_array($longbits)) 
				{
					$tool = trim($longbits[0]);
				} 
				else 
				{
					$tool = trim($bits[3]);
				}
			} 
			else if ($bits[2] == 'view') 
			{
				$longbits = explode('=', $bits[3]);
				if (is_array($longbits)) 
				{
					$tool = trim(end($longbits));
				} 
				else 
				{
					$tool = trim($bits[3]);
				}
			}
			// Check for revision indicator
			if (strstr($tool, '_r')) 
			{
				$version = strrchr($tool, '_r');
				$tool = str_replace($version, '', $tool);
			}
			// Check for dev indicator
			if (strstr($tool, '_dev')) 
			{
				$version = strrchr($tool, '_dev');
				$tool = str_replace($version, '', $tool);
			}
		} 
		else if (substr($referrer, 0, 6) == '/tools' || substr($referrer, 0, 10) == '/resources') 
		{
			$bits = explode('/', $referrer);
			$tool = (isset($bits[2])) ? trim($bits[2]) : '';
		} 
		else if (substr($referrer, 0, 4) == 'http') 
		{
			$bits = explode('/', $referrer);
			$tool = (isset($bits[4])) ? trim($bits[4]) : '';
		}

		return $tool;
	}

	/**
	 * Checks if a tool is tagged with one of the selected tag/groups
	 * 
	 * @param      string $tool A tool to check
	 * @return     string Group name
	 */
	private function _getTicketGroup($tool)
	{
		// Do we have a tool?
		if (!$tool) 
		{
			return '';
		}

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
		$resource = new ResourcesResource($this->database);
		$tool = str_replace(':', '-', $tool);
		$resource->loadAlias($tool);

		if (!$resource || $resource->type != 7) 
		{
			return '';
		}

		// Get tags on the tools
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$rt = new ResourcesTags($this->database);
		$tags = $rt->getTags($resource->id, 0, 0, 1);

		if (!$tags) 
		{
			return 'app-' . $tool;
		}

		// Get tag/group associations
		//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');
		$tt = new TagsGroup($this->database);
		$tgas = $tt->getRecords();

		if (!$tgas) 
		{
			return 'app-' . $tool;
		}

		// Loop through the tags and make a flat array so we can search quickly
		$ts = array();
		foreach ($tags as $tag)
		{
			$ts[] = $tag->tag;
		}
		// Loop through the tag/group array and see if one of them is in the tags list
		foreach ($tgas as $tga)
		{
			if (in_array($tga->tag, $ts)) 
			{
				// We found one! So set the group
				return $tga->cn;
				break;
			}
		}
		return 'app-' . $tool;
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
				JText::_('SUPPORT_NO_TICKET_ID'),
				'error'
			);
			return;
		}

		// Check authorization
		if ($this->juser->get('guest')) 
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task . '&id=' . $id, false, true));
			$this->setRedirect(
				JRoute::_('index.php?option=com_user&view=login&return=' . $return)
			);
			return;
		}

		$this->view->database = $this->database;

		// Incoming
		// Incoming
		//$this->view->filters = $this->_getFilters();
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

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

		// Initiate database class and load info
		$this->view->row = new SupportTicket($this->database);
		$this->view->row->load($id);

		if (!$this->view->row->id) 
		{
			JError::raiseError(404, JText::_('SUPPORT_TICKET_NOT_FOUND'));
			return;
		}

		if ($this->view->row->login == $this->juser->get('username')
		 || $this->view->row->owner == $this->juser->get('username')) 
		{
			if (!$this->acl->check('read', 'tickets')) 
			{
				$this->acl->setAccess('read', 'tickets', 1);
			}
			if (!$this->acl->check('update', 'tickets')) 
			{
				$this->acl->setAccess('update', 'tickets', -1);
			}
			if (!$this->acl->check('create', 'comments')) 
			{
				$this->acl->setAccess('create', 'comments', -1);
			}
			if (!$this->acl->check('read', 'comments')) 
			{
				$this->acl->setAccess('read', 'comments', 1);
			}
		}

		if ($this->acl->authorize($this->view->row->group)) 
		{
			$this->acl->setAccess('read',   'tickets',  1);
			$this->acl->setAccess('update', 'tickets',  1);
			$this->acl->setAccess('delete', 'tickets',  1);
			$this->acl->setAccess('create', 'comments', 1);
			$this->acl->setAccess('read',   'comments', 1);
			$this->acl->setAccess('create', 'private_comments', 1);
			$this->acl->setAccess('read',   'private_comments', 1);
		}

		// Ensure the user is authorized to view this ticket
		$this->view->authorized = $this->_authorize($this->view->row->group);
		if (!$this->acl->check('read','tickets')) 
		{
			JError::raiseError(403, JText::_('SUPPORT_NOT_AUTH'));
			return;
		}

		// Get the next and previous support tickets
		//$this->view->row->prev = $this->view->row->getTicketId('prev', $this->view->filters, $this->view->authorized);
		//$this->view->row->next = $this->view->row->getTicketId('next', $this->view->filters, $this->view->authorized);

		// Create a summary title from the report
		$summary = substr($this->view->row->report, 0, 70);
		if (strlen($summary) >= 70) 
		{
			$summary .= '...';
		}
		if ($this->view->row->summary == $summary) 
		{
			$this->view->row->summary = '';
		} 
		else 
		{
			// Do some text cleanup
			//$this->view->row->summary = html_entity_decode(stripslashes($this->view->row->summary), ENT_COMPAT, 'UTF-8');
			//$this->view->row->summary = str_replace('&quote;','&quot;',$this->view->row->summary);
			//$this->view->row->summary = htmlentities($this->view->row->summary, ENT_COMPAT, 'UTF-8');
		}

		//$this->view->row->report = html_entity_decode(stripslashes($this->view->row->report), ENT_COMPAT, 'UTF-8');
		//$this->view->row->report = str_replace('&quote;','&quot;',$this->view->row->report);
		//if (!strstr($this->view->row->report, '</p>') && !strstr($this->view->row->report, '<pre class="wiki">')) 
		//{
			//$this->view->row->report = str_replace('<br />', '', $this->view->row->report);
			$this->view->row->report = $this->view->escape($this->view->row->report);
			$this->view->row->report = nl2br($this->view->row->report);
			$this->view->row->report = str_replace("\t",' &nbsp; &nbsp;',$this->view->row->report);
			//$this->view->row->report = preg_replace('/  /', ' &nbsp;', $this->view->row->report);
		//}

		if ($watch = JRequest::getWord('watch', ''))
		{
			$watch = strtolower($watch);

			$watching = new SupportTableWatching($this->database);
			$watching->load($this->view->row->id, $this->juser->get('id'));

			// Not already watching
			if (!$watching->id) 
			{
				// Start watching?
				if ($watch == 'start')
				{
					$watching->ticket_id = $this->view->row->id;
					$watching->user_id   = $this->juser->get('id');
					$watching->store();
				}
				// Otherwise, do nothing
			}
			else
			// Already watching
			{
				// Stop watching?
				if ($watch == 'stop')
				{
					$watching->delete();
				}
				// Otherwise, do nothing
			}
		}

		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		// Get messages
		$sm = new SupportMessage($this->database);
		$this->view->lists['messages'] = $sm->getMessages();

		// Get Tags
		$st = new SupportTags($this->database);
		$this->view->lists['tags'] = $st->get_tag_string($this->view->row->id, 0, 0, NULL, 0, 1);
		$this->view->lists['tagcloud'] = $st->get_tag_cloud(3, 1, $this->view->row->id);

		// Get comments
		$sc = new SupportComment($this->database);
		$this->view->comments = $sc->getComments($this->acl->check('read', 'private_comments'), $this->view->row->id);

		// Parse comment text for attachment tags
		$juri =& JURI::getInstance();

		$webpath = str_replace('//', '/', $juri->base() . $this->config->get('webpath') . DS . $id);
		if (isset($_SERVER['HTTPS'])) 
		{
			$webpath = str_replace('http:', 'https:', $webpath);
		}
		if (!strstr($webpath, '://')) 
		{
			$webpath = str_replace(':/', '://', $webpath);
		}

		$attach = new SupportAttachment($this->database);
		$attach->webpath = $webpath;
		$attach->uppath  = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $id;
		$attach->output  = 'web';
		for ($i=0; $i < count($this->view->comments); $i++)
		{
			$comment =& $this->view->comments[$i];
			//$comment->comment = stripslashes($comment->comment);
			if (!strstr($comment->comment, '</p>') && !strstr($comment->comment, '<pre class="wiki">')) 
			{
				$comment->comment = str_replace("<br />", '', $comment->comment);
				$comment->comment = $this->view->escape($comment->comment);
				$comment->comment = nl2br($comment->comment);
				$comment->comment = str_replace("\t", ' &nbsp; &nbsp;', $comment->comment);
				$comment->comment = preg_replace('/  /', ' &nbsp;', $comment->comment);
			}
			$comment->comment = $attach->parse($comment->comment);
		}

		$this->view->row->report = $attach->parse($this->view->row->report);

		// Get severities
		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

		// Populate the list of assignees based on if the ticket belongs to a group or not
		if (trim($this->view->row->group)) 
		{
			$this->view->lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]', 
				$this->view->row->owner, 
				1, 
				'', 
				trim($this->view->row->group)
			);
		} 
		elseif (trim($this->config->get('group'))) 
		{
			$this->view->lists['owner'] = $this->_userSelectGroup(
				'ticket[owner]',
				$this->view->row->owner, 
				1, 
				'', 
				trim($this->config->get('group'))
			);
		} 
		else 
		{
			$this->view->lists['owner'] = $this->_userSelect(
				'ticket[owner]', 
				$this->view->row->owner, 
				1
			);
		}

		// Set the pathway
		$this->_buildPathway($this->view->row);

		// Set the page title
		$this->_buildTitle($this->view->row);

		$this->view->title = $this->_title;

		// Get some needed styles
		$this->_getStyles();

		// Get some needed scripts
		$this->_getScripts('assets/js/' . $this->_name);

		$this->view->acl = $this->acl;

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
		$live_site = rtrim(JURI::base(), '/');

		// Make sure we are still logged in
		if ($this->juser->get('guest')) 
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task, false, true));
			$this->setRedirect(
				JRoute::_('index.php?option=com_user&view=login&return=' . $return)
			);
			return;
		}

		//$params = &JComponentHelper::getParams($this->_option);
		$allowEmailResponses = $this->config->get('email_processing');

		if ($allowEmailResponses and file_exists("/etc/hubmail_gw.conf"))
		{
			$encryptor = new Hubzero_EmailToken();
		}

		// Incoming
		$incoming = JRequest::getVar('ticket', array(), 'post');

		// Trim all posted items
		$incoming = array_map('trim', $incoming);

		$id = JRequest::getInt('id', 0, 'post');
		if (!$id) 
		{
			JError::raiseError(500, JText::_('No Ticket ID provided.'));
			return;
		}

		// Instantiate the tagging class - we'll need this a few times
		$st = new SupportTags($this->database);

		// Load the old ticket so we can compare for the changelog
		if ($id) 
		{
			$old = new SupportTicket($this->database);
			$old->load($id);

			// Get Tags
			$oldtags = $st->get_tag_string($id, 0, 0, NULL, 0, 1);
		}

		// Initiate class and bind posted items to database fields
		$row = new SupportTicket($this->database);
		if (!$row->bind($incoming)) 
		{
			echo SupportHtml::alert($row->getError());
			exit();
		}


		// Check content
		if (!$row->check()) 
		{
			echo SupportHtml::alert($row->getError());
			exit();
		}

		// If an existing ticket AND closed AND previously open
		if ($id && !$row->open && $row->open != $old->open)
		{
			// Record the closing time
			$row->closed = date('Y-m-d H:i:s', time());
		}

		// Store new content
		if (!$row->store()) 
		{
			echo SupportHtml::alert($row->getError());
			exit();
		}

		$row->load($id);

		// Save the tags
		$tags = trim(JRequest::getVar('tags', '', 'post'));
		$st->tag_object($this->juser->get('id'), $row->id, $tags, 0, true);
		$tags = $st->get_tag_string($row->id, 0, 0, NULL, 0, 1);

		// We must have a ticket ID before we can do anything else
		if ($id) 
		{
			// Incoming comment
			$comment = JRequest::getVar('comment', '', 'post', 'none', 2);
			if ($comment) 
			{
				// If a comment was posted to a closed ticket, re-open it.
				if ($old->open == 0 && $row->open == 0) 
				{
					$row->open = 1;
					$row->status = 1;
					$row->resolved = '';
					$row->store();
				}
				// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
				$ccreated_by = JRequest::getVar('username', '');
				if ($row->status == 2 && $ccreated_by == $row->login) 
				{
					$row->status = 1;
					$row->resolved = '';
					$row->store();
				}
			}

			// Compare fields to find out what has changed for this ticket and build a changelog
			$log = array(
				'changes'       => array(),
				'notifications' => array(),
				'cc'            => array()
			);

			if ($tags != $oldtags) 
			{
				$oldtags = (trim($oldtags) == '') ? JText::_('BLANK') : $oldtags;
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_TAGS'),
					'before' => $oldtags,
					'after'  => $tags
				);
			}
			if ($row->group != $old->group) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_GROUP'),
					'before' => $old->group,
					'after'  => $row->group
				);
			}
			if ($row->severity != $old->severity) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_SEVERITY'),
					'before' => $old->severity,
					'after'  => $row->severity
				);
			}
			if ($row->owner != $old->owner) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_OWNER'),
					'before' => $old->owner,
					'after'  => $row->owner
				);
			}
			if ($row->resolved != $old->resolved) 
			{
				$row->resolved = ($row->resolved) ? $row->resolved : '[unresolved]';
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_RESOLUTION'),
					'before' => $old->resolved,
					'after'  => $row->resolved
				);
			}
			if ($row->status != $old->status) 
			{
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_STATUS'),
					'before' => SupportHtml::getStatus($old->open, $old->status),
					'after'  => SupportHtml::getStatus($row->open, $row->status)
				);
			}

			$attachment = $this->uploadTask($row->id);
			$comment .= ($attachment) ? "\n\n" . $attachment : '';

			// Create a new support comment object and populate it
			$rowc = new SupportComment($this->database);
			$rowc->ticket     = $id;
			$rowc->comment    = nl2br($comment);
			$rowc->comment    = str_replace('<br>', '<br />', $rowc->comment);
			$rowc->created    = date('Y-m-d H:i:s', time());
			$rowc->created_by = JRequest::getVar('username', '');
			$rowc->changelog  = json_encode($log);
			$rowc->access     = JRequest::getInt('access', 0);

			if ($rowc->check()) 
			{
				// If we're only recording a changelog, make it private
				if ($rowc->changelog && !$rowc->comment) 
				{
					$rowc->access = 1;
				}
				
				// Save the data
				if (!$rowc->store()) 
				{
					echo SupportHtml::alert($rowc->getError());
					exit();
				}

				// Only do the following if a comment was posted
				// otherwise, we're only recording a changelog
				if ($comment || $row->owner != $old->owner) 
				{
					$jconfig =& JFactory::getConfig();

					// Parse comments for attachments
					$attach = new SupportAttachment($this->database);
					$attach->webpath = $live_site . $this->config->get('webpath') . DS . $id;
					$attach->uppath  = JPATH_ROOT . $this->config->get('webpath') . DS . $id;
					$attach->output  = 'email';

					// Build e-mail components
					$admin_email = $jconfig->getValue('config.mailfrom');

					$subject = JText::_(strtoupper($this->_name)).', '.JText::_('TICKET').' #'.$row->id.' comment ';

					$from = array();
					$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
					$from['email'] = $jconfig->getValue('config.mailfrom');

					$message = array();
					$message['plaintext']  = '----------------------------'."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET')).': '.$row->id."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.stripslashes($row->summary)."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$row->created."\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$row->name . ($row->login ? ' ('.$row->login.')' : '') . "\r\n";
					$message['plaintext'] .= strtoupper(JText::_('TICKET_FIELD_STATUS')).': '.SupportHtml::getStatus($row->status)."\r\n";
					$message['plaintext'] .= '----------------------------'."\r\n\r\n";
					$message['plaintext'] .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED',$row->id).': '.$rowc->created_by."\r\n";
					$message['plaintext'] .= JText::_('TICKET_EMAIL_COMMENT_CREATED').': '.$rowc->created."\r\n\r\n";
					if ($row->owner != $old->owner) 
					{
						if ($old->owner == '') 
						{
							$message['plaintext'] .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_SET_TO').' "'.$row->owner.'"'."\r\n\r\n";
						} 
						else 
						{
							$message['plaintext'] .= JText::_('TICKET_FIELD_OWNER').' '.JText::_('TICKET_CHANGED_FROM').' "'.$old->owner.'" to "'.$row->owner.'"'."\r\n\r\n";
						}
					}
					$message['plaintext'] .= $attach->parse($comment)."\r\n\r\n";

					// Prepare message to allow email responses to be parsed and added to the ticket
					if ($allowEmailResponses)
					{
						$ticketURL = $live_site . JRoute::_('index.php?option=' . $this->option);
						
						$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
						$prependtext .= "You can reply to this message, just include your reply text above this area\r\n" ;
						$prependtext .= "Attachments (up to 2MB each) are permitted\r\n" ;
						$prependtext .= "Message from " . $live_site . " / Ticket #" . $row->id . "\r\n";

						$message['plaintext'] = $prependtext . "\r\n\r\n" . $message['plaintext'];
					}

					$juri =& JURI::getInstance();
					$sef = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $row->id);

					$message['plaintext'] .= $juri->base() . ltrim($sef, DS) . "\r\n";

					// Html email
					$from['multipart'] = md5(date('U'));

					//$rowc->comment   = $attach->parse($rowc->comment);
					$rowc->changelog = $log;

					$eview = new JView(array(
						'name'   => 'emails', 
						'layout' => 'comment'
					));
					$eview->option     = $this->_option;
					$eview->controller = $this->_controller;
					$eview->comment    = $rowc;
					$eview->ticket     = $row;
					$eview->delimiter  = '';
					if ($allowEmailResponses)
					{
						$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
					}
					$eview->boundary   = $from['multipart'];
					$eview->attach     = $attach;

					$message['multipart'] = $eview->loadTemplate();
					$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

					// An array for all the addresses to be e-mailed outside of the hub messaging system
					$emails = array();

					// Send e-mail to admin?
					JPluginHelper::importPlugin('xmessage');
					$dispatcher =& JDispatcher::getInstance();

					// Find a list of everyone watching this ticket
					$watching = new SupportTableWatching($this->database);
					$watchers = $watching->find(array('ticket_id' => $row->id));

					$watcher_ids = array();
					if (count($watchers) > 0)
					{
						foreach ($watchers as $watcher)
						{
							$watcher_ids[] = $watcher->user_id;
						}
					}
					$watcher_found = array();

					// Send e-mail to ticket submitter?
					$email_submitter = JRequest::getInt('email_submitter', 0);
					if ($email_submitter == 1) 
					{
						// Is the comment private? If so, we do NOT send e-mail to the 
						// submitter regardless of the above setting
						if ($rowc->access != 1) 
						{
							$zuser =& JUser::getInstance($row->login);
							// Make sure there even IS an e-mail and it's valid
							if (is_object($zuser) && $zuser->get('id')) 
							{
								// Track everyone already messaged so we don't message them twice
								if (in_array($zuser->get('id'), $watcher_ids))
								{
									$watcher_found[] = $zuser->get('id');
								}
								$type = 'support_reply_submitted';
								if ($row->status == 1) 
								{
									$element = $row->id;
									$description = 'index.php?option=' . $this->_option . '&task=ticket&id=' . $row->id;
								} 
								else 
								{
									$element = null;
									$description = '';
									if ($row->status == 2) 
									{
										$type = 'support_close_submitted';
									}
								}

								// Only build tokens in if component is configured to allow email responses to tickets and ticket comments
								if ($allowEmailResponses)
								{
									// The reply-to address contains the token 
									$token = $encryptor->buildEmailToken(1, 1, $zuser->get('id'), $id);
									$from['replytoemail'] = 'htc-' . $token;
								}
								
								if (!$dispatcher->trigger('onSendMessage', array($type, $subject, $message, $from, array($zuser->get('id')), $this->_option))) 
								{
									$this->setError(JText::_('Failed to message ticket submitter.'));
								} 
								else 
								{
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_SUBMITTER'),
										'name'    => $row->name,
										'address' => $row->email
									);
								}
							} 
							else if ($row->email && SupportUtilities::checkValidEmail($row->email)) 
							{
								if ($allowEmailResponses)
								{
									// Build a temporary token for this user, userid will not be valid, but the token will
									$token = $encryptor->buildEmailToken(1, 1, -9999, $id);
									$emails[] = array($row->email, 'htc-' . $token);
								}
								else
								{
									$emails[] = $row->email;
								}

								$log['notifications'][] = array(
									'role'    => JText::_('COMMENT_SEND_EMAIL_SUBMITTER'),
									'name'    => $row->name,
									'address' => $row->email
								);
							}
						}
					}
					
					// Send e-mail to ticket owner?
					$email_owner = JRequest::getInt('email_owner', 0);
					if ($email_owner == 1) 
					{
						if ($row->owner) 
						{
							$juser =& JUser::getInstance($row->owner);

							// Track everyone already messaged so we don't message them twice
							if (in_array($juser->get('id'), $watcher_ids))
							{
								$watcher_found[] = $juser->get('id');
							}

							// Only put tokens in if component is configured to allow email responses to tickets and ticket comments
							if ($allowEmailResponses)
							{
								// The reply-to address contains the token 
								$token = $encryptor->buildEmailToken(1, 1, $juser->get('id'), $id);
								$from['replytoemail'] = 'htc-' . $token;
							}

							if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option))) 
							{
								$this->setError(JText::_('Failed to message ticket owner.'));
							} 
							else 
							{
								$log['notifications'][] = array(
									'role'    => JText::_('COMMENT_SEND_EMAIL_OWNER'),
									'name'    => $juser->get('name'),
									'address' => $juser->get('email')
								);
							}
						}
					}

	
					// Add any CCs to the e-mail list
					$cc = JRequest::getVar('cc', '');
					if (trim($cc)) 
					{
						$cc = explode(',', $cc);
						foreach ($cc as $acc)
						{
							$acc = trim($acc);

							// Is this a username or email address?
							if (!strstr($acc, '@')) 
							{
								// Username or user ID - load the user
								$acc = (is_string($acc)) ? strtolower($acc) : $acc;
								$juser =& JUser::getInstance($acc);
								// Did we find an account?
								if (is_object($juser)) 
								{
									// Track everyone already messaged so we don't message them twice
									if (in_array($juser->get('id'), $watcher_ids))
									{
										$watcher_found[] = $juser->get('id');
									}

									if ($allowEmailResponses)
									{
										// The reply-to address contains the token 
										$token = $encryptor->buildEmailToken(1, 1, $juser->get('id'), $id);
										$from['replytoemail'] = 'htc-' . $token;
									}

									// Get the user's email address
									if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, array($juser->get('id')), $this->_option))) 
									{
										$this->setError(JText::_('Failed to message ticket owner.'));
									}
									$log['notifications'][] = array(
										'role'    => JText::_('COMMENT_SEND_EMAIL_CC'),
										'name'    => $juser->get('name'),
										'address' => $juser->get('email')
									);
									$log['cc'][] = $juser->get('username');
								} 
								else 
								{
									// Move on - nothing else we can do here
									continue;
								}
							// Make sure it's a valid e-mail address
							} 
							else if (SupportUtilities::checkValidEmail($acc)) 
							{
								
								if ($allowEmailResponses)
								{
									// The reply-to address contains the token
									$token = $encryptor->buildEmailToken(1, 1, -9999, $id);
									$emails[] = array($acc, 'htc-' . $token);
								}
								else
								{
									$emails[] = $acc;
								}
								$log['notifications'][] = array(
									'role'    => JText::_('COMMENT_SEND_EMAIL_CC'),
									'name'    => JText::_('[none]'),
									'address' => $acc
								);
								$log['cc'][] = $acc;
							}
						}
					}

					// Send an e-mail to each address
					foreach ($emails as $email)
					{
						if ($allowEmailResponses)
						{
							// In this case each item in email in an array, 1- To, 2:reply to address
							SupportUtilities::sendEmail($email[0], $subject, $message, $from, $email[1]);
						}
						else 
						{
							// email is just a plain 'ol string
							SupportUtilities::sendEmail($email, $subject, $message, $from);
						}
					}

					// Were there any changes?
					if (count($log['notifications']) > 0) 
					{
						$rowc->changelog = json_encode($log);

						// Save the data
						if (!$rowc->store()) 
						{
							echo SupportHtml::alert($rowc->getError());
							exit();
						}
					}

					// Message people watching this ticket
					$watch = array_diff($watcher_ids, $watcher_found);
					if (!$dispatcher->trigger('onSendMessage', array('support_reply_assigned', $subject, $message, $from, $watch, $this->_option))) 
					{
						$this->setError(JText::_('Failed to message watchers.'));
					}
				}
			}
		}

		// Display the ticket with changes, new comment
		if ($this->getError())
		{
			/*$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $id),
				$this->getError(),
				'error'
			);*/
			$this->addComponentMessage($this->getError(), 'error');
		}
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=ticket&id=' . $id)
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
	 * @return     mixed Return description (if any) ...
	 */
	public function createTask()
	{
		/*
		option  = 'com_support';
		task    = 'create';
		no_html = 1;
		type    = 1;
		sesstoken (optional)
		
		login    (optional) default: automated
		severity (optional) default: normal
		category (optional) default: Tools
		summary  (optional) default: first 75 characters of report
		report
		email    (optional) default: supportemail
		name     (optional) default: Automated Error Report
		os       (optional)
		browser  (optional)
		ip       (optional)
		hostname (optional)
		uas      (optional)
		referrer (optional)
		cookies  (optional) default: 1 (since it's coming from rappture we assume they're already logged in and thus have cookies enabled)
		section  (optional)
		upload   (optional)
		*/

		// trim and addslashes all posted items
		$incoming = array_map('trim', $_POST);
		$incoming = array_map('addslashes', $incoming);

		// initiate class and bind posted items to database fields
		$row = new SupportTicket($this->database);
		if (!$row->bind($incoming)) 
		{
			return $row->getError();
		}

		// Check for a session token
		$sess = JRequest::getVar('sesstoken', '');
		$sessnum = '';
		if ($sess) 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'mw.utils.php');
			$mwdb =& MwUtils::getMWDBO();

			// retrieve the username and IP from session with this session token
			$query = "SELECT * FROM session WHERE session.sesstoken='".$sess."' LIMIT 1";
			$mwdb->setQuery($query);
			$viewperms = $mwdb->loadObjectList();

			if ($viewperms) 
			{
				foreach ($viewperms as $sinfo)
				{
					$row->login = $sinfo->username;
					$row->ip    = $sinfo->remoteip;
					$sessnum    = $sinfo->sessnum;
				}

				// get user's infor from login
				$juser =& JUser::getInstance($row->login);
				$row->name  = $juser->get('name');
				$row->email = $juser->get('email');
			}
		}

		$row->login = ($row->login) ? $row->login : 'automated';

		if (strstr($row->summary, '"') || strstr($row->summary, "'")) 
		{
			$summary = str_replace("\'","\\\\\\\\\'", $row->summary);
			$summary = str_replace('\"','\\\\\\\\\"', $summary);
			$query = "SELECT id FROM #__support_tickets WHERE LOWER(summary) LIKE '%".strtolower($summary)."%' AND type=1 LIMIT 1";
		} 
		else 
		{
			$query = "SELECT id FROM #__support_tickets WHERE LOWER(summary) LIKE '%".strtolower($row->summary)."%' AND type=1 LIMIT 1";
		}
		// check for an existing ticket with this report
		$this->database->setQuery($query);
		$ticket = $this->database->loadResult();
		if ($this->database->getErrorNum()) 
		{
			return $this->database->stderr();
		}

		if ($ticket) 
		{
			$changelog = '';

			// open existing ticket if closed
			$oldticket = new SupportTicket($this->database);
			$oldticket->load($ticket);
			$oldticket->instances++;
			if ($oldticket->open == 0) 
			{
				$oldticket->open = 1;
				$oldticket->status = 1;
				$oldticket->resolved = 'reopened';

				$log = array(
					'changes' => array(),
					'notifications' => array()
				);

				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_STATUS'),
					'before' => SupportHtml::getStatus(0, 1),
					'after'  => SupportHtml::getStatus($oldticket->open, $oldticket->status)
				);
				$log['changes'][] = array(
					'field'  => JText::_('TICKET_FIELD_INSTANCE'),
					'before' => ($oldticket->instances - 1),
					'after'  => $oldticket->instances
				);
				$changelog = json_encode($log);
			}

			// check content
			if (!$oldticket->check()) 
			{
				return $oldticket->getError();
			}

			// store new content
			if (!$oldticket->store()) 
			{
				return $oldticket->getError();
			}

			// make a log note if we had to reopen the ticket
			if ($changelog) 
			{
				$rowc = new SupportComment($this->database);
				$rowc->ticket     = $ticket;
				$rowc->comment    = '';
				$rowc->created    = date('Y-m-d H:i:s', time());
				$rowc->created_by = $row->login;
				$rowc->changelog  = $changelog;
				$rowc->access     = 1;

				if ($rowc->check()) 
				{
					if (!$rowc->store()) 
					{
						return $rowc->getError();
					}
				}
			}

			$status = (!$oldticket->open && $oldticket->resolved) ? $oldticket->resolved : 'open';
			$count  = $oldticket->instances;
		} 
		else 
		{
			// set some defaults
			$row->status    = 0;
			$row->created   = date('Y-m-d H:i:s', time());
			$row->severity  = ($row->severity) ? $row->severity : 'normal';
			$row->category  = ($row->category) ? $row->category : JText::_('CATEGORY_TOOLS');
			$row->resolved  = '';
			$row->email     = ($row->email)    ? $row->email    : $this->_data['supportemail'];
			$row->name      = ($row->name)     ? $row->name     : JText::_('AUTOMATED_REPORT');
			$row->cookies   = ($row->cookies)  ? $row->cookies  : 1;
			$row->instances = 1;
			$row->section   = ($row->section)  ? $row->section  : 1;
			$row->type      = 1;

			if (!$row->summary) 
			{
				$row->summary = $this->txt_shorten($row->report, 75);
			}

			// clean any cross-site scripting from report
			ximport('Hubzero_Filter');
			$row->summary = Hubzero_Filter::cleanXss($row->summary);
			$row->report  = Hubzero_Filter::cleanXss($row->report);
			$row->report  = str_replace('<br>', '<br />', $row->report);
			$row->report  = '' . $row->report;

			// check content
			if (!$row->check()) 
			{
				return $row->getError();
			}

			// store new content
			if (!$row->store()) 
			{
				return $row->getError();
			}

			if (!$row->id) 
			{
				$query = "SELECT id FROM #__support_tickets 
							WHERE created='" . $row->created . "' 
							AND category='" . $row->category . "' 
							AND email='" . $row->email . "' 
							AND name='" . $row->name . "' 
							AND summary='" . $row->summary . "' 
							AND report='" . $row->report . "'";
				$this->database->setQuery($query);
				$row->id = $this->database->loadResult();
			}
			
			$attachment = $this->uploadTask($row->id);
			$row->report .= ($attachment) ? "\n\n" . $attachment : '';
			// Save the data
			if (!$row->store()) 
			{
				$this->setError($row->getError());
			}

			$ticket = $row->id;
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
				JRoute::_('index.php?option=com_user&view=login&return=' . $return)
			);
			return;
		}

		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Ensure we have a database object
		if (!$this->database) 
		{
			JError::raiseError(500, JText::_('SUPPORT_DATABASE_NOT_FOUND'));
			return;
		}

		// Get the ID of the file requested
		$id = JRequest::getInt('id', 0);

		// Instantiate an attachment object
		$attach = new SupportAttachment($this->database);
		$attach->load($id);
		if (!$attach->filename) 
		{
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$row = new SupportTicket($this->database);
		$row->load($attach->ticket);

		if (!$row->report) 
		{
			JError::raiseError(404, JText::_('SUPPORT_TICKET_NOT_FOUND'));
			return;
		}

		// Load ACL
		if ($row->login == $this->juser->get('username')
		 || $row->owner == $this->juser->get('username')) 
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
			JError::raiseError(403, JText::_('SUPPORT_NOT_AUTH_FILE'));
			return;
		}

		// Ensure we have a path
		if (empty($file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $file)) 
		{
			JError::raiseError(404, JText::_('SUPPORT_BAD_FILE_PATH'));
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
			JError::raiseError(404, JText::_('SUPPORT_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('SUPPORT_SERVER_ERROR'));
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
	public function uploadTask($listdir)
	{
		// Check if they are logged in
		/*if ($this->juser->get('guest')) 
		{
			return '';
		}*/

		if (!$listdir) 
		{
			$this->setError(JText::_('SUPPORT_NO_UPLOAD_DIRECTORY'));
			return '';
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

		// Construct our file path
		$path = JPATH_ROOT . $this->config->get('webpath') . DS . $listdir;

		// Build the path if it doesn't exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
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
			$this->setError(JText::_('ATTACHMENT: Incorrect file type.'));
			return JText::_('ATTACHMENT: Incorrect file type.');
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('ERROR_UPLOADING'));
			return '';
		} 
		else 
		{
			// Scan for viruses
			$path = $path . DS . $file['name']; //JPATH_ROOT . DS . 'virustest';
			exec("clamscan -i --no-summary --block-encrypted $path", $output, $status);
			if ($status == 1)
			{
				if (JFile::delete($path)) 
				{
					$this->setError(JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.'));
					return JText::_('ATTACHMENT: File rejected because the anti-virus scan failed.');
				}
			}

			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);

			$row = new SupportAttachment($this->database);
			$row->bind(array(
				'id' => 0,
				'ticket' => $listdir,
				'filename' => $file['name'],
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
		$query = "SELECT a.username AS value, a.name AS text"
			. " FROM #__users AS a"
			. " INNER JOIN #__support_acl_aros AS aro ON aro.model='user' AND aro.foreign_key = a.id"
			. " WHERE a.block = '0'"
			. " ORDER BY ". $order;

		$this->database->setQuery($query);
		if ($nouser) 
		{
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
			$users = array_merge($users, $this->database->loadObjectList());
		} 
		else 
		{
			$users = $this->database->loadObjectList();
		}

		$query = "SELECT a.username AS value, a.name AS text, aro.alias"
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
			foreach ($groups as $name => $gusers)
			{
				$users[] = JHTML::_('select.optgroup', JText::_('group:') . ' ' . $name);
				$users = array_merge($users, $gusers);
			}
		}

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
		if ($nouser) 
		{
			$users[] = JHTML::_('select.option', '', 'No User', 'value', 'text');
		}

		ximport('Hubzero_Group');

		if (strstr($group, ',')) 
		{
			$groups = explode(',', $group);
			if (is_array($groups)) 
			{
				foreach ($groups as $g)
				{
					$hzg = Hubzero_Group::getInstance(trim($g));

					if ($hzg->get('gidNumber')) 
					{
						$members = $hzg->get('members');

						$users[] = JHTML::_('select.optgroup', stripslashes($hzg->description));
						foreach ($members as $member)
						{
							$u =& JUser::getInstance($member);
							if (!is_object($u)) 
							{
								continue;
							}

							$m = new stdClass();
							$m->value = $u->get('username');
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
			$hzg = Hubzero_Group::getInstance($group);

			if ($hzg && $hzg->get('gidNumber')) 
			{
				$members = $hzg->get('members');

				foreach ($members as $member)
				{
					$u =& JUser::getInstance($member);
					if (!is_object($u)) 
					{
						continue;
					}

					$m = new stdClass();
					$m->value = $u->get('username');
					$m->text  = $u->get('name');
					$m->groupname = $group;
					
					$names = explode(' ', $u->get('name'));
					$last = trim(end($names));
					
					$users[$last . ',' . $u->get('name')] = $m;
				}
			}
			
			ksort($users);
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false);

		return $users;
	}

	/**
	 * Check if the user has authority to access the ticket
	 * 
	 * @param      string $toolgroup A group to check access against
	 * @return     mixed 
	 */
	protected function _authorize($toolgroup='')
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			if ($this->juser->authorize($this->_option, 'manage')) 
			{
				return 'admin';
			}
		}
		else 
		{
			$this->config->set('access-admin-component', $this->juser->authorise('core.admin', null));
			$this->config->set('access-manage-component', $this->juser->authorise('core.manage', null));
			if ($this->config->get('access-admin-component') || $this->config->get('access-manage-component'))
			{
				return 'admin';
			}
		}

		// Was a specific group set in the config?
		$group = trim($this->config->get('group'));
		if ($group or $toolgroup) 
		{
			ximport('Hubzero_User_Helper');

			// Check if they're a member of this group
			$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0) 
			{
				foreach ($ugs as $ug)
				{
					if ($group && $ug->cn == $this->gid) 
					{
						return true;
					}
					if ($toolgroup && $ug->cn == $toolgroup) 
					{
						return true;
					}
				}
			}
		}

		return false;
	}
}
