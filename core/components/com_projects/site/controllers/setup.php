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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Components\Projects\Tables;
use Components\Projects\Helpers;
use Components\Projects\Models\Orm\Description\Field;
use Components\Projects\Models\Orm\Description;
use Components\Projects\Models\Orm\Project as ProjectORM;
use Exception;
use Request;
use Route;
use User;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'description' . DS . 'field.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'description.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'project.php';

/**
 * Projects setup controller class
 */
class Setup extends Base
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('start', 'display');

		// Incoming
		$defaultSection = $this->_task == 'edit' ? 'info' : '';
		$this->section  = Request::getVar('active', $defaultSection);
		$this->group    = NULL;

		// Login required
		if (User::isGuest())
		{
			$this->_msg = $this->_task == 'edit' || !$this->_task
				? Lang::txt('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA')
				: Lang::txt('COM_PROJECTS_LOGIN_SETUP');
			$this->_login();
			return;
		}

		if (!User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.manage', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('ALERTNOTAUTH'),
				'warning'
			);
		}

		parent::execute();
	}

	/**
	 * Display setup screens
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->_task = 'setup';

		// Get project information
		if ($this->_identifier)
		{
			if (!$this->model->exists() || $this->model->isDeleted())
			{
				throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND'), 404);
			}
		}
		elseif (!$this->model->exists())
		{
			// Is user authorized to create a project?
			if (!$this->model->access('create'))
			{
				// Dispay error
				$this->setError(Lang::txt('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP'));
				$this->_showError();
				return;
			}

			$this->model->set('alias', Request::getVar('name', '', 'post'));
			$this->model->set('title', Request::getVar('title', '', 'post'));
			$this->model->set('about', trim(Request::getVar('about', '', 'post', 'none', 2)));
			$this->model->set('private', 1);
			$this->model->set('setup_stage', 0);
			$this->model->set('type', Request::getInt('type', 1, 'post'));
		}

		// Get group ID
		if ($this->_gid)
		{
			// Load the group
			$this->group = \Hubzero\User\Group::getInstance($this->_gid);

			// Ensure we found the group info
			if (!is_object($this->group) || (!$this->group->get('gidNumber') && !$this->group->get('cn')))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_NO_GROUP_FOUND'), 404);
			}
			$this->_gid = $this->group->get('gidNumber');
			$this->model->set('owned_by_group', $this->_gid);

			// Make sure we have up-to-date group membership information
			if ($this->model->exists())
			{
				$objO = $this->model->table('Owner');
				$objO->reconcileGroups($this->model->get('id'), $this->model->get('owned_by_group'), $this->model->get('sync_group'));
			}
			else
			{
				// Set group syncing to "undecided" state
				$this->model->set('sync_group', -1);
			}
		}

		// Check authorization
		if ($this->model->exists() && !$this->model->access('owner'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}
		elseif (!$this->model->exists() && $this->_gid)
		{
			// Check group authorization to create a project
			if (!$this->group->is_member_of('members', User::get('id'))
			 && !$this->group->is_member_of('managers', User::get('id')))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ALERTNOTAUTH_GROUP'), 403);
			}
		}

		// Determine setup steps
		$setupSteps = array('describe', 'team', 'finalize');
		if ($this->_setupComplete < 3)
		{
			array_pop($setupSteps);
		}

		// Send to requested page
		$step = $this->section ? array_search($this->section, $setupSteps) : NULL;
		$step = $step !== NULL && $step <= $this->model->get('setup_stage') ? $step : $this->model->get('setup_stage');

		if ($step < $this->_setupComplete)
		{
			$layout = $setupSteps[$step];
			$this->section = $layout;
		}
		else
		{
			// Setup complete, go to project page
			App::redirect(Route::url($this->model->link()));
			return;
		}

		// Set layout
		$this->view->setLayout($layout);

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		if ($this->section == 'team')
		{
			$this->view->content = $this->_loadTeamEditor();
		}

		// Output HTML
		$this->view->model    = $this->model;
		$this->view->step     = $step;
		$this->view->section  = $this->section;
		$this->view->title    = $this->title;
		$this->view->option   = $this->_option;
		$this->view->config   = $this->config;
		$this->view->extended = Request::getInt('extended', 0, 'post');

		// Get messages	and errors
		$this->view->msg = $this->_getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Save
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$step = Request::getInt('step', '0'); // Where do we go next?

		if ($this->_identifier && !$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// New project?
		$new = $this->model->exists() ? false : true;
		$setup = ($new || $this->model->inSetup()) ? true : false;

		// Is user authorized to create a project?
		if ($new && !$this->model->access('create'))
		{
			// Dispay error
			$this->setError(Lang::txt('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP'));
			$this->_showError();
			return;
		}

		// Determine setup steps
		$setupSteps = array('describe', 'team', 'finalize');
		if ($this->_setupComplete < 3)
		{
			array_pop($setupSteps);
		}

		// Next screen requested
		$this->next = $setup && isset($setupSteps[$step]) ? $setupSteps[$step] : $this->section;

		// Are we allowed to save this step?
		$current = array_search($this->section, $setupSteps);
		if ($new && $current > 0)
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Cannot save a new project unless in setup
		if ($new && !$setup)
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Get group ID
		if ($this->_gid)
		{
			// Load the group
			$this->group = \Hubzero\User\Group::getInstance($this->_gid);

			// Ensure we found the group info
			if (!is_object($this->group) || (!$this->group->get('gidNumber') && !$this->group->get('cn')))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_NO_GROUP_FOUND'), 404);
			}
			$this->_gid = $this->group->get('gidNumber');
			$this->model->set('owned_by_group', $this->_gid);

			// Make sure we have up-to-date group membership information
			if ($this->model->exists())
			{
				$objO = $this->model->table('Owner');
				$objO->reconcileGroups($this->model->get('id'), $this->model->get('owned_by_group'), $this->model->get('sync_group'));
			}
			else
			{
				// Set group syncing to "undecided" state
				$this->model->set('sync_group', -1);
			}
		}

		// Check authorization
		if ($this->model->exists() && !($this->model->access('owner') || $this->model->access('manager') || ($this->model->access('content') && $this->config->get('edit_description'))))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}
		elseif (!$this->model->exists() && $this->_gid)
		{
			// Check group authorization to create a project
			if (!$this->group->is_member_of('members', User::get('id'))
			 && !$this->group->is_member_of('managers', User::get('id')))
			{
				throw new Exception(Lang::txt('COM_PROJECTS_ALERTNOTAUTH_GROUP'), 403);
			}
		}

		if ($this->section == 'finalize')
		{
			// Complete project setup
			if ($this->_finalize())
			{
				$this->_setNotification(Lang::txt('COM_PROJECTS_NEW_PROJECT_CREATED'), 'success');

				// Some follow-up actions
				$this->_onAfterProjectCreate();

				App::redirect(Route::url($this->model->link()));
				return;
			}
		}
		else
		{
			// Save
			$this->_process();
		}

		// Record setup stage and move on
		if ($setup && !$this->getError() && $step > $this->model->get('setup_stage'))
		{
			$this->model->set('setup_stage', $step);
			$this->model->store();

			// Did we actually complete setup?
			if (!$this->model->inSetup())
			{
				// Complete project setup
				if ($this->_finalize())
				{
					$this->_setNotification(Lang::txt('COM_PROJECTS_NEW_PROJECT_CREATED'), 'success');

					// Some follow-up actions
					$this->_onAfterProjectCreate();

					App::redirect(Route::url($this->model->link()));
					return;
				}
			}
		}

		// Don't go next in case of error
		if ($this->getError())
		{
			$this->next = $this->section;
			$this->_setNotification($this->getError(), 'error');
		}
		else
		{
			$this->_setNotification(Lang::txt('COM_PROJECTS_' . strtoupper($this->section) . '_SAVED'), 'success');
		}

		// Redirect
		$task   = $setup ? 'setup' : 'edit';
		$append = $new && $this->model->exists() && $this->next == 'describe' ? '#describearea' : '';
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=' . $task . '&alias=' . $this->model->get('alias') . '&active=' . $this->next) . $append
		);
		return;
	}

	/**
	 * Finalize project
	 *
	 * @return  void
	 */
	protected function _finalize()
	{
		$agree       = Request::getInt('agree', 0, 'post');
		$restricted  = Request::getVar('restricted', '', 'post');
		$agree_irb   = Request::getInt('agree_irb', 0, 'post');
		$agree_ferpa = Request::getInt('agree_ferpa', 0, 'post');
		$state       = 1;

		// Cannot save a new project unless in setup
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Final checks (agreements etc)
		if ($this->_setupComplete == 3)
		{
			// General restricted data question
			if ($this->config->get('restricted_data', 0) == 2)
			{
				if (!$restricted)
				{
					$this->setError(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA'));
					return false;
				}

				// Save params
				$this->model->saveParam('restricted_data', $restricted);
			}

			// Restricted data with specific questions
			if ($this->config->get('restricted_data', 0) == 1)
			{
				$restrictions = array(
					'hipaa_data'  => Request::getVar('hipaa', 'no', 'post'),
					'ferpa_data'  => Request::getVar('ferpa', 'no', 'post'),
					'export_data' => Request::getVar('export', 'no', 'post'),
					'irb_data'    => Request::getVar('irb', 'no', 'post')
				);

				// Save individual restrictions
				foreach ($restrictions as $key => $value)
				{
					$this->model->saveParam($key, $value);
				}

				// No selections?
				if (!isset($_POST['restricted']))
				{
					foreach ($restrictions as $key => $value)
					{
						if ($value == 'yes')
						{
							$restricted = 'yes';
						}
					}

					if ($restricted != 'yes')
					{
						$this->setError(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_HIPAA'));
						return false;
					}
				}

				// Handle restricted data choice, save params
				$this->model->saveParam('restricted_data', $restricted);

				if ($restricted == 'yes')
				{
					// Check selections
					$selected = 0;
					foreach ($restrictions as $key => $value)
					{
						if ($value == 'yes')
						{
							$selected++;
						}
					}
					// Make sure user made selections
					if ($selected == 0)
					{
						$this->setError(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_SPECIFY_DATA'));
						return false;
					}

					// Check for required confirmations
					if (($restrictions['ferpa_data'] == 'yes' && !$agree_ferpa)
						|| ($restrictions['irb_data'] == 'yes' && !$agree_irb))
					{
						$this->setError(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA_AGREE_REQUIRED'));
						return false;
					}

					// Stop if hipaa/export controlled, or send to extra approval screen
					if ($this->config->get('approve_restricted', 0))
					{
						if ($restrictions['export_data'] == 'yes'
							|| $restrictions['hipaa_data'] == 'yes'
							|| $restrictions['ferpa_data'] == 'yes')
						{
							$state = 5; // pending approval
						}
					}
				}
				elseif ($restricted == 'maybe')
				{
					$this->model->saveParam('followup', 'yes');
				}
			}

			// Check to make sure user has agreed to terms
			if ($agree == 0)
			{
				$this->setError(Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS'));
				return false;
			}

			// Collect grant information
			if ($this->config->get('grantinfo', 0))
			{
				$grant_agency    = Request::getVar('grant_agency', '');
				$grant_title     = Request::getVar('grant_title', '');
				$grant_PI        = Request::getVar('grant_PI', '');
				$grant_budget    = Request::getVar('grant_budget', '');
				$this->model->saveParam('grant_budget', $grant_budget);
				$this->model->saveParam('grant_agency', $grant_agency);
				$this->model->saveParam('grant_title', $grant_title);
				$this->model->saveParam('grant_PI', $grant_PI);
				$this->model->saveParam('grant_status', 0);
			}
		}

		// Is the project active already?
		$active = $this->model->get('state') == 1 ? 1 : 0;

		// Sync with system group
		$objO = $this->model->table('Owner');
		$objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));

		// Activate project
		if (!$active)
		{
			$this->model->set('state', $state);
			$this->model->set('provisioned', 0); // remove provisioned flag if any
			$this->model->set('setup_stage', $this->_setupComplete);
			$this->model->set('created', Date::toSql());

			// Save changes
			if (!$this->model->store())
			{
				$this->setError($this->model->getError());
				return false;
			}

			$this->_notify = $state == 1 ? true : false;
		}

		return true;
	}

	/**
	 * After a new project is created
	 *
	 * @return  void
	 */
	protected function _onAfterProjectCreate()
	{
		// Initialize files repository
		$this->_iniGitRepo();

		// Email administrators about a new project
		if ($this->config->get('messaging') == 1)
		{
			$admingroup     = $this->config->get('admingroup', '');
			$sdata_group    = $this->config->get('sdata_group', '');
			$ginfo_group    = $this->config->get('ginfo_group', '');
			$project_admins = Helpers\Html::getGroupMembers($admingroup);
			$ginfo_admins   = Helpers\Html::getGroupMembers($ginfo_group);
			$sdata_admins   = Helpers\Html::getGroupMembers($sdata_group);

			$admins = array_merge($project_admins, $ginfo_admins, $sdata_admins);
			$admins = array_unique($admins);

			// Send out email to admins
			if (!empty($admins))
			{
				Helpers\Html::sendHUBMessage(
					$this->_option,
					$this->model,
					$admins,
					Lang::txt('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
					'projects_new_project_admin',
					'new'
				);
			}
		}

		// Internal project notifications
		if (isset($this->_notify) && $this->_notify === true)
		{
			// Record activity
			$this->model->recordActivity(Lang::txt('COM_PROJECTS_PROJECT_STARTED'));

			// Send out emails
			$this->_notifyTeam();
		}

		// Trigger project create event
		Event::trigger('projects.onProjectCreate', array($this->model));
	}

	/**
	 * Initialize Git repo
	 *
	 * @return  boolean
	 */
	protected function _iniGitRepo()
	{
		if (!$this->model->exists())
		{
			return false;
		}

		// Create and initialize local repo
		if (!$this->model->repo()->iniLocal())
		{
			$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
			return false;
		}

		return true;
	}

	/**
	 * Process data
	 *
	 * @return  void
	 */
	protected function _process()
	{
		// New project?
		$new = $this->model->exists() ? false : true;

		// Are we in setup?
		$setup = ($new || $this->model->inSetup()) ? true : false;

		// Incoming
		$private = Request::getInt('private', 1);

		// Save section
		switch ($this->section)
		{
			case 'describe':
			case 'info':
			case 'info_custom':
				// Incoming
				$name  = trim(Request::getVar('name', '', 'post'));
				$title = trim(Request::getVar('title', '', 'post'));

				$name  = preg_replace('/ /', '', $name);
				$name  = strtolower($name);

				// Clean up title from any scripting
				$title = preg_replace('/\s+/', ' ', $title);
				$title = $this->_txtClean($title);

				// Check incoming data
				if ($setup && $new && !$this->model->check($name, $this->model->get('id')))
				{
					$this->setError(Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY'));
					return false;
				}
				elseif (!$title)
				{
					$this->setError(Lang::txt('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY'));
					return false;
				}

				if ($this->model->exists())
				{
					$this->model->set('modified', Date::toSql());
					$this->model->set('modified_by', User::get('id'));
				}
				else
				{
					$this->model->set('alias', $name);
					$this->model->set('created', Date::toSql());
					$this->model->set('created_by_user', User::get('id'));
					$this->model->set('owned_by_group', $this->_gid);
					$this->model->set('owned_by_user', User::get('id'));
					$this->model->set('private', $this->config->get('privacy', 1));
				}

				$this->model->set('title', \Hubzero\Utility\String::truncate($title, 250));
				$this->model->set('about', trim(Request::getVar('about', '', 'post', 'none', 2)));
				$this->model->set('type', Request::getInt('type', 1, 'post'));

				// save advanced permissions
				if (isset($_POST['private']))
				{
					$this->model->set('private', $private);
				}

				if ($setup && !$this->model->exists())
				{
					// Copy params from default project type
					$objT = $this->model->table('Type');
					$this->model->set('params', $objT->getParams($this->model->get('type')));
				}

				// Save changes
				if (!$this->model->store())
				{
					$this->setError($this->model->getError());
					return false;
				}

				// Save custom description
				if ($this->section == 'info_custom')
				{
					$newInfo = Request::getVar('description', array());

					$projectID = $this->model->get('id');
					$project = ProjectORM::one($this->model->get('id'));

					$old = Description::collect($project->descriptions);
					$formFields = array_merge($old, $newInfo);
					$knownFields = Field::all()->rows()->toObject();

					foreach ($knownFields as $kField)
					{
						$existingField = Description::all()->whereEquals('project_id', $this->model->get('id'))
							->whereEquals('description_key', $kField->name)
							->limit(1)
							->row();

						if ($existingField->id != NULL)
						{
							$existingField->set('description_value', $formFields[$kField->name]);
							$existingField->set('ordering', $kField->ordering);
							$existingField->save();
						}
						else
						{
							// Create a new field
							$newField = new Description;

							$newField
								->set('description_key', $kField->name)
								->set('description_value', $formFields[$kField->name])
								->set('project_id', $this->model->get('id'))
								->set('ordering', $kField->ordering);

							if (!$newField->save())
							{
								$this->setError($newField->getError());
							}
						}
					}
				}

				// Save owners for new projects
				if ($new && $this->section != 'info_custom')
				{
					$this->_identifier = $this->model->get('alias');

					// Group owners
					$objO = $this->model->table('Owner');
					if ($this->_gid)
					{
						// Only add the creator
						// They'll choose if they want to sync the entire group or not in the next step
						if (!$objO->saveOwners($this->model->get('id'), User::get('id'), User::get('id'), $this->_gid, 0, 1, 1, '', $split_group_roles = 0))
						{
							$this->setError(Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $objO->getError());
							return false;
						}
						// Make sure project creator is manager
						$objO->reassignRole(
							$this->model->get('id'),
							$users = array(User::get('id')),
							0 ,
							1
						);
					}
					elseif (!$objO->saveOwners($this->model->get('id'), User::get('id'), User::get('id'), $this->_gid, 1, 1, 1))
					{
						$this->setError(Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $objO->getError());
						return false;
					}
				}

				// Record activity
				$this->model->recordActivity(Lang::txt('COM_PROJECTS_PROJECT_INFO_UPDATED'));

			break;

			case 'team':

				if ($new)
				{
					return false;
				}

				if ($this->model->groupOwner())
				{
					// Save group sync settings
					$this->model->set('sync_group', Request::getInt('sync_group', 0, 'post'));

					if (!$this->model->store())
					{
						$this->setError($this->model->getError());
						return false;
					}

					// Are we syncing group membership?
					if ($this->model->get('sync_group'))
					{
						$objO = $this->model->table('Owner');
						$objO->saveOwners($this->model->get('id'), User::get('id'), 0, $this->_gid, 0, 1, 1, '', $split_group_roles = 0);
					}
				}

				// Save team
				$content = Event::trigger('projects.onProject', array(
					$this->model,
					'save',
					array('team')
				));

				if (isset($content[0]) && $this->next == $this->section)
				{
					if (isset($content[0]['msg']) && !empty($content[0]['msg']))
					{
						$this->_setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
					}
				}

			break;

			case 'settings':

				if ($new)
				{
					return false;
				}

				// Save privacy
				if (isset($_POST['private']))
				{
					$this->model->set('private', $private);

					// Save changes
					if (!$this->model->store())
					{
						$this->setError($this->model->getError());
						return false;
					}
				}

				// Save params
				$incoming   = Request::getVar('params', array());
				if (!empty($incoming))
				{
					foreach ($incoming as $key => $value)
					{
						$this->model->saveParam($key, $value);
						$this->model->params->set($key, $value);

						// If grant information changed
						if ($key == 'grant_status')
						{
							// Meta data for comment
							$meta = '<meta>' . Date::of('now')->toLocal('M d, Y')
							. ' - ' . User::get('name') . '</meta>';

							$cbase   = $this->model->get('admin_notes');
							$cbase  .= '<nb:sponsored>'
							. Lang::txt('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE')
							. $meta . '</nb:sponsored>';
							$this->model->set('admin_notes', $cbase);

							// Save admin notes
							if (!$this->model->store())
							{
								$this->setError($this->model->getError());
								return false;
							}

							$admingroup = $this->config->get('ginfo_group', '');

							if (\Hubzero\User\Group::getInstance($admingroup))
							{
								$admins = Helpers\Html::getGroupMembers($admingroup);

								// Send out email to admins
								if (!empty($admins))
								{
									Helpers\Html::sendHUBMessage(
										$this->_option,
										$this->model,
										$admins,
										Lang::txt('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
										'projects_new_project_admin',
										'admin',
										Lang::txt('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE'),
										'sponsored'
									);
								}
							}
						}
					}

					// Record activity
					$this->model->recordActivity(Lang::txt('COM_PROJECTS_PROJECT_SETTINGS_UPDATED'));
				}
			break;
		}
	}

	/**
	 * Load team editor
	 *
	 * @return  string
	 */
	protected function _loadTeamEditor()
	{
		// Get plugin output
		$content = Event::trigger('projects.onProject', array(
			$this->model,
			$this->_task,
			array('team')
		));

		if (!isset($content[0]))
		{
			// Must never happen
			return false;
		}
		if (isset($content[0]['msg']) && !empty($content[0]['msg']))
		{
			$this->_setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
		}

		return $content[0]['html'];
	}

	/**
	 * Edit project
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// Check that project exists
		if (!$this->model->exists() || $this->model->isDeleted())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Check if project is in setup
		if ($this->model->inSetup())
		{
			App::redirect(Route::url($this->model->link('setup')));
		}

		// Only managers can edit project
		if (!$this->model->access('manager') && !($this->model->access('content') && $this->config->get('edit_description')))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Which section are we editing?
		$sections = array('info', 'team', 'settings');
		if ($this->config->get('edit_settings', 0) == 0)
		{
			array_pop($sections);
		}
		$this->section = in_array($this->section, $sections) ? $this->section : 'info';

		if ($this->config->get('custom_profile') == 'custom' && ($this->section == 'info' || $this->section == 'info_custom'))
		{
			//$sections = array('info_custom', 'team', 'settings');
			$this->view->fields = Field::all()->order('ordering', 'ASC');

			$projectID = $this->model->get('id');

			// Load project values
			$projectData = Description::all()->where('project_id', '=', $projectID);
			$data = Description::collect($projectData);
			$this->view->data = $data;
			$this->section = 'info_custom';
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		$this->view->setLayout('edit');
		if ($this->section == 'team')
		{
			$this->view->content = $this->_loadTeamEditor();
		}

		// Output HTML
		$this->view->model      = $this->model;
		$this->view->uid        = User::get('id');
		$this->view->section    = $this->section;
		$this->view->sections   = $sections;
		$this->view->title      = $this->title;
		$this->view->option     = $this->_option;
		$this->view->config     = $this->config;
		$this->view->task       = $this->_task;
		$this->view->publishing = $this->_publishing;
		$this->view->active     = 'edit';

		// Get messages and errors
		$error = $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError($error);
		}
		$this->view->msg = $this->_getNotifications('success');

		$this->view->display();
	}

	/**
	 * Verify project name (AJAX)
	 *
	 * @return  boolean
	 */
	public function verifyTask()
	{
		// Incoming
		$name = isset($this->_text) ? $this->_text : trim(Request::getVar('text', ''));
		$id   = $this->_identifier  ? $this->_identifier: trim(Request::getInt('pid', 0));
		$ajax = isset($this->_ajax) ? $this->_ajax : trim(Request::getInt('ajax', 0));

		$this->model->check($name, $id, $ajax);

		if ($ajax)
		{
			echo json_encode(array(
				'error' => $this->model->getError(),
				'message' => Lang::txt('COM_PROJECTS_VERIFY_PASSED')
			));
			return;
		}

		if ($this->model->getError())
		{
			return false;
		}
		return true;
	}

	/**
	 * Suggest alias name (AJAX)
	 *
	 * @return  mixed
	 */
	public function suggestaliasTask()
	{
		// Incoming
		$title   = isset($this->_text) ? $this->_text : trim(Request::getVar('text', ''));
		$title   = urldecode($title);

		$suggested = Helpers\Html::suggestAlias($title);
		$maxLength = $this->config->get('max_name_length', 30);
		$maxLength = $maxLength > 30 ? 30 : $maxLength;

		$this->model->check($suggested, $maxLength);
		if ($this->model->getError())
		{
			return false;
		}
		echo $suggested;
		return;
	}

	/**
	 * Convert Microsoft characters and strip disallowed content
	 * This includes script tags, HTML comments, xhubtags, and style tags
	 *
	 * @param   string  &$text  Text to clean
	 * @return  string
	 */
	private function _txtClean(&$text)
	{
		// Handle special characters copied from MS Word
		$text = str_replace('“','"', $text);
		$text = str_replace('”','"', $text);
		$text = str_replace("’","'", $text);
		$text = str_replace("‘","'", $text);

		$text = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<style[^>]*>.*?</style>'si", '', $text);
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);

		return $text;
	}
}
