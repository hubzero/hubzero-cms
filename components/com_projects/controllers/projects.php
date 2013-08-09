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

ximport('Hubzero_Controller');
ximport('Hubzero_Environment');

/**
 * Primary component controller (extends Hubzero_Controller)
 */
class ProjectsControllerProjects extends Hubzero_Controller
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
		$this->_longname = JText::_('COMPONENT_LONG_NAME');

		// Load the component config
		$config 		=& JComponentHelper::getParams( $this->_option );
		$this->_config 	= $config;
				
		// Publishing enabled?
		$this->_publishing = 
			is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications')
			? 1 : 0;
		
		// Include scripts
		$this->_inlcudeScripts();
		
		// Check for necessary db setup
		if ($this->_config->get( 'dbcheck', 1 ))
		{
			$this->_checkTables();
		}
		
		// Is component on?
		if (!$this->_config->get( 'component_on', 0 ))
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
				
		// Needed to support no-conflict jquery mode
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$plugin 		= JPluginHelper::getPlugin( 'system', 'jquery' );
			$p_params 		= new JParameter($plugin->params);
			if ($p_params->get('noconflictSite'))
			{
				$app = JFactory::getApplication();
				$document = JFactory::getDocument();
				$document->addScript('templates' . DS . $app->getTemplate() . DS .  'js' . DS . 'modal.js');			
			}
			$this->_getStyles('', 'jquery.fancybox.css', true); // add fancybox styling
		}
										
		switch ( $this->_task ) 
		{
			// Setup
			case 'start': 				
			case 'setup': 				
				$this->_setup(); 		
				break;
			
			// Project views
			case 'view':   				
				$this->_view();   		
				break;
			
			// Edit project	
			case 'edit':   				
				$this->_edit();   		
				break;
			
			// Change of state	
			case 'suspend':   			
			case 'reinstate':   		
			case 'fixownership':  
			case 'delete':   	 		
				$this->_changeState();  
				break;
							
			// Listings
			case 'intro':  		
				// Front-face projects		
				$this->_intro();  		
				break; 
				
			case 'browse':  
				// Public projects list			
				$this->_browse();  		
				break;
				
			case 'features':  	
				// Intro to component features		
				$this->_features();  	
				break;
				
			// Activate provisioned project
			case 'activate':        			
				$this->_activate();        	
				break;	
				
			// Reviewers
			case 'process':        			
				$this->_process();        	
				break;
			
			// Image handling - via iFrame
			case 'upload':     			
				$this->_upload();     	
				break;
				
			case 'deleteimg':  			
				$this->_deleteimg();  	
				break;
				
			case 'img':        			
				$this->_img();        	
				break;
			
			// AJAX calls
			case 'verify':  			
				$this->_verify();    	
				break;
				
			case 'autocomplete':		
				$this->_autocomplete(); 
				break;
				
			case 'showcount':			
				$this->showCount(); 	
				break;
				
			case 'wikipreview':			
				$this->_wikiPreview(); 	
				break;			
			
			// Authentication for outside services	
			case 'auth':			
				$this->_auth(); 	
				break;
				
			// Stats reports
			case 'reports':			
				$this->_reports(); 	
				break;
				
			// Public view
			case 'get':		
				$this->_pubView(); 	
				break;
												
			default: 
				$this->_task = 'intro';
				$this->_intro(); 									
				break;
		}
	}
	
	/**
	 * Pub view for project files, notes etc.
	 * 
	 * @return     void
	 */
	protected function _pubView()
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
		$dispatcher =& JDispatcher::getInstance();
		
		// Serve requested item
	 	$content = $dispatcher->trigger( 'serve', array($objSt->projectid, $objSt->reference));

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
			foreach($messages as $message) 
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
	 * Check for necessary db tables
	 * 
	 * @return     void
	 */
	protected function _checkTables()
	{
		$tables = $this->database->getTableList();
		$prefix = $this->database->getPrefix();

		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_projects' . DS . 'helpers' . DS . 'install.php');
		
		$installHelper = new ProjectsInstall($this->database, $tables);
		
		// Initial install
		if (!in_array($prefix . 'projects', $tables)) 
		{
			$installHelper->runInstall();
		}
		
		// Enable project logs
		if (!in_array($prefix . 'project_logs', $tables)) 
		{
			$installHelper->installLogs();
		}

		// Enable project stats
		if (!in_array($prefix . 'project_stats', $tables)) 
		{
			$installHelper->installStats();
		}

		// Enable project files remote connections
		if (!in_array($prefix . 'project_remote_files', $tables)) 
		{
			$installHelper->installRemotes();
		}
		
		// Enable publications
		if ($this->_publishing) 
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_publications' . DS . 'helpers' . DS . 'install.php');
			
			$pubInstallHelper = new PubInstall($this->database, $tables);
			$pubInstallHelper->installPublishing();
		}
		elseif (is_file(JPATH_ROOT . DS . 'plugins' . DS . 'projects'. DS
				.'publications.php'))
		{
			// Make entry for projects publications plugin
			$installHelper->installPlugin('publications', 0);
		}
		
		// Enable public links (NEW)
		if (!in_array($prefix . 'project_public_stamps', $tables)) 
		{
			$installHelper->installPubStamps();
		}		
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
		
		// Logging
		if ( is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.log.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.log.php');
		}
		
		// Stats
		if ( is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.stats.php');
		}
		
		// Remote connections support (new)
		if ( is_file(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'connect.php'))
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'connect.php' );
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
			require_once( JPATH_SITE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'remote' . DS . 'google.php' );			
		}				
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	/**
	 * Login view
	 * 
	 * @return     void
	 */
	protected function _login() 
	{		
		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
		
		// Fix for weird bug refusing to redirect to /files
		if (substr($rtrn, -6, 6) == '/files')
		{
			$rtrn .= DS;
		}
		
		$this->setRedirect(
			JRoute::_('index.php?option=com_login').'?return=' . base64_encode($rtrn),
			$this->_msg,
			'warning'
		);
	}
			
	/**
	 * Intro to projects (main view)
	 * 
	 * @return     void
	 */	
	protected function _intro() 
	{
		// Incoming
		$action  = JRequest::getVar( 'action', '' );
		
		if ($this->juser->get('guest') && $action == 'login') 
		{
			$this->_msg = JText::_('COM_PROJECTS_LOGIN_TO_VIEW_YOUR_PROJECTS');
			$this->_login();
			return;
		}
												
		// Instantiate a new view
		$view = new JView( array('name'=>'intro') );
		$view->filters = array();
		
		// Filters
		$view->filters['mine']   	 = 1;
		$view->filters['updates'] 	 = 1;
		$view->filters['sortby'] 	 = 'myprojects';
		$setup_complete 			 = $this->_config->get('confirm_step', 0) ? 3 : 2;
		
		// Get a record count
		$obj = new Project( $this->database );
		$view->total = $obj->getCount($view->filters, $admin = false, $this->juser->get('id'), 0, $setup_complete);
		
		// Get records
		$view->rows = $obj->getRecords($view->filters, $admin = false, $this->juser->get('id'), 0, $setup_complete);

		// Add the CSS to the template
		$this->_getStyles('', 'introduction.css', true); // component, stylesheet name, look in media system dir
		$this->_getStyles();

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);
		$view->title = $this->title;
		
		// Log activity
		$this->_logActivity();
		
		// Output HTML
		$view->option 	= $this->_option;
		$view->config 	= $this->_config;
		$view->database = $this->database;
		$view->uid 		= $this->juser->get('id');
		$view->guest 	= $this->juser->get('guest');
		$view->msg 		= isset($this->_msg) && $this->_msg ? $this->_msg : $this->getNotifications('success');
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	/**
	 * Features page
	 * 
	 * @return     void
	 */	
	protected function _features() 
	{		
		// Get language file
		JPlugin::loadLanguage( 'com_projects_features' );
		
		// Instantiate a new view
		$view = new JView( array('name'=>'intro', 'layout'=>'features') );
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getProjectScripts();

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);
		$view->title = $this->title;
		
		// Log activity
		$this->_logActivity();
						
		// Output HTML
		$view->option = $this->_option;
		$view->config = $this->_config;
		$view->guest  = $this->juser->get('guest');
		$view->publishing	= $this->_publishing;
		
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->display();		
	}
	
	/**
	 * Browse projects
	 * 
	 * @return     void
	 */	
	protected function _browse() 
	{
		// Incoming
		$reviewer 	= JRequest::getVar( 'reviewer', '' );
		$action  	= JRequest::getVar( 'action', '' );
		$layout	 	= 'default';
		
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
			
			if ($this->checkReviewerAuth($reviewer))
			{
				$layout = $reviewer;	
			}
			else
			{
				$view 		 = new JView( array('name'=>'error') );
				$view->error = JText::_('COM_PROJECTS_REVIEWER_RESTRICTED_ACCESS');
				$view->title = $reviewer == 'sponsored' 
							 ? JText::_('COM_PROJECTS_REVIEWER_SPS')
							 : JText::_('COM_PROJECTS_REVIEWER_HIPAA');
				$view->display();
				return;
			}
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'browse', 'layout' => $layout) );

		// Incoming
		$view->filters = array();
		$view->filters['limit']  	= JRequest::getVar( 'limit', intval($this->_config->get('limit', 25)), 'request' );
		$view->filters['start']  	= JRequest::getInt( 'limitstart', 0, 'get' );
		$view->filters['sortby'] 	= JRequest::getVar( 'sortby', 'title' );
		$view->filters['search'] 	= JRequest::getVar( 'search', '' );
		$view->filters['sortdir']	= JRequest::getVar( 'sortdir', 'ASC');
		$view->filters['getowner']	= 1;
		$view->filters['reviewer']	= $reviewer;
		if ($reviewer == 'sensitive' || $reviewer == 'sponsored')
		{
			$view->filters['filterby']	= JRequest::getVar( 'filterby', 'pending' );	
		}
				
		// Get config
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		
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
		$view->total = $obj->getCount($view->filters, $admin = false, $this->juser->get('id'), 0, $setup_complete);
		
		// Get records
		$view->rows = $obj->getRecords( $view->filters, $admin = false, $this->juser->get('id'), 0, $setup_complete );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
		
		// Log activity
		$this->_logActivity(0, 'general', $layout);
		
		// Output HTML
		$view->option 		= $this->_option;
		$view->config 		= $this->_config;
		$view->database 	= $this->database;
		$view->uid 			= $this->juser->get('id');
		$view->guest 		= $this->juser->get('guest');
		$view->title 		= $this->title;
		$view->reviewer 	= $reviewer;
		$view->msg 			= isset($this->_msg) && $this->_msg ? $this->_msg : $this->getNotifications('success');
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	/**
	 * Setup screens
	 * 
	 * @return     void
	 */	
	protected function _setup() 
	{	
		// Incoming
		$save_stage 	= JRequest::getInt( 'save_stage', '0');
		$extended 		= JRequest::getInt( 'extended', 0, 'post');
		$requested_step = JRequest::getInt( 'step', 6);			
		$tempid 		= JRequest::getInt( 'tempid', 0 );
		$restricted 	= JRequest::getVar( 'restricted', 'no', 'post' );
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
			$creatorgroup = $this->_config->get('creatorgroup', '');
			
			if ($creatorgroup) 
			{	
				$cgroup = Hubzero_Group::getInstance($creatorgroup);
				if ($cgroup)
				{
					if (!$cgroup->is_member_of('members',$this->juser->get('id')) &&
						!$cgroup->is_member_of('managers',$this->juser->get('id'))) 
					{
						$this->_buildPathway(null);
						$view = new JView( array('name'=>'error', 'layout' =>'restricted') );
						$view->error  = JText::_('COM_PROJECTS_SETUP_ERROR_NOT_FROM_CREATOR_GROUP');
						$view->title = $this->title;
						$view->display();
						return;
					}
				}
			}		
			
			// New entry or error
			$obj->id 			= 0;
			$obj->alias 		= JRequest::getVar( 'name', '', 'post' );
			$obj->title 		= JRequest::getVar( 'title', '', 'post' );
			$obj->about 		= rtrim(Hubzero_Filter::cleanXss(JRequest::getVar( 'about', '', 'post' )));
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
			$group = Hubzero_Group::getInstance( $this->_gid );

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
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
					
		// Is earlier setup stage requested and are we allowed to go there?
		$stage = $requested_step != 6 && $obj->setup_stage >= $requested_step && $obj->setup_stage != $setup_complete 
			   ? $requested_step : $obj->setup_stage; 
				
		// Get temp id for saving image before saving project
		if ($stage < 1) 
		{
			$tempid = $tempid ? $tempid : ProjectsHtml::generateCode (4 ,4 ,0 ,1 ,0 );
		}
		
		// Saving new team members?
		$members = urldecode(trim(JRequest::getVar( 'newmember', '' )));
		$groups = urldecode(trim(JRequest::getVar( 'newgroup', '' )));
		
		// Get user session
		$jsession =& JFactory::getSession();
				
		if ($members or $groups) 
		{
			// Get plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher =& JDispatcher::getInstance();
			$content = $dispatcher->trigger( 'onProject', array(
				$project, $this->_option, $authorized, 
				$this->juser->get('id'), '', '', 'save' 
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
			
			if ($this->_save($pid, $what, $setup = 1, $tempid)) 
			{									
				// Record setup stage and move on
				if ($save_stage > $obj->setup_stage) 
				{
					$obj->saveStage($this->_identifier, $save_stage);
				}
				$stage = $save_stage;
				
				// Log activity
				$this->_logActivity($pid, 'setup', $what, 'save', $authorized);
				if ($this->_config->get('logging', 0))
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
		if ($this->_config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);					
		}
		
		// Do we need to ask about restricted data up front?
		if ($this->_config->get('restricted_upfront', 0) == 1 && !$this->_identifier) 
		{
			$proceed = JRequest::getInt( 'proceed', 0, 'post');
			if (!$proceed)
			{
				$layout = 'restricted';
			}
		}
				
		// Instantiate a new view
		$view = new JView( array('name'=>'setup','layout' => $layout) );	
		
		// Get project params
		$view->params = new JParameter( $obj->params );
		
		// Pass variables to view
		$view->project 			= $obj;
		$view->stage 			= $stage;
		$view->requested_step 	= $requested_step;
		$view->tempid 			= $tempid;
		
		// Special variables for project description stage
		if ($stage == 0) 
		{
			$view->verified = JRequest::getInt( 'verified', 0 );
			$view->extended = $extended;
		}
							
		// Collect known information about project (for info box)
		if ($stage != 0) 
		{ 
			// Get some more variables
			$objT = new ProjectType( $this->database );
			$view->typetitle = $objT->getTypeTitle($obj->type);
			$view->uploadnow = JRequest::getInt( 'uploadnow', 0 );
			
			// Get project thumb
			$view->thumb_src = ProjectsHtml::getThumbSrc($obj->id, $obj->alias, $obj->picture, $this->_config); 			
		}
		
		// Editing team
		if ($stage == 1) 
		{			
			// Get plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher =& JDispatcher::getInstance();
			
			// Get project
			$project = $obj->getProject($this->_identifier, $this->juser->get('id'));
			
			// Get plugin output
			$tAction = JRequest::getVar( 'action', 'setup');
			$content = $dispatcher->trigger( 'onProject', array(
				$project, $this->_option, $authorized,
				$this->juser->get('id'), $this->getNotifications('success'), 
				$this->getNotifications('error'), $tAction 
			));
				
			// Get plugin output
			if (isset($content[0])) 
			{
				if (isset($content[0]['msg']) && !empty($content[0]['msg'])) 
				{
					$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
				}
				if($content[0]['html'])
				{
					$view->content = $content[0]['html'];	
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
			$view->team 		= $objO->getOwnerNames($this->_identifier);

			// Load updated project
			$obj->loadProject($this->_identifier);
			$view->params = new JParameter( $obj->params );
		}	
		
		// Set the pathway
		$this->_buildPathway($view->project, $group);
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getProjectScripts();
		$this->_getScripts('assets/js/setup');
					
		// Output HTML
		$view->title  		= $this->title;
		$view->option 		= $this->_option;
		$view->config 		= $this->_config;
		$view->gid 			= $this->_gid;
		$view->group 		= $group;
		$view->restricted 	= $restricted;
		
		// Get messages	and errors	
		$view->msg = isset($this->_msg) ? $this->_msg : $this->getNotifications('success');
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		
		$view->display();
	}
		
	/**
	 * Project view
	 * 
	 * @return     void
	 */	
	protected function _view() 
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
			$view = new JView( array('name'=>'error') );
			$view->error  = JText::_('COM_PROJECTS_PROJECT_RELOGIN');
			$view->title = JText::_('COM_PROJECTS_PROJECT_RELOGIN_REQUIRED');
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
		$obj  = new Project( $this->database );
		$objO = new ProjectOwner( $this->database );
		$objAA = new ProjectActivity( $this->database );
							
		// Is user invited to project?
		$confirmcode = JRequest::getVar( 'confirm', '' );
		$email = JRequest::getVar( 'email', '' );

		// Load project
		$project = $obj->getProject($this->_identifier, $this->juser->get('id'));
		if (!$project) 
		{
			$this->setError(JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
			$this->_task = '';
			$this->_intro();
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
			$group = Hubzero_Group::getInstance( $project->owned_by_group );
			
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
					$view->config 	= $this->_config;
					$view->uid 		= $this->juser->get('id');
					$view->guest 	= $this->juser->get('guest'); 
					$view->display();
					return;
				} 
				else 
				{
					// Project on hold					
					$view = new JView( array('name'=>'error') );
					$view->error  = JText::_('COM_PROJECTS_PROJECT_OWNER_DELETED');
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
			$this->_task = '';
			$this->_intro();
			return;
		}
		
		// Get publication of a provisioned project
		if ($project->provisioned == 1)
		{
			if (!$this->_publishing)
			{
				$this->setError(JText::_('COM_PROJECTS_PROJECT_CANNOT_LOAD'));
				$this->_task = '';
				$this->_intro();
				return;
			}
			
			$objPub = new Publication($this->database);
			$pub = $objPub->getProvPublication($project->id);	
		}
			
		// Check if project is in setup
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		if ($project->setup_stage < $setup_complete && (!$ajax && $this->active != 'team')) 
		{	
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . a . 'task=setup'
				. a . 'alias=' . $project->alias);
			return;
		}
		
		// Sync with system group in case of changes
		if ($sync) 
		{
			$objO->sysGroup($project->alias, $this->_config->get('group_prefix', 'pr-'));
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
			$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
			if($ugs && count($ugs) > 0)
			{
				$sdata_group 	= $this->_config->get('sdata_group', '');
				$ginfo_group 	= $this->_config->get('ginfo_group', '');

				foreach ($ugs as $ug)
				{
					if ($this->_config->get('approve_restricted') && $sdata_group && $ug->cn == $sdata_group ) 
					{
						$reviewer = 'sensitive';
					}
					elseif ($this->_config->get('grantinfo') && $ginfo_group && $ug->cn == $ginfo_group )
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
						$objO->sysGroup($project->alias, $this->_config->get('group_prefix', 'pr-'));
						
						// Go to project page					
						$this->_redirect = JRoute::_('index.php?option=' . $this->_option 
							. a . 'alias=' . $project->alias);
						return;
					}
				}
				else 
				{
					// Different email
					$view 		  = new JView( array('name'=>'error') );
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
					$view 			= new JView( array('name'=>'error') );
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
											
		// Instantiate a new view
		$view 				= new JView( array('name'=>'view', 'layout' => $layout) );		
		$view->project 		= $project;
		$view->suspended 	= $suspended;
		$view->reviewer 	= $reviewer;
				
		// Provisioned project
		if ($project->provisioned == 1) 
		{
			// Get JS & CSS
			$document =& JFactory::getDocument();
			$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'publications.css');
			
			$view->pub 		 = isset($pub) ? $pub : '';
			$view->team 	 = $objO->getOwnerNames($this->_identifier);
			$view->suggested = $this->_suggestAlias($pub->title);
			$view->verified  = $this->_verify(0, $view->suggested, $pid);
			$view->suggested = $view->verified ? $view->suggested : '';
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
				$timecheck = date('Y-m-d H:i:s', time() - (10 * 60)); // last second
				if ($project->created_by_user == $this->juser->get('id') && $timecheck <= $project->created)
				{
				    $objAA->deleteActivity($aid);
				}
			}
		}
		
		// Get latest log from user session
		$jsession =& JFactory::getSession();
		
		// Log activity
		if (!$jsession->get('projects-nolog'))
		{
			$this->_logActivity($pid, 'project', $this->active, $action, $authorized);		
		}
		
		// Allow future logging
		if ($this->_config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);					
		}
		
		// Go through plugins
		$view->content = '';
		if ($layout == 'internal') 
		{
			$plugin = $this->active == 'feed' ? 'blog' : $this->active;
			$plugin = $this->active == 'info' ? '' : $plugin;
					
			// Get plugin
			JPluginHelper::importPlugin( 'projects');
			$dispatcher =& JDispatcher::getInstance();
			
			// Get available plugins
			$hub_project_plugins = $dispatcher->trigger( 'onProjectAreas', array( ) );
			$view->tabs = $hub_project_plugins;
			
			// Make sure we have name and title
			$tabs = array();
			for ($i = 0, $n = count($view->tabs); $i <= $n; $i++) 
			{
				if (empty($view->tabs[$i]) || !isset($view->tabs[$i]['name']))
				{
					unset($view->tabs[$i]);
				}
				else
				{
					$tabs[] = $view->tabs[$i]['name'];
				}
			}
			
			// Get plugin content
			if ($this->active != 'info')
			{
				// Do not go further if plugin is inactive or does not exist
				if (!in_array($plugin, $tabs))
				{
					$this->_redirect = JRoute::_('index.php?option=' . $this->_option
						. a . 'task=view' . a . 'alias=' . $project->alias);
					return;
				}
				
				// Need this to direct to correct repository
				$extraParam = '';				
				$case = JRequest::getVar( 'case', '' );	
				$pagename = JRequest::getVar( 'pagename', '' );			
				
				// Are we trying to load app source code or wiki?
				if ((($this->active == 'apps' && $action == 'source') 
					|| ($this->active == 'files' && preg_match("/apps:/", $case))
					|| ($this->active == 'notes' && preg_match("/app:/", $pagename))
					|| ($this->active == 'apps' && $action == 'wiki'))
					&& 	is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' 
					. DS . 'com_apps' . DS . 'tables' . DS . 'app.php') 
					&& JPluginHelper::isEnabled('projects', 'apps'))
				{			
					$appname = JRequest::getVar( 'app', '' );
					$reponame = preg_replace( "/apps:/", "", $case);
					
					$appname = $appname ? $appname : $reponame;
					
					// Get app library
					require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
						. DS . 'com_apps' . DS . 'tables' . DS . 'app.php');
					
					// Check that app belongs to this project
					$app = new App( $this->database );
					$app->loadApp($appname, $project->id);
					
					// Direct to relevant plugin
					if (($action == 'source' || $this->active == 'files') && $app && $app->status > 1)
					{
						$plugin = 'files';
						$extraParam = 'apps:' . $app->name;
						$action = JRequest::getVar( 'do', '' );
						$this->active = 'apps';
					}
					if ($app && ($action == 'wiki' || $this->active == 'notes'))
					{
						$plugin = 'notes';
						$extraParam = $app->name;
						$this->active = 'apps';
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
								$view->content .= $section['html'];
							}
						}
						elseif (isset($section['referer']) && $section['referer'] != '') 
						{
							if ($this->_config->get('logging', 0))
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
			$view->project->counts = $counts;				
		}
		
		// Record page view
		if ($project->owner && $this->active == 'feed' && $project->confirmed) 
		{
			$objO->recordView($pid, $this->juser->get('id'));
		}
		
		// Get project params
		$view->params = new JParameter( $project->params );
		
		// Get team for public page
		if ($layout == 'external' && $view->params->get('team_public', 0)) 
		{
			$view->team = $objO->getOwners( $pid, $filters = array('status' => 1) );
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
					$this->_config,
					$view->params
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
					$side_modules = $this->_getModules( $view->project, $this->_option, 
						$this->juser->get('id'), $suggestions);
				}
			}
						
			$view->side_modules      = isset($side_modules) ? $side_modules : '';
			$view->notification      = isset($notification) ? $notification : '';
		}
												
		// Output HTML
		$view->title  		= $this->title;
		$view->active 		= $this->active;
		$view->task 		= $this->_task;
		$view->authorized	= $authorized;
		$view->option 		= $this->_option;
		$view->config 		= $this->_config;
		$view->uid 			= $this->juser->get('id');
		$view->guest 		= $this->juser->get('guest');
		$view->msg 			= $this->getNotifications('success');
		
		if ($layout == 'invited')
		{
			$view->confirmcode  = $confirmcode;
			$view->email		= $email;
		}
				
		$error 	= $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error) 
		{
			$view->setError( $error );
		}
		$view->display();
		return;
	}
	
	/**
	 * Edit project view
	 * 
	 * @return     void
	 */	
	protected function _edit() 
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
		if ($this->_config->get('edit_settings', 0)) 
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
			$pid = $project->id;
			$alias = $project->alias;
		}
	
		// Check if project is in setup
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
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
		$jsession =& JFactory::getSession();
		
		// Log activity
		if (!$jsession->get('projects-nolog'))
		{
			$logAction = $save ? 'save' : 'edit';
			$this->_logActivity($pid, 'project', $this->active, $logAction, $authorized);		
		}
		
		// Allow future logging
		if ($this->_config->get('logging', 0))
		{
			$jsession->set('projects-nolog', 0);					
		}
				
		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );		
		$view->project = $project;
				
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
					if ($this->_save($pid, 'params', 0) && $this->_save($pid, 'privacy', 0) ) 
					{	
						// Set message
						$this->setNotification(JText::_('COM_PROJECTS_SETTINGS_SAVED'));
					}
				}
				
				// Get project params
				$view->params = new JParameter( $view->project->params );
			
			break;
			
			case 'team':			
				// Get team plugin
				JPluginHelper::importPlugin( 'projects', 'team' );
				$dispatcher =& JDispatcher::getInstance();
				$auth = $project->role == 1 ? 1 : 0;
				$tAction = $save ? 'save' : JRequest::getVar( 'action', 'edit');
				$content = $dispatcher->trigger( 'onProject', array(
					$project, $this->_option, $auth,
					$this->juser->get('id'), $this->getNotifications('success'), 
					$this->getNotifications('error'), $tAction 
				));
				
				// Get plugin output
				if (isset($content[0])) 
				{
					if (isset($content[0]['msg']) && !empty($content[0]['msg'])) 
					{
						$this->setNotification($content[0]['msg']['message'], $content[0]['msg']['type']);
					}
					if($content[0]['html'])
					{
						$view->content = $content[0]['html'];	
					}
					else 
					{						
						$this->_redirect = JRoute::_('index.php?option=' . $this->_option 
							. a . 'alias=' . $project->alias . a . 'task=edit') . '?edit=team';
						return;
					}
				}
			break;
			
			case 'info':
			default:
				$objT = new ProjectType( $this->database );
				$view->types = $objT->getTypes();
				
				if ($save) 
				{
					// Save info
					if ($this->_save($pid, 'info', 0) && $this->_save($pid, 1, 0)) 
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
				$objAA->recordActivity( $pid, $this->juser->get('id'), JText::_('COM_PROJECTS_EDITED') 
					. ' ' . JText::_('COM_PROJECTS_PROJECT_INFORMATION'), $pid, 
					JText::_('COM_PROJECTS_PROJECT_INFORMATION'), JRoute::_('index.php?option=' 
					. $this->_option . a . 'alias=' . $project->alias . a . 'active=info'), 'project' );
			}
			
			if ($this->_config->get('logging', 0))
			{
				$jsession->set('projects-nolog', 1);					
			}
			$url = JRoute::_('index.php?option=' . $this->_option
				. a . 'task=edit' . a . 'alias='.$project->alias) . '?edit=' . $this->active;
			$this->_redirect = $url;
			return;
		}
		
		// Output HTML
		$view->uid 			= $this->juser->get('id');
		$view->active 		= $active;
		$view->sections 	= $sections;
		$view->title  		= $this->title;
		$view->authorized 	= $authorized;
		$view->option 		= $this->_option;
		$view->config 		= $this->_config;
		$view->task 		= $this->_task;
		$view->publishing	= $this->_publishing;
	
		// Get messages	and errors	
		$view->msg = $this->getNotifications('success');
		$error = $this->getError() ? $this->getError() : $this->getNotifications('error');
		if ($error) 
		{
			$view->setError( $error );
		}
		$view->display();		
	}
	
	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	/**
	 * Save project information
	 * 
	 * @return     void
	 */
	protected function _save($pid = 0, $what = 'info', $setup = 1, $tempid = 0) 
	{
		$dateFormat = '%b %d, %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'M d, Y';
			$tz = false;
		}

		// Incoming
		$name 		= trim(JRequest::getVar( 'name', '', 'post' ));
		$title 		= trim(JRequest::getVar( 'title', '', 'post' ));
		$about 		= trim(JRequest::getVar( 'about', '', 'post' ));
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
				if (!$this->_verify(0) && $setup) 
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
					$obj->alias = $setup ? $name : $obj->alias; // name can only change during setup
					$obj->modified = date( 'Y-m-d H:i:s' );
					$obj->modified_by = $this->juser->get('id');
				}
				else 
				{
					$obj->alias = $name;
					$obj->private = $this->_config->get('privacy', 1);
					$obj->created = date( 'Y-m-d H:i:s' );
					$obj->created_by_user = $this->juser->get('id');
					$obj->owned_by_user = $this->juser->get('id');
					$obj->owned_by_group = $this->_gid;
					
					// Get image name if tempid was used
					if ($tempid) 
					{
						$obj->picture = $this->_getPictureName ( $tempid, $temp = 1 );
					}
				}
				$obj->title = Hubzero_View_Helper_Html::shortenText($title, 250, 0);
				$obj->about = rtrim(Hubzero_Filter::cleanXss($about));
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
				if (!$obj->store()) {
					$this->setError( $obj->getError() );
					return false;
				} 
				if (!$obj->id) {
					$obj->checkin();
				}
				
				// Save resctricted data choice
				if ($restricted && $setup && !$pid)
				{
					$restricted = $restricted == 'yes' ? 'yes' : 'no';
					
					// Save params	
					$obj->saveParam($obj->id, 'restricted_data', htmlentities($restricted));
				}				
				
				// Send ID of newly created project back to setup screens
				$this->_identifier = $obj->id;
				
				if (!$pid && $obj->id) 
				{
					// Save owners for new projects
					if ($this->_gid) 
					{
						if (!$objO->saveOwners ( $obj->id, $this->juser->get('id'), 0, 
						$this->_gid, 0, 1, 1, '', $split_group_roles = 0 )) 
						{
							$this->setError( JText::_('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $objO->getError() );
							return false;
						}
						// Make sure project creator is manager
						$objO->reassignRole ( $obj->id, $users = array($this->juser->get('id')), 0 , 1 );
					}
					elseif (!$objO->saveOwners ( $obj->id, $this->juser->get('id'), 
					$this->juser->get('id'), $this->_gid, 1, 1, 1 )) 
					{
						$this->setError( JText::_('COM_PROJECTS_ERROR_SAVING_AUTHORS') . ': ' . $objO->getError() );
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
					foreach($incoming as $key => $value) 
					{									
						$obj->saveParam($pid, $key, htmlentities($value));
						
						// Get updated project
						$project = $obj->getProject($pid, $this->juser->get('id'));
						
						// If grant information changed
						if ($key == 'grant_status' && $old_params != $project->params)
						{
							// Meta data for comment
							$meta = '<meta>' . JHTML::_('date', date( 'Y-m-d H:i:s' ), $dateFormat, $tz)
							. ' - ' . $this->juser->get('name') . '</meta>';
							
							$cbase   = $obj->admin_notes;
							$cbase  .= '<nb:sponsored>' . JText::_('COM_PROJECTS_PROJECT_MANAGER_GRANT_INFO_UPDATE')
							. $meta . '</nb:sponsored>';
							$obj->admin_notes = $cbase;
							
							// Save admin notes
							if (!$obj->store()) 
							{
								$this->setError( $obj->getError() );
								return false;
							}

							$admingroup = $this->_config->get('ginfo_group', '');

							if (Hubzero_Group::getInstance($admingroup))
							{
								$admins = $this->_getGroupMembers($admingroup);

								// Send out email to admins
								if (!empty($admins)) 
								{
									ProjectsHelper::sendHUBMessage(
										$this->_option,
										$this->_config,
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
					$setup_complete 	= $this->_config->get('confirm_step', 0) ? 3 : 2;
					$agree 				= JRequest::getInt( 'agree', 0, 'post' );
					$restricted 		= JRequest::getVar( 'restricted', '', 'post' );	
					$agree_irb 			= JRequest::getInt( 'agree_irb', 0, 'post' );
					$agree_ferpa 		= JRequest::getInt( 'agree_ferpa', 0, 'post' );
					$state				= 1;

					if ($setup_complete == 3 ) 
					{					
						// General restricted data question
						if ($this->_config->get('restricted_data', 0) == 2) 
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
						if ($this->_config->get('restricted_data', 0) == 1) 
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
								if ($this->_config->get('approve_restricted', 0)) 
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
						if ($this->_config->get('grantinfo', 0)) 
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
					$objO->sysGroup($obj->alias, $this->_config->get('group_prefix', 'pr-'));						
	
					// Activate project
					if (!$active) 
					{
						$obj->state = $state;
						$obj->provisioned = 0; // remove provisioned flag if any
						$obj->created = date( 'Y-m-d H:i:s' );

						// Save changes
						if (!$obj->store()) 
						{
							$this->setError( $obj->getError() );
							return false;
						}
						
						// Email administrators about a new project											
						if ($this->_config->get('messaging') == 1)
						{
							// Get updated project
							$project = $obj->getProject($pid, $this->juser->get('id'));
							
							$admingroup 	= $this->_config->get('admingroup', '');
							$sdata_group 	= $this->_config->get('sdata_group', '');
							$ginfo_group 	= $this->_config->get('ginfo_group', '');
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
									$this->_config,
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
							$this->_notifyTeam($pid, 'invite');
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
	protected function _activate() 
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
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
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
		if (!$this->_verify(0, $name, $project->id)) 
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
			// Instantiate a new view
			$view = new JView( array('name'=>'view', 'layout' => 'provisioned') );		
			$view->project = $project;
			$view->project->title = $title;

			// Output HTML
			$view->pub 		 	= isset($pub) ? $pub : '';
			$view->team 	 	= $objO->getOwnerNames($this->_identifier);
			$view->suggested 	= $name;
			$view->verified  	= $this->_verify(0, $view->suggested, $project->id);
			$view->suggested 	= $view->verified ? $view->suggested : '';
			$view->title  		= $this->title;
			$view->active 		= $this->active;
			$view->task 		= $this->_task;
			$view->authorized 	= 1;
			$view->option 		= $this->_option;
			$view->config 		= $this->_config;
			$view->uid 			= $this->juser->get('id');
			$view->guest 		= $this->juser->get('guest');
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			$view->msg = isset($this->_msg) ? $this->_msg : '';
			$view->display();
			return;
		}
		
		// Get Publications helper
		$helper = new PublicationHelper($this->database);	
		
		// Get project parent directory
		$path = $helper->buildDevPath($project->alias, '', '', '');
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
			$obj->title 		= Hubzero_View_Helper_Html::shortenText($title, 250, 0);
			$obj->alias 		= $name;
			$obj->state 		= 0;
			$obj->setup_stage 	= $setup_complete - 1;
			$obj->modified		= date( 'Y-m-d H:i:s' );
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
		$this->_setup();
		return;
	}
	
	/**
	 * Change project status
	 * 
	 * @return     void
	 */	
	protected function _changeState() 
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
			$keep = JRequest::getInt( 'keep', 0 );
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
		$obj->modified = date( 'Y-m-d H:i:s' );
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
				$this->_task = 'intro';
				$this->_intro();
				return;
			}
		}
		$this->_task = 'view';
		$this->_view();	
	}
	
	/**
	 * Stats
	 * 
	 * @return     void
	 */
	protected function _reports() 
	{
		// Incoming
		$period = JRequest::getVar( 'period', 'alltime');
		
		// Instantiate a project and related classes
		$obj = new Project( $this->database );
		$objAA = new ProjectActivity ( $this->database );
		
		// Is user in special admin group to view advanced stats?
		$admin = $this->checkReviewerAuth('general');
							
		// Get all test projects
		$testProjects = $obj->getTestProjects();
							
		// Instantiate a new view
		$view = new JView( array('name'=>'reports') );
		
		// Add the CSS to the template
		$this->_getStyles();

		// Set the pathway
		$this->_buildPathway(null);

		// Set the page title
		$this->_buildTitle(null);
		$view->title = $this->title;
		
		// Log activity
		$this->_logActivity();		

		// Output HTML
		$view->task 		= $this->_task;
		$view->admin 		= $admin;
		$view->option 		= $this->_option;
		$view->config 		= $this->_config;
		$view->uid 			= $this->juser->get('id');
		$view->guest 		= $this->juser->get('guest');
		$view->stats		= $obj->getStats($period, $admin, $this->_config, $testProjects, $this->_publishing);
		$view->publishing	= $this->_publishing;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_msg) ? $this->_msg : '';
		$view->display();
		return;		
	}
	
	/**
	 * Authenticate for outside services
	 * 
	 * @return     void
	 */
	protected function _auth() 
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
		
	//----------------------------------------------------------
	// Reviewers
	//----------------------------------------------------------
	
	/**
	 * Reviewers actions (sensitive data, sponsored research)
	 * 
	 * @return     void
	 */	
	protected function _process() 
	{
		$dateFormat = '%b %d, %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'M d, Y';
			$tz = false;
		}

		// Incoming
		$reviewer 	= JRequest::getVar( 'reviewer', '' );
		$action  	= JRequest::getVar( 'action', '' );
		$comment  	= JRequest::getVar( 'comment', '' );
		$approve  	= JRequest::getInt( 'approve', 0 );
		$filterby  	= JRequest::getVar( ' ', 'pending' );
		$notify 	= JRequest::getVar( 'notify', 0, 'post' );
		
		// Instantiate a project and related classes
		$obj = new Project( $this->database );
		$objAA = new ProjectActivity ( $this->database );
		
		// Check authorization
		$authorized = $this->checkReviewerAuth($reviewer);
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
		
		$pid = $obj->id;
		
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
		$this->_logActivity($pid, 'reviewer', $reviewer, $action, $authorized);	
		
		if ($action == 'save' && !$this->getError() && $obj->id)
		{
			// Make sure admin_notes column is there
			if (!isset($obj->admin_notes))
			{
				$fields = $this->database->getTableFields('jos_projects');
				if (!array_key_exists('admin_notes', $fields['jos_projects'] )) 
				{
					$this->database->setQuery( "ALTER TABLE `jos_projects` ADD `admin_notes` text" );
					if (!$this->database->query()) 
					{
						echo $this->database->getErrorMsg();
						return false;
					}
				}
			}
			
			$cbase = $obj->admin_notes;
			
			// Meta data for comment
			$now = date( 'Y-m-d H:i:s' );
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
					$quota = $params->get('quota');
					$premiumQuota = ProjectsHtml::convertSize( 
						floatval($this->_config->get('premiumQuota', '30')), 'GB', 'b');
					$obj->saveParam($obj->id, 'quota', htmlentities($premiumQuota));
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
				$comment = Hubzero_View_Helper_Html::shortenText($comment, 500, 0);
				$comment = Hubzero_View_Helper_Html::purifyText($comment);
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
				$project = $obj->getProject($pid, $this->juser->get('id'));
				
				$admingroup = $reviewer == 'sensitive' 
					? $this->_config->get('sdata_group', '') 
					: $this->_config->get('ginfo_group', '');
					
				if (Hubzero_Group::getInstance($admingroup))
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
							$this->_config,
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
						$this->_notifyTeam($obj->id, 'invite');
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
			$view = new JView( array('name'=>'review') );
			
			// Output HTML
			$view->reviewer 	= $reviewer;
			$view->ajax 		= JRequest::getInt( 'ajax', 0 );
			$view->title 		= $this->title;
			$view->option 		= $this->_option;
			$view->project		= $obj;
			$view->params		= $params;
			$view->thumb_src 	= ProjectsHtml::getThumbSrc($obj->id, $obj->alias, $obj->picture, $this->_config);
			$view->config 		= $this->_config;
			$view->database 	= $this->database;
			$view->action		= $action;
			$view->filterby		= $filterby;
			$view->uid 			= $this->juser->get('id');
			$view->msg 			= isset($this->_msg) && $this->_msg ? $this->_msg : $this->getNotifications('success');
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			$view->display();
		}	
	}
	
	/**
	 * Authorize reviewer
	 * 
	 * @return     void
	 */
	protected function checkReviewerAuth($reviewer)
	{
		if ($reviewer != 'sponsored' && $reviewer != 'sensitive' && $reviewer != 'general')
		{
			return false;
		}
		
		if ($this->juser->get('guest'))
		{
			return false;
		}
		
		$sdata_group 	= $this->_config->get('sdata_group', '');
		$ginfo_group 	= $this->_config->get('ginfo_group', '');
		$admingroup 	= $this->_config->get('admingroup', '');
		$group      	= '';
		$authorized 	= false;
		
		// Get authorized group	
		if ($reviewer == 'sensitive' && $sdata_group)
		{
			$group = Hubzero_Group::getInstance($sdata_group);
		}
		elseif ($reviewer == 'sponsored' && $ginfo_group)
		{
			$group = Hubzero_Group::getInstance($ginfo_group);
		}
		elseif ($reviewer == 'general' && $admingroup)
		{
			$group = Hubzero_Group::getInstance($admingroup);
		}
			
		if ($group)
		{
			// Check if they're a member of this group
			$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
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
	
	//----------------------------------------------------------
	// Verification
	//----------------------------------------------------------
	
	/**
	 * Verify project/app name (AJAX)
	 * 
	 * @param  int $ajax
	 * @param  string $name
	 * @param  int $pid
	 * @return     void
	 */
	protected function _verify( $ajax = 0, $name = '', $pid = 0 )
	{
		// Incoming
		$name 	= $name ? $name : trim(JRequest::getVar( 'name', '' ));
		$pid 	= $pid ? $pid : JRequest::getInt( 'pid', 0 );
		$ajax 	= $ajax == 1 ? 1 : JRequest::getInt( 'ajax', 0 );
		$tool 	= JRequest::getInt( 'tool', 0 );
		$app 	= JRequest::getInt( 'app', 0 );
		$class 	= 'verify_failed';
		
		// Set name length
		$min_length = $tool ? 3 : $this->_config->get('min_name_length', 3);
		$max_length = $tool ? 20 : $this->_config->get('max_name_length', 25);
		
		// Array of reserved names (task names and default dirs)
		$reserved = array();
		$names = $this->_config->get('reserved_names', '');
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
		// Check for illegal characters
		elseif (preg_match('/[^a-z0-9]/', $name)) 
		{
			if (!$ajax) { return false; }
			$result = JText::_('COM_PROJECTS_ERROR_NAME_INVALID');
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
		// Verify app name uniqueness
		elseif ($tool) 
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
				. DS . 'com_apps' . DS . 'tables' . DS . 'app.php');
				
			$objA = new App( $this->database );
			if ($objA->checkUniqueName($name, $app))
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
	
	//----------------------------------------------------------
	// (Project) image handling
	//----------------------------------------------------------

	/**
	 * Upload project image
	 * 
	 * @return     void
	 */	
	protected function _upload()
	{
		// How many steps in setup process?
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		$prefix = JPATH_ROOT;	
		
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}
		
		// Incoming project ID
		$id 	= JRequest::getInt( 'id', 0 );
		$tempid = JRequest::getInt( 'tempid', 0 );
		if (!$id && !$tempid) 
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NO_ID') );
			$this->_img( '', $id, $tempid );
			return;
		}
		
		// Check authorization - extra check
		if ($id) 
		{
			$authorized = $this->_authorize($id);	
			if (!$authorized) 
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) 
		{
			$this->setError( JText::_('COM_PROJECTS_NO_FILE') );
			$this->_img( '', $id, $tempid );
			return;
		}
		
		// Build upload path
		$useid = $id ? $id : $tempid;
		
		// Use if or alias?
		if ($this->_config->get('use_alias', 1) && $id) 
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );	
		}
		else 
		{
			$dir = Hubzero_View_Helper_Html::niceidformat( $useid );
		}
		$webdir = DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
		$path  = $prefix . $webdir;
		$path .= !$id && $tempid ? DS . 'temp' : '';
		$path .= DS . $dir . DS . 'images';
		
		if (!is_dir( $path )) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) 
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->_img( '', $id, $tempid );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		
		// Do we have an old file we're replacing?
		$curfile = JRequest::getVar( 'currentfile', '' );
		
		// Delete older file with same name
		if (file_exists($path . DS . $file['name'])) 
		{
			JFile::delete($path . DS . $file['name']);
		}
		
		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) 
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_UPLOADING') );
			$file = $curfile;
		} 
		else 
		{
			if (ProjectsHelper::virusCheck($path . DS . $file['name']))
			{
				$this->setError(JText::_('Virus detected, refusing to upload'));
				$this->_img( '', $id, $tempid );
				return;
			}
			
			$ih = new ProjectsImgHandler();

			// Instantiate a project, change some info and save
			if ($id) 
			{
				$obj = new Project( $this->database );
				$obj->loadProject($id);
				$obj->picture = $file['name'];
				if (!$obj->store()) 
				{
					$this->setError( $obj->getError() );
				}
				elseif ($obj->setup_stage >= $setup_complete) 
				{
					// Record activity
					$objAA = new ProjectActivity( $this->database );
					$aid = $objAA->recordActivity( $id, $this->juser->get('id'),
						JText::_('COM_PROJECTS_REPLACED_PROJECT_PICTURE'), $id, '', 
						'', 'project', 0 );
				}
			}
			
			// Resize the image if necessary
			$ih->set('image',$file['name']);
			$ih->set('path',$path.DS);
			$ih->set('maxWidth', 186);
			$ih->set('maxHeight', 186);
			if (!$ih->process()) 
			{
				$this->setError( $ih->getError() );
			}
			
			// Create a thumbnail image
			$ih->set('maxWidth', 50);
			$ih->set('maxHeight', 50);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', $ih->createThumbName());
			if (!$ih->process()) 
			{
				$this->setError( $ih->getError() );
			}
				
			$file = $file['name'];
	
			// Remove old images
			if ($curfile != '' && $curfile != $file ) 
			{
				if (file_exists($path . DS . $curfile)) 
				{
					JFile::delete($path . DS . $curfile);
				}
				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path . DS . $curthumb)) 
				{
					JFile::delete($path . DS . $curthumb);
				}
			}
		}
		
		// Push through to the image view
		$this->_img( $file, $id, $tempid );
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
		$webdir = DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
		
		$from_dir =  Hubzero_View_Helper_Html::niceidformat( $temid );
		// Use if or alias?
		if ($this->_config->get('use_alias', 1)) 
		{
			$obj = new Project( $this->database );
			$to_dir = $obj->getAlias( $pid );	
		}
		else 
		{
			$to_dir = Hubzero_View_Helper_Html::niceidformat( $pid );
		}
		$from_path 	= $prefix . $webdir . DS . 'temp' . DS . $from_dir . DS . 'images';
		$to_path 	= $prefix . $webdir . DS . $to_dir . DS . 'images';
		
		jimport('joomla.filesystem.folder');
		
		// Make sure the path exist
		if (!is_dir( $to_path )) 
		{
			if (!JFolder::create( $to_path, 0777 )) 
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
		if ($this->_config->get( 'use_alias', 1 )) 
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );	
		}
		else 
		{
			$dir = Hubzero_View_Helper_Html::niceidformat( $id );
		}
		$webdir = DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
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
					if (preg_match( "/(bmp|gif|jpg|png|swf)/", $img_file )) 
					{
						$images[] = $img_file;
					}
					if (preg_match( "/_thumb/", $img_file )) 
					{
						$tns[] = $img_file;
					}
					$images = array_diff($images, $tns);
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
	 * Delete image
	 * 
	 * @return     void
	 */					
	protected function _deleteimg()
	{
		$prefix = JPATH_ROOT;	
	
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return false;
		}
		
		// Incoming project ID
		$id 	= JRequest::getInt( 'imaid', 0 );
		$tempid = JRequest::getInt( 'tempid', 0 );
		if (!$id && !$tempid) 
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NO_ID') );
			$this->_img( '', $id, $tempid );
			return;
		}
	
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file['name']) 
		{
			$this->setError( JText::_('COM_PROJECTS_NO_FILE') );
			$this->_img( '', $id, $tempid );
			return;
		}
		
		// Build the file path
		$useid = $id ? $id : $tempid;
		
		// Use if or alias?
		if ($id) 
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );	
		}
		else 
		{
			$dir = Hubzero_View_Helper_Html::niceidformat( $useid );
		}
		
		$webdir = DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
		$path   = $prefix . $webdir;
		$path  .= !$id && $tempid ? DS . 'temp' : '';
		$path  .= DS . $dir;
		$tpath  = $path;
		$path  .= DS . 'images';

		if (!file_exists($path . DS . $file) or !$file) 
		{ 
			$this->setError( JText::_('COM_PROJECTS_FILE_NOT_FOUND') ); 
		} 
		else 
		{
			$ih = new ProjectsImgHandler();
			
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
				$this->_img( $file, $id, $tempid );
				return;
			}
			
			$curthumb = $ih->createThumbName($file);
			if (file_exists($path . DS . $curthumb)) 
			{
				if (!JFile::delete($path . DS . $curthumb)) 
				{
					$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
					$this->_img( $file, $id );
					return;
				}
			}
			
			// Clean up temp folder
			if (!$id && $tempid) 
			{
				jimport('joomla.filesystem.folder');
				JFolder::delete( $tpath);
			}
			
			// Instantiate a project, change some info and save
			if ($id) {
				$obj = new Project( $this->database );
				$obj->loadProject($id);
				$obj->picture = '';
				if (!$obj->store()) 
				{
					$this->setError( $obj->getError() );
				}
			}

			$file = '';
		}
	
		// Push through to the image view
		$this->_img( $file, $id, $tempid);
	}
	
	/**
	 * Display project image and upload form
	 * 
	 * @param  string $file
	 * @param  int $id
	 * @param  int $tempid
	 * @return     void
	 */			
	protected function _img( $file = '', $id = 0, $tempid = 0 )
	{
		// Incoming
		if (!$id) 
		{
			$id = JRequest::getInt( 'id', 0, 'get' );
		}
		if (!$file) 
		{
			$file = JRequest::getVar( 'file', '', 'get' );
			
			// clean up file value
			$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
			$file  = preg_replace($regex, '', $file);
		}
		if (!$tempid) 
		{
			$tempid = JRequest::getInt( 'tempid', 0, 'get' );
		}
		
		$useid = $id ? $id : $tempid;
		$prefix = JPATH_ROOT;	
		
		// Use if or alias?
		if ($id) 
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );	
		}
		else 
		{
			$dir = Hubzero_View_Helper_Html::niceidformat( $useid );
		}
		
		// Build the file path
		$webdir = DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
		$path  = $webdir;
		$path .= !$id && $tempid ? DS . 'temp' : '';
		$path .= DS . $dir . DS . 'images';
							
		// Output HTML
		$view 					= new JView( array('name'=>'image'));
		$view->option 			= $this->_option;
		$view->webpath 			= $webdir;
		$view->default_picture 	= $this->_config->get('defaultpic');
		$view->path 			= $path;
		$view->file 			= $file;
		
		$ih = new ProjectsImgHandler();
		$view->thumb 		= file_exists($prefix . $path . DS . $file) ? $ih->createThumbName($file) : '';
		$view->file_path 	= $prefix . $path;
		$view->id 			= $id;
		$view->tempid 		= $tempid;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//----------------------------------------------------------
	// Private Functions
	//----------------------------------------------------------

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
				Hubzero_View_Helper_Html::shortenText($group->get('description'), 50, 0),
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
	
		$document =& JFactory::getDocument();
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
			$group = Hubzero_Group::getInstance($admingroup);
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
	 * Autocomplete
	 * 
	 * @return void
	 */
	protected function _autocomplete() 
	{
		$filters 			= array();
		$filters['limit']  	= 20;
		$filters['start']  	= 0;
		$filters['search'] 	= trim(JRequest::getString( 'value', '' ));
		$which 				= JRequest::getVar('which', '');
		
		if (!$which) {
			return false;
		}
		
		// Fetch results
		$rows = AutocompleteHandler::_getAutocomplete( $filters, $which, $this->database, $this->juser->get('id') );

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0) 
		{
			foreach ($rows as $row) 
			{
				if ($which == 'user') 
				{
					$json[] = '["' . $row->fullname . ' (' . $row->uidNumber . ')","' 
							. $row->fullname . ' (' . $row->uidNumber . '), "]';
				}
				else 
				{
					$json[] = '["' . $row->description . '","' . $row->cn . '"]';
				}
				
			}
		}
		
		echo '[' . implode(',',$json) . ']';
		return;
	}
	
	/**
	 * Wiki preview
	 * 
	 * @return void
	 */	
	protected function _wikiPreview()
	{
		// Incoming
		$raw  = JRequest::getVar( 'raw', '' );
		$ajax = JRequest::getInt( 'ajax', 0 );
		
		if (!$ajax) 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option);			
			return;
		}
		
		// Convert
		if ($raw) 
		{
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			
			//import the wiki parser
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => '',
				'pagename' => 'projects',
				'pageid'   => '',
				'filepath' => '',
				'domain'   => ''
			);
			$html = $p->parse( $raw, $wikiconfig );
			echo $html ? $html : ProjectsHtml::showNoPreviewMessage(JText::_('COM_PROJECTS_PREVIEW_NONE'));
		}
		else 
		{
			echo ProjectsHtml::showNoPreviewMessage(JText::_('COM_PROJECTS_PREVIEW_NONE'));
		}		
	}

	/**
	 * Notify project team
	 * 
	 * @param  int $pid
	 * @param  string $action
	 * @param  int $managers_only
	 * @return void
	 */			
	protected function _notifyTeam($pid = '', $action = '', $managers_only = 0)
	{
		// Is messaging turned on?
		if ($this->_config->get('messaging') != 1)
		{
			return false;
		}
		
		// Which notifications are allowed?
		$actions = array('invite');		
		if (!$pid || !$action || !in_array($action, $actions))
		{
			return false;
		}
		
		// Get project
		$obj 		= new Project( $this->database );
		$objO 		= new ProjectOwner( $this->database );
		$project 	= $obj->getProject($pid, $this->juser->get('id'));
			
		// Set up email config
		$jconfig 		=& JFactory::getConfig();
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
		
		// Email subject
		switch ($action) 
		{
			case 'invite':   
			default:	 
				$subject_active  = JText::_('COM_PROJECTS_EMAIL_SUBJECT_ADDED') . ' ' . $project->alias; 
				$subject_pending = JText::_('COM_PROJECTS_EMAIL_SUBJECT_INVITE') . ' ' . $project->alias; 		  
				break;			
		}
						
		// Message body
		$eview 					= new JView( array('name'=>'emails', 'layout'=> $action ) );
		$eview->option 			= $this->_option;
		$eview->hubShortName 	= $jconfig->getValue('config.sitename');
		$eview->project 		= $project;
		$eview->goto 			= 'alias=' . $project->alias;
		$eview->user 			= $this->juser->get('id');

		// Get profile of author group
		if ($project->owned_by_group) 
		{
			$eview->nativegroup = Hubzero_Group::getInstance( $project->owned_by_group );
		}
		
		// Send out message/email
		foreach ($team as $member) 
		{
			$eview->role = $member->role;
			if ($member->userid && $member->userid != $this->juser->get('id') ) 
			{
				$eview->uid = $member->userid;	
				$message 	= $eview->loadTemplate();
				$message 	= str_replace("\n", "\r\n", $message);	
				
				// Creator
				if ($member->userid == $project->created_by_user && $action == 'invite')
				{
					$subject_active  = JText::_('COM_PROJECTS_EMAIL_SUBJECT_CREATOR_CREATED') 
					. ' ' . $project->alias . '!'; 
				}

				// Send HUB message
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
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
				$message 		= $eview->loadTemplate();
				$message 		= str_replace("\n", "\r\n", $message);
				ProjectsHtml::email($member->invited_email, $jconfig->getValue('config.sitename') 
					. ': ' . $subject_pending, $message, $from);
			}
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
	public function showCount ( $pid = 0, $what = '', $authorized = 0, $ajax = 1, $uid = 0 ) 
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
		
		$db =& JFactory::getDBO();
		$project = new Project( $db );
		if (!$project->load( $pid ))
		{
			return false;
		}
		// Get plugin
		JPluginHelper::importPlugin( 'projects', $what);
		$dispatcher =& JDispatcher::getInstance();
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
		$enabled = $this->_config->get('logging', 0);		
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
		
		// Log activity
		$objLog  				= new ProjectLog( $this->database );
		$objLog->projectid 		= $pid;
		$objLog->userid 		= $this->juser->get('id');
		$objLog->owner 			= intval($owner);
		$objLog->ip 			= Hubzero_Environment::ipAddress();
		$objLog->section 		= $section;
		$objLog->layout 		= $layout ? $layout : $this->_task;
		$objLog->action 		= $action ? $action : 'view';
		$objLog->time 			= date('Y-m-d H:i:s');
		$objLog->request_uri 	= $_SERVER['REQUEST_URI'];
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
		$limit = $this->_config->get('sidebox_limit', 3);
		$modules = '';
		
		// Show side module with suggestions
		if (count($suggestions) > 1 && $project->num_visits < 10) 
		{ 				
			$view = new JView(
				array(
					'name' => 'modules',
					'layout' => 'suggestions'
				)
			);
			$view->option = $option;
			$view->suggestions = $suggestions;
			$view->project = $project;
			$modules .= $view->loadTemplate();
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
		$view->option = $option;
		$view->items = $todos;
		$view->project = $project;
		$modules .= $view->loadTemplate();
		
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
				JPlugin::loadLanguage( 'plg_projects_publications' );	
			}
					
			// Publications side module
			$view = new JView(
				array(
					'name' => 'modules',
					'layout' => 'publications'
				)
			);
			$view->option = $option;
			$view->items = $pubs;
			$view->project = $project;
			$modules .= $view->loadTemplate();
		}	
		
		// Get notes
		$projectsHelper = new ProjectsHelper( $this->database );
		$masterscope = 'projects' . DS . $project->alias . DS . 'notes';
		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		$group = $group_prefix . $project->alias;
		$notes = $projectsHelper->getNotes($group, $masterscope, $limit, 'RAND()');
		
		// To-do side module
		$view = new JView(
			array(
				'name' => 'modules',
				'layout' => 'notes'
			)
		);
		$view->option = $option;
		$view->items = $notes;
		$view->project = $project;
		$modules .= $view->loadTemplate();	
		
		return $modules;
	}
}
