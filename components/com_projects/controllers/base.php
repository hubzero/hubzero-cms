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

use Hubzero\Component\SiteController;
use Components\Projects\Tables;

/**
 * Base projects controller (extends \Hubzero\Component\SiteController)
 */
class Base extends SiteController
{
	/**
	 * Execute function
	 *
	 * @return void
	 */
	public function execute()
	{
		// Is component on?
		if (!$this->config->get( 'component_on', 0 ))
		{
			$this->_redirect = '/';
			return;
		}

		// Publishing enabled?
		$this->_publishing = \JPluginHelper::isEnabled('projects', 'publications') ? 1 : 0;

		// Setup complete?
		$this->_setupComplete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Include scripts
		$this->_includeScripts();

		// Incoming project identifier
		$id    = \JRequest::getInt( 'id', 0 );
		$alias = \JRequest::getVar( 'alias', '' );
		$this->_identifier = $id ? $id : $alias;

		// Incoming
		$this->_task = strtolower(\JRequest::getWord( 'task', '' ));
		$this->_gid  = \JRequest::getVar( 'gid', 0 );

		// Model
		$this->model = new \Components\Projects\Models\Project();

		// Execute the task
		parent::execute();
	}

	/**
	 * Include necessary scripts
	 *
	 * @return     void
	 */
	protected function _includeScripts()
	{
		// No need in some controllers
		if ($this->_controller == 'media')
		{
			return;
		}

		// Include publications model
		if ($this->_publishing)
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_publications'
				. DS . 'models' . DS . 'publication.php');
		}

		// Database development on?
		if (\JPluginHelper::isEnabled('projects', 'databases'))
		{
			require_once( PATH_CORE . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.php');
			require_once( PATH_CORE . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.version.php');
		}

		// Logging and stats
		require_once( PATH_CORE . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.log.php');
		require_once( PATH_CORE . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php');

		// Include external file connection
		require_once( PATH_CORE . DS . 'components' . DS . 'com_projects' . DS
				. 'helpers' . DS . 'connect.php' );
		require_once( PATH_CORE . DS . 'administrator' . DS . 'components'
				. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
	}

	/**
	 * Set notifications
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	protected function _setNotification( $message, $type = 'success' )
	{
		// If message is set push to notifications
		if ($message != '')
		{
			$this->addComponentMessage($message, $type);
		}
	}

	/**
	 * Get notifications
	 * @param  string $type
	 * @return $messages if they exist
	 */
	protected function _getNotifications($type = 'success')
	{
		// Get messages in queue
		$messages = $this->getComponentMessage();

		// Return first message of type
		if ($messages && count($messages) > 0)
		{
			foreach ($messages as $message)
			{
				if ($message['type'] == $type)
				{
					return $message['message'];
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$task = (isset($this->_task) && !empty($this->_task))
			? '&task=' . $this->_task
			: '';
		$message = isset($this->_msg)
			? $this->_msg
			: Lang::txt('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');

		$rtrn = \JRequest::getVar('REQUEST_URI', Route::url('index.php?option='
			. $this->_option . '&controller=' . $this->_controller . $task), 'server');

		$this->setRedirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
		return;
	}

	/**
	 * Error view
	 *
	 * @return     void
	 */
	protected function _showError( $layout = 'default' )
	{
		// Need to be project creator
		$view 			= new \Hubzero\Component\View(
			array('name' => 'error', 'layout' => $layout)
		);
		$view->error  	= $this->getError();
		$view->title 	= $this->title;
		$view->display();
		return;
	}

	/**
	 * Authorize users
	 *
	 * @param  int $check_site_admin
	 * @return void
	 */
	protected function _authorize( $check_site_admin = 0, $groups = array() )
	{
		// Check login
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check whether user belongs to the project
		if ($this->_identifier)
		{
			$pOwner = new Tables\Owner( $this->database );
			if ($result = $pOwner->isOwner($this->juser->get('id'), $this->_identifier))
			{
				return $result;
			}
		}

		// Check if they're a site admin (from Joomla)
		if ($check_site_admin)
		{
			if ($this->juser->get('id') && $this->juser->authorize($this->_option, 'manage'))
			{
				return 'admin';
			}
		}

		// Check if user is in authorized groups (e.g. reviewers)
		if (!empty($groups))
		{
			foreach ($groups as $gr)
			{
				if ($group = \Hubzero\User\Group::getInstance($gr))
				{
					// Check if they're a member of this group
					$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
					if ($ugs && count($ugs) > 0)
					{
						foreach ($ugs as $ug)
						{
							if ($group && $ug->cn == $group->get('cn'))
							{
								$authorized = true;
								return $authorized;
							}
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Authorize reviewer
	 *
	 * @return     void
	 */
	protected function _checkReviewerAuth($reviewer)
	{
		$reviewers = array('sponsored', 'sensitive', 'general');
		if (!in_array($reviewer, $reviewers))
		{
			return false;
		}

		// Needs to be logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		$sdata_group 	= $this->config->get('sdata_group', '');
		$ginfo_group 	= $this->config->get('ginfo_group', '');
		$admingroup 	= $this->config->get('admingroup', '');
		$group      	= '';
		$authorized 	= false;

		// Get authorized group
		if ($reviewer == 'sensitive' && $sdata_group)
		{
			$group = \Hubzero\User\Group::getInstance($sdata_group);
		}
		elseif ($reviewer == 'sponsored' && $ginfo_group)
		{
			$group = \Hubzero\User\Group::getInstance($ginfo_group);
		}
		elseif ($reviewer == 'general' && $admingroup)
		{
			$group = \Hubzero\User\Group::getInstance($admingroup);
		}

		if ($group)
		{
			// Check if they're a member of this group
			$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($group && $ug->cn == $group->get('cn'))
					{
						$authorized = true;
					}
				}
			}
		}

		return $authorized;
	}

	/**
	 * Build the title for this component
	 *
	 * @return void
	 */
	protected function _buildTitle()
	{
		$active  = isset($this->active) ? $this->active : NULL;
		$project = isset($this->project) ? $this->project : NULL;

		// Set the title
		$this->title  = $this->_task == 'edit'
			? Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task))
			: Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		// Add project title
		if (is_object($project) && $project->alias)
		{
			if ($project->provisioned == 1)
			{
				$this->title .= ': ' . Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT');
			}
			else
			{
				$this->title .= ': '.stripslashes($project->title);
				if ($active && !$this->juser->get('guest'))
				{
					$this->title .= ' :: ' . ucfirst(Lang::txt('COM_PROJECTS_TAB_' . strtoupper($active)));
				}
			}
		}

		// Other views
		switch ($this->_task)
		{
			case 'browse':
				$reviewer 	 = \JRequest::getVar( 'reviewer', '' );
				if ($reviewer == 'sponsored' || $reviewer == 'sensitive')
				{
					$this->title = $reviewer == 'sponsored'
								 ? Lang::txt('COM_PROJECTS_REVIEWER_SPS')
								 : Lang::txt('COM_PROJECTS_REVIEWER_HIPAA');
				}
				else
				{
					$this->title .= ($this->_task)
						? ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)) : '';
				}
				break;

			case 'intro':
			case 'view':
			case 'process':
			case 'setup':
			case 'edit':
				// Nothing to add
				break;

			default:
				if ($this->_task)
				{
					$this->title .= ': ' . Lang::txt(strtoupper($this->_option)
						. '_' . strtoupper($this->_task));
				}
				break;
		}

		$document = \JFactory::getDocument();
		$document->setTitle( $this->title );

		return $this->title;
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		$app     = \JFactory::getApplication();
		$pathway = $app->getPathway();

		$group_tasks = array('start', 'setup', 'view');
		$project     = isset($this->project) ? $this->project : NULL;
		$group       = isset($this->group) ? $this->group : NULL;
		$active      = isset($this->active) ? $this->active : NULL;

		// Point to group if group project
		if (is_object($group) && in_array($this->_task, $group_tasks) )
		{
			$pathway->setPathway(array());
			$pathway->addItem(
				Lang::txt('COM_PROJECTS_GROUPS_COMPONENT'),
				Route::url('index.php?option=com_groups')
			);
			$pathway->addItem(
				\Hubzero\Utility\String::truncate($group->get('description'), 50),
				Route::url('index.php?option=com_groups&cn=' . $group->cn)
			);
			$pathway->addItem(
				Lang::txt('COM_PROJECTS_PROJECTS'),
				Route::url('index.php?option=com_groups&cn=' . $group->cn . '&active=projects')
			);
		}
		elseif (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				Lang::txt('COMPONENT_LONG_NAME'),
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Project path
		if (is_object($project) && $project->alias)
		{
			if ($project->provisioned == 1)
			{
				$pathway->addItem(
					stripslashes(Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT')),
					Route::url('index.php?option=' . $this->_option . '&alias='
					.$project->alias . '&action=activate')
				);
			}
			else
			{
				$pathway->addItem(
					stripslashes($project->title),
					Route::url('index.php?option=' . $this->_option . '&alias=' . $project->alias)
				);
			}
		}

		// Controllers
		switch ($this->_controller)
		{
			case 'reports':
				$pathway->addItem(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller)),
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
				);
				break;

			default:
				break;
		}

		// Task is set?
		if (!$this->_task)
		{
			return;
		}

		// Tasks
		switch ($this->_task)
		{
			case 'view':
				if ($this->active && is_object($project))
				{
					switch ($this->active)
					{
						case 'feed':
							// nothing to add
						break;

						default:
							$pathway->addItem(
								ucfirst(Lang::txt('COM_PROJECTS_TAB_'.strtoupper($this->active))),
								Route::url('index.php?option=' . $this->_option . '&alias='
								. $project->alias . '&active=' . $this->active)
							);
						break;
					}
				}
			break;

			case 'setup':
			case 'edit':
				if (!is_object($project) || !$project->id)
				{
					$pathway->addItem(
						Lang::txt(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
					);
					break;
				}
				break;

			case 'browse':
			case 'process':
				$reviewer 	= \JRequest::getWord( 'reviewer', '' );
				if ($reviewer == 'sponsored' || $reviewer == 'sensitive')
				{
					$title = $reviewer == 'sponsored'
										? Lang::txt('COM_PROJECTS_REVIEWER_SPS')
										: Lang::txt('COM_PROJECTS_REVIEWER_HIPAA');

					$pathway->addItem(
						$title,
						Route::url('index.php?option=' . $this->_option
						. '&task=browse&reviewer=' . $reviewer)
					);
				}
				else
				{
					$pathway->addItem(
						Lang::txt(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
					);
				}
			break;

			case 'intro':
			case 'activate':
				// add nothing else
				break;

			default:
				$pathway->addItem(
					Lang::txt(strtoupper($this->_option).'_'.strtoupper($this->_task)),
					Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
				);
				break;
		}
	}

	/**
	 * Log project activity
	 *
	 * @param  int 		$pid		Project ID
	 * @param  string 	$section	Major category of activity
	 * @param  string 	$layout		Plugin or layout name
	 * @param  string 	$action		Task name
	 * @param  int 		$owner		Project owner ID if project owner
	 * @return void
	 */
	protected function _logActivity ($pid = 0, $section = 'general',
		$layout = '', $action = '', $owner = 0)
	{
		// Is logging enabled?
		$enabled = $this->config->get('logging', 0);
		if (!$enabled)
		{
			return false;
		}

		// Is this an ajax call?
		$ajax = \JRequest::getInt( 'ajax', 0 );
		if ($ajax && $enabled == 1)
		{
			return false;
		}

		$juri = \JURI::getInstance();

		// Log activity
		$objLog  				= new Tables\Log( $this->database );
		$objLog->projectid 		= $pid;
		$objLog->userid 		= $this->juser->get('id');
		$objLog->owner 			= intval($owner);
		$objLog->ip 			= \JRequest::ip();
		$objLog->section 		= $section;
		$objLog->layout 		= $layout ? $layout : $this->_task;
		$objLog->action 		= $action ? $action : 'view';
		$objLog->time 			= date('Y-m-d H:i:s');
		$objLog->request_uri 	= \JRequest::getVar('REQUEST_URI', $juri->base(), 'server');
		$objLog->ajax 			= $ajax;
		$objLog->store();
	}

	/**
	 * Notify project team
	 *
	 * @param  string $action
	 * @param  int $managers_only
	 * @return void
	 */
	protected function _notifyTeam($managers_only = 0)
	{
		// Is messaging turned on?
		if ($this->config->get('messaging') != 1)
		{
			return false;
		}

		// Check required
		if (!$this->_identifier)
		{
			return false;
		}

		$message = array();

		// Get project
		if (!isset($this->project) || !is_object($this->project) || !$this->project->alias)
		{
			$obj 		= new Tables\Project( $this->database );
			$this->project 	= $obj->getProject($this->_identifier, $this->juser->get('id'));
			if (!$this->project)
			{
				return false;
			}
		}

		// Set up email config
		$jconfig 		= \JFactory::getConfig();
		$from 			= array();
		$from['name']  	= $jconfig->getValue('config.sitename') . ' ' . Lang::txt('COM_PROJECTS');
		$from['email'] 	= $jconfig->getValue('config.mailfrom');

		// Get team/managers
		$filters = array( 'select'=> 'o.userid, o.invited_code, o.invited_email, o.role ', 'sortby' => 'status' );
		if ($managers_only)
		{
			$filters['role'] = 1;
		}
		// Get team
		$objO = new Tables\Owner( $this->database );
		$team = $objO->getOwners( $this->_identifier, $filters );

		// Must have addressees
		if (empty($team))
		{
			return false;
		}

		$subject_active  = Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_ADDED') . ' ' . $this->project->alias;
		$subject_pending = Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_INVITE') . ' ' . $this->project->alias;

		// Message body
		$eview = new \Hubzero\Component\View( array('name'=>'emails', 'layout' =>'invite_plain') );
		$eview->option 			= $this->_option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $this->project;
		$eview->goto 			= 'alias=' . $this->project->alias;
		$eview->user 			= $this->juser->get('id');
		$eview->delimiter  		= '';

		// Get profile of author group
		if ($this->project->owned_by_group)
		{
			$eview->nativegroup = \Hubzero\User\Group::getInstance( $this->project->owned_by_group );
		}

		// Send out message/email
		foreach ($team as $member)
		{
			$eview->role = $member->role;
			if ($member->userid && $member->userid != $this->juser->get('id') )
			{
				$eview->uid = $member->userid;
				$message['plaintext'] 	= $eview->loadTemplate();
				$message['plaintext'] 	= str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('invite_html');
				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				// Creator
				if ($member->userid == $this->project->created_by_user)
				{
					$subject_active  = Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_CREATOR_CREATED')
					. ' ' . $this->project->alias . '!';
				}

				// Send HUB message
				\JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher = \JDispatcher::getInstance();
				$dispatcher->trigger( 'onSendMessage',
					array(
						'projects_member_added',
						$subject_active,
						$message,
						$from,
						array($member->userid),
						$this->_option
					)
				);
			}
			elseif ($member->invited_email && $member->invited_code)
			{
				$eview->uid 	= 0;
				$eview->code 	= $member->invited_code;
				$eview->email 	= $member->invited_email;

				$message['plaintext'] 	= $eview->loadTemplate();
				$message['plaintext'] 	= str_replace("\n", "\r\n", $message['plaintext']);

				// HTML email
				$eview->setLayout('invite_html');
				$message['multipart'] = $eview->loadTemplate();
				$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

				\Components\Projects\Helpers\Html::email($member->invited_email, $jconfig->getValue('config.sitename')
					. ': ' . $subject_pending, $message, $from);
			}
		}
	}

	/**
	 * Post activity to project feed
	 *
	 * @return     void
	 */
	protected function _postActivity($activity = '', $underline = '', $url = '', $class = 'project')
	{
		$objAA = new Tables\Activity ( $this->database );

		if (isset($this->project) && is_object($this->project) && $this->project->id && $activity)
		{
			return $objAA->recordActivity( $this->project->id, $this->juser->get('id'),
				$activity, $this->project->id, $underline, $url, $class
			);
		}
		return false;
	}
}