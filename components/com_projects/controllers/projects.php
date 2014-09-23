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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class ProjectsControllerProjects extends \Hubzero\Component\SiteController
{
	/**
	 * Method to set a property of the class
	 *
	 * @param     string $property Name of property
	 * @param     mixed  $value    Value of the property
	 * @return    void
	 */
	public function setVar($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Method to get a property of the class
	 *
	 * @param      string $property Name of property
	 * @return     mixed
	 */
	public function getVar($property)
	{
		return $this->$property;
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Publishing enabled?
		$this->_publishing =
			is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
				. 'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications')
			? 1 : 0;

		// Include scripts
		$this->_inlcudeScripts();

		// Is component on?
		if (!$this->config->get( 'component_on', 0 ))
		{
			$this->_redirect = '/';
			return;
		}

		// Incoming
		$this->_task 		= strtolower(JRequest::getVar( 'task', '' ));
		$this->_gid   		= JRequest::getVar( 'gid', 0 );
		$this->_id 			= JRequest::getInt( 'id', 0 );
		$this->_alias   	= JRequest::getVar( 'alias', '' );
		$this->_identifier  = $this->_id ? $this->_id : $this->_alias;

		// Add fancybox styling
		$this->_getStyles('', 'jquery.fancybox.css', true);

		// Set the default task
		$this->registerTask('__default', 'intro');

		// Register tasks
		$this->registerTask('start', 'setup');
		$this->registerTask('suspend', 'changestate');
		$this->registerTask('reinstate', 'changestate');
		$this->registerTask('fixownership', 'changestate');
		$this->registerTask('delete', 'changestate');
		$this->registerTask('get', 'pubview');

		parent::execute();
	}

	/**
	 * Pub view for project files, notes etc.
	 *
	 * @return     void
	 */
	public function pubviewTask()
	{
		if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');
		}
		else
		{
			return false;
		}

		// Incoming
		$stamp = JRequest::getVar( 's', '' );

		// Clean up stamp value (only numbers and letters)
		$regex  = array('/[^a-zA-Z0-9]/');
		$stamp  = preg_replace($regex, '', $stamp);

		// Load item reference
		$objSt = new ProjectPubStamp( $this->database );
		if (!$stamp || !$objSt->loadItem($stamp))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Can only serve files or notes at the moment
		if (!in_array($objSt->type, array('files', 'notes', 'publications')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
			return;
		}

		// Get plugin
		JPluginHelper::importPlugin( 'projects', $objSt->type );
		$dispatcher = JDispatcher::getInstance();

		// Serve requested item
		$content = $dispatcher->trigger('serve', array($objSt->projectid, $objSt->reference));

		// Return content
		if ($content[0])
		{
			return $content[0];
		}

		// Redirect if nothing fetched
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option)
		);

		return;
	}

	/**
	 * Set notifications
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	public function setNotification( $message, $type = 'success' )
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
	public function getNotifications($type = 'success')
	{
		// Get messages in quene
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
	 * Push scripts to document head
	 *
	 * @return     void
	 */
	protected function _getProjectScripts()
	{
		$this->_getScripts('assets/js/' . $this->_name);
	}

	/**
	 * Include necessary scripts
	 *
	 * @return     void
	 */
	protected function _inlcudeScripts()
	{
		// Enable publication management
		if ($this->_publishing)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'version.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'access.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'audience.level.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'audience.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'author.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'license.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'category.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'master.type.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'screenshot.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'attachment.php');
			require_once( JPATH_ROOT . DS . 'components'.DS
				. 'com_publications' . DS . 'helpers' . DS . 'helper.php');
		}

		// Database development on?
		if ( is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.database.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.version.php');
		}

		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.log.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
				. 'helpers' . DS . 'connect.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
				. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
		require_once( JPATH_SITE . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'remote' . DS . 'google.php' );
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option
			. '&task=' . $this->_task), 'server');

		if (substr($rtrn, -1, 1) != '/'
			&& substr($rtrn, -9, 9) != 'sponsored'
			&& substr($rtrn, -9, 9) != 'sensitive')
		{
			$rtrn .= DS;
		}

		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login').'?return=' . base64_encode($rtrn),
			$this->_msg,
			'warning'
		);
	}

	/**
	 * Intro to projects (main view)
	 *
	 * @return     void
	 */
	public function introTask()
	{
		$this->view->setLayout('intro');
		$this->_task = 'intro';

		// Incoming
		$action  = JRequest::getVar( 'action', '' );

		// When logging in
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_TO_VIEW_YOUR_PROJECTS');
			$this->_login();
			return;
		}

		// Filters
		$this->view->filters 			= array();
		$this->view->filters['mine']   	= 1;
		$this->view->filters['updates'] = 1;
		$this->view->filters['sortby'] 	= 'myprojects';

		// How many setup steps do we have?
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Get a record count
		$obj = new Project( $this->database );
		$this->view->total = $obj->getCount(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$setup_complete
		);

		// Get records
		$this->view->rows = $obj->getRecords(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$setup_complete
		);

		// Add the CSS to the template
		$this->_getStyles('', 'introduction.css', true);
		$this->_getStyles();

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);
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
								: $this->getNotifications('success');

		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->display();
	}

	/**
	 * Features page
	 *
	 * @return     void
	 */
	public function featuresTask()
	{
		// Get language file
		$lang = JFactory::getLanguage();
		$lang->load('com_projects_features');

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);
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
		$reviewer 	= JRequest::getVar( 'reviewer', '' );
		$action  	= JRequest::getVar( 'action', '' );
		$layout	 	= 'browse';

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Set the pathway
		$this->_task = 'browse';
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);

		// Check reviewer authorization
		if ($reviewer == 'sensitive' || $reviewer == 'sponsored' )
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PROJECTS_LOGIN_REVIEWER');
				$this->_login();
				return;
			}

			if (ProjectsHelper::checkReviewerAuth($reviewer, $this->config))
			{
				$layout = $reviewer;
			}
			else
			{
				$view 		 = new JView( array('name' => 'error') );
				$view->error = JText::_('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS');
				$view->title = $reviewer == 'sponsored'
							 ? JText::_('COM_PROJECTS_REVIEWER_SPS')
							 : JText::_('COM_PROJECTS_REVIEWER_HIPAA');
				$view->display();
				return;
			}
		}

		// Set layout
		$this->view->setLayout( $layout );

		// Incoming
		$this->view->filters 				= array();
		$this->view->filters['limit']  		= JRequest::getVar(
			'limit',
			intval($this->config->get('limit', 25)),
			'request'
		);
		$this->view->filters['start']  		= JRequest::getInt( 'limitstart', 0, 'get' );
		$this->view->filters['sortby'] 		= JRequest::getVar( 'sortby', 'title' );
		$this->view->filters['search'] 		= JRequest::getVar( 'search', '' );
		$this->view->filters['sortdir']		= JRequest::getVar( 'sortdir', 'ASC');
		$this->view->filters['getowner']	= 1;
		$this->view->filters['reviewer']	= $reviewer;

		if ($reviewer == 'sensitive' || $reviewer == 'sponsored')
		{
			$this->view->filters['filterby']	= JRequest::getVar( 'filterby', 'pending' );
		}

		// Get config
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Login for private projects
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PRIVATE_PROJECTS');
			$this->_login();
			return;
		}

		// Get a record count
		$obj = new Project( $this->database );

		// Get count
		$this->view->total = $obj->getCount(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$setup_complete
		);

		// Get records
		$this->view->rows = $obj->getRecords(
			$this->view->filters,
			$admin = false,
			$this->juser->get('id'),
			0,
			$setup_complete
		);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Log activity
		$this->_logActivity(0, 'general', $layout);

		// Output HTML
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->database 	= $this->database;
		$this->view->uid 		= $this->juser->get('id');
		$this->view->guest 		= $this->juser->get('guest');
		$this->view->title 		= $this->title;
		$this->view->reviewer 	= $reviewer;
		$this->view->msg 		= isset($this->_msg) && $this->_msg
								? $this->_msg : $this->getNotifications('success');
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->display();
	}

	/**
	 * Setup screens
	 *
	 * @return     void
	 */
	public function setupTask()
	{
		// Incoming
		$save_stage 	= JRequest::getInt( 'save_stage', '0');
		$extended 		= JRequest::getInt( 'extended', 0, 'post');
		$requested_step = JRequest::getInt( 'step', 6);
		$tempid 		= JRequest::getInt( 'tempid', 0 );
		$restricted 	= JRequest::getWord( 'restricted', 'no', 'post' );
		$group 			= '';
		$pid 			= 0;

		// Set the page title
		$this->_buildTitle(null);

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_SETUP');
			$this->_login();
			return;
		}

		// Instantiate a project
		$obj = new Project( $this->database );

		// Get project information
		if ($this->_identifier)
		{
			// Get Project
			$project = $obj->getProject($this->_identifier, $this->juser->get('id'));

			if (!$obj->loadProject($this->_identifier) or !$project)
			{
				JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
				return;
			}

			$pid = $project->id;
			$alias = $project->alias;

			// Is project deleted?
			if ($project->state == 2)
			{
				JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_DELETED') );
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

			if ($creatorgroup)
			{
				$cgroup = \Hubzero\User\Group::getInstance($creatorgroup);
				if ($cgroup)
				{
					if (!$cgroup->is_member_of('members',$this->juser->get('id')) &&
						!$cgroup->is_member_of('managers',$this->juser->get('id')))
					{
						$this->_buildPathway(null);
						$view 			= new JView( array('name'=>'error', 'layout' => 'restricted') );
						$view->error  	= JText::_('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP');
						$view->title 	= $this->title;
						$view->display();
						return;
					}
				}
			}

			// New entry or error
			$obj->id 			= 0;
			$obj->alias 		= JRequest::getCmd( 'name', '', 'post' );
			$obj->title 		= JRequest::getVar( 'title', '', 'post' );
			$obj->about 		= rtrim(\Hubzero\Utility\Sanitize::clean(JRequest::getVar( 'about', '', 'post' )));
			$obj->type 			= JRequest::getInt( 'type', 1, 'post' );
			$obj->setup_stage 	= 0;
			$obj->private 		= 1;
		}

		// Instantiate related classes
		$objO = new ProjectOwner( $this->database );

		// Get group ID
		if ($this->_gid)
		{
			// Load the group page
			$group = \Hubzero\User\Group::getInstance( $this->_gid );

			// Ensure we found the group info
			if (!is_object($group) || (!$group->get('gidNumber') && !$group->get('cn')) )
			{
				JError::raiseError( 404, JText::_('COM_PROJECTS_NO_GROUP_FOUND') );
				return;
			}
			$this->_gid = $group->get('gidNumber');

			// Make sure we have up-to-date group membership information
			$objO->reconcileGroups($pid);
		}

		// Check authorization
		$authorized = $this->_authorize($pid);
		if ($pid && (!$authorized or $authorized != 1 or $obj->created_by_user != $this->juser->get('id')))
		{
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		elseif (!$pid && $this->_gid && !$authorized)
		{
			// Check group authorization to create a project
			if (!$group->is_member_of('members',$this->juser->get('id'))
				&& !$group->is_member_of('managers',$this->juser->get('id')))
			{
				JError::raiseError( 403, JText::_('COM_PROJECTS_ALERTNOTAUTH_GROUP') );
				return;
			}
		}

		// Do we have 2 or 3 steps in the process (configurable)
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Is earlier setup stage requested and are we allowed to go there?
		$stage = $requested_step != 6
				 && $obj->setup_stage >= $requested_step
				 && $obj->setup_stage != $setup_complete
				 ? $requested_step : $obj->setup_stage;

		// Get temp id for saving image before saving project
		if ($stage < 1)
		{
			$tempid = $tempid ? $tempid : ProjectsHtml::generateCode (4, 4, 0, 1, 0);
		}

		// Saving new team members?
		$members = urldecode(trim(JRequest::getVar( 'newmember', '' )));
		$groups  = urldecode(trim(JRequest::getVar( 'newgroup', '' )));

		// Get user session
		$jsession = JFactory::getSession();

		if ($members or $groups)
		{
			// Get team plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher = JDispatcher::getInstance();

			// Save team
			$content = $dispatcher->trigger( 'onProject', array(
				$project,
				$this->_option,
				$authorized,
				$this->juser->get('id'),
				$this->getNotifications('success'),
				$this->getNotifications('error'),
				'save'
			));

			if (isset($content[0]))
			{
				if (isset($content[0]['msg']) && !empty($content[0]['msg']))
				{
					$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
				}
			}

			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a
				. 'task=setup' . a . 'alias=' . $obj->alias) . '/?step=1';
			return;
		}
		// Save new info
		elseif ($save_stage or $extended)
		{
			switch ($stage)
			{
				case 0:
				default:
						$what = 'info';
						break;

				case 1: $what = $setup_complete == 3 ? 'team' : 'finalize';
						break;

				case 2: $what = 'finalize';
						break;
			}

			// Saved successfully
			if ($this->saveTask($pid, $what, $setup = 1, $tempid))
			{
				// Record setup stage and move on
				if ($save_stage > $obj->setup_stage)
				{
					$obj->saveStage($this->_identifier, $save_stage);
				}
				$stage = $save_stage;

				// Log activity
				$this->_logActivity($pid, 'setup', $what, 'save', $authorized);
				if ($this->config->get('logging', 0))
				{
					$jsession->set('projects-nolog', 1);
				}

				// Redirect to next stage (for prettier URL)
				if ($stage == 0)
				{
					$this->_redirect = JRoute::_('index.php?option='
						. $this->_option . a . 'task=setup' . a . 'alias='
						. $obj->alias) . '/?extended=1#ext';
					return;
				}
				else
				{
					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'alias=' . $obj->alias);
					return;
				}
			}
		}

		// Send to requested page
		$layouts = array('describe', 'team', 'finalize');
		if ($stage <= ($setup_complete - 1))
		{
			$layout = $layouts[$stage];
		}
		else
		{
			// Setup complete, go to project page
			$this->setup  		= $setup_complete;
			$this->_identifier 	= $pid;
			$this->_redirect 	= JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $obj->alias);
			return;
		}

		// Log activity
		if (!$jsession->get('projects-nolog'))
		{
			$logAction = !$pid ? 'start' : 'edit';
			$this->_logActivity($pid, 'setup', $layout, $logAction, $authorized);
		}

		// Allow future logging
		if ($this->config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);
		}

		// Do we need to ask about restricted data up front?
		if ($this->config->get('restricted_upfront', 0) == 1 && !$this->_identifier)
		{
			$proceed = JRequest::getInt( 'proceed', 0, 'post');
			if (!$proceed)
			{
				$layout = 'restricted';
			}
		}

		// Set layout
		$this->view->setLayout( $layout );

		// Get project params
		$this->view->params = new JParameter( $obj->params );

		// Pass variables to view
		$this->view->project 			= $obj;
		$this->view->stage 				= $stage;
		$this->view->requested_step 	= $requested_step;
		$this->view->tempid 			= $tempid;

		// Special variables for project description stage
		if ($stage == 0)
		{
			$this->view->verified = JRequest::getInt( 'verified', 0 );
			$this->view->extended = $extended;
		}

		// Collect known information about project (for info box)
		if ($stage != 0)
		{
			// Get some more variables
			$objT = new ProjectType( $this->database );
			$this->view->typetitle = $objT->getTypeTitle($obj->type);
			$this->view->uploadnow = JRequest::getInt( 'uploadnow', 0 );

			// Get project thumb
			$this->view->thumb_src = ProjectsHtml::getThumbSrc($obj->id, $obj->alias, $obj->picture, $this->config);
		}

		// Editing team
		if ($stage == 1)
		{
			// Get plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher = JDispatcher::getInstance();

			// Get project
			$project = $obj->getProject($this->_identifier, $this->juser->get('id'));

			// Get plugin output
			$tAction = JRequest::getVar( 'action', 'setup');
			$content = $dispatcher->trigger( 'onProject', array(
				$project,
				$this->_option,
				$authorized,
				$this->juser->get('id'),
				$this->getNotifications('success'),
				$this->getNotifications('error'),
				$tAction
			));

			// Get plugin output
			if (isset($content[0]))
			{
				if (isset($content[0]['msg']) && !empty($content[0]['msg']))
				{
					$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
				}
				if ($content[0]['html'])
				{
					$this->view->content = $content[0]['html'];
				}
				else
				{
					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'alias=' . $project->alias . a . 'task=setup' . a . 'step=1');
					return;
				}
			}
		}
		// Final screen
		if ($stage == 2)
		{
			$this->view->team 		= $objO->getOwnerNames($this->_identifier);

			// Load updated project
			$obj->loadProject($this->_identifier);
			$this->view->params = new JParameter( $obj->params );
		}

		// Set the pathway
		$this->_buildPathway($this->view->project, $group);

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();
		$this->_getScripts('assets/js/setup');

		// Output HTML
		$this->view->title  		= $this->title;
		$this->view->option 		= $this->_option;
		$this->view->config 		= $this->config;
		$this->view->gid 			= $this->_gid;
		$this->view->group 			= $group;
		$this->view->restricted 	= $restricted;

		// Get messages	and errors
		$this->view->msg = isset($this->_msg) ? $this->_msg : $this->getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
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
		$preview 		=  JRequest::getInt( 'preview', 0 );
		$this->active 	=  JRequest::getVar( 'active', 'feed' );
		$ajax 			=  JRequest::getInt( 'ajax', 0 );
		$action  		=  JRequest::getVar( 'action', '' );
		$sync 			=  0;

		// Stop ajax action if user got logged put
		if ($ajax && $this->juser->get('guest'))
		{
			// Project on hold
			$view 		  = new JView( array('name' => 'error') );
			$view->error  = JText::_('COM_PROJECTS_PROJECT_RELOGIN');
			$view->title  = JText::_('COM_PROJECTS_PROJECT_RELOGIN_REQUIRED');
			$view->display();
			return;
		}

		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj  	= new Project( $this->database );
		$objO 	= new ProjectOwner( $this->database );
		$objAA 	= new ProjectActivity( $this->database );

		// Is user invited to project?
		$confirmcode = JRequest::getVar( 'confirm', '' );
		$email 		 = JRequest::getVar( 'email', '' );

		// Load project
		$project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$project)
		{
			$this->setError(JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
			$this->introTask();
			return;
		}
		else
		{
			$pid 	= $project->id;
			$alias  = $project->alias;
		}

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Is this a group project?
		$group = NULL;
		if ($project->owned_by_group)
		{
			$group = \Hubzero\User\Group::getInstance( $project->owned_by_group );

			// Was owner group deleted?
			if (!$group)
			{
				$this->_buildPathway($project);
				$this->_buildTitle($project);

				// Options for project creator
				if ($project->created_by_user == $this->juser->get('id'))
				{
					$view 			= new JView( array('name'=>'changeowner') );
					$view->project 	= $project;
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
					// Project on hold
					$view 		 = new JView( array('name' => 'error') );
					$view->error = JText::_('COM_PROJECTS_PROJECT_OWNER_DELETED');
					$view->title = JText::_('COM_PROJECTS_PROJECT_OWNERSHIP_ERROR');
					$view->display();
					return;
				}
			}
		}

		// Reconcile members of project groups
		if (!$ajax)
		{
			if ($objO->reconcileGroups($pid, $project->owned_by_group))
			{
				$sync = 1;
			}
		}

		// Is project deleted?
		if ($project->state == 2)
		{
			$this->setError(JText::_('COM_PROJECTS_PROJECT_DELETED'));
			$this->introTask();
			return;
		}

		// Get publication of a provisioned project
		if ($project->provisioned == 1)
		{
			if (!$this->_publishing)
			{
				$this->setError(JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
				$this->introTask();
				return;
			}

			$objPub = new Publication($this->database);
			$pub = $objPub->getProvPublication($project->id);
		}

		// Check if project is in setup
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
		if ($project->setup_stage < $setup_complete && (!$ajax && $this->active != 'team'))
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'task=setup'
				. a . 'alias=' . $project->alias);
			return;
		}

		// Sync with system group in case of changes
		if ($sync)
		{
			$objO->sysGroup($project->alias, $this->config->get('group_prefix', 'pr-'));

			// Reload project
			$project = $obj->getProject($project->alias, $this->juser->get('id'));
		}

		// Set the pathway
		$this->_buildPathway($project, $group);

		// Set the page title
		$this->_buildTitle($project, $this->active);

		// Check authorization
		$role = $project->owner && $project->role == 0 ? 4 : $project->role;
		$authorized = $project->owner ? $role : 0;

		// Do we need to login?
		if ($this->juser->get('guest') && $action == 'login')
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PROJECT');
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
		$layout = ($authorized) && $preview && !$project->private ? 'external' : $layout;

		// Invitation view
		if ($confirmcode && (!$project->owner or !$project->confirmed))
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
						$objO->sysGroup($project->alias, $this->config->get('group_prefix', 'pr-'));

						// Go to project page
						$this->_redirect = JRoute::_('index.php?option=' . $this->_option
							. a . 'alias=' . $project->alias);
						return;
					}
				}
				else
				{
					// Different email
					$view 		  = new JView( array('name' => 'error') );
					$view->error  = JText::_('COM_PROJECTS_INVITE_DIFFERENT_EMAIL');
					$view->title  = $this->title;
					$view->display();
					return;
				}
			}
		}

		// Is this a provisioned project?
		if ($project->provisioned == 1)
		{
			if ($action == 'activate')
			{
				if ($this->juser->get('id') == $project->created_by_user && $project->setup_stage >= $setup_complete)
				{
					$layout = 'provisioned';

					// Add JS
					$this->_getScripts('assets/js/setup');
				}
				elseif ($this->juser->get('guest'))
				{
					$this->_msg = JText::_('COM_PROJECTS_LOGIN_TO_VIEW_PROJECT');
					$this->_login();
					return;
				}
				else
				{
					// Need to be project creator
					$view 			= new JView( array('name' => 'error') );
					$view->error  	= JText::_('COM_PROJECTS_ERROR_MUST_BE_PROJECT_CREATOR');
					$view->title 	= $this->title;
					$view->display();
					return;
				}
			}
			else
			{
				// Redirect to publication
				if (isset($pub) && $pub->id)
				{
					$this->_redirect = JRoute::_('index.php?option=com_publications' . a
						. 'task=submit' . a . 'pid=' . $pub->id);
					return;
				}
				else
				{
					JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
					return;
				}
			}
		}

		// Private project
		if ($project->private && $layout != 'invited')
		{
			// Login required
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT');
				$this->_login();
				return;
			}
			if (!$authorized && !$reviewer)
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Is project suspended?
		$suspended = 0;
		if ($project->state == 0 && $project->setup_stage == $setup_complete)
		{
			if (!$authorized)
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			$layout = 'suspended';

			// Check who suspended project
			$suspended = $objAA->checkActivity( $pid, JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}

		// Is project pending approval?
		if ($project->state == 5 && $project->setup_stage == $setup_complete)
		{
			if ($reviewer)
			{
				$layout = 'external';
			}
			elseif ($this->juser->get('id') != $project->created_by_user)
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			else
			{
				$layout = 'pending';
			}
		}

		$this->view->setLayout( $layout );

		$this->view->project 	= $project;
		$this->view->suspended 	= $suspended;
		$this->view->reviewer 	= $reviewer;

		// Provisioned project
		if ($project->provisioned == 1)
		{
			// Get JS & CSS
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'publications.css');

			$this->view->pub 	   = isset($pub) ? $pub : '';
			$this->view->team 	   = $objO->getOwnerNames($this->_identifier);
			$this->view->suggested = $this->_suggestAlias($pub->title);
			$this->view->verified  = $this->verifyTask(0, $this->view->suggested, $pid);
			$this->view->suggested = $this->view->verified ? $this->view->suggested : '';
		}

		// First-time visit, record join activity
		if ($project->owner && !$project->provisioned && $this->active == 'feed' && $project->confirmed && !$ajax)
		{
			if (!$project->lastvisit )
			{
				$aid = $objAA->recordActivity( $pid, $this->juser->get('id'),
					JText::_('COM_PROJECTS_ACTIVITY_JOINED_THE_PROJECT'), $this->juser->get('id'),
					'', '', 'team', 1 );
				if ($aid)
				{
					$objO->saveParam ( $pid, $this->juser->get('id'), $param = 'join_activityid', $value = $aid );
				}

				// If newly created - remove join activity of project creator
				$timecheck = JFactory::getDate(time() - (10 * 60)); // last second
				if ($project->created_by_user == $this->juser->get('id') && $timecheck <= $project->created)
				{
				    $objAA->deleteActivity($aid);
				}
			}
		}

		// Get latest log from user session
		$jsession = JFactory::getSession();

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

		// Go through plugins
		$this->view->content = '';
		if ($layout == 'internal')
		{
			$plugin = $this->active == 'feed' ? 'blog' : $this->active;
			$plugin = $this->active == 'info' ? '' : $plugin;

			// Get plugin
			JPluginHelper::importPlugin( 'projects');
			$dispatcher = JDispatcher::getInstance();

			// Get plugins with side tabs
			$this->view->tabs 	= $dispatcher->trigger( 'onProjectAreas', array( ) );
			$availPlugins 		= $dispatcher->trigger( 'onProjectAreas', array('all' => true) );

			// Get tabs
			$tabs = $this->_getTabs($this->view->tabs);

			// Get active plugins (some may not be in tabs)
			$activePlugins = $this->_getTabs($availPlugins);

			// Get plugin content
			if ($this->active != 'info')
			{
				// Do not go further if plugin is inactive or does not exist
				if (!in_array($plugin, $activePlugins))
				{
					if ($ajax)
					{
						// Plugin not active in this project
						echo '<p class="error">' . JText::_('COM_PROJECTS_ERROR_CONTENT_CANNOT_LOAD') . '</p>';
						return;
					}

					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'task=view' . a . 'alias=' . $project->alias);
					return;
				}

				// Need this to direct to correct repository
				$extraParam = '';
				$case = JRequest::getVar( 'case', '' );
				$pagename = JRequest::getVar( 'pagename', '' );

				// Are we trying to load tool source code or wiki?
				if ((($this->active == 'tools' && $action == 'source')
					|| ($this->active == 'files' && preg_match("/tools:/", $case))
					|| ($this->active == 'notes' && preg_match("/tool:/", $pagename))
					|| ($this->active == 'tools' && $action == 'wiki'))
					&& 	is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'
					. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.php')
					&& JPluginHelper::isEnabled('projects', 'tools'))
				{
					$toolname = JRequest::getVar( 'tool', '' );
					$reponame = preg_replace( "/tools:/", "", $case);

					$toolname = $toolname ? $toolname : $reponame;

					// Get project tool library
					require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
						. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.php');

					// Check that tool belongs to this project
					$tool = new ProjectTool( $this->database );
					$tool->loadTool($toolname, $project->id);

					// Direct to relevant plugin
					if (($action == 'source' || $this->active == 'files') && $tool)
					{
						$plugin = 'files';
						$extraParam = 'tools:' . $tool->name;
						$action = JRequest::getVar( 'do', '' );
						$this->active = 'tools';
					}
					if ($tool && ($action == 'wiki' || $this->active == 'notes'))
					{
						$plugin = 'notes';
						$extraParam = $tool->name;
						$this->active = 'tools';
					}
				}

				// Plugin params
				$plugin_params = array(
					$project,
					$this->_option,
					$authorized,
					$this->juser->get('id'),
					$this->getNotifications('success'),
					$this->getNotifications('error'),
					$action,
					array($plugin),
					$extraParam
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
							$this->setNotification($section['msg']['message'], $section['msg']['type']);
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
					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'task=view' . a . 'alias=' . $project->alias);
					return;
				}
			}

			$dispatcher->trigger( 'onProjectCount', array( $project, &$counts) );
			$counts['newactivity'] = $objAA->getNewActivityCount( $project->id, $this->juser->get('id'));
			$this->view->project->counts = $counts;
		}

		// Record page view
		if ($project->owner && $this->active == 'feed' && $project->confirmed)
		{
			$objO->recordView($pid, $this->juser->get('id'));
		}

		// Get project params
		$this->view->params = new JParameter( $project->params );

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
				$c = JRequest::getInt( 'c', 0 );
				if ($c) {
					$objO->saveParam ( $project->id, $this->juser->get('id'), $param = 'hide_welcome', 1 );
					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'task=view' . a . 'alias=' . $project->alias);
					return;
				}

				// Show welcome screen?
				$owner_params = new JParameter( $project->owner_params );
				$show_welcome = ((!$project->lastvisit or $project->num_visits < 3)
								&& ($owner_params->get('hide_welcome', 0) == 0))  ? 1 : 0;

				// Show welcome banner with suggestions
				$suggestions = ProjectsHelper::getSuggestions(
					$project,
					$this->_option,
					$this->juser->get('id'),
					$this->config,
					$this->view->params
				);

				if ($show_welcome)
				{
					$wview = new JView(
						array(
							'name'=>'welcome'
						)
					);
					$wview->option 		= $this->_option;
					$wview->project 	= $project;
					$wview->suggestions = $suggestions;
					$wview->creator 	= $project->created_by_user == $this->juser->get('id') ? 1 : 0;;
					$notification 		= $wview->loadTemplate();
				}
				else
				{
					// Get side modules
					$side_modules = $this->_getModules( $this->view->project, $this->_option,
						$this->juser->get('id'), $suggestions);
				}
			}

			$this->view->side_modules      = isset($side_modules) ? $side_modules : '';
			$this->view->notification      = isset($notification) ? $notification : '';
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
		$this->view->msg 		= $this->getNotifications('success');

		if ($layout == 'invited')
		{
			$this->view->confirmcode  = $confirmcode;
			$this->view->email		  = $email;
		}

		$error 	= $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}
		$this->view->display();
		return;
	}

	/**
	 * Edit project view
	 *
	 * @return     void
	 */
	public function editTask()
	{
		// Incoming
		$save  	 = JRequest::getInt( 'save', '0');
		$updated = 0;

		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj = new Project( $this->database );
		$objAA = new ProjectActivity ( $this->database );

		// Which section are we editing?
		$this->active =  JRequest::getVar( 'edit', 'info' );
		$sections = array('info', 'team');
		if ($this->config->get('edit_settings', 0))
		{
			$sections[] = 'settings';
		}
		$active = in_array( $this->active, $sections ) ? $this->active : 'info';

		// Load project
		$project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$project)
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}
		else
		{
			$pid   = $project->id;
			$alias = $project->alias;
		}

		// Check if project is in setup
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
		if ($project->setup_stage < $setup_complete)
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option
				. a . 'task=setup') . '/?id=' . $pid;
			return;
		}

		// Is project deleted?
		if ($project->state == 2)
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_DELETED') );
			return;
		}

		// Set the pathway
		$this->_buildPathway($project);

		// Set the page title
		$this->_buildTitle($project);

		// Check authorization
		$authorized = $this->_authorize($pid);

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
			$this->_login();
			return;
		}
		if ($project->role != 1) { // Only managers can edit
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Get user session
		$jsession = JFactory::getSession();

		// Log activity
		if (!$jsession->get('projects-nolog'))
		{
			$logAction = $save ? 'save' : 'edit';
			$this->_logActivity($pid, 'project', $this->active, $logAction, $authorized);
		}

		// Allow future logging
		if ($this->config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);
		}

		$this->view->setLayout( 'edit' );
		$this->view->project = $project;

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Get section-specific extra bits of information
		switch ($this->active)
		{
			case 'settings':
				if ($save)
				{
					// Save settings and access
					if ($this->saveTask($pid, 'params', 0)
						&& $this->saveTask($pid, 'privacy', 0) )
					{
						// Set message
						$this->setNotification(JText::_('COM_PROJECTS_SETTINGS_SAVED'));
					}
				}

				// Get project params
				$this->view->params = new JParameter( $this->view->project->params );

			break;

			case 'team':
				// Get team plugin
				JPluginHelper::importPlugin( 'projects', 'team' );
				$dispatcher = JDispatcher::getInstance();
				$auth = $project->role == 1 ? 1 : 0;
				$tAction = $save ? 'save' : JRequest::getVar( 'action', 'edit');
				$content = $dispatcher->trigger( 'onProject', array(
					$project,
					$this->_option,
					$auth,
					$this->juser->get('id'),
					$this->getNotifications('success'),
					$this->getNotifications('error'),
					$tAction
				));

				// Get plugin output
				if (isset($content[0]))
				{
					if (isset($content[0]['msg']) && !empty($content[0]['msg']))
					{
						$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
					}
					if ($content[0]['html'])
					{
						$this->view->content = $content[0]['html'];
					}
					else
					{
						$this->_redirect = JRoute::_('index.php?option='
							. $this->_option
							. a . 'alias=' . $project->alias . a . 'task=edit')
							. '?edit=team';
						return;
					}
				}
			break;

			case 'info':
			default:
				$objT = new ProjectType( $this->database );
				$this->view->types = $objT->getTypes();

				if ($save)
				{
					// Save info
					if ($this->saveTask($pid, 'info', 0) && $this->saveTask($pid, 1, 0))
					{
						// Set message
						$this->setNotification(JText::_('COM_PROJECTS_INFO_SAVED'));
						$updated = 1;
					}
				}
			break;
		}

		// Redirect after saving
		if ($save)
		{
			if ($updated)
			{
				$objAA->recordActivity( $pid, $this->juser->get('id'),
					JText::_('COM_PROJECTS_EDITED')
					. ' ' . JText::_('COM_PROJECTS_PROJECT_INFORMATION'), $pid,
					JText::_('COM_PROJECTS_PROJECT_INFORMATION'),
					JRoute::_('index.php?option='
					. $this->_option . a . 'alias=' . $project->alias
					. a . 'active=info'), 'project' );
			}

			if ($this->config->get('logging', 0))
			{
				$jsession->set('projects-nolog', 1);
			}
			$url = JRoute::_('index.php?option=' . $this->_option
				. a . 'task=edit' . a . 'alias='.$project->alias)
				. '?edit=' . $this->active;
			$this->_redirect = $url;
			return;
		}

		// Output HTML
		$this->view->uid 		= $this->juser->get('id');
		$this->view->active 	= $active;
		$this->view->sections 	= $sections;
		$this->view->title  	= $this->title;
		$this->view->authorized = $authorized;
		$this->view->option 	= $this->_option;
		$this->view->config 	= $this->config;
		$this->view->task 		= $this->_task;
		$this->view->publishing	= $this->_publishing;

		// Get messages	and errors
		$this->view->msg = $this->getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error)
		{
			$this->view->setError( $error );
		}
		$this->view->display();
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	/**
	 * Save project information
	 *
	 * @return     void
	 */
	public function saveTask($pid = 0, $what = 'info', $setup = 1, $tempid = 0)
	{
		$dateFormat = 'M d, Y';
		$tz = false;

		// Incoming
		$name 		= trim(JRequest::getVar( 'name', '', 'post' ));
		$title 		= trim(JRequest::getVar( 'title', '', 'post' ));
		$type 		= JRequest::getInt( 'type', 1, 'post' );
		$private 	= JRequest::getInt( 'private', 1, 'post' );
		$restricted = JRequest::getVar( 'restricted', '', 'post' );

		// Instantiate needed classes
		$obj 	= new Project( $this->database );
		$objT 	= new ProjectType( $this->database );
		$objO 	= new ProjectOwner( $this->database );

		switch ( $what )
		{
			case 'info': // save basic info

				$name = preg_replace('/ /', '', $name);
				$name = strtolower($name);

				// Check incoming data
				if (!$this->verifyTask(0) && $setup)
				{
					$this->setError( JText::_('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY') );
					return false;
				}
				elseif ($title == '' or strlen($title) < 3)
				{
					$this->setError( JText::_('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY') );
					return false;
				}

				// Load existing project
				if ($obj->loadProject($pid))
				{
					// name can only change during setup
					$obj->alias = $setup ? $name : $obj->alias;

					$obj->modified 	  = JFactory::getDate()->toSql();
					$obj->modified_by = $this->juser->get('id');
				}
				else
				{
					$obj->alias 			= $name;
					$obj->private 			= $this->config->get('privacy', 1);
					$obj->created 			= JFactory::getDate()->toSql();
					$obj->created_by_user 	= $this->juser->get('id');
					$obj->owned_by_user 	= $this->juser->get('id');
					$obj->owned_by_group 	= $this->_gid;

					// Get image name if tempid was used
					if ($tempid)
					{
						$obj->picture = $this->_getPictureName ( $tempid, $temp = 1 );
					}
				}
				$obj->title = \Hubzero\Utility\String::truncate($title, 250);
				$obj->about = trim(JRequest::getVar( 'about', '', 'post', 'none', 2 ));
				$obj->type 	= $type;

				// save advanced permissions
				if (isset($_POST['private']))
				{
					$obj->private = $private;
				}

				if ($setup && !$pid)
				{
					// Copy params from default project type
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

				// Save resctricted data choice
				if ($restricted && $setup && !$pid)
				{
					$restricted = $restricted == 'yes' ? 'yes' : 'no';

					// Save params
					$obj->saveParam(
						$obj->id,
						'restricted_data',
						htmlentities($restricted)
					);
				}

				// Send ID of newly created project back to setup screens
				$this->_identifier = $obj->id;

				if (!$pid && $obj->id)
				{
					// Save owners for new projects
					if ($this->_gid)
					{
						if (!$objO->saveOwners (
							$obj->id,
							$this->juser->get('id'),
							0,
							$this->_gid,
							0,
							1,
							1,
							'',
							$split_group_roles = 0
						))
						{
							$this->setError( JText::_('COM_PROJECTS_ERROR_SAVING_AUTHORS')
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
					$this->juser->get('id'), $this->_gid, 1, 1, 1 ))
					{
						$this->setError( JText::_('COM_PROJECTS_ERROR_SAVING_AUTHORS')
							. ': ' . $objO->getError() );
						return false;
					}

					// Transfer temp images for new projects to correct folder
					if ($obj->picture)
					{
						$this->_copyPicture ( $tempid, $obj->id );
					}
				}
			break;

			case 'team':
				// Nothing to save here
			break;

			case 'privacy':
				if (isset($_POST['private']))
				{
					if ($obj->loadProject($pid))
					{
						$obj->private = $private;

						// Save changes
						if (!$obj->store())
						{
							$this->setError( $obj->getError() );
							return false;
						}
					}
				}
			break;

			case 'params':
				$incoming   = JRequest::getVar( 'params', array() );
				if (!empty($incoming))
				{
					$old_params = $obj->params;
					foreach ($incoming as $key => $value)
					{
						$obj->saveParam($pid, $key, htmlentities($value));

						// Get updated project
						$project = $obj->getProject(
							$pid,
							$this->juser->get('id')
						);

						// If grant information changed
						if ($key == 'grant_status'
							&& $old_params != $project->params)
						{
							// Meta data for comment
							$meta = '<meta>' . JHTML::_('date', JFactory::getDate(), $dateFormat, $tz)
							. ' - ' . $this->juser->get('name') . '</meta>';

							$cbase   = $obj->admin_notes;
							$cbase  .= '<nb:sponsored>'
							. JText::_('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE')
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
								$admins = $this->_getGroupMembers($admingroup);

								// Send out email to admins
								if (!empty($admins))
								{
									ProjectsHelper::sendHUBMessage(
										$this->_option,
										$this->config,
										$project,
										$admins,
										JText::_('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
										'projects_new_project_admin',
										'admin',
										JText::_('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE'),
										'sponsored'
									);
								}
							}
						}
					}
				}
			break;

			case 'finalize':
				if ($obj->loadProject($pid))
				{
					$setup_complete 	= $this->config->get('confirm_step', 0) ? 3 : 2;
					$agree 				= JRequest::getInt( 'agree', 0, 'post' );
					$restricted 		= JRequest::getVar( 'restricted', '', 'post' );
					$agree_irb 			= JRequest::getInt( 'agree_irb', 0, 'post' );
					$agree_ferpa 		= JRequest::getInt( 'agree_ferpa', 0, 'post' );
					$state				= 1;

					if ($setup_complete == 3 )
					{
						// General restricted data question
						if ($this->config->get('restricted_data', 0) == 2)
						{
							if (!$restricted)
							{
								$this->setError( JText::_('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA'));
								return false;
							}

							// Save params
							$obj->saveParam($pid, 'restricted_data', htmlentities($restricted));
						}

						// Restricted data with specific questions
						if ($this->config->get('restricted_data', 0) == 1)
						{
							$restrictions = array(
								'hipaa_data'  => JRequest::getVar( 'hipaa', 'no', 'post' ),
								'ferpa_data'  => JRequest::getVar( 'ferpa', 'no', 'post' ),
								'export_data' => JRequest::getVar( 'export', 'no', 'post' ),
								'irb_data'    => JRequest::getVar( 'irb', 'no', 'post' )
							);

							// Save individual restrictions
							foreach ($restrictions as $key => $value)
							{
								$obj->saveParam($pid, $key, $value);
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
									$this->setError( JText::_('COM_PROJECTS_ERROR_SETUP_TERMS_HIPAA'));
									return false;
								}
							}

							// Handle restricted data choice, save params
							$obj->saveParam($pid, 'restricted_data', htmlentities($restricted));

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
									$this->setError( JText::_('COM_PROJECTS_ERROR_SETUP_TERMS_SPECIFY_DATA'));
									return false;
								}

								// Check for required confirmations
								if (($restrictions['ferpa_data'] == 'yes' && !$agree_ferpa)
									|| ($restrictions['irb_data'] == 'yes' && !$agree_irb))
								{
									$this->setError( JText::_('COM_PROJECTS_ERROR_SETUP_TERMS_RESTRICTED_DATA_AGREE_REQUIRED'));
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
								$obj->saveParam($pid, 'followup', 'yes');
							}
						}

						// Check to make sure user has agreed to terms
						if ($agree == 0)
						{
							$this->setError( JText::_('COM_PROJECTS_ERROR_SETUP_TERMS'));
							return false;
						}

						// Collect grant information
						if ($this->config->get('grantinfo', 0))
						{
							$grant_agency = JRequest::getVar( 'grant_agency', '' );
							$grant_title = JRequest::getVar( 'grant_title', '' );
							$grant_PI = JRequest::getVar( 'grant_PI', '' );
							$grant_budget = JRequest::getVar( 'grant_budget', '' );
							$obj->saveParam($pid, 'grant_budget', htmlentities($grant_budget));
							$obj->saveParam($pid, 'grant_agency', htmlentities($grant_agency));
							$obj->saveParam($pid, 'grant_title', htmlentities($grant_title));
							$obj->saveParam($pid, 'grant_PI', htmlentities($grant_PI));
							$obj->saveParam($pid, 'grant_status', 0);
						}
					}

					// Is the project active already?
					$active = $obj->state == 1 ? 1 : 0;

					// Sync with system group
					$objO->sysGroup($obj->alias, $this->config->get('group_prefix', 'pr-'));

					// Initialize Git repo
					$path = ProjectsHelper::getProjectPath($obj->alias,
									$this->config->get('webpath'), $this->config->get('offroot', 0));
					if (!is_dir( $path ))
					{
						// Create path
						jimport('joomla.filesystem.folder');
						if (!JFolder::create( $path ))
						{
							$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
						}
					}
					include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects'
						. DS . 'helpers' . DS . 'githelper.php' );
					$this->_git = new ProjectsGitHelper(
						$this->config->get('gitpath', '/opt/local/bin/git'),
						0,
						$this->config->get('offroot', 0) ? '' : JPATH_ROOT
					);
					$this->_git->iniGit($path);

					// Activate project
					if (!$active)
					{
						$obj->state = $state;
						$obj->provisioned = 0; // remove provisioned flag if any
						$obj->created = JFactory::getDate()->toSql();

						// Save changes
						if (!$obj->store())
						{
							$this->setError( $obj->getError() );
							return false;
						}

						// Email administrators about a new project
						if ($this->config->get('messaging') == 1)
						{
							// Get updated project
							$project = $obj->getProject($pid, $this->juser->get('id'));

							$admingroup 	= $this->config->get('admingroup', '');
							$sdata_group 	= $this->config->get('sdata_group', '');
							$ginfo_group 	= $this->config->get('ginfo_group', '');
							$project_admins = $this->_getGroupMembers($admingroup);
							$ginfo_admins 	= $this->_getGroupMembers($ginfo_group);
							$sdata_admins 	= $this->_getGroupMembers($sdata_group);

							$admins = array_merge($project_admins, $ginfo_admins, $sdata_admins);
							$admins = array_unique($admins);

							// Send out email to admins
							if (!empty($admins))
							{
								ProjectsHelper::sendHUBMessage(
									$this->_option,
									$this->config,
									$project,
									$admins,
									JText::_('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
									'projects_new_project_admin',
									'new'
								);
							}
						}

						// If project gets activated - send notifications
						if ($state == 1)
						{
							// Record activity
							$objAA = new ProjectActivity ( $this->database );
							$objAA->recordActivity( $pid, $this->juser->get('id'),
								JText::_('COM_PROJECTS_PROJECT_STARTED',
								$pid, '', '', 'project'));

							// Send out emails
							$this->_notifyTeam($pid);
						}
					}
				}
			break;
		}

		return true;
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
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate needed classes
		$obj  = new Project( $this->database );
		$objO = new ProjectOwner( $this->database );

		// Get Project
		$project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$obj->loadProject($this->_identifier) or !$project)
		{
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Must be project creator
		if ($project->created_by_user != $this->juser->get('id'))
		{
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Must be a provisioned project to be activated
		if ($project->provisioned != 1)
		{
			// Redirect to project page
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $project->alias);
			return;
		}

		// Redirect to setup if activation not complete
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
		if ($project->setup_stage < $setup_complete)
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'task=setup'
				. a . 'alias=' . $project->alias);
			return;
		}

		// Get publication of a provisioned project
		$objPub = new Publication($this->database);
		$pub = $objPub->getProvPublication($project->id);

		// Incoming
		$name  = trim(JRequest::getVar( 'new-alias', '', 'post' ));
		$title = trim(JRequest::getVar( 'title', '', 'post' ));
		$pubid = trim(JRequest::getInt( 'pubid', 0, 'post' ));

		$name = preg_replace('/ /', '', $name);
		$name = strtolower($name);

		// Check incoming data
		if (!$this->verifyTask(0, $name, $project->id))
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NAME_INVALID_OR_EMPTY') );
		}
		elseif ($title == '' or strlen($title) < 3)
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_TITLE_SHORT_OR_EMPTY') );
		}

		// Set the pathway
		$this->_buildPathway($project);

		// Set the page title
		$this->_buildTitle($project);

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Return to page in case of error
		if ($this->getError())
		{
			$this->view->setLayout( 'provisioned' );
			$this->view->project 		= $project;
			$this->view->project->title = $title;

			// Output HTML
			$this->view->pub 		 	= isset($pub) ? $pub : '';
			$this->view->team 	 		= $objO->getOwnerNames($this->_identifier);
			$this->view->suggested 		= $name;
			$this->view->verified  		= $this->verifyTask(0, $this->view->suggested, $project->id);
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
		$path 	 = $helper->buildDevPath($project->alias, '', '', '');
		$newpath = $helper->buildDevPath($name, '', '', '');

		// Rename project parent directory
		if (is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::copy($path, $newpath, '', true))
			{
				$this->setError( JText::_('COM_PROJECTS_FAILED_TO_COPY_FILES') );
			}
			else
			{
				// Delete original repo
				JFolder::delete($path);
			}
		}

		// Save new alias & title
		if (!$this->getError())
		{
			$obj->title 		= \Hubzero\Utility\String::truncate($title, 250);
			$obj->alias 		= $name;
			$obj->state 		= 0;
			$obj->setup_stage 	= $setup_complete - 1;
			$obj->modified		= JFactory::getDate()->toSql();
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
		$this->_identifier 	= $obj->id;
		$this->_task 		= 'setup';
		$this->setupTask();
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
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Instantiate a project and related classes
		$obj = new Project( $this->database );
		$objAA = new ProjectActivity ( $this->database );

		// Load project
		if (!$obj->loadProject($this->_identifier))
		{
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Is project deleted?
		if ($obj->state == 2)
		{
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_DELETED') );
			return;
		}

		// Already suspended
		if ($this->_task == 'suspend' && $obj->state == 0)
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $obj->alias);
			return;
		}

		// Suspended by admin: manager cannot activate
		if ($this->_task == 'reinstate')
		{
			$suspended = $objAA->checkActivity( $obj->id, JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
			if ($suspended == 1)
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Login required
		if ($this->juser->get('guest'))
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');
			$this->_login();
			return;
		}

		// Fix ownership?
		if ($this->_task == 'fixownership')
		{
			$keep 	 = JRequest::getInt( 'keep', 0 );
			$groupid = $obj->owned_by_group;
			if ($obj->created_by_user != $this->juser->get('id'))
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			if (!$groupid)
			{
				// Nothing to fix
				$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $obj->alias);
				return;
			}
			$obj->owned_by_group = 0;

			// Make sure creator is still in team
			$objO = new ProjectOwner( $this->database );
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
		$authorized = $this->_authorize($obj->id);
		if ($authorized != 1)
		{
			// Only managers can change project state
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Update project
		$obj->modified = JFactory::getDate()->toSql();
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

		// Add activity
		if ($this->_task != 'fixownership')
		{
			$what = ($this->_task == 'suspend')
				? JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED')
				: JText::_('COM_PROJECTS_ACTIVITY_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$what = JText::_('COM_PROJECTS_ACTIVITY_PROJECT_DELETED');
			}
			$objAA->recordActivity( $obj->id, $this->juser->get('id'), $what );
		}

		// Send to project page
		if ($this->_task != 'fixownership')
		{
			$this->_msg = $this->_task == 'suspend'
				? JText::_('COM_PROJECTS_PROJECT_SUSPENDED')
				: JText::_('COM_PROJECTS_PROJECT_REINSTATED');

			if ($this->_task == 'delete')
			{
				$this->setError(JText::_('COM_PROJECTS_PROJECT_DELETED'));

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
		$error  = JRequest::getVar( 'error', '', 'get' );
		$code   = JRequest::getVar( 'code', '', 'get' );

		$state  = JRequest::getVar( 'state', '', 'get' );
		$json	=  base64_decode($state);
		$json 	=  json_decode($json);

		$service = $json->service ? $json->service : 'google';
		$this->_identifier = $json->alias;

		// Cannot proceed without project id/alias
		if (!$this->_identifier)
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
			return;
		}

		// Load project
		$obj = new Project( $this->database );
		if (!$obj->loadProject($this->_identifier) )
		{
			$this->_buildPathway();
			$this->_buildTitle();
			JError::raiseError( 404, JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
			return;
		}

		// Successful authorization grant, fetch the access token
		if ($code)
		{
			$return	= JRoute::_('index.php?option=' . $this->_option . a . 'alias='
				. $this->_identifier . a . 'active=files') . '?action=connect';
			$return .= '&service=' . $service;
			$return .= '&code=' . $code;
		}
		elseif (isset($json->return))
		{
			$return = $json->return . '&service=' . $service;
		}

		// Catch errors
		if ($error)
		{
			$error =  $error == 'access_denied'
				? JText::_('Sorry, we cannot connect you to external file service without your permission')
				: JText::_('Sorry, we cannot connect you to external file service at this time');
			$this->setNotification($error, 'error');
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
		$dateFormat = 'M d, Y';
		$tz = false;

		// Incoming
		$reviewer 	= JRequest::getVar( 'reviewer', '' );
		$action  	= JRequest::getVar( 'action', '' );
		$comment  	= JRequest::getVar( 'comment', '' );
		$approve  	= JRequest::getInt( 'approve', 0 );
		$filterby  	= JRequest::getVar( 'filterby', 'pending' );
		$notify 	= JRequest::getVar( 'notify', 0, 'post' );

		// Instantiate a project and related classes
		$obj 		= new Project( $this->database );
		$objAA 		= new ProjectActivity ( $this->database );

		// Check authorization
		$authorized = ProjectsHelper::checkReviewerAuth($reviewer, $this->config);
		if (!$authorized)
		{
			$this->setError( JText::_('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS') );
			return;
		}

		// We need to have a project
		if (!$this->_identifier)
		{
			$this->setError( JText::_('COM_PROJECTS_PROJECT_NOT_FOUND') );
		}

		// Load project
		if (!$obj->loadProject($this->_identifier))
		{
			$this->setError( JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD') );
		}

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);

		// Add the CSS to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getProjectScripts();

		// Get project params
		$params = new JParameter( $obj->params );

		// Log activity
		$this->_logActivity($obj->id, 'reviewer', $reviewer, $action, $authorized);

		if ($action == 'save' && !$this->getError() && $obj->id)
		{
			$cbase = $obj->admin_notes;

			// Meta data for comment
			$now = JFactory::getDate()->toSql();
			$actor = $this->juser->get('name');
			$meta = '<meta>' . JHTML::_('date', $now, $dateFormat, $tz) . ' - ' . $actor . '</meta>';

			// Save approval
			if ($reviewer == 'sensitive')
			{
				$approve = $approve == 1 && $obj->state == 5 ? 1 : 0; // can only approve pending project
				$obj->state = $approve ? 1 : $obj->state;
			}
			elseif ($reviewer == 'sponsored')
			{
				$grant_agency 		= JRequest::getVar( 'grant_agency', '' );
				$grant_title 		= JRequest::getVar( 'grant_title', '' );
				$grant_PI 			= JRequest::getVar( 'grant_PI', '' );
				$grant_budget 		= JRequest::getVar( 'grant_budget', '' );
				$grant_approval 	= JRequest::getVar( 'grant_approval', '' );
				$rejected 			= JRequest::getVar( 'rejected', 0 );

				// New approval
				if (trim($params->get('grant_approval')) == '' && trim($grant_approval) != ''
				&& $params->get('grant_status') != 1 && $rejected != 1)
				{
					// Increase
					$approve = 1;

					// Bump up quota
					$premiumQuota = ProjectsHtml::convertSize(
						floatval($this->config->get('premiumQuota', '30')), 'GB', 'b');
					$obj->saveParam($obj->id, 'quota', htmlentities($premiumQuota));

					// Bump up publication quota
					$premiumPubQuota = ProjectsHtml::convertSize(
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
					$cbase  .= '<nb:' . $reviewer . '>' . JText::_('COM_PROJECTS_PROJECT_APPROVED_HIPAA');
					$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
					$cbase  .= $meta . '</nb:' . $reviewer . '>';
				}
				if ($reviewer == 'sponsored')
				{
					if ($approve == 1)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS') . ' '
						. ucfirst(JText::_('COM_PROJECTS_APPROVAL_CODE')) . ': ' . $grant_approval;
						$cbase  .= (trim($comment) != '') ? '. ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
					elseif ($approve == 2)
					{
						$cbase  .= '<nb:' . $reviewer . '>' . JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS');
						$cbase  .= (trim($comment) != '') ? ' ' . $comment : '';
						$cbase  .= $meta . '</nb:' . $reviewer . '>';
					}
					$notify = 1;
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
				$project = $obj->getProject($obj->id, $this->juser->get('id'));

				$admingroup = $reviewer == 'sensitive'
					? $this->config->get('sdata_group', '')
					: $this->config->get('ginfo_group', '');

				if (\Hubzero\User\Group::getInstance($admingroup))
				{
					$admins = $this->_getGroupMembers($admingroup);
					$admincomment = $comment
						? $actor . ' ' . JText::_('COM_PROJECTS_SAID') . ': ' . $comment
						: '';

					// Send out email to admins
					if (!empty($admins))
					{
						ProjectsHelper::sendHUBMessage(
							$this->_option,
							$this->config,
							$project,
							$admins,
							JText::_('COM_PROJECTS_EMAIL_ADMIN_REVIEWER_NOTIFICATION'),
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
				$this->setNotification($this->getError(), 'error');
			}
			else
			{
				if ($approve)
				{
					if ($reviewer == 'sensitive')
					{
						$this->setNotification(JText::_('COM_PROJECTS_PROJECT_APPROVED_HIPAA_MSG') );

						// Send out emails to team members
						$this->_notifyTeam($obj->id);
					}
					if ($reviewer == 'sponsored')
					{
						$notification =  $approve == 2
								? JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS_MSG')
								: JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS_MSG');
						$this->setNotification($notification);
					}
				}
				elseif ($comment)
				{
					$this->setNotification(JText::_('COM_PROJECTS_REVIEWER_COMMENT_POSTED') );
				}

				// Add to project activity feed
				if ($notify)
				{
					$activity = '';
					if ($approve && $reviewer == 'sponsored')
					{
						$activity = $approve == 2
								? JText::_('COM_PROJECTS_PROJECT_REJECTED_SPS_ACTIVITY')
								: JText::_('COM_PROJECTS_PROJECT_APPROVED_SPS_ACTIVITY');
					}
					elseif ($comment)
					{
						$activity = JText::_('COM_PROJECTS_PROJECT_REVIEWER_COMMENTED');
					}

					if ($activity)
					{
						$objAA = new ProjectActivity( $this->database );
						$aid = $objAA->recordActivity( $obj->id, $this->juser->get('id'),
							$activity, $obj->id, '', '', 'admin', 0, 1, 1 );

						// Append comment to activity
						if ($comment && $aid)
						{
							$objC = new ProjectComment( $this->database );
							$cid = $objC->addComment( $aid, 'activity', $comment,
							$this->juser->get('id'), $aid, 1 );

							if ($cid)
							{
								$objAA = new ProjectActivity( $this->database );
								$caid = $objAA->recordActivity( $obj->id, $this->juser->get('id'),
									JText::_('COM_PROJECTS_COMMENTED') . ' '
									. JText::_('COM_PROJECTS_ON') . ' '
									.  JText::_('COM_PROJECTS_AN_ACTIVITY'),
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
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a
				. 'task=browse') . '?reviewer=' . $reviewer . '&filterby=' . $filterby ;
			return;
		}
		else
		{
			// Instantiate a new view
			$this->view->setLayout( 'review' );

			// Output HTML
			$this->view->reviewer 	= $reviewer;
			$this->view->ajax 		= JRequest::getInt( 'ajax', 0 );
			$this->view->title 		= $this->title;
			$this->view->option 	= $this->_option;
			$this->view->project	= $obj;
			$this->view->params		= $params;
			$this->view->thumb_src 	= ProjectsHtml::getThumbSrc($obj->id, $obj->alias, $obj->picture, $this->config);
			$this->view->config 	= $this->config;
			$this->view->database 	= $this->database;
			$this->view->action		= $action;
			$this->view->filterby	= $filterby;
			$this->view->uid 		= $this->juser->get('id');
			$this->view->msg 		= isset($this->_msg) && $this->_msg
									? $this->_msg : $this->getNotifications('success');
			if ($this->getError())
			{
				$this->view->setError( $this->getError() );
			}
			$this->view->display();
		}
	}

	/**
	 * Update activity counts (AJAX)
	 *
	 * @param  int $pid
	 * @param  string $what
	 * @param  int $authorized
	 * @param  int $ajax
	 * @param  int $uid
	 * @return void
	 */
	public function showcountTask ( $pid = 0, $what = '', $authorized = 0, $ajax = 1, $uid = 0 )
	{
		$pid  	= $pid ? $pid : JRequest::getInt( 'pid', 0 );
		$what 	= $what ? $what : JRequest::getVar( 'what', '' );
		$uid 	= $uid ? $uid : $this->juser->get('id');
		$count 	= 0;

		// Check id
		if (!$pid)
		{
			return false;
		}

		// Check authorization
		if (!$authorized)
		{
			$authorized = $this->_authorize($pid);
		}
		if (!$authorized)
		{
			return false;
		}

		$db = JFactory::getDBO();
		$project = new Project( $db );
		if (!$project->loadProject( $pid ))
		{
			return false;
		}
		// Get plugin
		JPluginHelper::importPlugin( 'projects', $what);
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onProjectCount', array( $project, &$counts) );

		if (isset($counts[$what]))
		{
			$count = $counts[$what];
		}

		if ($ajax)
		{
			echo $count;
			return;
		}
		else
		{
			return $count;
		}
	}

	/**
	 * Verify project/tool name (AJAX)
	 *
	 * @param  int $ajax
	 * @param  string $name
	 * @param  int $pid
	 * @return     void
	 */
	public function verifyTask( $ajax = 0, $name = '', $pid = 0 )
	{
		// Incoming
		$name 	= $name ? $name : trim(JRequest::getVar( 'name', '' ));
		$pid 	= $pid ? $pid : JRequest::getInt( 'pid', 0 );
		$ajax 	= $ajax == 1 ? 1 : JRequest::getInt( 'ajax', 0 );
		$tool 	= JRequest::getInt( 'tool', 0 );
		$class 	= 'verify_failed';

		// Set name length
		$min_length = $tool ? 3 : $this->config->get('min_name_length', 3);
		$max_length = $tool ? 20 : $this->config->get('max_name_length', 25);

		// Array of reserved names (task names and default dirs)
		$reserved = array();
		$names = $this->config->get('reserved_names', '');
		$tasks = array(	'start', 'setup', 'browse',
			'intro', 'features', 'deleteimg',
			'reports', 'stats', 'view', 'edit',
			'suspend', 'reinstate', 'fixownership',
			'delete', 'intro', 'activate', 'process',
			'upload', 'img', 'verify', 'autocomplete',
			'showcount', 'wikipreview', 'auth', 'public');

		if ($names)
		{
			$reserved = explode(',', $names);

			// More reserved names
			$reserved[] = 'admin';
			$reserved[] = 'usage';
		}

		if ($name)
		{
			$name = preg_replace('/ /', '', $name);
			$name = strtolower($name);
		}

		// Cannot be empty
		if (!$name)
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_EMPTY');
		}
		// Check for length
		elseif (strlen($name) < intval($min_length))
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_TOO_SHORT');
		}
		elseif (strlen($name) > intval($max_length))
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_TOO_LONG');
		}
		// Check for illegal characters
		elseif (preg_match('/[^a-z0-9]/', $name))
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_INVALID');
		}
		// Check for all numeric (not allowed)
		elseif (is_numeric($name))
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC');
		}

		// Verify tool name uniqueness
		elseif ($tool)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
				. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.php');

			$objA = new ProjectTool( $this->database );
			if ($objA->checkUniqueName($name, $tool))
			{
				if (!$ajax) { return false; }
				$result = JText::_('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE');
			}
			else
			{
				if (!$ajax) { return true; }
				$class = 'verify_passed';
				$result = JText::_('COM_PROJECTS_VERIFY_PASSED');
			}
		}
		// Verify name uniqueness
		else
		{
			$obj = new Project( $this->database );
			if ($obj->checkUniqueName( $name, $pid ) && !in_array( $name, $reserved ) && !in_array( $name, $tasks ))
			{
				if (!$ajax) { return true; }
				$class = 'verify_passed';
				$result = JText::_('COM_PROJECTS_VERIFY_PASSED');
			}
			else
			{
				if (!$ajax) { return false; }
				$result = JText::_('COM_PROJECTS_ERROR_NAME_NOT_UNIQUE');
			}
		}

		if (!$ajax) { return true; }
		echo $class . '::' . $result . '::' . $name;
	}

	//----------------------------------------------------------
	// Private Functions
	//----------------------------------------------------------

	/**
	 * Suggest alias name from title
	 *
	 * @param  string $title
	 * @return     void
	 */
	protected function _suggestAlias($title = '')
	{
		if ($title)
		{
			$name = preg_replace('/ /', '', $title);
			$name = strtolower($name);
			$name = preg_replace('/[^a-z0-9]/', '', $name);
			$name = substr($name, 0, 30);
			return $name;
		}
	}

	/**
	 * Copy temp image file
	 *
	 * @param  string $tempid
	 * @param  int $pid
	 * @return     void
	 */
	protected function _copyPicture ( $tempid, $pid )
	{
		$prefix = JPATH_ROOT;

		// Get web directory
		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);

		$from_dir =  \Hubzero\Utility\String::pad( $temid );

		// Get alias
		if (intval($pid))
		{
			$obj = new Project( $this->database );
			$to_dir = $obj->getAlias( $pid );
		}
		else
		{
			$to_dir = $pid;
		}

		$from_path 	= $prefix . $webdir . DS . 'temp' . DS . $from_dir . DS . 'images';
		$to_path 	= $prefix . $webdir . DS . $to_dir . DS . 'images';

		jimport('joomla.filesystem.folder');

		// Make sure the path exist
		if (!is_dir( $to_path ))
		{
			if (!JFolder::create( $to_path ))
			{
				return false;
			}
		}
		// do we have files to transfer?
		$files = JFolder::files($from_path, '.', false, true, array());
		if (!empty($files))
		{
			if (!JFolder::copy($from_path, $to_path, '', true))
			{
				return false;
			}
			else
			{
				// Delete temp images
				JFolder::delete($from_path);
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * Get picture name
	 *
	 * @param  int $id
	 * @param  int $temp
	 * @return     void
	 */
	protected function _getPictureName ( $id, $temp = 1 )
	{
		$prefix = JPATH_ROOT;

		// Build the file path
		if (intval($id))
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );
		}
		else
		{
			$dir = $id;
		}

		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path   = $prefix . $webdir;
		$path  .= $temp ? DS . 'temp' : '';
		$path  .= DS . $dir . DS . 'images';

		// Collectors
		$images = array();
		$tns = array();

		// Looks for images in direcory
		$d = @dir($path);
		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($prefix . $path . DS . $img_file) && substr($entry, 0, 1) != '.'
					&& strtolower($entry) !== 'index.html')
				{
					if (preg_match( "/(bmp|gif|jpg|png|swf)/", $img_file ) && $img_file != 'thumb.png')
					{
						$images[] = $img_file;
					}
				}
			}

			$d->close();
			if (!empty($images))
			{
				return $images[0];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway( $project = null, $group = null )
	{
		$pathway = JFactory::getApplication()->getPathway();
		$group_tasks = array('start', 'setup', 'view');

		// Add group

		if (is_object($group) && in_array($this->_task, $group_tasks) )
		{
			$pathway->setPathway(array());
			$pathway->addItem(
				JText::_('COM_PROJECTS_GROUPS_COMPONENT'),
				JRoute::_('index.php?option=com_groups')
			);
			$pathway->addItem(
				\Hubzero\Utility\String::truncate($group->get('description'), 50),
				JRoute::_('index.php?option=com_groups' . a . 'cn=' . $group->cn)
			);
			$pathway->addItem(
				JText::_('COM_PROJECTS_PROJECTS'),
				JRoute::_('index.php?option=com_groups' . a . 'cn=' . $group->cn . a . 'active=projects')
			);
		}
		elseif (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_('COMPONENT_LONG_NAME'),
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		if (is_object($project) && $project->alias)
		{
			if ($project->provisioned == 1)
			{
				$pathway->addItem(
					stripslashes(JText::_('COM_PROJECTS_PROVISIONED_PROJECT')),
					JRoute::_('index.php?option=' . $this->_option . a . 'alias='
					.$project->alias) . '/?action=activate'
				);
			}
			else
			{
				$pathway->addItem(
					stripslashes($project->title),
					JRoute::_('index.php?option=' . $this->_option . a . 'alias=' . $project->alias)
				);
			}
		}
		if ($this->_task)
		{
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
									ucfirst(JText::_('COM_PROJECTS_TAB_'.strtoupper($this->active))),
									JRoute::_('index.php?option=' . $this->_option . a . 'alias='
									. $project->alias . a . 'active=' . $this->active)
								);
							break;
						}
					}
				break;

				case 'setup':
					if (!is_object($project) || !$project->id)
					{
						$pathway->addItem(
							JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
							'index.php?option=' . $this->_option . '&task=' . $this->_task
						);
						break;
					}
					break;

				case 'browse':
				case 'process':
					$reviewer 	= JRequest::getVar( 'reviewer', '' );
					if ($reviewer == 'sponsored' || $reviewer == 'sensitive')
					{
						$title = $reviewer == 'sponsored'
											? JText::_('COM_PROJECTS_REVIEWER_SPS')
											: JText::_('COM_PROJECTS_REVIEWER_HIPAA');

						$pathway->addItem(
							$title,
							JRoute::_('index.php?option=' . $this->_option . a
							. 'task=browse') . '?reviewer=' . $reviewer
						);
					}
					else
					{
						$pathway->addItem(
							JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
							'index.php?option=' . $this->_option . '&task=' . $this->_task
						);
					}
				break;

				case 'intro':
				case 'activate':
				// add nothing else
					break;
				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);
					break;
			}
		}
	}

	/**
	 * Build the title for this component
	 *
	 * @param  object $project
	 * @param  string $active
	 * @return void
	 */
	protected function _buildTitle( $project = null, $active = null )
	{
		$this->title  = JText::_('COMPONENT_LONG_NAME');
		if (is_object($project) && $project->alias)
		{
			if ($project->provisioned == 1)
			{
				$this->title .= ': ' . JText::_('COM_PROJECTS_PROVISIONED_PROJECT');
			}
			else
			{
				$this->title .= ': '.stripslashes($project->title);
				if ($active && !$this->juser->get('guest'))
				{
					$this->title .= ' :: ' . ucfirst(JText::_('COM_PROJECTS_TAB_' . strtoupper($active)));
				}
			}
		}
		elseif ($this->_task == 'browse')
		{
			$reviewer 	 = JRequest::getVar( 'reviewer', '' );
			if ($reviewer == 'sponsored' || $reviewer == 'sensitive')
			{
				$this->title = $reviewer == 'sponsored'
							 ? JText::_('COM_PROJECTS_REVIEWER_SPS')
							 : JText::_('COM_PROJECTS_REVIEWER_HIPAA');
			}
			else
			{
				$this->title .= ($this->_task)
					? ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)) : '';
			}
		}
		elseif ($this->_task != 'intro' && $this->_task != 'view' && $this->_task != 'process')
		{
			$this->title .= ($this->_task)
				? ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)) : '';
		}

		$document = JFactory::getDocument();
		$document->setTitle( $this->title );
	}

	/**
	 * Get group members
	 *
	 * @param  string $admingroup
	 * @return void
	 */
	protected function _getGroupMembers($admingroup)
	{
		$admins = array();
		if ($admingroup)
		{
			$group = \Hubzero\User\Group::getInstance($admingroup);
			if ($group)
			{
				$gidNumber = $group->get('gidNumber');

				if ($gidNumber)
				{
					$members 	= $group->get('members');
					$managers 	= $group->get('managers');
					$admins 	= array_merge($members, $managers);
					$admins 	= array_unique($admins);
				}
			}
		}

		return $admins;
	}

	/**
	 * Authorize users
	 *
	 * @param  int $projectid
	 * @param  int $check_site_admin
	 * @return void
	 */
	protected function _authorize( $projectid = 0, $check_site_admin = 0 )
	{
		// Check login
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check whether user belongs to the project
		if ($projectid != 0)
		{
			$pOwner = new ProjectOwner( $this->database );
			if ($result = $pOwner->isOwner($this->juser->get('id'), $projectid))
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

		return false;
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
	protected function _logActivity ($pid = 0, $section = 'general', $layout = '', $action = '', $owner = 0)
	{
		// Is logging enabled?
		$enabled = $this->config->get('logging', 0);
		if (!$enabled)
		{
			return false;
		}

		// Is this an ajax call?
		$ajax = JRequest::getInt( 'ajax', 0 );
		if ($ajax && $enabled == 1)
		{
			return false;
		}

		$juri = JURI::getInstance();

		// Log activity
		$objLog  				= new ProjectLog( $this->database );
		$objLog->projectid 		= $pid;
		$objLog->userid 		= $this->juser->get('id');
		$objLog->owner 			= intval($owner);
		$objLog->ip 			= JRequest::ip();
		$objLog->section 		= $section;
		$objLog->layout 		= $layout ? $layout : $this->_task;
		$objLog->action 		= $action ? $action : 'view';
		$objLog->time 			= date('Y-m-d H:i:s');
		$objLog->request_uri 	= JRequest::getVar('REQUEST_URI', $juri->base(), 'server');
		$objLog->ajax 			= $ajax;
		$objLog->store();
	}

	/**
	 * Get side modules for project page (internal)
	 *
	 * @param  object $project
	 * @param  string $option
	 * @param  int $uid
	 * @param  array $suggestions
	 * @return void
	 */
	protected function _getModules( $project = '', $option = '', $uid = 0, $suggestions = array())
	{
		$limit = $this->config->get('sidebox_limit', 3);
		$modules = '';

		// Show side module with suggestions
		if (count($suggestions) > 1 && $project->num_visits < 10)
		{
			$view = new JView(
				array(
					'name' 	 => 'modules',
					'layout' => 'suggestions'
				)
			);
			$view->option 		= $option;
			$view->suggestions 	= $suggestions;
			$view->project 		= $project;
			$modules 		   .= $view->loadTemplate();
		}

		// Get todo's
		$objTD = new ProjectTodo( $this->database );
		$todos = $objTD->getTodos ($project->id, $filters = array(
			'sortby' => 'due DESC, p.duedate ASC', 'limit' => $limit
		  )
		);

		// To-do side module
		$view = new JView(
			array(
				'name' => 'modules',
				'layout' => 'todo'
			)
		);
		$view->option 	= $option;
		$view->items 	= $todos;
		$view->project 	= $project;
		$modules 	   .= $view->loadTemplate();

		// Get publications
		if ($this->_publishing)
		{
			$objP = new Publication( $this->database );
			$pubs = $objP->getRecords($filters = array(
				'sortby' => 'random', 'limit' => $limit, 'project' => $project->id,
				'ignore_access' => 1, 'dev' => 1
			));

			if (count($pubs) > 0)
			{
				// Get language file
				$lang = JFactory::getLanguage();
				$lang->load('plg_projects_publications');
			}

			// Publications side module
			$view = new JView(
				array(
					'name' => 'modules',
					'layout' => 'publications'
				)
			);
			$view->option 	= $option;
			$view->items 	= $pubs;
			$view->project 	= $project;
			$modules 	   .= $view->loadTemplate();
		}

		// Get notes
		$projectsHelper = new ProjectsHelper( $this->database );
		$masterscope 	= 'projects' . DS . $project->alias . DS . 'notes';
		$group_prefix 	= $this->config->get('group_prefix', 'pr-');
		$group 			= $group_prefix . $project->alias;
		$notes 			= $projectsHelper->getNotes($group, $masterscope, $limit, 'RAND()');

		// To-do side module
		$view = new JView(
			array(
				'name' 	 => 'modules',
				'layout' => 'notes'
			)
		);
		$view->option 	= $option;
		$view->items 	= $notes;
		$view->project 	= $project;
		$modules 	   .= $view->loadTemplate();

		return $modules;
	}

	/**
	 * Notify project team
	 *
	 * @param  int $pid
	 * @param  string $action
	 * @param  int $managers_only
	 * @return void
	 */
	protected function _notifyTeam($pid = '', $managers_only = 0)
	{
		// Is messaging turned on?
		if ($this->config->get('messaging') != 1)
		{
			return false;
		}

		// Check required
		if (!$pid)
		{
			return false;
		}

		$message = array();

		// Get project
		$obj 		= new Project( $this->database );
		$objO 		= new ProjectOwner( $this->database );
		$project 	= $obj->getProject($pid, $this->juser->get('id'));

		// Set up email config
		$jconfig 		= JFactory::getConfig();
		$from 			= array();
		$from['name']  	= $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_PROJECTS');
		$from['email'] 	= $jconfig->getValue('config.mailfrom');

		// Get team/managers
		$filters = array( 'select'=> 'o.userid, o.invited_code, o.invited_email, o.role ', 'sortby' => 'status' );
		if ($managers_only)
		{
			$filters['role'] = 1;
		}
		$team = $objO->getOwners( $pid, $filters );

		// Must have addressees
		if (empty($team))
		{
			return false;
		}

		$subject_active  = JText::_('COM_PROJECTS_EMAIL_SUBJECT_ADDED') . ' ' . $project->alias;
		$subject_pending = JText::_('COM_PROJECTS_EMAIL_SUBJECT_INVITE') . ' ' . $project->alias;

		// Message body
		$eview 					= new JView( array('name'=>'emails', 'layout'=> 'invite_plain' ) );
		$eview->option 			= $this->_option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $project;
		$eview->goto 			= 'alias=' . $project->alias;
		$eview->user 			= $this->juser->get('id');
		$eview->delimiter  		= '';

		// Get profile of author group
		if ($project->owned_by_group)
		{
			$eview->nativegroup = \Hubzero\User\Group::getInstance( $project->owned_by_group );
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
				if ($member->userid == $project->created_by_user)
				{
					$subject_active  = JText::_('COM_PROJECTS_EMAIL_SUBJECT_CREATOR_CREATED')
					. ' ' . $project->alias . '!';
				}

				// Send HUB message
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher = JDispatcher::getInstance();
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

				ProjectsHtml::email($member->invited_email, $jconfig->getValue('config.sitename')
					. ': ' . $subject_pending, $message, $from);
			}
		}
	}

	/**
	 * Get tabs
	 *
	 * @return    array
	 */
	protected function _getTabs( &$plugins )
	{
		// Make sure we have name and title
		$tabs = array();
		for ($i = 0, $n = count($plugins); $i <= $n; $i++)
		{
			if (empty($plugins[$i]) || !isset($plugins[$i]['name']))
			{
				unset($plugins[$i]);
			}
			else
			{
				$tabs[] = $plugins[$i]['name'];
			}
		}

		return $tabs;
	}
}