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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Manage projects
 */
class ProjectsControllerProjects extends Hubzero_Controller
{
	/**
	 * Executes a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->_config = $config;
				
		// Publishing enabled?
		$this->_publishing = 
			is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php')
			&& JPluginHelper::isEnabled('projects', 'publications')
			? 1 : 0;
		
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
				.'com_publications' . DS . 'tables' . DS . DS.'master.type.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . DS.'screenshot.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . DS.'attachment.php');
			require_once( JPATH_ROOT . DS . 'components'.DS
				. 'com_publications' . DS . 'helpers' . DS . 'helper.php');	
		}
		
		$this->_task = strtolower(JRequest::getVar('task', '','request'));
		parent::execute();
	}

	/**
	 * Lists projects
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'projects') );
		$view->option 	= $this->_option;
		$view->task 	= $this->_task;
		$view->config 	= $this->_config;
		
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet(DS .'components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'projects.css');
			
		// Get filters
		$view->filters = array();
		$view->filters['search'] 		= urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		$view->filters['search_field'] 	= urldecode($app->getUserStateFromRequest($this->_option.'.search_field', 'search_field', 'title'));
		$view->filters['sortby']  		= trim($app->getUserStateFromRequest($this->_option.'.sort', 'filter_order', 'id'));
		$view->filters['sortdir'] 		= trim($app->getUserStateFromRequest($this->_option.'.sortdir', 'filter_order_Dir', 'DESC'));
		$view->filters['authorized'] 	= true;
		$view->filters['getowner'] 		= 1;
		$view->filters['activity'] 		= 1;
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new Project( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters, true, 0, 1 );
		
		// Get records
		$view->rows = $obj->getRecords( $view->filters, true, 0, 1 );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
		
		// Set any errors
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		
		// Check that master path is there
		if ($this->_config->get('offroot') && !is_dir($this->_config->get('webpath')))
		{			
			$view->setError( JText::_('Master directory does not exist. Administrator must fix this! ')  . $this->_config->get('webpath') );	
		}		
		
		// Output the HTML
		$view->display();
	}
	
	/**
	 * Edit project info
	 * 
	 * @return     void 
	 */
	public function editTask()
	{
		// Incoming project ID
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array( $id )) 
		{
			$id = $id[0];
		}
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet(DS . 'components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'projects.css');
		$document->addStyleSheet(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
		$document->addScript(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');		
						
		// Do we need to incule extra scripts?
		$plugin 		= JPluginHelper::getPlugin( 'system', 'jquery' );
		$p_params 		= $plugin ? new JParameter($plugin->params) : NULL;
		
		if (!$plugin || !$p_params->get('activateAdmin'))
		{
			$document->addScript(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'files.js');
		}
		else
		{
			$document->addScript(DS . 'plugins' . DS . 'projects' . DS . 'files' . DS . 'files.jquery.js');
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->_config;
		
		$obj = new Project( $this->database );
		$objAC = new ProjectActivity( $this->database );
				
		if ($id) 
		{
			if (!$obj->loadProject($id)) 
			{
				$this->_message = JText::_('COM_PROJECTS_NOTICE_ID_NOT_FOUND');
				$this->_redirect = 'index.php?option='.$this->_option;
				return;
			}
		}
		if (!$id) 
		{
			$this->_message = JText::_('COM_PROJECTS_NOTICE_NEW_PROJECT_FRONT_END');
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Get project types
		$objT = new ProjectType( $this->database );
		$view->types = $objT->getTypes();
				
		// Get plugin
		JPluginHelper::importPlugin( 'projects');
		$dispatcher =& JDispatcher::getInstance();
		
		// Get activity counts
		$dispatcher->trigger( 'onProjectCount', array( $obj, &$counts, 1) );
		$counts['activity'] = $objAC->getActivityCount( $obj->id, $this->juser->get('id'));
		$view->counts = $counts;
		
		// Get team
		$objO = new ProjectOwner( $this->database );
		
		// Sync with system group
		$objO->sysGroup($obj->alias, $this->_config->get('group_prefix', 'pr-'));
		
		// Get members and managers
		$view->managers = $objO->getOwnerNames($id, 0, '1', 1);	
		$view->members = $objO->getOwnerNames($id, 0, '0', 1);
		$view->authors = $objO->getOwnerNames($id, 0, '2', 1);
			
		// Get last activity
		$afilters = array('limit' => 1);
		$last_activity = $objAC->getActivities ($id, $afilters);	
		$view->last_activity = count($last_activity) > 0 ? $last_activity[0] : '';
		
		// Was project suspended?
		$view->suspended = false;
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		if ($obj->state == 0 && $obj->setup_stage >= $setup_complete) 
		{
			$view->suspended = $objAC->checkActivity( $id, JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}
		
		// Get project params
		$view->params = new JParameter( $obj->params );
		
		// Get Disk Usage
		JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher =& JDispatcher::getInstance();
		$project = $obj->getProject($id, $this->juser->get('id'));	
		$content = $dispatcher->trigger( 'diskspace', array( $this->_option, $project, 
			'files', 'admin', '', $this->_config, NULL));
		$view->diskusage = isset($content[0])  ? $content[0]: '';
				
		// Set any errors
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		
		// Get tags on this item
		$tagsHelper = new ProjectTags( $this->database);
		$view->tags = $tagsHelper->get_tag_string($id, 0, 0, NULL, 0, 1);
		
		// Output the HTML
		$view->obj = $obj;
		$view->publishing	= $this->_publishing;
		$view->display();	
	}
	
	/**
	 * Saves a project
	 * Redirects to main listing
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Config
		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;
		
		// Get some needed classes
		$objAA = new ProjectActivity ( $this->database );
		
		// Incoming
		$formdata = $_POST;
		$id = JRequest::getVar( 'id', 0 );
		$action = JRequest::getVar( 'admin_action', '' );
		$message = rtrim(Hubzero_Filter::cleanXss(JRequest::getVar( 'message', '' )));
		
		// Initiate extended database class
		$obj = new Project( $this->database );
		if (!$id or !$obj->loadProject($id)) 
		{
			$this->setError( JText::_('COM_PROJECTS_NOTICE_ID_NOT_FOUND') );
			return false;
		}
		
		$obj->title = $formdata['title'] ? rtrim($formdata['title']) : $obj->title;
		$obj->about = rtrim(Hubzero_Filter::cleanXss($formdata['about']));
		$obj->type 	= isset($formdata['type']) ? $formdata['type'] : 1;
		$obj->modified = date( 'Y-m-d H:i:s' );
		$obj->modified_by = $this->juser->get('id');
		$obj->private = JRequest::getVar( 'private', 0 );
		
		// Was project suspended?
		$suspended = false;
		if ($obj->state == 0 && $obj->setup_stage >= $setup_complete) 
		{
			$suspended = $objAA->checkActivity( $id, JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'));
		}
		
		// Email config
		$jconfig 		=& JFactory::getConfig();
		$from 			= array();
		$from['name']  	= $jconfig->getValue('config.sitename').' '.JText::_('COM_PROJECTS');
		$from['email'] 	= $jconfig->getValue('config.mailfrom');
		$subject 		= JText::_('COM_PROJECTS_PROJECT').' "'.$obj->alias.'" ';
		$sendmail 		= 0;
		$project 		= $obj->getProject($id, $this->juser->get('id'));
		
		// Get project managers
		$objO = new ProjectOwner( $this->database );
		$managers = $objO->getIds( $id, 1, 1 );

		// Admin actions
		if ($action) 
		{
			switch ($action) 
			{
				case 'delete':   	 
					$obj->state = 2;  
				 	$what = JText::_('COM_PROJECTS_ACTIVITY_PROJECT_DELETED');
					$subject .= JText::_('COM_PROJECTS_MSG_ADMIN_DELETED');
				break;
				
				case 'suspend':      
					$obj->state = 0;   
					$what = JText::_('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED');   
					$subject .= JText::_('COM_PROJECTS_MSG_ADMIN_SUSPENDED');  
				break;
				
				case 'reinstate':    
					$obj->state = 1; 
					$what = $suspended 
						? JText::_('COM_PROJECTS_ACTIVITY_PROJECT_REINSTATED') 
						: JText::_('COM_PROJECTS_ACTIVITY_PROJECT_ACTIVATED');  
					$subject .= $suspended 
						? JText::_('COM_PROJECTS_MSG_ADMIN_REINSTATED') 
						: JText::_('COM_PROJECTS_MSG_ADMIN_ACTIVATED') ;     
				break;
			}
			
			// Add activity
			$objAA->recordActivity( $obj->id, $this->juser->get('id'), $what, 0, '', '', 'project', 0, $admin = 1 );
			$sendmail = 1;
		}
		else if($message) 
		{
			$subject .= ' - '.JText::_('COM_PROJECTS_MSG_ADMIN_NEW_MESSAGE');
			$sendmail = 1;  
		}
		
		// Save changes
		if (!$obj->store()) 
		{
			$this->setError( $obj->getError() );
			return false;
		}
		
		// Incoming tags
		$tags = JRequest::getVar('tags', '', 'post');

		// Save the tags
		$rt = new ProjectTags($this->database);
		$rt->tag_object($this->juser->get('id'), $obj->id, $tags, 1, 1);
		
		// Save params
		$incoming   = JRequest::getVar( 'params', array() );
		if (!empty($incoming)) 
		{
			foreach($incoming as $key=>$value) 
			{
				if ($key == 'quota') 
				{
					// convert GB to bytes
					$value = ProjectsHtml::convertSize( floatval($value), 'GB', 'b');
				}				
				$obj->saveParam($id, $key, htmlentities($value));
			}
		}
		
		// Send message
		if ($this->_config->get('messaging', 0) && $sendmail && count($managers) > 0) 
		{
			// Get message body
			$eview = new JView( array('name'=>'emails' ) );
			$eview->option = $this->_option;
			$eview->subject = $subject;
			$eview->action = $action;
			$eview->hubShortName = $jconfig->getValue('config.sitename');
			$eview->project = $project;
			$livesite = $jconfig->getValue('config.live_site');
			$eview->url = $livesite.DS.'projects' . DS . $project->alias;
			$eview->params = new JParameter( $obj->params );
			$eview->config = $this->_config;
			$body = $eview->loadTemplate();
			if ($message) 
			{
				$body.=  JText::_('COM_PROJECTS_MSG_MESSAGE_FROM_ADMIN').': '."\n".$message;
			}	
			$body = str_replace("\n\n", "\n", $body);		
		
			// Send HUB message
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger( 'onSendMessage', array( 'projects_admin_message', $subject, $body, $from, $managers, $this->_option ));
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id='.$id;
		$this->_message = JText::_('Item successfully saved');
	}
	
	/**
	 * Redirects
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option;
		return;
	}
	
	/**
	 * Erases all project information (to be used for test projects only)
	 * 
	 * @return     void
	 */
	public function eraseTask() 
	{
		$id = JRequest::getVar( 'id', 0 );
		$permanent = 1;
		jimport('joomla.filesystem.folder');
		
		// Initiate extended database class
		$obj = new Project( $this->database );
		if (!$id or !$obj->loadProject($id)) 
		{
			$this->setError( JText::_('COM_PROJECTS_NOTICE_ID_NOT_FOUND') );
			return false;
		}
		
		// Load project
		$obj->load($id);
		
		// Get project group
		$group_prefix = $this->_config->get('group_prefix', 'pr-');
		$prgroup = $group_prefix.$obj->alias;
			
		// Store project info
		$alias = $obj->alias;
		$identifier = $this->_config->get('use_alias', 0) ? $alias : $id;
		
		// Delete project
		$obj->delete();
		
		// Erase all owners
		$objO = new ProjectOwner ($this->database );
		$objO->removeOwners ( $id, '', 0, $permanent, '', $all = 1 );
		
		// Erase owner group
		ximport('Hubzero_Group');
		$group = new Hubzero_Group();
		$group->read( $prgroup );
		if ($group) 
		{
			$group->delete();	
		}		
				
		// Erase all comments
		$objC = new ProjectComment ($this->database );
		$objC->deleteProjectComments ( $id, $permanent );
		
		// Erase all activities
		$objA = new ProjectActivity( $this->database );
		$objA->deleteActivities( $id, $permanent );
		
		// Erase all todos
		$objTD = new ProjectTodo( $this->database );
		$objTD->deleteTodos( $id, '', $permanent );
		
		// Erase all blog entries
		$objB = new ProjectMicroblog( $this->database );
		$objB->deletePosts( $id, $permanent );
		
		// Erase all notes
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'attachment.php');
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'author.php');
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'comment.php');
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'log.php');
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
		include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php');
		
		if (is_file(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'config.php')) 
		{
			include_once(JPATH_ROOT.DS.'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'config.php');
		}
		$masterscope = 'projects' . DS . $alias . DS . 'notes';
		
		// Get all notes
		$this->database->setQuery( "SELECT DISTINCT p.id FROM #__wiki_page AS p 
			WHERE p.group_cn='".$prgroup."' AND p.scope LIKE '".$masterscope."%' " );
		$notes = $this->database->loadObjectList();
		
		if($notes) 
		{
			foreach($notes as $note) 
			{
				$page = new WikiPage( $this->database );
						
				// Delete the page's history, tags, comments, etc.
				$page->deleteBits( $note->id );

				// Finally, delete the page itself
				$page->delete( $note->id );
			}
		}
					
		// Erase all files, remove files repository
		JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'eraseRepo', array($identifier) );
		
		// Delete base dir for .git repos
		$dir = $this->_config->get('use_alias', 1) ? $alias : Hubzero_View_Helper_Html::niceidformat( $id );
		$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;		
		
		$repodir = $this->_config->get('webpath');
		if (substr($repodir, 0, 1) != DS) 
		{
			$repodir = DS.$repodir;
		}
		if (substr($repodir, -1, 1) == DS) 
		{
			$repodir = substr($repodir, 0, (strlen($repodir) - 1));
		}
		$path = $prefix.$repodir.DS.$dir;
		if (is_dir($path)) 
		{
			JFolder::delete( $path);			
		}
		
		// Delete images/preview directories
		$webdir = $this->_config->get('imagepath', '/site/projects');
		if (substr($webdir, 0, 1) != DS) 
		{
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS) 
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$webpath = JPATH_ROOT.$webdir.DS.$dir;
		if (is_dir($webpath)) 
		{
			JFolder::delete( $webpath);
		}		
		
		// Erase all publications
		if ($this->_publishing)
		{
			// TBD
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('COM_PROJECTS_PROJECT').' #'.$id.' ('.$alias.') '.JText::_('COM_PROJECTS_PROJECT_ERASED');		
	}
}
