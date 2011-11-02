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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class SupportControllerResolutions extends Hubzero_Controller
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
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function display()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.resolutions.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.resolutions.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		$obj = new SupportResolution($this->database);
		
		// Record count
		$this->view->total = $obj->getCount($this->view->filters);
		
		// Fetch results
		$this->view->rows = $obj->getRecords($this->view->filters);
		
		// Initiate paging
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
	 * Create a new record
	 *
	 * @return	void
	 */
	public function add() 
	{
		$this->view->setLayout('edit');
		$this->edit();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function edit($row=null) 
	{
		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getInt('id', 0);
			
			// Initiate database class and load info
			$this->view->row = new SupportResolution($this->database);
			$this->view->row->load($id);
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
	 * Save changes to a record
	 *
	 * @return	void
	 */
	public function save() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
	
		// Trim and addslashes all posted items
		$res = JRequest::getVar('res', array(), 'post');
		$res = array_map('trim', $res);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportResolution($this->database);
		if (!$row->bind($res)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
	
		// Code cleaner for xhtml transitional compliance
		$row->title = trim($row->title);
		if (!$row->alias) 
		{
			$row->alias = preg_replace("/[^a-zA-Z0-9]/", '', $row->title);
			$row->alias = strtolower($row->alias);
		}
		
		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->edit($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->edit($row);
			return;
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
		$this->_message = JText::_('RESOLUTION_SUCCESSFULLY_SAVED');
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function remove() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$ids = JRequest::getVar('id', array());
	
		// Check for an ID
		if (count($ids) < 1) 
		{
			$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
			$this->_message = JText::_('SUPPORT_ERROR_SELECT_RESOLUTION_TO_DELETE');
			return;
		}
		
		foreach ($ids as $id) 
		{
			// Delete message
			$msg = new SupportResolution($this->database);
			$msg->delete(intval($id));
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
		$this->_message = JText::sprintf('RESOLUTION_SUCCESSFULLY_DELETED', count($ids));
	}
	
	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancel()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
	}
}
