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

class SupportControllerAcl extends Hubzero_Controller
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
		// Instantiate a new view
		$this->view->acl = SupportACL::getACL();
		$this->view->database = $this->database;
		
		// Fetch results
		$aro = new SupportAro($this->database);
		$this->view->rows = $aro->getRecords();

		// Output HTML
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}
	
	/**
	 * Update an existing record
	 *
	 * @return	void
	 */
	public function update()
	{
		// Check for request forgeries
		//JRequest::checkToken('get') or jexit('Invalid Token');

		$id     = JRequest::getInt('id', 0);
		$action = JRequest::getVar('action', '');
		$value  = JRequest::getInt('value', 0);
		
		$row = new SupportAroAco($this->database);
		$row->load($id);
		
		switch ($action) 
		{
			case 'create': $row->action_create = $value; break;
			case 'read':   $row->action_read   = $value; break;
			case 'update': $row->action_update = $value; break;
			case 'delete': $row->action_delete = $value; break;
		} 
		
		// Check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
		$this->_message = JText::_('ACL successfully updated');
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

		$ids = JRequest::getVar('id', array());
		
		foreach ($ids as $id) 
		{
			$row = new SupportAro($this->database);
			$row->load(intval($id));

			if ($row->id) 
			{
				$aro_aco = new SupportAroAco($this->database);
				if (!$aro_aco->deleteRecordsByAro($row->id)) 
				{
					JError::raiseError(500, $aro_aco->getError());
					return;
				}
			}

			if (!$row->delete()) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
		$this->_message = JText::_('ACL successfully removed');
	}
	
	/**
	 * Save a new record
	 *
	 * @return	void
	 */
	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim and addslashes all posted items
		$aro = JRequest::getVar('aro', array(), 'post');
		$aro = array_map('trim', $aro);
	
		// Initiate class and bind posted items to database fields
		$row = new SupportAro($this->database);
		if (!$row->bind($aro)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		
		if (!$row->foreign_key || !$row->alias) 
		{
			switch ($row->model) 
			{
				case 'user':
					if (!$row->foreign_key) 
					{
						$user = JUser::getInstance($row->alias);
						if (!is_object($user)) 
						{
							JError::raiseError(500, 'Cannot find user');
							return;
						}
						$row->foreign_key = $user->get('id');
					} 
					else 
					{
						$user = JUser::getInstance($row->foreign_key);
						if (!is_object($user)) 
						{
							JError::raiseError(500, 'Cannot find user');
							return;
						}
						$row->alias = $user->get('username');
					}
				break;
				
				case 'group':
					ximport('Hubzero_Group');
					if (!$row->foreign_key) 
					{
						$group = Hubzero_Group::getInstance($row->alias);
						if (!is_object($group)) 
						{
							JError::raiseError(500, 'Cannot find group');
							return;
						}
						$row->foreign_key = $group->gidNumber;
					} 
					else 
					{
						$group = Hubzero_Group::getInstance($row->foreign_key);
						if (!is_object($group)) 
						{
							JError::raiseError(500, 'Cannot find group');
							return;
						}
						$row->alias = $group->cn;
					}
				break;
			}
		}
		
		// Check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if (!$row->id) 
		{
			$row->id = $this->database->insertid();
		}

		// Trim and addslashes all posted items
		$map = JRequest::getVar('map', array(), 'post');

		foreach ($map as $k => $v) 
		{
			// Initiate class and bind posted items to database fields
			$aroaco = new SupportAroAco($this->database);
			if (!$aroaco->bind($v)) 
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			$aroaco->aro_id = (!$aroaco->aro_id) ? $row->id : $aroaco->aro_id;

			// Check content
			if (!$aroaco->check()) 
			{
				JError::raiseError(500, $aroaco->getError());
				return;
			}

			// Store new content
			if (!$aroaco->store()) 
			{
				JError::raiseError(500, $aroaco->getError());
				return;
			}
		}

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&c=' . $this->_controller;
		$this->_message = JText::_('ACL successfully created');
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
