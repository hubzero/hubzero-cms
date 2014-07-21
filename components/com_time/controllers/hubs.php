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

/**
 * Hubs controller for time component
 */
class TimeControllerHubs extends TimeControllerBase
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Instantiate hubs class
		$hubs = new TimeHubs($this->database);
		$app  = JFactory::getApplication();

		// Get the total number of hubs (for pagination)
		$this->view->total               = $hubs->getCount();
		$this->view->filters['start']    = (JRequest::getInt('start')) ? JRequest::getInt('start') : JRequest::getInt('limitstart', 0);
		$this->view->filters['limit']    = JRequest::getInt('limit', $app->getUserState("{$this->_option}.{$this->_controller}.limit"));
		$this->view->filters['orderby']  = JRequest::getVar('orderby', $app->getUserState("{$this->_option}.{$this->_controller}.orderby"));
		$this->view->filters['orderdir'] = JRequest::getVar('orderdir', $app->getUserState("{$this->_option}.{$this->_controller}.orderdir"));

		// Set sort order, sort direction, and start in session
		$app->setUserState("{$this->_option}.{$this->_controller}.start", $this->view->filters['start']);
		$app->setUserState("{$this->_option}.{$this->_controller}.limit", $this->view->filters['limit']);
		$app->setUserState("{$this->_option}.{$this->_controller}.orderby", $this->view->filters['orderby']);
		$app->setUserState("{$this->_option}.{$this->_controller}.orderdir", $this->view->filters['orderdir']);

		// Initiate pagination
		jimport('joomla.html.pagination');

		// Set the list limit to 10 by default, if not set otherwise
		$this->view->filters['limit'] = (empty($this->view->filters['limit'])) ? 10 : $this->view->filters['limit'];

		// Navigation
		$pageNav             = new JPagination($this->view->total, $this->view->filters['start'], $this->view->filters['limit']);
		$this->view->pageNav = $pageNav->getListFooter();

		// Get the hubs
		$this->view->hubs = $hubs->getRecords($this->view->filters);

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		// Display
		$this->view->display();
	}

	/**
	 * New task
	 *
	 * @return void
	 */
	public function newTask()
	{
		$this->view->setLayout('edit');
		$this->view->task = 'edit';
		$this->editTask();
	}

	/**
	 * New/Edit function
	 *
	 * @return void
	 */
	public function editTask($hub=null, $contacts=null)
	{
		// If we already have a hub, use it, otherwise, instanciate a new object with the request variable
		if (isset($hub) && is_object($hub))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$this->view->row = $hub;
		}
		else
		{ // Create a new object (i.e. we're coming in clean)
			// Get the id if we're editing a hub
			$hid = JRequest::getInt('id');

			$hub = new TimeHubs($this->database);
			$hub->load($hid);
			$this->view->row = $hub;
		}

		$obj = new TimeModelHub($this->view->row);

		// Parse the notes for the view
		$this->view->row->notes = $obj->notes('raw');

		// Check if we have a contacts array coming in - if so, use that
		if (isset($contacts) && is_array($contacts))
		{
			// Use the prexisting object (i.e. we had an error when saving)
			$this->view->contacts = $contacts;
		}
		else
		{
			// Get the contacts for the hub (only if we're editing)
			$this->view->contacts = array();
			if (!empty($this->view->row->id))
			{
				$contacts                = new TimeContacts($this->database);
				$filters['hub_id']       = $this->view->row->id;
				$this->view->contacts    = $contacts->getRecords($filters);
			}
		}

		// Create support level list
		$this->view->slist = TimeHTML::buildSupportLevelList($this->view->row->support_level);

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$app = JFactory::getApplication();
		$this->view->start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Set a few things for the vew
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		// Display
		$this->view->display();
	}

	/**
	 * View/read-only hub entry
	 *
	 * @return void
	 */
	public function readonlyTask()
	{
		// Get the id if we're editing a hub
		$hid = JRequest::getInt('id');
		$app = JFactory::getApplication();

		// Create a new object
		$hub = new TimeHubs($this->database);
		$hub->load($hid);
		$this->view->row = $hub;

		// Get the contacts for the hub
		$contacts                = new TimeContacts($this->database);
		$filters['hub_id']       = $this->view->row->id;
		$this->view->contacts    = $contacts->getRecords($filters);

		// Get the count of active tasks
		$tasks              = new TimeTasks($this->database);
		$tFilters['limit']  = 1000;
		$tFilters['start']  = 0;
		$tFilters['hub']    = $this->view->row->id;
		$tFilters['active'] = 1;

		$q['q'][0]['column'] = 'hub_id';
		$q['q'][0]['o']      = '=';
		$q['q'][0]['value']  = $this->view->row->id;
		$q['q'][1]['column'] = 'active';
		$q['q'][1]['o']      = '=';
		$q['q'][1]['value']  = 1;

		$this->view->activeTasks  = $tasks->getCount($q);

		// Get the summary hours for the hub
		$records                = new TimeRecords($this->database);
		$hours                  = $records->getSummaryHoursByHub(array('hub'=>$this->view->row->id, 'limit'=>1));
		$this->view->totalHours = ($hours) ? $hours[0]->hours : 0;

		$obj = new TimeModelHub($this->view->row);

		// Parse the notes for the view
		$this->view->row->notes = $obj->notes('parsed');

		// If viewing an entry from a page other than the first, take the user back to that page if they click "all xxx"
		$this->view->start = ($app->getUserState("{$this->_option}.{$this->_controller}.start") != 0)
			? '&start='.$app->getUserState("{$this->_option}.{$this->_controller}.start")
			: '';

		// Set a few things for the vew
		$this->_buildPathway();
		$this->view->title = $this->_buildTitle();

		// Display
		$this->view->display();
	}

	/**
	 * Save new hub and redirect to the hubs page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Incoming posted data
		$hub      = JRequest::getVar('hubs', array(), 'post', 'none', 2);
		$hub      = array_map('trim', $hub);
		$contacts = JRequest::getVar('contacts', array(), 'post');

		// Create object(s)
		$hubs = new TimeHubs($this->database);

		// Create variables to capture errors
		$has_errors       = false;
		$contactsObjArray = array();

		// Save the hub info
		if (!$hubs->save($hub))
		{
			// Something went wrong...return errors (probably from 'check')
			$this->view->setError($hubs->getError());

			// Set the flag for errors
			$has_errors = true;
		}

		// Save the contacts info
		foreach ($contacts as $contact)
		{
			// Add the hub id to the contact array
			$contact['hub_id'] = $hubs->id;

			// Trim
			$contact = array_map('trim', $contact);

			// First check and make sure we don't save an empty contact
			if (empty($contact['name']) || empty($contact['phone']) || empty($contact['email']) || empty($contact['role']))
			{
				break;
			}

			// Create object
			$contactObj = new TimeContacts($this->database);

			// Save the contact info
			$contactObj->save($contact);

			// Add all contacts ojects to a new array to pass back to the edit view if necessary
			$contactsObjArray[] = $contactObj;
		}

		// If we had errors, redirect back to edit
		if ($has_errors == true)
		{
			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($hubs, $contactsObjArray);
			return;
		}

		// If saving a hub from a page other than the first, take the user back to that page after saving
		$startnum = JFactory::getApplication()->getUserState("{$this->_option}.{$this->_controller}.start");
		$start    = ($startnum != 0) ? '&start='.$startnum : '';

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_HUBS_SAVE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete hubs
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// Incoming posted data
		$hub = JRequest::getInt('id');

		// Check if the hub has an active tasks
		$tasks = new TimeTasks($this->database);
		$filters = array('q' => array());
		$filters['q'][0] = array(
			'column' => 'hub_id',
			'o'      => '=',
			'value'  => $hub
		);
		$count = $tasks->getCount($filters);

		// If delete a record from a page other than the first, take the user back to that page after deletion
		$startnum = JFactory::getApplication()->getUserState("{$this->_option}.{$this->_controller}.start");
		$start    = ($startnum != 0) ? '&start='.$startnum : '';

		// If there are active tasks, don't allow deletion
		if ($count > 0)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=readonly&id=' . $hub),
				JText::_('COM_TIME_HUBS_DELETE_HAS_ASSOCIATED_TASKS'),
				'warning'
			);
			return;
		}

		// Create object and load hub by id
		$hubs = new TimeHubs($this->database);
		$hubs->load($hub);

		// Get hub contacts
		$contacts          = new TimeContacts($this->database);
		$filters['hub_id'] = $hub;
		$contacts          = $contacts->getRecords($filters);

		// Delete contacts from the hub
		foreach ($contacts as $contact)
		{
			$ct = new TimeContacts($this->database);
			$ct->load($contact->id);

			// @FIXME: add logic for displaying any errors!
			$ct->delete();
		}

		// Delete the hub
		// @FIXME: add logic for displaying any errors!
		$hubs->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $start),
			JText::_('COM_TIME_HUBS_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete a contact
	 *
	 * @return void
	 */
	public function deletecontactTask()
	{
		// Incoming posted data
		$cid = JRequest::getInt('id');

		// Create object and load hub by id
		$contact = new TimeContacts($this->database);
		$contact->load($cid);

		// Get the hub id for the return
		$hid = $contact->hub_id;

		// Delete the hub
		// @FIXME: add logic for displaying any errors!
		$contact->delete();

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $hid),
			JText::_('COM_TIME_HUBS_CONTACT_DELETE_SUCCESSFUL'),
			'passed'
		);
	}
}