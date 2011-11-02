<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

class SupportControllerStats extends Hubzero_Controller
{
	/**
	 * A list of executable tasks
	 *
	 * @param array
	 */
	protected $_taskMap = array('__default' => 'display');
	
	/**
	 * The name of the task to be executed
	 *
	 * @param string
	 */
	protected $_doTask = null;
	
	/**
	 * The name of this controller
	 *
	 * @param string
	 */
	protected $_controller = null;
	
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Determine the methods to exclude from the base class.
		$xMethods = get_class_methods('Hubzero_Controller');
		
		$r = new ReflectionClass($this);
		$methods = $r->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method)
		{
			$name = $method->getName();

			// Add default display method if not explicitly declared.
			if (!in_array($name, $xMethods) || $name == 'display') 
			{
				//$this->methods[] = strtolower($mName);
				// Auto register the methods as tasks.
				$this->_taskMap[strtolower($name)] = $name;
			}
		}
		
		$this->_task = strtolower(JRequest::getWord('task', 'display'));

		if (isset($this->_taskMap[$this->_task])) 
		{
			$doTask = $this->_taskMap[$this->_task];
		}
		elseif (isset($this->_taskMap['__default'])) 
		{
			$doTask = $this->_taskMap['__default'];
		}
		else 
		{
			return JError::raiseError(404, JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $this->_task));
		}

		if (preg_match('/' . ucfirst($this->_name) . 'Controller(.*)/i', get_class($this), $r))
		{
			$this->_controller = strtolower($r[1]);
			
			$this->view = new JView(array(
				'name' => $this->_controller,
				'layout' => preg_replace('/[^A-Z0-9_]/i', '', $doTask)
			));
		}
		else
		{
			$this->view = new JView(array(
				'name' => $doTask
			));
		}
		
		$this->view->option = $this->_option;
		$this->view->task = $doTask;
		$this->view->controller = $this->_controller;
		
		// Record the actual task being fired
		$this->_doTask = $doTask;
		
		$this->$doTask();
	}
	
	/**
	 * Displays some overview stats of tickets
	 *
	 * @return	void
	 */
	public function display() 
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.$this->_name.'.css');
		
		// Instantiate a new view
		$this->view->title = JText::_(strtoupper($this->_name));
		
		$type = JRequest::getVar('type', 'submitted');
		$this->view->type = ($type == 'automatic') ? 1 : 0;
		
		$this->view->group = JRequest::getVar('group', '');
		
		$this->view->sort = JRequest::getVar('sort', 'name');
		
		// Set up some dates
		$jconfig =& JFactory::getConfig();
		$this->offset = $jconfig->getValue('config.offset');
		
		$year  = JRequest::getInt('year', strftime("%Y", time()+($this->offset*60*60)));
		$month = strftime("%m", time()+($this->offset*60*60));
		$day   = strftime("%d", time()+($this->offset*60*60));
		if ($day<="9"&ereg("(^[1-9]{1})",$day)) 
		{
			$day = "0$day";
		}
		if ($month<="9"&ereg("(^[1-9]{1})",$month)) 
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
		$week = strftime("%d", $week_start);
		
		$this->view->year = $year;
		$this->view->opened = array();
		$this->view->closed = array();
		
		$st = new SupportTicket($this->database);
		
		// Get opened ticket information
		$this->view->opened['year'] = $st->getCountOfTicketsOpened($this->view->type, $year, '01', '01', $this->view->group);
		
		$this->view->opened['month'] = $st->getCountOfTicketsOpened($this->view->type, $year, $month, '01', $this->view->group);
		
		$this->view->opened['week'] = $st->getCountOfTicketsOpened($this->view->type, $year, $month, $week, $this->view->group);
		
		// Currently open tickets
		$this->view->opened['open'] = $st->getCountOfOpenTickets($this->view->type, false, $this->view->group);
		
		// Currently unassigned tickets
		$this->view->opened['unassigned'] = $st->getCountOfOpenTickets($this->view->type, true, $this->view->group);
		
		// Get closed ticket information
		$this->view->closed['year'] = $st->getCountOfTicketsClosed($this->view->type, $year, '01', '01', null, $this->view->group);
		
		$this->view->closed['month'] = $st->getCountOfTicketsClosed($this->view->type, $year, $month, '01', null, $this->view->group);
		
		$this->view->closed['week'] = $st->getCountOfTicketsClosed($this->view->type, $year, $month, $week, null, $this->view->group);
		
		// Users
		$this->view->users = null;
		
		if ($this->view->group) 
		{
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a, #__xgroups AS g, #__xgroups_members AS gm"
				. "\n WHERE g.cn='".$this->view->group."' AND g.gidNumber=gm.gidNumber AND gm.uidNumber=a.id"
				. "\n ORDER BY a.name";
		} 
		else 
		{
			$query = "SELECT a.username, a.name, a.id"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"	// map aro to group
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n WHERE a.block = '0' AND g.id=25"
				. "\n ORDER BY a.name";
		}
		
		$this->database->setQuery($query);
		$users = $this->database->loadObjectList();
		if ($users) 
		{
			$u = array();
			$p = array();
			$g = array();
			foreach ($users as $user) 
			{
				$user->closed = array();
				
				// Get closed ticket information
				$user->closed['year'] = $st->getCountOfTicketsClosed($this->view->type, $year, '01', '01', $user->username, $this->view->group);

				$user->closed['month'] = $st->getCountOfTicketsClosed($this->view->type, $year, $month, '01', $user->username, $this->view->group);

				$user->closed['week'] = $st->getCountOfTicketsClosed($this->view->type, $year, $month, $week, $user->username, $this->view->group);
				
				$p[$user->id] = $user;
				switch ($this->view->sort) 
				{
					case 'year':
						$u[$user->id] = $user->closed['year'];
					break;
					case 'month':
						$u[$user->id] = $user->closed['month'];
					break;
					case 'week':
						$u[$user->id] = $user->closed['week'];
					break;
					case 'name':
					default:
						$u[$user->id] = $user->name;
					break;
				}
			}
			if ($this->view->sort != 'name') 
			{
				arsort($u);
			} 
			else 
			{
				asort($u);
			}
			foreach ($u as $k => $v) 
			{
				$g[] = $p[$k];
			}
			
			$this->view->users = $g;
		}

		// Get avgerage lifetime
		$this->view->lifetime = $st->getAverageLifeOfTicket($this->view->type, $year, $this->view->group);
		
		// Tickets over time
		$this->view->closedmonths = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$this->view->closedmonths[$i] = $st->getCountOfTicketsClosedInMonth(
				$this->view->type, 
				$year, 
				sprintf("%02d",$i), 
				$this->view->group
			);
		}
		
		$this->view->openedmonths = array();
		for ($i = 1; $i <= 12; $i++) 
		{
			$this->view->openedmonths[$i] = $st->getCountOfTicketsOpenedInMonth(
				$this->view->type, 
				$year, 
				sprintf("%02d",$i), 
				$this->view->group
			);
		}
		
		// Output HTML
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}
}
