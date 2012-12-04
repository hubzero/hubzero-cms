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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Tasks plugin for time component
 */
class plgTimeTasks extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeTasks(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'tasks' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'tasks',
			'title'  => JText::_('PLG_TIME_TASKS'),
			'return' => 'html'
		);

		return $area;
	}

	/**
	 * @param    string $action - plugin action to take (default 'view')
	 * @param    string $option - component option
	 * @param    string $active - active tab
	 * @return   array Return   - $arr with HTML of current active plugin
	 */
	public function onTime($action='', $option, $active='')
	{
		// Get this area details
		$this_area = $this->onTimeAreas();

		// Check if the active tab is the current one, otherwise return
		if ($this_area['name'] != $active)
		{
			return;
		}

		$return = 'html';

		// The output array we're returning
		$arr = array(
			'html'=>''
		);

		// Set some values for use later
		$this->_option   =  $option;
		$this->action    =  $action;
		$this->active    =  $active;
		$this->db        =  JFactory::getDBO();
		$this->juser     =& JFactory::getUser();
		$this->mainframe =& JFactory::getApplication();

		// Include needed DB classes and helper files
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'filters.php');

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('time','tasks');
		Hubzero_Document::addPluginScript('time','tasks');

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html')
		{
			switch ($action)
			{
				// Views
				case 'edit':         $arr['html'] = $this->_edit();          break;
				case 'new':          $arr['html'] = $this->_edit();          break;
				case 'view':         $arr['html'] = $this->_view();          break;

				// Data
				case 'save':         $arr['html'] = $this->_save();          break;
				case 'delete':       $arr['html'] = $this->_delete();        break;
				case 'toggleactive': $arr['html'] = $this->_toggleActive();  break;

				// Default
				default:             $arr['html'] = $this->_view();          break;
			}
		}

		// Return the output
		return $arr;
	}

	/**
	 * Primary/default view function
	 * 
	 * @return object Return
	 */
	private function _view()
	{
		// Instantiate tasks class
		$tasks = new TimeTasks($this->db);
		$hub   = new TimeHubs($this->db);

		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'tasks',
				'name'=>'view'
			)
		);

		// Set filters for view
		$view->filters = TimeFilters::getFilters();

		// Get the total number of tasks (for pagination)
		$view->total = $tasks->getCount($view->filters);

		// Setup pagination
		$view->pageNav = TimeFilters::getPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Get the tasks
		$view->tasks = $tasks->getTasks($view->filters);
		$view->tasks = TimeFilters::highlight($view->tasks, $view->filters);

		// Get the column list and operators
		$view->cols      = TimeFilters::getColumnNames('time_tasks', array("id", "description"));
		$view->operators = TimeFilters::buildSelectOperators();

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;

		return $view->loadTemplate();
	}

	/**
	 * New/Edit function
	 * 
	 * @return object Return
	 */
	private function _edit($task=null)
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'tasks',
				'name'=>'edit'
			)
		);

		// Get the id if we're editing a task
		$pid = JRequest::getInt('id');

		// Incoming
		if (is_object($task))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$view->row = $task;
		}
		else 
		{
			// Create a new object (i.e. we're coming in clean)
			$task = new TimeTasks($this->db);
			$task->load($pid);
			$view->row = $task;
		}

		// Build the hubs list, priority list, assignee list, and liaison list
		$view->hlist         = TimeHTML::buildHubsList($this->active, $view->row->hub_id, 1);
		$view->priority_list = TimeHTML::buildPriorityList($view->row->priority, $this->active);
		$view->alist         = TimeHTML::buildUserList($view->row->assignee, $this->active, 2);
		$view->llist         = TimeHTML::buildLiaisonList($view->row->liaison, $this->active, 1);

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$view->start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->action        = $this->action;

		return $view->loadTemplate();
	}

	/**
	 * Save new time task and redirect to the tasks page
	 * 
	 * @return void
	 */
	private function _save()
	{
		$task = JRequest::getVar('task', array(), 'post');
		$task = array_map('trim', $task);

		// Create object
		$tasks = new TimeTasks($this->db);

		// Save the posted array
		if (!$tasks->save($task))
		{
			// Something went wrong...return errors to view
			$this->addPluginMessage($tasks->getError(), 'error');
			return $this->_edit($tasks);
		}

		// If saving a task from a page other than the first, take the user back to that page after saving
		$start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Success, we made it, set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=tasks' . $start),
			JText::_('PLG_TIME_TASKS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete task
	 * 
	 * @return void
	 */
	private function _delete()
	{
		// Incoming posted data
		$tid = JRequest::getInt('id');

		// If deleting a record from a page other than the first, take the user back to that page after deletion
		$start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Check if the task has any active records
		$records = new TimeRecords($this->db);
		$count   = $records->getCount($filters = array('task'=>$tid));

		// If there are active records, don't allow deletion
		if($count > 0)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=tasks&action=edit&id=' . $tid),
				JText::_('PLG_TIME_TASK_DELETE_HAS_ASSOCIATED_RECORDS'),
				'warning'
			);
		}

		// Create object and load by task id
		$tasks = new TimeTasks($this->db);
		$tasks->load($tid);

		// Delete the task
		$tasks->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=tasks' . $start),
			JText::_('PLG_TIME_TASKS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Toggle a task's active status
	 * 
	 * @return void
	 */
	private function _toggleActive()
	{
		// Incoming posted data
		$pid = JRequest::getInt('id');

		// If delete a record from a page other than the first, take the user back to that page after toggle
		$start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Create object and store new content
		$task = new TimeTasks($this->db);
		$task->load($pid);
		$active = ($task->active == 0) ? 1 : 0;

		$task->active = $active;
		if (!$task->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=tasks' . $start),
			JText::_('PLG_TIME_TASKS_ACTIVE_STATUS_CHANGED'),
			'passed'
		);
	}

	/**
	 * Set redirect
	 * 
	 * @return void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}