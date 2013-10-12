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

jimport( 'joomla.plugin.plugin' );

/**
 * Project publications
 */
class plgProjectsPublications extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgProjectsPublications(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'projects', 'publications' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Load component configs
		$this->_config =& JComponentHelper::getParams( 'com_projects' );	
		
		// Load publications component configs
		$this->_pubconfig =& JComponentHelper::getParams( 'com_publications' );					
			
		// Areas required for publication
		$this->_required = ProjectsHelper::getParamArray(
			$this->_params->get('required_areas', 'content, description, authors, license' ));

		// Areas that can be updated after publication
		$this->_updateAllowed = ProjectsHelper::getParamArray(
			$this->_params->get('updatable_areas', '' ));
		
		// Common extensions (for gallery)
		$this->_image_ext = ProjectsHelper::getParamArray(
			$this->_params->get('image_types', 'bmp, jpeg, jpg, png' ));			
		$this->_video_ext = ProjectsHelper::getParamArray(
			$this->_params->get('video_types', 'avi, mpeg, mov, wmv' ));	
			
		// Process steps
		$this->_section = '';
		$this->_layout = '';
					
		// Output collectors
		$this->_referer = '';
		$this->_message = array();
	}
	
	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @return     array   Plugin name and title
	 */
	public function &onProjectAreas() 
	{
		$area = array();
		
		if (JPluginHelper::isEnabled('projects', 'publications') 
			&& is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_publications' . DS . 'tables' . DS . 'publication.php')) 
		{
			$area = array(
				'name' => 'publications',
				'title' => JText::_('COM_PROJECTS_TAB_PUBLICATIONS')
			);
		}
		
		return $area;
	}
	
	/**
	 * Event call to return count of items
	 * 
	 * @param      object  $project 		Project
	 * @param      integer &$counts 		
	 * @return     array   integer
	 */
	public function &onProjectCount( $project, &$counts ) 
	{
		// Get this area details
		$this->_area = $this->onProjectAreas();
		
		$counts['publications'] = 0;
		
		if (empty($this->_area) || !$project) 
		{
			return $counts;
		}
		else
		{
			$database =& JFactory::getDBO();
			
			// Instantiate project publication
			$objP = new Publication( $database );
			
			$filters = array();
			$filters['project']  		= $project->id;
			$filters['ignore_access']   = 1;
			$filters['dev']   	 		= 1;
			
			$counts['publications'] = count($objP->getCount($filters));
		}
		
		return $counts;
	}
	
	/**
	 * Event call to return data for a specific project
	 * 
	 * @param      object  $project 		Project
	 * @param      string  $option 			Component name
	 * @param      integer $authorized 		Authorization
	 * @param      integer $uid 			User ID
	 * @param      integer $msg 			Message
	 * @param      integer $error 			Error
	 * @param      string  $action			Plugin task
	 * @param      string  $areas  			Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onProject ( $project, $option, $authorized, 
		$uid, $msg = '', $error = '', $action = '', $areas = null )
	{
		$returnhtml = true;
	
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'message'=>'',
			'error'=>''
		);
		
		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (empty($this->_area) || !in_array($this->_area['name'], $areas)) 
			{
				return;
			}
		}
		
		// Is the user logged in?
		if ( !$authorized && !$project->owner ) 
		{
			return $arr;
		}
		
		// Load language file
		$this->loadLanguage();
		
		$database =& JFactory::getDBO();
		
		// Enable views
		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');
				
		// Get JS & CSS
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginScript('projects', 'publications');
		Hubzero_Document::addPluginStylesheet('projects', 'publications');
				
		// Import publication helpers
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php' );
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php' );
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'html.php' );
				
		// Get task									
		$this->_task = JRequest::getVar('action','');
		$this->_pid = JRequest::getInt('pid', 0);
		if (!$this->_task) 
		{
			$this->_task = $this->_pid ? 'publication' : $action;
		}
		
		$this->_uid = $uid;
		if (!$this->_uid) 
		{
			$juser =& JFactory::getUser();
			$this->_uid = $juser->get('id');
		}
		$this->_database = $database;
		
		// Contribute process outside of projects
		if (!is_object($project) or !$project->id) 
		{			
			$ajax_tasks = array('showoptions', 'showinfo', 'save', 'showitem');
			$this->_task = $action == 'start' ? 'start' : 'contribute';
			if ($action == 'new') 
			{
				$this->_task = 'new';
			}
			elseif (in_array($action, $ajax_tasks))
			{
				$this->_task = $action;
			}
		}
		elseif ($project->provisioned == 1 && !$this->_pid)
		{
			// No browsing within provisioned project
			$this->_task = $action == 'browse' ? 'contribute' : $action;
		}	
			
		$this->_project 	= $project;
		$this->_action 		= $action;
		$this->_option 		= $option;
		$this->_authorized 	= $authorized;
		$this->_msg 		= $msg;
		if ($error) 
		{
			$this->setError( $error );	
		}

		// In case of read-only access
		if ($authorized == 3 && $this->_pid)
		{			
			$valid_tasks = array('contribute', 'browse', 'review', 'versions');
			if (!in_array($this->_task, $valid_tasks)) 
			{
				$this->_task = 'review';
			}
		}
					
		// Actions				
		switch ($this->_task) 
		{
			case 'browse':
			default: 			
				$arr['html'] = $this->browse(); 		
				break;
			
			case 'start': 		
				$arr['html'] = $this->start(); 			
				break;
			
			case 'new': 		
				$arr['html'] = $this->add(); 			
				break;
									
			case 'save': 		
				$arr['html'] = $this->save(); 			
				break;
							
			case 'edit': 
			case 'publication': 
				$arr['html'] = $this->edit(); 			
				break;
			
			case 'newversion': 
			case 'savenew':	
				$arr['html'] = $this->_newVersion(); 			
				break;
				
			case 'suggest_license':
			case 'save_license': 
				$arr['html'] = $this->_suggestLicense(); 			
				break;
			
			case 'versions': 	
				$arr['html'] = $this->versions(); 		
				break;	
			
			// Review
			case 'review': 	
				$arr['html'] = $this->review(); 		
				break;	
			
			// Content
			case 'showoptions': 		
				$arr['html'] = $this->_showOptions(); 	
				break;
			case 'showinfo': 		
				$arr['html'] = $this->_showContentInfo(); 	
				break;
			case 'edititem': 		
				$arr['html'] = $this->_editContent(); 	
				break;
			case 'saveitem': 		
				$arr['html'] = $this->_saveContent(); 	
				break;
			case 'showitem': 		
				$arr['html'] = $this->_loadContentItem(); 	
				break;
				
			// Description
			case 'wikipreview':
				$arr['html'] = $this->_previewWiki(); 		
				break;				
			
			// Authors
			case 'showauthor': 		
				$arr['html'] = $this->_showAuthor(); 		
				break;
			case 'editauthor': 		
				$arr['html'] = $this->_editAuthor(); 		
				break;
			case 'saveauthor': 		
				$arr['html'] = $this->_saveAuthor(); 		
				break;
			
			// Audience
			case 'showaudience': 	
				$arr['html'] = $this->_showAudience(); 	
				break;
			
			// Gallery
			case 'showimage': 		
				$arr['html'] = $this->_loadScreenshot(); 	
				break;			
			case 'editimage': 		
				$arr['html'] = $this->_editScreenshot(); 	
				break;
			case 'saveimage': 		
				$arr['html'] = $this->_saveScreenshot(); 	
				break;
			
			// Tags
			case 'loadtags': 		
				$arr['html'] = $this->suggestTags(); 		
				break;
			
			// Change publication state				
			case 'publish': 
			case 'republish': 
			case 'archive':
			case 'revert': 
			case 'post': 
				$arr['html'] = $this->_publish(); 	
				break;
			
			case 'cancel': 	
				$arr['html'] = $this->_unpublish(); 	
				break;	
				
			// Contribute process outside of projects
			case 'contribute': 		
				$arr['html'] = $this->contribute(); 		
				break;
				
			// Show stats
			case 'stats': 		
				$arr['html'] = $this->_stats(); 		
				break;
				
			case 'diskspace':
				$arr['html'] = $this->pubDiskSpace($option, $project, $this->_task, $this->_config); 
				break;										
		}			
	
		$arr['referer'] = $this->_referer;
		$arr['msg'] = $this->_message;
				
		// Return data
		return $arr;

	}
	
	/**
	 * Browse publications
	 * 
	 * @return     string
	 */
	public function browse() 
	{
		// Build query
		$filters = array();
		$filters['limit'] 	 		= JRequest::getInt('limit', 25);
		$filters['start'] 	 		= JRequest::getInt('limitstart', 0);
		$filters['sortby']   		= JRequest::getVar( 't_sortby', 'title');
		$filters['sortdir']  		= JRequest::getVar( 't_sortdir', 'ASC');
		$filters['project']  		= $this->_project->id;
		$filters['ignore_access']   = 1;
		$filters['dev']   	 		= 1; // get dev versions
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'browse'
			)
		);
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		
		// Get all publications
		$view->rows = $objP->getRecords($filters);

		// Get total count
		$results = $objP->getCount($filters);
		$view->total = ($results && is_array($results)) ? count($results) : 0;

		// Areas required for publication
		$view->required = array('content', 'description', 'license', 'authors');
		
		// Get master publication types
		$mt = new PublicationMasterType( $this->_database );
		$choices = $mt->getTypes('alias', 1);
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
		$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');		
				
		// Get used space
		$helper 	   = new PublicationHelper($this->_database);
		$view->dirsize = $helper->getDiskUsage($this->_project->id, $view->rows);
		$view->params  = new JParameter( $this->_project->params );
		$view->quota   = $view->params->get('pubQuota') 
						? $view->params->get('pubQuota') 
						: ProjectsHtml::convertSize(floatval($this->_config->get('pubQuota', '1')), 'GB', 'b');	
		
		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->filters 		= $filters;
		$view->config 		= $this->_config;	
		$view->choices 		= $choices;
		$view->title		= $this->_area['title'];
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 * Get supported master types applicable to individual project
	 * 
	 * @return     string
	 */
	private function _getAllowedTypes($tChoices) 
	{
		$choices = array();
		
		if (is_object($this->_project) && $this->_project->id && !empty($tChoices))
		{
			foreach ($tChoices as $choice)
			{
				$pluginName = is_object($choice) ? $choice->alias : $choice;

				// We need a plugin
				if (!JPluginHelper::isEnabled('projects', $pluginName))
				{
					continue;
				}
				
				$plugin = JPluginHelper::getPlugin('projects', $pluginName);
				$params = new JParameter($plugin->params);
				
				// Get restrictions from plugin params 
				$projects = $params->get('restricted') ? ProjectsHelper::getParamArray($params->get('restricted')) : array();
				
				if (!empty($projects))
				{
					if (!in_array($this->_project->alias, $projects))
					{
						continue;
					}
				}

				$choices[] = $choice;
			}
		}
		return $choices;
	}
	
	/**
	 * Start a publication
	 * 
	 * @return     string
	 */
	public function start() 
	{		
		// Get master publication types
		$mt = new PublicationMasterType( $this->_database );
		$choices = $mt->getTypes('*', 1, 0, 'ordering', $this->_config);
		
		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id) 
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
			
			// Send to file picker
			return $this->add();
		}
		
		// Check that choices apply to a particular project
		$choices = $this->_getAllowedTypes($choices);

		// Do we have a choice?
		if (count($choices) <= 1 ) 
		{
			// Send to file picker
			return $this->add();
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'start',
			)
		);
				
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route);
		
		// Append breadcrumbs
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(
			stripslashes(JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')),
			$view->url . '?action=start'	
		);
		
		// Output HTML
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->config 		= $this->_config;
		$view->choices 		= $choices;
		$view->title		= $this->_area['title'];
				
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 * First screen in publication process, adding content
	 * 
	 * @return     string
	 */
	public function add() 
	{
		// Incoming
		$base = JRequest::getVar('base', 'files');
			
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'edit',
				'layout'=>'primarycontent'
			)
		);
					
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		$view->pub = $objP;
		
		// Instantiate publication version
		$objPV = new PublicationVersion( $this->_database );
		$view->row = $objPV;
		$view->version = 'dev';
		$view->move = 1;
		
		// Get master publication types
		$mt = new PublicationMasterType( $this->_database );
		$view->choices = $mt->getTypes('alias', 1);
		
		// Check that choices apply to a particular project
		$view->choices = $this->_getAllowedTypes($view->choices);
		
		if (!in_array($base, $view->choices)) 
		{
			$base = 'files'; // default to files
		}
		
		// Get Files JS
		Hubzero_Document::addPluginScript('projects', 'files');
		Hubzero_Document::addPluginStylesheet('projects', 'files');
		
		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id) 
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
		}
		
		if ($this->_project->provisioned)
		{
			$base = 'files'; // default to files
		}
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . '?action=start');
		
		// Append breadcrumbs
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(
			stripslashes(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_NEWPUB')),
			$view->url
		);
		
		$this->_base = $base;
		
		// Get attached files
		$view->attachments = array();
		
		// Get active panels
		$this->_getPanels();
		
		// Available sections in order
		$view->panels 		= $this->_panels;
		$view->lastpane 	= 'content';
		$view->last_idx 	= 0;
		$view->current_idx 	= 0;
		
		// Checked areas
		$view->checked = array();
		foreach ($view->panels as $key => $value) 
		{
			$view->checked[$value] = 0;
		}
		
		// Output HTML
		$view->params 		= new JParameter( $this->_project->params );
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->base 		= $base;
		$view->active 		= 'content';
		$view->config 		= $this->_config;	
		$view->pubparams 	= $this->_params;
		$view->inreview 	= 0;
		$view->title		= $this->_area['title'];
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 *  Publication stats
	 * 
	 * @return     string
	 */
	protected function _stats() 
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$version 	= JRequest::getVar( 'version', '' );	
		
		// Load publication & version classes
		$objP = new Publication( $this->_database );
		$row  = new PublicationVersion( $this->_database );
		
		// Check that version exists
		$version = $row->checkVersion($pid, $version) ? $version : 'default';
		
		// Add stylesheet
		$document =& JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'css' . DS . 'impact.css');
		
		// Is logging enabled?
		if ( is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_publications' . DS . 'tables' . DS . 'logs.php'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_publications' . DS . 'tables' . DS . 'logs.php');
		}
		else
		{
			$this->setError('Publication logs not present on this hub, cannot generate stats');
			return false;
		}
				
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'stats'
			)
		);
		
		// Start url
		$route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
					
		// Get pub stats for each publication		
		$pubLog = new PublicationLog($this->_database);
		$view->pubstats = $pubLog->getPubStats($this->_project->id, $pid);

		// Get date of first log
		$view->firstlog = $pubLog->getFirstLogDate();
		
		// Test
		$view->totals = $pubLog->getTotals($this->_project->id, 'project');
		
		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->pub			= $objP->getPublication($pid, $version, $this->_project->id);		
		$view->task 		= $this->_task;
		$view->config 		= $this->_config;	
		$view->pubconfig 	= $this->_pubconfig;
		$view->version 		= $version;
		$view->route 		= $route;
		$view->url 			= $pid ? JRoute::_($view->route . a . 'pid=' . $pid) : JRoute::_($view->route);
		$view->title		= $this->_area['title'];
		$view->helper		= new PublicationHelper($this->_database);

		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 * Edit a publication
	 * 
	 * @return     string
	 */
	public function edit() 
	{
		// Incoming
		$move 		= JRequest::getInt( 'move', 0 ); 
		$section  	= JRequest::getVar( 'section', 'version' );
		$toolid 	= JRequest::getVar( 'toolid', 0 );
		$pid 		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$version 	= JRequest::getVar( 'version', '' );	
		$step 		= JRequest::getVar( 'step', '' );	
		$base 		= JRequest::getVar( 'base', 'files' );
		$primary 	= JRequest::getVar( 'primary', 1 ); 
		$layout 	= $section;
		$inreview 	= JRequest::getInt( 'review', 0 );
		
		// Load publication & version classes
		$objP = new Publication( $this->_database );
		$row  = new PublicationVersion( $this->_database );
		
		// Check that version exists
		$version = $row->checkVersion($pid, $version) ? $version : 'default';
		
		// Instantiate project publication
		$pub = $objP->getPublication($pid, $version, $this->_project->id);

		// If publication not found, raise error
		if (!$pub) 
		{
			$this->_referer = JRoute::_($route);
			$this->_message = array(
				'message' => JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'),
				'type' => 'error');
			return;
		}

		// Master type
		$this->_base = $pub->base;
		
		// Get active panels
		$this->_getPanels();
		
		// Available sections in order
		if (!in_array($section, $this->_panels) && $section != 'version') 
		{
			$layout = 'version';
			$section = 'version';
		}
		
		// Get master publication types
		$mt = new PublicationMasterType( $this->_database );
		$choices = $mt->getTypes('alias', 1);
		
		// Check that choices apply to a particular project
		$choices = $this->_getAllowedTypes($choices);
		
		// Default primary content
		if (!in_array($base, $choices)) 
		{
			$base = 'files';
		}
		
		// New version?
		if ($this->_task == 'newversion') 
		{
			$section = 'content';
		}
		
		// Start url
		$route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';		
		
		// Which content panel?
		if ($section == 'content') 
		{
			if ($step) 
			{
				$layout = $step == 'supportingdocs' ? 'supportingdocs' : 'primarycontent';
			}
			else 
			{
				$layout = $primary ? 'primarycontent' : 'supportingdocs';
			}
			
			// Get choice of content type for supporting items
			if ($layout == 'supportingdocs')
			{
				$sChoices = $mt->getTypes('alias', 0, 1);
				
				// Check that choices apply to a particular project
				$choices = $this->_getAllowedTypes($sChoices);				
			}
		}
		// Which description panel?
		if ($section == 'description' && $this->_pubconfig->get('show_metadata', 0)) 
		{
			$layout = $step == 'metadata' ? 'metadata' : 'description';
		}
		
		if ($section == 'content' || $section == 'gallery')
		{
			Hubzero_Document::addPluginScript('projects', 'files');
		}
		
		// Base of primary content corresponds to master type!
		if ($section == 'content' && $primary && $step != 'supportingdocs')
		{
			$base = $pub->base;
		}
		
		// Main version
		if ($pub->main == 1) 
		{
			$version = 'default';
		}
		// We have a draft
		if ($pub->state == 3) 
		{
			$version = 'dev';
		}
		// Unpublished version, can't view sections
		if ($pub->state == 0) 
		{
			$section = 'version';
			$layout  = 'version';
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'edit',
				'layout'=>$layout
			)
		);
		$view->panels = $this->_panels;	
		$view->pub = $pub;
		$view->route = $route;
		
		// Build pub url
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
		
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $pub->version_id, $pub->id);
		$view->helper = $helper;
				
		// Instantiate publication version
		$row->loadVersion($pid, $version);
			
		// Append breadcrumbs
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$url = $version != 'default' ? $view->url.a.'version='.$version : $view->url;
		$pathway->addItem(
			stripslashes($row->title),
			$view->url	
		);
		
		// Get extra tool information
		if ($view->pub->base == 'tools' && $view->pub->toolid) 
		{
			$tool = array();
			$hzt = Hubzero_Tool::getInstance($view->pub->toolid);
			$tool['tool'] = $hzt;
			$tool['dev'] = $hzt->getRevision('dev');
			$tool['current'] = $hzt->getRevision('current');
		}
		
		// Autocompleter
		if ($section == 'authors' || $section == 'access') 
		{
			// Do we need to incule extra scripts?
			$plugin 		= JPluginHelper::getPlugin( 'system', 'jquery' );
			$p_params 		= $plugin ? new JParameter($plugin->params) : NULL;
			
			if (!$plugin || $p_params->get('noconflictSite'))
			{
				$document =& JFactory::getDocument();
				$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'observer.js');
				$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'textboxlist.js');
				$document->addScript('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.js');
				$document->addStyleSheet('plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.css');
			}
		}
				
		// Get extra info specific to each panel
		switch ($section) 
		{
			case 'version':
				// Get authors
				$pa = new PublicationAuthor( $this->_database );
				$view->authors = $pa->getAuthors($row->id);
				break;
				
			case 'content':
			    $pContent = new PublicationAttachment( $this->_database );
				$role = $layout == 'primarycontent'  ? '1' : '0';
				$view->attachments = $pContent->getAttachments ( $row->id, $filters = array('role' => $role) );
				$view->base = $pub->base ? $pub->base : 'files';
										
				// Get project file path
				$view->fpath = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), 1);
				$view->prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT;
				
				// Get projects helper
				$view->projectsHelper = new ProjectsHelper( $this->_database );
												
				// Get Files JS
				Hubzero_Document::addPluginScript('projects', 'files');
				Hubzero_Document::addPluginStylesheet('projects', 'files');							
				break;
			
			case 'description':
				// Get custom metadata fields (depending on type)
				$rt = new PublicationCategory( $this->_database );
				$rt->load( $pub->category );
				$view->customFields = $rt->customFields;
				break;
			
			case 'authors':	
				// Get authors
				$pa = new PublicationAuthor( $this->_database );
				$view->authors = $pa->getAuthors($row->id);
				
				// Get team members
				$objO = new ProjectOwner( $this->_database );
				$view->teamids = $objO->getIds( $this->_project->id, 'all', 0, 0 );
				break;
			
			case 'access':						
				// Sys group
				$cn = $this->_config->get('group_prefix', 'pr-').$this->_project->alias;
				$view->sysgroup = new Hubzero_Group();
				if (Hubzero_Group::exists($cn)) 
				{
					$view->sysgroup = Hubzero_Group::getInstance( $cn );
				}
				
				// Is access restricted?
				$paccess = new PublicationAccess( $this->_database );
				$view->access_groups = $paccess->getGroups($row->id, $row->publication_id, $version, $cn);								
				break;
			
			case 'license':
				// Get available licenses
				$objL = new PublicationLicense( $this->_database);
				$apps_only = $pub->master_type == 'tools' ? 1 : 0;
				$view->licenses = $objL->getLicenses( $filters=array('apps_only' => $apps_only));
				
				// If no active licenses are found, give default choice
				if (!$view->licenses)
				{
					$view->licenses = $objL->getDefaultLicense();
				}
				
				// Get selected license
				$view->license = '';
				if ($row->license_type) 
				{
					$view->license = $objL->getPubLicense( $row->id );
				}
				break;
			
			case 'audience':
				// Get audience info
				$ra = new PublicationAudience( $this->_database );
				$view->audience = $ra->getAudience($row->publication_id, $row->id, $getlabels = 1, $numlevels = 4);
				
				// Get audience levels
				$ral = new PublicationAudienceLevel ( $this->_database );
				$view->levels = $ral->getLevels( 4, array(), 0 );
				if (!($view->audience)) 
				{
					$view->audience = new PublicationAudience( $this->_database );
				}
				break;
			
			case 'gallery':
				// Get screenshots
				$pScreenshot = new PublicationScreenshot( $this->_database );
				$view->shots = $pScreenshot->getScreenshots( $row->id );
				
				// Get publications helper
				$helper = new PublicationHelper( $this->_database );
				
				// Get gallery path
				$webpath = $this->_pubconfig->get('webpath');
				$view->gallery_path = $helper->buildPath($row->publication_id, $row->id, $webpath, 'gallery');
				
				// Get project file path
				$view->fpath = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), 1);
				$view->prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT;				
				break;
			
			case 'tags':
				// Get tags
				$tagsHelper = new PublicationTags( $this->_database);
				//$view->tags = $tagsHelper->getTags($row->publication_id);
				
				$tags_men = $tagsHelper->get_tags_on_object($row->publication_id, 0, 0, 0, 0);

				$mytagarray = array();
				foreach ($tags_men as $tag_men) 
				{
					$mytagarray[] = $tag_men['raw_tag'];
				}
				$view->tags = implode(', ', $mytagarray);
				
				// Get types
				$rt = new PublicationCategory( $this->_database );
				$view->categories = $rt->getContribCategories();										
				break;
		}
		
		//Import the wiki parser
		ximport('Hubzero_Wiki_Parser');
		$view->parser =& Hubzero_Wiki_Parser::getInstance();

		$view->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => 'projects',
			'pageid'   => '',
			'filepath' => '',
			'domain'   => ''
		);
				
		// Get type info
		$view->_category = new PublicationCategory( $this->_database );
		$view->_category->load($pub->category);
		$view->_category->_params = new JParameter( $view->_category->params );
		
		// What's the last visited panel
		$view->params 		= new JParameter( $row->params );
		$view->lastpane 	= $view->params->get('stage', 'content');		
		$indexes 			= $this->_getIndex($row, $view->lastpane, $section);
		$view->last_idx 	= $indexes['last_idx'];
		$view->current_idx 	= $indexes['current_idx'];		

		// Checked areas
		$view->checked = $this->_checkDraft( $pub->base, $row, $version );
		
		// Areas required for publication
		$view->required = $this->_required;

		// Areas that can be updated after publication
		$view->mayupdate = $this->_updateAllowed;
		
		// Check if all required area are filled in
		$view->publication_allowed = $this->_checkPublicationPermit($view->checked);
		
		// Master type params (determines management options)
		$mType = $mt->getType($this->_base);
		$view->typeParams = new JParameter( $mType->params );
						
		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->version 		= $version;
		$view->active 		= $section;
		$view->layout 		= $layout;
		$view->tool 		= isset($tool) ? $tool : array();
		$view->row 			= $row;
		$view->move 		= $move;
		$view->task 		= $this->_task;
		$view->config 		= $this->_config;	
		$view->pubconfig 	= $this->_pubconfig;
		$view->inreview 	= $inreview;
		$view->choices 		= $choices;
		$view->base 		= $base;
		$view->title		= $this->_area['title'];
				
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	/**
	 * Suggest licence
	 * 
	 * @return     string
	 */
	protected function _suggestLicense() 
	{
		// Incoming
		$pid  		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$version 	= JRequest::getVar( 'version', 'default' );	
		$ajax 		= JRequest::getInt('ajax', 0);
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		$row = new PublicationVersion( $this->_database );

		// If publication not found, raise error
		$pub = $objP->getPublication($pid, $version, $this->_project->id);
		if (!$pub) 
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			$this->_task = '';
			return $this->browse();
		}
		
		// Build pub url
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid);
				
		if ($this->_task == 'save_license')
		{
			$l_title 	= htmlentities(JRequest::getVar('license_title', '', 'post'));
			$l_url 		= htmlentities(JRequest::getVar('license_url', '', 'post'));
			$l_text 	= htmlentities(JRequest::getVar('details', '', 'post'));
			
			if (!$l_title && !$l_url && !$l_text)
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_ERROR'));
			}
			else
			{
				// Include support scripts
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'comment.php' );
				$juser =& JFactory::getUser();
				
				// Load the support config
				$sparams = JComponentHelper::getParams('com_support');
			
				$row = new SupportTicket( $this->_database );
				$row->created =  date( "Y-m-d H:i:s" );
				$row->login = $juser->get('username');
				$row->email = $juser->get('email');
				$row->name = $juser->get('name');			
				$row->summary = JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_NEW');
				
				$report 	 	= JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_TITLE') . ': '. $l_title ."\r\n";
				$report 	   .= JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_URL') . ': '. $l_url ."\r\n";
				$report 	   .= JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_COMMENTS') . ': '. $l_text ."\r\n";
				$row->report 	= $report;
				$row->referrer 	= JRequest::getVar('HTTP_REFERER', NULL, 'server');
				$row->type	 	= 0;
				$row->severity	= 'normal';
				
				$admingroup = $this->_config->get('admingroup', '');
				$group = Hubzero_Group::getInstance($admingroup);
				$row->group = $group ? $group->get('cn') : '';

				if (!$row->store()) 
				{
					$this->setError($row->getError());
				}
				else
				{
					$ticketid = $row->id;
					
					// Notify project admins
					$message  = $row->name . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTED')."\r\n";;
					$message .= '----------------------------'."\r\n";
					$message .=	$report;
					$message .= '----------------------------'."\r\n";
					
					if ($ticketid)
					{						
						$juri =& JURI::getInstance();
												
						$message .= JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_TICKET_PATH') ."\n";
						$message .= $juri->base() . 'support/ticket/' . $ticketid . "\n\n";
					}

					if ($group)
					{
						$members 	= $group->get('members');
						$managers 	= $group->get('managers');
						$admins 	= array_merge($members, $managers);
						$admins 	= array_unique($admins);

						// Send out email to admins
						if (!empty($admins)) 
						{
							ProjectsHelper::sendHUBMessage(
								$this->_option,
								$this->_config,
								$this->_project, 
								$admins, 
								JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_NEW'),
								'projects_new_project_admin', 
								'admin',
								$message
							);
						}
					}
					
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGESTION_SENT');
				}	
			}
		}
		else
		{
			 $view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'publications',
					'name'=>'suggestlicense'
				)
			);

			// Output HTML
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->pid 			= $pid;
			$view->pub 			= $pub;
			$view->task 		= $this->_task;
			$view->config 		= $this->_config;	
			$view->pubconfig 	= $this->_pubconfig;
			$view->ajax 		= $ajax;
			$view->route 		= $route;
			$view->version 		= $version;
			$view->url 			= $url;
			$view->title		= $this->_area['title'];

			// Get messages	and errors	
			$view->msg = $this->_msg;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Redirect 
		$this->_referer = $url . '?version=' . $version . '&section=license';
		return;
	}
	
	/**
	 * Start/save a new version
	 * 
	 * @return     string
	 */
	protected function _newVersion() 
	{
		// Incoming
		$pid  = $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$ajax = JRequest::getInt('ajax', 0);
		$label = trim(JRequest::getVar( 'version_label', '', 'post' )); 
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		$row = new PublicationVersion( $this->_database );
		
		// If publication not found, raise error
		$pub = $objP->getPublication($pid, 'default', $this->_project->id);
		if (!$pub) 
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			$this->_task = '';
			return $this->browse();
		}
		
		// Get publications helper
		$helper = new PublicationHelper( $this->_database );
		
		// Build pub url
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid);
			
		// Check if dev version is already there
		if ($row->checkVersion($pid, 'dev')) 
		{
			// Redirect 
			$this->_referer = $url.'?version=dev';
			return;
		}
		
		// Load default version
		$row->loadVersion($pid, 'default');
		$oldid = $row->id;
		$now = date( 'Y-m-d H:i:s' );
		
		// Can't start a new version if there is a finalized or submitted draft
		if ($row->state == 4 || $row->state == 5 ) 
		{
			// Determine redirect path
			$this->_referer = $url.'?version=default';
			return;
		}
		
		// Saving new version
		if ($this->_task == 'savenew') 
		{
			$used_labels = $row->getUsedLabels( $pid, 'dev');
			if (!$label) 
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_NONE') );	
			}
			elseif ($label && in_array($label, $used_labels)) 
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );	
			}
			else 
			{
				// Create new version
				$new =  new PublicationVersion( $this->_database );	
				$new = $row; // copy of default version
				$new->id = 0;
				$new->created = $now;
				$new->created_by = $this->_uid;
				$new->modified = $now;
				$new->modified_by = $this->_uid;
				$new->rating = '0.0';
				$new->state = 3;
				$new->version_label = $label;
				$new->doi = '';
				$new->ark = '';
				$new->secret = strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
				$new->version_number = $pub->versions + 1;
				$new->main = 0;
				
				if ($new->store()) 
				{
					$newid = $new->id;
					
					// Get attachments
					$pContent = new PublicationAttachment( $this->_database );
					$attachments = $pContent->getAttachments( $oldid );
					
					// Copy attachments from default to new version
					if ($attachments) 
					{
						foreach ($attachments as $att) 
						{
							$pAttach = new PublicationAttachment( $this->_database );
							$pAttach->publication_id 		= $att->publication_id;
							$pAttach->title 				= $att->title;
							$pAttach->role 					= $att->role;
							$pAttach->path 					= $att->path;
							$pAttach->vcs_hash 				= $att->vcs_hash;
							$pAttach->vcs_revision 			= $att->vcs_revision;
							$pAttach->object_id 			= $att->object_id;
							$pAttach->object_name 			= $att->object_name;
							$pAttach->object_instance 		= $att->object_instance;
							$pAttach->object_revision 		= $att->object_revision;
							$pAttach->type 					= $att->type;
							$pAttach->params 				= $att->params;
							$pAttach->attribs 				= $att->attribs;
							$pAttach->ordering 				= $att->ordering;
							$pAttach->publication_version_id= $newid;
							$pAttach->created_by 			= $this->_uid;
							$pAttach->created 				= $now;
							if (!$pAttach->store()) 
							{
								continue;
							}
						}
					}					
					
					jimport('joomla.filesystem.file');
					jimport('joomla.filesystem.folder');
					
					// Build publication path
					$base_path = $this->_pubconfig->get('webpath');
					$oldpath = $helper->buildPath($pid, $oldid, $base_path, $pub->secret, 1);
					$newpath = $helper->buildPath($pid, $newid, $base_path, $new->secret, 1);
					
					// Copy attachment files
					if (is_dir($oldpath)) 
					{
						JFolder::copy($oldpath, $newpath, '', true);				
					}				
					
					// Get authors
					$pa = new PublicationAuthor( $this->_database );
					$authors = $pa->getAuthors($oldid);

					// Copy authors from default to new version
					if ($authors) 
					{
						foreach ($authors as $author) 
						{
							$pAuthor = new PublicationAuthor( $this->_database );
							$pAuthor->user_id = $author->user_id;
							$pAuthor->ordering = $author->ordering;
							$pAuthor->credit = $author->credit;
							$pAuthor->role = $author->role;
							$pAuthor->status = $author->status;
							$pAuthor->organization = $author->organization;
							$pAuthor->name = $author->name;
							$pAuthor->project_owner_id = $author->project_owner_id;
							$pAuthor->publication_version_id = $newid;
							$pAuthor->created = $now;
							$pAuthor->created_by = $this->_uid;
							if (!$pAuthor->createAssociation()) 
							{
								continue;
							}
						}
					}
					
					// Copy gallery images
					$pScreenshot = new PublicationScreenshot( $this->_database );
					$screenshots = $pScreenshot->getScreenshots( $oldid );
					if ($screenshots) 
					{
						foreach ($screenshots as $shot) 
						{
							$pShot = new PublicationScreenshot( $this->_database );
							$pShot->filename = $shot->filename;
							$pShot->srcfile = $shot->srcfile;
							$pShot->publication_id = $shot->publication_id;
							$pShot->publication_version_id = $newid;
							$pShot->title = $shot->title;
							$pShot->created = $now;
							$pShot->created_by = $this->_uid;
							$pShot->ordering = $shot->ordering;
							if (!$pShot->store()) 
							{
								continue;
							}
						}
					}
					
					// Copy image files
					$g_oldpath = $helper->buildPath($pid, $oldid, $base_path, 'gallery', 1);
					$g_newpath = $helper->buildPath($pid, $newid, $base_path, 'gallery', 1);
					if (is_dir($g_oldpath)) 
					{
						JFolder::copy($g_oldpath, $g_newpath, '', true);				
					}	
														
					// Copy access info
					$pAccess = new PublicationAccess( $this->_database );
					$access_groups = $pAccess->getGroups($oldid);
					if ($access_groups) 
					{
						foreach($access_groups as $ag) 
						{
							$pNewAccess = new PublicationAccess( $this->_database );
							$pNewAccess->publication_version_id = $newid;
							$pNewAccess->group_id = $ag->group_id;
							if (!$pNewAccess->store()) 
							{
								continue;
							}
						}
					}
					
					// Copy audience info
					$pAudience = new PublicationAudience( $this->_database );
					if ($pAudience->loadByVersion($oldid)) 
					{
						$pAudienceNew = new PublicationAudience( $this->_database );
						$pAudienceNew = $pAudience;
						$pAudienceNew->publication_version_id = $newid;
						$pAudienceNew->store();
					}
					
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NEW_VERSION_STARTED');	
					
					// Record activity
					$pubtitle = Hubzero_View_Helper_Html::shortenText($new->title, 100, 0);
					$action  = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_STARTED_VERSION').' '.$new->version_label.' ';
					$action .=  JText::_('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION').' "'.$pubtitle.'"';
					$objAA = new ProjectActivity ( $this->_database );
					$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
						   $action, $pid, $pubtitle,
						   JRoute::_('index.php?option=' . $this->_option . a . 
						   'alias=' . $this->_project->alias . a . 'active=publications' . a . 
						   'pid=' . $pid) . '/?version=' . $new->version_number, 'publication', 1 );			
				}
				else 
				{
					$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_SAVING_NEW_VERSION') );		
				}												
			}
		}
		// Need to ask for new version label	
		else 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'publications',
					'name'=>'newversion'
				)
			);
			
			// Output HTML
			$view->option 		= $this->_option;
			$view->database 	= $this->_database;
			$view->project 		= $this->_project;
			$view->authorized 	= $this->_authorized;
			$view->uid 			= $this->_uid;
			$view->pid 			= $pid;
			$view->pub 			= $pub;
			$view->task 		= $this->_task;
			$view->config 		= $this->_config;	
			$view->pubconfig 	= $this->_pubconfig;
			$view->ajax 		= $ajax;
			$view->route 		= $route;
			$view->url 			= $url;
			$view->title		= $this->_area['title'];
	
			// Get messages	and errors	
			$view->msg = $this->_msg;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
				
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}

		// Redirect 
		$this->_referer = $url.'?version=dev';
		return;	
	}
	
	/**
	 * Review publication
	 * 
	 * @return     string
	 */
	public function review() 
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$version 	= JRequest::getVar('version', '');
		$pubdate 	= JRequest::getVar('publish_date');	
		
		$document =& JFactory::getDocument();
		$document->addStylesheet('components' . DS . 'com_projects' . DS . 'assets' . DS . 'css' . DS . 'calendar.css');
				
		// Check that version number exists
		$row = new PublicationVersion( $this->_database );
		$version = $version && $row->checkVersion($pid, $version) ? $version : 'dev';
		
		// Load default version preview for users with read-only access
		if ($this->_authorized == 3)
		{
			$version = 'default';
		}
				
		// Instantiate project publication
		$objP = new Publication( $this->_database );
				
		// If publication not found, raise error
		$pub = $objP->getPublication($pid, $version, $this->_project->id);
		if (!$pub) 
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			$this->_task = '';
			return $this->browse();
		}
		
		// Master type
		$this->_base = $pub->base;
		
		// Get active panels
		$this->_getPanels();
		
		// Instantiate publication version
		$row->loadVersion($pid, $version);
					
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'review'
			)
		);
		
		// Build pub url
		$view->route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
			
		// Append breadcrumbs
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$url =  $view->url . '?version='.$version;
		$pathway->addItem(
			stripslashes($row->title),
			$url	
		);		
		
		// Get master publication types
		$mt = new PublicationMasterType( $this->_database );
		$choices = $mt->getTypes('alias', 1);
		
		// Check that choices apply to a particular project
		$choices = $this->_getAllowedTypes($choices);
				
		// Get type info
		$view->_category = new PublicationCategory( $this->_database );
		$view->_category->load($pub->category);
		$view->_category->_params = new JParameter( $view->_category->params );
				
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $pub->version_id, $pub->id);
		$view->helper = $helper;
		
		// Get projects helper
		$view->projectsHelper = new ProjectsHelper( $this->_database );
		
		// What's the last visited panel
		$view->params 		= new JParameter( $row->params );
		$view->lastpane 	= $view->params->get('stage', 'content');		
		$indexes 			= $this->_getIndex($row, $view->lastpane, '');
		$view->last_idx 	= $indexes['last_idx'];
		$view->current_idx 	= $indexes['current_idx'];	

		// Checked areas
		$view->checked = $this->_checkDraft( $pub->base, $row, $version );

		// Areas required for publication
		$view->required = $this->_required;

		// Areas that can be updated after publication
		$view->mayupdate = $this->_updateAllowed;

		// Check if all required area are filled in
		$view->publication_allowed = $this->_checkPublicationPermit($view->checked);
		
		// Get detailed information
		// Get authors
		$pa = new PublicationAuthor( $this->_database );
		$view->authors = $pa->getAuthors($row->id);
		
		// Get attachments
		$pContent = new PublicationAttachment( $this->_database );
		$view->primary = $pContent->getAttachments( $row->id, $filters = array('role' => '1') );
		$view->secondary = $pContent->getAttachments( $row->id, $filters = array('role' => '0') );
								
		// Build publication paths (to access attachments and images)
		$base_path = $this->_pubconfig->get('webpath');
		if ($version == 'dev') 
		{
			$view->fpath = $helper->buildDevPath($pub->project_alias);
		}
		else 
		{
			$view->fpath = $view->helper->buildPath($pub->id, $pub->version_id, $base_path, $pub->secret, $root = 1);
		}
		$gallery_path = $view->helper->buildPath($pub->id, $pub->version_id, $base_path, 'gallery');
		
		// Get project file path
		$view->prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT;
		$view->project_path = ProjectsHelper::getProjectPath($this->_project->alias, 
				$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		
		// Get tags
		$view->helper->getTagCloud( 1 );
		$view->tags = $view->helper->tagCloud;
		
		// Get license info
		$pLicense = new PublicationLicense( $this->_database );
		$view->license = $pLicense->getLicense($pub->license_type);
		
		// Sys group
		$cn = $this->_config->get('group_prefix', 'pr-').$this->_project->alias;
		$view->sysgroup = new Hubzero_Group();
		if (Hubzero_Group::exists($cn)) 
		{
			$view->sysgroup = Hubzero_Group::getInstance( $cn );
		}
		
		// Is access restricted?
		$paccess = new PublicationAccess( $this->_database );
		$view->access_groups = $paccess->getGroups($pub->version_id, $pub->id, $version, $cn);
		
		// Get gallery images
		$pScreenshot = new PublicationScreenshot( $this->_database );
		$gallery = $pScreenshot->getScreenshots( $pub->version_id );	
		$view->shots = PublicationsHtml::showGallery($gallery, $gallery_path);
		
		// Get JS
		$document =& JFactory::getDocument();
		$document->addScript('components' . DS . 'com_publications' . DS . 'publications.js');
		
		//Import the wiki parser
		ximport('Hubzero_Wiki_Parser');
		$view->parser =& Hubzero_Wiki_Parser::getInstance();

		$view->wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => '',
			'pagename' => 'projects',
			'pageid'   => '',
			'filepath' => '',
			'domain'   => ''
		);

		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->version 		= $version;
		$view->pub 			= $pub;
		$view->row 			= $row;
		$view->task 		= $this->_task;
		$view->config 		= $this->_config;	
		$view->pubconfig 	= $this->_pubconfig;
		$view->choices 		= $choices;
		$view->panels 		= $this->_panels;
		$view->title		= $this->_area['title'];
		$view->pubdate		= $pubdate;
		
		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	/**
	 * Save publication
	 * 
	 * @return     string
	 */
	public function save() 
	{
		// Incoming
		$move 		= JRequest::getInt( 'move', 0 ); 
		$section 	= JRequest::getVar( 'section', 'version' );
		$toolid 	= JRequest::getVar( 'toolid', 0 );
		$pid 		= JRequest::getInt( 'pid', 0 );
		$version 	= JRequest::getVar( 'version', '' );	
		$primary 	= JRequest::getVar( 'primary', 1 ); 
		$base 		= JRequest::getVar( 'base', 'files' );
		$selections = JRequest::getVar( 'selections', array(), 'post' );
		$inreview 	= JRequest::getInt( 'review', 0 );
		$step 		= JRequest::getVar( 'step', '' );	
			
		$layout = $section;
		$newpub = 0;
		$newversion = 0;
		$now = date( 'Y-m-d H:i:s' );

		// Check that version exists
		$row = new PublicationVersion( $this->_database );
		$version = $row->checkVersion($pid, $version) ? $version : 'default';
				
		// Get selected content
		if ($section == 'content' or $section == 'gallery') 
		{
			$selections = $this->_parseSelections($selections);
			
			// Check for primary content
			if ($section == 'content' && $primary && (empty($selections) || $selections['count'] == 0)) 
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NO_CONTENT_SELECTED') );
			}
			else
			{
				$arr = explode("::", $selections['first']);
				$first_type = urldecode($arr[0]);
				$first_item = (isset($arr[1])) ? urldecode($arr[1]) : '';
			}
		}		
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		$mt = new PublicationMasterType( $this->_database );
						
		// If publication not found, raise error
		if (!$objP->load($pid) && $section != 'content') 
		{
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND') );
			return;
		}	
		// Save new publication
		elseif (!$objP->id && $primary) 
		{
			 // Flag as new publication
			$newpub = 1;
						
			if (!$this->getError()) 
			{				
				// Determine publication master type
				$choices = $mt->getTypes('alias', 1);
				
				// Check that choices apply to a particular project
				$choices = $this->_getAllowedTypes($choices);
				
				$mastertype = in_array($base, $choices) ? $base : 'files';
								
				// Need to provision a project
				if (!is_object($this->_project) or !$this->_project->id) 
				{			
					$this->_project = new Project( $this->_database );
					$this->_project->provisioned = 1;
					$random = strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
					$this->_project->alias 	 			= 'pub-' . $random;
					$this->_project->title 	 			= $this->_project->alias;
					$this->_project->type 	 			= $base == 'tools' ? 2 : 3; // content publication
					$this->_project->state   			= 1;
					$this->_project->created 			= date( 'Y-m-d H:i:s' );
					$this->_project->created_by_user 	= $this->_uid;
					$this->_project->owned_by_user 		= $this->_uid;
					$this->_project->setup_stage 		= 3;

					// Get project type params
					require_once( JPATH_ROOT. DS .'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.type.php');
					$objT = new ProjectType( $this->_database );
					$this->_project->params = $objT->getParams ($this->_project->type);

					// Save changes
					if (!$this->_project->store()) 
					{
						$this->setError( $this->_project->getError() );
						return false;
					}	
					
					if (!$this->_project->id) 
					{
						$this->_project->checkin();
					}				
				}
				
				// Determine publication type
				$objT = new PublicationCategory( $this->_database );
				$defaultCat = $this->_pubconfig->get('default_category', '');
				
				$cat = $defaultCat 
						? $objT->getCatId($defaultCat) 
						: $objT->getCatId($objT->suggestCat($arr));

				// Determine title
				$title = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DEFAULT_TITLE');
				if ($base == 'files' && count($selections['files']) == 1) 
				{
					$title = $first_item;
				}
				elseif ($base == 'databases')
				{
					// Get project database object
					$objPD = new ProjectDatabase($this->_database);
					if (!$objPD->loadRecord($first_item))
					{
						JError::raiseError( $objPD->getError() );
						return false;
					}
					else
					{
						$title = $objPD->title;
					}
				} 
				elseif ($base == 'notes')
				{
					// Get helper
					$projectsHelper = new ProjectsHelper( $this->_database );
					
					$masterscope = 'projects' . DS . $this->_project->alias . DS . 'notes';
					$group = $this->_config->get('group_prefix', 'pr-') . $this->_project->alias;

					$note = $projectsHelper->getSelectedNote($first_item, $group, $masterscope);
					$title = $note ? $note->title : '';					
				}
				
				$title = $title == '' ? JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DEFAULT_TITLE') : $title;
				
				// Make a new publication entry
				$objP->master_type = $mt->getTypeId($mastertype);
				$objP->category = $cat;				
				$objP->project_id = $this->_project->id;
				$objP->created_by = $this->_uid;
				$objP->created = $now;
				if (!$objP->store()) 
				{
					JError::raiseError( $objP->getError() );
					return false;
				} 
				if (!$objP->id) 
				{
					$objP->checkin();
				}
				$pid = $objP->id;
				$this->_pid = $pid;
								
				// Initizalize Git repo and transfer files from member dir
				if ($this->_project->provisioned == 1 && $newpub)
				{
					if (!$this->_prepDir())
					{
						// Roll back
						$this->_project->delete();
						$objP->delete();
						
						JError::raiseError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_FAILED_INI_GIT_REPO') );
						return false;
					}
					else 
					{
						// Add creator as project owner
						$objO = new ProjectOwner( $this->_database );
						if (!$objO->saveOwners ( $this->_project->id, 
							$this->_uid, $this->_uid, 
							0, 1, 1, 1 )) 
						{
							// File auto ticket to report this - TBD
							//*******
							$this->setError( JText::_('COM_PROJECTS_ERROR_SAVING_AUTHORS').': '.$objO->getError() );
							return false;
						}
					}	
				}
				
				// Make a new dev version entry
				$row 					= new PublicationVersion( $this->_database );
				$row->publication_id 	= $pid;
				$row->title 			= $title;
				$row->state 			= 3; // dev
				$row->main 				= 1;
				$row->created_by 		= $this->_uid;
				$row->created 			= $now;
				$row->version_number 	= 1;
				$row->license_type 		= 0;
				
				// Get hash code for version (to be used as a dir name to guard against direct file access)
				$code = strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
				$row->secret = $code;
				
				$row->params = 'stage=content'."\n";			
				if (!$row->store()) 
				{
					// Roll back
					$objP->delete();
					
					JError::raiseError( $row->getError() );
					return false;
				}
				if (!$row->id) 
				{
					$row->checkin();
				} 
				$vid = $row->id;
								
				// Proccess attachments
				$added = $this->_processContent( $pid, $vid, $selections, $primary, $row->secret, $row->state, $newpub);
				
				// Roll back on error
				if (!$added)
				{
					$objP->delete();
					$row->delete();
					if ($this->_project->provisioned == 1 && $newpub)
					{
						$this->_project->delete();
					}
					
					$this->setError( JText::_('COM_PROJECTS_ERROR_ATTACHING_CONTENT'));
					//return false;
				}
				else
				{
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_NEW_PUB_STARTED');	
				}

			} // end if no error (new pub)			
		} 
		elseif ($objP->project_id != $this->_project->id) 
		{
			// Publication belongs to another project
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_PROJECT_ERROR') );
			return;
		}
		
		// Saving existing publication
		if (!$newpub && !$this->getError()) 
		{				
			// Instantiate publication version
			$row = new PublicationVersion( $this->_database );
			if (!$row->loadVersion( $pid, $version )) 
			{
				JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND') );
				return;
			}
			// Disable editing for some DOI-related info, if published
			$canedit = ($row->state == 1 || $row->state == 0 || $row->state == 6) ? 0 : 1;
			
			// Make sure version has a secret id
			if (!$row->secret) 
			{
				$code = strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
				$row->secret = $code;
				$row->store();
			}

			// Save sections
			switch ($section) 
			{
				case 'version':
					$label = trim(JRequest::getVar( 'label', '', 'post' )); 
					$used_labels = $row->getUsedLabels( $pid, $version );
					if ($label && in_array($label, $used_labels)) 
					{
						$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );	
					}
					elseif ($label) 
					{
						$row->version_label = $label;
						if ($row->store()) 
						{
							$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL_SAVED');
						}						
					}				
					break;

				case 'content':
					if ($version == 'dev' || ($version == 'default' 
						&& (!$primary || $row->state == 4 || $row->state == 5))) 
					{
						// Check for primary content
						if ($primary && (empty($selections) || $selections['count'] == 0) ) 
						{
							$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NO_CONTENT_SELECTED') );
						}
						else
						{							
							$added = $this->_processContent( $row->publication_id, $row->id, 
								$selections, $primary, $row->secret, $row->state, 0 );
						}				

						$this->_msg = $primary 
							? JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT_SAVED') 
							: JText::_('PLG_PROJECTS_PUBLICATIONS_SUP_CONTENT_SAVED');
					}
					elseif ($version == 'default') 
					{
						// Published version! cannot update primary content
						$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ERROR_CANNOT_SAVE_PRIMARY') );
					}					
					break;

				case 'description':
					if ($step == 'metadata') 
					{
						$row->metadata = $this->_processMetadata( $objP->type );
					}
					else 
					{
						$title 			= trim(JRequest::getVar( 'title', '', 'post' )); 
						$title 			= htmlspecialchars($title);
						$abstract 		= trim(JRequest::getVar( 'abstract', '', 'post' )); 
						$abstract 		= Hubzero_Filter::cleanXss(htmlspecialchars($abstract));
						$description 	= trim(JRequest::getVar( 'description', '', 'post' ));	
						$description 	= stripslashes($description);

						if ($canedit) 
						{
							if (!$title || !$description || !$abstract) 
							{
								$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_MISSING_REQUIRED_INFO') );
							}
							$row->title = $title;
							$row->abstract = Hubzero_View_Helper_Html::shortenText($abstract, 250, 0);
							$row->description = $description;						
						}
						elseif (!$description) 
						{
							$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_MISSING_REQUIRED_INFO') );
						}
						else 
						{
							$row->description = $description;
						}						
					}
					
					if (!$row->store()) 
					{
						JError::raiseError( $row->getError() );
						return false;
					}
					
					if (!$this->getError()) 
					{
						$this->_msg = $step == 'metadata' 
							? JText::_('PLG_PROJECTS_PUBLICATIONS_METADATA_SAVED') 
							: JText::_('PLG_PROJECTS_PUBLICATIONS_DESCRIPTION_SAVED');
					}
				
					break;

				case 'authors':
					$selections = explode("##", $selections);
					if (count($selections) > 0) 
					{
						if ($this->_processAuthors($row->id, $selections)) 
						{
							$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_SAVED');		
						}									
					}
					else 
					{
						$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_NO_AUTHORS_SAVED') );
					}
					
					break;
				
				case 'access':
					// Incoming
					$access = JRequest::getInt( 'access', 0, 'post' ); 
								
					if ($access >= 2) 
					{			
						$access_groups = JRequest::getVar( 'access_group', 0, 'post' ); 
						
						// Sys group
						$cn = $this->_config->get('group_prefix', 'pr-').$this->_project->alias;
						$sysgroup = new Hubzero_Group();
						if (Hubzero_Group::exists($cn)) 
						{
							$sysgroup = Hubzero_Group::getInstance( $cn );
						}

						$paccess = new PublicationAccess( $this->_database );
						$paccess->saveGroups($row->id, $access_groups, $sysgroup->get('gidNumber'));
						$private = JRequest::getVar( 'private', 0, 'post' ); 
						$access = $private ? 3 : 2;
					}
					
					$row->access = $access;
					if (!$row->store()) 
					{
						JError::raiseError( $row->getError() );
						return false;
					}					
				
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_SAVED');
					break;
				
				case 'license':
					// Incoming
					$license = trim(JRequest::getVar( 'license', 0, 'post' )); 
					$text 	 = JRequest::getVar( 'license_text', '', 'post', 'array' );
					$agree 	 = JRequest::getVar( 'agree', 0, 'post', 'array' ); 

					// Get standard license info
					$objL = new PublicationLicense( $this->_database);
					$selected_license = $objL->getLicenseByName ($license);
					
					if ($selected_license) 
					{
						if ($selected_license->agreement == 1 
							&& (empty($agree) || !isset($agree[$license]) || $agree[$license] == 0)) 
						{
							$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_AGREEMENT') );
						}
						elseif ($selected_license->customizable == 1 
							&& $selected_license->text && (empty($text) 
							|| !isset($text[$license]) || $text[$license] == '')) 
						{
							$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_TEXT') );
						}
						else 
						{
							$row->license_text = isset($text[$license]) ? stripslashes(rtrim($text[$license])) : '';
							$row->license_type = $selected_license->id;
							if (!$row->store()) 
							{
								JError::raiseError( $row->getError() );
								return false;
							}
							$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SAVED');
						}
					}
					else 
					{
						$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SELECTION_NOT_FOUND') );
					}
					
					break;

				case 'audience':
					if ($this->_processAudience($row->publication_id, $row->id)) 
					{
						$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_SAVED');
					}
				
					break;

				case 'gallery':
					$this->_processGallery($row->publication_id, $row->id, $selections);
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_SAVED');
					break;

				case 'tags':
					$tagsHelper = new PublicationTags( $this->_database);
					$tags = trim(JRequest::getVar('tags', '', 'post'));
					$tagsHelper->tag_object($this->_uid, $row->publication_id, $tags, 1);
	
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_SAVED');
					
					// Save category
					$objT = new PublicationCategory( $this->_database );
					$cat = JRequest::getInt( 'pubtype', 0 );
					if ($cat && $objP->category != $cat) 
					{
						$objP->category = $cat;
						$objP->store();
					}					
					break;

				case 'notes':
					$notes = trim(JRequest::getVar( 'notes', '', 'post' ));	
					$notes = stripslashes($notes);
					$row->release_notes = $notes;
					if (!$row->store()) 
					{
						JError::raiseError( $row->getError() );
						return false;
					}
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_NOTES_SAVED');
					break;
			}	
		}		
		
		// Save last accomplished step
		if (!$this->getError()) 
		{
			// Get last accomplished section
			$pubparams = new JParameter( $row->params );
			$lastpane = $pubparams->get('stage', 'content');

			// Get next and last accomplished step
			$indexes = $this->_getIndex($row, $lastpane, $section);
			$last_idx = $indexes['last_idx'];
			$next_idx = $indexes['next_idx'];
			
			// Master type
			$this->_base = $mt->getTypeAlias($objP->master_type);
			
			// Get active panels
			$this->_getPanels();

			if ($move) 
			{
				// Determine next section & layout
				if ($section == 'content' && $primary) 
				{
					$layout = 'supportingdocs';
				}
				elseif ($section == 'description') 
				{
					$add_metadata = JRequest::getInt( 'add_metadata', 0 ); 
					$layout = $add_metadata ? 'metadata' : 'authors';
					$section = $add_metadata ? 'description' : 'authors';
				}
				else 
				{
					if ($next_idx == count($this->_panels)) 
					{
						// last step accomplished
						$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_DRAFT_COMPLETE');
						$section = 'version';
					}
					else 
					{
						$section = $this->_panels[$next_idx];
					}	
				}				
			}
			else 
			{
				// Determine layout
				$primary = JRequest::getInt( 'primary', 0, 'post' ); 
				if ($section == 'content' && !$primary) 
				{
					$layout = 'supportingdocs';
				}
				elseif ($section == 'description') 
				{
					$layout = $step == 'metadata' ? 'metadata' : 'description';
				}
			}

			// Save visit to panel (only when moving one step at a time)
			if ($next_idx > $last_idx && ($next_idx == $last_idx + 1)) 
			{
				$nextstep = isset($this->_panels[$next_idx]) && $lastpane != 'review' ? $this->_panels[$next_idx] : 'review';
				$row->saveParam( $row->id, 'stage', $nextstep  );
			}
		}
		
		// Record activity
		if (!$this->getError() && $newpub && !$this->_project->provisioned)
		{
			$objAA = new ProjectActivity ( $this->_database );
			$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
				   JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_STARTED_NEW_PUB')
				 	.' (id '.$pid.')', $pid, 'publication',
				   JRoute::_('index.php?option=' . $this->_option . a . 
				   'alias=' . $this->_project->alias . a . 'active=publications' . a . 
				   'pid='.$pid), 'publication', 1 );
				
			// Notify project managers
			$objO = new ProjectOwner($this->_database);
			$managers = $objO->getIds($this->_project->id, 1, 1);
			if (!empty($managers))
			{
				$profile =& Hubzero_Factory::getProfile();
				$profile->load( $this->_uid );
				$juri =& JURI::getInstance();
				
				$sef = JRoute::_('index.php?option=' . $this->_option . a 
					. 'alias=' . $this->_project->alias . a . 'active=publications' 
					. a . 'pid='.$pid);
				if (substr($sef,0,1) == '/') 
				{
					$sef = substr($sef,1,strlen($sef));
				}
					
				ProjectsHelper::sendHUBMessage(
					'com_projects',
					$this->_config,
					$this->_project, 
					$managers, 
					JText::_('COM_PROJECTS_EMAIL_MANAGERS_NEW_PUB_STARTED'),
					'projects_admin_message', 
					'publication',
					$profile->get('name') . ' ' 
						. JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_STARTED_NEW_PUB')
					 	.' (id '.$pid.')' . ' - ' . $juri->base() 
						. $sef . '/?version=' . $row->version_number
				);
			}				
		}
		
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}
		
		// Determine redirect path
		$url = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($url . a . 'pid=' . $pid);
	
		if ($section == 'review' or $inreview) 
		{		
			$url .= '?action=review';
		}
		else 
		{
			$url .= '?section=' . $section;
			$url .= $section != $layout ?  '&step=' . $layout : '';
			$url .= $move ? '&move=' . $move : '';
		}
		$url .= $version != 'default' ? '&version=' . $version : '';
		
		// Redirect 
		$this->_referer = $url;
		return;
	}
	
	/**
	 * Check if there is available space for publishing
	 * 
	 * @return     string
	 */
	protected function _overQuota()
	{
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		
		// Get all publications
		$rows = $objP->getRecords(array('project' => $this->_project->id, 'dev' => 1, 'ignore_access' => 1));		
		
		// Get used space
		$helper 	   = new PublicationHelper($this->_database);
		$dirsize 	   = $helper->getDiskUsage($this->_project->id, $rows);
		$params  	   = new JParameter( $this->_project->params );
		$quota   	   = $params->get('pubQuota') 
						? $params->get('pubQuota') 
						: ProjectsHtml::convertSize(floatval($this->_config->get('pubQuota', '1')), 'GB', 'b');
						
		if (($quota - $dirsize) <= 0)
		{
			return true;
		}	
		
		return false;
	}
	
	/**
	 * Change publication status
	 * 
	 * @return     string
	 */
	protected function _publish() 
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$confirm 	= JRequest::getInt('confirm', 0);	
		$version 	= JRequest::getVar('version', 'dev');
		$republish  = $this->_task == 'republish' ? 1 : 0; 
		$agree   	= JRequest::getInt('agree', 0);
		$pubdate 	= JRequest::getVar('publish_date', '', 'post');
		
		$notify 	= 1;
				
		// Load review step
		if (!$confirm && $this->_task != 'revert') 
		{
			return $this->review();
		}
		
		// Determine redirect path
		$url = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($url . a . 'pid=' . $pid);
		
		// Agreement to terms is required
		if ($confirm && !$agree)
		{			
			$url .= '/?action= ' . $this->_task . '&version=' . $version;
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_REVIEW_AGREE_TERMS_REQUIRED') );	
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
			
			// Redirect 
			$this->_referer = $url;
			return;
		}
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		
		// Get all publications
		$rows = $objP->getRecords(array('project' => $this->_project->id, 'dev' => 1, 'ignore_access' => 1));		
		
		if ($this->_overQuota())
		{
			$url .= '/?action= ' . $this->_task . '&version=' . $version;
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NO_DISK_SPACE') );	
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
			
			// Redirect 
			$this->_referer = $url;
			return;
		}
		
		// Import pub utilities
		require_once(JPATH_ROOT . DS. 'administrator' . DS . 'components' . DS 
		. 'com_publications' . DS . 'helpers' . DS . 'utilities.php');		
							
		// If publication not found, raise error
		$pub = $objP->getPublication($pid, $version, $this->_project->id);
		if (!$pub) 
		{
			if ($pid) 
			{
				$this->_referer = $url;
				return;
			}
			else 
			{
				JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND') );
				return;
			}
		}
		
		// Instantiate publication version
		$row = new PublicationVersion( $this->_database );
		if (!$row->loadVersion($pid, $version)) 
		{
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND') );
			return;
		}
						
		// Check that version label was not published before
		$used_labels = $row->getUsedLabels( $pid, $version );
		if (!$row->version_label || in_array($row->version_label, $used_labels)) 
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );	
		}
				
		// Determine state for new version		
		if ($this->_task == 'post' || $this->_task == 'revert')
		{
			$state = 4; // No approval needed
		}
		elseif ($republish)
		{
			$state = 1; // No approval needed
		}
		else 
		{
			$row->submitted = date( 'Y-m-d H:i:s' );
			if ($this->_pubconfig->get('autoapprove') == 1 )  
			{
				$state = 1;
			}
			else 
			{
				$apu = $this->_pubconfig->get('autoapproved_users');
				$apu = explode(',', $apu);
				$apu = array_map('trim',$apu);
				
				$juser =& JFactory::getUser();				
				if (in_array($juser->get('username'),$apu)) 
				{
					// Set status to published
					$state = 1;
				} else {
					// Set status to pending review (submitted)
					$state = 5;
				}
			}
		}
		
		// Main version?
		$main = $republish ? $row->main : 1;
		$main_vid = $row->getMainVersionId($pid); // current default version		
		
		// Checks
		if ($republish && $row->state != 0) 
		{
			// Can only re-publish unpublished version
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CANNOT_REPUBLISH') );
		}
		elseif ($this->_task == 'revert' &&  $row->state != 5) 
		{
			// Can only revert a pending resource
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_CANNOT_REVERT') );
		}
		elseif (!$republish && $this->_task != 'revert' &&  $row->state != 3 && $row->state != 4) 
		{
			// Can only publish a draft or posted version
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_DEV') );
		}
		
		// Embargo?
		if ($pubdate)
		{
			$date = explode('-', $pubdate);
			print_r($date);
			if (count($date) == 3) 
			{
				$year 	= $date[0];
				$month 	= $date[1];
				$day 	= $date[2];
				if (intval($month) && intval($day) && intval($year)) 
				{
					if (strlen($day) == 1) 
					{ 
						$day='0' . $day; 
					}

					if (strlen($month) == 1) 
					{ 
						$month = '0' . $month; 
					} 
					if (checkdate($month, $day, $year)) 
					{
						
						$pubdate = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
					}
				}
			}
			
			$tenYearsFromNow = date("Y-m-d H:i:s", strtotime("+10 years"));
			
			// Stop if more than 10 years from now
			if ($pubdate > $tenYearsFromNow)
			{
				$this->setError(JText::_('Embargo period on a publication cannot extend for more than 10 years. Please choose an earlier date.') );
				$url .= '/?action= ' . $this->_task . '&version=' . $version;
				$this->_message = array('message' => $this->getError(), 'type' => 'error');

				// Redirect 
				$this->_referer = $url;
				return;
			}
		}
						
		if (!$this->getError()) 
		{		
			// Checked areas
			$checked = $this->_checkDraft( $pub->base, $row, $version );

			// Check if all required areas are filled in
			$publication_allowed = $this->_checkPublicationPermit($checked);
			if (!$publication_allowed) 
			{
				JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_ALLOWED') );
				return;
			}

			// Save state
			$row->state = $state;
			$row->main = $main;
			$row->secret = $row->secret ? $row->secret : strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
			$row->rating = '0.0';
			if (!$republish)
			{
				$row->published_up = date( 'Y-m-d H:i:s' );	// Publication open immediately (no embargo)	
			}
			
			// Set embargo
			if ($pubdate)
			{
				$row->published_up = $pubdate;
			}
			
			$row->published_down = '';
			$row->modified = date( 'Y-m-d H:i:s' );
			$row->modified_by = $this->_uid;	
			
			// Get type
			$objT = new PublicationCategory( $this->_database );
			$objT->load($pub->category);
			$category = ucfirst($objT->alias);
			
			// Collect extra metadata
			$metadata = array();
			$metadata['typetitle']    = $category ? $category : 'Dataset';
			$metadata['resourceType'] = isset($objT->dc_type) && $objT->dc_type ? $objT->dc_type : 'Dataset';
			$metadata['language'] = 'en';
			
			// Get license type
			$objL = new PublicationLicense( $this->_database);
			if ($objL->load($row->license_type))
			{
				$metadata['rightsType'] = isset($objL->dc_type) && $objL->dc_type ? $objL->dc_type : 'other';
				$metadata['license'] = $objL->title;
			}
			
			// Get dc:contibutor
			$profile =& Hubzero_Factory::getProfile();
			$owner 	 = $this->_project->owned_by_user ? $this->_project->owned_by_user : $this->_project->created_by_user;
			if($profile->load( $owner ))
			{
				$metadata['contributor'] = $profile->get('name');	
			}
			
			// Get authors
			$pa = new PublicationAuthor( $this->_database );
			$authors = $pa->getAuthors($row->id);
						
			// Get DOI
			if (($state == 1 || $state == 5) && !$row->doi 
				&& $this->_pubconfig->get('doi_shoulder') && $this->_pubconfig->get('doi_service')) 
			{		
				// Issue a new DOI
				$reserve = $state == 5 ? 1 : 0;
				$doi = PublicationUtilities::registerDoi($row, $authors, $this->_pubconfig, 
					$metadata, $doierr, $reserve);
				
				if ($doi) 
				{
					$row->doi = $doi;
				}
				
				// Can't proceed without a valid DOI
				if (!$doi || $doierr) 
				{
					$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_DOI').' '.$doierr);
				}
			}
			
			// Proceed if no errors
			if (!$this->getError()) 
			{
				if (!$row->store()) 
				{
					JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED') );
					return;
				}

				// Remove main flag from previous default version			
				if ($main && $main_vid && $main_vid != $row->id) 
				{
					$row->removeMainFlag($main_vid);
				}

				// Copy attachments
				$published = $this->_publishAttachments($row);
				
				// Produce archival package
				$this->archivePub($pid, $row->id);

				// Display status message
				switch ($state) 
				{
					case 1:
					default:       
						$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_PUBLISHED'); 
						$action 	= $republish
									? JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_REPUBLISHED')
									: JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_PUBLISHED');      
						break;
						
					case 4:       
						$this->_msg = $this->_task == 'revert'
									? JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_REVERTED')
									: JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_SAVED') ;
						$action 	= $this->_task == 'revert'
									? JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_REVERTED') 
									: JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_SAVED');  
						$notify = 0;       
						break;
						
					case 5:       
						$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_SUCCESS_PENDING'); 
						$action 	= JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACTIVITY_SUBMITTED');            
						break;
				}
				$this->_msg .= ' <a href="'.JRoute::_('index.php?option=com_publications' . a . 
					    'id=' . $pid ) .'">'. JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VIEWIT').'</a>';

				$pubtitle = Hubzero_View_Helper_Html::shortenText($row->title, 100, 0);
				$action .= ' '.$row->version_label.' ';
				$action .=  JText::_('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION').' "'.html_entity_decode($pubtitle).'"';
				$action  = htmlentities($action, ENT_QUOTES, "UTF-8");

				// Record activity
				if (!$this->_project->provisioned) 
				{
					$objAA = new ProjectActivity ( $this->_database );
					$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
						   $action, $pid, $pubtitle,
						   JRoute::_('index.php?option=' . $this->_option . a . 
						   'alias=' . $this->_project->alias . a . 'active=publications' . a . 
						   'pid=' . $pid) . '/?version=' . $row->version_number, 'publication', 1 );
				}
				
				// Notify administrator of a new publication
				$profile =& Hubzero_Factory::getProfile();
				$profile->load( $this->_uid );
				$juri =& JURI::getInstance();
				
				$sef = JRoute::_('index.php?option=com_publications' . a . 'id=' . $pid );
				if (substr($sef,0,1) == '/') 
				{
					$sef = substr($sef,1,strlen($sef));
				}
				
				if ($notify)
				{
					$admingroup = $this->_config->get('admingroup', '');
					$group = Hubzero_Group::getInstance($admingroup);
					$admins = array();
					
					if ($admingroup && $group)
					{
						$members 	= $group->get('members');
						$managers 	= $group->get('managers');
						$admins 	= array_merge($members, $managers);
						$admins 	= array_unique($admins);

						ProjectsHelper::sendHUBMessage(
							'com_projects',
							$this->_config,
							$this->_project, 
							$admins, 
							JText::_('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS'),
							'projects_new_project_admin', 
							'publication',
							$profile->get('name') . ' ' . $action . '  - ' . $juri->base() 
								. $sef . '/?version=' . $row->version_number
						);
					}
				}
				
				// Notify project managers
				$objO = new ProjectOwner($this->_database);
				$managers = $objO->getIds($this->_project->id, 1, 1);
				if (!$this->_project->provisioned && !empty($managers))
				{
					ProjectsHelper::sendHUBMessage(
						'com_projects',
						$this->_config,
						$this->_project, 
						$managers, 
						JText::_('COM_PROJECTS_EMAIL_MANAGERS_NEW_PUB_STATUS'),
						'projects_admin_message', 
						'publication',
						$profile->get('name') . ' ' . html_entity_decode($action) . ' - ' . $juri->base() 
							. $sef . '/?version=' . $row->version_number
					);
				}
			}
		}
		
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}
	
		// Redirect 
		$this->_referer = $url;
		return;	
			
	}

	/**
	 * Unpublish version/delete draft
	 * 
	 * @return     string
	 */
	protected function _unpublish() 
	{
		// Incoming
		$pid 		= $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		$confirm 	= JRequest::getInt('confirm', 0);	
		$version 	= JRequest::getVar('version', 'default'); 
		$ajax 		= JRequest::getInt('ajax', 0);
		
		// Determine redirect path
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid);
				
		// Instantiate project publication
		$objP = new Publication( $this->_database );
		
		// If publication not found, raise error
		$pub = $objP->getPublication($pid, $version, $this->_project->id);
		if (!$pub) 
		{
			if ($pid) 
			{
				$this->_referer = $url;
				return;
			}
			else 
			{
				JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND') );
				return;
			}
		}
		
		// Instantiate publication version
		$row = new PublicationVersion( $this->_database );
		if (!$row->loadVersion($pid, $version)) 
		{
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND') );
			return;
		}
		
		// Save version ID
		$vid = $row->id;
		
		// Append breadcrumbs
		if (!$ajax) 
		{
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			$pathway->addItem(
				stripslashes($pub->title),
				$url	
			);
		}
		
		// Can only unpublish published version or delete a draft
		if ($pub->state != 1 && $pub->state != 3 && $pub->state != 4) 
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_CANT_DELETE'));
		}
		
		// Get published versions count
		$objV = new PublicationVersion( $this->_database );
		$publishedCount = $objV->getPublishedCount($pid);
						
		// Unpublish/delete version
		if ($confirm) 
		{
			if (!$this->getError()) 
			{
				$pubtitle = Hubzero_View_Helper_Html::shortenText($row->title, 100, 0);
				$objAA = new ProjectActivity ( $this->_database );
				
				if ($pub->state == 1) 
				{					
					// Unpublish published version
					$row->published_down = date( 'Y-m-d H:i:s' );	
					$row->modified = date( 'Y-m-d H:i:s' );
					$row->modified_by = $this->_uid;
					$row->state = 0;
					if (!$row->store()) 
					{
						JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNPUBLISH_FAILED') );
						return;
					}
					else 
					{
						$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_UNPUBLISHED');
						
						// Add activity
						$action  = JText::_('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_UNPUBLISHED');
						$action .= ' '.strtolower(JText::_('version')).' '.$row->version_label.' '
						.JText::_('PLG_PROJECTS_PUBLICATIONS_OF').' '.strtolower(JText::_('publication')).' "'
						.$pubtitle.'" ';

						$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
							   $action, $pid, $pubtitle,
							   JRoute::_('index.php?option=' . $this->_option . a . 
							   'alias=' . $this->_project->alias . a . 'active=publications' . a . 
							   'pid=' . $pid) . '/?version=' . $row->version_number, 'publication', 0 );
					}
				}
				elseif ($pub->state == 3 || $pub->state == 4) 
				{					
					$vlabel = $row->version_label;
					
					// Delete draft version
					if (!$row->delete()) 
					{
						JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DELETE_DRAFT_FAILED') );
						return;
					}
					
					// Delete authors
					$pa = new PublicationAuthor( $this->_database );
					$authors = $pa->deleteAssociations($vid);
					
					// Delete attachments
					$pContent = new PublicationAttachment( $this->_database );
					$pContent->deleteAttachments($vid);
					
					// Delete screenshots
					$pScreenshot = new PublicationScreenshot( $this->_database );
					$pScreenshot->deleteScreenshots($vid);
					
					jimport('joomla.filesystem.file');
					jimport('joomla.filesystem.folder');
					
					// Get publications helper
					$helper = new PublicationHelper( $this->_database );		
					
					// Build publication path
					$base_path = $this->_pubconfig->get('webpath');
					$path = $helper->buildPath($pid, $vid, $base_path, '', 1);
					
					// Delete all files
					if (is_dir($path)) {
						JFolder::delete($path);
					}
					
					// Delete access accosiations
					$pAccess = new PublicationAccess( $this->_database );
					$pAccess->deleteGroups($vid);
					
					// Delete audience
					$pAudience = new PublicationAudience( $this->_database );
					$pAudience->deleteAudience($vid);
				
					// Delete publication existence
					if ($pub->versions == 0) 
					{
						$objP->delete($pid);
						$objP->deleteExistence($pid);
						$url  = JRoute::_($route);	
						
						// Delete related publishing activity from feed		
						$objAA = new ProjectActivity( $this->_database );
						$objAA->deleteActivityByReference($this->_project->id, $pid, 'publication');	
					}
					
					// Add activity
					$action  = JText::_('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_DRAFT_DELETED');
					$action .= ' '.$vlabel.' ';
					$action .=  JText::_('PLG_PROJECTS_PUBLICATIONS_OF_PUBLICATION').' "'.$pubtitle.'"';
					
					$aid = $objAA->recordActivity( $this->_project->id, $this->_uid, 
						   $action, $pid, '', '', 'publication', 0 );
					
					$this->_msg = JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_DRAFT_DELETED');
				}
			}
		}
		else 
		{	
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'publications',
					'name'=>'cancel'
				)
			);

			// Output HTML
			$view->option 			= $this->_option;
			$view->database 		= $this->_database;
			$view->project 			= $this->_project;
			$view->authorized 		= $this->_authorized;
			$view->uid 				= $this->_uid;
			$view->pid 				= $pid;
			$view->version 			= $version;
			$view->pub 				= $pub;
			$view->publishedCount 	= $publishedCount;
			$view->task 			= $this->_task;
			$view->config 			= $this->_config;	
			$view->pubconfig 		= $this->_pubconfig;
			$view->ajax 			= $ajax;
			$view->route			= $route;
			$view->url 				= $url;
			$view->title		  	= $this->_area['title'];

			// Get messages	and errors	
			$view->msg = $this->_msg;
			if ($this->getError()) 
			{
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		
		// Pass success or error message
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		elseif (isset($this->_msg) && $this->_msg) 
		{
			$this->_message = array('message' => $this->_msg, 'type' => 'success');
		}
		
		$url.= $version != 'default' ? '?version='.$version : '';	
		$this->_referer = $url;
		return;
	
	}

	//----------------------------------------
	// Process steps
	//----------------------------------------
	
	/**
	 * Edit content
	 * 
	 * @return     string
	 */
	protected function _editContent() 
	{
		// Incoming
		$pid 		= JRequest::getInt( 'pid', 0 );
		$vid 		= JRequest::getInt( 'vid', 0 );
		
		$ajax 		= JRequest::getInt('ajax', 0);
		$no_html 	= JRequest::getInt('no_html', 0);
		$move 		= JRequest::getInt('move', 0);
		$role 		= JRequest::getInt('role', 0);
		
		$item 		= urldecode(JRequest::getVar( 'item', '' ));
		$type 		= strtolower(array_shift(explode('::', $item)));
		$item 		= array_pop(explode('::', $item));

		if (!$vid || !$pid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
		}
		if (!$item) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
		}

		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'edititem'
			)
		);

		// Get attachment information
		$row= new PublicationAttachment( $this->_database );
		$row->loadAttachment($vid, $item );
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);	

		$view->row 		= $row;
		$view->item 	= $item;
		$view->type 	= $type;
		$view->role		= $role;
		$view->vid 		= $vid;
		$view->pid 		= $pid;
		$view->ajax 	= $ajax;
		$view->move 	= $move;
		$view->no_html 	= $no_html;
		$view->option 	= $this->_option;
		$view->project 	= $this->_project;	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_message) ? $this->_message : '';
		return $view->loadTemplate();	
	}
	
	/**
	 * Save content
	 * 
	 * @return     string
	 */
	protected function _saveContent() 
	{
		// Incoming
		$pid 		= JRequest::getInt( 'pid', 0 );
		$vid 		= JRequest::getInt( 'vid', 0 );
		
		$ajax 		= JRequest::getInt('ajax', 0);
		$no_html 	= JRequest::getInt('no_html', 0);
		$move 		= JRequest::getInt('move', 0);
		$title 		= JRequest::getVar('title', '');
		$role 		= JRequest::getInt('role', 0);
		
		$item 		= urldecode(JRequest::getVar( 'item', '' ));
		$type 		= strtolower(array_shift(explode('::', $item)));
		$item 		= array_pop(explode('::', $item));
		
		if (!$vid || !$pid || !$item) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
		}
		
		// Get version label
		$row = new PublicationVersion( $this->_database );
		$row->load($vid);
		$version = $row->version_number;
				
		// Save any changes to selections/ordering
		$selections = JRequest::getVar( 'selections', '', 'post' );
		$selections = $this->_parseSelections($selections);
		$this->_processContent( $pid, $vid, $selections, $role );

		$now = date( 'Y-m-d H:i:s' );
		
		$objPA = new PublicationAttachment( $this->_database );
		if ($objPA->loadAttachment( $vid, $item )) 
		{
			if ($title && $objPA->title != $title) 
			{
				$objPA->modified = $now;
				$objPA->modified_by = $this->_uid;
			}
			$objPA->title = $title;
		}
		else 
		{
			$objPA = new PublicationAttachment( $this->_database );	
			$objPA->publication_id = $pid;
			$objPA->publication_version_id = $vid;
			$objPA->path = $item;
			$objPA->type = $type;
			$objPA->vcs_hash = $vcs_hash;
			$objPA->created_by = $this->_uid;
			$objPA->created = date( 'Y-m-d H:i:s' );
			$objPA->title = $title ? $title : '';
			$objPA->role = $role;
		}

		// Pass success or error message
		if ($objPA->store()) 
		{
			$this->_message = array('message' => JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ITEM_SAVED'), 'type' => 'success');
		}
		else 
		{
			$this->_message = array('message' => JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_SAVING_ITEM'), 'type' => 'error');
		}

		// Redirect 
		$url = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($url . a . 'pid=' . $pid);
		
		$url .= '?section=content&primary='.$role;
		$url .= '&version='.$version;
		$url .= $move ? '&move=' . $move : '';
		
		// Redirect
		$this->_referer = $url;
		return;
	}
	
	/**
	 * Show content item full info (AJAX)
	 * 
	 * @return     string
	 */
	protected function _loadContentItem()
	{
		// Incoming
		$pid 	= JRequest::getInt( 'pid', 0 );
		$vid 	= JRequest::getInt( 'vid', 0 );
		$role 	= JRequest::getInt('role', 0);
		$move 	= JRequest::getInt('move', 0);
		$item 	= urldecode(JRequest::getVar( 'item', '' ));
		
		$type = strtolower(array_shift(explode('::', $item)));
		$item = array_pop(explode('::', $item));
		$layout = $type == 'file' ? 'default' : $type;
		$hash = '';
		
		if (!$type || !$item)
		{
			return '<p class="error">' . JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_LOAD_ITEM') . '</p>';
		}
		
		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id) 
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
		}
				
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'contentitem',
				'layout' => $layout
			)
		);
		
		// Get attachment info
		$view->att = new PublicationAttachment( $this->_database );
		$view->att->loadAttachment($vid, $item, $type );
		
		// Get helper
		$projectsHelper = new ProjectsHelper( $this->_database );
				
		// File content
		if ($type == 'file')
		{
			// Get projects helper
			$projectsHelper = new ProjectsHelper( $this->_database );
			$view->revision = '';

			if ($this->_project->provisioned == 1)
			{
				$project_path = '';
			}
			else
			{
				// Get project file path
				$project_path = ProjectsHelper::getProjectPath($this->_project->alias, 
						$this->_config->get('webpath'), $this->_config->get('offroot', 0));

				// Git path	
				$gitpath = $this->_config->get('gitpath', '/opt/local/bin/git');

				// Revision info for files
				if ($type == 'file' && is_dir($project_path)) 
				{
					$view->revision = $projectsHelper->showGitInfo($gitpath, $project_path, $view->att->vcs_hash, $item);
				}			
			}
			$view->path 	= $project_path;
		}
		
		// Database content
		if ($type == 'data')
		{
			$view->data = new ProjectDatabase($this->_database);
			$view->data->loadRecord($item);
		}
		
		// Wiki content
		if ($type == 'note')
		{			
			$masterscope = 'projects' . DS . $this->_project->alias . DS . 'notes';
			$group = $this->_config->get('group_prefix', 'pr-') . $this->_project->alias;
			
			$view->note = $projectsHelper->getSelectedNote($item, $group, $masterscope);
		}
		
		// Build pub url
		$view->route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
		
		$view->project 	= $this->_project;
		$view->option 	= $this->_option;
		$view->pid 		= $pid;
		$view->vid 		= $vid;
		$view->item 	= $item;
		$view->canedit 	= (!is_object($this->_project) or !$this->_project->id) ? 0 : 1;
		$view->move 	= $move;
		$view->role		= $role;
		
		return $view->loadTemplate();
		
	}
	
	/**
	 * Show content info (AJAX)
	 * 
	 * @return     string
	 */
	protected function _showContentInfo() 
	{
		// Incoming
		$pid 	= JRequest::getInt( 'pid', 0 );
		$vid 	= JRequest::getInt( 'vid', 0 );
		$role 	= JRequest::getInt('role', 0);
		$item 	= urldecode(JRequest::getVar( 'item', '' ));
		$html   = '';
		
		$type = strtolower(array_shift(explode('::', $item)));
		$item = array_pop(explode('::', $item));
		$hash = '';
		
		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id) 
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
		}
		
		// Get attachment info
		$objPA = new PublicationAttachment( $this->_database );
		if ($objPA->loadAttachment($vid, $item, $type )) 
		{
			$html .= $objPA->title ? '"' . $objPA->title . '"' : JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
			$hash  = $objPA->vcs_hash;
		}
		else 
		{
			$html .= JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
		}
		
		// Get projects helper
		$projectsHelper = new ProjectsHelper( $this->_database );
		
		// Get project file path
		$project_path = ProjectsHelper::getProjectPath($this->_project->alias, 
				$this->_config->get('webpath'), $this->_config->get('offroot', 0));
		
		// Git path	
		$gitpath = $this->_config->get('gitpath', '/opt/local/bin/git');

		// Revision info
		if ($type == 'file' && is_dir($project_path)) 
		{
			$revision = $projectsHelper->showGitInfo($gitpath, $project_path, $hash, $item);
			$html .= $revision ? ' &middot; '.$revision : '';
		}

		return $html ? $html : '.';
	}
	
	/**
	 * Show content options (AJAX)
	 * 
	 * @return     string
	 */
	protected function _showOptions() 
	{
		// Incoming
		$pid 	= JRequest::getInt( 'pid', 0 );
		$vid 	= JRequest::getInt( 'vid', 0 );
		$picked = JRequest::getVar( 'serveas', '' );
		$base 	= JRequest::getVar( 'base', '' );
		
		// Contribute process outside of projects
		if (!is_object($this->_project) or !$this->_project->id) 
		{
			$this->_project = new Project( $this->_database );
			$this->_project->provisioned = 1;
		}
		
		// Instantiate pub attachment
		$objPA = new PublicationAttachment( $this->_database );	
		
		// Get selections
		$selections = JRequest::getVar( 'selections', '');
		$selections = $this->_parseSelections($selections);
		
		// Allowed choices
		$options = array('download', 'tardownload', 'inlineview');
		
		// Check if selections are the same as in another publication
		$used = $objPA->checkUsed($base, $selections, $this->_project->id, $pid);
		$duplicateVersion = NULL;	
		
		// Get original content
		$original_serveas = '';
		if ($vid) 
		{
			$original = $objPA->getAttachments($vid, $filters = array('role' => 1));
			if ($original) 
			{
				$params = new JParameter( $original[0]->params );
				$original_serveas = $params->get('serveas');	
			}
		
			// Check against duplication
			$info = array();
			if ($base == 'files' && !empty($selections['files']))
			{
				foreach ($selections['files'] as $file) 
				{
					if ($objPA->loadAttachment($vid, urldecode($file), 'file' )) 
					{
						$finfo = array();
						$finfo['path'] = $objPA->path;
						$finfo['hash'] = $objPA->vcs_hash;
						$info[] = $finfo;
					}
				}	
			}
			elseif ($base == 'databases' && !empty($selections['data']))
			{	
				foreach($selections['data'] as $data) 
				{
					if ($objPA->loadAttachment($vid, $data, 'data' )) 
					{
						$finfo = array();
						$finfo['object_name'] = $objPA->object_name;
						$finfo['object_revision'] = $objPA->object_revision;
						$info[] = $finfo;
					}
				}
			}

			$count = isset($selections['count']) ? $selections['count'] : 0;

			// Look for duplicate version
			$duplicateVersion = $objPA->checkVersionDuplicate($count, $info, $pid, $vid, $base);		
		}
		
		if ($base == 'files')
		{			
			// Formats that can be previewed via Google viewer
			$docs 	= array('pdf', 'doc', 'docx', 'xls', 'xlsx', 
				'ppt', 'pptx', 'pages', 'ai', 
				'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'svg'
			);
			
			$html5video = array('mp4','m4v','webm','ogv'); // formats for HTML5 video

			// Required
			ximport('Hubzero_Content_Mimetypes');
			
			// Allow viwing files via Google Doc viewer?
			$googleView	= $this->_params->get('googleview');

			// Determine how to serve content
			$serveas = 'download';
			$choices = array();		
			if (!empty($selections)) 
			{	
				// Files
				if (!empty($selections['files']))
				{
					$count = count($selections['files']);

					// Get file mimetype
					$mt = new Hubzero_Content_Mimetypes();
					$mimetypes = array();

					foreach($selections['files'] as $file) 
					{
						$mtype = $mt->getMimeType(urldecode($file));
						$mimetypes[] = strtolower(array_shift(explode('/', $mtype)));
					}

					// If one file, determine how to serve (to be extended)
					if ($count == 1) 
					{
						$ext = strtolower(array_pop(explode('.', basename($selections['files'][0]))));
						
						// Some files can be viewed inline
						if (in_array('video', $mimetypes) 
							|| in_array('audio', $mimetypes) 
							|| in_array('image', $mimetypes)) 
						{
							$serveas = $original_serveas ? $original_serveas : 'inlineview';
							
							// Offer choice
							$choices[] = 'inlineview';
							$choices[] = 'download';
						}
						elseif ($googleView && in_array(strtolower($ext), $docs))
						{
							$serveas = $original_serveas ? $original_serveas : 'download';
							
							// Offer choice
							$choices[] = 'download';
							$choices[] = 'inlineview';
						}
						else 
						{
							$serveas = 'download';
						}
					}
					else 
					{
						// More than 1 file
						$serveas = 'tardownload';
					}				
				}			
			}
		}
		else
		{
			$serveas = 'external';
			$choices = array();
		}
				
		// Something got picked?
		if ($picked && in_array($picked, $options)) 
		{
			$serveas = $picked;
		}
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'primaryoptions'
			)
		);
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);	
		
		$view->selections 	= $selections;
		$view->serveas 		= $serveas;
		$view->choices 		= $choices;
		$view->pid 			= $pid;
		$view->picked 		= $picked;
		$view->duplicateV 	= $duplicateVersion;
		$view->used 		= $used;
		$view->base 		= $base;
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	/**
	 * Process content
	 * 
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      array  		$selections
	 * @param      integer  	$primary
	 * @param      string  		$secret
	 * @param      integer  	$state
	 * @param      integer  	$newpub			Is this a new publication?
	 * @param      boolean  	$update_hash
	 *
	 * @return     integer
	 */
	protected function _processContent( $pid, $vid, $selections, $primary, $secret = '', $state = 0, $newpub = 0, $update_hash = 1 ) 
	{
		// Incoming
		$serveas = JRequest::getVar('serveas', '');
		
		// Get project file path
		$fpath = ProjectsHelper::getProjectPath($this->_project->alias, 
				$this->_config->get('webpath'), $this->_config->get('offroot'));
		$added = 0;
		
		$objPA = new PublicationAttachment( $this->_database );
		
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $vid, $pid);
		
		// Get original content
		$filters = array();
		$filters['select'] = 'a.path, a.type, a.object_name';
		$filters['role'] = $primary ? 1 : '0';
		$original = $objPA->getAttachments($vid, $filters);	
				
		$i = 1;
		
		// Save data attachments
		if (isset($selections['data']) && count($selections['data']) > 0 ) 
		{
			$database_name = $selections['data'][0];
			$dbVersion = NULL;
			
			// Get database object and load record
			$objData = new ProjectDatabase($this->_database);
			$objData->loadRecord($database_name);
			
			// Get databases plugin
			JPluginHelper::importPlugin( 'projects', 'databases');
			$dispatcher =& JDispatcher::getInstance();
			
			// Original database not found
			if (!$objData->id)
			{
				if ($newpub == 1)
				{
					// Can't proceed
					return false;
				}
				else
				{
					// Original got deleted, can't do much
					return true;
				}
			}
						
			// Build publication path
			$base_path = $this->_pubconfig->get('webpath');
			$publishPath = $helper->buildPath($pid, $vid, $base_path, 'data', 1);
			$pPath = JRoute::_('index.php?option=com_publications' . a . 'id=' . $pid) 
				. '/?vid=' . $vid . a . 'task=serve';
			
			// Create new version path
			if (!is_dir( $publishPath )) 
			{
				if (!JFolder::create( $publishPath, 0777 )) 
				{
					$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
					return Hubzero_View_Helper_Html::error($this->getError());
				}
			}
			
			// First-time clone
			if ($newpub)
			{
				$result = $dispatcher->trigger( 'clone_database', array( $database_name, $this->_project, $pPath) );
				$dbVersion = $result && isset($result[0]) ? $result[0] : NULL;
				
				// Failed to clone
				if (!$dbVersion)
				{
					$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_FAILED_DB_CLONE') );
					return false;
				}
			}
						
			// Save attachment data
			if ($objPA->loadAttachment($vid, $database_name, 'data')) 
			{
				$rtime = $objPA->modified ? strtotime($objPA->modified) : NULL;
				if ($objPA->object_id != $objData->id || strtotime($objData->updated) > $rtime )
				{
					// New database instance - need to clone again and get a new version number
					$result = $dispatcher->trigger( 'clone_database', array( $database_name, $this->_project, $pPath) );
					$dbVersion = $result && isset($result[0]) ? $result[0] : NULL;
					$objPA->modified_by = $this->_uid;
					$objPA->modified = date( 'Y-m-d H:i:s' );					
				}
				else
				{
					// No changes
					$dbVersion = $objPA->object_revision;
				}
			}
			else 
			{
				$objPA = new PublicationAttachment( $this->_database );
				$objPA->publication_id = $pid;
				$objPA->publication_version_id = $vid;
				$objPA->type = 'data';
				$objPA->created_by = $this->_uid;
				$objPA->created = date( 'Y-m-d H:i:s' );	
			}			
			
			// We do need a revision number!
			if (!$dbVersion)
			{
				return false;
			}
			
			// NEW determine accompanying files and copy them in the right location				
			$this->_publishDataFiles($objData, $publishPath);
			
			// Save object information
			$objPA->object_id   = $objData->id;
			$objPA->object_name = $database_name;
			$objPA->object_revision = $dbVersion;
			
			// Build link path
			$objPA->path = 'dataviewer' . DS . 'view' . DS . 'publication:dsl' . DS . $database_name . DS . '?v=' . $dbVersion;
					
			$objPA->ordering = $i;
			$objPA->role = $primary;
			$objPA->title = $objPA->title ? $objPA->title : $objData->title;
			$objPA->params = $primary  == 1 && $serveas ? 'serveas='.$serveas : $objPA->params;
			
			if ($objPA->store()) 
			{
				$i++;
				$added++;
			}			
		}
		
		// Save note attachments
		if (isset($selections['notes']) && count($selections['notes']) > 0) 
		{
			// Get helper
			$projectsHelper = new ProjectsHelper( $this->_database );
		
			$masterscope = 'projects' . DS . $this->_project->alias . DS . 'notes';
			$group = $this->_config->get('group_prefix', 'pr-') . $this->_project->alias;
			
			// Attach every selected file
			foreach ($selections['notes'] as $pageId) 
			{
				// get project note						
				$note = $projectsHelper->getSelectedNote($pageId, $group, $masterscope);
			
				if (!$note)
				{
					// Can't proceed
					continue;
				}
							
				if ($objPA->loadAttachment($vid, $pageId, 'note')) 
				{
					$objPA->modified_by = $this->_uid;
					$objPA->modified = date( 'Y-m-d H:i:s' );
				}
				else 
				{
					$objPA = new PublicationAttachment( $this->_database );
					$objPA->publication_id 			= $pid;
					$objPA->publication_version_id 	= $vid;
					$objPA->path 					= '';
					$objPA->type 					= 'note';
					$objPA->created_by 				= $this->_uid;
					$objPA->created 				= date( 'Y-m-d H:i:s' );
				}
				
				// Save object information
				$objPA->object_id   	= $pageId;
				$objPA->object_name 	= $note->pagename;
				$objPA->object_revision = $note->version;
				$objPA->object_instance = $note->instance;
			
				$objPA->ordering 		= $i;
				$objPA->role 			= $primary;
				$objPA->title 			= $note->title;
				$objPA->params 			= $primary  == 1 && $serveas ? 'serveas='.$serveas : $objPA->params;
			
				if ($objPA->store()) 
				{
					$i++;
					$added++;
				}	
			}
		}
				
		// Save file attachments
		if (isset($selections['files']) && count($selections['files']) > 0) 
		{
			// Git helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new ProjectsGitHelper(
				$this->_config->get('gitpath', '/opt/local/bin/git'), 
				$this->_uid
			);
			
			// Attach every selected file
			foreach ($selections['files'] as $file) 
			{
				$path = urldecode($file);
				
				// Get Git hash				
				$vcs_hash = $this->_git->gitLog($fpath, urldecode($file), '', 'hash');

				if ($objPA->loadAttachment($vid, $path, 'file')) 
				{
					// Update only if no hash recorded (or update requested)
					$objPA->vcs_hash = $objPA->vcs_hash && $update_hash == 0 ? $objPA->vcs_hash : $vcs_hash;
					$objPA->modified_by = $this->_uid;
					$objPA->modified = date( 'Y-m-d H:i:s' );
				}
				else 
				{
					$objPA = new PublicationAttachment( $this->_database );
					$objPA->publication_id = $pid;
					$objPA->publication_version_id = $vid;
					$objPA->path = $path;
					$objPA->type = 'file';
					$objPA->vcs_hash = $vcs_hash;
					$objPA->created_by = $this->_uid;
					$objPA->created = date( 'Y-m-d H:i:s' );	
				}
						
				$objPA->ordering = $i;
				$objPA->role = $primary;
				$objPA->title = $objPA->title ? $objPA->title : '';
				$objPA->params = $primary  == 1 && $serveas ? 'serveas='.$serveas : $objPA->params;				
				
				if ($objPA->store()) 
				{
					$i++;
					$added++;
					if ($secret && ($state != 1 || ($state == 1 && !$primary))) 
					{
						$this->_publishAttachment($pid, $vid, $objPA->path, $objPA->vcs_hash, $secret);							
					}
				}
			}
		}
		
		// Delete attachments if not selected
		if (count($original) > 0) 
		{
			foreach ($original as $old) 
			{
				// Go through file attachments
				if ($old->type == 'file')
				{
					if (!in_array(trim($old->path), $selections['files'])) 
					{
						$objPA->deleteAttachment($vid, $old->path, $old->type);	
						if ($secret) 
						{
							$this->_unpublishAttachment($pid, $vid, $old->path, $secret);	
						}
					}
				}
				elseif ($old->type == 'note')
				{
					if (!in_array(trim($old->object_id), $selections['notes']))
					{
						$objPA->deleteAttachment($vid, $old->object_id, $old->type);	
					}
				}
			}
		}
		
		return $added;		
	}
	
	/**
	 * Publish supporting database files
	 * 
	 * @param      object  	$objPD
	 *
	 * @return     boolean or error
	 */
	protected function _publishDataFiles($objPD, $publishPath = '') 
	{				
		if (!$objPD->id)
		{
			return false;
		}
		
		$repoPath = ProjectsHelper::getProjectPath($this->_project->alias, 
			$this->_config->get('webpath'), $this->_config->get('offroot')
		);
		
		// Get data definition
		$dd = json_decode($objPD->data_definition, true);

		$files 	 = array();
		$columns = array();

		foreach ($dd['cols'] as $colname => $col) 
		{
			if (isset($col['linktype']) && $col['linktype'] == "repofiles")
			{
				$dir = '';
				if (isset($col['linkpath']) && $col['linkpath'] != '')
				{
					$dir = $col['linkpath'];
				}
				$columns[$col['idx']] = $dir;
			}			
		}
		
		// No files to publish
		if (empty($columns))
		{
			return false;
		}
		
		$repoPath = $objPD->source_dir ? $repoPath . DS . $objPD->source_dir : $repoPath;
		$csv = $repoPath . DS . $objPD->source_file;
		
		$files = array();

		if (file_exists($csv) && ($handle = fopen($csv, "r")) !== FALSE)
		{
			// Check if expert mode CSV
			$expert_mode = false;
			$col_labels = fgetcsv($handle);
			$col_prop = fgetcsv($handle);
			$data_start = fgetcsv($handle);
			
			if (isset($data_start[0]) && $data_start[0] == 'DATASTART') 
			{
				$expert_mode = true;
			}

			// Non expert mode
			if (!$expert_mode) {
				$handle = fopen($path . '/' . $file, "r");
				$col_labels = fgetcsv($handle);
			}
						
			while ($r = fgetcsv($handle)) 
			{
				for ($i = 0; $i < count($col_labels); $i++) 
				{
					if (isset($columns[$i]))
					{
						if ((isset($r[$i]) && $r[$i] != ''))
						{
							$file = $columns[$i] ? $columns[$i] . DS . trim($r[$i]) : trim($r[$i]);
							if (file_exists( $repoPath . DS . $file))
							{
								$files[] = $file;
							}
						}
					}					
				}
			}
		}
		
		// Copy files from repo to published location
		if (!empty($files))
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			
			foreach ($files as $file)
			{
				if (!file_exists( $repoPath . DS . $file))
				{
					continue;
				}
				
				// If parent dir does not exist, we must create it
				if (!file_exists(dirname($publishPath . DS . $file))) 
				{
					JFolder::create(dirname($publishPath . DS . $file));
				}
				
				JFile::copy($repoPath . DS . $file, $publishPath . DS . $file);
				
				// Get file extention
				$ext = explode('.', $file);
				$ext = count($ext) > 1 ? end($ext) : '';
				
				// Image formats
				$image_formats = array('png', 'gif', 'jpg', 'jpeg', 'tiff', 'bmp');
				
				// Image file?
				if (!in_array(strtolower($ext), $image_formats))
				{
					continue;
				}
				
				$ih = new ProjectsImgHandler();
				
				// Generate thumbnail
				$thumb 	= PublicationsHtml::createThumbName($file, '_tn', $extension = 'gif');
				$tpath  = dirname($thumb) == '.' ? $publishPath : $publishPath . DS . dirname($thumb);
				JFile::copy($repoPath . DS . $file, $publishPath . DS . $thumb);
				
				$ih->set('image', basename($thumb));
				$ih->set('overwrite',true);
				$ih->set('path', $tpath . DS );
				$ih->set('maxWidth', 100);
				$ih->set('maxHeight', 60);
				$ih->process();
				
				// Generate medium image
				$med 	= PublicationsHtml::createThumbName($file, '_medium', $extension = 'gif');
				$mpath  = dirname($med) == '.' ? $publishPath : $publishPath . DS . dirname($med);
				JFile::copy($repoPath . DS . $file, $publishPath . DS . $med);
				
				$ih->set('image', basename($med));
				$ih->set('overwrite',true);
				$ih->set('path', $mpath . DS );
				$ih->set('maxWidth', 800);
				$ih->set('maxHeight', 800);
				$ih->set('quality', 75);
				$ih->set('force', false);
				$ih->process();
			}
		}
	}
	
	/**
	 * Unpublish content attachment
	 * 
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      string  		$fpath
	 * @param      string  		$secret
	 *
	 * @return     boolean or error
	 */
	protected function _unpublishAttachment($pid, $vid, $fpath, $secret) 
	{
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $vid, $pid);
		
		// Remove files that got unselected from a finalized draft
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		// Build publication path
		$base_path = $this->_pubconfig->get('webpath');
		$newpath = $helper->buildPath($pid, $vid, $base_path, $secret, 1);
		
		if (is_dir( $newpath )) 
		{
			$file =  $newpath. DS .$fpath;
			if (is_file($file)) 
			{
				JFile::delete($file);
			}
		}
		
		return true;		
	}
	
	/**
	 * Publish content attachment
	 * 
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      string  		$fpath
	 * @param      string  		$hash
	 * @param      string  		$secret
	 *
	 * @return     boolean or error
	 */
	protected function _publishAttachment ($pid, $vid, $fpath, $hash, $secret) 
	{
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $vid, $pid);
		
		// Get projects helper
		$projectsHelper = new ProjectsHelper( $this->_database );
		
		// Remove files that got unselected from a finalized draft
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		// Build publication path
		$base_path 	= $this->_pubconfig->get('webpath');
		$devpath 	= $helper->buildDevPath($this->_project->alias);
		$newpath 	= $helper->buildPath($pid, $vid, $base_path, $secret, 1);
		$gitpath 	= $this->_config->get('gitpath', '/opt/local/bin/git');
		
		// Create new version path
		if (!is_dir( $newpath )) 
		{
			if (!JFolder::create( $newpath, 0777 )) 
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return Hubzero_View_Helper_Html::error($this->getError());
			}
		}
					
		// Get latest commit hash
		$latest = $projectsHelper->showGitInfo($gitpath, $devpath, $hash, $fpath, 2);
		
		// If parent dir does not exist, we must create it
		if (!file_exists(dirname($newpath. DS .$fpath))) 
		{
			JFolder::create(dirname($newpath. DS .$fpath));
		}
		
		// Copy file if there (will be at latest revision)
		if (is_file($devpath. DS .$fpath)) 
		{
			if (!is_file($newpath. DS .$fpath) || $hash != $latest['hash']) 
			{
				JFile::copy($devpath. DS .$fpath, $newpath. DS .$fpath);
			}			
		}
		
		return true;			
	}
	
	/**
	 * Publish attachments
	 * 
	 * @param      object  		$row
	 * @param      string  		$which
	 *
	 * @return     integer
	 */
	protected function _publishAttachments($row, $which = 'all') 
	{
		// Transfer file attachments from project files Git repo to a webroot directory
		
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $row->id, $row->publication_id);
		
		$published = 0;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		// Get projects helper
		$projectsHelper = new ProjectsHelper( $this->_database );
		
		// Set filters
		$filters = array();
		if ($which != 'all') 
		{
			$filters['role'] = $which == 'primary' ? 1 : '0';
		}
		
		// Get attachments
		$pContent = new PublicationAttachment( $this->_database );
		$attachments = $pContent->getAttachments( $row->id, $filters);

		// Build publication paths
		$base_path 	= $this->_pubconfig->get('webpath');
		$devpath 	= $helper->buildDevPath($this->_project->alias);
		$newpath 	= $helper->buildPath($row->publication_id, $row->id, $base_path, $row->secret, 1);
		$gitpath 	= $this->_config->get('gitpath', '/opt/local/bin/git');
		
		// Create new version path
		if (!is_dir( $newpath )) 
		{
			if (!JFolder::create( $newpath, 0777 )) 
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return Hubzero_View_Helper_Html::error($this->getError());
			}
		}
		
		// Check if we need to store file hash
		$fields = $this->_database->getTableFields('jos_publication_attachments');
		if (!array_key_exists('content_hash', $fields['jos_publication_attachments'] )) 
		{
			$this->_database->setQuery( "ALTER TABLE `jos_publication_attachments` 
				ADD `content_hash` varchar(255) NOT NULL" );
			if (!$this->_database->query()) 
			{
				echo $this->_database->getErrorMsg();
				return false;
			}
		}		
					
		// Copy content & attachment info
		if ($attachments && count($attachments) > 0) 
		{
			foreach($attachments as $att) 
			{
				if ($att->type == 'data') 
				{
					$database_name = $att->object_name;
					$database_rev  = $att->object_revision;
					
					// Build publication path
					$publishPath = $helper->buildPath($row->publication_id, $row->id, $base_path, 'data', 1);
					$pPath = JRoute::_('index.php?option=com_publications' . a . 'id=' . $row->publication_id) 
						. '/?vid=' . $row->id . a . 'task=serve';

					// Get database object and load record
					$objData = new ProjectDatabase($this->_database);
					$objData->loadRecord($database_name);
					
					if (!$objData->id)
					{
						// Can't do much
						break;
					}
					
					// Get last record update time
					$rtime = $att->modified ? strtotime($att->modified) : NULL;
					
					// Db updated, clone again
					if (strtotime($objData->updated) > $rtime)
					{
						// Get databases plugin
						JPluginHelper::importPlugin( 'projects', 'databases');
						$dispatcher =& JDispatcher::getInstance();
						
						// New database instance - need to clone again and get a new version number
						$result = $dispatcher->trigger( 'clone_database', array( $database_name, $this->_project, $pPath) );
						$dbVersion = $result && isset($result[0]) ? $result[0] : NULL;
						
						// Update attatchment record with new revision & path
						if ($dbVersion)
						{
							// Make sure all data files are in the right location
							$this->_publishDataFiles($objData, $publishPath);
							
							$objAtt = new PublicationAttachment( $this->_database );
							$objAtt->load($att->id);
							$objAtt->path = 'dataviewer' . DS . 'view' . DS . 'publication:dsl' . DS . $database_name . DS . '?v=' . $dbVersion;
							$objAtt->object_revision = $dbVersion;
							$objAtt->modified_by = $this->_uid;
							$objAtt->modified = date( 'Y-m-d H:i:s' );
							$objAtt->store();
							$published++;
						}
					}
				}
				
				if ($att->type == 'file') 
				{					
					// Get latest commit hash
					$latest = $projectsHelper->showGitInfo($gitpath, $devpath, $att->vcs_hash, $att->path, 2);
					
					// If parent dir does not exist, we must create it
					if (!file_exists(dirname($newpath. DS .$att->path))) 
					{
						JFolder::create(dirname($newpath. DS .$att->path));
					}
				
					// Copy file if there (will be at latest revision)
					if (is_file($devpath. DS .$att->path)) 
					{
						JFile::copy($devpath. DS .$att->path, $newpath. DS .$att->path);
					}
					else 
					{
						// Check out revision when file was added
						chdir($devpath);
						exec($gitpath.' checkout '.$att->vcs_hash.' '.escapeshellarg($att->path).' 2>&1', $out);
						if (file_exists($devpath. DS .$att->path)) 
						{
							JFile::copy($devpath. DS .$att->path, $newpath. DS .$att->path);
						}
						// Check back latest revision
						exec($gitpath.' checkout '.$latest['hash'].' '.escapeshellarg($att->path).' 2>&1', $out);
					}
												
					// Copy succeeded?
					if (is_file($newpath. DS .$att->path)) 
					{
						$objAtt = new PublicationAttachment( $this->_database );
						$objAtt->load($att->id);
						$objAtt->vcs_hash = $latest['hash'];
						$objAtt->modified_by = $this->_uid;
						$objAtt->modified = date( 'Y-m-d H:i:s' );
						if (array_key_exists('content_hash', $fields['jos_publication_attachments'] ))
						{
							$file_hash = hash_file('sha256', $newpath. DS .$att->path);
							$objAtt->content_hash = $file_hash;
							
							// Create hash file
							$hfile =  $newpath . DS .$att->path . '.hash';
							if (!is_file($hfile))
							{
								$handle = fopen($hfile, 'w');
								fwrite($handle, $file_hash);
								fclose($handle);
								chmod($hfile, 0644);
							}
						}
						$objAtt->store();
						$published++;
					}
				}
			}
		}
		
		return $published;
	}
	
	/**
	 * Process authors
	 * 
	 * @param      integer  	$vid
	 * @param      array  		$selections
	 *
	 * @return     boolean
	 */
	protected function _processAuthors( $vid, $selections ) 
	{		
		$pAuthor = new PublicationAuthor( $this->_database );
		$now = date( 'Y-m-d H:i:s' );
		
		// Get original authors
		$oauthors = $pAuthor->getAuthors($vid, 2);

		// Save/update authors
		$order = 1;
		foreach ($selections as $sel) 
		{
			if ($sel == '' || intval($sel) == 0) {
				continue;
			}
			if ($pAuthor->loadAssociationByOwner($sel, $vid)) 
			{
				if ($pAuthor->ordering != $order) {
					$pAuthor->modified = $now;
					$pAuthor->modified_by = $this->_uid;
				}
				
				$pAuthor->ordering = $order;
				$pAuthor->status = 1;
				if ($pAuthor->updateAssociationByOwner()) 
				{
					$order++;
				}
			}
			else 
			{
				$pAuthor = new PublicationAuthor( $this->_database );

				$profile = $pAuthor->getProfileInfoByOwner($sel);
				$invited = $profile->invited_name ? $profile->invited_name : $profile->invited_email;
				
				$firstName = '';
				$lastName  = '';
				
				$pAuthor->project_owner_id = $sel;
				$pAuthor->publication_version_id = $vid;
				$pAuthor->user_id = $profile->uidNumber ? $profile->uidNumber : 0;
				$pAuthor->ordering = $order;
				$pAuthor->status = 1;
				$pAuthor->organization = $profile->organization ? $profile->organization : '';
				$pAuthor->name = $profile && $profile->name ? $profile->name : $invited;
				$pAuthor->firstName = $firstName;
				$pAuthor->lastName = $lastName;
				$pAuthor->created = $now;
				$pAuthor->created_by = $this->_uid;
				if (!$pAuthor->createAssociation()) 
				{
					continue;
				}
				else 
				{
					$order++;
				}
			}				
		}
		
		// Delete authors if not selected
		if (count($oauthors) > 0) 
		{
			foreach($oauthors as $old) 
			{
				if (!in_array($old->project_owner_id, $selections)) 
				{
					$pAuthor->deleteAssociationByOwner($old->project_owner_id, $vid);	
				}
			}
		}
		return true;
	}
	
	/**
	 * Show author (AJAX)
	 *
	 * @return     string (html)
	 */
	protected function _showAuthor() 
	{	
		// Incoming
		$uid 		= JRequest::getInt('uid', 0);
		$vid 		= JRequest::getInt('vid', 0);
		$owner		= JRequest::getInt('owner', 0);
		$move 		= JRequest::getInt('move', 0);
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'author'
			)
		);
		
		// Get author information
		$pAuthor 		= new PublicationAuthor( $this->_database );
		$view->author 	= $pAuthor->getAuthorByOwnerId($vid, $owner);
		$view->owner 	= $owner;		
		$view->order 	= $pAuthor->getCount($vid);
		
		// Build pub url
		$view->route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $this->_pid);
		
		$view->project 	= $this->_project;
		$view->pid 		= $this->_pid;
		$view->vid 		= $vid;
		$view->option 	= $this->_option;
		$view->move 	= $move;
		$view->canedit	= 1;
		return $view->loadTemplate();		
	}
	
	/**
	 * Edit author view
	 *
	 * @return     string
	 */
	protected function _editAuthor() 
	{
		// AJAX		
		// Incoming
		$uid 		= JRequest::getInt('uid', 0);
		$vid 		= JRequest::getInt('vid', 0);
		$pid 		= JRequest::getInt('pid', 0);
		$ajax 		= JRequest::getInt('ajax', 0);
		$no_html 	= JRequest::getInt('no_html', 0);
		$move 		= JRequest::getInt('move', 0);
		$owner 		= JRequest::getInt('owner', 0);
		$new 		= JRequest::getVar('new', '');
		
		if (!$vid || !$pid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_EDIT_AUTHOR'));
		}
		if (!$uid && !$owner && !$new) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_EDIT_AUTHOR'));
		}
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'editauthor'
			)
		);
		
		// Get author information
		$pAuthor = new PublicationAuthor( $this->_database );
		$view->author = $pAuthor->getAuthorByOwnerId($vid, $owner);
		
		if (!$view->author) 
		{
			if ($owner)
			{
				 $this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_EDIT_AUTHOR'));
			}
			else 
			{
				$view->author = new PublicationAuthor( $this->_database );
				$view->author->p_name 			= '';
				$view->author->p_organization 	= '';
				$view->author->invited_name 	= '';
				$view->author->invited_email 	= '';
				$view->author->givenName 		= '';
				$view->author->surname 			= '';
				$view->author->picture 			= '';
				$view->author->username 		= '';
				
				// Are we adding someone new?
				if ($new)
				{
					$newm = explode(',' , $new);
					$new  = trim($newm[0]);
					
					// Are we adding a registered user?
					$parts =  preg_split("/[(]/", $new);
					if (count($parts) == 2) 
					{
						$name = $parts[0];
						$uid = preg_replace('/[)]/', '', $parts[1]);
						$uid = is_numeric($uid) ? $uid : '';
					}
					elseif (intval($new))
					{
						$uid = $new;	
					}
					else
					{
						// Instantiate a new registration object
						ximport('Hubzero_Registration');
						$xregistration = new Hubzero_Registration();
						
						$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
						// Is this an email?
						if (preg_match($regex, strtolower($new))) 
						{
							$uid = $xregistration->getEmailId(strtolower($new));
							$view->author->invited_email = strtolower($new);
						}
						else 
						{
							// This must be a name
							$view->author->p_name = $new;
							$view->author->invited_name = $new;
						}
					}
					
					if ($uid)
					{
						// Owner already?
						$author = $pAuthor->getAuthorByUid($vid, $uid);
						
						if ($author)
						{
							$view->author 	= $author;
							$view->owner 	=  $author->project_owner_id;
						}
						else
						{
							$profile = Hubzero_User_Profile::getInstance($uid);
							$view->author->givenName 		= $profile->get('givenName');
							$view->author->surname 			= $profile->get('surname');
							$view->author->picture 			= $profile->get('picture');
							$view->author->username 		= $profile->get('username');
							$view->author->p_name 			= $profile->get('name');
							$view->author->p_organization 	= $profile->get('organization');							
						}						
					}					
				}
			}	
		}
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
		
		$view->uid 		= $uid;
		$view->vid 		= $vid;
		$view->pid 		= $pid;
		$view->owner 	= $owner;
		$view->ajax 	= $ajax;
		$view->move 	= $move;
		$view->no_html 	= $no_html;
		$view->option 	= $this->_option;
		$view->project 	= $this->_project;	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_message) ? $this->_message : '';
		return $view->loadTemplate();					
	}
		
	/**
	 * Save author info
	 *
	 * @return   void (redirect)
	 */
	protected function _saveAuthor() 
	{
		// Incoming
		$uid 		= JRequest::getInt(	'uid', 0);
		$vid 		= JRequest::getInt( 'vid', 0 );
		$pid 		= JRequest::getInt( 'pid', 0 );
		$email 		= JRequest::getVar( 'email', '', 'post' );
		$firstName 	= JRequest::getVar( 'firstName', '', 'post' );
		$lastName 	= JRequest::getVar( 'lastName', '', 'post' );
		$org 		= JRequest::getVar( 'organization', '', 'post' );
		$credit 	= JRequest::getVar( 'credit', '', 'post' );
		$move 		= JRequest::getInt( 'move', 0 ); 
		$owner 		= JRequest::getInt(	'owner', 0);
		$new 		= 0;
		$sendInvite = 0;
		
		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/'; 
		
		// Save any changes to selections/ordering
		$selections = JRequest::getVar('selections', '', 'post');
		$selections = explode("##", $selections);
		if (count($selections) > 0 && trim($selections[0]) != '') 
		{
			$this->_processAuthors($vid, $selections);
		}
		
		$now = date( 'Y-m-d H:i:s' );
		if (!$vid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_EDIT_AUTHOR'));
		}
		
		// Get owner class		
		$objO = new ProjectOwner( $this->_database );
		
		// Instantiate a new registration object
		ximport('Hubzero_Registration');
		$xregistration = new Hubzero_Registration();
		
		// Get current owners
		$owners = $objO->getIds($this->_project->id, 'all', 1);		
				
		// Save new owner (or find existing owner by uid/email)
		if (!$owner) 
		{
			$email = preg_match($regex, $email) ? $email : '';
			$name = $firstName . ' ' . $lastName;
			
			if ($email && !$uid)
			{
				// Do we have a registered user with this email?
				$uid = $xregistration->getEmailId($email);
			}
			
			// Check that profile exists
			if ($uid)
			{
				$profile = Hubzero_User_Profile::getInstance($uid);
				$uid = $profile->get('uidNumber') ? $uid : 0;				
			}
	
			if ($uid)
			{
				$owner = $objO->getOwnerId($this->_project->id, $uid);
			}
			elseif ($email)
			{
				$owner = $objO->checkInvited( $this->_project->id, $email );	
			}
			else 
			{
				$owner = $objO->checkInvitedByName( $this->_project->id, trim($name));
			}
			
			if ($owner && $objO->load($owner))
			{
				if ($email && $objO->invited_email != $email)
				{
					$sendInvite = 1;	
				}
				$objO->status = $objO->userid ? 1 : 0;
				$objO->invited_name = $objO->userid ? $objO->invited_name : $name;
				$objO->invited_email = $objO->userid ? $objO->invited_email : $email;
				$objO->store();
			}			
			elseif ($email || trim($name))
			{
				// Generate invitation code
				$code = ProjectsHtml::generateCode();
				$objO->projectid = $this->_project->id;
				$objO->userid = $uid;
				$objO->status = $uid ? 1 : 0;
				$objO->added = date( 'Y-m-d H:i:s' );
				$objO->role = 2;
				$objO->invited_email = $email;
				$objO->invited_name = $name;
				$objO->store();	
				$owner = $objO->id;
				$sendInvite = $email ? 1 : 0;	
			}
			
			$new = 1;
		}
		
		// Not part of team - throw an error
		if (!$owner) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_EDIT_AUTHOR'));
		}
		
		// Get version label
		$row = new PublicationVersion( $this->_database );
		$row->load($vid);
		$version = $row->version_number;
				
		// Get author information
		$pAuthor = new PublicationAuthor( $this->_database );
		$exists = 0;
		if ($pAuthor->loadAssociationByOwner( $owner, $vid )) 
		{
			$pAuthor->modified = $now;
			$pAuthor->modified_by = $this->_uid;
			$exists= 1;
		}
		else 
		{
			$pAuthor->created = $now;
			$pAuthor->created_by = $this->_uid;
			$pAuthor->publication_version_id = $vid;
			$pAuthor->project_owner_id = $owner;
			$pAuthor->user_id = intval($uid);
			$pAuthor->ordering = $pAuthor->getLastOrder($vid) + 1;
		}
		$pAuthor->status = 1;
				
		// Get info from user profile (if registered) or project owner profile (if invited)
		$profile = $pAuthor->getProfileInfoByOwner($owner);
				
		// Get default name
		$default_invited = $profile->invited_name 
		? $profile->invited_name : $profile->invited_email;
		$default_name = $profile && $profile->name ? $profile->name : $default_invited;
		$name = '';
		
		// Determine first and last names from default name
		if ($profile->uidNumber)
		{
			$nameParts 		= explode(" ", $profile->name);
			$part_lastname  = end($nameParts);
			$part_firstname = count($nameParts) > 1 ? $nameParts[0] : '';
		}
		else 
		{
			$nameParts 		= explode(" ", $profile->invited_name);
			$part_lastname  = end($nameParts);
			$part_firstname = count($nameParts) > 1 ? $nameParts[0] : '';
		}
		$default_firstname 	= $profile && $profile->givenName ? $profile->givenName : $part_firstname ;
		$default_lastname 	= $profile && $profile->surname ? $profile->surname : $part_lastname;
				
		$saved = 0;
		if (!$this->getError()) 
		{
			$pAuthor->organization = $org ? $org : $profile->organization;
			if (!$firstName && !$lastName)
			{
				$pAuthor->firstName = $default_firstname;
				$pAuthor->lastName = $default_lastname;
			}
			else 
			{
				$pAuthor->firstName = $firstName;
				$pAuthor->lastName = $lastName;
			}
			// Make up name from first and last name
			$name = $pAuthor->firstName . ' ' . $pAuthor->lastName;
			
			$pAuthor->name = $pAuthor->firstName && $pAuthor->lastName ? $name : $default_name;
			$pAuthor->credit = $credit;

			// Save new info
			if ($exists) {
				if ($pAuthor->updateAssociationByOwner()) 
				{
					$saved = 1;
				}
			}
			else 
			{
				if ($pAuthor->createAssociation()) 
				{
					$saved = 1;
				}	
			}	
		}
		
		// Update project owner (invited)
		if (!$new && !$profile->uidNumber && $objO->load($owner))
		{			
			$update = 0;
			$user   = 0;
			
			// Save email only if valid and new
			if ($email)
			{
				if (preg_match($regex, $email)) 
				{
					$invitee = $objO->checkInvited( $this->_project->id, $email );
									
					// Do we have a registered user with this email?
					$user = $xregistration->getEmailId($email);
					
					// Duplicate? - stop
					if ($invitee && $invitee != $owner)
					{
						// Stop
					}
					elseif (in_array($user, $owners))
					{
						// Stop
					}
					elseif ($email != $objO->invited_email)
					{
						$objO->invited_email = $email;
						$objO->userid = $objO->userid ? $objO->userid : $user;
						$update = 1;
						$sendInvite = 1;
					}
				}
			}
			if ($update || $name)
			{
				$objO->invited_name = $name;
				$objO->store();
			}			
		}
		
		// (Re)send email invitation
		if ($sendInvite && $email)
		{
			// TBD
		}
		
		// Pass success or error message
		if ($saved) 
		{
			$this->_message = array('message' => JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_INFO_SAVED'), 'type' => 'success');
		}
		elseif (!$this->getError()) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
		}
		if ($this->getError()) 
		{
			$this->_message = array('message' => $this->getError(), 'type' => 'error');
		}
		
		// Redirect 
		$url  = $this->_project->provisioned 
			  ? 'index.php?option=com_publications' . a . 'task=submit'
			  : 'index.php?option=com_projects' . a 
			  . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url  = JRoute::_($url . a . 'pid=' . $pid);
		$url .= '?section=authors';
		$url .= '&version='.$version;
		$url .= $move ? '&move=' . $move : '';
		
		// Redirect
		$this->_referer = $url;
		return;
	}
	
	/**
	 * Process metadata
	 * 
	 * @param      integer  	$rtype
	 *
	 * @return     string
	 */
	protected function _processMetadata( $rtype = 0 ) 
	{	
		// Incoming
		$rtype = $rtype ? $rtype : JRequest::getInt( 'rtype', 0 );
		$nbtag = JRequest::getVar( 'nbtag', array(), 'request', 'array' );
		$metadata = '';
		
		// Get custom areas, add wrapper tags, and compile into fulltext
		$cat = new PublicationCategory( $this->_database );
		$cat->load( $rtype );

		$fields = array();
		if (trim($cat->customFields) != '') 
		{
			$fs = explode("\n", trim($cat->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		}
		
		foreach ($nbtag as $tagname=>$tagcontent)
		{
			$tagcontent = trim(stripslashes($tagcontent));
			if ($tagcontent != '') 
			{
				$metadata .= "\n".'<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>'."\n";
			} else 
			{
				foreach ($fields as $f) 
				{
					if ($f[0] == $tagname && end($f) == 1) 
					{
						$this->setError( JText::sprintf('PLG_PROJECTS_PUBLICATIONS_REQUIRED_FIELD_CHECK', $f[1]) );
						return;
					}
				}
			}
		}		
		return $metadata;
	}
	
	/**
	 * Preview wiki
	 *
	 * @return     string (html)
	 */
	protected function _previewWiki()
	{
		// Incoming
		$raw  = JRequest::getVar( 'raw', '' );
		
		// Convert
		if ($raw) 
		{
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			
			// import the wiki parser
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => '',
				'pagename' => 'projects',
				'pageid'   => '',
				'filepath' => '',
				'domain'   => ''
			);
			$html = $p->parse( $raw, $wikiconfig );
			return $html ? $html : ProjectsHtml::showNoPreviewMessage();
		}
		else 
		{
			return ProjectsHtml::showNoPreviewMessage();
		}		
	}
	
	/**
	 * Process audience
	 * 
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 *
	 * @return     boolean
	 */
	protected function _processAudience( $pid, $vid ) 
	{
		// Incoming
		$sel    = JRequest::getVar( 'audience', '', 'post' );
		$noshow = JRequest::getVar( 'no_audience', false, 'post' );

		$picked = array();		
		$picked = explode('-', $sel);
		$result = 0;

		$pAudience = new PublicationAudience( $this->_database );
		if (!$pAudience->loadByVersion($vid)) 
		{
			$pAudience->publication_id = $pid;
			$pAudience->publication_version_id = $vid;
		}
		for ( $k = 0 ; $k <=5; $k++) 
		{
			$lev = 'level'.$k;
			$pAudience->$lev = ($noshow || !in_array ( $lev, $picked)) ? 0 : 1;
			if (in_array ( $lev, $picked)) {
				$result = 1;
			}
		}
		
		if ($noshow == false && $result == 0) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_NO_SELECTIONS') );
			return false;
		}
		
		$pAudience->created = date( 'Y-m-d H:i:s' );
		$pAudience->created_by = $this->_uid;
				
		if (!$pAudience->store()) 
		{
			$this->setError( $pAudience->getError() );
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	/**
	 * Show audience
	 *
	 * @return     string (html)
	 */
	protected function _showAudience() 
	{	
		// Incoming
		$sel    = JRequest::getVar( 'audience', '' );
		$noshow = JRequest::getVar( 'no_audience', false );
		
		$picked = array();		
		$picked = explode('-', $sel);
		
		$pAudience = new PublicationAudience( $this->_database );
		$ral = new PublicationAudienceLevel ( $this->_database );
		$levels = $ral->getLevels( 4, array(), 0 );
		$audience = array();
		$result = 0;
		
		// Build our object
		if (!empty($levels)) 
		{
			for($k=0; $k < count($levels); $k++) 
			{
				$label = 'label'.$k;
				$desc = 'desc'.$k;
				$lev = 'level'.$k;
				$pAudience->$label = $levels[$k]->title;
				$pAudience->$desc = $levels[$k]->description;
				if (in_array($lev, $picked)) 
				{
					$result = 1;
					$pAudience->$lev = 1;
				}
				else 
				{
					$pAudience->$lev = 0;
				}
			}
		}
		
		if ($result == 1) 
		{
		 	return PublicationsHtml::showSkillLevel($pAudience);
		}
		else 
		{
			return JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_NOT_SHOWN');
		}
	}
	
	/**
	 * Process gallery
	 * 
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      array		$selections
	 *
	 * @return     boolean
	 */
	protected function _processGallery( $pid, $vid, $selections ) 
	{
		$pScreenshot = new PublicationScreenshot( $this->_database );
		$now = date( 'Y-m-d H:i:s' );
		
		// Get original screenshots
		$originals = $pScreenshot->getScreenshots( $vid );
		
		// Get project file path		
		$fpath = ProjectsHelper::getProjectPath($this->_project->alias, 
				$this->_config->get('webpath'), 1);

		$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
		$from_path = $prefix.$fpath;
		
		// Get files plugin
		JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Get screenshot path
		$webpath = $this->_pubconfig->get('webpath');

		// Get publications helper
		$helper = new PublicationHelper( $this->_database );
		$gallery_path = $helper->buildPath($pid, $vid, $webpath, 'gallery');		
		
		if (isset($selections['files']) && count($selections['files']) > 0) 
		{
			$ordering = 1;
			foreach($selections['files'] as $file) 
			{
				$file = urldecode($file);
				
				// Git helper
				include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
				$this->_git = new ProjectsGitHelper(
					$this->_config->get('gitpath', '/opt/local/bin/git'), 
					0,
					$this->_config->get('offroot', 0) ? '' : JPATH_ROOT
				);

				// Get Git hash
				$hash = $this->_git->gitLog($fpath, $file, '' , 'hash');
				
				$src = $this->_createScreenshot ( $file, $hash, $from_path, $gallery_path, 'name' );
				
				if ($pScreenshot->loadFromFilename($file, $vid)) 
				{
					$pScreenshot->ordering = $ordering;
				}
				elseif ($src) 
				{
					$pScreenshot = new PublicationScreenshot( $this->_database );
					$pScreenshot->filename = $file;
					$pScreenshot->srcfile = $src;
					$pScreenshot->publication_id = $pid;
					$pScreenshot->publication_version_id = $vid;
					$pScreenshot->title = basename($file);
					$pScreenshot->created = $now;
					$pScreenshot->created_by = $this->_uid;
					$pScreenshot->ordering = $ordering;	
				}
				
				// Create publication thumbnail from first screenshot
				$pubthumb = $helper->getThumb($pid, $vid, $this->_pubconfig, true);	
				
				if ($pScreenshot->store()) 
				{
					$ordering++;
				}
			}
		}
		
		// Delete screenshots if not selected
		if (count($originals) > 0) 
		{	
			$selected = isset($selections['files']) && count($selections['files']) > 0	? $selections['files'] : array();
			$ih = new ProjectsImgHandler();
			jimport('joomla.filesystem.file');
			foreach($originals as $old) 
			{
				if (!in_array($old->filename, $selected)) 
				{
					$pScreenshot->deleteScreenshot($old->filename, $vid);
					
					// Clean up files
					$thumb = $ih->createThumbName($old->srcfile, '_tn', $extension = 'png');
					if (is_file(JPATH_ROOT.$gallery_path. DS .$old->srcfile)) 
					{
						JFile::delete(JPATH_ROOT.$gallery_path. DS .$old->srcfile);
					}	
					if (is_file(JPATH_ROOT.$gallery_path. DS .$thumb)) 
					{
						JFile::delete(JPATH_ROOT.$gallery_path. DS .$thumb);
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Edit screenshot
	 *
	 * @return     string (html)
	 */
	protected function _editScreenshot() 
	{
		// AJAX		
		// Incoming
		$ima 		= JRequest::getVar('ima', '');
		$vid 		= JRequest::getInt('vid', 0);
		$pid 		= JRequest::getInt('pid', 0);
		$ajax 		= JRequest::getInt('ajax', 0);
		$no_html 	= JRequest::getInt('no_html', 0);
		$move 		= JRequest::getInt('move', 0);
		$filename 	= basename($ima);
		
		if (!$vid || !$pid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE'));
		}
		if (!$ima) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE'));
		}
	
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'editimage'
			)
		);
		
		// Get screenshot path
		$webpath = $this->_pubconfig->get('webpath');

		// Get publications helper
		$helper = new PublicationHelper( $this->_database );
		$gallery_path = $helper->buildPath($pid, $vid, $webpath, 'gallery');	
		
		$ih = new ProjectsImgHandler();
		
		// Load screenshot info if any
		$pScreenshot = new PublicationScreenshot( $this->_database );		
		if ($pScreenshot->loadFromFilename($ima, $vid)) 
		{	
			$view->file = $pScreenshot->srcfile;
			$view->thumb = $ih->createThumbName($pScreenshot->srcfile, '_tn', $extension = 'png');
		}		
		elseif (!$this->getError()) 
		{		
			// Get project file path		
			$fpath =  ProjectsHelper::getProjectPath($this->_project->alias, 
					$this->_config->get('webpath'), 1);

			$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
			$from_path = $prefix.$fpath;

			// Get files plugin
			JPluginHelper::importPlugin( 'projects', 'files' );
			$dispatcher =& JDispatcher::getInstance();

			// Get Git hash
			$results = $dispatcher->trigger( 'whatChanged', array($fpath, $ima, 'hash') );
			$hash = $results ? $results[0] : 0;	

			// Get full & thumb image names
			$ih = new ProjectsImgHandler();
			$view->file = $ih->createThumbName($filename, '-'.substr($hash, 0, 3));
			$view->thumb = $ih->createThumbName($filename, '-'.substr($hash, 0, 3).'_tn', $extension = 'png');			
					
		}
		else 
		{
			$view->file = '';
			$view->thumb = '';
		}
		
		if (!is_file(JPATH_ROOT.$gallery_path. DS .$view->file) 
			|| !is_file(JPATH_ROOT.$gallery_path. DS .$view->thumb)) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_MISSING_FILE') );
		}
	
		// Is image?
		$ext = explode('.',$ima);
		$ext = end($ext);
		if (in_array($ext, $this->_image_ext) ) 
		{
			$view->type = 'image';
		}
		elseif (in_array($ext, $this->_video_ext)) 
		{
			$view->type = 'video';	
		}
		else 
		{
			$view->type = '';
		}
		$view->ext = $ext;
		
		// Build pub url
		$view->route = $this->_project->provisioned 
					? 'index.php?option=com_publications' . a . 'task=submit'
					: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);		
	
		$view->gallery_path = $gallery_path;
		$view->shot 		= $pScreenshot;
		$view->ima 			= $ima;
		$view->vid 			= $vid;
		$view->pid 			= $pid;
		$view->ajax 		= $ajax;
		$view->no_html 		= $no_html;
		$view->move 		= $move;
		$view->option 		= $this->_option;
		$view->project 		= $this->_project;	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_message) ? $this->_message : '';
		return $view->loadTemplate();	
	}	
	
	/**
	 * Load screenshot
	 *
	 * @return     string (html)
	 */
	protected function _loadScreenshot() 
	{
		// AJAX		
		// Incoming
		$ima 	= JRequest::getVar('ima', '');
		$vid 	= JRequest::getInt('vid', 0);
		$pid 	= JRequest::getInt('pid', 0);
		$move 	= JRequest::getInt('move', 0);
				
		$hash 	= '';
		$src 	= '';
		$title 	= '';
				
		if (!$vid || !$pid) 
		{
			return JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE');
		}
		if (!$ima) 
		{
			return JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE');
		}
		else 
		{
			$ima = str_replace('file::', '', $ima);
		}
		
		// Is image?
		$ext = explode('.',$ima);
		$ext = end($ext);
		if (!in_array(strtolower($ext), $this->_image_ext) && !in_array(strtolower($ext), $this->_video_ext)) 
		{
			return JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_WRONG_EXT') . ' ' . $ext;
		}
						
		// Get screenshot path
		$webpath = $this->_pubconfig->get('webpath');

		// Get publications helper
		$helper = new PublicationHelper( $this->_database );
		$gallery_path = $helper->buildPath($pid, $vid, $webpath, 'gallery');		
		
		// Does screenshot already exist?
		$pScreenshot = new PublicationScreenshot( $this->_database );
		if ($pScreenshot->loadFromFilename($ima, $vid)) 
		{
			$ih = new ProjectsImgHandler();
			$thumb = $ih->createThumbName($pScreenshot->srcfile, '_tn', $extension = 'png');
			$src = $gallery_path. DS .$thumb;
			$title = $pScreenshot->title ? $pScreenshot->title : basename($pScreenshot->filename);
		}
		else 
		{
			// Get project file path		
			$fpath = ProjectsHelper::getProjectPath($this->_project->alias, 
				$this->_config->get('webpath'), 1);
			
			$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
			$from_path = $prefix . $fpath;

			// Git helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new ProjectsGitHelper(
				$this->_config->get('gitpath', '/opt/local/bin/git'), 
				0,
				$this->_config->get('offroot', 0) ? '' : JPATH_ROOT
			);

			// Get Git hash
			$hash = $this->_git->gitLog($fpath, $ima, '' , 'hash');

			$src = $this->_createScreenshot ( $ima, $hash, $from_path, $gallery_path );
			$title = basename($ima);
		}
			
		if ($src) 
		{
			// Output HTML
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'projects',
					'element'=>'publications',
					'name'=>'screenshot'
				)
			);
			
			// Build pub url
			$view->route = $this->_project->provisioned 
				? 'index.php?option=com_publications' . a . 'task=submit'
				: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
			$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
			
			$view->project 	= $this->_project;
			$view->option 	= $this->_option;
			$view->pid 		= $pid;
			$view->vid 		= $vid;
			$view->ima 		= $ima;
			$view->title 	= $title;
			$view->src 		= $src;
			$view->move 	= $move;
			return $view->loadTemplate();
		}
		else 
		{
			return JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE');	
		}		
	}
	
	/**
	 * Create screenshot
	 * 
	 * @param      string  		$ima
	 * @param      string 		$hash
	 * @param      string		$from_path
	 * @param      string		$gallery_path
	 * @param      string		$return
	 *
	 * @return     string (image source or hashed name)
	 */
	protected function _createScreenshot( $ima, $hash, $from_path, $gallery_path, $return = 'src' ) 
	{
		$src = '';
		$filename = basename($ima);
	
		$ih = new ProjectsImgHandler();
		$hashed = $ih->createThumbName($filename, '-'.substr($hash, 0, 6));
		$thumb = $ih->createThumbName($filename, '-'.substr($hash, 0, 6).'_tn', $extension = 'png');
		
		/*
		if (is_file(JPATH_ROOT . $gallery_path. DS .$thumb) 
			&& is_file(JPATH_ROOT . $gallery_path. DS .$hashed)) 
		{
			$src = $gallery_path. DS .$thumb;
		}
		else 
		{
		*/
			// Make sure the path exist
			if (!is_dir( JPATH_ROOT.$gallery_path )) 
			{
				jimport('joomla.filesystem.folder');
				JFolder::create( JPATH_ROOT . $gallery_path, 0777 );
			}
			jimport('joomla.filesystem.file');
			if (!JFile::copy($from_path. DS .$ima, JPATH_ROOT.$gallery_path. DS .$hashed)) 
			{
				return false;
			}
			else 
			{				
				// Is image?
				$ext = explode('.', $filename);
				$ext = end($ext);
				if (in_array(strtolower($ext), $this->_image_ext)) 
				{
					// Also create a thumbnail
					JFile::copy($from_path . DS .$ima, JPATH_ROOT . $gallery_path . DS . $thumb);
					$ih->set('image',$thumb);
					$ih->set('overwrite',true);
					$ih->set('path',JPATH_ROOT . $gallery_path . DS);
					$ih->set('maxWidth', 100);
					$ih->set('maxHeight', 60);
					if (!$ih->process()) 
					{
						return false;
					}
					else 
					{
						$src = $gallery_path. DS .$thumb;
					}
				}
				else 
				{
					// Do we have a thumbnail from Google?
					$objRFile = new ProjectRemoteFile ($this->_database);		
					$remote   = $objRFile->getConnection($this->_project->id, '', 'google', $ima);
					$default  = '';
					
					if ($remote)
					{
						$rthumb = substr($remote['id'], 0, 20) . '_' . strtotime($remote['modified']) . '.png';	
						$imagepath = trim($this->_config->get('imagepath', '/site/projects'), DS);
						$to_path = $imagepath . DS . strtolower($this->_project->alias) . DS . 'preview';
						if ($rthumb && is_file(JPATH_ROOT. DS . $to_path . DS . $rthumb))
						{
							$default = $to_path . DS . $rthumb;
						}
					}
					
					// Copy default video thumbnail
					$default = $default ? $default 
							: trim($this->_pubconfig->get('video_thumb', 'components/com_publications/images/video_thumb.gif'), DS);
					
					if (is_file(JPATH_ROOT . DS . $default)) 
					{
						JFile::copy(JPATH_ROOT . DS . $default, JPATH_ROOT . $gallery_path . DS . $thumb);
						$ih->set('image',$thumb);
						$ih->set('overwrite',true);
						$ih->set('path',JPATH_ROOT . $gallery_path . DS);
						$ih->set('maxWidth', 100);
						$ih->set('maxHeight', 60);
						if (!$ih->process()) 
						{
							return false;
						}
						else 
						{
							$src = $gallery_path. DS .$thumb;
						}
					}
					else 
					{
						return false;
					}
				}
			}		
	/*	} */
		
		return $return == 'src' ? $src : $hashed;				
	}

	/**
	 * Save screenshot
	 *
	 * @return     void (redirect)
	 */
	protected function _saveScreenshot() 
	{
		// Incoming
		$ima 		= urldecode(JRequest::getVar( 'ima', '', 'post' ));
		$vid 		= JRequest::getInt( 'vid', 0, 'post' );
		$pid 		= JRequest::getInt( 'pid', 0, 'post' );
		$title 		= JRequest::getVar( 'title', '', 'post' );
		$srcfile 	= JRequest::getVar( 'srcfile', '', 'post' );
		$move 		= JRequest::getInt( 'move', 0 ); 
		
		// Save any changes to selections/ordering
		$selections = JRequest::getVar( 'selections', '', 'post' );
		$selections = $this->_parseSelections($selections);
		$this->_processGallery( $pid, $vid, $selections );
		
		$now = date( 'Y-m-d H:i:s' );
		
		if (!$vid || !$pid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE'));
		}
		if (!$ima) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_GETTING_IMAGE'));
		}
		
		// Get version label
		$row = new PublicationVersion( $this->_database );
		$row->load($vid);
		$version = $row->version_number;
		
		$pScreenshot = new PublicationScreenshot( $this->_database );
		if ($pScreenshot->loadFromFilename( $ima, $vid )) 
		{
			if ($title && $pScreenshot->title != $title) 
			{
				$pScreenshot->modified = $now;
				$pScreenshot->modified_by = $this->_uid;
				$pScreenshot->title = $title;
			}
		}
		else 
		{
			$pScreenshot = new PublicationScreenshot( $this->_database );
			$pScreenshot->filename = $ima;
			$pScreenshot->srcfile = $srcfile;
			$pScreenshot->publication_id = $pid;
			$pScreenshot->publication_version_id = $vid;
			$pScreenshot->title = $title ? $title : basename($ima);
			$pScreenshot->created = $now;
			$pScreenshot->created_by = $this->_uid;
		}
		
		// Pass success or error message
		if ($pScreenshot->store()) 
		{
			$this->_message = array('message' => JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_IMAGE_SAVED'), 'type' => 'success');
		}
		else 
		{
			$this->_message = array('message' => JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_ERROR_SAVING_IMAGE'), 'type' => 'error');
		}
	
		// Build pub url
		$route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$url = JRoute::_($route . a . 'pid=' . $pid);
		
		$url .= '?section=gallery';
		$url .= '&version=' . $version;
		$url .= $move ? '&move=' . $move : '';
		
		// Redirect
		$this->_referer = $url;
		return;	
	}
	
	/**
	 * Parse tags
	 * 
	 * @param      string  		$tag_string
	 * @param      integer 		$keep
	 *
	 * @return     array
	 */
	protected function _parseTags( $tag_string, $keep = 0 ) 
	{
		$newwords = array();
		
		// If the tag string is empty, return the empty set.
		if ($tag_string == '') 
		{
			return $newwords;
		}
		
		// Perform tag parsing
		$tag_string = trim($tag_string);
		$raw_tags = explode(',',$tag_string);
		
		foreach ($raw_tags as $raw_tag)
		{
			$raw_tag = trim($raw_tag);
			$nrm_tag = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $raw_tag));
			if ($keep != 0) 
			{
				$newwords[$nrm_tag] = $raw_tag;
			} 
			else 
			{
				$newwords[] = $nrm_tag;
			}
		}
		return $newwords;
	}
	
	/**
	 * Create tags
	 * 
	 * @param      string  		$newtag
	 *
	 * @return     array
	 */
	protected function _createTags( $newtag = '' ) 
	{
		$tagarray = array();
		$newTags = $this->_parseTags($newtag);
		$rawTags = $this->_parseTags($newtag, 1);
		$tagObj = new TagsTag( $this->_database );
		
		foreach ($newTags as $tag) 
		{
			$tag = trim($tag);
			if ($tag != '') {										
				if ($tagObj->loadTag($tag)) 
				{
					$tagarray[] = $tagObj->id;
				}
				else {
					// Create tag
					$tagObj = new TagsTag( $this->_database );
					if (get_magic_quotes_gpc()) 
					{
						$tag = addslashes($tag);
					}
					$tagObj->tag = $tag;
					$tagObj->raw_tag = isset($rawTags[$tag]) ? $rawTags[$tag] : $tag;
					if ($tagObj->store()) 
					{
						$tagObj->checkin();
						if ($tagObj->id) 
						{
							$tagarray[] = $tagObj->id;
						}
					}						
				}
			}
		}
		return $tagarray;
	}
	
	/**
	 * Suggest tags (AJAX)
	 *
	 * @return   string (html)
	 */
	public function suggestTags() 
	{
		// AJAX
		// Incoming
		$vid 		= JRequest::getInt('vid', 0);
		$pid 		= JRequest::getInt('pid', 0);
		$limit 		= JRequest::getInt('limit', 8);
		$newtag 	= JRequest::getVar('tags', '');
		$tcount 	= 1; // minimum number of tagged objects
		
		// Get original/new selections
		$selections = JRequest::getVar('selections', '');
		$selections = explode("##", $selections);
		$attached = array();
		if (count($selections) > 0) 
		{
			foreach ($selections as $sel) 
			{
				if (trim($sel) != '' && intval($sel)) 
				{
					$attached[] = trim($sel);
				}
			}
		}
				
		// Some new tags are provided
		if ($newtag) 
		{			
			$new = $this->_createTags($newtag);
			$attached = array_merge($attached, $new);
		}
		array_unique($attached);
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'tags'
			)
		);
		
		if (!$vid || !$pid) 
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_ERROR_NO_PUBLICATION'));
		}
		else 
		{
			$objP = new Publication( $this->_database );
			$pub = $objP->getPublication($pid, 'default');
			if (!$pub) 
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_ERROR_NO_PUBLICATION'));
			}		
		}
		
		if (!$this->getError()) 
		{	
			// Get attached tags
			$tagsHelper = new PublicationTags( $this->_database);
			$view->original = $tagsHelper->getTags($pid);
			
			$view->attached_tags = $tagsHelper->getPickedTags($attached);
			
			// Get suggestions
			$view->tags = $tagsHelper->getSuggestedTags($pub->title, $pub->cat_alias, $view->attached_tags, $limit, $tcount );
		}
		
		// Build pub url
		$view->route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);

		$view->pid 		= $pid;
		$view->vid 		= $vid;
		$view->option 	= $this->_option;
		$view->project 	= $this->_project;	
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		$view->msg = isset($this->_message) ? $this->_message : '';
		return $view->loadTemplate();
			
	}

	/**
	 * Show publication versions
	 *
	 * @return     string (html)
	 */
	public function versions() 
	{	
		// Incoming
		$pid = $this->_pid ? $this->_pid : JRequest::getInt('pid', 0);
		
		// Instantiate project publication
		$objP = new Publication( $this->_database );	
		$objV = new PublicationVersion( $this->_database );	
		
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'versions'
			)
		);
		
		$view->pub = $objP->getPublication($pid, 'default', $this->_project->id);
		if (!$view->pub) 
		{		
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND') );
			return;
		}
		
		// Build pub url
		$view->route = $this->_project->provisioned 
			? 'index.php?option=com_publications' . a . 'task=submit'
			: 'index.php?option=com_projects' . a . 'alias=' . $this->_project->alias . a . 'active=publications';
		$view->url = JRoute::_($view->route . a . 'pid=' . $pid);
			
		// Append breadcrumbs
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(
			stripslashes($view->pub->title),
			$view->url	
		);
		
		// Get versions
		$view->versions = $objV->getVersions( $pid, $filters = array('withdev' => 1));
		
		// Output HTML
		$view->option 		= $this->_option;
		$view->database 	= $this->_database;
		$view->project 		= $this->_project;
		$view->authorized 	= $this->_authorized;
		$view->uid 			= $this->_uid;
		$view->pid 			= $pid;
		$view->task 		= $this->_task;
		$view->config 		= $this->_config;	
		$view->pubconfig 	= $this->_pubconfig;
		$view->title		= $this->_area['title'];

		// Get messages	and errors	
		$view->msg = $this->_msg;
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();		
	}
	
	/**
	 * Contribute from outside projects
	 *
	 * @return     string (html)
	 */
	public function contribute() 
	{
		// Get user info
		$juser =& JFactory::getUser();
		
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'browse',
				'layout'=>'intro'
			)
		);
		$view->option  		= $this->_option;
		$view->pubconfig 	= $this->_pubconfig;	
		$view->outside  	= 1;
		$view->juser   		= $juser;
		$view->uid 			= $this->_uid;
		$view->database		= $this->_database;
		
		// Get publications
		if (!$juser->get('guest'))
		{
			$view->filters = array();
			
			// Get user projects
			$obj = new Project( $this->_database );
			$view->filters['projects']  = $obj->getUserProjectIds($juser->get('id'), 0, 1);
			
			$view->filters['mine']		= $juser->get('id');
			$view->filters['dev']		= 1;
			$view->filters['sortby']	= 'mine';
			$view->filters['limit']  	= JRequest::getInt( 'limit', 3 );
			
			// Get publications created by user
			$objP = new Publication( $this->_database );
			$view->mypubs = $objP->getRecords( $view->filters );
			
			// Get pub count
			$count = $objP->getCount( $view->filters );
			$view->mypubs_count = ($count && is_array($count)) ? count($count) : 0;
			
			// Get other pubs that user can manage
			$view->filters['coauthor'] = 1;
			$view->coauthored = $objP->getRecords( $view->filters );
			$coauthored = $objP->getCount( $view->filters );
			$view->coauthored_count = ($coauthored && is_array($coauthored)) ? count($coauthored) : 0;		
		}
		
		return $view->loadTemplate();		
	}

	//----------------------------------------
	// Private functions
	//----------------------------------------
	
	/**
	 * Check if publication may be published
	 * 
	 * @param      array 		$checked
	 *
	 * @return     boolean
	 */	
	protected function _checkPublicationPermit( $checked = array() ) 
	{
		$publication_allowed = true;
		foreach($this->_required as $req) 
		{
			if (isset($checked[$req]) && $checked[$req] == 0) 
			{
				$publication_allowed = false;
			}
		}
		
		return $publication_allowed;		
	}
	
	/**
	 * Check what's missing in draft
	 * 
	 * @param      string  		$type master type
	 * @param      object 		$row
	 * @param      string		$version
	 *
	 * @return     array
	 */
	protected function _checkDraft( $type, $row, $version = 'dev' ) 
	{
		$checked = array();
		
		if (!isset($this->_panels))
		{
			$this->_base = $type;
			
			// Get active panels
			$this->_getPanels();
		}
		
		// Check each enabled panel
		foreach ($this->_panels as $key => $value) 
		{
			$checked[$value] = $version == 'dev' ? 0 : 1;
			
			if ($value == 'description')
			{
				// Check description
				$checked['description'] = $row->description && $row->abstract ? 1 : 0;
			}
			elseif ($value == 'content')
			{
				// Check content
				$checked['content'] = 0;
				if ($type == 'tools') 
				{
					// Check tool
					// TBD
				} 
				else 
				{
					$pContent = new PublicationAttachment( $this->_database );
					$count_attachments = $pContent->getAttachments ( $row->id, $filters = array('count' => 1) );
					if ($count_attachments > 0) 
					{
						// Check that no previous version has the same primary content
						// TBD
						$checked['content'] = 1;
					}
				}
			}
			elseif ($value == 'authors')
			{
				// Check authors
				$pAuthor = new PublicationAuthor( $this->_database );
				$checked['authors'] = $pAuthor->getCount($row->id) >= 1 ? 1 : 0;
			}
			elseif ($value == 'license')
			{
				// Check license
				$checked['license'] = ($row->license_type) ? 1 : 0;	
				
			}
			elseif ($value == 'audience')
			{
				// Check audience
				$pAudience = new PublicationAudience( $this->_database );
				$checked['audience'] = $pAudience->loadByVersion($row->id) ? 1 : 0;
			}
			elseif ($value == 'access')
			{
				// Check access - public by default
				$checked['access'] = 1;
			}
			elseif ($value == 'gallery')
			{
				// Check sreenshots
				$pScreenshot = new PublicationScreenshot( $this->_database );
				$checked['gallery'] = count($pScreenshot->getScreenshots( $row->id )) > 0 ? 1 : 0;	
			}
			elseif ($value == 'tags')
			{
				// Check tags
				$tagsHelper = new PublicationTags( $this->_database);
				$checked['tags'] = $tagsHelper->countTags($row->publication_id) > 0 ? 1 : 0;
			}
			elseif ($value == 'notes')
			{
				// Check release notes
				$checked['notes'] = $row->release_notes ? 1 : 0;		
			}
		}		

		return $checked;		
	}
	
	/**
	 * Get current position in pub contribution process
	 * 
	 * @param      object  		$row
	 * @param      string 		$lastpane
	 * @param      string		$current
	 *
	 * @return     array
	 */
	protected function _getIndex($row, $lastpane, $current) 
	{	
		$check = array();
		
		if (!isset($this->_panels))
		{
			// Get active panels
			$this->_getPanels();
		}
		
		// Get active and last visted index
		$last_idx = 0;
		$current_idx = 0;
		$next_idx = 0;
		
		while ($panel = current($this->_panels)) 
		{
		    if ($panel == $lastpane) 
			{
		        $last_idx = key($this->_panels);
		    }
		 	if ($panel == $current) 
			{
		        $current_idx = key($this->_panels);
				$next_idx = key($this->_panels) + 1;
		    }
			if ($lastpane == 'review') 
			{
				$current_idx = 0;
			}
		    next($this->_panels);
		}
		
		$check['last_idx'] 		= $last_idx;
		$check['current_idx'] 	= $current_idx;
		$check['next_idx'] 		= $next_idx;
		
		return $check;			
	}
	
	/**
	 * Parse selections
	 * 
	 * @param      array  		$selections
	 *
	 * @return     array
	 */
	protected function _parseSelections( $selections = '' ) 
	{
		if ($selections) 
		{
			$sels = explode("##", $selections);
			
			$files 		= array();
			$tools 		= array();
			$links 		= array();
			$notes 		= array();
			$data  		= array();
			$collection = array();
			
			$first = '';
			
			foreach($sels as $sel) 
			{
				$arr = explode("::", $sel);
				if ($arr[0] == 'file') 
				{
					$files[] = urldecode($arr[1]);
				}
				elseif ($arr[0] == 'link') 
				{
					$links[] = urldecode($arr[1]);	
				}
				elseif ($arr[0] == 'tool') 
				{
					$tools[] = urldecode($arr[1]);	
				}
				elseif ($arr[0] == 'note') 
				{
					$notes[] = urldecode($arr[1]);	
				}
				elseif ($arr[0] == 'data') 
				{
					$data[] = urldecode($arr[1]);	
				}
				elseif ($arr[0] == 'collection') 
				{
					$collection[] = urldecode($arr[1]);	
				}
			}
			
			$count = count($files) + count($links) + count($tools) + count($notes) + count($data) + count($collection);
						
			$selections = array(
				'first' => $sels[0], 
				'files' => $files, 
				'links' => $links, 
				'tools' => $tools, 
				'notes' => $notes, 
				'data' => $data, 
				'collection' => $collection,
				'count' => $count
			);
		}
		
		return $selections;
	}
			
	/**
	 * Get clone path
	 * 
	 * @return     string
	 */
	protected function _getClonePath() 
	{		
		$clone  = $this->_config->get('gitclone');
		$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
		$webdir = $this->_config->get('webpath');
		
		if (substr($webdir, 0, 1) != DS) 
		{
			$webdir = DS.$webdir;
		}
		if (substr($webdir, -1, 1) == DS) 
		{
			$webdir = substr($webdir, 0, (strlen($webdir) - 1));
		}
		$clone = $clone ? JPATH_ROOT.$clone : $prefix . $webdir . DS .'clone' . DS . '.git';
		return $clone;				
	}
	
	/**
	 * Get member path
	 * 
	 * @return     string
	 */
	protected function _getMemberPath() 
	{		
		// Get members config
		$mconfig =& JComponentHelper::getParams( 'com_members' );
			
		// Build upload path
		$dir  = Hubzero_View_Helper_Html::niceidformat( $this->_uid );
		$path = JPATH_ROOT;
		if (substr($mconfig->get('webpath', '/site/members'), 0, 1) != DS) 
		{
			$path .= DS;
		}
		$path .= $mconfig->get('webpath', '/site/members'). DS .$dir. DS .'files';

		if (!is_dir( $path )) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) 
			{
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return;
			}
		}

		return $path;		
	}
	
	/**
	 * Prep file directory (provisioned project)
	 * 
	 * @param      boolean		$force
	 *
	 * @return     boolean
	 */
	protected function _prepDir($force = true) 
	{		
		jimport('joomla.filesystem.folder');
		
		// Get member files path
		$memberPath = $this->_getMemberPath();
		
		// Get project path
		$path = ProjectsHelper::getProjectPath($this->_project->alias, 
			$this->_config->get('webpath'), 1);
			
		$prefix = $this->_config->get('offroot', 0) ? '' : JPATH_ROOT ;
					
		if (!is_dir( $prefix . $path )) 
		{
			if (!JFolder::create( $prefix . $path, 0777 )) 
			{
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return;
			}
		}
		
		// Build .git repo
		$gitRepoBase = $prefix . $path. DS .'.git';
		
		if (!is_dir( $gitRepoBase )) 
		{
			// Git helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new ProjectsGitHelper(
				$this->_config->get('gitpath', '/opt/local/bin/git'), 
				0,
				$this->_config->get('offroot', 0) ? '' : JPATH_ROOT
			);
			
			// Initialize Git
			$this->_git->iniGit($path);
		}
		
		$path = $prefix . $path;
		
		// Copy files from member directory
		if (!JFolder::copy($memberPath, $path, '', true)) 
		{
			$this->setError( JText::_('COM_PROJECTS_FAILED_TO_COPY_FILES') );
			return false;
		}
		
		// Read copied files
		$get = $this->_readDir($path, $path);
		$num = count($get);
		$checkedin = 0;
		
		// Check-in copied files
		if ($get)
		{
			// cd
			chdir($path);
			
			// Get git path
			$gitpath = $this->_config->get('gitpath', '/opt/local/bin/git');
			
			// Get author profile (for Git comments)
			$profile =& Hubzero_Factory::getProfile();
			$profile->load( $this->_uid );
			$name = $profile->get('name');
			$email = $profile->get('email');
			$author = escapeshellarg($name.' <'.$email.'> ');				
			
			foreach($get as $file)
			{
				if (is_file($path . DS . $file))
				{
					// Git add
					exec($gitpath.' add '.escapeshellarg($file).' 2>&1', $out);

					// Git commit
					exec($gitpath.' commit -m "Added file '.escapeshellarg($file).'" --author="'.$author.'" 2>&1', $out);
					$checkedin++;	
				}
			}
		}
		if ($num == $checkedin)
		{
			// Clean up member files
			JFolder::delete($memberPath);
			
			return true;	
		}
			
		return false;		
	}
	
	/**
	 * Read directory
	 * 
	 * @param      string  		$path
	 * @param      string  		$dirpath
	 * @param      string  		$filter
	 * @param      boolean  	$recurse
	 * @param      array  		$exclude
	 *
	 * @return     array
	 */
	protected function _readDir($path, $dirpath = '', $filter = '.', $recurse = true, $exclude = array('.svn', 'CVS'))
	{
		$arr = array();
		$handle = opendir($path);

		while (($file = readdir($handle)) !== false)
		{
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) 
			{
				$dir = $path . DS . $file;
				$isDir = is_dir($dir);
				if ($isDir) 
				{
					$arr2 = $this->_readDir($dir, $dirpath);
					$arr = array_merge($arr, $arr2);
				} 
				else 
				{
					if (preg_match("/$filter/", $file)) 
					{
						$file = $path . DS . $file;
						$file = str_replace($dirpath.DS, '', $file);
						$arr[] = $file;					
					}
				}
			}
		}
		closedir($handle);
		
		return $arr;
	}
		
	/**
	 * Get panels
	 *
	 * @return    void
	 */
	protected function _getPanels()
	{
		// Availbale panels, in order
		$availPanels = 'content,description,authors,audience,gallery,tags,access,license,notes';
		
		// Get panels order from config
		$panels = $this->_pubconfig->get('panels', $availPanels);
		
		$availPanels = explode (',', $availPanels);
		$panels = ProjectsHelper::getParamArray($panels);
		$this->_panels = array();
		
		// Get master type params
		$typeParams = NULL;
		if (isset($this->_base))
		{
			$mt = new PublicationMasterType( $this->_database );
			$mType = $mt->getType($this->_base);
			$typeParams = new JParameter( $mType->params );
		}
				
		// Skip some panels if set in params
		foreach ($panels as $panel) 
		{
			if (!in_array(trim($panel), $availPanels))
			{
				continue;
			}
			
			if ($typeParams)
			{
				$on = $typeParams->get('panel_' . $panel);
				if ($on && $on == 2)
				{
					continue;
				}
			}
			
			if (trim($panel) == 'access' && $this->_pubconfig->get('show_access', 0) == 0) 
			{
				continue;
			}
			elseif (trim($panel) == 'audience' && $this->_pubconfig->get('show_audience', 0) == 0) 
			{
				continue;
			}
			elseif (trim($panel) == 'gallery' && $this->_pubconfig->get('show_gallery', 0) == 0) 
			{
				continue;
			}
			elseif (trim($panel) == 'license' && $this->_pubconfig->get('show_license', 0) == 0) 
			{
				continue;
			}
			elseif (trim($panel) == 'tags' && $this->_pubconfig->get('show_tags', 0) == 0) 
			{
				continue;
			}
			elseif (trim($panel) == 'notes' && $this->_pubconfig->get('show_notes', 0) == 0) 
			{
				continue;
			}
			$this->_panels[] = trim($panel);
		}
	}
	
	/**
	 * Get data as CSV file
	 * 
	 * @param      integer  	$db_name
	 * @param      integer  	$version
	 *
	 * @return     string data
	 */
	protected function _getCsvData($db_name = '', $version = '', $tmpFile = '')
	{
		if (!$db_name || !$version)
		{
			return false;
		}

		mb_internal_encoding('UTF-8');

		// component path for "com_dataviewer"
		$dv_com_path = JPATH_ROOT . DS . 'components' . DS . 'com_dataviewer';

		require_once($dv_com_path . DS . 'dv_config.php');
		require_once($dv_com_path . DS . 'lib' . DS . 'db.php');
		require_once($dv_com_path . DS . 'modes' . DS . 'mode_dsl.php');
		require_once($dv_com_path . DS . 'filter' . DS . 'csv.php');

		$dv_conf = get_conf(NULL);
		$dd = get_dd(NULL, $db_name, $version);
		$dd['serverside'] = false;

		$sql = query_gen($dd);
		$result = get_results($sql, $dd);

		ob_start();
		filter($result, $dd, true);
		$csv = ob_get_contents();
		ob_end_clean();

		if ($csv && $tmpFile)
		{
			$handle = fopen($tmpFile, 'w');
			fwrite($handle, $csv);
			fclose($handle);

			return true;
		}

		return $csv;
	}
	
	/**
	 * Get disk space
	 * 
	 * @param      string	$option
	 * @param      object  	$project
	 * @param      string  	$case
	 * @param      integer  $by
	 * @param      string  	$action
	 * @param      object 	$config
	 * @param      string  	$app
	 *
	 * @return     string
	 */
	protected function pubDiskSpace( $option, $project, $action, $config)
	{
		// Output HTML
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'projects',
				'element'=>'publications',
				'name'=>'diskspace'
			)
		);
		
		// Include styling and js
		$document =& JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'diskspace.css');
		$document->addScript('plugins' . DS . 'projects' . DS . 'files' . DS . 'js' . DS . 'diskspace.js');
		
		$database =& JFactory::getDBO();
		
		// Build query
		$filters = array();
		$filters['limit'] 	 		= JRequest::getInt('limit', 25);
		$filters['start'] 	 		= JRequest::getInt('limitstart', 0);
		$filters['sortby']   		= JRequest::getVar( 't_sortby', 'title');
		$filters['sortdir']  		= JRequest::getVar( 't_sortdir', 'ASC');
		$filters['project']  		= $project->id;
		$filters['ignore_access']   = 1;
		$filters['dev']   	 		= 1; // get dev versions
		
		// Instantiate project publication
		$objP = new Publication( $database );
		
		// Get all publications
		$view->rows = $objP->getRecords($filters);
		
		// Get used space
		$helper 	   = new PublicationHelper($database);
		$view->dirsize = $helper->getDiskUsage($project->id, $view->rows);
		$view->params  = new JParameter( $project->params );
		$view->quota   = $view->params->get('pubQuota') 
						? $view->params->get('pubQuota') 
						: ProjectsHtml::convertSize(floatval($config->get('pubQuota', '1')), 'GB', 'b');
				
		// Get total count
		$results = $objP->getCount($filters);
		$view->total = ($results && is_array($results)) ? count($results) : 0;
				
		$view->params = new JParameter( $project->params );
		$view->action 	= $action;
		$view->project 	= $project;
		$view->option 	= $option;
		$view->config 	= $config;
		$view->title	= isset($this->_area['title']) ? $this->_area['title'] : '';
		
		return $view->loadTemplate();		
	}
	
	/**
	 * Archive data in a publication and package
	 * 
	 * @param      integer  	$db_name
	 * @param      integer  	$version
	 *
	 * @return     string data
	 */
	public function archivePub($pid = 0, $vid = 0)
	{
		if (!$pid || !$vid)
		{
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		// Archival name
		$tarname = JText::_('Publication').'_'.$pid.'.zip';
				
		// Load publication & version classes
		$objP  = new Publication( $database );
		$objV  = new PublicationVersion( $database );
		
		if (!$objP->load($pid) || !$objV->load($vid))
		{
			return false;
		}
		
		// Get publications helper
		$helper = new PublicationHelper($database, $vid, $pid);
		
		// Start README
		$readme  = $objV->title . "\n ";
		$readme .= 'Version ' . $objV->version_label . "\n ";
				
		// Get authors
		$pa = new PublicationAuthor( $database );
		$authors = $pa->getAuthors($vid);
		
		$tmpFile   = '';
		$tmpReadme = '';
		
		$readme .= 'Authors: ' . "\n ";
		
		foreach ($authors as $author)
		{
			$readme .= ($author->name) ? $author->name : $author->p_name;
			$org = ($author->organization) ? $author->organization : $author->p_organization;
			
			if ($org)
			{
				$readme .= ', ' . $org;
			}
			$readme .= "\n ";
		}
		
		$readme .= 'doi:' . $objV->doi . "\n ";
		$readme .= '#####################################' . "\n ";
		
		$readme .= "\n ";
		$readme .= 'License: ' . "\n ";
		
		// Get license type
		$objL = new PublicationLicense( $database);
		$license = '';
		if ($objL->load($objV->license_type))
		{
			$license = $objL->title . "\n ";
		}
		
		$license .= $objV->license_text ? "\n " . $objV->license_text . "\n " : '';
		$readme .= $license . "\n ";
		$readme .= '#####################################' . "\n ";
		$readme .= "\n ";
		$readme .= 'Included Publication Materials:' . "\n ";
				
		// Build publication path 
		$base_path = $this->_pubconfig->get('webpath');
		$path = $helper->buildPath($pid, $vid, $base_path);
		
		$galleryPath = JPATH_ROOT . $path . DS . 'gallery';
		$dataPath 	 = JPATH_ROOT . $path . DS . 'data';
		$contentPath = JPATH_ROOT . $path . DS . $objV->secret;
		
		$tarpath = JPATH_ROOT . $path . DS . $tarname;
		
		$zip = new ZipArchive;
		if ($zip->open($tarpath, ZipArchive::OVERWRITE) === TRUE) 
		{
			// Get joomla libraries
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			
			$i = 0;
			
			// Get attachments
			$pContent 	= new PublicationAttachment( $database );
			$sDocs 		= $pContent->getAttachmentsArray( $vid, '4' );
			$pDocs 		= $pContent->getAttachmentsArray( $vid, '1' );
			
			$mFolder 	= JText::_('Publication').'_'.$pid;
			
			// Add primary and supporting content
			$mainFiles = array();
			if (is_dir($contentPath))
			{
				$mainFiles = JFolder::files($contentPath, '.', true, true);
			}			
			
			if (!empty($mainFiles))
			{
				foreach ($mainFiles as $e)
				{					
					$fileinfo = pathinfo($e);
					$a_dir  = $fileinfo['dirname'];
					$a_dir	= trim(str_replace($contentPath, '', $a_dir), DS);
															
					$fPath  = $a_dir && $a_dir != '.' ? $a_dir . DS : '';
					$fPath .= basename($e);
						
					if (in_array($fPath, $pDocs))
					{
						$fPath = $mFolder . DS . 'content' . DS . $fPath;
					}
					elseif (in_array($fPath, $sDocs))
					{
						$fPath = $mFolder . DS . 'supporting' . DS . $fPath;
					}
					else
					{
						// Skip everything else
						continue;
					}
					$readme .= str_replace($mFolder . DS, '', $fPath) . "\n ";
					
					$zip->addFile($e, $fPath);
					$i++;
				}
			}			
			
			// Add data files
			$dataFiles = array();
			if (is_dir($dataPath))
			{
				$dataFiles = JFolder::files($dataPath, '.', true, true);
			}
		
			if (!empty($dataFiles))
			{
				foreach ($dataFiles as $e)
				{
					// Skip thumbnails
					if (preg_match("/_tn.gif/", $e) || preg_match("/_medium.gif/", $e))
					{
						continue;
					}
					
					$fileinfo = pathinfo($e);
					$a_dir  = $fileinfo['dirname'];
					$a_dir	= trim(str_replace($dataPath, '', $a_dir), DS);
					
					$fPath = $a_dir && $a_dir != '.' ? $a_dir . DS : '';
					$fPath = $mFolder . DS . 'data' . DS . $fPath . basename($e);
					
					$readme .= str_replace($mFolder . DS, '', $fPath) . "\n ";
					$zip->addFile($e, $fPath);
					$i++;
				}				
			}
			
			// Get gallery info
			$pScreenshot = new PublicationScreenshot( $database );
			$gImages = $pScreenshot->getScreenshotArray( $vid );
			
			// Add screenshots	
			$galleryFiles = array();
			if (is_dir($galleryPath))
			{
				$galleryFiles = JFolder::files($galleryPath, '.', true, true);
			}
			
			if (!empty($galleryFiles) && !empty($gImages))
			{
				$g = 1;
				foreach ($galleryFiles as $e)
				{
					$fPath = trim(str_replace($galleryPath . DS, '', $e), DS);
					if (!isset($gImages[$fPath]))
					{
						continue;
					}
					
					$gName = $g . '-' . basename($gImages[$fPath]);
					
					$fPath = $mFolder . DS . 'gallery' . DS . $gName; 
					
					$readme .= str_replace($mFolder . DS, '', $fPath) . "\n ";
					$zip->addFile($e, $fPath);
					$i++;
					$g++;
				}				
			}
			
			// Database type?
			$mainContent = $pContent->getAttachments( $vid, array('role' => 1));			
			if (!empty($mainContent) && $mainContent[0]->type == 'data')
			{
				$firstChild = $mainContent[0];
				$db_name 	= $firstChild->object_name;
				$db_version = $firstChild->object_revision;
				
				// Add CSV file
				if ($db_name && $db_version)
				{
					$tmpFile 	= JPATH_ROOT . $path . DS . 'data.csv';	
					$csvFile 	= $mFolder . DS . 'data.csv';				
					$csv 		= $this->_getCsvData($db_name, $db_version, $tmpFile);
					
					if ($csv && file_exists($tmpFile))
					{
						$readme .= str_replace($mFolder . DS, '', $csvFile) . "\n ";
						$zip->addFile($tmpFile, $csvFile);
						$i++;
					}
				}
			}
			
			if ($i > 0 && $readme)
			{
				$tmpReadme = JPATH_ROOT . $path . DS . 'README.txt';
				$rmFile  = $mFolder . DS . 'README.txt';
				
				$readme .= str_replace($mFolder . DS, '', $rmFile) . "\n ";
				$readme .= '#####################################' . "\n ";
				$readme .= 'Archival package produced ' . date( 'Y-m-d H:i:s' );
								
				$handle  = fopen($tmpReadme, 'w');
				fwrite($handle, $readme);
				fclose($handle);
				
				$zip->addFile($tmpReadme, $rmFile);
			}
		
		    $zip->close();		    
		} 
		else 
		{
		    return false;
		}
		
						
		// Delete temp files
		if (file_exists($tmpReadme))
		{
			unlink($tmpReadme);
		}
		if (file_exists($tmpFile))
		{
			unlink($tmpFile);
		}
				
		return $tarpath;
	}
	
	/**
	 * Serve publication-related file (via public link)
	 * 
	 * @param   int  	$projectid
	 * @return  void
	 */
	public function serve( $projectid = 0, $query = '')  
	{
		$data = json_decode($query);
		
		if (!isset($data->pid) || !$projectid)
		{
			return false;
		}
		
		$disp 	= isset($data->disp) ? $data->disp : 'inline';
		$type 	= isset($data->type) ? $data->type : 'file';
		$folder = isset($data->folder) ? $data->folder : 'wikicontent';
		$fpath	= isset($data->path) ? $data->path : 'inline';
		
		if ($type != 'file')
		{
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		// Instantiate a project
		$obj = new Project( $database );
				
		// Get referenced path
		$pubconfig =& JComponentHelper::getParams( 'com_publications' );
		$base_path = $pubconfig->get('webpath');
		$pubPath = PublicationHelper::buildPath($data->pid, $data->vid, $base_path, $folder, $root = 0);
		
		$serve = JPATH_ROOT . $pubPath . DS . $fpath;
		
		// Ensure the file exist
		if (!file_exists($serve)) 
		{				
			// Throw error
			JError::raiseError( 404, JText::_('COM_PROJECTS_FILE_NOT_FOUND'));
			return;
		}
		
		// Get some needed libraries
		ximport('Hubzero_Content_Server');
				
		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($serve);
		$xserver->disposition($disp);
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(basename($fpath));

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_SERVER_ERROR') );
		} 
		else 
		{
			exit;
		}

		return;
	}
}
