<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class ResourcesController extends JObject
{
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$this->_task  = JRequest::getVar( 'task', '' );
		$this->_id    = JRequest::getInt( 'id', 0 );
		$this->_alias = JRequest::getVar( 'alias', '' );
		$this->_resid = JRequest::getInt( 'resid', 0 );
		
		if ($this->_resid && !$this->_task) {
			$this->_task = 'play';
		}
		if (($this->_id || $this->_alias) && !$this->_task) {
			$this->_task = 'view';
		}
		
		switch ($this->_task) 
		{
			// Individual resource specific actions/views
			case 'view':       $this->view();       break;
			case 'play':       $this->play();       break;
			case 'citation':   $this->citation();   break;
			case 'download':   $this->download();   break;
			case 'sourcecode': $this->sourcecode(); break;
			case 'license':    $this->license();    break;
			case 'feed.rss':   $this->feed();       break;
			case 'feed':       $this->feed();       break;
			
			// Resource discovery
			case 'browse':     $this->browse();     break;
			case 'browsetags': $this->browsetags(); break;
			
			// Should only be called via AJAX
			case 'browser':    $this->browser();    break;
			case 'plugin':     $this->plugin();     break;
			case 'savetags':   $this->savetags();   break;

			default: $this->intro(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//-----------
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}
	
	//-----------
	
	private function _getScripts($script='')
	{
		$document =& JFactory::getDocument();
		if ($script) {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$script.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$script.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------
	
	private function _buildPathway() 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if (count($pathway->getPathWay()) <= 1 && $this->_task) {
			switch ($this->_task) 
			{
				case 'browse':
					if ($this->_task_title) {
						$pathway->addItem(
							$this->_task_title,
							'index.php?option='.$this->_option.'&task='.$this->_task
						);
					}
				break;
				case 'browsetags':
					if ($this->_task_title) {
						$pathway->addItem(
							$this->_task_title,
							'index.php?option='.$this->_option.'&type='.$this->type
						);
					}
				break;
				default:
					$pathway->addItem(
						JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
						'index.php?option='.$this->_option.'&task='.$this->_task
					);
				break;
			}
		}
	}
	
	//-----------
	
	private function _buildTitle() 
	{
		if (!$this->_title) {
			$this->_title = JText::_(strtoupper($this->_option));
			if ($this->_task) {
				switch ($this->_task) 
				{
					case 'browse':
					case 'browsetags':
						if ($this->_task_title) {
							$this->_title .= ': '.$this->_task_title;
						}
					break;
					default:
						$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
					break;
				}
			}
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function intro() 
	{
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();

		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Instantiate a new view
		$view = new JView( array('name'=>'intro') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		
		$database =& JFactory::getDBO();
		
		// Get major types
		$t = new ResourcesType( $database );
		$view->categories = $t->getMajorTypes();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		$view->option = $this->_option;
		$view->config = $this->config;

		// Set the default sort
		$default_sort = 'date';
		if ($this->config->get('show_ranking')) {
			$default_sort = 'ranking';
		}

		// Incoming
		$view->filters = array();
		$view->filters['tag']    = JRequest::getVar( 'tag', '' );
		$view->filters['type']   = JRequest::getVar( 'type', '' );
		$view->filters['sortby'] = JRequest::getVar( 'sortby', $default_sort );
		$view->filters['limit']  = JRequest::getInt( 'limit', 25 );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0 );

		// Determine if user can edit
		$view->authorized = $this->_authorize();

		//$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		// Get major types
		$t = new ResourcesType( $database );
		$view->types = $t->getMajorTypes();

		if (!is_int($view->filters['type'])) {
			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/learningmodules => Learning Modules
			for ($i = 0; $i < count($view->types); $i++) 
			{	
				$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $view->types[$i]->type);
				$normalized = strtolower($normalized);

				if (trim($view->filters['type']) == $normalized) {
					$view->filters['type'] = $view->types[$i]->id;
					break;
				}
			}
		}

		// Instantiate a resource object
		$rr = new ResourcesResource( $database );
		
		// Execute count query
		$results = $rr->getCount( $view->filters );
		$view->total = ($results && is_array($results)) ? count($results) : 0;
		
		// Run query with limit
		$view->results = $rr->getRecords( $view->filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
	
		// Get type if not given
		$this->_title = JText::_(strtoupper($this->_option)).': ';
		if ($view->filters['type'] != '') {
			$t->load( $view->filters['type'] );
			$this->_title .= $t->type;
			$this->_task_title = $t->type;
		} else {
			$this->_title .= JText::_('COM_RESOURCES_ALL');
			$this->_task_title = JText::_('COM_RESOURCES_ALL');
		}
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();

		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browsetags() 
	{
		// Check if we're using this view type
		if ($this->config->get('browsetags') == 'off') {
			$this->browse();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'browse','layout'=>'tags') );
		$view->option = $this->_option;
		$view->config = $this->config;
		
		// Incoming
		$view->tag = JRequest::getVar( 'tag', '' );
		$view->tag2 = JRequest::getVar( 'with', '' );
		$view->type = strtolower(JRequest::getVar( 'type', 'tools' ));
		
		$view->supportedtag = $this->config->get('supportedtag');
		if (!$view->tag && $view->supportedtag && $view->type == 'tools') {
			$view->tag = $view->supportedtag;
		}
		
		$database =& JFactory::getDBO();
		
		// Get major types
		$t = new ResourcesType( $database );
		$view->types = $t->getMajorTypes();
		
		// Normalize the title
		// This is so we can determine the type of resource to display from the URL
		// For example, /resources/learningmodules => Learning Modules
		$activetype = 0;
		$activetitle = '';
		for ($i = 0; $i < count($view->types); $i++) 
		{	
			$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $view->types[$i]->type);
			$normalized = strtolower($normalized);
			$view->types[$i]->title = $normalized;
			
			if (trim($view->type) == $normalized) {
				$activetype = $view->types[$i]->id;
				$activetitle = $view->types[$i]->type;
			}
		}
		asort($view->types);
		
		// Ensure we have a type to display
		if (!$activetype) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Instantiate a resource object
		$rr = new ResourcesResource( $database );
		
		// Determine if user can edit
		$view->authorized = $this->_authorize();
		
		// Set the default sort
		$default_sort = 'rating';
		if ($this->config->get('show_ranking')) {
			$default_sort = 'ranking';
		}
		
		// Set some filters
		$view->filters = array();
		$view->filters['tag']    = ($view->tag2) ? $view->tag2 : '';
		$view->filters['type']   = $activetype;
		$view->filters['sortby'] = $default_sort;
		$view->filters['limit']  = 10;
		$view->filters['start']  = 0;
		
		// Run query with limit
		$view->results = $rr->getRecords( $view->filters );
		
		$this->type = $view->type;
		if ($activetitle) {
			$this->_task_title = $activetitle;
		} else {
			$this->_task_title = JText::_('COM_RESOURCES_ALL');
		}
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		$this->_getScripts('tagbrowser');
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	// NOTE: This view should only be called through AJAX
	
	protected function browser()
	{
		$database =& JFactory::getDBO();

		// Incoming
		$level = JRequest::getInt( 'level', 0 );
		
		// A container for info to pass to the HTML for the view
		$bits = array();
		$bits['supportedtag'] = $this->config->get('supportedtag');
		
		// Process the level
		switch ($level)
		{
			case 1:
				// Incoming
				$bits['type'] = JRequest::getInt( 'type', 7 );
				$bits['id'] = JRequest::getInt( 'id', 0 );
				$bits['tg'] = JRequest::getVar( 'input', '' );
				$bits['tg2'] = JRequest::getVar( 'input2', '' );
				
				$rt = new ResourcesTags( $database );
				
				// Get tags that have been assigned
				$bits['tags'] = $rt->get_tags_with_objects( $bits['id'], $bits['type'], $bits['tg2'] );
			break;
		
			case 2: 
				// Incoming
				$bits['type'] = JRequest::getInt( 'type', 7 );
				$bits['id'] = JRequest::getInt( 'id', 0 );
				$bits['tag'] = JRequest::getVar( 'input', '' );
				$bits['tag2'] = JRequest::getVar( 'input2', '' );
				$bits['sortby'] = JRequest::getVar( 'sortby', 'title' );
				$bits['filter']  = JRequest::getVar( 'filter', array('level0','level1','level2','level3','level4') );

				if ($bits['tag'] == $bits['tag2']) {
					$bits['tag2'] = '';
				}
				
				// Get parameters
				$bits['params'] = $this->config;
				
				// Get extra filter options
				$bits['filters'] = array();
				if ($this->config->get('show_audience') && $bits['type'] == 7) {
					include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'resources.audience.php');
					$rL = new ResourceAudienceLevel( $database );
					$bits['filters'] = $rL->getLevels();
				}
				
				$rt = new ResourcesTags( $database );
				$bits['rt'] = $rt;
				
				// Get resources assigned to this tag
				$bits['tools'] = $rt->get_objects_on_tag( $bits['tag'], $bits['id'], $bits['type'], $bits['sortby'], $bits['tag2'], $bits['filter'] );
				
				// Set the typetitle
				$bits['typetitle'] = JText::_('COM_RESOURCES');
				
				// See if we can load the type so we can set the typetitle
				if (isset($bits['type']) && $bits['type'] != 0) {
					$t = new ResourcesType( $database );
					$t->load( $bits['type'] );
					$bits['typetitle'] = stripslashes($t->type);
				}
				
				$bits['supportedtagusage'] = $rt->getTagUsage( $bits['supportedtag'], 'id' );
			break;
			
			case 3:
				// Incoming (should be a resource ID)
				$id = JRequest::getInt( 'input', 0 );
			
				$rt = new ResourcesTags( $database );
				$bits['rt'] = $rt;
				$bits['config'] = $this->config;
			
				// Get resource
				$resource = new ResourcesResource( $database );
				$resource->load( $id );
				$resource->ranking = round($resource->ranking, 1);
				
				// Get parameters and merge with the component params
				$rparams =& new JParameter( $resource->params );
				$params = $this->config;
				$params->merge( $rparams );
				$bits['params'] = $params;
				
				// Version checks (tools only)
				if ($resource->type == 7 && $resource->alias) {
					$tables = $database->getTableList();
					$table = $database->_table_prefix.'tool_version';

					if (in_array($table,$tables)) {
						// Load the tool version
						$tv = new ToolVersion( $database );
						//$tool = new ToolVersion( $database );
						$tool = $tv->getVersionInfo('','current', $resource->alias );

						// Replace resource info with requested version
						if ($tool) {
							$tool = $tool[0];
						}
						// get contribtool params
						$tparams =& JComponentHelper::getParams( 'com_contribtool' );
						$tv->compileResource($tool, '', &$resource, '', $tparams);
					}
				}
			
				// Generate the SEF
				if ($resource->alias) {
					$sef = JRoute::_('index.php?option='.$this->_option.'&alias='. $resource->alias);
				} else {
					$sef = JRoute::_('index.php?option='.$this->_option.'&id='. $resource->id);
				}
				
				// Get resource helper
				$helper = new ResourcesHelper($resource->id, $database);
				$helper->getFirstChild();
				
				// Get the first child
				if ($helper->firstChild || $resource->type == 7) {
					$bits['primary_child'] = ResourcesHtml::primary_child( $this->_option, $resource, $helper->firstChild, '' );
				}
				
				// Get Resources plugins
				JPluginHelper::importPlugin( 'resources' );
				$dispatcher =& JDispatcher::getInstance();

				// Get the sections
				$bits['sections'] = $dispatcher->trigger( 'onResources', array($resource, $this->_option, array('about'), 'metadata') );
				
				// Fill our container
				$bits['resource'] = $resource;
				$bits['helper'] = $helper;
				$bits['sef'] = $sef;
			break;
		}
	
		// Instantiate a new view
		$view = new JView( array('name'=>'browse','layout'=>'tags_list') );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->level = $level;
		$view->bits = $bits;
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function play()
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$this->resid = JRequest::getInt( 'resid', 0 );
		$id = JRequest::getInt( 'id', 0 );
		
		$helper = new ResourcesHelper( $id, $database );
		
		// Do we have a child ID?
		if (!$this->resid) {
			// No ID, default to the first child
			$helper->getFirstChild();

			$this->resid = $helper->firstChild->id;
		}
		
		// We have an ID, load it
		$activechild = new ResourcesResource( $database );
		$activechild->load( $this->resid );

		// Do some work on the child's path to make sure it's kosher
		if ($activechild->path) {
			$activechild->path = stripslashes($activechild->path);
			
			if (substr($activechild->path, 0, 7) == 'http://' 
			 || substr($activechild->path, 0, 8) == 'https://'
			 || substr($activechild->path, 0, 6) == 'ftp://'
			 || substr($activechild->path, 0, 9) == 'mainto://'
			 || substr($activechild->path, 0, 9) == 'gopher://'
			 || substr($activechild->path, 0, 7) == 'file://'
			 || substr($activechild->path, 0, 7) == 'news://'
			 || substr($activechild->path, 0, 7) == 'feed://'
			 || substr($activechild->path, 0, 6) == 'mms://') {
				// Do nothing
			} else {
				if (substr($activechild->path, 0, 1) != DS) { 
					$activechild->path = DS.$activechild->path;
					if (substr($activechild->path, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath')) {
						// Do nothing
					} else {
						$activechild->path = $this->config->get('uploadpath').$activechild->path;
					}
				}
			}
		}
		
		// Store the object in our registry
		$this->activechild = $activechild;
		
		// Viewing via AJAX?
		$no_html = JRequest::getInt( 'no_html', 0 );
		if ($no_html) {
			$resource = new ResourcesResource( $database );
			$resource->load( $id );
			
			// Instantiate a new view
			$view = new JView( array('name'=>'view','layout'=>'play') );
			$view->option = $this->_option;
			$view->config = $this->config;
			$view->database = $database;
			$view->resource = $resource;
			$view->helper = $helper;
			$view->resid = $this->resid;
			$view->activechild = $activechild;
			$view->no_html = $no_html;
			$view->fsize = 0;

			// Output HTML
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {
			// Push on through to the view
			$this->view();
		}
	}
	
	//-----------

	protected function view()
	{
		// Incoming
		$id       = JRequest::getInt( 'id', 0 );            // Rsource ID (primary method of identifying a resource)
		$alias    = JRequest::getVar( 'alias', '' );        // Alternate method of identifying a resource
		$fsize    = JRequest::getVar( 'fsize', '' );        // A parameter to see file size without formatting
		$revision = JRequest::getVar( 'rev', '' );          // Get svk revision of a tool
		$tab      = JRequest::getVar( 'active', 'about' );  // The active tab (section)
		
		// Ensure we have an ID or alias to work with
		if (!$id && !$alias) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Load the resource
		$resource = new ResourcesResource( $database );
		if ($alias) {
			$alias = str_replace(':','-',$alias);
			$resource->loadAlias( $alias );
			$id = $resource->id;
		} else {
			$resource->load( $id );
			$alias = $resource->alias;
		}

		// Make sure we got a result from the database
		if (!$resource || !$resource->title) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		}
		
		// Make sure the resource is published and standalone
		if ($resource->published == 0 || $resource->standalone != 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Make sure they have access to view this resource
		if ($this->checkGroupAccess($resource)) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Build the pathway
		$normalized_valid_chars = 'a-zA-Z0-9';
		$typenorm = preg_replace("/[^$normalized_valid_chars]/", "", $resource->getTypeTitle());
		$typenorm = strtolower($typenorm);
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem($resource->getTypeTitle(),JRoute::_('index.php?option='.$this->_option.'&type='.$typenorm));

		// Tool development version requested
		$juser =& JFactory::getUser();
		if ($juser->get('guest') && $revision=='dev') {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		if (($revision=='dev' && $alias) or (!$revision && $resource->type==7 && $resource->published!=1) ) {
			$objT = new Tool( $database );
			$toolid = $objT->getToolId($alias);
			if (!$this->check_toolaccess($toolid)) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Whew! Finally passed all the checks
		// Let's get down to business...
		$this->resource = $resource;
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Push some scripts to the template
		$this->_getScripts();
		
		// Version checks (tools only)
		$alltools = array();
		$thistool = '';
		$curtool  = '';

		if ($resource->type == 7 && $resource->alias) {		
			$tables = $database->getTableList();
			$table = $database->_table_prefix.'tool_version';
			
			// Get contribtool params
			$tparams =& JComponentHelper::getParams( 'com_contribtool' );
			$ldap = $tparams->get('ldap_read');
		
			if (in_array($table,$tables)) {
				$tv = new ToolVersion( $database );
				$tv->getToolVersions( '', $alltools, $alias, $ldap); 

				if ($alltools) {
					foreach ($alltools as $tool) 
					{
						// Archive version, if requested
						if (($revision && $tool->revision == $revision && $revision != 'dev') or ($revision == 'dev' and $tool->state==3) ) {
							$thistool = $tool;
						}
						// Current version
						if ($tool->state == 1 && count($alltools) > 1 &&  $alltools[1]->version == $tool->version) {
							$curtool = $tool;
						}
						// Dev version
						if (!$revision && count($alltools)==1 && $tool->state==3) {
							$thistool = $tool;
							$revision = 'dev';
						}
					}
	
					if (!$thistool && !$curtool && count($alltools) > 1) { 
						// Tool is retired, display latest unpublished version
						$thistool = $alltools[1];
						$revision = $alltools[1]->revision;
					}
	
					if ($curtool && $thistool && $thistool == $curtool) { 
						// Display default resource page for current version
						$thistool = '';
					}			
				}
			
				// Replace resource info with requested version
				$tv->compileResource($thistool, $curtool, &$resource, $revision, $tparams);
			}
		}

		// Record the hit
		//$resource->hit( $id );
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );

		// Is the visitor authorized to edit this resource?
		$helper->getContributorIDs();
		$authorized = $this->_authorize( $helper->contributorIDs );
		
		// Do not show for tool versions
		if ($thistool && $revision!='dev') {
			$authorized = false;
		}
		
		// Get Resources plugins
		JPluginHelper::importPlugin( 'resources' );
		$dispatcher =& JDispatcher::getInstance();
		
		$sections = array();
		$cats = array();
		
		// We need to do this here because we need some stats info to pass to the body
		if (!$thistool) {
			// Trigger the functions that return the areas we'll be using
			$cats = $dispatcher->trigger( 'onResourcesAreas', array($resource) );
			
			// Get the sections
			$sections = $dispatcher->trigger( 'onResources', array($resource, $this->_option, array($tab), 'all') );
		}
	
		$available = array('play');
		foreach ($cats as $cat) 
		{
			$name = key($cat);
			if ($name != '') {
				$available[] = $name;
			}
		}
		if ($tab != 'about' && !in_array($tab, $available)) {
			$tab = 'about';
		}
		
		// Get parameters and merge with the component params
		$rparams =& new JParameter( $resource->params );
		$params = $this->config;
		$params->merge( $rparams );
		
		// Get attributes
		$attribs =& new JParameter( $resource->attribs );
		
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			ximport('xuserhelper');
			$xgroups = XUserHelper::getGroups($juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->getUsersGroups($xgroups);
		} else {
			$usersgroups = array();
		}
		
		$no_html = JRequest::getInt( 'no_html', 0 );
		
		$body = '';
		// Build the HTML of the "about" tab
		if ($resource->type == 7 && $resource->alias && !$no_html && strtolower($tab) == 'about') {	
			// Tool page view
			$body = ResourcesHtml::abouttool( $database, $authorized, $usersgroups, $resource, $helper, $this->config, $sections, $thistool, $curtool, $alltools, $revision, $params, $attribs, $this->_option, $fsize );
		} else if (strtolower($tab) == 'about') {
			// Default view of about tab
			$body = ResourcesHtml::aboutnontool( $database, $authorized, $usersgroups, $resource, $helper, $this->config, $sections, $params, $attribs, $this->_option, $fsize );
		}
		
		// Add the default "About" section to the beginning of the lists
		$cat = array();
		$cat['about'] = JText::_('COM_RESOURCES_ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));
		
		// Display different main text if "playing" a resource
		if ($this->_task == 'play') {
			$activechild = NULL;
			if (is_object($this->activechild)) {
				$activechild = $this->activechild;
			}
			
			$view = new JView( array('name'=>'view','layout'=>'play') );
			$view->option = $this->_option;
			$view->config = $this->config;
			$view->database = $database;
			$view->resource = $resource;
			$view->helper = $helper;
			$view->resid = $this->resid;
			$view->activechild = $activechild;
			$view->no_html = 0;
			$view->fsize = 0;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$body = $view->loadTemplate();
			
			$cat = array();
			$cat['play'] = JText::_('COM_RESOURCES_PLAY');
			$cats[] = $cat;
			$sections[] = array('html'=>$body,'metadata'=>'');
			$tab = 'play';
		}
		
		// Get filters (for series & workshops listing)
		$filters = array();
		$defaultsort = ($resource->type == 31) ? 'date' : 'ordering';
		$defaultsort = ($resource->type == 31 && $this->config->get('show_ranking')) ? 'ranking' : $defaultsort;
		$filters['sortby'] = JRequest::getVar( 'sortby', $defaultsort );
		$filters['limit'] = JRequest::getInt( 'limit', 0 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0 );
		$filters['id']    = $resource->id;

		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_option)).': '.stripslashes($resource->title) );
		
		$pathway->addItem(stripslashes($resource->title),JRoute::_('index.php?option='.$this->_option.'&id='.$resource->id));
		
		// Get the type
		$t = new ResourcesType( $database );
		$t->load($resource->type);
		
		// Normalize the title
		// This is so we can determine the type of resource template to display
		// For example, Learning Modules => learningmodules
		$type_alias = '';
		if ($t) {	
			$type_alias = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $t->type));
		}
		
		// Determine the layout we're using
		$v = array('name'=>'view');
		$app =& JFactory::getApplication();
		if ($type_alias 
		 && (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->_option.DS.'view'.DS.$type_alias.'.php') 
		 || is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'views'.DS.'view'.DS.'tmpl'.DS.$type_alias.'.php'))) {
			$v['layout'] = $type_alias;
		}
		// Instantiate a new view
		$view = new JView( $v );
		$view->filters = $filters;
		if ($resource->type == 7 && $resource->alias) {
			$view->thistool = $thistool;
			$view->curtool = $curtool;
			$view->alltools = $alltools;
			$view->revision = $revision;
		}
		$view->config = $this->config;
		$view->option = $this->_option;
		$view->resource = $resource;
		$view->params = $params;
		$view->authorized = $authorized;
		$view->attribs = $attribs;
		$view->fsize = $fsize;
		$view->cats = $cats;
		$view->tab = $tab;
		$view->sections = $sections;
		$view->database = $database;
		$view->usersgroups = $usersgroups;
		$view->helper = $helper;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output HTML
		if ($no_html) {
			$jconfig =& JFactory::getConfig();
			$css = $jconfig->getValue('config.live_site').DS;
			
			$app =& JFactory::getApplication();
			if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->_option.DS.'resources.css')) {
				$css .= 'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->_option.DS.'resources.css';
			} else {
				$css .= 'components'.DS.$this->_option.DS.'resources.css';
			}
			
			$html = '<div id="nb-resource">'.$view->loadTemplate().'</div>';
			$html = str_replace( '"', '\"', $html );
			$html = str_replace( "\n", " ", $html );
			$html = str_replace( "\r", " ", $html );
			print( "var head = document.getElementsByTagName('head')[0];");
			print( "var sheet = document.createElement('link');
				sheet.href = '".$css."';
				sheet.setAttribute('type','text/css');
				head.appendChild(sheet);");
			print( "document.write( \"". $html ."\" );" );
		} else {		
			$view->display();
		}
	}

	//-----------
	
	protected function feed() 
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');
		
		$database =& JFactory::getDBO();
		
		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		ximport('xfeed');
		$doc = new XDocumentFeed;
		$app =& JFactory::getApplication();
		$params =& $app->getParams();

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$alias = JRequest::getVar( 'alias', '' );
		
		// Ensure we have an ID or alias to work with
		if (!$id && !$alias) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Load the resource
		$resource = new ResourcesResource( $database );
		if ($alias) {
			$resource->loadAlias( $alias );
			$id = $resource->id;
		} else {
			$resource->load( $id );
			$alias = $resource->alias;
		}

		// Make sure we got a result from the database
		if (!$resource) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		}
		
		// Make sure the resource is published and standalone
		if ($resource->published == 0 || $resource->standalone != 1) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Make sure they have access to view this resource
		if ($this->checkGroupAccess($resource)) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		// Incoming
		$filters = array();
		if ($resource->type == 2) {
			$filters['sortby'] = JRequest::getVar( 'sortby', 'ordering' );
		} else {
			$filters['sortby'] = JRequest::getVar( 'sortby', 'ranking' );
		}
		$filters['limit'] = JRequest::getInt( 'limit', 100 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0 );
		$filters['year']  = JRequest::getInt( 'year', 0 );
		$filters['id']    = $resource->id;
		
		$feedtype = JRequest::getVar( 'format', 'audio' );
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		
		$rows = $helper->getStandaloneChildren( $filters );
		
		// Get HUB configuration
		$jconfig =& JFactory::getConfig();

		$juri =& JURI::getInstance();
		$base = $juri->base();
		if (substr($base, -1) == DS) {
			$base = substr($base, 0, -1);
		}

		// Build some basic RSS document information
		$dtitle = $jconfig->getValue('config.sitename').' - '.Hubzero_View_Helper_Html::purifyText(stripslashes($resource->title));
		$doc->title = trim(Hubzero_View_Helper_Html::shortenText(html_entity_decode($dtitle), 250, 0));
		$doc->description = Hubzero_View_Helper_Html::xhtml(html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($resource->introtext))));
		$doc->copyright = JText::sprintf('COM_RESOURCES_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('COM_RESOURCES_RSS_CATEGORY');
		$doc->link = JRoute::_('index.php?option='.$this->_option.'&id='.$resource->id);
		
		$rt = new ResourcesTags($database);
		$rtags = $rt->get_tags_on_object($resource->id, 0, 0, null, 0, 1);
		$tagarray = array();
		$categories = array();
		$subcategories = array();
		if ($rtags) {
			foreach ($rtags as $tag) 
			{
				if (substr($tag['tag'], 0, 6) == 'itunes') {
					$tbits = explode(':',$tag['raw_tag']);
					if (count($tbits) > 2) {
						$subcategories[] = end($tbits);
					} else {
						$categories[] = str_replace('itunes:','',$tag['raw_tag']);
					}
				} elseif ($tag['admin'] == 0) {
					$tagarray[] = $tag['raw_tag'];
				}
			}
		}
		$tags = implode( ', ', $tagarray );
		//$tags = $rt->get_tag_string( $resource->id, 0, 0, 0, 0, 0 );
		$tags = trim(Hubzero_View_Helper_Html::shortenText($tags, 250, 0));
		if (substr($tags,-1,1) == ',') {
			$tags = substr($tags, 0, -1);
		}
		
		$helper->getUnlinkedContributors();
		$cons = $helper->ul_contributors;
		$cons = explode(';',$cons);
		$author = '';
		foreach ($cons as $con) 
		{
			if ($con) {
				$author = trim($con);
				break;
			}
		}
		
		$doc->itunes_summary = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($resource->introtext)));
		if (count($categories) > 0) {
			$doc->itunes_category = $categories[0];
			if (count($subcategories) > 0) {
				$doc->itunes_subcategories = $subcategories;
			}
		}
		$doc->itunes_explicit = "no";
		$doc->itunes_keywords = $tags;
		$doc->itunes_author = $author;
		
		$dimg = $this->checkForImage('itunes_artwork', $this->config->get('uploadpath'), $resource->created, $resource->id);
		if ($dimg) {
			$dimage = new XFeedImage();
			$dimage->url = $dimg;
			$dimage->title = trim(Hubzero_View_Helper_Html::shortenText(html_entity_decode($dtitle.' '.JText::_('COM_RESOURCES_RSS_ARTWORK')), 250, 0));
			$dimage->link = $base.$doc->link;
			
			$doc->itunes_image = $dimage;
		}
		
		$owner = new XFeedItunesOwner;
		$owner->email = $jconfig->getValue('config.mailfrom');
		$owner->name = $jconfig->getValue('config.sitename');
		
		$doc->itunes_owner = $owner;

		// Start outputing results if any found
		if (count($rows) > 0) {
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to resource
				$link = JRoute::_('index.php?option='.$this->_option.'&id='.$row->id);
				if (substr($link, 0, 1) != DS) { 
					$link = DS.$link;
				}

				// Strip html from feed item description text
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($row->introtext)));
				$author = '';
				@$date = ( $row->publish_up ? date( 'r', strtotime($row->publish_up) ) : '' );
				
				// Instantiate a resource helper
				$rhelper = new ResourcesHelper($row->id, $database);
				
				// Get any podcast/vodcast files
				$podcast = '';

				$rhelper->getChildren();
				if ($rhelper->children && count($rhelper->children) > 0) {
					$grandchildren = $rhelper->children;
					foreach ($grandchildren as $grandchild) 
					{
						$ftype = ResourcesHtml::getFileExtension($grandchild->path);
						if (stripslashes($grandchild->introtext) != '') {
							$gdescription = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($grandchild->introtext)));
						}
						switch ($feedtype) 
						{
							case 'slides':
								//if ($ftype == 'ppt' || $ftype == 'pdf' || $ftype == 'doc') {
								if ($grandchild->logicaltype == 14) {
									$podcast = $grandchild->path;
								}
							break;
							
							case 'video':
								$vts = array('mp4', 'm4v', 'mov', 'wmv', 'avi', 'asf', 'qt', 'mp2', 'mpeg', 'mpe', 'mpg', 'mpv2');
								if (in_array($ftype, $vts)) {
									$podcast = $grandchild->path;
								}
							break;
							
							case 'audio':
							default:
								$ats = array('m4a', 'mp3', 'wav', 'aiff', 'aif', 'ra', 'ram');
								if (in_array($ftype, $ats)) {
									$podcast = $grandchild->path;
								}
							break;
						}
					}
				}

				// Get the contributors of this resource
				$rhelper->getContributors();
				$author = strip_tags($rhelper->contributors);
				
				// Get attributes
				$attribs =& new JParameter( $row->attribs );
				
				$rtt = new ResourcesTags($database);
				$rtags = $rtt->get_tag_string( $row->id, 0, 0, 0, 0, 0 );
				$rtags = trim(Hubzero_View_Helper_Html::shortenText($rtags, 250, 0));
				if (substr($rtags,-1,1) == ',') {
					$rtags = substr($rtags, 0, -1);
				}
				
				// Load individual item creator class
				$item = new XFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = ($row->typetitle) ? $row->typetitle : '';
				$item->author      = $author;
				
				$img = $this->checkForImage('itunes_artwork', $this->config->get('uploadpath'), $row->created, $row->id);
				if ($img) {
					$image = new XFeedImage();
					$image->url = $img;
					$image->title = $title.' '.JText::_('COM_RESOURCES_RSS_ARTWORK');
					$image->link = $base.$link;
					
					$item->itunes_image = $image;
				}
				
				$item->itunes_summary = $description;
				$item->itunes_explicit = "no";
				$item->itunes_keywords = $rtags;
				$item->itunes_author = $author;
				if ($attribs->get('duration')) {
					$item->itunes_duration = $attribs->get('duration');
				}
				
				if ($podcast) {
					$podcastp = $podcast;
					$podcast = $this->fullPath($podcast);
					if (substr($podcastp, 0, 1) != DS) { 
						$podcastp = DS.$podcastp;
					}
					if (substr($podcastp, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath')) {
						// Do nothing
					} else {
						$podcastp = $this->config->get('uploadpath').$podcastp;
					}
					$podcastp = JPATH_ROOT.$podcastp;
					if (file_exists( $podcastp )) {
						$fs = filesize( $podcastp );

						$enclosure = new XFeedEnclosure; //JObject;
						$enclosure->url = $podcast;
						switch ( ResourcesHtml::getFileExtension($podcast) ) 
						{
							case 'm4v': $enclosure->type = 'video/x-m4v'; break;
							case 'mp4': $enclosure->type = 'video/mp4'; break;
							case 'wmv': $enclosure->type = 'video/wmv'; break;
							case 'mov': $enclosure->type = 'video/quicktime'; break;
							case 'qt': $enclosure->type = 'video/quicktime'; break;
							case 'mpg': $enclosure->type = 'video/mpeg'; break;
							case 'mpeg': $enclosure->type = 'video/mpeg'; break;
							case 'mpe': $enclosure->type = 'video/mpeg'; break;
							case 'mp2': $enclosure->type = 'video/mpeg'; break;
							case 'mpv2': $enclosure->type = 'video/mpeg'; break;
							
							case 'mp3': $enclosure->type = 'audio/mpeg'; break;
							case 'm4a': $enclosure->type = 'audio/x-m4a'; break;
							case 'aiff': $enclosure->type = 'audio/x-aiff'; break;
							case 'aif': $enclosure->type = 'audio/x-aiff'; break;
							case 'wav': $enclosure->type = 'audio/x-wav'; break;
							case 'ra': $enclosure->type = 'audio/x-pn-realaudio'; break;
							case 'ram': $enclosure->type = 'audio/x-pn-realaudio'; break;
							
							case 'ppt': $enclosure->type = 'application/vnd.ms-powerpoint'; break;
							case 'pps': $enclosure->type = 'application/vnd.ms-powerpoint'; break;
							case 'pdf': $enclosure->type = 'application/pdf'; break;
							case 'doc': $enclosure->type = 'application/msword'; break;
							case 'txt': $enclosure->type = 'text/plain'; break;
							case 'html': $enclosure->type = 'text/html'; break;
							case 'htm': $enclosure->type = 'text/html'; break;
						}
						$enclosure->length = $fs;

						$item->guid = $podcast;
						$item->enclosure = $enclosure;
					}
					// Loads item info into rss array
					$doc->addItem( $item );
				}
			}
		}
		
		// Output the feed
		echo $doc->render();
	}
	
	//-----------
	
	private function checkForImage($filename, $upath, $created, $id) 
	{
		$path = ResourcesHtml::build_path( $created, $id, '' );
		
		// Ensure the path has format of /path
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
		}
		if (substr($path, -1) == DS) { 
			$path = substr($path, 0, -1);
		}
		// Ensure the upath has format of /upath
		if (substr($upath, 0, 1) != DS) { 
			$upath = DS.$upath;
		}
		if (substr($upath, -1) == DS) { 
			$upath = substr($upath, 0, -1);
		}

		$d = @dir(JPATH_ROOT.$upath.$path);

		$images = array();

		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$upath.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|jpg|png", $img_file )) {
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}

		$b = 0;
		$img = '';
		if ($images) {
			foreach ($images as $ima) 
			{
				if (substr($ima, 0, strlen($filename)) == $filename) {
					$img = $ima;
					break;
				}
			} 
		}
		
		if (!$img) {
			return '';
		}
		
		$juri =& JURI::getInstance();
		$base = $juri->base();
		
		// Ensure the base has format of http://base (no trailing slash)
		if (substr($base, -1) == DS) {
			$base = substr($base, 0, -1);
		}
		// http://base/upath/path/img
		return $base.$upath.$path.DS.$img;
	}
	
	//-----------
	// NOTE: This view should only be called through AJAX

	protected function plugin()
	{
		// Incoming
		$trigger = trim(JRequest::getVar( 'trigger', '' ));
		
		// Ensure we have a trigger
		if (!$trigger) {
			echo ResourcesHtml::error( JText::_('COM_RESOURCES_NO_TRIGGER_FOUND') );
			return;
		}
		
		// Get Resources plugins
		JPluginHelper::importPlugin( 'resources' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Call the trigger
		$results = $dispatcher->trigger( $trigger, array($this->_option) );
		if (is_array($results)) {
			$html = $results[0]['html'];
		}
		
		// Output HTML
		echo $html;
	}

	//-----------

	protected function download()
	{
		// Get some needed libraries
		ximport('xserver');

		$database =& JFactory::getDBO();

		// Ensure we have a database object
		if (!$database) {
			JError::raiseError( 500, JText::_('COM_RESOURCES_DATABASE_NOT_FOUND') );
			return;
		}
		
		// Incoming
		$id    = JRequest::getInt('id',0);
		$alias = JRequest::getVar('alias','');

		// Load the resource
		$resource = new ResourcesResource( $database );
		if ($alias && !$resource->loadAlias( $alias )) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		} elseif (!$resource->load( $id )) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		}

		// Check if the resource is for logged-in users only and the user is logged-in
		$juser =& JFactory::getUser();
		if ($resource->access == 1 && $juser->get('guest')) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		// Check if the resource is "private" and the user is allowed to view it
		if ($resource->access == 4 || $resource->access == 3 || !$resource->standalone) {
			if ($this->checkGroupAccess($resource)) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}
		
		// Ensure we have a path
		if (empty($resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_FILE_NOT_FOUND') );
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_BAD_FILE_PATH') );
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_BAD_FILE_PATH') );
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_BAD_FILE_PATH') );
			return;
		}
		// Disallow \
		if (strpos('\\',$resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_BAD_FILE_PATH') );
			return;
		}
		// Disallow ..
		if (strpos('..',$resource->path)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_BAD_FILE_PATH') );
			return;
		}
		
		// Get the configured upload path
		$base_path = $this->config->get('uploadpath');
		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Does the path start with a slash?
		if (substr($resource->path, 0, 1) != DS) { 
			$resource->path = DS.$resource->path;
			// Does the beginning of the $resource->path match the config path?
			if (substr($resource->path, 0, strlen($base_path)) == $base_path) {
				// Yes - this means the full path got saved at some point
			} else {
				// No - append it
				$resource->path = $base_path.$resource->path;
			}
		}
		
		// Add JPATH_ROOT
		$filename = JPATH_ROOT.$resource->path;
		
		// Ensure the file exist
		if (!file_exists($filename)) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_FILE_NOT_FOUND').' '.$filename );
			return;
		}
		
		// Initiate a new content server and serve up the file
		$xserver = new XContentServer();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support
		
		if (!$xserver->serve()) {
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_RESOURCES_SERVER_ERROR') );
		} else {
			exit;
		}
		return;
	}

	//----------------------------------------------------------
	// Tools
	//----------------------------------------------------------

	protected function sourcecode()
	{
		// Get tool instance
		$tool = JRequest::getVar( 'tool', 0 );
		
		// Ensure we have a tool
		if (!$tool) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		}
		
		ximport('xserver');
		
		$database =& JFactory::getDBO();
		
		// Load the tool version
		$tv = new ToolVersion( $database );
		$tv->loadFromInstance( $tool );
		
		// Concat tarball name for this version
		$tarname = $tv->toolname.'-r'.$tv->revision.'.tar.gz';
		
		// Get contribtool params
		$tparams =& JComponentHelper::getParams( 'com_contribtool' );
		$tarball_path = $tparams->get('sourcecodePath');
		$tarpath = $tarball_path.DS.$tv->toolname.DS;
		$opencode = ($tv->codeaccess=='@OPEN') ? 1 : 0;
					
		// Is a tarball available?
		if (!file_exists( $tarpath . $tarname )) {
			// File not found
			JError::raiseError( 404, JText::_('COM_RESOURCES_FILE_NOT_FOUND') );
			return;
		}
		
		if (!$opencode) {
			// This tool is not open source
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		// Serve up the file
		$xserver = new XContentServer();
		$xserver->filename($tarpath . $tarname);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas($tarname);
		if (!$xserver->serve_attachment($tarpath . $tarname, $tarname, false)) { // @TODO fix byte range support
			JError::raiseError( 404, JText::_('COM_RESOURCES_SERVER_ERROR') );
		} else {
			exit;
		}
		return;
	}
	
	//-----------
	
	protected function license()
	{
		// Get tool instance
		$tool = JRequest::getVar( 'tool', '' );
		$no_html = JRequest::getVar( 'no_html', 0 );
		
		// Ensure we have a tool to work with
		if (!$tool) {
			JError::raiseError( 404, JText::_('COM_RESOURCES_RESOURCE_NOT_FOUND') );
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Load the tool version
		$row = new ToolVersion( $database );
		$row->loadFromInstance( $tool );
		
		// Output HTML
		if ($row) {
			// Set the page title
			$title = stripslashes($row->title).': '.JText::_('COM_RESOURCES_LICENSE');
			
			// Write title
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
		} else {
			// Set the page title
			$title = JText::_('COM_RESOURCES_PAGE_UNAVAILABLE');
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'license') );
		$view->option = $this->_option;
		$view->config = $this->config;
		$view->row = $row;
		$view->title = $title;
		$view->no_html = $no_html;

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Citations
	//----------------------------------------------------------
	
	protected function citation()
	{
		$database =& JFactory::getDBO();
		
		ximport('fileuploadutils');
		
		$xhub =& XFactory::getHub();
		$hubDOIpath = $this->config->get('doi');
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$format = JRequest::getVar( 'format', 'bibtex' );
		
		// Append DOI handle
		$revision = JRequest::getVar( 'rev', 0 );
		$handle ='';
		if ($revision) {
			$rdoi = new ResourcesDoi( $database );
			$doi = $rdoi->getDoi( $id, $revision );
			
			if ($doi) {
				$handle = $hubDOIpath.'r'.$id.'.'.$doi;
			}
		}
		
		// Load the resource
		$row = new ResourcesResource( $database );
		$row->load( $id );
	
		$thedate = ($row->publish_up != '0000-00-00 00:00:00') 
				 ? $row->publish_up 
				 : $row->created;
		
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getUnlinkedContributors();
		$row->author = $helper->ul_contributors;
	
		// Build the download path
		$path = JPATH_ROOT.$this->config->get('webpath');
		$date = $row->created;
		$dir_resid = FileUploadUtils::niceidformat( $row->id );
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs )) {
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		$path .= DS.$dir_year.DS.$dir_month.DS.$dir_resid.DS;

		if (!is_dir($path)) {
			FileUploadUtils::make_path($path);
		}
		
		// Build the URL for this resource
		$sef = JRoute::_('index.php?option='.$this->_option.a.'id='.$row->id);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		$juri =& JURI::getInstance();
		$url = $juri->base().$sef;

		// Choose the format
		switch ($format) 
		{
			case 'endnote':
				$doc = '';
				switch ($row->type) 
				{
					case 'misc':
					default:
						$doc .= "%0 ".JText::_('COM_RESOURCES_GENERIC')."\r\n";
						break; // generic
				} 
				$doc .= "%D " . JHTML::_('date', $thedate, '%Y') . "\r\n";
				$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";

				$author_array = explode(";", $row->author);
				foreach($author_array as $auth) 
				{
					$auth = preg_replace( '/{{(.*?)}}/s', '', $auth );
					if (!strstr($auth,',')) {
						$bits = explode(' ',$auth);
						$n = array_pop($bits).', ';
						$bits = array_map('trim',$bits);
						$auth = $n.trim(implode(' ',$bits));
					}
					$doc .= "%A " . trim($auth) . "\r\n";
				} 
				$doc .= "%U " . $url . "\r\n";
				if ($thedate) {
					$doc .= "%8 " . JHTML::_('date', $thedate, '%b') . "\r\n";
				}
				//$doc .= "\r\n";
				if ($handle) {
					$doc .= "%1 " .'doi:'.  $handle;
					$doc .= "\r\n";
				}

				$file = 'resource'.$id.'.enw';
				$mime = 'application/x-endnote-refer';
			break;
			
			case 'bibtex':
			default:
				include_once( JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'BibTex.php' );

				$bibtex = new Structures_BibTex();
				$addarray = array();
				$addarray['type']    = 'misc';
				$addarray['cite']    = $this->_config['sitename'].$row->id;
				$addarray['title']   = stripslashes($row->title);
				$auths = explode(';',$row->author);
				for ($i=0, $n=count( $auths ); $i < $n; $i++)
				{
					$author = trim($auths[$i]);
					$author_arr = explode(',',$author);
					$author_arr = array_map('trim',$author_arr);
					
					$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
					$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';
				}
				$addarray['month'] = JHTML::_('date', $thedate, '%b');
				$addarray['url']   = $url;
				$addarray['year']  = JHTML::_('date', $thedate, '%Y');
				if ($handle) {
					$addarray['doi'] = $handle;
				}
				
				$bibtex->addEntry($addarray);

				$file = 'resource_'.$id.'.bib';
				$mime = 'application/x-bibtex';
				$doc = $bibtex->bibTex();
			break;
		}
		
		// Write the contents to a file
		$fp = fopen($path.$file, "w") or die("can't open file"); 
		fwrite($fp, $doc);
		fclose($fp);
		
		$this->serveup(false, $path, $file, $mime);
		
		die; // REQUIRED
	}
	
	//-----------
	
	protected function serveup($inline = false, $p, $f, $mime)
	{
		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]) ) 
					? $_SERVER["HTTP_USER_AGENT"] 
					: $HTTP_USER_AGENT;

		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());

		$fsize = filesize( $p.$f );
		$mod_date = date('r', filemtime( $p.$f ) );

		$cont_dis = $inline ? 'inline' : 'attachment';

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");

        header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
        header("Content-Type: "    . $mime ); // MIME type
        header("Content-Length: "  . $fsize);

 		// No encoding - we aren't using compression... (RFC1945)
		
        $this->readfile_chunked($p.$f);
        // The caller MUST 'die();'
    }
    
	//-----------
	
	protected function readfile_chunked($filename,$retbytes=true) 
	{
		$chunksize = 1*(1024*1024); // How many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) 
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // Return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	//----------------------------------------------------------
	// Other Views
	//----------------------------------------------------------

	protected function savetags()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->view();
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$tags = JRequest::getVar( 'tags', '' );
		$no_html = JRequest::getInt( 'no_html', 0 );
		
		// Process tags
		$database =& JFactory::getDBO();
		$rt = new ResourcesTags( $database );
		$rt->tag_object($juser->get('id'), $id, $tags, 1, 0);
	
		if (!$no_html) {
			// Push through to the resource view
			$this->view();
		}
	}
	
	//----------------------------------------------------------
	//	Checks
	//----------------------------------------------------------

	private function _authorize($contributorIDs=array()) 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}
		
		// Check if they're the resource creator
		$resource = $this->resource;
		if (is_object($resource) && $resource->created_by == $juser->get('id')) {
			return true;
		}
		
		// Check if they're a resource "contributor"
		if (is_array($contributorIDs)) {
			if (in_array($juser->get('id'), $contributorIDs)) {
				return true;
			}
		}
		
		return false;
	}
	
	//-----------
	
	private function checkGroupAccess($resource)
	{	
		//$juser =& XFactory::getUser();
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			// Check if they're a site admin (from Joomla)
			if ($juser->authorize($this->_option, 'manage')) {
				return false;
			}
			
			ximport('xuserhelper');
			$xgroups = XUserHelper::getGroups($juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->getUsersGroups($xgroups);
		} else {
			$usersgroups = array();
		}
		
		// Get the list of groups that can access this resource
		$allowedgroups = $resource->getGroups();
		if ($resource->standalone != 1) {
			$database =& JFactory::getDBO();
			$helper = new ResourcesHelper( $resource->id, $database );
			$helper->getParents();
			$parents = $helper->parents;
			if (count($parents) == 1) {
				$p = new ResourcesResource( $database );
				$p->load($parents[0]->id);
				$allowedgroups = $p->getGroups();
			}
		}
		$this->allowedgroups = $allowedgroups;
		
		// Find what groups the user has in common with the resource, if any
		$common = array_intersect($usersgroups, $allowedgroups);
		
		// Make sure they have the proper group access
		$restricted = false;
		if ($resource->access == 4 || $resource->access == 3) {
			// Are they logged in?
			if ($juser->get('guest')) {
				// Not logged in
				$restricted = true;
			} else {
				// Logged in
				
				// Check if the user is apart of the group that owns the resource
				// or if they have any groups in common
				if (!in_array($resource->group_owner, $usersgroups) && count($common) < 1) {
					$restricted = true;
				}
			}
		}
		if (!$resource->standalone) {
			if ($p && ($p->access == 4 || $p->access == 3) && count($common) < 1) {
				$restricted = true;
			}
		}
		return $restricted;
	}
	
	//-----------

	private function getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) {
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) {
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}
	
	//-----------
	
	private function fullPath($path) 
	{
		if (substr($path, 0, 7) == 'http://' 
		 || substr($path, 0, 8) == 'https://'
		 || substr($path, 0, 6) == 'ftp://'
		 || substr($path, 0, 9) == 'mainto://'
		 || substr($path, 0, 9) == 'gopher://'
		 || substr($path, 0, 7) == 'file://'
		 || substr($path, 0, 7) == 'news://'
		 || substr($path, 0, 7) == 'feed://'
		 || substr($path, 0, 6) == 'mms://') {
			// Do nothing
		} else {
			if (substr($path, 0, 1) != DS) { 
				$path = DS.$path;
			}
			if (substr($path, 0, strlen($this->config->get('uploadpath'))) == $this->config->get('uploadpath')) {
				// Do nothing
			} else {
				$path = $this->config->get('uploadpath').$path;
			}
		}

		$juri =& JURI::getInstance();
		$base = $juri->base();
		
		if (substr($path, 0, 1) == DS && substr($base, -1) == DS) {
			$path = substr($path, 1);
		}

		return $base.$path;
	}
	
	//-----------
	
	private function check_toolaccess($toolid) 
	{
		$database =& JFactory::getDBO();
		
		$juser =& JFactory::getUser();
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

	 	// Create a Tool object
		$obj = new Tool( $database );
		// check if user in tool dev team
		$developers = $obj->getToolDevelopers($toolid);
		if ($developers) {
			foreach ($developers as $dv) 
			{
				if ($dv->uidNumber == $juser->get('id')) {
					return true;
				}
			}
		}

		return false;
	}
}
?>