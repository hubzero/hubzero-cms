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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

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

		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		//if ($result === false) return $this->not_found();

		$this->config = JComponentHelper::getParams('com_support');
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'acl.php');
		$this->acl = SupportACL::getACL();
		$this->acl->setUser($userid);

		switch ($this->segments[0])
		{
			case 'ticket':
				switch ($this->segments[1])
				{
					case 'create': $this->commentCreate(); break;
					case 'edit':   $this->commentUpdate(); break;
					case 'delete': $this->commentDelete(); break;
					default:
						$this->ticketRead($this->segments[1]);
					break;
				}
			break;
			case 'tickets': $this->tickets();      break;
			case 'stats':   $this->stats();        break;
			case 'create':  $this->ticketCreate(); break;
			case 'comment':
				switch ($this->segments[1])
				{
					case 'create': $this->commentCreate(); break;
					case 'edit':   $this->commentUpdate(); break;
					case 'delete': $this->commentDelete(); break;
					default:       $this->commentRead();   break;
				}
			break;
			default:
				$this->service();
			break;
		}
	}

	/**
	 * Displays a available options and parameters the API
	 * for this comonent offers.
	 *
	 * @return  void
	 */
	private function service()
	{
		$response = new stdClass();
		$response->component = 'support';
		$response->tasks = array();

		if ($this->acl->check('read', 'tickets'))
		{
			$response->tasks = array(
				'comment' => array(
					'description' => JText::_('Get a specific comment on a ticket.'),
					'parameters'  => array(
						'ticket' => array(
							'description' => JText::_('Ticket ID. Used in conjuction with the position parameter.'),
							'type'        => 'integer',
							'default'     => '0'
						),
						'position' => array(
							'description' => JText::_('Comment position. Used in conjuction with the ticket parameter.'),
							'type'        => 'integer',
							'default'     => 'null',
							'accepts'     => array('first', 'last')
						),
						'id' => array(
							'description' => JText::_('Comment ID. Specifying this will override any other parameters'),
							'type'        => 'integer',
							'default'     => '0'
						),
					),
				),
				'comment/create' => array(
					'description' => JText::_('Create a comment on a support ticket.'),
					'parameters'  => array(
						'ticket' => array(
							'description' => JText::_('Ticket ID.'),
							'type'        => 'integer',
							'default'     => '0',
							'required'    => 'true'
						),
						'comment' => array(
							'description' => JText::_('Comment text.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'status' => array(
							'description' => JText::_('Ticket status.'),
							'type'        => 'integer',
							'default'     => 'null'
						),
						'open' => array(
							'description' => JText::_('Open/Closed state of the ticket.'),
							'type'        => 'integer',
							'default'     => 'null'
						),
						'tags' => array(
							'description' => JText::_('Comma-separated list of tags to apply to the ticket.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'cc' => array(
							'description' => JText::_('Comma-separated list of user IDs, usernames, and/or email addresses.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'owner' => array(
							'description' => JText::_('User currently assigned to the ticket.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'severity' => array(
							'description' => JText::_('User currently assigned to the ticket.'),
							'type'        => 'string',
							'default'     => 'null'
						),
					),
				),
				'ticket' => array(
					'description' => JText::_('Get detail information about a ticket.'),
					'parameters'  => array(
						'id' => array(
							'description' => JText::_('Ticket ID.'),
							'type'        => 'integer',
							'default'     => 'null'
						),
					),
				),
				'tickets' => array(
					'description' => JText::_('Get a list of threads for a specific section and category.'),
					'parameters'  => array(
						'section' => array(
							'description' => JText::_('Section alias.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'category' => array(
							'description' => JText::_('Category alias.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'search' => array(
							'description' => JText::_('A word or phrase to search for.'),
							'type'        => 'string',
							'default'     => 'null'
						),
						'limit' => array(
							'description' => JText::_('Number of result to return.'),
							'type'        => 'integer',
							'default'     => '25'
						),
						'limitstart' => array(
							'description' => JText::_('Number of where to start returning results.'),
							'type'        => 'integer',
							'default'     => '0'
						),
					),
				),
			);
		}

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
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
				FROM `#__support_tickets`
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
	 * Displays details for a ticket
	 *
	 * @return    void
	 */
	private function ticketRead($id=0)
	{
		if (!$this->acl->check('read', 'tickets'))
		{
			return $this->errorMessage(403, JText::_('Permission denied.'));
		}

		// Initiate class and bind data to database fields
		$ticket_id = JRequest::getInt('ticket', $id);

		// Initiate class and bind data to database fields
		$ticket = new SupportModelTicket($ticket_id);

		$response = new stdClass;
		$response->id = $ticket->get('id');

		$response->owner = new stdClass;
		$response->owner->username = $ticket->owner('username');
		$response->owner->name     = $ticket->owner('name');
		$response->owner->id       = $ticket->owner('id');

		$response->reporter = new stdClass;
		$response->reporter->name     = $ticket->submitter('name');
		$response->reporter->username = $ticket->submitter('username');
		$response->reporter->email    = $ticket->submitter('email');

		$response->status = new stdClass;
		$response->status->alias = $ticket->status('class');
		$response->status->title = $ticket->status('text');
		$response->status->id    = $ticket->get('status');

		foreach (array('created', 'severity', 'os', 'browser', 'ip', 'hostname', 'uas', 'referrer', 'open', 'closed') as $prop)
		{
			$response->$prop = $ticket->get($prop);
		}

		$response->report = $ticket->content('raw');

		$response->url = rtrim(JURI::base(), DS) . DS . ltrim(JRoute::_('index.php?option=com_support&controller=tickets&task=tickets&id=' . $response->id), DS);

		$response->comments = array();
		foreach ($ticket->comments() as $comment)
		{
			$c = new stdClass;
			$c->id = $comment->get('id');
			$c->created = $comment->get('created');
			$c->creator = new stdClass;
			$c->creator->username = $comment->creator('username');
			$c->creator->name     = $comment->creator('name');
			$c->creator->id       = $comment->creator('id');
			$c->private = ($comment->access ? true : false);
			$c->content = $comment->content('raw');

			$response->comments[] = $c;
		}

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Create a new ticket
	 *
	 * @return     void
	 */
	private function ticketCreate()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false) return $this->not_found();

		// Initiate class and bind data to database fields
		$ticket = new SupportModelTicket();

		// Set the created date
		$ticket->set('created', JFactory::getDate()->toSql());

		// Incoming
		$ticket->set('report', JRequest::getVar('report', '', 'post', 'none', 2));
		if (!$ticket->get('report'))
		{
			$this->errorMessage(500, JText::_('Error: Report contains no text.'));
			return;
		}
		$ticket->set('os', JRequest::getVar('os', 'unknown', 'post'));
		$ticket->set('browser', JRequest::getVar('browser', 'unknown', 'post'));
		$ticket->set('severity', JRequest::getVar('severity', 'normal', 'post'));

		// Cut suggestion at 70 characters
		$summary = substr($ticket->get('report'), 0, 70);
		if (strlen($summary) >= 70)
		{
			$summary .= '...';
		}
		$ticket->set('summary', $summary);

		// Get user data
		$ticket->set('name', $result->get('name'));
		$ticket->set('email', $result->get('email'));
		$ticket->set('login', $result->get('username'));

		// Set some helpful info
		$ticket->set('instances', 1);
		$ticket->set('section', 1);
		$ticket->set('open', 1);
		$ticket->set('status', 0);

		$ticket->set('ip', JRequest::ip());
		$ticket->set('hostname', gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server')));

		// Save the data
		if (!$ticket->store())
		{
			$this->errorMessage(500, $ticket->getErrors());
			return;
		}

		// Any tags?
		if ($tags = trim(JRequest::getVar('tags', '', 'post')))
		{
			$ticket->tag($tags, $result->get('uidNumber'));
		}

		// Set the response
		$msg = new stdClass;
		$msg->submitted = $ticket->get('created');
		$msg->ticket    = $ticket->get('id');

		$this->setMessageType(JRequest::getVar('format', 'json'));
		$this->setMessage($msg, 200, 'OK');
	}

	/**
	 * Display details for a comment
	 *
	 * @return    void
	 */
	private function commentRead()
	{
		if (!$this->acl->check('read', 'tickets'))
		{
			return $this->errorMessage(403, JText::_('Permission denied.'));
		}

		// Initiate class and bind data to database fields
		$id = JRequest::getInt('comment', 0);

		// Initiate class and bind data to database fields
		$ticket = new SupportModelComment($id);

		$response = new stdClass;
		$response->id = $comment->get('id');
		$response->ticket = $comment->get('ticket');

		$response->owner = new stdClass;
		$response->owner->username = $ticket->owner('username');
		$response->owner->name     = $ticket->owner('name');
		$response->owner->id       = $ticket->owner('id');

		$response->content = $comment->content('raw');

		$response->url = rtrim(JURI::base(), DS) . DS . ltrim(JRoute::_('index.php?option=com_support&controller=tickets&task=tickets&id=' . $comment->get('ticket') . '#c' . $comment->get('id')), DS);

		$response->private = ($comment->get('access') ? true : false);

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Create a new comment on a ticket
	 *
	 * @return     void
	 */
	private function commentCreate()
	{
		// Initiate class and bind data to database fields
		$ticket_id = JRequest::getInt('ticket', 0, 'post');

		// Load the old ticket so we can compare for the changelog
		$old = new SupportModelTicket($ticket_id);
		$old->set('tags', $old->tags('string'));

		if (!$old->exists())
		{
			$this->errorMessage(500, JText::sprintf('Ticket "%s" does not exist.', $ticket_id));
			return;
		}

		// Initiate class and bind posted items to database fields
		$ticket = new SupportModelTicket($ticket_id);
		$ticket->set('status',   JRequest::getInt('status', $ticket->get('status'), 'post'));
		$ticket->set('open',     JRequest::getInt('open', $ticket->get('open'), 'post'));
		$ticket->set('category', JRequest::getInt('category', $ticket->get('category'), 'post'));
		$ticket->set('severity', JRequest::getVar('severity', $ticket->get('severity'), 'post'));
		$ticket->set('owner',    JRequest::getVar('owner', $ticket->get('owner'), 'post'));
		$ticket->set('group',    JRequest::getVar('group', $ticket->get('group'), 'post'));

		// If an existing ticket AND closed AND previously open
		if ($ticket_id && !$ticket->get('open') && $ticket->get('open') != $old->get('open'))
		{
			// Record the closing time
			$ticket->set('closed', JFactory::getDate()->toSql());
		}

		// Any tags?
		if ($tags = trim(JRequest::getVar('tags', '', 'post')))
		{
			$ticket->tag($tags, $user->get('uidNumber'));
			$ticket->set('tags', $ticket->tags('string'));
		}

		// Store new content
		if (!$ticket->store())
		{
			$this->errorMessage(500, $ticket->getError());
			return;
		}

		// Create a new comment
		$comment = new SupportModelComment();
		$comment->set('ticket', $ticket->get('id'));
		$comment->set('comment', nl2br(JRequest::getVar('comment', '', 'post', 'none', 2)));
		if ($comment->get('comment'))
		{
			// If a comment was posted by the ticket submitter to a "waiting user response" ticket, change status.
			if ($ticket->isWaiting() && $user->get('username') == $ticket->get('login'))
			{
				$ticket->open();
			}
		}
		$comment->set('created', JFactory::getDate()->toSql());
		$comment->set('created_by', $user->get('uidNumber'));
		$comment->set('access', JRequest::getInt('access', 0, 'post'));

		// Compare fields to find out what has changed for this ticket and build a changelog
		$comment->changelog()->diff($old, $ticket);

		$comment->changelog()->cced(JRequest::getVar('cc', '', 'post'));

		// Store new content
		if (!$comment->store())
		{
			$this->errorMessage(500, $comment->getError());
			return;
		}

		$jconfig = JFactory::getConfig();

		if ($ticket->get('owner'))
		{
			$comment->addTo(array(
				'role'  => JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
				'name'  => $ticket->owner('name'),
				'email' => $ticket->owner('email'),
				'id'    => $ticket->owner('id')
			));
		}

		// Add any CCs to the e-mail list
		foreach ($comment->changelog()->get('cc') as $cc)
		{
			$comment->addTo($cc, JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
		}

		// Check if the notify list has eny entries
		if (count($comment->to()))
		{
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');

			$allowEmailResponses = $ticket->config('email_processing');
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

			$subject = JText::sprintf('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $ticket->get('id'));

			$from = array(
				'name'      => JText::sprintf('COM_SUPPORT_EMAIL_FROM', $jconfig->getValue('config.sitename')),
				'email'     => $jconfig->getValue('config.mailfrom'),
				'multipart' => md5(date('U'))
			);

			$message = array();

			// Plain text email
			$eview = new \Hubzero\Component\View(array(
				'base_path' => JPATH_ROOT . '/components/com_support',
				'name'      => 'emails',
				'layout'    => 'comment_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->comment    = $comment;
			$eview->ticket     = $ticket;
			$eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

			$message['plaintext'] = $eview->loadTemplate();
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML email
			$eview->setLayout('comment_html');
			$message['multipart'] = $eview->loadTemplate();

			// Send e-mail to admin?
			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();

			foreach ($comment->to('ids') as $to)
			{
				if ($allowEmailResponses)
				{
					// The reply-to address contains the token
					$token = $encryptor->buildEmailToken(1, 1, $to['id'], $ticket->get('id'));
					$from['replytoemail'] = 'htc-' . $token . strstr($jconfig->getValue('config.mailfrom'), '@');
				}

				// Get the user's email address
				if (!$dispatcher->trigger('onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), 'com_support')))
				{
					$this->setError(JText::sprintf('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')'));
				}
				$comment->changelog()->notified(
					$to['role'],
					$to['name'],
					$to['email']
				);
			}

			foreach ($comment->to('emails') as $to)
			{
				if ($allowEmailResponses)
				{
					$token = $encryptor->buildEmailToken(1, 1, -9999, $ticket->get('id'));

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

				$comment->changelog()->notified(
					$to['role'],
					$to['name'],
					$to['email']
				);
			}
		}

		// Were there any changes?
		if (count($comment->changelog()->get('notifications')) > 0
		 || count($comment->changelog()->get('cc')) > 0
		 || count($comment->changelog()->get('changes')) > 0)
		{
			// Save the data
			if (!$comment->store())
			{
				$this->errorMessage(500, $comment->getError());
				return;
			}
		}

		$msg = new stdClass;
		$msg->ticket  = $ticket->get('id');
		$msg->comment = $comment->get('id');
		$msg->notified = $comment->changelog()->get('notifications');

		$this->setMessageType(JRequest::getVar('format', 'json'));
		$this->setMessage($msg, 200, 'OK');
	}
}
