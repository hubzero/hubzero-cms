<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

class CronControllerJobs extends Hubzero_Controller
{
	public function displayTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.jobs.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.jobs.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.jobs.sort', 
			'sort', 
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.jobs.sortdir', 
			'sort_Dir', 
			'DESC'
		));

		$model = new CronJob($this->database);
		
		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @return	void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);
		
		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (is_array($ids)) 
		{
			$id = intval($ids[0]);
		}
	
		// load infor from database
		$this->view->row = new CronJob($this->database);
		$this->view->row->load($id);
		
		if (!$this->view->row->id)
		{
			$this->view->row->created = date('Y-m-d H:i:s', time());
			$this->view->row->created_by = $this->juser->get('id');
			
			$this->view->row->recurrence = '';
		}
		//$bits = explode(' ', $this->view->row->recurrence);
		$this->view->row->minute = '*'; //$bits[0];
		$this->view->row->hour = '*'; //$bits[1];
		$this->view->row->day = '*'; //$bits[2];
		$this->view->row->month = '*'; //$bits[3];
		$this->view->row->dayofweek = '*'; //$bits[4];
		
		$defaults = array(
			'',
			'0 0 1 1 *',
			'0 0 1 * *',
			'0 0 * * 0',
			'0 0 * * *',
			'0 * * * *'
		);
		if (!in_array($this->view->row->recurrence, $defaults))
		{
			$this->view->row->recurrence = 'custom';
		}
		
		$e = array();
		JPluginHelper::importPlugin('cron');
		$dispatcher =& JDispatcher::getInstance();
		$events = $dispatcher->trigger('onCronEvents');
		if ($events)
		{
			foreach ($events as $event)
			{
				$e[$event->plugin] = $event->events;
			}
		}
		
		$query = "SELECT p.* FROM #__extensions AS p WHERE p.type='plugin' AND p.folder='cron' AND enabled=1 ORDER BY p.ordering";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT p.* FROM #__plugins AS p WHERE p.folder='cron' AND published=1 ORDER BY p.ordering";
		}
		$this->database->setQuery($query);
		$this->view->plugins = $this->database->loadObjectList();
		if ($this->view->plugins)
		{
			foreach ($this->view->plugins as $key => $plugin)
			{
				$this->view->plugins[$key]->events = (isset($e[$plugin->element])) ? $e[$plugin->element] : array();
			}
		}

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves an entry and redirects to listing
	 *
	 * @return	void
	 */
	public function saveTask() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		$recurrence = array();
		if (isset($fields['minute']))
		{
			$recurrence[] = ($fields['minute']['c']) ? $fields['minute']['c'] : $fields['minute']['s'];
		}
		if (isset($fields['hour']))
		{
			$recurrence[] = ($fields['hour']['c']) ? $fields['hour']['c'] : $fields['hour']['s'];
		}
		if (isset($fields['day']))
		{
			$recurrence[] = ($fields['day']['c']) ? $fields['day']['c'] : $fields['day']['s'];
		}
		if (isset($fields['month']))
		{
			$recurrence[] = ($fields['month']['c']) ? $fields['month']['c'] : $fields['month']['s'];
		}
		if (isset($fields['dayofweek']))
		{
			$recurrence[] = ($fields['dayofweek']['c']) ? $fields['dayofweek']['c'] : $fields['dayofweek']['s'];
		}
		if (!empty($recurrence))
		{
			$fields['recurrence'] = implode(' ', $recurrence);
		}

		// Initiate extended database class
		$row = new CronJob($this->database);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}
		
		// Check content
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Item Successfully Saved')
		);
	}

	/**
	 * Deletes one or more records and redirects to listing
	 * 
	 * @return     void
	 */
	public function removeTask() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$ids = JRequest::getVar('id', array());
		
		// Ensure we have an ID to work with
		if (empty($ids)) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No entry selected'),
				'error'
			);
			return;
		}
		
		$obj = new CronJob($this->database);
		
		// Loop through each ID
		foreach ($ids as $id) 
		{
			$obj->delete(intval($id));
		}
		
		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Item(s) Successfully Removed')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 * 
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}
	
	/**
	 * Calls stateTask to unpublish entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}
	
	/**
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function stateTask($state=0) 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$action = ($state == 1) ? JText::_('unpublish') : JText::_('publish');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Select an entry to ' . $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new CronJob($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store()) 
			{
				$this->addComponentMessage($row->getError(), 'error');
			}
		}

		// set message
		if ($state == 1) 
		{
			$message = JText::_(count($ids) . ' Item(s) successfully published');
		} 
		else
		{
			$message = JText::_(count($ids) . ' Item(s) successfully unpublished');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}
	
	/**
	 * Sets the state of one or more entries
	 * 
	 * @param      integer The state to set entries to
	 * @return     void
	 */
	public function accessTask() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$state = JRequest::getInt('access', 0);
		$ids = JRequest::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Select an entry to change access'),
				'error'
			);
			return;
		}

		foreach ($ids as $id) 
		{
			// Update record(s)
			$row = new ForumSection($this->database);
			$row->load(intval($id));
			$row->access = $state;
			if (!$row->store()) 
			{
				$this->addComponentMessage($row->getError(), 'error');
			}
		}

		// set message
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_(count($ids) . ' Item(s) successfully changed access')
		);
	}
	
	/**
	 * Cancels a task and redirects to listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

