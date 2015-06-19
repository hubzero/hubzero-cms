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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Developer\Site\Controllers;

use Components\Developer\Models;
use Hubzero\Component\SiteController;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Developer Applications Controller
 */
class Applications extends SiteController
{
	/**
	 * Override execute method to init developer model
	 * 
	 * @return  void
	 */
	public function execute()
	{
		// create new developer model
		$this->developer = new Models\Developer();

		// authorize application usage
		$this->_authorize('application', Request::getInt('id', null));

		// call parent execute
		parent::execute();
	}

	/**
	 * List developer applications
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get developers apps
		$this->view->applications = $this->developer->applications('list', array(
			'uidNumber' => User::get('id'),
			'state'     => array(0,1)
		));

		// get developers authorized apps
		$this->view->token = $this->developer->accessTokens('list', array(
			'uidNumber' => User::get('id'),
			'state'     => array(1)
		));

		// build pathway
		$this->_buildPathway();
		$this->_buildTitle();

		// render view
		$this->view->display();
	}

	/**
	 * View specific developer application
	 * 
	 * @return  void
	 */
	public function viewTask()
	{
		// get the app id
		$id = Request::getInt('id', 0);

		// get developers apps
		$this->view->application = $this->developer->application($id);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=view&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// is the app available
		if ($this->view->application->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_DOES_NOT_EXIST'),
				'warning'
			);
			return;
		}

		// make sure its ours
		if (!$this->config->get('access-view-application', 0))
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// build pathway
		$this->_buildPathway();

