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

namespace Components\Developer\Site\Controllers;

use Components\Developer\Models\Application\Member;
use Components\Developer\Models\Application;
use Components\Developer\Models\Accesstoken;
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
		$applications = Application::all()
			->whereEquals('created_by', User::get('id'))
			->whereIn('state', array(0,1))
			->rows();

		// get developers authorized apps
		$tokens = Accesstoken::all()
			->whereEquals('uidNumber', User::get('id'))
			->whereIn('state', array(1))
			->rows();

		// build pathway
		$this->_buildPathway();
		$this->_buildTitle();

		// render view
		$this->view
			->set('applications', $applications)
			->set('tokens', $tokens)
			->display();
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

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=view&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get developers apps
		$application = Application::oneOrFail($id);

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
		$this->view
			->set('application', $application)
			->display();
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
		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// check to see if we are passing in a model
		// most likely from a failed save attempt
		if (!($application instanceof Application))
		{
			// Grab the incoming ID and load the record for editing
			$id = Request::getInt('id', 0);

			$application = Application::oneOrNew($id);
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

		// make sure its ours
		// or we can create
		if (!$this->config->get('access-edit-application', 0)
		 && !$this->config->get('access-create-application', 0)
		 && $id > 0)
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
			->set('application', $application)
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
		Request::checkToken();

		// get request vars
		$data = Request::getVar('application', array(), 'post', 2, 'none');
		$team = Request::getVar('team', array(), 'post', 2, 'none');

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $data['id'], false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// bind data to model
		$model = Application::oneOrNew($data['id'])->set($data);

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
		 && !$this->config->get('access-create-application', 0)
		 && $data['id'] > 0)
		{
			App::redirect(
				Route::url('index.php?option=com_developer&controller=applications'),
				Lang::txt('COM_DEVELOPER_API_APPLICATION_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// attempt to save model
		if (!$model->save())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
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
		$found = array();
		foreach ($model->team()->rows() as $member)
		{
			$found[] = $member->get('uidNumber');
		}

		// Add each non-team member to team
		foreach ($team as $uidNumber)
		{
			if (!in_array($uidNumber, $found))
			{
				$member = Member::blank();
				$member->set('uidNumber', $uidNumber);
				$member->set('application_id', $model->get('id'));
				$member->save();
			}
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
		Request::checkToken();

		// get the app id
		$id = Request::getInt('id', 0);

		// must be logged in
		if (User::isGuest())
		{
			$return = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=delete&id=' . $id, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// get developers apps
		$application = Application::oneOrFail($id);

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
		$application->set('state', Application::STATE_DELETED);

		if (!$application->save())
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
		Request::checkToken();

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
		$application = Application::oneOrFail($id);

		// generate new client secret
		$clientSecret = $application->newClientSecret();

		// set our new values on application & store
		$application->set('client_secret', $clientSecret);

		if (!$application->save())
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
		Request::checkToken('get');

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
		$accessToken = Accesstoken::oneOrFail($token);

		// delete the access token
		if ($accessToken->get('application_id') == $id)
		{
			$accessToken->destroy();
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
		Request::checkToken('get');

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
		$application = Application::oneOrFail($id);

		// expire access tokens
		$application->revokeAccessTokens();

		// expire refresh tokens
		$application->revokeRefreshTokens();

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
		$application = Application::oneOrFail($id);

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
		$team = $application->team()->rows();

		foreach ($team as $member)
		{
			if ($member->get('uidNumber') == $uidNumber)
			{
				// delete team member
				if (!$member->destroy())
				{
					App::redirect(
						Route::url($application->link('edit')),
						Lang::txt('COM_DEVELOPER_API_APPLICATION_UNABLE_TO_DELETE_MEMBER'),
						'error'
					);
					return;
				}
			}
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
			$app = Application::oneOrNew($assetId);

			$team = array();
			foreach ($app->team()->rows() as $member)
			{
				$team[] = $member->get('uidNumber');
			}

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
			$application = Application::oneOrFail($appid);

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