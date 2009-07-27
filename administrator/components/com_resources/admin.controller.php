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
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------

	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		switch ( $this->getTask() ) 
		{
			// Media manager
			case 'media':        $this->media();         break;
			case 'listfiles':    $this->listfiles();     break;
			case 'upload':       $this->upload();        break;
			case 'deletefolder': $this->deletefolder();  break;
			case 'deletefile':   $this->deletefile();    break;
			
			// Resource management
			// Creation, editing, saving, deleting
			case 'add':          $this->edit(1);         break;
			case 'edit':         $this->edit(0);         break;
			case 'save':         $this->save();          break;
			case 'remove':       $this->remove();        break;
			case 'getauthor':    $this->getauthor();     break;
			case 'edittags':     $this->edittags();      break;
			case 'savetags':     $this->savetags();      break;
			
			// Resource child management
			case 'addchild':     $this->addchild();      break;
			case 'removechild':  $this->removechild();   break;
			
			// Resource processors
			// these only change one or two fields at a time
			case 'checkin':      $this->checkin();       break;
			case 'cancel':       $this->cancel();        break;
			case 'resethits':    $this->resethits();     break;
			case 'resetrating':  $this->resetrating();   break;
			case 'resetranking': $this->resetranking();  break;
			case 'publish':      $this->publish();       break;
			case 'unpublish':    $this->publish();       break;
			case 'accesspublic':     $this->access();    break;
			case 'accessregistered': $this->access();    break;
			case 'accessspecial':    $this->access();    break;
			case 'accessprotected':  $this->access();    break;
			case 'accessprivate':    $this->access();    break;
			case 'orderup':      $this->reorder();       break;
			case 'orderdown':    $this->reorder();       break;
			case 'regroup':      $this->regroup();       break;
			
			// Resource type management
			case 'canceltype':   $this->viewtypes();     break;
			case 'viewtypes':    $this->viewtypes();     break;
			case 'newtype':      $this->newtype();       break;
			case 'edittype':     $this->edittype();      break;
			case 'savetype':     $this->savetype();      break;
			case 'deletetype':   $this->deletetype();    break;
			
			// Resource views
			case 'orphans':      $this->orphans();       break;
			case 'children':     $this->children();      break;
			case 'browse':       $this->resources();     break;
			case 'ratings':      $this->ratings();       break;
	
			default: $this->resources(); break;
		}
		
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT COUNT(*) FROM #__plugins WHERE `folder`='resources'" );
		$plugins = $database->loadResult();
		if (!$plugins) {
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Reviews', 'reviews', 'resources', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Citations', 'citations', 'resources', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Questions', 'questions', 'resources', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Versions', 'versions', 'resources', 0, 3, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Usage', 'usage', 'resources', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Related', 'related', 'resources', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
			$database->setQuery( "INSERT INTO #__plugins(`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
			VALUES('', 'Resources - Recommendations', 'recommendations', 'resources', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', '')" );
			if (!$database->query()) {
				echo $database->getErrorMsg();
				return false;
			}
		}
		
		/*$database->setQuery( "ALTER TABLE `#__resource_types` ADD `customFields` text default NULL" );
		if (!$database->query()) {
			echo $database->getErrorMsg();
			return false;
		}
		$database->setQuery( "ALTER TABLE `#__author_assoc` ADD `role` varchar(50) default NULL" );
		if (!$database->query()) {
			echo $database->getErrorMsg();
			return false;
		}
		$database->setQuery( "ALTER TABLE `jos_resource_types` ADD `description` tinytext, ADD `contributable` int(2) default '1'" );
		if (!$database->query()) {
			echo $database->getErrorMsg();
			return false;
		}*/
		/*include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$module = new JTableModule( $database );
		$module->title = 'Submissions in progress';
		$module->ordering = 0;
		$module->position = 'mysubmissions';
		$module->published = 1;
		$module->module = 'mod_mysubmissions';
		$module->store();*/
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function resources()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'admin.resources.css');

		// Get configuration
		$config = JFactory::getConfig();
	
		// Incoming
		$filters = array();
		$filters['limit']    = $app->getUserStateFromRequest($this->_option.'.resources.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']    = $app->getUserStateFromRequest($this->_option.'.resources.limitstart', 'limitstart', 0, 'int');
		$filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.resources.search','search', '')));
		$filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.resources.sort', 'filter_order', 'created'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.resources.sortdir', 'filter_order_Dir', 'DESC'));
		$filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.resources.status', 'status', 'all' ));
		$filters['type']     = trim($app->getUserStateFromRequest($this->_option.'.resources.type', 'type', '' ));

		// Get record count
		$sqlcount  = "SELECT count(*) FROM #__resources AS r ";
		$sqlcount .= "WHERE r.standalone=1";
		if ($filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$filters['status'];
		} 
		if ($filters['type']) {
			$sqlcount .= "\n AND r.type=".$filters['type'];
		}
		if ($filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$sqlcount .= "\n OR r.id=".$filters['search'];
			}
			$sqlcount .= ")";
		}
		$database->setQuery( $sqlcount );
		$total = $database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// get resources
		/*$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, 
					r.published, r.publish_up, r.publish_down, r.checked_out_time, u.name AS editor, 
					g.name AS groupname, t.type AS typetitle, 
					(SELECT count(*) FROM #__resource_assoc AS ra WHERE ra.parent_id=r.id) AS children, 
					(SELECT count(*) FROM #__citations_assoc AS ct WHERE ct.oid=r.id AND ct.table='resource') AS citations,
					(SELECT count(*) FROM #__resource_tags AS rt WHERE rt.resourceid=r.id) AS tags";*/
					
		$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, 
					r.published, r.publish_up, r.publish_down, r.checked_out, r.checked_out_time, r.params, u.name AS editor, 
					g.name AS groupname, t.type AS typetitle, 
					(SELECT count(*) FROM #__resource_assoc AS ra WHERE ra.parent_id=r.id) AS children";
		
		$query .= "\n FROM #__resources AS r";
		$query .= "\n LEFT JOIN #__users AS u ON u.id = r.checked_out";
		$query .= "\n LEFT JOIN #__groups AS g ON g.id = r.access";
		$query .= "\n LEFT JOIN #__resource_types AS t ON r.type=t.id";
		$query .= "\n WHERE r.standalone=1";
		if ($filters['status'] != 'all') {
			$query .= " AND r.published=".$filters['status'];
		} 
		if ($filters['type']) {
			$query .= "\n AND r.type=".$filters['type'];
		}
		if ($filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$query .= "\n OR r.id=".$filters['search'];
			}
			$query .= ")";
		}
		$query .= " ORDER BY ".$filters['sort']." ".$filters['sort_Dir']." LIMIT $pageNav->limitstart,$pageNav->limit";
		
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		// Get <select> of types
		$rt = new ResourcesType( $database );
		$arr = $rt->getMajorTypes();
		$types = ResourcesHtml::selectType($arr, 'type', $filters['type'], '[ all types ]', '', '', '');

		// Output HTML
		ResourcesHtml::resources( $database, $rows, $pageNav, $this->_option, $filters, $types );
	}

	//-----------

	protected function children()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/admin.resources.css');
		
		// Resource's parent ID
		$pid = JRequest::getInt( 'pid', 0 );

		// Get configuration
		$config = JFactory::getConfig();
	
		// Incoming
		$filters = array();
		$filters['limit']    = $app->getUserStateFromRequest($this->_option.'.children.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']    = $app->getUserStateFromRequest($this->_option.'.children.limitstart', 'limitstart', 0, 'int');
		$filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.children.search','search', '')));
		$filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.children.sort', 'filter_order', 'ordering'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.children.sortdir', 'filter_order_Dir', 'ASC'));
		$filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.children.status', 'status', 'all' ));

		// Record count
		$sqlcount = "SELECT count(*) FROM #__resources AS r, #__resource_assoc AS ra WHERE ra.child_id=r.id AND ra.parent_id=".$pid;
		if ($filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$filters['status'];
		}
		if ($filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$sqlcount .= "\n OR r.id=".$filters['search'];
			}
			$sqlcount .= ")";
		}
		$database->setQuery( $sqlcount );
		$total = $database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Get only children of this parent
		$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
					r.publish_up, r.publish_down, r.path, r.checked_out, r.checked_out_time, r.standalone, u.name AS editor, g.name AS groupname, 
					lt.type AS logicaltitle, ra.*, gt.type as grouptitle, t.type AS typetitle, NULL as position, 
					(SELECT count(*) FROM #__resource_assoc AS rraa WHERE rraa.child_id=r.id AND rraa.parent_id!=".$pid.") AS multiuse";
		$query .= "\n FROM #__resource_types AS t, #__resources AS r";
		$query .= "\n LEFT JOIN #__users AS u ON u.id = r.checked_out";
		$query .= "\n LEFT JOIN #__groups AS g ON g.id = r.access";
		$query .= "\n LEFT JOIN #__resource_types AS lt ON lt.id=r.logical_type, #__resource_assoc AS ra ";
		$query .= "\n LEFT JOIN #__resource_types AS gt ON gt.id=ra.grouping";
		$query .= "\n WHERE r.type=t.id AND ra.child_id=r.id AND ra.parent_id=".$pid;
		if ($filters['status'] != 'all') {
			$query .= " AND r.published=".$filters['status'];
		} 
		if ($filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$query .= "\n OR r.id=".$filters['search'];
			}
			$query .= ")";
		}
		$query .= " ORDER BY ".$filters['sort']." ".$filters['sort_Dir']." LIMIT $pageNav->limitstart,$pageNav->limit";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		// Get parent info
		$parent = new ResourcesResource( $database );
		$parent->load( $pid );

		// Get sections
		$sections = array();
		if ($parent->type == 4) {
			$rt = new ResourcesType( $database );
			$sections = $rt->getTypes( 29 );
		}

		// Output HTML
		ResourcesHtml::children( $rows, $pageNav, $this->_option, $filters, $sections, $this->_task, $pid, $parent );
	}
	
	//-----------

	protected function orphans()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/admin.resources.css');
		
		$pid = '-1';
		
		// Get configuration
		$config = JFactory::getConfig();
	
		// Incoming
		$filters = array();
		$filters['limit']    = $app->getUserStateFromRequest($this->_option.'.orphans.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']    = $app->getUserStateFromRequest($this->_option.'.orphans.limitstart', 'limitstart', 0, 'int');
		$filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.orphans.search','search', '')));
		$filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.orphans.sort', 'filter_order', 'title'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.orphans.sortdir', 'filter_order_Dir', 'DESC'));
		$filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.orphans.status', 'status', 'all' ));
	
		// Get record count
		$sqlcount  = "SELECT count(*) FROM #__resources AS r ";
		$sqlcount .= "WHERE standalone!=1";
		if ($filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$filters['status'];
		} 
		if ($filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$sqlcount .= "\n OR r.id=".$filters['search'];
			}
			$sqlcount .= ")";
		}
		$sqlcount .= " AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		$database->setQuery( $sqlcount );
		$total = $database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Get records
		$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
					r.publish_up, r.publish_down, r.checked_out, r.checked_out_time, r.path, r.standalone, u.name AS editor, g.name AS groupname, 
					t.type AS typetitle, NULL as logicaltitle";
		$query .= "\n FROM #__resources AS r";
		$query .= "\n LEFT JOIN #__users AS u ON u.id = r.checked_out";
		$query .= "\n LEFT JOIN #__groups AS g ON g.id = r.access";
		$query .= "\n LEFT JOIN #__resource_types AS t ON t.id=r.type";
		$query .= "\n WHERE r.standalone!=1";
		if ($filters['status'] != 'all') {
			$query .= " AND r.published=".$filters['status'];
		} 
		if ($filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".$filters['search']."%'";
			if (is_numeric($filters['search'])) {
				$query .= "\n OR r.id=".$filters['search'];
			}
			$query .= ")";
		}
		$query .= " AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		$query .= " ORDER BY ".$filters['sort']." ".$filters['sort_Dir']." LIMIT $pageNav->limitstart,$pageNav->limit";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	
		// Get sections
		$rt = new ResourcesType( $database );
		$sections = $rt->getTypes( 29 );

		// Output HTML
		ResourcesHtml::children( $rows, $pageNav, $this->_option, $filters, $sections, $this->_task, $pid, '' );
	}

	//-----------

	protected function ratings()
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Do we have an ID to work with?
		if ($id) {
			$rr = new ResourcesReview( $database );
			$rows = $rr->getRatings( $id );

			ResourcesHtml::ratings( $database, $rows, $this->_option, $id );
		}
	}

	//----------------------------------------------------------
	// Children
	//----------------------------------------------------------

	protected function addchild()
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$pid  = JRequest::getInt( 'pid', 0 );
		$id   = JRequest::getVar( 'id', array(0) );
		$step = JRequest::getVar( 'step', 1 );

		if (!empty($id) && !$pid) {
			$pid = $id[0];
			$id = 0;
		}
		
		// Make sure we have a prent ID
		if (!$pid) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('Missing parent resource ID');
			return;
		}
	
		switch ($step)
		{
			case 1:
				// Get the available types
				$rt = new ResourcesType( $database );
				$arr = $rt->getTypes( 30 );
				$types = ResourcesHtml::selectType($arr, 'type', '', '', '', '', '');
				
				// Load the parent resource
				$parent = new ResourcesResource( $database );
				$parent->load( $pid );
				
				// Output HTML
				ResourcesHtml::addChild( $this->_option, $this->_task, $types, $pid, $parent );
			break;
			
			case 2:
				// Get the creation method
				$method = JRequest::getVar( 'method', '' );
			
				if ($method == 'create') {
					// We're starting from scratch
					$this->edit( 1 );
				} elseif ($method == 'existing') {
					// We're just linking up an existing resource
					// Get the child ID we're linking
					$cid = JRequest::getInt( 'childid', 0 );
					if ($cid) {
						// Link 'em up!
						$this->attachchild( $cid, $pid );
					} else {
						// No child ID! Throw an error and present the form from the previous step
						$this->setError( JText::_('Please provide an ID #')) ;
						
						// Get the available types
						$rt = new ResourcesType( $database );
						$arr = $rt->getTypes( 30 );
						$types = ResourcesHtml::selectType($arr, 'type', '', '', '', '', '');
					
						// Load the parent resource
						$parent = new ResourcesResource( $database );
						$parent->load( $pid );
						
						// Output HTML
						ResourcesHtml::addChild( $this->_option, $this->_task, $types, $pid, $parent, $this->getError() );
					}
				}
			break;
		}
	}

	//-----------

	protected function attachchild( $id, $pid )
	{
		// Make sure we have both parent and child IDs
		if (!$pid) {
			echo ResourcesHtml::alert( JText::_('Error: Missing parent ID') );
			exit();
		}
		
		if (!$id) {
			echo ResourcesHtml::alert( JText::_('Error: Missing child ID') );
			exit();
		}
		
		$database =& JFactory::getDBO();
		
		// Instantiate a ResourcesAssoc object
		$assoc = new ResourcesAssoc( $database );
			
		// Get the last child in the ordering
		$order = $assoc->getLastOrder( $pid );
		$order = ($order) ? $order : 0;
			
		// Increase the ordering - new items are always last
		$order = $order + 1;

		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id = $id;
		$assoc->ordering = $order;
		$assoc->grouping = 0;
		if (!$assoc->check()) {
			die( $assoc->getError() );
		}
		if (!$assoc->store(true)) {
			die( $assoc->getError() );
		}

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
		$this->_message = JText::_('Child successfully added');
	}

	//-----------

	protected function removechild()
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		$pid = JRequest::getInt( 'pid', 0 );
		
		// Make sure we have a parent ID
		if (!$pid) {
			echo ResourcesHtml::alert( JText::_('Error: Missing parent ID') );
			exit();
		}
		
		// Make sure we have children IDs
		if (!$ids || count($ids) < 1) {
			echo ResourcesHtml::alert( JText::_('Error: Missing child ID') );
			exit();
		}
		
		$database =& JFactory::getDBO();
		
		$assoc = new ResourcesAssoc( $database );
		
		// Multiple IDs - loop through and delete them
		foreach ($ids as $id)
		{
			$assoc->delete( $pid, $id );
		}
		
		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
		$this->_message = JText::sprintf('%s children successfully removed', count($ids));
	}

	//----------------------------------------------------------
	// Resource Functions
	//----------------------------------------------------------

	protected function edit( $isnew=0 ) 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Get the resource component config
		$rconfig = $this->config;

		// Push some needed styles to the tmeplate
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/admin.resources.css');
		
		// Incoming resource ID
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array( $id )) {
			$id = $id[0];
		}
		
		// Incoming parent ID - this determines if the resource is standalone or not
		$pid = JRequest::getInt( 'pid', 0 );

		// Grab some filters for returning to place after editing
		$return = array();
		$return['type']   = JRequest::getVar( 'type', '' );
		$return['sort']   = JRequest::getVar( 'sort', '' );
		$return['status'] = JRequest::getVar( 'status', '' );

		// Instantiate our resource object
		$row = new ResourcesResource( $database );
		$row->load( $id );

		// Fail if checked out not by 'me'
		if ($row->checked_out && $row->checked_out <> $juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_( 'This resource is currently being edited by another administrator' );
			return;
		}
		
		// Is this a new resource?
		if (!$id) {
			$row->created      = date( 'Y-m-d H:i:s', time() );
			$row->created_by   = $juser->get('id');
			$row->modified     = '0000-00-00 00:00:00';
			$row->modified_by  = 0;
			$row->publish_up   = date( 'Y-m-d H:i:s', time() );
			$row->publish_down = 'Never';
			if ($pid) {
				$row->published  = 1;
				$row->standalone = 0;
			} else {
				$row->published  = 3; // default to "new" status
				$row->standalone = 1;
			}
		}

		// Editing existing
		$row->checkout( $juser->get('id') );

		if (trim( $row->publish_down ) == '0000-00-00 00:00:00') {
			$row->publish_down = JText::_('Never');
		}

		// Get name of resource creator
		$query = "SELECT name from #__users WHERE id=".$row->created_by;
		$database->setQuery( $query );
		$row->created_by_name = $database->loadResult();
		$row->created_by_name = ($row->created_by_name) ? $row->created_by_name : JText::_('Unknown');

		// Get name of last person to modify resource
		if ($row->modified_by) {
			$query = "SELECT name from #__users WHERE id=".$row->modified_by;
			$database->setQuery( $query );
			$row->modified_by_name = $database->loadResult();
			$row->modified_by_name = ($row->modified_by_name) ? $row->modified_by_name : JText::_('Unknown');
		} else {
			$row->modified_by_name = '';
		}
		
		// Get params definitions
		$params  =& new JParameter( $row->params, JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'resources.xml' );
		$attribs =& new JParameter( $row->attribs );

		// Build selects of various types
		$rt = new ResourcesType( $database );
		if ($row->standalone != 1) {
			$lists['type'] = ResourcesHtml::selectType( $rt->getTypes( 30 ), 'type', $row->type, '', '', '', '');
			$lists['logical_type'] = ResourcesHtml::selectType( $rt->getTypes( 28 ), 'logical_type', $row->logical_type, '[ none ]', '', '', '');
			$lists['sub_type'] = ResourcesHtml::selectType( $rt->getTypes( 30 ), 'logical_type', $row->logical_type, '[ none ]', '', '', '');
		} else {
			$lists['type'] = ResourcesHtml::selectType( $rt->getTypes( 27 ), 'type', $row->type, '', '', '', '');
			$lists['logical_type'] = ResourcesHtml::selectType( $rt->getTypes( 21 ), 'logical_type', $row->logical_type, '[ none ]', '', '', '');
		}
	
		// Build the <select> of admin users
		$lists['created_by'] = $this->userSelect( 'created_by', 0, 1 );
	
		// Build the <select> for the group access
		$lists['access'] = ResourcesHtml::selectAccess($rconfig->get('accesses'), $row->access);
	
		// Is this a standalone resource?
		if ($row->standalone == 1) {
			// Get groups
			ximport('xgroup');
			$filters = array();
			$filters['fields'] = array('description','published','gidNumber','type');
			$groups = XGroupHelper::get_groups('hub', false, $filters);
			
			// Build <select> of groups
			$lists['groups'] = ResourcesHtml::selectGroup($groups, $row->group_owner);

			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );

			// Get all contributors
			$mp = new MembersProfile( $database );
			$members = null; //$mp->getRecords( array('sortby'=>'surname DESC','limit'=>'all','search'=>'','show'=>''), true );

			// Get all contributors linked to this resource
			$ma = new MembersAssociation( $database );
			$sql = "SELECT n.uidNumber AS id, n.givenName, n.middleName, n.surname, a.role  
					FROM $mp->_tbl AS n, $ma->_tbl AS a  
					WHERE a.subtable='resources'
					AND a.subid=$row->id 
					AND n.uidNumber=a.authorid
					ORDER BY a.ordering";
			$database->setQuery( $sql );
			$authnames = $database->loadObjectList();
			
			// Build <select> of contributors
			$lists['authors'] = ResourcesHtml::selectAuthors($members, $authnames, $attribs, $this->_option);
			
			// Get the tags on this item
			$rt = new ResourcesTags( $database );
			$lists['tags'] = $rt->get_tag_string($row->id, 0, 0, NULL, 0, 1);
		}

		// Output HTML
		ResourcesHtml::editResource( $rconfig, $row, $lists, $params, $attribs, $juser->get('id'), $this->_option, $pid, $isnew, $return );
	}

	//-----------

	protected function save() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Initiate extended database class
		$row = new ResourcesResource( $database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		
		$isNew = 0;
		if ($row->id < 1) {
			$isNew = 1;
		}

		if ($isNew) {
			// New entry
			$row->created    = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			$row->created_by = $row->created_by ? $row->created_by : $juser->get('id');
		} else {
			$old = new ResourcesResource( $database );
			$old->load( $row->id );
			
			$created_by_id = JRequest::getInt( 'created_by_id', 0 );
		
			// Updating entry
			$row->modified    = date( "Y-m-d H:i:s" );
			$row->modified_by = $juser->get('id');
			//$row->created     = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			if ($created_by_id) {
				$row->created_by = $row->created_by ? $row->created_by : $created_by_id;
			} else {
				$row->created_by = $row->created_by ? $row->created_by : $juser->get('id');
			}
		}
		if (trim( $row->publish_down ) == 'Never') {
			$row->publish_down = '0000-00-00 00:00:00';
		}
		
		// Get parameters
		$params = JRequest::getVar( 'params', '', 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}

		// Get attributes
		$attribs = JRequest::getVar( 'attrib', '', 'post' );
		if (is_array( $attribs )) {
			$txta = array();
			foreach ( $attribs as $k=>$v) 
			{
				$txta[] = "$k=$v";
			}
			$row->attribs = implode( "\n", $txta );
		}

		// Get custom areas, add wrappers, and compile into fulltext
		if (isset($_POST['nbtag'])) {
			$type = new ResourcesType( $database );
			$type->load( $row->type );
			
			$fields = array();
			if (trim($type->customFields) != '') {
				$fs = explode("\n", trim($type->customFields));
				foreach ($fs as $f) 
				{
					$fields[] = explode('=', $f);
				}
			} else {
				if ($row->type == 7) {
					$flds = $this->config->get('tagstool');
				} else {
					$flds = $this->config->get('tagsothr');
				}
				$flds = explode(',',$flds);
				foreach ($flds as $fld) 
				{
					$fields[] = array($fld, $fld, 'textarea', 0);
				}
			}
			
			$nbtag = $_POST['nbtag'];
			$nbtag = array_map('trim',$nbtag);
			foreach ($nbtag as $tagname=>$tagcontent)
			{
				if ($tagcontent != '') {
					$row->fulltext .= n.'<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>'.n;
				} else {
					foreach ($fields as $f) 
					{
						if ($f[0] == $tagname && end($f) == 1) {
							echo ResourcesHtml::alert( JText::sprintf('RESOURCES_REQUIRED_FIELD_CHECK', $f[1]) );
							exit();
						}
					}
				}
			}
		}

		// Code cleaner for xhtml transitional compliance
		$row->introtext = str_replace( '<br>', '<br />', $row->introtext );
		$row->fulltext  = str_replace( '<br>', '<br />', $row->fulltext );

		// Check content
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}

		// Store content
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}

		// Checkin resource
		$row->checkin();
	
		// Rename the temporary upload directory if it exist
		$tmpid = JRequest::getInt( 'tmpid', 0, 'post' );
		if ($tmpid != ResourcesHtml::niceidformat($row->id)) {
			jimport('joomla.filesystem.folder');
			
			// Build the full paths
			$path = ResourcesHtml::dateToPath( $row->created );
			$dir_id = ResourcesHtml::niceidformat( $row->id );
			
			$tmppath = $this->buildUploadPath($path.DS.$tmpid);
			$newpath = $this->buildUploadPath($path.DS.$dir_id);
			
			// Attempt to rename the temp directory
			$result = JFolder::move($tmppath, $newpath);
			if ($result !== true) {
				$this->setError( $result );
			}
			
			$row->path = str_replace($tmpid,ResourcesHtml::niceidformat($row->id),$row->path);
			$row->store();
		}
	
		// Incoming tags
		$tags = JRequest::getVar( 'tags', '', 'post' );
		
		// Save the tags
		$rt = new ResourcesTags($database);
		$rt->tag_object($juser->get('id'), $row->id, $tags, 1, 1);
	
		// Incoming authors
		$authorsOldstr = JRequest::getVar( 'old_authors', '', 'post' );
		$authorsNewstr = JRequest::getVar( 'new_authors', '', 'post' );

		if ($authorsNewstr != $authorsOldstr) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'resources.contributor.php');
			
			$authorsNew = split(',',$authorsNewstr);
			$authorsOld = split(',',$authorsOldstr);

			// We have either a new ordering or new authors or both
			if ($authorsNewstr) {
				for ($i=0, $n=count( $authorsNew ); $i < $n; $i++)
				{
					$rc = new ResourcesContributor( $database );
					$rc->subtable = 'resources';
					$rc->subid = $row->id;
					$rc->authorid = $authorsNew[$i];
					$rc->ordering = $i;
					$rc->role = trim(JRequest::getVar( $authorsNew[$i].'_role', '' ));
					if (in_array($authorsNew[$i], $authorsOld)) {
						// Updating record
						$rc->updateAssociation();
					} else {
						// New record
						$rc->createAssociation();
					}
				}
			}
			// Run through previous author list and check to see if any IDs had been dropped
			if ($authorsOldstr) {
				$rc = new ResourcesContributor( $database );
				
				for ($i=0, $n=count( $authorsOld ); $i < $n; $i++)
				{
					if (!in_array($authorsOld[$i], $authorsNew)) {
						$rc->deleteAssociation( $authorsOld[$i], $row->id, 'resources' );
					}
				}
			}
		}

		// If this is a child, add parent/child association
		$pid = JRequest::getInt( 'pid', 0, 'post' );
		if ($isNew && $pid) {
			$this->attachchild( $row->id, $pid );
		}

		// Is this a standalone resource and we need to email approved submissions?
		if ($row->standalone == 1 && $this->config->get('email_when_approved')) {
			// If the state went from pending to published
			if ($row->published == 1 && $old->published == 3) {
				$this->email_contributors($row, $database);
			}
		}

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
		$this->_message = JText::_('Item successfully saved');
	}
	
	//-----------
	
	private function email_contributors($row, $database) 
	{
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'resources.extended.php' );
		$helper = new ResourcesHelper( $row->id, $database );
		//$helper->getCons();
		//$contributors = $helper->_contributors;
		$helper->getContributorIDs();
		$contributors = $helper->contributorIDs;
		
		if ($contributors && count($contributors) > 0) {
			// Email all the contributors
			$xhub =& XFactory::getHub();

			// E-mail "from" info
			$from = array();
			$from['email'] = $xhub->getCfg('hubSupportEmail');
			$from['name']  = $xhub->getCfg('hubShortURL').' '.JText::_('SUBMISSIONS');

			// E-mail subject
			$subject = JText::_('EMAIL_SUBJECT');

			$juri =& JURI::getInstance();

			$sef = JRoute::_('index.php?option='.$this->_option.a.'id='. $row->id);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}
			
			// E-mail message
			$message  = JText::sprintf('EMAIL_MESSAGE', $xhub->getCfg('hubShortURL'))."\r\n";
			$message .= $xhub->getCfg('hubLongURL').DS.'resources'.DS.$row->id;

			// Send e-mail
			/*foreach ($contributors as $contributor)
			{
				$xuser = JUser::getInstance( $contributor->id );
				if (is_object($xuser)) {
					if ($xuser->get('email')) {
						$this->send_email($from, $xuser->get('email'), $subject, $message);
					}
				}
			}*/
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'resources_submission_approved', $subject, $message, $from, $contributors, $this->_option ))) {
				$this->setError( JText::_('Failed to message users.') );
			}
		}
	}
	
	//-----------

	private function send_email($from, $email, $subject, $message) 
	{
		if ($from) {
			$contact_email = $from['email'];
			$contact_name  = $from['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: ' . $hub['name'] . "\n";
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
	}

	//-----------

	protected function remove() 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		
		// Ensure we have some IDs to work with
		if (count($ids) < 1) {
			echo ResourcesHtml::alert( JText::_('Select a resource to delete') );
			exit;
		}
		
		jimport('joomla.filesystem.folder');

		foreach ($ids as $id) 
		{
			// Load resource info
			$row = new ResourcesResource( $database );
			$row->load( $id );
			
			// Get path and delete directories
			if ($row->path != '') {
				$listdir = $row->path;
			} else {
				// No stored path, derive from created date		
				$listdir = ResourcesHtml::build_path( $row->created, $id, '' );
			}
			
			// Build the path
			$path = $this->buildUploadPath( $listdir, '' );

			// Check if the folder even exists
			if (!is_dir($path) or !$path) { 
				$this->setError( JText::_('DIRECTORY_NOT_FOUND') ); 
			} else {
				// Attempt to delete the folder
				if (!JFolder::delete($path)) {
					$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
				}
			}
			
			// Delete associations to the resource
			$row->deleteExistence();
			
			// Delete the resource
			$row->delete();
		}
	
		$pid = JRequest::getInt( 'pid', 0 );
	
		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	protected function regroup()
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$ids = JRequest::getVar( 'id', array() );
		$pid = JRequest::getInt( 'pid', 0 );
		
		if (is_array($ids)) {
			$id = $ids[0];
		} else {
			$id = 0;
		}
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ResourcesHtml::alert( JText::_('No resource ID found.') );
			exit;
		}
		
		// Ensure we have an ID to work with
		if (!$pid) {
			echo ResourcesHtml::alert( JText::_('No parent resource ID found.') );
			exit;
		}
		
		// Load the Association, set its new grouping, save
		$assoc = new ResourcesAssoc( $database );
		$assoc->loadAssoc( $pid, $id );
		$assoc->grouping = JRequest::getInt( 'grouping'.$id, 0, 'post' );
		$assoc->store();

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	//-----------

	protected function access() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ResourcesHtml::alert( JText::_('No Resource ID found.') );
			exit;
		}
		
		// Choose access level
		switch ( $this->_task ) 
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
			case 'accessprotected':  $access = 3; break;
			case 'accessprivate':    $access = 4; break;
			default: $access = 0; break;
		} 

		// Load resource info
		$row = new ResourcesResource( $database );
		$row->load( $id );
		$row->access = $access;

		// Check and store changes
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit;
		}
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit;
		}
		
		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	//-----------

	protected function publish() 
	{
		$publish = ($this->_task == 'publish') ? 1 : 0;
		
		// Incoming
		$pid = JRequest::getInt( 'pid', 0 );
		$ids = JRequest::getVar( 'id', array() );

		// Check for a resource
		if (count( $ids ) < 1) {
			echo ResourcesHtml::alert( JText::sprintf('Select a resource to %s',$this->_task) );
			exit();
		}
		
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Loop through all the IDs
		foreach ($ids as $id) 
		{
			// Load the resource
			$resource = new ResourcesResource( $database );
			$resource->load( $id );
			
			// Only allow changes if the resource isn't checked out or
			// is checked out by the user requesting changes
			if (!$resource->checked_out || $resource->checked_out == $juser->get('id')) {
				$old = $resource->published;
				
				$resource->published = $publish;
				
				// If we're publishing, set the UP date
				if ($publish) {
					$resource->publish_up = date( "Y-m-d H:i:s" );
				}
				
				// Is this a standalone resource and we need to email approved submissions?
				if ($resource->standalone == 1 && $this->config->get('email_when_approved')) {
					// If the state went from pending to published
					if ($resource->published == 1 && $old == 3) {
						$this->email_contributors($resource, $database);
					}
				}
				
				// Store and checkin the resource
				$resource->store();
				$resource->checkin();
			}
		}
		
		if ($publish == '-1') {
			$this->_message = JText::sprintf('%s Item(s) successfully Archived', count($ids));
		} elseif ($publish == '1') {
			$this->_message = JText::sprintf('%s Item(s) successfully Published', count($ids));
		} elseif ($publish == '0') {
			$this->_message = JText::sprintf('%s Item(s) successfully Unpublished', count($ids));
		}
		
		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	//-----------

	protected function cancel()
	{
		$database =& JFactory::getDBO();

		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );

		// Checkin the resource
		$row = new ResourcesResource($database);
		$row->bind( $_POST );
		$row->checkin();
		
		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	//-----------

	protected function resethits() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			$database =& JFactory::getDBO();
			
			// Load the object, reset the hits, save, checkin
			$row = new ResourcesResource($database);
			$row->load($id);
			$row->hits = '0';
			$row->store();
			$row->checkin();
		}

		// Redirect
		//$this->_redirect = $this->buildRedirectURL();
		$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id[]='.$id;
		$this->_message = JText::_('Successfully reset Hit count');
	}

	//-----------

	protected function resetrating() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			$database =& JFactory::getDBO();
			
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($database);
			$row->load($id);
			$row->rating = '0.0';
			$row->times_rated = '0';
			$row->store();
			$row->checkin();
		}
		
		// Redirect
		//$this->_redirect = $this->buildRedirectURL();
		$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id[]='.$id;
		$this->_message = JText::_('Successfully reset Rating count');
	}

	//-----------

	protected function resetranking() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			$database =& JFactory::getDBO();
			
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($database);
			$row->load($id);
			$row->ranking = '0';
			$row->store();
			$row->checkin();
		}
		
		// Redirect
		//$this->_redirect = $this->buildRedirectURL();
		$this->_redirect = 'index.php?option='.$this->_option.'&task=edit&id[]='.$id;
		$this->_message = JText::_('Successfully reset Ranking');
	}

	//-----------

	protected function checkin()
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );

		// Make sure we have at least one ID 
		if (count( $ids ) < 1) {
			echo ResourcesHtml::alert( JText::_('Select a resource to check in') );
			exit;
		}

		$database =& JFactory::getDBO();

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the resource and check it in
			$row = new ResourcesResource( $database );
			$row->load( $id );
			$row->checkin();
		}
	
		// Redirect
		$this->_redirect = $this->buildRedirectURL();
	}

	//-----------

	protected function reorder() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getVar( 'id', array() );
		$id = $id[0];
		$pid = JRequest::getInt( 'pid', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			echo ResourcesHtml::alert( JText::_('No resource ID found.') );
			exit;
		}
		
		// Ensure we have a parent ID to work with
		if (!$pid) {
			echo ResourcesHtml::alert( JText::_('No parent resource ID found.') );
			exit;
		}

		// Get the element moving down - item 1
		$resource1 = new ResourcesAssoc( $database );
		$resource1->loadAssoc( $pid, $id );

		// Get the element directly after it in ordering - item 2
		$resource2 = clone( $resource1 );
		$resource2->getNeighbor( $this->_task );

		switch ($this->_task) 
		{
			case 'orderup':				
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource2->ordering;
				$orderdn = $resource1->ordering;
				
				$resource1->ordering = $orderup;
				$resource2->ordering = $orderdn;
				break;
			
			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource1->ordering;
				$orderdn = $resource2->ordering;
				
				$resource1->ordering = $orderdn;
				$resource2->ordering = $orderup;
				break;
		}
		
		// Save changes
		$resource1->store();
		$resource2->store();
		
		// Redirect
		$this->_redirect = 'index.php?option='. $this->_option .'&task=children&pid='. $pid;
	}

	//----------------------------------------------------------
	// Types
	//----------------------------------------------------------

	protected function viewtypes()
	{
		$database =& JFactory::getDBO();
		$app =& JFactory::getApplication();
		
		// Get configuration
		$config = JFactory::getConfig();

		// Incoming
		$filters = array();
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.types.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.types.limitstart', 'limitstart', 0, 'int');
		$filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.types.sort', 'filter_order', 'category'));
		$filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.types.sortdir', 'filter_order_Dir', 'DESC'));
		$filters['category'] = $app->getUserStateFromRequest($this->_option.'.types.category', 'category', 0, 'int');
		
		// Instantiate an object
		$rt = new ResourcesType( $database );
		
		// Get a record count
		$total = $rt->getAllCount( $filters );

		// Get records
		$rows = $rt->getAllTypes( $filters );

		// initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Get the category names
		$cats = $rt->getTypes('0');
	
		// Output HTML
		ResourcesHtml::types( $rows, $cats, $pageNav, $this->_option, $filters );
	}

	//-----------
	
	protected function newtype() 
	{
		$this->edittype();
	}
	
	//-----------

	protected function edittype()
	{
		$database =& JFactory::getDBO();
	
		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		// Load the object
		$row = new ResourcesType( $database );
		$row->load( $id );

		// Get the categories
		$arr = $row->getTypes( 0 );
		$categories = ResourcesHtml::selectType($arr, 'category', $row->category, '[ select ]', '', '', '');
	
		// Output HTML
		ResourcesHtml::editType( $row, $this->_option, $categories );
	}

	//-----------

	protected function savetype()
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		//$_POST = array_map('trim',$_POST);

		// Initiate extended database class
		$row = new ResourcesType( $database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		$row->contributable = ($row->contributable) ? $row->contributable : '0';
	
		// Get the custom fields
		$fields = JRequest::getVar('fields', array(), 'post');
		if (is_array($fields)) {
			$txta = array();
			foreach ($fields as $val)
			{
				if ($val['title']) {
					$k = $this->normalize(trim($val['title']));
					$t = str_replace('=','-',$val['title']);
					$j = (isset($val['type'])) ? $val['type'] : 'text';
					$x = (isset($val['required'])) ? $val['required'] : '0';
					$txta[] = $k.'='.$t.'='.$j.'='.$x;
				}
			}
			$field = implode( "\n", $txta );
			$row->customFields = $field;
		}
	
		// Check content
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=viewtypes';
		$this->_message = JText::_('Type successfully saved');
	}

	//-----------
	
	private function normalize($txt) 
	{
		// Strip any non-alphanumeric characters
		$normalized_valid_chars = 'a-zA-Z0-9';
		$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $txt);
		return strtolower($normalized);
	}

	//-----------

	protected function deletetype()
	{
		$database =& JFactory::getDBO();
		
		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No type selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=viewtypes';
			return;
		}
		
		$rt = new ResourcesType( $database );
		
		foreach ($ids as $id) 
		{
			// Check if the type is being used
			$total = $rt->checkUsage( $id );

			if ($total > 0) {
				echo ResourcesHtml::alert( JText::sprintf('There are resources with type %s. Please reassign them before deleting this type.', $id) );
				exit();
			}
			
			// Delete the type
			$rt->delete( $id );
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=viewtypes';
		$this->_message = JText::_('Type(s) successfully removed');
	}

	//----------------------------------------------------------
	// Media manager
	//----------------------------------------------------------

	protected function upload()
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar( 'listdir', '', 'post' );
		if (!$listdir) {
			$this->setError( JText::_('RESOURCES_NO_LISTDIR') );
			$this->media();
			return;
		}
		
		// Incoming sub-directory
		$subdir = JRequest::getVar( 'dirPath', '', 'post' );

		// Build the path
		$path = $this->buildUploadPath( $listdir, $subdir );
		
		// Are we creating a new folder?
		$foldername = JRequest::getVar( 'foldername', '', 'post' );
		if ($foldername != '') {
			// Make sure the name is valid
			if (eregi("[^0-9a-zA-Z_]", $foldername)) {
				$this->setError( JText::_('Directory name must only contain alphanumeric characters and no spaces please.') );
			} else {
				if (!is_dir( $path.DS.$foldername )) {
					//FileUploadUtils::make_path( $file_path.'/'.$foldername );
					jimport('joomla.filesystem.folder');
					if (!JFolder::create( $path.DS.$foldername, 0777 )) {
						$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
					}
				} else {
					$this->setError( JText::_('Directory already exists') );
				}
			}
			// Directory created
		} else {
			// Make sure the upload path exist
			if (!is_dir( $path )) {
				jimport('joomla.filesystem.folder');
				if (!JFolder::create( $path, 0777 )) {
					$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
					$this->media();
					return;
				}
			}
			
			// Incoming file
			$file = JRequest::getVar( 'upload', '', 'files', 'array' );
			if (!$file['name']) {
				$this->setError( JText::_('RESOURCES_NO_FILE') );
				$this->media();
				return;
			}
			
			// Make the filename safe
			jimport('joomla.filesystem.file');
			$file['name'] = JFile::makeSafe($file['name']);
			$file['name'] = str_replace(' ','_',$file['name']);

			// Perform the upload
			if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
				$this->setError( JText::_('ERROR_UPLOADING') );
			} else {
				// File was uploaded
				
				// Was the file an archive that needs unzipping?
				$batch = JRequest::getInt( 'batch', 0, 'post' );
				if ($batch) {
					//$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $file['name']);
					
					/*jimport('joomla.filesystem.archive');

					// Extract the files
					$ret = JArchive::extract( $file['name'], $path );
					if (!$ret) {
						$this->setError( JText::_('Could not extract package.') );
					}*/
					require_once( JPATH_ROOT.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php' );

					if (!extension_loaded('zlib')) {
						$this->setError( JText::_('ZLIB_PACKAGE_REQUIRED') );
					} else {
						if (substr($path, -1, 1) == DS) {
							$path = substr($path, 0, -1);
						}
						$zip = new PclZip( $path.DS.$file['name'] );

						// unzip the file
						$do = $zip->extract($path);
						if (!$do) {
							$this->setError( JText::_( 'UNABLE_TO_EXTRACT_PACKAGE' ) );
						} else {
							@unlink( $path.DS.$file['name'] );
						}
					}
				} // if ($batch) {
			}
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function deletefolder() 
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar( 'listdir', '' );
		if (!$listdir) {
			$this->setError( JText::_('RESOURCES_NO_LISTDIR') );
			$this->media();
		}
		
		// Incoming sub-directory
		$subdir = JRequest::getVar( 'subdir', '' );

		// Build the path
		$path = $this->buildUploadPath( $listdir, $subdir );
		
		// Incoming directory to delete
		$folder = JRequest::getVar( 'delFolder', '' );
		if (!$folder) {
			$this->setError( JText::_('RESOURCES_NO_DIRECTORY') );
			$this->media();
		}
		
		if (substr($folder,0,1) != DS) {
			$folder = DS.$folder;
		}
		
		// Check if the folder even exists
		if (!is_dir($path.$folder) or !$folder) { 
			$this->setError( JText::_('DIRECTORY_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path.$folder)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
			}
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function deletefile() 
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar( 'listdir', '' );
		if (!$listdir) {
			$this->setError( JText::_('RESOURCES_NO_LISTDIR') );
			$this->media();
		}
		
		// Incoming sub-directory
		$subdir = JRequest::getVar( 'subdir', '' );

		// Build the path
		$path = $this->buildUploadPath( $listdir, $subdir );
		
		// Incoming file to delete
		$file = JRequest::getVar( 'delFile', '' );
		if (!$file) {
			$this->setError( JText::_('RESOURCES_NO_FILE') );
			$this->media();
		}
		
		// Check if the file even exists
		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
			}
		}
		
		// Push through to the media view
		$this->media();
	}

	//-----------

	protected function media() 
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar( 'listdir', '' );
		if (!$listdir) {
			echo ResourcesHtml::error( JText::_('No list directory provided.') );
			return;
		}
		
		// Incoming sub-directory
		$subdir  = JRequest::getVar( 'subdir', '' );
		if (!$subdir) {
			$subdir = JRequest::getVar( 'dirPath', '', 'post' );
		}

		// Build the path
		$path = $this->buildUploadPath( $listdir, $subdir );
	
		// Get list of directories
		$dirs = $this->recursive_listdir( $path );
		
		$folders   = array();
		$folders[] = JHTML::_('select.option', '/');
		if ($dirs) {
			foreach ($dirs as $dir) 
			{
				$folders[] = JHTML::_('select.option', substr($dir,strlen($path)));
			}
		}
		if (is_array($folders)) {
			sort( $folders );
		}

		// Create folder <select> list
		$dirPath = JHTML::_('select.genericlist', $folders, 'dirPath', 'onchange="goUpDir()" ','value', 'text', $subdir );

		// Output HTML
		ResourcesHtml::media($dirPath, $listdir, $subdir, $path, $this->getError());
	}

	//-----------
 
	protected function listfiles() 
	{
		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$listdir = JRequest::getVar( 'listdir', '' );
		if (!$listdir) {
			echo ResourcesHtml::error( JText::_('No list directory provided.') );
			return;
		}
		
		// Incoming sub-directory
		$subdir = JRequest::getVar( 'subdir', '' );

		// Build the path
		$path = $this->buildUploadPath( $listdir, $subdir );

		$d = @dir($path);

		if ($d) {
			$images  = array();
			$folders = array();
			$docs    = array();
	
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 

				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			ResourcesHtml::imageStyle( $listdir );	

			if (count($images) > 0 || count($folders) > 0 || count($docs) > 0) {	
				ksort($images);
				ksort($folders);
				ksort($docs);

				ResourcesHtml::draw_table_header();

				for ($i=0; $i<count($folders); $i++) 
				{
					$folder_name = key($folders);		
					ResourcesHtml::show_dir( $this->_option, JPATH_ROOT, DS.$folders[$folder_name], $folder_name, $listdir, $subdir );
					next($folders);
				}
				for ($i=0; $i<count($docs); $i++) 
				{
					$doc_name = key($docs);	
					$icon = '';
					/*$icon = 'components/'.$this->_option.'/images/'.substr($doc_name,-3).'.png';
					if (!file_exists($icon))	{
						$icon = 'components/'.$this->_option.'/images/unknown.png';
					}*/
					ResourcesHtml::show_doc( $this->_option, $docs[$doc_name], $listdir, $icon, $subdir );
					next($docs);
				}
				for ($i=0; $i<count($images); $i++) 
				{
					$image_name = key($images);
					$icon = '';
					/*$icon = 'components/'.$this->_option.'/images/'.substr($image_name,-3).'.png';
					if (!file_exists($icon))	{
						$icon = 'components/'.$this->_option.'/images/unknown.png';
					}*/
					ResourcesHtml::show_doc( $this->_option, $images[$image_name], $listdir, $icon, $subdir );
					next($images);
				}
				ResourcesHtml::draw_table_footer();
			} else {
				ResourcesHtml::draw_no_results();
			}
		} else {
			ResourcesHtml::draw_no_results();
		}
	}
	
	//-----------
	
	private function buildUploadPath( $listdir, $subdir='' ) 
	{
		if ($subdir) {
			// Make sure the path doesn't end with a slash
			if (substr($subdir, -1) == DS) { 
				$subdir = substr($subdir, 0, strlen($subdir) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($subdir, 0, 1) != DS) { 
				$subdir = DS.$subdir;
			}
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
		
		// Make sure the path doesn't end with a slash
		if (substr($listdir, -1) == DS) { 
			$listdir = substr($listdir, 0, strlen($listdir) - 1);
		}
		// Ensure the path starts with a slash
		if (substr($listdir, 0, 1) != DS) { 
			$listdir = DS.$listdir;
		}
		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base_path)) == $base_path) {
			// Yes - ... this really shouldn't happen
		} else {
			// No - append it
			$listdir = $base_path.$listdir;
		}

		// Build the path
		return JPATH_ROOT.$listdir.$subdir;
	}

	//-----------

	private function recursive_listdir($base) 
	{ 
	    static $filelist = array(); 
	    static $dirlist  = array(); 

	    if (is_dir($base)) { 
	       $dh = opendir($base); 
	       while (false !== ($dir = readdir($dh))) 
		   { 
	           if (is_dir($base .DS. $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') { 
	                $subbase    = $base .DS. $dir; 
	                $dirlist[]  = $subbase; 
	                $subdirlist = $this->recursive_listdir($subbase); 
	            } 
	        } 
	        closedir($dh); 
	    } 
	    return $dirlist; 
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function buildRedirectURL( $pid=0 )
	{
		// Get configuration
		$config = JFactory::getConfig();
	
		// Paging variables
		$limit  = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$start  = JRequest::getInt('limitstart', 0);
		
		// Filters
		$vtask  = JRequest::getVar( 'viewtask', '' );
		$search = JRequest::getVar( 'search', '' );
		$filter = JRequest::getVar( 'filter', array(0) );
		if (!empty($filter)) {
			$filter = array_map('trim',$filter);
			$sort   = (isset($filter['sort'])) ? $filter['sort'] : '';
			$status = (isset($filter['status'])) ? $filter['status'] : '';
			$type   = (isset($filter['type'])) ? $filter['type'] : '';
			$sort_Dir = (isset($filter['sort_Dir'])) ? $filter['sort_Dir'] : '';
		} else {
			//$sort   = JRequest::getVar( 'sort', '' );
			$sort  = JRequest::getVar( 'filter_order', 'id' );
			$sort_Dir = JRequest::getVar( 'filter_order_Dir', 'desc' );
			$status = JRequest::getVar( 'status', '' );
			$type   = JRequest::getVar( 'type', 0 );
		}
		
		if ($status !== 0) {
			$status = 'all';
		}
		
		$url  = 'index.php?option='.$this->_option;
		if ($pid == '-1') {
			$vtask = 'orphans';
		}
		$url .= ($vtask)  ? '&task='.$vtask       : '';
		$url .= ($pid)    ? '&task=children&pid='.$pid : '';
		$url .= ($limit)  ? '&limit='.$limit      : '';
		$url .= ($start)  ? '&limitstart='.$start : '';
		$url .= ($search) ? '&search='.$search    : '';
		$url .= ($sort)   ? '&filter_order='.$sort        : '';
		$url .= ($sort_Dir) ? '&filter_order_Dir='.$sort_Dir        : '';
		$url .= '&status='.$status;
		$url .= ($type)   ? '&type='.$type        : '';
	
		return $url;
	}

	//-----------

	private function userSelect( $name, $active, $nouser=0, $javascript=NULL, $order='a.name' ) 
	{
		$database =& JFactory::getDBO();

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		$query = "SELECT a.id AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = " . $aro_id . ""	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=25"
			. "\n ORDER BY ". $order;

		$database->setQuery( $query );
		$result = $database->loadObjectList();

		if ($nouser) {
			$users[] = JHTML::_('select.option', '0', 'Do not change', 'value', 'text');
			$users = array_merge( $users, $result );
		} else {
			$users = $result;
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' '. $javascript, 'value', 'text', $active, false, false );

		return $users;
	}
	
	//-----------
	
	protected function getauthor() 
	{
		$u = JRequest::getInt('u', 0);
		
		// Get the member's info
		ximport('xprofile');
		$profile = new XProfile();
		$profile->load( $u );

		if (!$profile->get('name')) {
			$name  = $profile->get('givenName').' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName').' ' : '';
			$name .= $profile->get('surname');
		} else {
			$name  = $profile->get('name');
		}
		
		echo $name .' ('.$profile->get('uidNumber').')';
	}
	
	//----------------------------------------------------------
	// Functions for tagging a resource
	//----------------------------------------------------------

	protected function edittags()
	{
		$database =& JFactory::getDBO();
		
		$id = JRequest::getInt( 'id', 0 );

		// Get resource title
		$row = new ResourcesResource( $database );
		$row->load($id);

		// Get all tags
		$query  = "SELECT id, tag, raw_tag, alias, admin FROM #__tags ORDER BY raw_tag ASC";
		$database->setQuery( $query );
		$tags = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		// Get tags for this resource
		$rt = new ResourcesTags( $database );
		//$tags_men = $this->get_tags($id, 0);
		$tags_men = $rt->getTags($id, 0, 0, 1);
		$mytagarray = array();
		$myrawtagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[]    = $tag_men->tag;
			$myrawtagarray[] = $tag_men->raw_tag;
		}
		$objtags->tag_men = implode( ', ', $myrawtagarray );

		// Output HTML
		ResourcesHtml::edit_tags( $database, $objtags, $this->_option, $row->title, $id, $tags, $mytagarray );
	}

	//-----------

	protected function savetags()
	{
	    $database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$t_tags = JRequest::getVar( 'tags', '' );
		$c_tags = JRequest::getVar( 'tgs', array(0) );
	
		// Process tags
		$tagging = new ResourcesTags( $database );
		$tagArray  = $tagging->_parse_tags($t_tags);
		$tagArray2 = $tagging->_parse_tags($t_tags,1);
		$diff_tags = array_diff($tagArray, $c_tags);
		foreach ($diff_tags as $diffed)
		{
			array_push($c_tags,$tagArray2[$diffed]);
		}
		$tags = implode( ',', $c_tags );
		$tagging->tag_object($juser->get('id'), $id, $tags, 0, 1);
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}
?>