<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Site\Controllers;

use Components\Time\Models\Hub;
use Components\Time\Models\Contact;

/**
 * Hubs controller for time component
 */
class Hubs extends Base
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
			$hub = Hub::oneOrNew(Request::getInt('id'));
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
		$this->view->row   = Hub::oneOrFail(Request::getInt('id'));
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
		$hub = Hub::oneOrNew(Request::getInt('id'))->set(array(
			'name'             => Request::getVar('name'),
			'liaison'          => Request::getVar('liaison'),
			'anniversary_date' => Request::getVar('anniversary_date'),
			'notes'            => Request::getVar('notes', null, 'post', 'none', JREQUEST_ALLOWRAW),
			'support_level'    => Request::getVar('support_level')
		));

		$contacts = array();
		// Set the contact info on the hub
		foreach (Request::getVar('contacts', array(), 'post') as $contact)
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

		$allotments = array();
		// Set the allotment info on the hub
		foreach (Request::getVar('allotments', array(), 'post') as $allotment)
		{
			// First check and make sure we don't save a completely empty allotment
			if (empty($allotment['start_date'])
			 && empty($allotment['end_date'])
			 && empty($allotment['hours']))
			{
				break;
			}

			$allotments[] = Allotment::oneOrNew(isset($allotment['id']) ? $allotment['id'] : 0)->set($allotment);
		}

		$hub->attach('allotments', $allotments);

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
		App::redirect(
			Route::url($this->base . $this->start($hub)),
			Lang::txt('COM_TIME_HUBS_SAVE_SUCCESSFUL'),
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
		$hub = Hub::oneOrFail(Request::getInt('id'));

		// If there are active tasks, don't allow deletion
		if ($hub->tasks->count() > 0)
		{
			App::redirect(
				Route::url($this->base . '&task=readonly&id=' . $hub->get('id')),
				Lang::txt('COM_TIME_HUBS_DELETE_HAS_ASSOCIATED_TASKS'),
				'warning'
			);
			return;
		}

		// Delete the contacts first
		if (!$hub->contacts->destroyAll())
		{
			App::redirect(
				Route::url($this->base . '&task=readonly&id=' . $hub->get('id')),
				Lang::txt('COM_TIME_HUBS_DELETE_CONTACTS_FAILED'),
				'warning'
			);
			return;
		}

		// Now delete the actual hub
		if (!$hub->destroy())
		{
			App::redirect(
				Route::url($this->base . '&task=readonly&id=' . $hub->get('id')),
				Lang::txt('COM_TIME_HUBS_DELETE_FAILED'),
				'warning'
			);
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url($this->base . $this->start($hub)),
			Lang::txt('COM_TIME_HUBS_DELETE_SUCCESSFUL'),
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
		$contact = Contact::oneOrFail(Request::getInt('id'));

		// Get the hub id for the return
		$hid = $contact->hub_id;

		// Delete the contact
		$contact->destroy();

		// Set the redirect
		App::redirect(
			Route::url($this->base . '&task=edit&id=' . $hid),
			Lang::txt('COM_TIME_HUBS_CONTACT_DELETE_SUCCESSFUL'),
			'passed'
		);
	}

	/**
	 * Delete an allotment
	 *
	 * @return void
	 */
	public function deleteallotmentTask()
	{
		$allotment = Allotment::oneOrFail(Request::getInt('id'));

		// Get the hub id for the return
		$hid = $allotment->hub_id;

		// Delete the contact
		$allotment->destroy();

		// Set the redirect
		App::redirect(
			Route::url($this->base . '&task=edit&id=' . $hid),
			Lang::txt('COM_TIME_HUBS_ALLOTMENT_DELETE_SUCCESSFUL'),
			'passed'
		);
	}
}