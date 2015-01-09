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
		// Display
		$this->view->rows = Hub::all()->paginated()->ordered();
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
	 * @param  object $hub optional incoming hub data to load (in event of save failing)
	 * @return void
	 */
	public function editTask($hub=null)
	{
		// If we already have a hub, use it, otherwise, instanciate a new object with the request variable
		if (!isset($hub) || !is_object($hub))
		{
			$hub = Hub::oneOrNew(JRequest::getInt('id'));
		}

		// Display
		$this->view->row   = $hub;
		$this->view->start = $this->start($hub);
		$this->view->display();
	}

	/**
	 * View/read-only hub entry
	 *
	 * @return void
	 */
	public function readonlyTask()
	{
		// Display
		$this->view->row   = Hub::oneOrFail(JRequest::getInt('id'));
		$this->view->start = $this->start($this->view->row);
		$this->view->display();
	}

	/**
	 * Save hub and redirect to the hubs page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		$hub = Hub::oneOrNew(JRequest::getInt('id'))->set(array(
			'name'             => JRequest::getVar('name'),
			'liaison'          => JRequest::getVar('liaison'),
			'anniversary_date' => JRequest::getVar('anniversary_date'),
			'notes'            => JRequest::getVar('notes', null, 'post', 'none', JREQUEST_ALLOWRAW),
			'support_level'    => JRequest::getVar('support_level')
		));

		$contacts = array();
		// Set the contact info on the hub
		foreach (JRequest::getVar('contacts', array(), 'post') as $contact)
		{
			// First check and make sure we don't save a completely empty contact
			if (empty($contact['name'])
			 && empty($contact['phone'])
			 && empty($contact['email'])
			 && empty($contact['role']))
			{
				break;
			}

			$contacts[] = Contact::oneOrNew(isset($contact['id']) ? $contact['id'] : 0)->set($contact);
		}

		$hub->attach('contacts', $contacts);

		// Save the hub info
		if (!$hub->saveAndPropagate())
		{
			// Something went wrong...return errors
			foreach ($hub->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($hub);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($hub)),
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
		// Get model
		$hub = Hub::oneOrFail(JRequest::getInt('id'));

		// If there are active tasks, don't allow deletion
		if ($hub->tasks->count() > 0)
		{
			$this->setRedirect(
				JRoute::_($this->base . '&task=readonly&id=' . $hub->get('id')),
				JText::_('COM_TIME_HUBS_DELETE_HAS_ASSOCIATED_TASKS'),
				'warning'
			);
			return;
		}

		// Delete the contacts first
		if (!$hub->contacts->destroyAll())
		{
			$this->setRedirect(
				JRoute::_($this->base . '&task=readonly&id=' . $hub->get('id')),
				JText::_('COM_TIME_HUBS_DELETE_CONTACTS_FAILED'),
				'warning'
			);
			return;
		}

		// Now delete the actual hub
		if (!$hub->destroy())
		{
			$this->setRedirect(
				JRoute::_($this->base . '&task=readonly&id=' . $hub->get('id')),
				JText::_('COM_TIME_HUBS_DELETE_FAILED'),
				'warning'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . $this->start($hub)),
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
		$contact = Contact::oneOrFail(JRequest::getInt('id'));

		// Get the hub id for the return
		$hid = $contact->hub_id;

		// Delete the contact
		$contact->destroy();

		// Set the redirect
		$this->setRedirect(
			JRoute::_($this->base . '&task=edit&id=' . $hid),
			JText::_('COM_TIME_HUBS_CONTACT_DELETE_SUCCESSFUL'),
			'passed'
		);
	}
}