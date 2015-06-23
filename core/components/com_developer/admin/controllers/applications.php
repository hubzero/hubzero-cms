<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Chris Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Developer\Models;
use Request;
use Config;
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
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			'state'    => array(0,1,2), //all states
			'sort'     => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			)),
			'sort_Dir' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)),
			'limit'    => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start'    => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get our model
		// This is the entry point to the database and the 
		// table of characters we'll be retrieving data from
		$model = new Models\Developer();

		// Get a total record count
		// This is used for pagination and detemrining the total number of pages
		$this->view->total = $model->applications('count', $this->view->filters);

		// Get a list of records
		$this->view->rows  = $model->applications('list', $this->view->filters);

		// Output the view
		$this->view->display();
	}

	/**
	 * Create a new entry
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row  DrwhoModelSeason
	 * @return  void
	 */
	public function editTask($row=null)
	{
		// This is a flag to disable the main menu. This makes sure the user
		// doesn't navigate away while int he middle of editing an entry.
		// To leave the form, one must explicitely call the "cancel" task.
		Request::setVar('hidemainmenu', 1);

		// If we're being passed an object, use it instead
		// Thsi means we came from saveTask() and some error occurred.
		// Most likely a missing or incorrect field.
		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
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

			$this->view->row = new Models\Api\Application($id);
		}

		// If this is a new record, we'll set the creator data
		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', User::get('id'));
			$this->view->row->set('created', Date::of('now')->toSql());
		}

		// Get the show model.
		// We will need this in the form to output a list of seasons.
		$this->view->model = new Models\Developer();

		// Pass any received errors to the view
		// These will be coming from the editTask()
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the view
		$this->view
		     ->setLayout('edit')
		     ->display();
	}

	/**
	 * Save an entry and show the edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @param      boolean $redirect Redirect after save?
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$team   = Request::getVar('team', '', 'post', 2, 'none');

		// Bind the incoming data to our mdoel
		$row = new Models\Api\Application($fields);

		// Validate and save the data
		if (!$row->store(true))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
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
					$profile = \Hubzero\User\Profile\Helper::find_by_email($t);
				}
				else
				{
					// load profile by username
					$profile = \Hubzero\User\Profile::getInstance($t);
				}

				// swap usernames for uidnumbers
				if ($profile)
				{
					$team[$k] = $profile->get('uidNumber');
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
		$currentTeam = $row->team()->lists('uidNumber');

		// remove members not included now
		foreach (array_diff($currentTeam, $team) as $uidNumber)
		{
			$member = $row->team($uidNumber);
			$member->delete();
		}

		// add each non-team member to team
		foreach (array_diff($team, $currentTeam) as $uidNumber)
		{
			if ($uidNumber < 1)
			{
				continue;
			}

			// new team member object
			$teamMember = new Models\Api\Application\Team\Member(array(
				'uidNumber'      => $uidNumber,
				'application_id' => $row->get('id')
			));
			$teamMember->store();
		}

		// Are we redirecting?
		// This will happen if a user clicks the "save & close" button.
		if ($redirect)
		{
			// Set the redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_DEVELOPER_APPLICATION_SAVED')
			);
			return;
		}

		// Display the edit form. This will happen if the user clicked
		// the "save" or "apply" button.
		$this->editTask($row);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) > 0)
		{
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				// Get the model for this entry
				$entry = new Models\Api\Application(intval($id));

				// Delete the entry
				if (!$entry->delete())
				{
					// If the deletion process fails for any reason, we'll take the 
					// error message passed from the model and assign it to the message
					// handler to be displayed by the template after we redirect back
					// to the main listing.
					Notify::error($entry->getError());
				}
			}
		}

		// Set the redirect URL to the main entries listing.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_DEVELOPER_APPLICATIONS_DELETED')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the entry and set its state
			$row = new Models\Api\Application(intval($id));
			$row->set('state', $state);

			// Store the changes
			if (!$row->store())
			{
				// If the store() process fails for any reason, we'll take the 
				// error message passed from the model and assign it to the message
				// handler to be displayed by the template after we redirect back
				// to the main listing.
				Notify::error($row->getError());
				continue;
			}

			// Here, we're countign the number of successful state changes
			// so we can display that number in a message when we're done.
			$success++;
		}

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
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}

	/**
	 * Regenerate Client Id & Secret for application
	 * 
	 * @return void
	 */
	public function resetClientSecretTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task),
				'error'
			);
			return;
		}

		// loop through each id
		foreach ($ids as $id)
		{
			// Load the entry and set its state
			$row = new Models\Api\Application(intval($id));

			// generate new client secret
			$clientSecret = $row->newClientSecret();

			// set our new value on application & store
			$row->set('client_secret', $clientSecret);
			$row->store(false);
		}

		// Set the redirect URL to the main entries listing.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_DEVELOPER_REGENERATE_CLIENT_ID_AND_SECRET_SUCCESS')
		);
	}

	/**
	 * Remove any existing tokens for applications
	 * 
	 * @return void
	 */
	public function removeTokensTask()
	{
		// [SECURITY] Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_DEVELOPER_SELECT_APPLICATION_TO', $this->_task),
				'error'
			);
			return;
		}

		// loop through each application id
		foreach ($ids as $id)
		{
			// Load the entry and revoke tokens/codes
			$row = new Models\Api\Application(intval($id));
			$row->revokeAccessTokens();
			$row->revokeRefreshTokens();
			$row->revokeAuthorizationCodes();
		}

		// Set the redirect URL to the main entries listing.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_DEVELOPER_REVOKE_TOKENS_SUCCESS')
		);
	}
}