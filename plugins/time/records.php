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
 * Records plugin for time component
 */
class plgTimeRecords extends Hubzero_Plugin
{

	/**
	 * @param  unknown &$subject Parameter description (if any) ...
	 * @param  unknown $config Parameter description (if any) ...
	 * @return void
	 */
	public function plgTimeRecords(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'time', 'records' );
		$this->_params = new JParameter( $this->_plugin->params );
		$this->loadLanguage();
	}

	/**
	 * @return array Return
	 */
	public function &onTimeAreas()
	{
		$area = array(
			'name'   => 'records',
			'title'  => JText::_('PLG_TIME_RECORDS'),
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

		// Include needed DB classes and helpers
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'html.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'helpers'.DS.'filters.php');

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('time','records');
		Hubzero_Document::addPluginScript('time','records');

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			switch ($action)
			{
				// Views
				case 'edit':     $arr['html'] = $this->_edit();              break;
				case 'new':      $arr['html'] = $this->_edit();              break;
				case 'view':     $arr['html'] = $this->_view();              break;
				case 'readonly': $arr['html'] = $this->_read_only();         break;

				// Data management
				case 'save':     $arr['html'] = $this->_save();              break;
				case 'delete':   $arr['html'] = $this->_delete();            break;

				// Default
				default:         $arr['html'] = $this->_view();              break;
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
		// Instantiate records class
		$records = new TimeRecords($this->db);

		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'records',
				'name'=>'view'
			)
		);

		// Set filters for view
		$view->filters = TimeFilters::getFilters();

		// Get the total number of records (for pagination)
		$view->total = $records->getCount($view->filters);

		// Setup pagination
		$view->pageNav = TimeFilters::getPagination($view->total, $view->filters['start'], $view->filters['limit']);

		// Get the records
		$view->records = $records->getRecords($view->filters);

		// Get suborinates of current user
		$view->subordinates = TimeHTML::getSubordinates($this->juser->get('id'));

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->mainframe     = $this->mainframe;
		$view->active        = $this->active;
		$view->juser         = $this->juser;

		return $view->loadTemplate();
	}

	/**
	 * New/Edit function
	 * 
	 * @return object Return
	 */
	private function _edit($record=null)
	{
		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'records',
				'name'=>'edit'
			)
		);

		// Get the id if we're editing a record
		$rid = JRequest::getInt('id');

		// Incoming
		if (is_object($record))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$view->row = $record;
		}
		else 
		{
			// Create a new object (i.e. we're coming in clean)
			$record = new TimeRecords($this->db);
			$view->row = $record->getRecord($rid);

			// Prepopulate the task passed in URL if it's given
			if($task = JRequest::getInt('task', NULL))
			{
				$view->row->task_id = $task;
			}
		}

		// Get suborinates of current user
		$subordinates = TimeHTML::getSubordinates($this->juser->get('id'));

		// Only allow creator of the record to edit or delete or the manager of the user
		if(!empty($view->row->id) && ($view->row->user_id != $this->juser->get('id') && !in_array($view->row->user_id, $subordinates)))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=records'),
				JText::_('PLG_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
		}

		// If record is marked as billed, don't allow editing
		if($view->row->billed == 1)
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=records&action=readonly&id=' . $view->row->id),
				JText::_('PLG_TIME_RECORDS_WARNING_READONLY'),
				'warning'
			);
		}

		// Explode the time
		if(strstr($view->row->time, '.') !== false)
		{
			list($hrs, $mins) = explode(".", $view->row->time);
		}
		else
		{
			$hrs = $view->row->time;
			$mins = 0;
		}

		// Build select lists for edit page
		$view->htimelist = TimeHTML::buildTimeListHours($hrs);
		$view->mtimelist = TimeHTML::buildTimeListMins($mins);
		$view->hubslist  = TimeHTML::buildHubsList($this->active, $view->row->hid);
		$view->tasklist  = TimeHTML::buildTasksList($view->row->task_id, $this->active, $view->row->hid, $view->row->pactive);

		// Build subordinates list if applicable
		if (isset($subordinates) && !empty($subordinates))
		{
			$view->subordinates = TimeHTML::buildSubordinatesList($view->row->user_id, $subordinates);
		}

		// Is this a new record?
		if(empty($view->row->user_id))
		{
			// Set some defaults
			$view->row->user_id = $this->juser->get('id');
			$view->row->uname   = $this->juser->get('name');
			$view->row->date    = date('Y-m-d');
		}

		// If viewing a record from a page other than the first, take the user back to that page if they click "all records"
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
	 * Readonly view of single record
	 * 
	 * @return view
	 */
	private function _read_only()
	{
		// Get the id if we're editing a record
		$rid = JRequest::getInt('id');

		// Instantiate classes
		$record = new TimeRecords($this->db);

		// Create a new plugin view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'time',
				'element'=>'records',
				'name'=>'edit',
				'layout'=>'readonly'
			)
		);

		// Get suborinates of current user
		$view->subordinates = TimeHTML::getSubordinates($this->juser->get('id'));

		// Get the records for time and pass them to the view
		$view->row = $record->getRecord($rid);

		// If viewing a record from a page other than the first, take the user back to that page if they click "all records"
		$view->start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Set a few things for the vew
		$view->notifications = ($this->getPluginMessage()) ? $this->getPluginMessage() : array();
		$view->option        = $this->_option;
		$view->action        = $this->action;
		$view->juser         = $this->juser;

		return $view->loadTemplate();
	}

	/**
	 * Save new time record and redirect to the records page
	 * 
	 * @return void
	 */
	private function _save()
	{
		// Incoming posted data
		$record = JRequest::getVar('record', array(), 'post');
		$record = array_map('trim', $record);

		// Get suborinates of current user
		$subordinates = TimeHTML::getSubordinates($this->juser->get('id'));

		// Only create records for yourself or your subordinates
		if($record['user_id'] != $this->juser->get('id') && !in_array($record['user_id'], $subordinates))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=records'),
				JText::_('PLG_TIME_RECORDS_WARNING_CANT_EDIT_OTHER'),
				'warning'
			);
		}

		// Combine the time entry
		$record['time'] = $record['htime'] . '.' . $record['mtime'];

		// Create object and store new content
		$records = new TimeRecords($this->db);

		if (!$records->save($record))
		{
			// Something went wrong...return errors (probably from 'check')
			$this->addPluginMessage($records->getError(), 'error');

			// Add a few things to the records object to pass back to the edit view
			$records->hid     = '';
			$records->pactive = '';
			$records->uname   = $this->juser->get('name');
			return $this->_edit($records);
		}

		// If saving a record from a page other than the first, take the user back to that page after saving
		$start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=records' . $start),
			JText::_('PLG_TIME_RECORDS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete records
	 * 
	 * @return void
	 */
	private function _delete()
	{
		// Incoming posted data
		$record = JRequest::getInt('id');

		// If delete a record from a page other than the first, take the user back to that page after deletion
		$start = ($this->mainframe->getUserState("$this->option.$this->active.start") != 0) 
			? '&start='.$this->mainframe->getUserState("$this->option.$this->active.start") 
			: '';

		// Create object and store new content
		$records = new TimeRecords($this->db);
		$records->load($record);

		// Only allow creator of the record to edit or delete
		if($records->user_id != $this->juser->get('id'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=records'),
				JText::_('PLG_TIME_RECORDS_WARNING_CANT_DELETE_OTHER'),
				'warning'
			);
		}

		// If record is marked as billed, don't allow deletion
		if($records->billed == 1)
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&active=records'),
				JText::_('PLG_TIME_RECORDS_WARNING_CANT_DELETE_BILLED'),
				'warning'
			);
		}

		// Delete record
		$records->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&active=records' . $start),
			JText::_('PLG_TIME_RECORDS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Set redirect
	 * 
	 * @return void
	 */
	private function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}