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
 * /administrator/components/com_support/controllers/tickets.php
 *
 */

JLoader::import('Hubzero.Api.Controller');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'status.php');

/**
 * API controller class for support tickets
 */
class SupportControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return    void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		$this->config = JComponentHelper::getParams('com_support');
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'acl.php');
		$this->acl = SupportACL::getACL();
		$this->acl->setUser(JFactory::getApplication()->getAuthn('user_id'));

		switch ($this->segments[0])
		{
			case 'ticket':  $this->ticket();  break;
			case 'tickets': $this->tickets(); break;
			case 'stats':   $this->stats();   break;
			case 'create':  $this->create();  break;
			default:        $this->error();   break;
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function errorMessage($code, $message, $format = 'json')
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		//add error to message body
		$this->setMessageType($format);
		$this->setMessage($object);
	}

	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function stats()
	{
		$format = JRequest::getVar('format', 'json');

		$type = JRequest::getVar('type', 'submitted');
		$type = ($type == 'automatic') ? 1 : 0;

		$group = JRequest::getVar('group', '');

		// Set up some dates
		$jconfig = JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');

		$year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));
		if ($month <= "9"&preg_match("#(^[1-9]{1})#",$month))
		{
			$month = "0$month";
		}
		$day   = strftime("%d", time()+($this->offset*60*60));
		if ($day <= "9"&preg_match("#(^[1-9]{1})#",$day))
		{
			$day = "0$day";
		}

		/*$startday = 0;
		$numday = ((date("w",mktime(0,0,0,$month,$day,$year))-$startday)%7);
		if ($numday == -1)
		{
			$numday = 6;
		}
		$week_start = mktime(0, 0, 0, $month, ($day - $numday), $year);
		$week = strftime("%d", $week_start);*/

		$stats = new stdClass;
		$stats->open = 0;
		$stats->new = 0;
		$stats->unassigned = 0;
		$stats->closed = 0;
		$stats->tickets = new stdClass;
		$stats->tickets->opened = array();
		$stats->tickets->closed = array();

		$st = new SupportTicket($this->database);


		$sql = "SELECT id, created, YEAR(created) AS `year`, MONTH(created) AS `month`, status, owner
				FROM #__support_tickets
				WHERE report!=''
				AND type=" . $type . " AND open=1";
		if (!$group)
		{
			$sql .= " AND (`group`='' OR `group` IS NULL)";
		}
		else
		{
			$sql .= " AND `group`='{$group}'";
		}
		$sql .= " ORDER BY created ASC";
		$this->database->setQuery($sql);
		$openTickets = $this->database->loadObjectList();
		foreach ($openTickets as $o)
		{
			if (!isset($stats->tickets->opened[$o->year]))
			{
				$stats->tickets->opened[$o->year] = array();
			}
			if (!isset($stats->tickets->opened[$o->year][$o->month]))
			{
				$stats->tickets->opened[$o->year][$o->month] = 0;
			}
			$stats->tickets->opened[$o->year][$o->month]++;

			$stats->open++;

			if (!$o->status)
			{
				$stats->new++;
			}
			if (!$o->owner)
			{
				$stats->unassigned++;
			}
		}

		$this->setMessageType($format);
		$this->setMessage($stats);
	}

	/**
	 * Calculate time
	 *
	 * @param     $val string Timestamp or word [month, year, week, day]
	 * @return    string
	 */
	private function _toTimestamp($val=null)
	{
		if ($val)
		{
			$val = strtolower($val);

			if (strstr($val, ','))
			{
				$vals = explode(',', $val);
				foreach ($vals as $i => $v)
				{
					$vales[$i] = $this->_toTimestamp(trim($v));
				}
				return $vals;
			}
			switch ($val)
			{
				case 'year':
					$val = JFactory::getDate(mktime(0, 0, 0, date("m"), date("d"), date("Y")-1))->format("Y-m-d H:i:s");
				break;

				case 'month':
					$val = JFactory::getDate(mktime(0, 0, 0, date("m")-1, date("d"), date("Y")))->format("Y-m-d H:i:s");
				break;

				case 'week':
					$val = JFactory::getDate(mktime(0, 0, 0, date("m"), date("d")-7, date("Y")))->format("Y-m-d H:i:s");
				break;

				case 'day':
					$val = JFactory::getDate(mktime(0, 0, 0, date("m"), date("d")-1, date("Y")))->format("Y-m-d H:i:s");
				break;

				default:
					if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val, $regs))
					{
						// Time already matches pattern so do nothing.
						//$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
					}
					else if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $val, $regs))
					{
						$val .= ' 00:00:00';
					}
					else if (preg_match("/([0-9]{4})-([0-9]{2})/", $val, $regs))
					{
						$val .= '-01 00:00:00';
					}
					else
					{
						// Not an acceptable time
					}
				break;
			}
		}

		return $val;
	}

	/**
	 * Displays a list of tickets
	 *
	 * @return    void
	 */
	private function tickets()
	{
		if (!$this->acl->check('read', 'tickets'))
		{
			return $this->errorMessage(403, JText::_('Permission denied.'));
		}

		$obj = new SupportTicket($this->database);

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'group'      => JRequest::getVar('group', ''),
			'reportedby' => JRequest::getVar('reporter', ''),
			'owner'      => JRequest::getVar('owner', ''),
			'type'       => JRequest::getInt('type', 0),
			'status'     => strtolower(JRequest::getWord('status', '')),
			'tag'        => JRequest::getWord('tag', ''),
			'sort'       => JRequest::getWord('sort', 'created'),
			'sortdir'    => strtoupper(JRequest::getWord('sortDir', 'DESC'))
		);

		$filters['opened'] = $this->_toTimestamp(JRequest::getVar('opened', ''));
		$filters['closed'] = $this->_toTimestamp(JRequest::getVar('closed', ''));

		$response = new stdClass;
		$response->success = true;
		$response->total   = 0;
		$response->tickets = array();

		/*if ($filters['closed'])
		{
			$sql = "SELECT c.ticket, c.created
					FROM #__support_comments AS c
					LEFT JOIN #__support_tickets AS t ON c.ticket=t.id";

			$where = array();
			$where[] = "t.report != ''";
			$where[] = $filters['type'];
			if ($filters['group'] && $filters['group'] == '_none_')
			{
				$where[] = "(t.`group`='' OR t.`group` IS NULL)";
			}
			else if ($filters['group'])
			{
				$where[] = "t.`group`=" . $this->database->Quote($filters['group']);
			}
			if (is_array($filters['opened']))
			{
				$where[] = "c.`created` >= " . $this->_db->Quote($filters['closed'][0]);
				$where[] = "c.`created` <= " . $this->_db->Quote($filters['closed'][1]);
			}
			else
			{
				$where[] = "c.`created` >= " . $this->_db->Quote($filters['closed'][0]);
			}

			$sql .= " WHERE " . implode(" AND ", $where);
			$sql .= " ORDER BY c.created ASC";

			$this->database->setQuery($sql);
			$clsd = $this->database->loadObjectList();
			if ($clsd)
			{
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
				foreach ($closedTickets as $ticketId)
				{
					$filters[]
				}
			}
		}*/

		// Get a list of all statuses
		$sobj = new SupportTableStatus($this->database);

		$statuses = array();
		if ($data = $sobj->find('all'))
		{
			foreach ($data as $status)
			{
				$statuses[$status->id] = $status;
			}
		}

		// Get a count of tickets
		$response->total = $obj->getTicketsCount($filters);

		if ($response->total)
		{
			$response->tickets = $obj->getTickets($filters);

			$juri = JURI::getInstance();

			foreach ($response->tickets as $i => $ticket)
			{
				$owner = $ticket->owner;

				$response->tickets[$i]->owner = new stdClass;
				$response->tickets[$i]->owner->username = $owner;
				$response->tickets[$i]->owner->name     = $ticket->owner_name;
				$response->tickets[$i]->owner->id       = $ticket->owner_id;

				//unset($response->tickets[$i]->owner);
				unset($response->tickets[$i]->owner_name);
				unset($response->tickets[$i]->owner_id);

				$response->tickets[$i]->reporter = new stdClass;
				$response->tickets[$i]->reporter->name     = $ticket->name;
				$response->tickets[$i]->reporter->username = $ticket->login;
				$response->tickets[$i]->reporter->email    = $ticket->email;

				unset($response->tickets[$i]->name);
				unset($response->tickets[$i]->login);
				unset($response->tickets[$i]->email);

				$status = $response->tickets[$i]->status;

				$response->tickets[$i]->status = new stdClass;
				if (!$status)
				{
					$response->tickets[$i]->status->alias = 'new';
					$response->tickets[$i]->status->title = 'New';
				}
				else
				{
					$response->tickets[$i]->status->alias = (isset($statuses[$status]) ? $statuses[$status]->alias : 'unknown');
					$response->tickets[$i]->status->title = (isset($statuses[$status]) ? $statuses[$status]->title : 'unknown');
				}
				$response->tickets[$i]->status->id    = $status;

				$response->tickets[$i]->url = rtrim($juri->base(), DS) . DS . ltrim(JRoute::_('index.php?option=com_support&controller=tickets&task=tickets&id=' . $response->tickets[$i]->id), DS);
			}
		}

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Create a new ticket
	 *
	 * @return     void
	 */
	private function create()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false)	return $this->not_found();

		$this->setMessageType(JRequest::getVar('format', 'json'));

		// Create an object for returning messages
		$msg = new stdClass;

		// Initiate class and bind data to database fields
		$ticket = new SupportTicket($this->database);

		// Set the created date
		$ticket->created   = $msg->submitted = JFactory::getDate()->toSql();

		// Incoming
		$ticket->report   = JRequest::getVar('report', '', 'post', 'none', 2);
		if (!$ticket->report)
		{
			$this->errorMessage(500, JText::_('Error: Report contains no text.'));
			return;
		}
		$ticket->os        = JRequest::getVar('os', 'unknown', 'post');
		$ticket->browser   = JRequest::getVar('browser', 'unknown', 'post');
		$ticket->severity  = JRequest::getVar('severity', 'normal', 'post');

		// Cut suggestion at 70 characters
		$ticket->summary   = substr($ticket->report, 0, 70);
		if (strlen($ticket->summary) >= 70)
		{
			$ticket->summary .= '...';
		}

		// Get user data
		//$juser = JFactory::getUser();
		$ticket->name      = $result->get('name');
		$ticket->email     = $result->get('email');
		$ticket->login     = $result->get('username');

		// Set some helpful info
		$ticket->instances = 1;
		$ticket->section   = 1;
		$ticket->open      = 1;
		$ticket->status    = 0;

		$ticket->ip        = JRequest::ip();
		$ticket->hostname  = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

		// Check the data
		if (!$ticket->check())
		{
			$this->errorMessage(500, $ticket->getErrors());
			return;
		}

		// Save the data
		if (!$ticket->store())
		{
			$this->errorMessage(500, $ticket->getErrors());
			return;
		}

		// Any tags?
		$tags = trim(JRequest::getVar('tags', '', 'post'));
		if ($tags)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'tags.php');

			$st = new SupportTags($this->database);
			$st->tag_object($result->get('uidNumber'), $ticket->id, $tags, 0, true);
		}

		// Set the response
		$msg->success = true;
		$msg->ticket  = $ticket->id;

		$this->setMessage($msg);
	}
}
