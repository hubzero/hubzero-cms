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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Developer\Models\Application\Member;
use Components\Developer\Models\Application;
use Request;
use Notify;
use Lang;
use User;
use Date;
use App;

/**
 * Developer controller for applications
 */
class Applications extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'sort'     => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			)),
			'sort_Dir' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			))
		);

		$entries = Application::all();

		// Get records
		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// This is a flag to disable the main menu. This makes sure the user
		// doesn't navigate away while int he middle of editing an entry.
		// To leave the form, one must explicitely call the "cancel" task.
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Grab the incoming ID and load the record for editing
			//
			// IDs can come arrive in two formts: single integer or 
			// an array of integers. If it's the latter, we'll only take 
			// the first ID in the list.
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Application::oneOrNew($id);
		}

		// If this is a new record, we'll set the creator data
		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('created', Date::of('now')->toSql());
		}

		// Output the view
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$team   = Request::getVar('team', '', 'post', 2, 'none');

		// Bind the incoming data to our mdoel
		$row = Application::oneOrNew($fields['id'])->set($fields);

		// Validate and save the data
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// parse incoming team
		$team = array_map('trim', explode(',', $team));

		// clean up team
		foreach ($team as $k => $t)
		{
			// handle usernames & emails
			if (!is_numeric($t))
			{
				// handle emails
				if (strpos($t, '@'))
				{
					// load profile by email
					$profile = \Hubzero\User\User::oneByEmail($t);
				}
				else
				{
					// load profile by username
					$profile = \Hubzero\User\User::oneOrNew($t);
				}

				// swap usernames for uidnumbers
				if ($profile)
				{
					$team[$k] = $profile->get('id');
				}
				else
				{
					unset($team[$k]);
				}
			}
		}

		// add creator if new
		// will only ever get added once
		$team[] = User::get('id');

		// get current team
		$currentTeam = $row->team()->rows();

		$found = array();

		// Remove members not included now
		foreach ($currentTeam as $member)
		{
			if (!in_array($member->get('uidNumber'), $team))
			{
				$member->destroy();
			}

			$found[] = $member->get('uidNumber');
		}

		// Add each non-team member to team
		foreach ($team as $uidNumber)
		{
			if (!in_array($uidNumber, $found))
			{
				$member = Member::blank();
				$member->set('uidNumber', $uidNumber);
				$member->set('application_id', $row->get('id'));
				$member->save();
			}
		}

		Notify::success(Lang::txt('COM_DEVELOPER_APPLICATION_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) > 0)
		{
			$i = 0;

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				// Get the model for this entry
				$entry = Application::oneOrFail(intval($id));

				// Delete the entry
				if (!$entry->destroy())
				{
					Notify::error($entry->getError());
					continue;
				}

				$i++;
			}
		}

		// Set the redirect URL to the main entries listing.
		if ($i)
		{
			Notify::success(Lang::txt('COM_DEVELOPER_APPLICATIONS_DELETED'));
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			Notify::warning(Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task));

			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;

		foreach ($ids as $id)
		{
			// Load the entry and set its state
			$row = Application::oneOrFail(intval($id));
			$row->set('state', $state);

			// Store the changes
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			// Here, we're countign the number of successful state changes
			// so we can display that number in a message when we're done.
			$success++;
		}

		if ($success)
		{
			// Get the appropriate message for the task called. We're
			// passing in the number of successful state changes so it
			// can be displayed in the message.
			switch ($this->_task)
			{
				case 'publish':
					$message = Lang::txt('COM_DEVELOPER_APPLICATION_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_DEVELOPER_APPLICATION_UNPUBLISHED', $success);
				break;
				case 'archive':
					$message = Lang::txt('COM_DEVELOPER_APPLICATION_ARCHIVED', $success);
				break;
			}

			// Set the redirect URL to the main entries listing.
			Notify::success($message);
		}

		$this->cancelTask();
	}

	/**
	 * Regenerate Client Id & Secret for application
	 * 
	 * @return  void
	 */
	public function resetClientSecretTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			Notify::warning(Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task));

			return $this->cancelTask();
		}

		$i = 0;

		// loop through each id
		foreach ($ids as $id)
		{
			// Load the entry and set its state
			$row = Application::oneOrFail(intval($id));

			// generate new client secret
			$row->set('client_secret', $row->newClientSecret());

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// Set the redirect URL to the main entries listing.
		if ($i)
		{
			Notify::success(Lang::txt('COM_DEVELOPER_REGENERATE_CLIENT_ID_AND_SECRET_SUCCESS'));
		}

		$this->cancelTask();
	}

	/**
	 * Remove any existing tokens for applications
	 * 
	 * @return  void
	 */
	public function removeTokensTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			Notify::warning(Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task));

			return $this->cancelTask();
		}

		// loop through each application id
		foreach ($ids as $id)
		{
			// Load the entry and revoke tokens/codes
			$row = Application::oneOrFail(intval($id));
			$row->revokeAccessTokens();
			$row->revokeRefreshTokens();
			$row->revokeAuthorizationCodes();
		}

		// Set the redirect URL to the main entries listing.
		Notify::success(Lang::txt('COM_DEVELOPER_REVOKE_TOKENS_SUCCESS'));

		$this->cancelTask();
	}
}
