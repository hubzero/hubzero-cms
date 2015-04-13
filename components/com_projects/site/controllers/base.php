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

namespace Components\Projects\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Projects\Tables;
use Components\Projects\Helpers;
use Components\Projects\Models;

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
		$this->_publishing = Plugin::isEnabled('projects', 'publications') ? 1 : 0;

		// Setup complete?
		$this->_setupComplete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Include scripts
		$this->_includeScripts();

		// Incoming project identifier
		$id    = Request::getInt( 'id', 0 );
		$alias = Request::getVar( 'alias', '' );
		$this->_identifier = $id ? $id : $alias;

		// Incoming
		$this->_task = strtolower(Request::getWord( 'task', '' ));
		$this->_gid  = Request::getVar( 'gid', 0 );

		// Model
		$this->model = new Models\Project($this->_identifier);

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
		if (Plugin::isEnabled('projects', 'databases'))
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
				. DS . 'tables' . DS . 'database.php');
			require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
				. DS . 'tables' . DS . 'database.version.php');
		}

		// Logging and stats
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'tables' . DS . 'stats.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'tables' . DS . 'log.php');

		// Include external file connection
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'tables' . DS . 'remotefile.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'helpers' . DS . 'connect.php');
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

		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option='
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
		$view = new \Hubzero\Component\View(
			array('name' => 'error', 'layout' => $layout)
		);
		$view->error  	= $this->getError();
		$view->title 	= $this->title;
		$view->display();
		return;
	}

	/**
	 * Build the title for this component
	 *
	 * @return void
	 */
	protected function _buildTitle()
	{
		$active  = isset($this->active) ? $this->active : NULL;

		// Set the title
		$this->title  = $this->_task == 'edit'
			? Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task))
			: Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		// Project info
		if ($this->model->exists())
		{
			if ($this->model->isProvisioned())
			{
				$this->title .= ': ' . Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT');
			}
			else
			{
				$this->title .= ': ' . stripslashes($this->model->get('title'));
			}
		}

		// Other views
		switch ($this->_task)
		{
			case 'browse':
				$reviewer 	 = Request::getVar( 'reviewer', '' );
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
			case 'activate':
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
		$group_tasks = array('start', 'setup', 'view');
		$group       = isset($this->group) ? $this->group : NULL;
		$active      = isset($this->active) ? $this->active : NULL;

		// Point to group if group project
		if (is_object($group) && in_array($this->_task, $group_tasks) )
		{
			Pathway::clear();
			Pathway::append(
				Lang::txt('COM_PROJECTS_GROUPS_COMPONENT'),
				Route::url('index.php?option=com_groups')
			);
			Pathway::append(
				\Hubzero\Utility\String::truncate($group->get('description'), 50),
				Route::url('index.php?option=com_groups&cn=' . $group->cn)
			);
			Pathway::append(
				Lang::txt('COM_PROJECTS_PROJECTS'),
				Route::url('index.php?option=com_groups&cn=' . $group->cn . '&active=projects')
			);
		}
		elseif (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt('COMPONENT_LONG_NAME'),
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Project path
		if ($this->model->exists())
		{
			$alias = $this->model->get('alias');
			$provisioned = $this->model->isProvisioned();
			$title = $this->model->get('title');
		}

		if (!empty($alias))
		{
			if (!empty($provisioned))
			{
				Pathway::append(
					stripslashes(Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT')),
					Route::url('index.php?option=' . $this->_option . '&alias='
					. $alias . '&action=activate')
				);
			}
			else
			{
				Pathway::append(
					stripslashes($title),
					Route::url('index.php?option=' . $this->_option . '&alias=' . $alias)
				);
			}
		}

		// Controllers
		switch ($this->_controller)
		{
			case 'reports':
				Pathway::append(
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
				if ($active && !empty($alias))
				{
					switch ($active)
					{
						case 'feed':
							// nothing to add
						break;

						default:
							Pathway::append(
								ucfirst(Lang::txt('COM_PROJECTS_TAB_'.strtoupper($this->active))),
								Route::url('index.php?option=' . $this->_option . '&alias='
								. $alias . '&active=' . $active)
							);
						break;
					}
				}
			break;

			case 'setup':
			case 'edit':
				if (empty($alias))
				{
					Pathway::append(
						Lang::txt(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task)
					);
					break;
				}
				break;

			case 'browse':
			case 'process':
				$reviewer 	= Request::getWord( 'reviewer', '' );
				if ($reviewer == 'sponsored' || $reviewer == 'sensitive')
				{
					$title = $reviewer == 'sponsored'
										? Lang::txt('COM_PROJECTS_REVIEWER_SPS')
										: Lang::txt('COM_PROJECTS_REVIEWER_HIPAA');

					Pathway::append(
						$title,
						Route::url('index.php?option=' . $this->_option
						. '&task=browse&reviewer=' . $reviewer)
					);
				}
				else
				{
					Pathway::append(
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
				Pathway::append(
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
		$ajax = Request::getInt( 'ajax', 0 );
		if ($ajax && $enabled == 1)
		{
			return false;
		}

		$juri = \JURI::getInstance();

		// Log activity
		$objLog  				= new Tables\Log( $this->database );
		$objLog->projectid 		= $pid;
		$objLog->userid 		= User::get('id');
		$objLog->owner 			= intval($owner);
		$objLog->ip 			= Request::ip();
		$objLog->section 		= $section;
		$objLog->layout 		= $layout ? $layout : $this->_task;
		$objLog->action 		= $action ? $action : 'view';
		$objLog->time 			= date('Y-m-d H:i:s');
		$objLog->request_uri 	= Request::getVar('REQUEST_URI', $juri->base(), 'server');
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
			$this->project 	= $obj->getProject($this->_identifier, User::get('id'));
			if (!$this->project)
			{
				return false;
			}
		}

		// Set up email config
		$from 			= array();
		$from['name']  	= Config::get('config.sitename') . ' ' . Lang::txt('COM_PROJECTS');
		$from['email'] 	= Config::get('config.mailfrom');

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
		$eview->hubShortName 	= Config::get('config.sitename');
		$eview->project 		= $this->project;
		$eview->goto 			= 'alias=' . $this->project->alias;
		$eview->user 			= User::get('id');
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
			if ($member->userid && $member->userid != User::get('id') )
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
				Event::trigger( 'xmessage.onSendMessage',
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

				Helpers\Html::email($member->invited_email, Config::get('config.sitename')
					. ': ' . $subject_pending, $message, $from);
			}
		}
	}
}