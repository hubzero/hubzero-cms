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
	
	private function getTask()
	{
		$task  = JRequest::getVar( 'task', '' );
		$id    = JRequest::getInt( 'id', 0 );
		$alias = JRequest::getVar( 'alias', '' );
		$resid = JRequest::getInt( 'resid', 0 );
		
		if ($resid && !$task) {
			$task = 'play';
		}
		if (($id || $alias) && !$task) {
			$task = 'view';
		}
		$this->_task = $task;

		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ( $this->getTask() ) 
		{
			//case 'minimal':    $this->minimal();    break;
			
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
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}
	
	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function intro() 
	{
		$database =& JFactory::getDBO();
		
		// Get major types
		$t = new ResourcesType( $database );
		$categories = $t->getMajorTypes();
		
		// Push some needed styles and scripts to the template
		$this->getStyles();
		$this->getScripts();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		
		jimport( 'joomla.application.component.view');
		
		// Output HTML
		$view = new JView( array('name'=>'intro') );
		$view->title = JText::_(strtoupper($this->_name));
		$view->categories = $categories;
		$view->option = $this->_option;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browse()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Push some needed styles and scripts to the template
		$this->getStyles();
		$this->getScripts();

		// Set the default sort
		$xhub =& XFactory::getHub();
		$default_sort = 'date';
		if ($this->config->get('show_ranking')) {
			$default_sort = 'ranking';
		}

		// Incoming
		$filters = array();
		$filters['tag']    = JRequest::getVar( 'tag', '' );
		$filters['type']   = JRequest::getVar( 'type', '' );
		$filters['sortby'] = JRequest::getVar( 'sortby', $default_sort );
		$filters['limit']  = JRequest::getInt( 'limit', 25 );
		$filters['start']  = JRequest::getInt( 'limitstart', 0 );

		// Determine if user can edit
		$authorized = $this->_authorize();

		// Get major types
		$t = new ResourcesType( $database );
		$types = $t->getMajorTypes();

		if (!is_int($filters['type'])) {
			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/learningmodules => Learning Modules
			for ($i = 0; $i < count($types); $i++) 
			{	
				$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $types[$i]->type);
				$normalized = strtolower($normalized);

				if (trim($filters['type']) == $normalized) {
					$filters['type'] = $types[$i]->id;
					break;
				}
			}
		}

		// Instantiate a resource object
		$rr = new ResourcesResource( $database );
		
		// Execute count query
		$results = $rr->getCount( $filters );
		$total = ($results && is_array($results)) ? count($results) : 0;
		
		// Run query with limit
		$results = $rr->getRecords( $filters );
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
	
		// Get type if not given
		$title = JText::_(strtoupper($this->_name)).': ';
		if ($filters['type'] != '') {
			$t->load( $filters['type'] );
			$title .= $t->type;
		} else {
			$title .= JText::_('ALL');
		}

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		$pathway->addItem( JText::_('ALL'), 'index.php?option='.$this->_option.a.'task='.$this->_task );
		
		// Output HTML
		echo ResourcesHtml::browse($this->_option, $authorized, $title, $types, $filters, $pageNav, $results, $total, $this->config);
	}

	//-----------

	protected function browsetags() 
	{
		$database =& JFactory::getDBO();
		
		if ($this->config->get('browsetags') == 'off') {
			$this->browse();
			return;
		}
		
		// Push some needed styles and scripts to the template
		$this->getStyles();
		$this->getScripts();
		
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'tagbrowser.js')) {
			$document->addScript('components'.DS.$this->_option.DS.'tagbrowser.js');
		}
		
		// Incoming
		$tag = JRequest::getVar( 'tag', '' );
		$tag2 = JRequest::getVar( 'with', '' );
		$type = strtolower(JRequest::getVar( 'type', 'tools' ));
		$activetype = 0;
		$activetitle = '';
		
		$supportedtag = $this->config->get('supportedtag');
		if (!$tag && $supportedtag && $type == 'tools') {
			$tag = $supportedtag;
		}
		
		// Get major types
		$t = new ResourcesType( $database );
		$types = $t->getMajorTypes();
		
		// Normalize the title
		// This is so we can determine the type of resource to display from the URL
		// For example, /resources/learningmodules => Learning Modules
		for ($i = 0; $i < count($types); $i++) 
		{	
			$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $types[$i]->type);
			$normalized = strtolower($normalized);
			$types[$i]->title = $normalized;
			
			if (trim($type) == $normalized) {
				$activetype = $types[$i]->id;
				$activetitle = $types[$i]->type;
			}
		}
		asort($types);
		
		// Ensure we have a type to display
		if (!$activetype) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Get type if not given
		$title = JText::_(strtoupper($this->_name)).': ';
		if ($activetitle) {
			$title .= $activetitle;
		} else {
			$title .= JText::_('ALL');
		}
		
		// Instantiate a resource object
		$rr = new ResourcesResource( $database );
		
		// Determine if user can edit
		$authorized = $this->_authorize();
		
		// Set the default sort
		$xhub =& XFactory::getHub();
		$default_sort = 'rating';
		if ($this->config->get('show_ranking')) {
			$default_sort = 'ranking';
		}
		
		// Set some filters
		$filters = array();
		$filters['tag']    = ($tag2) ? $tag2 : '';
		$filters['type']   = $activetype;
		$filters['sortby'] = $default_sort;
		$filters['limit']  = 10;
		$filters['start']  = 0;
		
		// Run query with limit
		$results = $rr->getRecords( $filters );
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( JText::_(strtoupper($this->_name)), 'index.php?option='.$this->_option );
		}
		if (count($pathway->getPathWay()) <= 1) {
			if ($activetitle) {
				$pathway->addItem( $activetitle, 'index.php?option='.$this->_option.a.'type='.$type );
			} else {
				$pathway->addItem( JText::_('ALL'), 'index.php?option='.$this->_option.a.'type='.$type );
			}
		}
		
		// Output HTML
		echo ResourcesHtml::browsetags( $this->_option, $title, $types, $activetype, $results, $authorized, $this->config, $supportedtag, $tag, $tag2 );
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

				if ($bits['tag'] == $bits['tag2']) {
					$bits['tag2'] = '';
				}
				
				$rt = new ResourcesTags( $database );
				$bits['rt'] = $rt;
				
				// Get resources assigned to this tag
				$bits['tools'] = $rt->get_objects_on_tag( $bits['tag'], $bits['id'], $bits['type'], $bits['sortby'], $bits['tag2'] );
				
				// Set the typetitle
				$bits['typetitle'] = JText::_('RESOURCES');
				
				// See if we can load the type so we can set the typetitle
				if (isset($bits['type']) && $bits['type'] != 0) {
					$t = new ResourcesType( $database );
					$t->load( $bits['type'] );
					$bits['typetitle'] = stripslashes($t->type);
				}
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
						$tv->compileResource ($tool, '', &$resource, '', $tparams);
					}
				}
			
				// Generate the SEF
				if ($resource->alias) {
					$sef = JRoute::_('index.php?option='.$this->_option.a.'alias='. $resource->alias);
				} else {
					$sef = JRoute::_('index.php?option='.$this->_option.a.'id='. $resource->id);
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
	
		// Output HTML
		echo ResourcesHtml::browser($level,$bits);
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
			
			$sc = (isset($HTTP_COOKIE_VARS['sakaicookie'])) ? $HTTP_COOKIE_VARS['sakaicookie'] : '';
			$activechild->path .= ($sc) ? '?sakai.session='.$sc : '';
		}
		
		// Store the object in our registry
		$this->activechild = $activechild;
		
		$no_html = JRequest::getInt( 'no_html', 0 );
		if ($no_html) {
			$resource = new ResourcesResource( $database );
			$resource->load( $id );
			
			echo ResourcesHtml::play( $database, $resource, $helper, $this->resid, $activechild, '', $no_html );
		} else {
			// Push on through to the view
			$this->view();
		}
	}
	
	//-----------

	protected function view()
	{
		$database =& JFactory::getDBO();
		
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
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
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
		$pathway->addItem($resource->getTypeTitle(),JRoute::_('index.php?option='.$this->_option.a.'type='.$typenorm));

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
		
		// Send the CSS and JS to the template
		$this->getStyles();
		$this->getScripts();
		
		// Version checks (tools only)
		$alltools = array();
		$thistool = '';
		$curtool  = '';

		if ($resource->type == 7 && $resource->alias) {		
			$tables = $database->getTableList();
			$table = $database->_table_prefix.'tool_version';
			
			// get contribtool params
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
			
				// replace resource info with requested version
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
		//$params =& new JParameter( $resource->params );
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
		
		$body = '';
		if (strtolower($tab) == 'about') {
			// Build the HTML of the "about" tab
			$body = ResourcesHtml::about( $database, $authorized, $usersgroups, $resource, $helper, $this->config, $sections, $thistool, $curtool, $alltools, $revision, $params, $attribs, $this->_option, $fsize );

			// Course children
			if ($resource->type == 6) {
				$filters = array();
				$filters['sortby'] = 'ordering';
				$filters['limit'] = 0;
				$filters['start'] = 0;
				$filters['id']    = $resource->id;
				
				$schildren = $helper->getStandaloneChildren( $filters );

				$body .= ResourcesHtml::writeResultsTable( $database, $resource, $schildren, $this->_option );
			}

			// Series/Workshop children
			if ($resource->type == 31 || $resource->type == 2) {
				// Incoming
				$filters = array();
				if ($resource->type == 2) {
					$filters['sortby'] = JRequest::getVar( 'sortby', 'ordering' );
				} else {
					if ($this->config->get('show_ranking')) {
						$filters['sortby'] = JRequest::getVar( 'sortby', 'ranking' );
					} else {
						$filters['sortby'] = JRequest::getVar( 'sortby', 'date' );
					}
				}
				$filters['limit'] = JRequest::getInt( 'limit', 25 );
				$filters['start'] = JRequest::getInt( 'limitstart', 0 );
				$filters['id']    = $resource->id;

				// Get a count of standalone children
				$ccount = $helper->getStandaloneCount( $filters );

				if ($ccount > 0) {
					// Initiate paging for children
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $ccount, $filters['start'], $filters['limit'] );

					// Get children
					$children = $helper->getStandaloneChildren( $filters );

					// Build the results
					$body .= ResourcesHtml::browseChildrenForm( $this->_option, $resource, $filters, $pageNav, $database, $children, $authorized, $this->config );
				}
			}
		}
		
		// Some extra bits that can be displayed if NOT playing a learning module OR displaying tool version
		if (!$thistool) {
			// Trigger the functions that return the sub-areas we'll be using
			$subcats = $dispatcher->trigger( 'onResourcesSubAreas', array($resource) );

			// Get the sub sections
			$subsections = $dispatcher->trigger( 'onResourcesSub', array($resource, $this->_option) );

			// Build the HTML for the sub sections
			$subs = ResourcesHtml::sections( $subsections, $subcats, 'about', '', '' );
			if ($subs) {
				$body .= '<hr />'.n;
				$body .= $subs;
			}
		}
		
		// Add the default "About" section to the beginning of the lists
		$cat = array();
		$cat['about'] = JText::_('ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));
		
		// Display different main text if "playing" a resource
		if ($this->_task == 'play') {
			$activechild = NULL;
			if (is_object($this->activechild)) {
				$activechild = $this->activechild;
			}
			$body = ResourcesHtml::play( $database, $resource, $helper, $this->resid, $activechild, $fsize );
			
			$cat = array();
			$cat['play'] = JText::_('PLAY');
			$cats[] = $cat;
			$sections[] = array('html'=>$body,'metadata'=>'');
			$tab = 'play';
		}

		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.stripslashes($resource->title) );
		
		$pathway->addItem(stripslashes($resource->title),JRoute::_('index.php?option='.$this->_option.a.'id='.$resource->id));
		
		// Start building the final HTML
		$html  = ResourcesHtml::title( $this->_option, $resource, $params, $authorized, $this->config );
		$html .= ResourcesHtml::tabs( $this->_option, $resource->id, $cats, $tab, $resource->alias );
		$html .= ResourcesHtml::sections( $sections, $cats, $tab, 'hide', 'main' );

		// Output HTML
		$no_html = JRequest::getInt( 'no_html', 0 );
		if ($no_html) {
			$jconfig =& JFactory::getConfig();
			$css = $jconfig->getValue('config.live_site').DS;
			
			$app =& JFactory::getApplication();
			if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->_option.DS.'resources.css')) {
				$css .= 'templates'.DS. $app->getTemplate() .DS.'html'.DS.$this->_option.DS.'resources.css';
			} else {
				$css .= 'components'.DS.$this->_option.DS.'resources.css';
			}
			
			$html = '<div id="nb-resource">'.$html.'</div>';
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
			echo $html;
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
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
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
		$filters['limit'] = JRequest::getInt( 'limit', 25 );
		$filters['start'] = JRequest::getInt( 'limitstart', 0 );
		$filters['id']    = $resource->id;
		
		$feedtype = JRequest::getVar( 'format', 'audio' );
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		
		$rows = $helper->getStandaloneChildren( $filters );
		
		// Get HUB configuration
		//$xhub =& XFactory::getHub();
		$jconfig =& JFactory::getConfig();

		$juri =& JURI::getInstance();
		$base = $juri->base();
		if (substr($base, -1) == DS) {
			$base = substr($base, 0, -1);
		}

		// Build some basic RSS document information
		//$doc->title = $jconfig->getValue('config.sitename').' '.JText::_('RESOURCES_RSS_TITLE');
		$dtitle = $jconfig->getValue('config.sitename').' - '.ResourcesHtml::cleanText(stripslashes($resource->title));
		$doc->title = trim(ResourcesHtml::shortenText(html_entity_decode($dtitle), 250, 0));
		//$doc->description = JText::sprintf('RESOURCES_RSS_DESCRIPTION',$xhub->getCfg('hubShortName'));
		$doc->description = html_entity_decode(ResourcesHtml::cleanText(stripslashes($resource->introtext)));
		$doc->copyright = JText::sprintf('RESOURCES_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('RESOURCES_RSS_CATEGORY');
		$doc->link = JRoute::_('index.php?option='.$this->_option.a.'id='.$resource->id);
		//$doc->image;
		
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
		$tags = trim(ResourcesHtml::shortenText($tags, 250, 0));
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
		
		$doc->itunes_summary = html_entity_decode(ResourcesHtml::cleanText(stripslashes($resource->introtext)));
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
			$dimage->title = trim(ResourcesHtml::shortenText(html_entity_decode($dtitle.' '.JText::_('Artwork')), 250, 0));
			$dimage->link = $base.$doc->link;
			
			$doc->itunes_image = $dimage;
		}
		
		$owner = new XFeedItunesOwner;
		$owner->email = $jconfig->getValue('config.mailfrom');
		$owner->name = $jconfig->getValue('config.sitename');
		
		$doc->itunes_owner = $owner;

		// Build the URL for this resource
		/*$sef = JRoute::_('index.php?option='.$this->_option.a.'id='.$resource->id);
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}*/
		
		//$link = $juri->base().$sef;

		// Start outputing results if any found
		if (count($rows) > 0) {
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to resource
				$link = JRoute::_('index.php?option='.$this->_option.a.'id='.$row->id);
				if (substr($link, 0, 1) != DS) { 
					$link = DS.$link;
				}
				//$link = $base.$link;
				
				// Strip html from feed item description text
				$description = html_entity_decode(ResourcesHtml::cleanText(stripslashes($row->introtext)));
				$author = '';
				@$date = ( $row->publish_up ? date( 'r', strtotime($row->publish_up) ) : '' );
				
				// Instantiate a resource helper
				$rhelper = new ResourcesHelper($row->id, $database);
				
				// Get any podcast/vodcast files
				$podcast = '';
				//$vodcast = '';
				$rhelper->getChildren();
				if ($rhelper->children && count($rhelper->children) > 0) {
					$grandchildren = $rhelper->children;
					foreach ($grandchildren as $grandchild) 
					{
						$ftype = ResourcesHtml::getFileExtension($grandchild->path);
						if (stripslashes($grandchild->introtext) != '') {
							$gdescription = html_entity_decode(ResourcesHtml::cleanText(stripslashes($grandchild->introtext)));
						}
						if ($feedtype == 'video') {
							if ($ftype == 'mp4' || $ftype == 'mov' || $ftype == 'wmv') {
								$podcast = $grandchild->path;
							}
						} else {
							if ($ftype == 'mp3') {
								$podcast = $grandchild->path;
							}
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
				$rtags = trim(ResourcesHtml::shortenText($rtags, 250, 0));
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
					$image->title = $title.' '.JText::_('Artwork');
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
						//$fs = '';
						$enclosure = new XFeedEnclosure; //JObject;
						$enclosure->url = $podcast;
						switch ( ResourcesHtml::getFileExtension($podcast) ) 
						{
							case 'mp4': $enclosure->type = 'video/mp4'; break;
							case 'wmv': $enclosure->type = 'video/wmv'; break;
							case 'mov': $enclosure->type = 'video/quicktime'; break;
							case 'mp3': $enclosure->type = 'audio/mpeg'; break;
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
			echo ResourcesHtml::error( JText::_('RESOURCES_NO_TRIGGER_FOUND') );
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
			JError::raiseError( 500, JText::_('DATABASE_NOT_FOUND') );
			return;
		}
		
		// Incoming
		$id    = JRequest::getInt('id',0);
		$alias = JRequest::getVar('alias','');

		// Load the resource
		$resource = new ResourcesResource( $database );
		if ($alias && !$resource->loadAlias( $alias )) {
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
			return;
		} elseif (!$resource->load( $id )) {
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
			return;
		}

		// Check if the resource is for logged-in users only and the user is logged-in
		$juser =& JFactory::getUser();
		if ($resource->access == 1 && $juser->get('guest')) {
			JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			return;
		}
		
		// Check if the resource is "private" and the user is allowed to view it
		if ($resource->access == 4) {
			if ($this->checkGroupAccess($resource)) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}
		
		// Ensure we have a path
		if (empty($resource->path)) {
			JError::raiseError( 404, JText::_('FILE_NOT_FOUND') );
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $resource->path)) {
			JError::raiseError( 404, JText::_('BAD_FILE_PATH') );
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $resource->path)) {
			JError::raiseError( 404, JText::_('BAD_FILE_PATH') );
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $resource->path)) {
			JError::raiseError( 404, JText::_('BAD_FILE_PATH') );
			return;
		}
		// Disallow \
		if (strpos('\\',$resource->path)) {
			JError::raiseError( 404, JText::_('BAD_FILE_PATH') );
			return;
		}
		// Disallow ..
		if (strpos('..',$resource->path)) {
			JError::raiseError( 404, JText::_('BAD_FILE_PATH') );
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
			JError::raiseError( 404, JText::_('FILE_NOT_FOUND').' '.$filename );
			return;
		}
		
		// Initiate a new content server and serve up the file
		$xserver = new XContentServer();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->serve();

		// Should only get here on error
		JError::raiseError( 404, JText::_('SERVER_ERROR') );
		return;
	}

	//----------------------------------------------------------
	// Tools
	//----------------------------------------------------------

	protected function sourcecode()
	{
		ximport('xserver');
		
		$database =& JFactory::getDBO();
		
		// Get tool instance
		$tool = JRequest::getVar( 'tool', 0 );
		
		// Ensure we have a tool
		if (!$tool) {
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
			return;
		}
		
		// Load the tool version
		$tv = new ToolVersion( $database );
		$tv->loadFromInstance( $tool );
		
		// Concat tarball name for this version
		$tarname = $tv->toolname.'-r'.$tv->revision.'.tar.gz';
		// get contribtool params
		$tparams =& JComponentHelper::getParams( 'com_contribtool' );
		$tarball_path = $tparams->get('sourcecodePath');
		$tarpath = $tarball_path.DS.$tv->toolname.DS;
		$opencode = ($tv->codeaccess=='@OPEN') ? 1 : 0;
					
		// Is a tarball available?
		if (!file_exists( $tarpath . $tarname )) {
			// File not found
			JError::raiseError( 404, JText::_('FILE_NOT_FOUND') );
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
		$xserver->serve_attachment($tarpath . $tarname, $tarname, false); // @TODO fix byte range support
		
		// Should only get here on error
		JError::raiseError( 404, JText::_('SERVER_ERROR') );
		return;
	}
	
	//-----------
	
	protected function license()
	{
		$database =& JFactory::getDBO();
		
		// Get tool instance
		$tool = JRequest::getVar( 'tool', '' );
		$no_html = JRequest::getVar( 'no_html', 0 );
		
		// Ensure we have a tool to work with
		if (!$tool) {
			JError::raiseError( 404, JText::_('RESOURCE_NOT_FOUND') );
			return;
		}
		
		// Load the tool version
		$row = new ToolVersion( $database );
		$row->loadFromInstance( $tool );
		
		// Output HTML
		if ($row) {
			// Set the page title
			$title = stripslashes($row->title).': '.JText::_('LICENSE');
			
			// Write title
			$document =& JFactory::getDocument();
			$document->setTitle( $title );
		} else {
			// Set the page title
			$title = JText::_('RESOURCES_PAGE_UNAVAILABLE');
		}
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Output HTML
		echo ResourcesHtml::toollicense( $this->_option, $app, $row, $title, $no_html );
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
						$doc .= "%0 ".JText::_('GENERIC')."\r\n";
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
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Incoming
		$id   = JRequest::getInt( 'id', 0 );
		$tags = JRequest::getVar( 'tags', '' );
		$no_html = JRequest::getInt( 'no_html', 0 );
		
		// Process tags
		$rt = new ResourcesTags( $database );
		$rt->tag_object($juser->get('id'), $id, $tags, 1, 0);
	
		if (!$no_html) {
			// Push through to the resource view
			$this->view();
		}
	}

	/*protected function minimal()
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$pid = JRequest::getInt( 'id', 0 );
		$rid = JRequest::getInt( 'resid', 0 );
		
		// Load parent resource
		$parent = new ResourcesResource( $database );
		$parent->load( $pid );

		// Load resource
		$resource = new ResourcesResource( $database );
		$resource->load( $rid );
	
		// Record the hit
		$resource->hit( $rid );

		// Get some attributes
		$attribs =& new JParameter( $resource->attribs );
		$width  = $attribs->get( 'width', '' );
		$height = $attribs->get( 'height', '' );

		echo ResourcesHtml::minimal( $parent->title, $resource->path, $width, $height );
	}*/
	
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
		
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return true;
			}
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

			// Check if they're a site admin (from LDAP)
			$xuser =& XFactory::getUser();
			if (is_object($xuser)) {
				$app =& JFactory::getApplication();
				if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
					return false;
				}
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
		$this->allowedgroups = $allowedgroups;
		
		// Find what groups the user has in common with the resource, if any
		$common = array_intersect($usersgroups, $allowedgroups);
		
		// Make sure they have the proper group access
		$restricted = false;
		if ($resource->access == 4) {
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
