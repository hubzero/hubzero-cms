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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Controllers;

use Components\Projects\Tables;

/**
 * Projects setup controller class
 */
class Setup extends Base
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->registerTask('start', 'display');

		// Incoming
		$defaultSection = $this->_task == 'edit' ? 'info' : '';
		$this->section  = \JRequest::getVar( 'active', $defaultSection );

		$this->project  = NULL;
		$this->group    = NULL;

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = $this->_task == 'edit'
				? Lang::txt('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA')
				: Lang::txt('COM_PROJECTS_LOGIN_SETUP');
			$this->_login();
			return;
		}

		parent::execute();
	}

	/**
	 * Display setup screens
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->_task = 'setup';

		// Instantiate a project
		$obj = new Tables\Project( $this->database );

		// Get project information
		if ($this->_identifier)
		{
			// Get Project
			$this->project = $obj->getProject($this->_identifier, $this->juser->get('id'));

			if (!$obj->loadProject($this->_identifier) or !$this->project)
			{
				\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
				return;
			}

			$pid = $this->project->id;
			$alias = $this->project->alias;

			// Is project deleted?
			if ($this->project->state == 2)
			{
				\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_DELETED') );
				return;
			}

			// If this is a group project
			if (intval($obj->owned_by_group) > 0)
			{
				$this->_gid = $obj->owned_by_group;
			}
		}
		else
		{
			// Is project registration restricted to a group?
			$creatorgroup = $this->config->get('creatorgroup', '');

			// Check authorization
			if ($creatorgroup)
			{
				$cgroup = \Hubzero\User\Group::getInstance($creatorgroup);
				if ($cgroup)
				{
					if (!$cgroup->is_member_of('members',$this->juser->get('id')) &&
						!$cgroup->is_member_of('managers',$this->juser->get('id')))
					{
						// Dispay error
						$this->setError(Lang::txt('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP'));
						$this->_showError();
						return;
					}
				}
			}

			// New entry defaults
			$obj->id 			= 0;
			$obj->alias 		= \JRequest::getCmd( 'name', '', 'post' );
			$obj->title 		= \JRequest::getVar( 'title', '', 'post' );
			$obj->about 		= trim(\JRequest::getVar( 'about', '', 'post', 'none', 2 ));
			$obj->type 			= \JRequest::getInt( 'type', 1, 'post' );
			$obj->setup_stage 	= 0;
			$obj->private 		= 1;
		}

		// Get group ID
		if ($this->_gid)
		{
			// Load the group
			$this->group = \Hubzero\User\Group::getInstance( $this->_gid );

			// Ensure we found the group info
			if (!is_object($this->group) || (!$this->group->get('gidNumber') && !$this->group->get('cn')) )
			{
				JError::raiseError( 404, Lang::txt('COM_PROJECTS_NO_GROUP_FOUND') );
				return;
			}
			$this->_gid = $this->group->get('gidNumber');

			// Make sure we have up-to-date group membership information
			$objO = new Tables\Owner( $this->database );
			$objO->reconcileGroups($obj->id);
		}

		// Check authorization
		$this->view->authorized = $this->_authorize();
		if ($obj->id && (!$this->view->authorized
			|| $this->view->authorized != 1
			|| $obj->created_by_user != $this->juser->get('id'))
		)
		{
			\JError::raiseError( 403, Lang::txt('ALERTNOTAUTH') );
			return;
		}
		elseif (!$obj->id && $this->_gid && !$this->view->authorized)
		{
			// Check group authorization to create a project
			if (!$this->group->is_member_of('members', $this->juser->get('id'))
				&& !$this->group->is_member_of('managers',$this->juser->get('id')))
			{
				\JError::raiseError( 403, Lang::txt('COM_PROJECTS_ALERTNOTAUTH_GROUP') );
				return;
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
		$step = $step !== NULL && $step <= $obj->setup_stage ? $step : $obj->setup_stage;

		if ($step < $this->_setupComplete)
		{
			$layout = $setupSteps[$step];
			$this->section = $layout;
		}
		else
		{
			// Setup complete, go to project page
			$this->_redirect 	= Route::url('index.php?option=' . $this->_option . '&alias=' . $obj->alias);
			return;
		}

		// Set layout
		$this->view->setLayout( $layout );

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		if ($obj->id)
		{
			$this->view->params = new \JParameter( $obj->params );
		}
		if ($this->section == 'team')
		{
			$this->view->content = $this->_loadTeamEditor();
		}

		// Output HTML
		$this->view->juser  		= $this->juser;
		$this->view->project  		= $obj;
		$this->view->step			= $step;
		$this->view->section  		= $this->section;
		$this->view->title  		= $this->title;
		$this->view->option 		= $this->_option;
		$this->view->config 		= $this->config;
		$this->view->gid 			= $this->_gid;
		$this->view->group 			= $this->group;
		$this->view->extended       = \JRequest::getInt( 'extended', 0, 'post');

		// Get messages	and errors
		$this->view->msg = isset($this->_msg) ? $this->_msg : $this->_getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}

		$this->view->display();
		return;
	}

	/**
	 * Save
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming
		$step = \JRequest::getInt( 'step', '0'); // Where do we go next?

		// Instantiate a project
		$obj = new Tables\Project( $this->database );

		if ($this->_identifier && !$obj->loadProject($this->_identifier))
		{
			\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Are we in setup?
		$setup = $obj->id && $obj->state == 1 ? 0 : 1;

		// New project?
		$new = $obj->id ? false : true;

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
		if ($setup && !$this->_identifier && $current > 0)
		{
			// Error
			return;
		}

		// Cannot save a new project unless in setup
		if (!$setup && !$obj->id)
		{
			\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Check authorization
		$this->authorized = $this->_authorize();
		if ($obj->id && (!$this->authorized
			|| $this->authorized != 1
			|| ($setup && $obj->created_by_user != $this->juser->get('id')))
		)
		{
			\JError::raiseError( 403, Lang::txt('ALERTNOTAUTH') );
			return;
		}
		elseif (!$obj->id && $this->_gid && !$this->authorized)
		{
			// Load the group
			$this->group = \Hubzero\User\Group::getInstance( $this->_gid );

			// Ensure we found the group info
			if (!is_object($this->group) || (!$this->group->get('gidNumber') && !$this->group->get('cn')) )
			{
				\JError::raiseError( 404, Lang::txt('COM_PROJECTS_NO_GROUP_FOUND') );
				return;
			}

			// Check group authorization to create a project
			if (!$this->group->is_member_of('members',$this->juser->get('id'))
				&& !$this->group->is_member_of('managers',$this->juser->get('id')))
			{
				\JError::raiseError( 403, Lang::txt('COM_PROJECTS_ALERTNOTAUTH_GROUP') );
				return;
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

				$this->_redirect = Route::url('index.php?option=' . $this->_option
					. '&alias=' . $obj->alias);
				return;
			}
		}
		else
		{
			// Save
			$this->_process();
		}

		// Get Project after updates
		$this->project = $obj->getProject($this->_identifier, $this->juser->get('id'));

		// Record setup stage and move on
		if ($setup && !$this->getError() && $step > $obj->setup_stage)
		{
			$obj->saveStage($this->project->id, $step);
		}

		// Don't go next in case of error
		if ($this->getError())
		{
			$this->next = $this->section;
			$this->_setNotification($this->getError(), 'error');
		}
		else
		{
			$this->_setNotification(Lang::txt('COM_PROJECTS_'
				. strtoupper($this->section) . '_SAVED'), 'success');
		}

		// Redirect
		$task   = $setup ? 'setup' : 'edit';
		$append = $new && $this->project->id && $this->next == 'describe' ? '#describearea' : '';
		$this->_redirect = Route::url('index.php?option=' . $this->_option
			. '&task=' . $task . '&alias=' . $this->project->alias
			. '&active=' . $this->next ) . $append;
		return;
	}

	/**
	 * Finalize project
	 *
	 * @return     void
	 */
	protected function _finalize()
	{
		$agree 				= \JRequest::getInt( 'agree', 0, 'post' );
		$restricted 		= \JRequest::getVar( 'restricted', '', 'post' );
		$agree_irb 			= \JRequest::getInt( 'agree_irb', 0, 'post' );
		$agree_ferpa 		= \JRequest::getInt( 'agree_ferpa', 0, 'post' );
		$state				= 1;

		// Load project
		$obj = new Tables\Project( $this->database );
		if (!$obj->loadProject($this->_identifier))
		{
			$this->setError( Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Final checks (agreements etc)
		if ($this->_setupComplete == 3 )
		{
			// General restricted data question
			if ($this->config->get('restricted_data', 0) == 2)
			{
				if (!$restricted)
				{
					$this->setError( Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA'));
					return false;
				}

				// Save params
				$obj->saveParam($obj->id, 'restricted_data', htmlentities($restricted));
			}
			
			// Restricted data with specific questions
			if ($this->config->get('restricted_data', 0) == 1)
			{
				$restrictions = array(
					'hipaa_data'  => \JRequest::getVar( 'hipaa', 'no', 'post' ),
					'ferpa_data'  => \JRequest::getVar( 'ferpa', 'no', 'post' ),
					'export_data' => \JRequest::getVar( 'export', 'no', 'post' ),
					'irb_data'    => \JRequest::getVar( 'irb', 'no', 'post' )
				);

				// Save individual restrictions
				foreach ($restrictions as $key => $value)
				{
					$obj->saveParam($obj->id, $key, $value);
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
						$this->setError( Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_HIPAA'));
						return false;
					}
				}

				// Handle restricted data choice, save params
				$obj->saveParam($obj->id, 'restricted_data', htmlentities($restricted));

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
						$this->setError( Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_SPECIFY_DATA'));
						return false;
					}

					// Check for required confirmations
					if (($restrictions['ferpa_data'] == 'yes' && !$agree_ferpa)
						|| ($restrictions['irb_data'] == 'yes' && !$agree_irb))
					{
						$this->setError( Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA_AGREE_REQUIRED'));
						return false;
					}

					// Stop if hipaa/export controlled, or send to extra approval screen
					if ($this->config->get('approve_restricted', 0))
					{
						if ($restrictions['export_data'] == 'yes'
							|| $restrictions['hipaa_data'] == 'yes'
							|| $restrictions['ferpa_data'] == 'yes' )
						{
							$state = 5; // pending approval
						}
					}
				}
				elseif ($restricted == 'maybe')
				{
					$obj->saveParam($obj->id, 'followup', 'yes');
				}
			}

			// Check to make sure user has agreed to terms
			if ($agree == 0)
			{
				$this->setError( Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS'));
				return false;
			}

			// Collect grant information
			if ($this->config->get('grantinfo', 0))
			{
				$grant_agency    = \JRequest::getVar( 'grant_agency', '' );
				$grant_title     = \JRequest::getVar( 'grant_title', '' );
				$grant_PI        = \JRequest::getVar( 'grant_PI', '' );
				$grant_budget    = \JRequest::getVar( 'grant_budget', '' );
				$obj->saveParam($obj->id, 'grant_budget', htmlentities($grant_budget));
				$obj->saveParam($obj->id, 'grant_agency', htmlentities($grant_agency));
				$obj->saveParam($obj->id, 'grant_title', htmlentities($grant_title));
				$obj->saveParam($obj->id, 'grant_PI', htmlentities($grant_PI));
				$obj->saveParam($obj->id, 'grant_status', 0);
			}
		}

		// Is the project active already?
		$active = $obj->state == 1 ? 1 : 0;

		// Sync with system group
		$objO = new Tables\Owner( $this->database );
		$objO->sysGroup($obj->alias, $this->config->get('group_prefix', 'pr-'));

		// Activate project
		if (!$active)
		{
			$obj->state = $state;
			$obj->provisioned = 0; // remove provisioned flag if any
			$obj->created = \JFactory::getDate()->toSql();
			$obj->setup_stage = $this->_setupComplete;

			// Save changes
			if (!$obj->store())
			{
				$this->setError( $obj->getError() );
				return false;
			}

			$this->_notify = $state == 1 ? true : false;
		}

		// Get updated project
		$this->project = $obj->getProject(
			$obj->id,
			$this->juser->get('id')
		);

		return true;
	}

	/**
	 * After a new project is created
	 *
	 * @return     void
	 */
	protected function _onAfterProjectCreate()
	{
		// Initialize files repository
		$this->_iniGitRepo();

		// Email administrators about a new project
		if ($this->config->get('messaging') == 1)
		{
			$admingroup 	= $this->config->get('admingroup', '');
			$sdata_group 	= $this->config->get('sdata_group', '');
			$ginfo_group 	= $this->config->get('ginfo_group', '');
			$project_admins = \Components\Projects\Helpers\Html::getGroupMembers($admingroup);
			$ginfo_admins 	= \Components\Projects\Helpers\Html::getGroupMembers($ginfo_group);
			$sdata_admins 	= \Components\Projects\Helpers\Html::getGroupMembers($sdata_group);

			$admins = array_merge($project_admins, $ginfo_admins, $sdata_admins);
			$admins = array_unique($admins);

			// Send out email to admins
			if (!empty($admins))
			{
				\Components\Projects\Helpers\Html::sendHUBMessage(
					$this->_option,
					$this->config,
					$this->project,
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
			$this->_postActivity(Lang::txt('COM_PROJECTS_PROJECT_STARTED'));

			// Send out emails
			$this->_notifyTeam();
		}
	}

	/**
	 * Initialize Git repo
	 *
	 * @return     void
	 */
	protected function _iniGitRepo()
	{
		if (!isset($this->project) || !is_object($this->project) || !$this->project->alias)
		{
			return false;
		}

		// Build project repo path
		$path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->project->alias, 'files', false);

		// Create project repo path
		if (!is_dir( $path ))
		{
			jimport('joomla.filesystem.folder');
			if (!\JFolder::create( $path ))
			{
				$this->setError( Lang::txt('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
			}
		}

		// Git ini
		include_once( PATH_CORE . DS . 'components' . DS .'com_projects'
			. DS . 'helpers' . DS . 'githelper.php' );
		$this->_git = new \Components\Projects\Helpers\Git($path);
		$this->_git->iniGit();
	}

	/**
	 * Process data
	 *
	 * @return     void
	 */
	protected function _process()
	{
		// Load project
		$obj = new Tables\Project( $this->database );
		$obj->loadProject($this->_identifier);

		// New project?
		$new = $obj->id ? false : true;

		// Are we in setup?
		$setup = $obj->id && $obj->state == 1 ? false : true;

		// Incoming
		$private    = \JRequest::getInt( 'private', 1, 'post' );

		// Save section
		switch ($this->section)
		{
			case 'describe':
			case 'info':
			
				// Incoming
				$name       = trim(\JRequest::getVar( 'name', '', 'post' ));
				$title      = trim(\JRequest::getVar( 'title', '', 'post' ));
				$type       = \JRequest::getInt( 'type', 1, 'post' );

				$name = preg_replace('/ /', '', $name);
				$name = strtolower($name);

				// Clean up title from any scripting
				$title = preg_replace('/\s+/', ' ', $title);
				$title = $this->_txtClean($title);

				// Check incoming data
				if ($setup && $new && !$this->model->check($name, $obj->id))
				{
					$this->setError( Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY') );
					return false;
				}
				elseif (!$title)
				{
					$this->setError( Lang::txt('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY') );
					return false;
				}

				if ($obj->id)
				{
					$obj->modified    = \JFactory::getDate()->toSql();
					$obj->modified_by = $this->juser->get('id');
				}
				else
				{
					$obj->alias             = $name;
					$obj->private 			= $this->config->get('privacy', 1);
					$obj->created 			= \JFactory::getDate()->toSql();
					$obj->created_by_user 	= $this->juser->get('id');
					$obj->owned_by_user 	= $this->juser->get('id');
					$obj->owned_by_group 	= $this->_gid;
				}

				$obj->title = \Hubzero\Utility\String::truncate($title, 250);
				$obj->about = trim(\JRequest::getVar( 'about', '', 'post', 'none', 2 ));
				$obj->type 	= $type;

				// save advanced permissions
				if (isset($_POST['private']))
				{
					$obj->private = $private;
				}

				if ($setup && !$obj->id)
				{
					// Copy params from default project type
					$objT 	= new Tables\Type( $this->database );
					$obj->params = $objT->getParams ($obj->type);
				}

				// Save changes
				if (!$obj->store())
				{
					$this->setError( $obj->getError() );
					return false;
				}
				if (!$obj->id)
				{
					$obj->checkin();
				}

				// Save owners for new projects
				if ($new)
				{
					$this->_identifier = $obj->alias;

					// Group owners
					$objO 	= new Tables\Owner( $this->database );
					if ($this->_gid)
					{
						if (!$objO->saveOwners (
							$obj->id, $this->juser->get('id'), 0, $this->_gid,
							0, 1, 1, '', $split_group_roles = 0
						))
						{
							$this->setError( Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS')
								. ': ' . $objO->getError() );
							return false;
						}
						// Make sure project creator is manager
						$objO->reassignRole (
							$obj->id,
							$users = array($this->juser->get('id')),
							0 ,
							1
						);
					}
					elseif (!$objO->saveOwners ( $obj->id, $this->juser->get('id'),
						$this->juser->get('id'), $this->_gid, 1, 1, 1 )
					)
					{
						$this->setError( Lang::txt('COM_PROJECTS_ERROR_SAVING_AUTHORS')
							. ': ' . $objO->getError() );
						return false;
					}
				}

				break;

			case 'team':

				if ($new)
				{
					return false;
				}

				// Get team plugin
				\JPluginHelper::importPlugin( 'projects', 'team' );
				$dispatcher = \JDispatcher::getInstance();

				// Save team
				$content = $dispatcher->trigger( 'onProject', array(
					$obj,
					$this->_option,
					$this->authorized,
					$this->juser->get('id'),
					NULL,
					NULL,
					'save'
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
					$obj->private = $private;

					// Save changes
					if (!$obj->store())
					{
						$this->setError( $obj->getError() );
						return false;
					}
				}

				// Save params
				$incoming   = \JRequest::getVar( 'params', array() );
				if (!empty($incoming))
				{
					$old_params = $obj->params;
					foreach ($incoming as $key => $value)
					{
						$obj->saveParam($obj->id, $key, htmlentities($value));

						// Get updated project
						$this->project = $obj->getProject(
							$obj->id,
							$this->juser->get('id')
						);

						// If grant information changed
						if ($key == 'grant_status'
							&& $old_params != $this->project->params)
						{
							// Meta data for comment
							$meta = '<meta>' . \JHTML::_('date', \JFactory::getDate(), 'M d, Y')
							. ' - ' . $this->juser->get('name') . '</meta>';

							$cbase   = $obj->admin_notes;
							$cbase  .= '<nb:sponsored>'
							. Lang::txt('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE')
							. $meta . '</nb:sponsored>';
							$obj->admin_notes = $cbase;

							// Save admin notes
							if (!$obj->store())
							{
								$this->setError( $obj->getError() );
								return false;
							}

							$admingroup = $this->config->get('ginfo_group', '');

							if (\Hubzero\User\Group::getInstance($admingroup))
							{
								$admins = \Components\Projects\Helpers\Html::getGroupMembers($admingroup);

								// Send out email to admins
								if (!empty($admins))
								{
									\Components\Projects\Helpers\Html::sendHUBMessage(
										$this->_option,
										$this->config,
										$this->project,
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
				}
				break;
		}
	}

	/**
	 * Load team editor
	 *
	 * @return  html 
	 */
	protected function _loadTeamEditor()
	{
		// Get team plugin
		\JPluginHelper::importPlugin( 'projects', 'team' );
		$dispatcher = \JDispatcher::getInstance();

		// Get plugin output
		$content = $dispatcher->trigger( 'onProject', array(
			$this->project,
			$this->option,
			$this->authorized,
			$this->juser->get('id'),
			NULL,
			NULL,
			$this->_task
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
	 * @return     void
	 */
	public function editTask()
	{
		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj = new Tables\Project( $this->database );

		// Which section are we editing?
		$sections = array('info', 'team', 'settings');
		if ($this->config->get('edit_settings', 0) == 0)
		{
			array_pop($sections);
		}
		$this->section = in_array( $this->section, $sections ) ? $this->section : 'info';

		// Load project
		$this->project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$this->project)
		{
			\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Check if project is in setup
		if ($this->project->setup_stage < $this->_setupComplete)
		{
			$this->_redirect = Route::url('index.php?option=' . $this->_option
				. '&task=setup&id=' . $this->project->id);
			return;
		}

		// Is project deleted?
		if ($this->project->state == 2)
		{
			\JError::raiseError( 404, Lang::txt('COM_PROJECTS_PROJECT_DELETED') );
			return;
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check authorization
		$this->authorized = $this->_authorize();
		if ($this->authorized != 1)
		{
			// Only managers can edit
			\JError::raiseError( 403, Lang::txt('ALERTNOTAUTH') );
			return;
		}

		$this->view->setLayout( 'edit' );
		$this->view->project = $this->project;
		$this->view->params = new \JParameter( $this->view->project->params );

		if ($this->section == 'team')
		{
			$this->view->content = $this->_loadTeamEditor();
		}

		// Output HTML
		$this->view->uid 		= $this->juser->get('id');
		$this->view->section 	= $this->section;
		$this->view->sections 	= $sections;
		$this->view->title  	= $this->title;
		$this->view->authorized = $this->authorized;
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->task 		= $this->_task;
		$this->view->publishing	= $this->_publishing;
		$this->view->active		= 'edit';

		// Get messages	and errors
		$this->view->msg = $this->_getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}
		$this->view->display();
	}

	/**
	 * Verify project name (AJAX)
	 *
	 * @return   boolean
	 */
	public function verifyTask()
	{
		// Incoming
		$name   = isset($this->_text) ? $this->_text : trim(\JRequest::getVar( 'text', '' ));
		$id 	= $this->_identifier ? $this->_identifier: trim(\JRequest::getInt( 'pid', 0 ));
		$ajax 	= isset($this->_ajax) ? $this->_ajax : trim(\JRequest::getInt( 'ajax', 0 ));

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
	 * @param  int $ajax
	 * @param  string $name
	 * @param  int $pid
	 * @return  void
	 */
	public function suggestaliasTask()
	{
		// Incoming
		$title   = isset($this->_text) ? $this->_text : trim(\JRequest::getVar( 'text', '' ));
		$title   = urldecode($title);

		$suggested = \Components\Projects\Helpers\Html::suggestAlias($title);
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
	 * @param      string &$text Text to clean
	 * @return     string
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
