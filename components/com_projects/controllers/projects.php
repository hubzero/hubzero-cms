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
 * Primary component controller
 */
class Projects extends Base
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Set the default task
		$this->registerTask('__default', 'intro');

		// Register tasks
		$this->registerTask('suspend', 'changestate');
		$this->registerTask('reinstate', 'changestate');
		$this->registerTask('fixownership', 'changestate');
		$this->registerTask('delete', 'changestate');

		parent::execute();
	}

	/**
	 * Return results for autocompleter
	 *
	 * @return     string JSON
	 */
	public function autocompleteTask()
	{
		$filters = array(
			'limit'    => 20,
			'start'    => 0,
			'admin'    => 0,
			'search'   => trim(\JRequest::getString('value', '')),
			'getowner' => 1
		);

		// Get a record count
		$obj = new Tables\Project( $this->database );

		// Get records
		$rows = $obj->getRecords(
			$this->view->filters, false,
			$this->juser->get('id'), 0, $this->_setupComplete
		);

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$title = str_replace("\n", '', stripslashes(trim($row->title)));
				$title = str_replace("\r", '', $title);

				$item = array(
					'id'   => $row->alias,
					'name' => $title
				);

				// Push exact matches to the front
				if ($row->alias == $filters['search'])
				{
					array_unshift($json, $item);
				}
				else
				{
					$json[] = $item;
				}
			}
		}

		echo json_encode($json);
	}

	/**
	 * Intro to projects (main view)
	 *
	 * @return     void
	 */
	public function introTask()
	{
		// Set task
		$this->_task = 'intro';

		// Incoming
		$action  = \JRequest::getVar( 'action', '' );

		// When logging in
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = \JText::_('COM_PROJECTS_LOGIN_TO_VIEW_YOUR_PROJECTS');
			$this->_login();
			return;
		}

		// Filters
		$this->view->filters 			= array();
		$this->view->filters['mine']   	= 1;
		$this->view->filters['updates'] = 1;
		$this->view->filters['sortby'] 	= 'myprojects';

		// Get a record count
		$obj = new Tables\Project( $this->database );
		$this->view->total = $obj->getCount(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$this->_setupComplete
		);

		// Get records
		$this->view->rows = $obj->getRecords(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$this->_setupComplete
		);

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		// Log activity
		$this->_logActivity();

		// Output HTML
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->database 	= $this->database;
		$this->view->publishing = $this->_publishing;
		$this->view->uid 		= $this->juser->get('id');
		$this->view->guest 		= $this->juser->get('guest');
		$this->view->msg 		= isset($this->_msg) && $this->_msg
								? $this->_msg
								: $this->_getNotifications('success');

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->setLayout('intro')
					->display();
	}

	/**
	 * Features page
	 *
	 * @return     void
	 */
	public function featuresTask()
	{
		// Get language file
		$lang = \JFactory::getLanguage();
		$lang->load('com_projects_features');

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();
		$this->view->title = $this->title;

		// Log activity
		$this->_logActivity();

		// Output HTML
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->guest  	= $this->juser->get('guest');
		$this->view->publishing	= $this->_publishing;

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}
		$this->view->display();
	}

	/**
	 * Browse projects
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		// Incoming
		$reviewer 	= \JRequest::getWord( 'reviewer', '' );
		$action  	= \JRequest::getVar( 'action', '' );

		// Set the pathway
		$this->_task = 'browse';
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check reviewer authorization
		if ($reviewer == 'sensitive' || $reviewer == 'sponsored' )
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = \JText::_('COM_PROJECTS_LOGIN_REVIEWER');
				$this->_login();
				return;
			}

			if (!$this->_checkReviewerAuth($reviewer))
			{
				$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'default') );
				$this->view->error  = \JText::_('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS');
				$this->view->title = $reviewer == 'sponsored'
							 ? \JText::_('COM_PROJECTS_REVIEWER_SPS')
							 : \JText::_('COM_PROJECTS_REVIEWER_HIPAA');
				$this->view->display();
				return;
			}
		}

		// Incoming
		$this->view->filters 				= array();
		$this->view->filters['limit']  		= \JRequest::getVar(
			'limit',
			intval($this->config->get('limit', 25)),
			'request'
		);
		$this->view->filters['start']  		= \JRequest::getInt( 'limitstart', 0, 'get' );
		$this->view->filters['sortby'] 		= \JRequest::getVar( 'sortby', 'title' );
		$this->view->filters['search'] 		= \JRequest::getVar( 'search', '' );
		$this->view->filters['sortdir']		= \JRequest::getVar( 'sortdir', 'ASC');
		$this->view->filters['getowner']	= 1;
		$this->view->filters['reviewer']	= $reviewer;
		$this->view->filters['filterby']	= 'all';

		if ($reviewer == 'sensitive' || $reviewer == 'sponsored')
		{
			$this->view->filters['filterby'] = \JRequest::getVar( 'filterby', 'pending' );
		}

		// Login for private projects
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = \JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PRIVATE_PROJECTS');
			$this->_login();
			return;
		}

		// Get a record count
		$obj = new Tables\Project( $this->database );

		// Get count
		$this->view->total = $obj->getCount(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$this->_setupComplete
		);

		// Get records
		$this->view->rows = $obj->getRecords(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$this->_setupComplete
		);

		// Initiate paging
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Log activity
		$this->_logActivity(0, 'general', 'browse');

		// Output HTML
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->database 	= $this->database;
		$this->view->uid 		= $this->juser->get('id');
		$this->view->guest 		= $this->juser->get('guest');
		$this->view->title 		= $this->title;
		$this->view->reviewer 	= $reviewer;
		$this->view->msg 		= isset($this->_msg) && $this->_msg
								? $this->_msg : $this->_getNotifications('success');
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->display();
	}

	/**
	 * Project view
	 *
	 * @return     void
	 */
	public function viewTask()
	{
		// Incoming
		$preview 		=  \JRequest::getInt( 'preview', 0 );
		$this->active 	=  \JRequest::getVar( 'active', 'feed' );
		$ajax 			=  \JRequest::getInt( 'ajax', 0 );
		$action  		=  \JRequest::getVar( 'action', '' );
		$sync 			=  0;

		// Stop ajax action if user got logged out
		if ($ajax && $this->juser->get('guest'))
		{
			// Project on hold
			$this->view 		= new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'default') );
			$this->view->error  = \JText::_('COM_PROJECTS_PROJECT_RELOGIN');
			$this->view->title  = \JText::_('COM_PROJECTS_PROJECT_RELOGIN_REQUIRED');
			$this->view->display();
			return;
		}

		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj  	= new Tables\Project( $this->database );
		$objO 	= new Tables\Owner( $this->database );
		$objAA 	= new Tables\Activity( $this->database );

		// Is user invited to project?
		$confirmcode = \JRequest::getVar( 'confirm', '' );
		$email 		 = \JRequest::getVar( 'email', '' );

		// Load project
		$this->project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$this->project)
		{
			$this->setError(\JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
			$this->introTask();
			return;
		}
		else
		{
			$pid 	= $this->project->id;
			$alias  = $this->project->alias;
		}

		// Is this a group project?
		$this->group = NULL;
		if ($this->project->owned_by_group)
		{
			$this->group = \Hubzero\User\Group::getInstance( $this->project->owned_by_group );

			// Was owner group deleted?
			if (!$this->group)
			{
				$this->_buildPathway();
				$this->_buildTitle();

				// Options for project creator
				if ($this->project->created_by_user == $this->juser->get('id'))
				{
					$view 			= new \Hubzero\Component\View( array('name'=>'changeowner', 'layout' =>'default') );
					$view->project 	= $this->project;
					$view->task 	= $this->_task;
					$view->option 	= $this->_option;
					$view->config 	= $this->config;
					$view->uid 		= $this->juser->get('id');
					$view->guest 	= $this->juser->get('guest');
					$view->display();
					return;
				}
				else
				{
					// Error
					$this->setError(\JText::_('COM_PROJECTS_PROJECT_OWNER_DELETED'));
					$this->title = \JText::_('COM_PROJECTS_PROJECT_OWNERSHIP_ERROR');
					$this->_showError();
					return;
				}
			}
		}

		// Reconcile members of project groups
		if (!$ajax)
		{
			if ($objO->reconcileGroups($pid, $this->project->owned_by_group))
			{
				$sync = 1;
			}
		}

		// Is project deleted?
		if ($this->project->state == 2)
		{
			$this->setError(\JText::_('COM_PROJECTS_PROJECT_DELETED'));
			$this->introTask();
			return;
		}

		// Get publication of a provisioned project
		if ($this->project->provisioned == 1)
		{
			if (!$this->_publishing)
			{
				$this->setError(\JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
				$this->introTask();
				return;
			}

			$objPub = new Publication($this->database);
			$pub = $objPub->getProvPublication($this->project->id);
		}

		// Check if project is in setup
		if ($this->project->setup_stage < $this->_setupComplete && (!$ajax && $this->active != 'team'))
		{
			$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
				. '&task=setup&alias=' . $this->project->alias);
			return;
		}

		// Sync with system group in case of changes
		if ($sync)
		{
			$objO->sysGroup($this->project->alias, $this->config->get('group_prefix', 'pr-'));

			// Reload project
			$this->project = $obj->getProject($this->project->alias, $this->juser->get('id'));
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check authorization
		$role = $this->project->owner && $this->project->role == 0 ? 4 : $this->project->role;
		$authorized = $this->project->owner ? $role : 0;

		// Do we need to login?
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = \JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PROJECT');
			$this->_login();
			return;
		}

		// Check if they're a member of reviewer group
		$reviewer = false;
		if (!$this->juser->get('guest') && $authorized != 1)
		{
			$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0)
			{
				$sdata_group 	= $this->config->get('sdata_group', '');
				$ginfo_group 	= $this->config->get('ginfo_group', '');

				foreach ($ugs as $ug)
				{
					if ($this->config->get('approve_restricted') && $sdata_group && $ug->cn == $sdata_group )
					{
						$reviewer = 'sensitive';
					}
					elseif ($this->config->get('grantinfo') && $ginfo_group && $ug->cn == $ginfo_group )
					{
						$reviewer = 'sponsored';
					}
				}
			}
		}

		// Determine internal (private for team)/external (public) layout to load
		$layout = ($authorized) ? 'internal' : 'external';
		$layout = ($authorized) && $preview && !$this->project->private ? 'external' : $layout;

		// Invitation view
		if ($confirmcode && (!$this->project->owner or !$this->project->confirmed))
		{
			$match = $obj->matchInvite( $pid, $confirmcode, $email );
			if ($this->juser->get('guest') && $match)
			{
				$layout = 'invited';
			}
			elseif ($match && $objO->load($match))
			{
				if ($this->juser->get('email') == $email)
				{
					// Confirm user
					$objO->status = 1;
					$objO->userid = $this->juser->get('id');

					if (!$objO->store())
					{
						$this->setError( $objO->getError() );
						return false;
					}
					else
					{
						// Sync with system group
						$objO->sysGroup($this->project->alias, $this->config->get('group_prefix', 'pr-'));

						// Go to project page
						$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
							. '&alias=' . $this->project->alias);
						return;
					}
				}
				else
				{
					// Error - different email
					$this->setError(\JText::_('COM_PROJECTS_INVITE_DIFFERENT_EMAIL'));
					$this->_showError();
					return;
				}
			}
		}

		// Is this a provisioned project?
		if ($this->project->provisioned == 1)
		{
			if ($action == 'activate')
			{
				if ($this->juser->get('id') == $this->project->created_by_user && $this->project->setup_stage >= $this->_setupComplete)
				{
					$layout = 'provisioned';
				}
				elseif ($this->juser->get('guest'))
				{
					$this->_msg = \JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PROJECT');
					$this->_login();
					return;
				}
				else
				{
					$this->setError(\JText::_('COM_PROJECTS_ERROR_MUST_BE_PROJECT_CREATOR'));
					$this->_showError();
					return;
				}
			}
			else
			{
				// Redirect to publication
				if (isset($pub) && $pub->id)
				{
					$this->_redirect = \JRoute::_('index.php?option=com_publications&task=submit&pid=' . $pub->id);
					return;
				}
				else
				{
					\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
					return;
				}
			}
		}

		// Private project
		if ($this->project->private && $layout != 'invited')
		{
			// Login required
			if ($this->juser->get('guest'))
			{
				$this->_msg = \JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
				$this->_login();
				return;
			}
			if (!$authorized && !$reviewer)
			{
				\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Is project suspended?
		$suspended = 0;
		if ($this->project->state == 0 && $this->project->setup_stage == $this->_setupComplete)
		{
			if (!$authorized)
			{
				\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
				return;
			}
			$layout = 'suspended';

			// Check who suspended project
			$suspended = $objAA->checkActivity( $pid, \JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}

		// Is project pending approval?
		if ($this->project->state == 5 && $this->project->setup_stage == $this->_setupComplete)
		{
			if ($reviewer)
			{
				$layout = 'external';
			}
			elseif ($this->juser->get('id') != $this->project->created_by_user)
			{
				\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
				return;
			}
			else
			{
				$layout = 'pending';
			}
		}

		$this->view->setLayout( $layout );

		$this->view->project 	= $this->project;
		$this->view->suspended 	= $suspended;
		$this->view->reviewer 	= $reviewer;

		// Provisioned project
		if ($this->project->provisioned == 1)
		{
			// Get JS & CSS
			$document = \JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'publications.css');

			$this->view->pub 	   = isset($pub) ? $pub : '';
			$this->view->team 	   = $objO->getOwnerNames($this->_identifier);
			$this->view->suggested = \Components\Projects\Helpers\Html::suggestAlias($pub->title);
			$this->view->verified  = $this->model->check($this->view->suggested, $pid, 0);
			$this->view->suggested = $this->view->verified ? $this->view->suggested : '';
		}

		// First-time visit, record join activity
		if ($this->project->owner && !$this->project->provisioned && $this->active == 'feed' && $this->project->confirmed && !$ajax)
		{
			if (!$this->project->lastvisit )
			{
				$aid = $this->_postActivity(\JText::_('COM_PROJECTS_ACTIVITY_JOINED_THE_PROJECT'), '', '', 'team');
				if ($aid)
				{
					$objO->saveParam ( $pid, $this->juser->get('id'), $param = 'join_activityid', $value = $aid );
				}

				// If newly created - remove join activity of project creator
				$timecheck = \JFactory::getDate(time() - (10 * 60)); // last second
				if ($this->project->created_by_user == $this->juser->get('id') && $timecheck <= $this->project->created)
				{
				    $objAA->deleteActivity($aid);
				}
			}
		}

		// Get latest log from user session
		$jsession = \JFactory::getSession();

		// Log activity
		if (!$jsession->get('projects-nolog'))
		{
			$this->_logActivity($pid, 'project', $this->active, $action, $authorized);
		}

		// Allow future logging
		if ($this->config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);
		}

		// Get plugin
		\JPluginHelper::importPlugin( 'projects');
		$dispatcher = \JDispatcher::getInstance();

		// Get all plugins
		$plugins 	= $dispatcher->trigger( 'onProjectAreas', array( 'all' => true ) );

		// Get tabbed plugins
		$this->view->tabs = \Components\Projects\Helpers\Html::getTabs($plugins);

		// Go through plugins
		$this->view->content = '';
		if ($layout == 'internal')
		{
			$plugin = $this->active == 'feed' ? 'blog' : $this->active;
			$plugin = $this->active == 'info' ? '' : $plugin;

			// Get active plugins (some may not be in tabs)
			$activePlugins = \Components\Projects\Helpers\Html::getPluginNames($plugins);

			// Get plugin content
			if ($this->active != 'info')
			{
				// Do not go further if plugin is inactive or does not exist
				if (!in_array($plugin, $activePlugins))
				{
					if ($ajax)
					{
						// Plugin not active in this project
						echo '<p class="error">' . \JText::_('COM_PROJECTS_ERROR_CONTENT_CANNOT_LOAD') . '</p>';
						return;
					}

					$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
						. '&task=view&alias=' . $this->project->alias);
					return;
				}

				// Plugin params
				$plugin_params = array(
					$this->project,
					$this->_option,
					$authorized,
					$this->juser->get('id'),
					$this->_getNotifications('success'),
					$this->_getNotifications('error'),
					$action,
					array($plugin)
				);

				// Get plugin content
				$sections = $dispatcher->trigger( 'onProject', $plugin_params);

				// Output
				if (!empty($sections))
				{
					foreach ($sections as $section)
					{
						if (isset($section['msg']) && !empty($section['msg']))
						{
							$this->_setNotification($section['msg']['message'], $section['msg']['type']);
						}

						if (isset($section['html']) && $section['html'])
						{
							if ($ajax)
							{
								// AJAX output
								echo $section['html'];
								return;
							}
							else
							{
								// Normal output
								$this->view->content .= $section['html'];
							}
						}
						elseif (isset($section['referer']) && $section['referer'] != '')
						{
							if ($this->config->get('logging', 0))
							{
								$jsession->set('projects-nolog', 1);
							}
							$this->_redirect = $section['referer'];
							return;
						}
					} // end foreach
				}
				else
				{
					// No html output
					$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
						. '&task=view&alias=' . $this->project->alias);
					return;
				}
			}

			$dispatcher->trigger( 'onProjectCount', array( $this->project, &$counts) );
			$counts['newactivity'] = $objAA->getNewActivityCount( $this->project->id, $this->juser->get('id'));
			$this->view->project->counts = $counts;
		}

		// Record page view
		if ($this->project->owner && $this->active == 'feed' && $this->project->confirmed)
		{
			$objO->recordView($pid, $this->juser->get('id'));
		}

		// Get project params
		$this->view->params = new \JParameter( $this->project->params );

		// Get team for public page
		if ($layout == 'external' && $this->view->params->get('team_public', 0))
		{
			$this->view->team = $objO->getOwners( $pid, $filters = array('status' => 1) );
		}

		// Get additional modules for team members
		if ($layout == 'internal')
		{
			if ($this->active == 'feed')
			{
				// Hide welcome screen?
				$c = \JRequest::getInt( 'c', 0 );
				if ($c)
				{
					$objO->saveParam(
						$this->project->id,
						$this->juser->get('id'),
						$param = 'hide_welcome', 1
					);
					$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
						. '&task=view&alias=' . $this->project->alias);
					return;
				}
			}

			// Get notification
			$notification       		= $dispatcher->trigger('onProjectNotification',
				array( $this->project, $this->juser->get('id'), $this->active, $this->_option )
			);
			$this->view->notification 	= $notification && !empty($notification)
				? $notification[0] : NULL;

			// Get side content
			$sideContent       			= $dispatcher->trigger('onProjectExtras',
				array( $this->project, $this->juser->get('id'), $this->active, $this->_option )
			);
			$this->view->sideContent 	= $sideContent && !empty($sideContent)
				? $sideContent[0] : NULL;
		}

		// Output HTML
		$this->view->title  	= $this->title;
		$this->view->active 	= $this->active;
		$this->view->task 		= $this->_task;
		$this->view->authorized	= $authorized;
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->uid 		= $this->juser->get('id');
		$this->view->guest 		= $this->juser->get('guest');
		$this->view->msg 		= $this->_getNotifications('success');

		if ($layout == 'invited')
		{
			$this->view->confirmcode  = $confirmcode;
			$this->view->email		  = $email;
		}

		$error 	= $this->getError() ? $this->getError() : $this->_getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}
		$this->view->display();
		return;
	}

	/**
	 * Activate provisioned project
	 *
	 * @return     void
	 */
	public function activateTask()
	{
		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate needed classes
		$obj  = new Tables\Project( $this->database );
		$objO = new Tables\Owner( $this->database );

		// Get Project
		$this->project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$obj->loadProject($this->_identifier) or !$this->project)
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Must be project creator
		if ($this->project->created_by_user != $this->juser->get('id'))
		{
			\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
			return;
		}

		// Must be a provisioned project to be activated
		if ($this->project->provisioned != 1)
		{
			// Redirect to project page
			$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
				. '&alias=' . $this->project->alias);
			return;
		}

		// Redirect to setup if activation not complete
		if ($this->project->setup_stage < $this->_setupComplete)
		{
			$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
				. '&task=setup&alias=' . $this->project->alias);
			return;
		}

		// Get publication of a provisioned project
		$objPub = new Publication($this->database);
		$pub = $objPub->getProvPublication($this->project->id);

		// Incoming
		$name  = trim(\JRequest::getVar( 'new-alias', '', 'post' ));
		$title = trim(\JRequest::getVar( 'title', '', 'post' ));
		$pubid = trim(\JRequest::getInt( 'pubid', 0, 'post' ));

		$name = preg_replace('/ /', '', $name);
		$name = strtolower($name);

		// Check incoming data
		if (!$this->model->check($name, $this->project->id))
		{
			$this->setError( \JText::_('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY') );
		}
		elseif ($title == '' or strlen($title) < 3)
		{
			$this->setError( \JText::_('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY') );
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Return to page in case of error
		if ($this->getError())
		{
			$this->view->setLayout( 'provisioned' );
			$this->view->project 		= $this->project;
			$this->view->project->title = $title;

			// Output HTML
			$this->view->pub 		 	= isset($pub) ? $pub : '';
			$this->view->team 	 		= $objO->getOwnerNames($this->_identifier);
			$this->view->suggested 		= $name;
			$this->view->verified  		= $this->model->check($name, $this->project->id);
			$this->view->suggested 		= $this->view->verified ? $this->view->suggested : '';
			$this->view->title  		= $this->title;
			$this->view->active 		= $this->active;
			$this->view->task 			= $this->_task;
			$this->view->authorized 	= 1;
			$this->view->option 		= $this->_option;
			$this->view->config 		= $this->config;
			$this->view->uid 			= $this->juser->get('id');
			$this->view->guest 			= $this->juser->get('guest');
			$this->view->msg 			= isset($this->_msg) ? $this->_msg : '';

			if ($this->getError())
			{
				$this->view->setError( $this->getError() );
			}

			$this->view->display();
			return;
		}

		// Get Publications helper
		$helper = new PublicationHelper($this->database);

		// Get project parent directory
		$path 	 = $helper->buildDevPath($this->project->alias, '', '', '');
		$newpath = $helper->buildDevPath($name, '', '', '');

		// Rename project parent directory
		if (is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!\JFolder::copy($path, $newpath, '', true))
			{
				$this->setError( \JText::_('COM_PROJECTS_FAILED_TO_COPY_FILES') );
			}
			else
			{
				// Delete original repo
				\JFolder::delete($path);
			}
		}

		// Save new alias & title
		if (!$this->getError())
		{
			$obj->title 		= \Hubzero\Utility\String::truncate($title, 250);
			$obj->alias 		= $name;
			$obj->state 		= 0;
			$obj->setup_stage 	= $this->_setupComplete - 1;
			$obj->modified		= \JFactory::getDate()->toSql();
			$obj->modified_by 	= $this->juser->get('id');

			// Save changes
			if (!$obj->store())
			{
				$this->setError( $obj->getError() );
			}
			if (!$obj->id)
			{
				$obj->checkin();
			}
		}

		// Log activity
		$this->_logActivity($obj->id, 'provisioned', 'activate', 'save', 1);

		// Send to continue setup
		$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
			. '&task=setup&alias=' . $obj->alias);
		return;
	}

	/**
	 * Change project status
	 *
	 * @return     void
	 */
	public function changestateTask()
	{
		if (!$this->_identifier)
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj = new Tables\Project( $this->database );
		$objAA = new Tables\Activity ( $this->database );

		// Load project
		if (!$obj->loadProject($this->_identifier))
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Is project deleted?
		if ($obj->state == 2)
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_DELETED') );
			return;
		}

		// Already suspended
		if ($this->_task == 'suspend' && $obj->state == 0)
		{
			$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
				. '&alias=' . $obj->alias);
			return;
		}

		// Suspended by admin: manager cannot activate
		if ($this->_task == 'reinstate')
		{
			$suspended = $objAA->checkActivity( $obj->id,
				\JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED')
			);
			if ($suspended == 1)
			{
				\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = \JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
			$this->_login();
			return;
		}

		// Fix ownership?
		if ($this->_task == 'fixownership')
		{
			$keep 	 = \JRequest::getInt( 'keep', 0 );
			$groupid = $obj->owned_by_group;
			if ($obj->created_by_user != $this->juser->get('id'))
			{
				\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
				return;
			}
			if (!$groupid)
			{
				// Nothing to fix
				$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
					. '&alias=' . $obj->alias);
				return;
			}
			$obj->owned_by_group = 0;

			// Make sure creator is still in team
			$objO = new Tables\Owner( $this->database );
			$objO->saveOwners ( $obj->id, $this->juser->get('id'), $this->juser->get('id'), 0, 1, 1, 1 );

			// Remove owner group affiliation for all team members
			$objO->removeGroupDependence( $obj->id, $groupid );

			if ($keep)
			{
				$obj->owned_by_user = $this->juser->get('id');
			}
			else
			{
				$obj->state = 2;
			}
		}

		// Check authorization
		$authorized = $this->_authorize();
		if ($authorized != 1)
		{
			// Only managers can change project state
			\JError::raiseError( 403, \JText::_('ALERTNOTAUTH') );
			return;
		}

		// Update project
		$obj->modified = \JFactory::getDate()->toSql();
		$obj->modified_by = $this->juser->get('id');
		if ($this->_task != 'fixownership')
		{
			$obj->state = $this->_task == 'suspend' ? 0 : 1;
			$obj->state = $this->_task == 'delete' ? 2 : $obj->state;
		}

		if (!$obj->store())
		{
			$this->setError( $obj->getError() );
			return false;
		}

		// Log activity
		$this->_logActivity($obj->id, 'project', 'status', $this->_task, $authorized);

		if ($this->_task != 'fixownership')
		{
			// Add activity
			$what = ($this->_task == 'suspend')
				? \JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED')
				: \JText::_('COM_PROJECTS_ACTIVITY_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$what = \JText::_('COM_PROJECTS_ACTIVITY_PROJECT_DELETED');
			}
			$objAA->recordActivity( $obj->id, $this->juser->get('id'), $what );

			// Send to project page
			$this->_msg = $this->_task == 'suspend'
				? \JText::_('COM_PROJECTS_PROJECT_SUSPENDED')
				: \JText::_('COM_PROJECTS_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$this->setError(\JText::_('COM_PROJECTS_PROJECT_DELETED'));
				$this->introTask();
				return;
			}
		}

		$this->_task = 'view';
		$this->viewTask();
		return;
	}

	/**
	 * Authenticate for outside services
	 *
	 * @return     void
	 */
	public function authTask()
	{
		// Incoming
		$error  = \JRequest::getVar( 'error', '', 'get' );
		$code   = \JRequest::getVar( 'code', '', 'get' );

		$state  = \JRequest::getVar( 'state', '', 'get' );
		$json	=  base64_decode($state);
		$json 	=  json_decode($json);

		$service = $json->service ? $json->service : 'google';
		$this->_identifier = $json->alias;

		// Load project
		$obj = new Tables\Project( $this->database );
		if (!$this->_identifier || !$obj->loadProject($this->_identifier) )
		{
			\JError::raiseError( 404, \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Successful authorization grant, fetch the access token
		if ($code)
		{
			$return  = \JRoute::_('index.php?option=' . $this->_option . '&alias='
				. $this->_identifier . '&active=files&action=connect&service='
				. $service . '&code=' . $code);
		}
		elseif (isset($json->return))
		{
			$return = $json->return . '&service=' . $service;
		}

		// Catch errors
		if ($error)
		{
			$error =  $error == 'access_denied'
				? \JText::_('Sorry, we cannot connect you to external file service without your permission')
				: \JText::_('Sorry, we cannot connect you to external file service at this time');
			$this->_setNotification($error, 'error');
			$return = $json->return;
		}

		$this->_redirect = $return;
		return;
	}

	/**
	 * Reviewers actions (sensitive data, sponsored research)
	 *
	 * @return     void
	 */
	public function processTask()
	{
		// Incoming
		$reviewer 	= \JRequest::getWord( 'reviewer', '' );
		$action  	= \JRequest::getVar( 'action', '' );
		$comment  	= \JRequest::getVar( 'comment', '' );
		$approve  	= \JRequest::getInt( 'approve', 0 );
		$filterby  	= \JRequest::getVar( 'filterby', 'pending' );
		$notify 	= \JRequest::getVar( 'notify', 0, 'post' );

		// Instantiate a project and related classes
		$obj 		= new Tables\Project( $this->database );
		$objAA 		= new Tables\Activity ( $this->database );

		// Check authorization
		$authorized = $this->_checkReviewerAuth($reviewer);
		if (!$authorized)
		{
			$this->setError( \JText::_('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS') );
			return;
		}

		// We need to have a project
		if (!$this->_identifier)
		{
			$this->setError( \JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
		}

		// Load project
		if (!$obj->loadProject($this->_identifier))
		{
			$this->setError( \JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
		}

		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Get project params
		$params = new \JParameter( $obj->params );

		// Log activity
		$this->_logActivity($obj->id, 'reviewer', $reviewer, $action, $authorized);

		if ($action == 'save' && !$this->getError() && $obj->id)
		{
			$cbase = $obj->admin_notes;

			// Meta data for comment
			$now = \JFactory::getDate()->toSql();
			$actor = $this->juser->get('name');
			$meta = '<meta>' . \JHTML::_('date', $now, 'M d, Y') . ' - ' . $actor . '</meta>';

			// Save approval
			if ($reviewer == 'sensitive')
			{
				$approve = $approve == 1 && $obj->state == 5 ? 1 : 0; // can only approve pending project
				$obj->state = $approve ? 1 : $obj->state;
			}
			elseif ($reviewer == 'sponsored')
			{
				$grant_agency 		= \JRequest::getVar( 'grant_agency', '' );
				$grant_title 		= \JRequest::getVar( 'grant_title', '' );
				$grant_PI 			= \JRequest::getVar( 'grant_PI', '' );
				$grant_budget 		= \JRequest::getVar( 'grant_budget', '' );
				$grant_approval 	= \JRequest::getVar( 'grant_approval', '' );
				$rejected 			= \JRequest::getVar( 'rejected', 0 );

				// New approval
				if (trim($params->get('grant_approval')) == '' && trim($grant_approval) != ''
				&& $params->get('grant_status') != 1 && $rejected != 1)
				{
					// Increase
					$approve = 1;

					// Bump up quota
					$premiumQuota = \Components\Projects\Helpers\Html::convertSize(
						floatval($this->config->get('premiumQuota', '30')), 'GB', 'b');
					$obj->saveParam($obj->id, 'quota', htmlentities($premiumQuota));

					// Bump up publication quota
					$premiumPubQuota = \Components\Projects\Helpers\Html::convertSize(
						floatval($this->config->get('premiumPubQuota', '10')), 'GB', 'b');
					$obj->saveParam($obj->id, 'pubQuota', htmlentities($premiumPubQuota));
				}

				// Reject
				if ($rejected == 1 && $params->get('grant_status') != 2)
				{
					$approve = 2;
				}

				$obj->saveParam($obj->id, 'grant_budget', htmlentities($grant_budget));
				$obj->saveParam($obj->id, 'grant_agency', htmlentities($grant_agency));
				$obj->saveParam($obj->id, 'grant_title', htmlentities($grant_title));
				$obj->saveParam($obj->id, 'grant_PI', htmlentities($grant_PI));
				$obj->saveParam($obj->id, 'grant_approval', htmlentities($grant_approval));
				if ($approve)
				{
					$obj->saveParam($obj->id, 'grant_status', $approve);
				}
			}

			// Save comment
			if (trim($comment) != '')
			{
				$comment = \Hubzero\Utility\String::truncate($comment, 500);
				$comment = \Hubzero\Utility\Sanitize::stripAll($comment);
				if (!$approve)
				{
					$cbase  .= '<nb:' . $reviewer . '>' . $comment . $meta . '</nb:' . $reviewer . '>';
				}
			}
			if ($approve)
			{
				if ($reviewer == 'sensitive')
				{
					$cbase  .= '<nb:' . $reviewer . '>' . \JText::_('COM_PROJECTS_PROJECT_APPROVED_HIPAA');
					$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
					$cbase  .= $meta . '</nb:' . $reviewer . '>';
				}
				if ($reviewer == 'sponsored')
				{
					if ($approve == 1)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . \JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS') . ' '
						. ucfirst(\JText::_('COM_PROJECTS_APPROVAL_CODE')) . ': ' . $grant_approval;
						$cbase  .= (trim($comment) != '') ? '. ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
					elseif ($approve == 2)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . \JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS');
						$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
				}
			}

			$obj->admin_notes = $cbase;

			// Save changes
			if ($approve || $comment)
			{
				if (!$obj->store())
				{
					$this->setError( $obj->getError() );
				}

				// Get updated project
				$this->project = $obj->getProject($obj->id, $this->juser->get('id'));

				$admingroup = $reviewer == 'sensitive'
					? $this->config->get('sdata_group', '')
					: $this->config->get('ginfo_group', '');

				if (\Hubzero\User\Group::getInstance($admingroup))
				{
					$admins = \Components\Projects\Helpers\Html::getGroupMembers($admingroup);
					$admincomment = $comment
						? $actor . ' ' . \JText::_('COM_PROJECTS_SAID') . ': ' . $comment
						: '';

					// Send out email to admins
					if (!empty($admins))
					{
						\Components\Projects\Helpers\Html::sendHUBMessage(
							$this->_option,
							$this->config,
							$this->project,
							$admins,
							\JText::_('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
							'projects_new_project_admin',
							'admin',
							$admincomment,
							$reviewer
						);
					}
				}
			}

			// Pass success or error message
			if ($this->getError())
			{
				$this->_setNotification($this->getError(), 'error');
			}
			else
			{
				if ($approve)
				{
					if ($reviewer == 'sensitive')
					{
						$this->_setNotification(\JText::_('COM_PROJECTS_PROJECT_APPROVED_HIPAA_MSG') );

						// Send out emails to team members
						$this->_notifyTeam();
					}
					if ($reviewer == 'sponsored')
					{
						$notification =  $approve == 2
								? \JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS_MSG')
								: \JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS_MSG');
						$this->_setNotification($notification);
					}
				}
				elseif ($comment)
				{
					$this->_setNotification(\JText::_('COM_PROJECTS_REVIEWER_COMMENT_POSTED') );
				}

				// Add to project activity feed
				if ($notify)
				{
					$activity = '';
					if ($approve && $reviewer == 'sponsored')
					{
						$activity = $approve == 2
								? \JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS_ACTIVITY')
								: \JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS_ACTIVITY');
					}
					elseif ($comment)
					{
						$activity = \JText::_('COM_PROJECTS_PROJECT_REVIEWER_COMMENTED');
					}

					if ($activity)
					{
						$objAA = new Tables\Activity( $this->database );
						$aid = $objAA->recordActivity( $obj->id, $this->juser->get('id'),
							$activity, $obj->id, '', '', 'admin', 0, 1, 1 );

						// Append comment to activity
						if ($comment && $aid)
						{
							$objC = new Tables\Comment( $this->database );
							$cid = $objC->addComment( $aid, 'activity', $comment,
							$this->juser->get('id'), $aid, 1 );

							if ($cid)
							{
								$objAA = new Tables\Activity( $this->database );
								$caid = $objAA->recordActivity( $obj->id, $this->juser->get('id'),
									\JText::_('COM_PROJECTS_COMMENTED') . ' '
									. \JText::_('COM_PROJECTS_ON') . ' '
									.  \JText::_('COM_PROJECTS_AN_ACTIVITY'),
									$cid, '', '', 'quote', 0, 1, 1 );

								if ($caid)
								{
									$objC->storeCommentActivityId($cid, $caid);
								}
							}
						}
					}
				}
			}

			// Go back to project listing
			$this->_redirect = \JRoute::_('index.php?option=' . $this->_option
				. '&task=browse&reviewer=' . $reviewer . '&filterby=' . $filterby);
			return;
		}
		else
		{
			// Instantiate a new view
			$this->view->setLayout( 'review' );

			// Output HTML
			$this->view->reviewer 	= $reviewer;
			$this->view->ajax 		= \JRequest::getInt( 'ajax', 0 );
			$this->view->title 		= $this->title;
			$this->view->option 	= $this->_option;
			$this->view->project	= $obj;
			$this->view->params		= $params;
			$this->view->config 	= $this->config;
			$this->view->database 	= $this->database;
			$this->view->action		= $action;
			$this->view->filterby	= $filterby;
			$this->view->uid 		= $this->juser->get('id');
			$this->view->msg 		= isset($this->_msg) && $this->_msg
									? $this->_msg : $this->_getNotifications('success');
			if ($this->getError())
			{
				$this->view->setError( $this->getError() );
			}
			$this->view->display();
		}
	}
}