		// render view
		$this->view->display();
	}

	/**
	 * Create a new developer application
	 * 
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an existing developer application
	 * 
	 * @param   object  $application  Optional application model returned from save
	 * @return  void
	 */
	public function editTask($application = null)
	{
		// check to see if we are passing in a model
		// most likely from a failed save attempt
		if ($application instanceof Models\Api\Application)
		{
			$this->view->application = $application;
		}
		else
		{
			// Grab the incoming ID and load the record for editing
			$id = Request::getInt('id', 0);

			$this->view->application = new Models\Api\Application($id);
		}

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// is the app available
		if ($this->view->application->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_DOES_NOT_EXIST'),
				'warning'
			);
			return;
		}

		// make sure its ours
		// or we can create
		if (!$this->config->get('access-edit-application', 0)
			&& (!$this->config->get('access-create-application', 0) && $id > 0))
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Pass any received errors to the view
		// These will be coming from the editTask()
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// build pathway
		$this->_buildPathway();

		// render view
		// forcing edit view
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save developer application details
	 * 
	 * @return  void
	 */
	public function saveTask()
	{
		// CSRF check
		Request::checkToken() or exit('Invalid token');

		// get request vars
		$data = Request::getVar('application', array(), 'post', 2, 'none');
		$team = Request::getVar('team', array(), 'post', 2, 'none');

		// bind data to model
		$model = new Models\Api\Application($data);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $data['id'], false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// is the app available
		if ($model->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_DOES_NOT_EXIST'),
				'warning'
			);
			return;
		}

		// make sure its ours
		if (!$this->config->get('access-edit-application', 0)
			&& (!$this->config->get('access-create-application', 0) && $data['id'] > 0))
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// attempt to save model
		if (!$model->store(true))
		{
			$this->setError($model->getError());
			$this->editTask($model);
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
		$currentTeam = $model->team()->lists('uidNumber');

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
				'application_id' => $model->get('id')
			));
			$teamMember->store();
		}

		// Redirect back to the main listing with a success message
		App::redirect(
			Route::url($model->link()),
			Lang::txt('COM_DEVELOPER_API_APPLICATION_SAVED'),
			'passed'
		);
	}

	/**
	 * Delete developer application
	 * 
	 * @return  void
	 */
	public function deleteTask()
	{
		// CSRF check
		Request::checkToken() or exit('Invalid token');

		// get the app id
		$id = Request::getInt('id', 0);

		// get developers apps
		$application = $this->developer->application($id);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=delete&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// is the app available
		if ($application->isDeleted())
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_DOES_NOT_EXIST'),
				'warning'
			);
			return;
		}

		// make sure we have access to delete
		if (!$this->config->get('access-delete-application', 0))
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// attempt to delete app
		if (!$application->delete())
		{
			App::redirect(
				Route::url($application->link()),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_UNABLE_TO_DELETE'),
				'error'
			);
			return;
		}

		// Redirect back to the main listing with a success message
		App::redirect(
			Route::url('index.php?option=com_developer&controller=applications'),
			Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETED'),
			'passed'
		);
	}

	/**
	 * Generate a new client id & secret
	 * 
	 * @return  void
	 */
	public function resetClientSecretTask()
	{
		// CSRF check
		Request::checkToken() or exit('Invalid token');

		// get the app id
		$id = Request::getInt('id', 0);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=view&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get developers app
		$application = $this->developer->application($id);

		// generate new client secret
		$clientSecret = $application->newClientSecret();

		// set our new values on application & store
		$application->set('client_secret', $clientSecret);
		if (!$application->store(false))
		{
			App::redirect(
				Route::url($application->link()),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_UNABLE_TO_RESET_CLIENT_SECRET'),
				'error'
			);
			return;
		}

		// Redirect back the application
		App::redirect(
			Route::url($application->link()),
			Lang::txt('COM_DEVELOPER_API_APPLICATION_CLIENT_SECRET_RESET'),
			'passed'
		);
	}

	/**
	 * Revoke application token
	 * 
	 * @return  void
	 */
	public function revokeTask()
	{
		// CSRF check
		Request::checkToken('get') or exit('Invalid token');

		// get the app id
		$id    = Request::getInt('id', 0);
		$token = Request::getInt('token', 0);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=view&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get access tokens apps
		$accessToken = $this->developer->accessToken($token);

		// delete the access token
		if ($accessToken->get('application_id') == $id)
		{
			$accessToken->delete();
		}

		$return = Route::url('index.php?option=com_developer&controller=applications');
		if (Request::getvar('return') == 'tokens')
		{
			$return = Route::url('index.php?option=com_developer&controller=applications&id=' . $id . '&active=tokens');
		}

		// Redirect back to the main listing with a success message
		App::redirect(
			$return,
			Lang::txt('COM_DEVELOPER_API_APPLICATION_AUTHORIZED_REVOKED'),
			'passed'
		);
	}

	/**
	 * Revoke all tokens for an application
	 * 
	 * @return  void
	 */
	public function revokeAllTask()
	{
		// CSRF check
		Request::checkToken('get') or exit('Invalid token');

		// get the app id
		$id = Request::getInt('id', 0);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=view&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get access tokens apps
		$accessTokens = $this->developer->accessTokens('list', array(
			'application_id' => $id
		));

		// get access tokens apps
		$refreshTokens = $this->developer->refreshTokens('list', array(
			'application_id' => $id
		));

		// expire access tokens
		$accessTokens->map(function($token)
		{
			$token->delete();
		});

		// expire refresh tokens
		$refreshTokens->map(function($token)
		{
			$token->delete();
		});

		// Redirect back to the main listing with a success message
		App::redirect(
			Route::url('index.php?option=com_developer&controller=applications&id=' . $id . '&active=tokens'),
			Lang::txt('COM_DEVELOPER_API_APPLICATION_AUTHORIZED_REVOKED'),
			'passed'
		);
	}

	/**
	 * Remove member from app team
	 * 
	 * @return  void
	 */
	public function removeMemberTask()
	{
		// get request vars
		$id        = Request::getInt('id', 0);
		$uidNumber = Request::getInt('uidNumber', 0);

		// get the app
		$application = $this->developer->application($id);

		// make sure we can remove members from app
		if (!$this->config->get('access-remove-member-application', 0))
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// get team member
		if (!$member = $application->team($uidNumber))
		{
			App::redirect(
				Route::url($application->link('edit')),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_MEMBER_NOT_FOUND'),
				'warning'
			);
			return;
		}

		// delete team member
		if (!$member->delete())
		{
			App::redirect(
				Route::url($application->link('edit')),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_UNABLE_TO_DELETE_MEMBER'),
				'error'
			);
			return;
		}

		// Redirect back to the main listing with a success message
		App::redirect(
			Route::url($application->link('edit')),
			Lang::txt('COM_DEVELOPER_API_APPLICATION_MEMBER_DELETED'),
			'passed'
		);
	}

	/**
	 * Set the authorization level for the user
	 *
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  void
	 */
	protected function _authorize($assetType='application', $assetId=null)
	{
		// Logged in?
		if (!User::isGuest())
		{
			// Set comments to viewable
			$this->config->set('access-create-' . $assetType, true);
		}

		// do we have an application?
		if ($assetId != null)
		{
			$app = new Models\Api\Application($assetId);
			$team = $app->team()->lists('uidNumber');

			if (in_array(User::get('id'), $team) || User::get('id') == $app->get('created_by'))
			{
				// Set comments to viewable
				$this->config->set('access-view-' . $assetType, true);
				$this->config->set('access-edit-' . $assetType, true);
				$this->config->set('access-delete-' . $assetType, true);
				$this->config->set('access-remove-member-' . $assetType, true);
			}
		}
	}

	/**
	 * Build Breadcrumb Trail
	 * 
	 * @return  void
	 */
	protected function _buildPathway()
	{
		// create breadcrumbs
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		// add "API"
		Pathway::append(
			Lang::txt('COM_DEVELOPER_API'),
			'index.php?option=' . $this->_option . '&controller=api'
		);

		// add "Applications"
		Pathway::append(
			Lang::txt('COM_DEVELOPER_API_APPLICATIONS'),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);

		// do we have an application
		if ($appid = Request::getInt('id', 0))
		{
			$application = new Models\Api\Application($appid);

			// add "Applications"
			Pathway::append(
				$application->get('name'),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id=' . $appid
			);
		}

		// add task
		if (isset($this->_task)
			&& !in_array($this->_task, array('view', 'display', 'applications','granted')))
		{
			// add "Applications"
			Pathway::append(
				Lang::txt('COM_DEVELOPER_API_APPLICATION_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id=' . $appid . '&task=' . $this->_task
			);
		}

		// add active
		if ($active = Request::getCmd('active', null))
		{
			// add "Applications"
			Pathway::append(
				Lang::txt(ucfirst($active)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id=' . $appid . '&task=' . $this->_task
			);
		}
	}

	public function _buildTitle()
	{

	}
}