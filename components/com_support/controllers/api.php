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

ini_set('display_errors', 1);
error_reporting(E_ALL);

JLoader::import('Hubzero.Api.Controller');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');

/**
 * API controller class for support tickets
 */
class SupportApiController extends Hubzero_Api_Controller
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

		$this->segments = $this->getRouteSegments();
		$this->response = $this->getResponse();

		$this->config = JComponentHelper::getParams('com_support');
		$this->database = JFactory::getDBO();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'acl.php');
		$this->acl = SupportACL::getACL();

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
	 * Short description for 'not_found'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function error()
	{
		$this->getResponse()->setErrorMessage(404, 'Not Found');
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
		$jconfig =& JFactory::getConfig();
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
	 * Displays a list of tickets
	 *
	 * @return    void
	 */
	private function tickets()
	{
		//get request vars
		$format = JRequest::getVar('format', 'json');

		$limit = JRequest::getInt('limit', 25);
		$start = JRequest::getInt('limitstart', 0);
		//$period = JRequest::getVar('period', 'month');
		//$category = JRequest::getVar('category', '');

		/*if ($this->acl->check('read', 'tickets')) 
		{
		}

		ximport('Hubzero_Whatsnew');
		JLoader::import('joomla.plugin.helper');

		//encode results and return response
		$object = new stdClass();
		$object->tickets = Hubzero_Whatsnew::getWhatsNewBasedOnPeriodAndCategory( $period, $category, $limit );*/

		$obj = new SupportTicket($this->database);
		$obj->tickets = null;

		//$this->response->setResponseProvides($format);
		//$this->response->setMessage($object);
		$this->setMessageType($format);
		$this->setMessage($obj);
	}

	/**
	 * Create a new ticket
	 *
	 * @return     void
	 */
	private function create()
	{
		$this->setMessageType(JRequest::getVar('format', 'json'));

		// Create an object for returning messages
		$msg = new stdClass;

		// Initiate class and bind data to database fields
		$ticket = new SupportTicket($this->database);

		// Set the created date
		$ticket->created   = $msg->submitted = date("Y-m-d H:i:s");

		// Incoming
		$ticket->report   = JRequest::getVar('report', '', 'post', 'none', 2);
		if (!$ticket->report)
		{
			$msg->success = false;
			$msg->errors  = array(JText::_('Error: Report contains no text.'));

			$this->setMessage($msg);
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
		$juser = JFactory::getUser();
		$ticket->name      = $juser->get('name');
		$ticket->email     = $juser->get('email');
		$ticket->login     = $juser->get('username');

		// Set some helpful info
		$ticket->instances = 1;
		$ticket->section   = 1;
		$ticket->open      = 1;
		$ticket->status    = 0;

		ximport('Hubzero_Environment');
		$ticket->ip        = Hubzero_Environment::ipAddress();
		$ticket->hostname  = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

		// Check the data
		if (!$ticket->check()) 
		{
			$msg->success = false;
			$msg->errors  = $row->getErrors();

			$this->setMessage($msg);
			return;
		}

		// Save the data
		if (!$ticket->store()) 
		{
			$msg->success = false;
			$msg->errors  = $row->getErrors();

			$this->setMessage($msg);
			return;
		}

		// Any tags?
		$tags = trim(JRequest::getVar('tags', '', 'post'));
		if ($tags)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'tags.php');

			$st = new SupportTags($this->database);
			$st->tag_object($juser->get('id'), $ticket->id, $tags, 0, true);
		}

		// Set the response
		$msg->success = true;
		$msg->ticket  = $ticket->id;

		$this->setMessage($msg);
	}
}
