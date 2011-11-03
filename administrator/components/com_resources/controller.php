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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'ResourcesController'
 * 
 * Long description (if any) ...
 */
class ResourcesController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
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
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------


	/**
	 * Short description for 'resources'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function resources()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'resources') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components'.DS.$this->_option.DS.'resources.css');

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.resources.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.resources.limitstart', 'limitstart', 0, 'int');
		$view->filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.resources.search','search', '')));
		$view->filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.resources.sort', 'filter_order', 'created'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.resources.sortdir', 'filter_order_Dir', 'DESC'));
		$view->filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.resources.status', 'status', 'all' ));
		$view->filters['type']     = trim($app->getUserStateFromRequest($this->_option.'.resources.type', 'type', '' ));

		// Get record count
		$sqlcount  = "SELECT count(*) FROM #__resources AS r ";
		$sqlcount .= "WHERE r.standalone=1";
		if ($view->filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['type']) {
			$sqlcount .= "\n AND r.type=".$view->filters['type'];
		}
		if ($view->filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".addslashes($view->filters['search'])."%'";
			if (is_numeric($view->filters['search'])) {
				$sqlcount .= "\n OR r.id=".$view->filters['search'];
			}
			$sqlcount .= ")";
		}
		$this->database->setQuery( $sqlcount );
		$view->total = $this->database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

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
		if ($view->filters['status'] != 'all') {
			$query .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['type']) {
			$query .= "\n AND r.type=".$view->filters['type'];
		}
		if ($view->filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".addslashes($view->filters['search'])."%'";
			if (is_numeric($view->filters['search'])) {
				$query .= "\n OR r.id=".$view->filters['search'];
			}
			$query .= ")";
		}
		$query .= " ORDER BY ".$view->filters['sort']." ".$view->filters['sort_Dir']." LIMIT ".$view->pageNav->limitstart.",".$view->pageNav->limit;

		$this->database->setQuery( $query );
		$view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) {
			echo $this->database->stderr();
			return false;
		}

		// Get <select> of types
		$rt = new ResourcesType( $this->database );
		$view->types = $rt->getMajorTypes();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'children'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function children()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'children') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/resources.css');

		// Resource's parent ID
		$view->pid = JRequest::getInt( 'pid', 0 );

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.children.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.children.limitstart', 'limitstart', 0, 'int');
		$view->filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.children.search','search', '')));
		$view->filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.children.sort', 'filter_order', 'ordering'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.children.sortdir', 'filter_order_Dir', 'ASC'));
		$view->filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.children.status', 'status', 'all' ));

		// Record count
		$sqlcount = "SELECT count(*) FROM #__resources AS r, #__resource_assoc AS ra WHERE ra.child_id=r.id AND ra.parent_id=".$view->pid;
		if ($view->filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".$view->filters['search']."%'";
			if (is_numeric($view->filters['search'])) {
				$sqlcount .= "\n OR r.id=".$view->filters['search'];
			}
			$sqlcount .= ")";
		}
		$this->database->setQuery( $sqlcount );
		$view->total = $this->database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Get only children of this parent
		$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
					r.publish_up, r.publish_down, r.path, r.checked_out, r.checked_out_time, r.standalone, u.name AS editor, g.name AS groupname, 
					lt.type AS logicaltitle, ra.*, gt.type as grouptitle, t.type AS typetitle, NULL as position, 
					(SELECT count(*) FROM #__resource_assoc AS rraa WHERE rraa.child_id=r.id AND rraa.parent_id!=".$view->pid.") AS multiuse";
		$query .= "\n FROM #__resource_types AS t, #__resources AS r";
		$query .= "\n LEFT JOIN #__users AS u ON u.id = r.checked_out";
		$query .= "\n LEFT JOIN #__groups AS g ON g.id = r.access";
		$query .= "\n LEFT JOIN #__resource_types AS lt ON lt.id=r.logical_type, #__resource_assoc AS ra ";
		$query .= "\n LEFT JOIN #__resource_types AS gt ON gt.id=ra.grouping";
		$query .= "\n WHERE r.type=t.id AND ra.child_id=r.id AND ra.parent_id=".$view->pid;
		if ($view->filters['status'] != 'all') {
			$query .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".$view->filters['search']."%'";
			if (is_numeric($view->filters['search'])) {
				$query .= "\n OR r.id=".$view->filters['search'];
			}
			$query .= ")";
		}
		$query .= " ORDER BY ".$view->filters['sort']." ".$view->filters['sort_Dir']." LIMIT ".$view->pageNav->limitstart.",".$view->pageNav->limit;
		$this->database->setQuery( $query );
		$view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) {
			echo $this->database->stderr();
			return false;
		}

		// Get parent info
		$view->parent = new ResourcesResource( $this->database );
		$view->parent->load( $view->pid );

		// Get sections
		$view->sections = array();
		if ($view->parent->type == 4) {
			$rt = new ResourcesType( $this->database );
			$view->sections = $rt->getTypes( 29 );
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'orphans'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function orphans()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'children') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/resources.css');

		$view->pid = '-1';

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = $app->getUserStateFromRequest($this->_option.'.orphans.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']    = $app->getUserStateFromRequest($this->_option.'.orphans.limitstart', 'limitstart', 0, 'int');
		$view->filters['search']   = urldecode(trim($app->getUserStateFromRequest($this->_option.'.orphans.search','search', '')));
		$view->filters['sort']     = trim($app->getUserStateFromRequest($this->_option.'.orphans.sort', 'filter_order', 'title'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.orphans.sortdir', 'filter_order_Dir', 'DESC'));
		$view->filters['status']   = trim($app->getUserStateFromRequest($this->_option.'.orphans.status', 'status', 'all' ));

		// Get record count
		$sqlcount  = "SELECT count(*) FROM #__resources AS r ";
		$sqlcount .= "WHERE standalone!=1";
		if ($view->filters['status'] != 'all') {
			$sqlcount .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['search']) {
			$sqlcount .= "\n AND (LOWER( r.title ) LIKE '%".$view->filters['search']."%'";
			if (is_numeric($view->filters['search'])) {
				$sqlcount .= "\n OR r.id=".$view->filters['search'];
			}
			$sqlcount .= ")";
		}
		$sqlcount .= " AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		$this->database->setQuery( $sqlcount );
		$view->total = $this->database->loadResult();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Get records
		$query  = "SELECT r.id, r.title, r.type, r.logical_type, r.created, r.created_by, r.access, r.published, 
					r.publish_up, r.publish_down, r.checked_out, r.checked_out_time, r.path, r.standalone, u.name AS editor, g.name AS groupname, 
					t.type AS typetitle, NULL as logicaltitle";
		$query .= "\n FROM #__resources AS r";
		$query .= "\n LEFT JOIN #__users AS u ON u.id = r.checked_out";
		$query .= "\n LEFT JOIN #__groups AS g ON g.id = r.access";
		$query .= "\n LEFT JOIN #__resource_types AS t ON t.id=r.type";
		$query .= "\n WHERE r.standalone!=1";
		if ($view->filters['status'] != 'all') {
			$query .= " AND r.published=".$view->filters['status'];
		}
		if ($view->filters['search']) {
			$query .= "\n AND (LOWER( r.title ) LIKE '%".$view->filters['search']."%'";
			if (is_numeric($view->filters['search'])) {
				$query .= "\n OR r.id=".$view->filters['search'];
			}
			$query .= ")";
		}
		$query .= " AND NOT EXISTS(SELECT * FROM #__resource_assoc AS a WHERE a.child_id = r.id)";
		$query .= " ORDER BY ".$view->filters['sort']." ".$view->filters['sort_Dir']." LIMIT ".$view->pageNav->limitstart.",".$view->pageNav->limit;
		$this->database->setQuery( $query );
		$view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) {
			echo $this->database->stderr();
			return false;
		}

		// Get sections
		$rt = new ResourcesType( $this->database );
		$view->sections = $rt->getTypes( 29 );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'ratings'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function ratings()
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Do we have an ID to work with?
		if (!$id) {
			return false;
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'resource', 'layout'=>'ratings') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$rr = new ResourcesReview( $this->database );
		$view->rows = $rr->getRatings( $id );

		$view->id = $id;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	// Children
	//----------------------------------------------------------


	/**
	 * Short description for 'addchild'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function addchild()
	{
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
				// Instantiate a new view
				$view = new JView( array('name'=>'addchild') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->pid = $pid;

				// Get the available types
				$rt = new ResourcesType( $this->database );
				$view->types = $rt->getTypes( 30 );

				// Load the parent resource
				$view->parent = new ResourcesResource( $this->database );
				$view->parent->load( $view->pid );

				// Set any errors
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				// Output the HTML
				$view->display();
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
						$child = new ResourcesResource( $this->database );
						$child->load( $cid );

						if ($child && $child->title != '') {
							// Link 'em up!
							$this->attachchild( $cid, $pid );
						} else {
							// Instantiate a new view
							$view = new JView( array('name'=>'addchild') );
							$view->option = $this->_option;
							$view->task = $this->_task;
							$view->pid = $pid;

							// No child ID! Throw an error and present the form from the previous step
							$this->setError( JText::_('Resource with provided ID # not found.')) ;

							// Get the available types
							$rt = new ResourcesType( $this->database );
							$view->types = $rt->getTypes( 30 );

							// Load the parent resource
							$view->parent = new ResourcesResource( $this->database );
							$view->parent->load( $pid );

							// Set any errors
							if ($this->getError()) {
								$view->setError( $this->getError() );
							}

							// Output the HTML
							$view->display();
						}
					} else {
						// Instantiate a new view
						$view = new JView( array('name'=>'addchild') );
						$view->option = $this->_option;
						$view->task = $this->_task;
						$view->pid = $pid;

						// No child ID! Throw an error and present the form from the previous step
						$this->setError( JText::_('Please provide an ID #')) ;

						// Get the available types
						$rt = new ResourcesType( $this->database );
						$view->types = $rt->getTypes( 30 );

						// Load the parent resource
						$view->parent = new ResourcesResource( $this->database );
						$view->parent->load( $pid );

						// Set any errors
						if ($this->getError()) {
							$view->setError( $this->getError() );
						}

						// Output the HTML
						$view->display();
					}
				}
			break;
		}
	}

	/**
	 * Short description for 'attachchild'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      unknown $pid Parameter description (if any) ...
	 * @return     void
	 */
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

		// Instantiate a ResourcesAssoc object
		$assoc = new ResourcesAssoc( $this->database );

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

	/**
	 * Short description for 'removechild'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

		$assoc = new ResourcesAssoc( $this->database );

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


	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $isnew Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit( $isnew=0 )
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'resource') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->isnew = $isnew;

		// Get the resource component config
		$view->rconfig = $this->config;

		// Push some needed styles to the tmeplate
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/'.$this->_option.'/resources.css');

		// Incoming resource ID
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array( $id )) {
			$id = $id[0];
		}

		// Incoming parent ID - this determines if the resource is standalone or not
		$view->pid = JRequest::getInt( 'pid', 0 );

		// Grab some filters for returning to place after editing
		$view->return = array();
		$view->return['type']   = JRequest::getVar( 'type', '' );
		$view->return['sort']   = JRequest::getVar( 'sort', '' );
		$view->return['status'] = JRequest::getVar( 'status', '' );

		// Instantiate our resource object
		$view->row = new ResourcesResource( $this->database );
		$view->row->load( $id );

		// Fail if checked out not by 'me'
		if ($view->row->checked_out && $view->row->checked_out <> $this->juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_( 'This resource is currently being edited by another administrator' );
			return;
		}

		// Is this a new resource?
		if (!$id) {
			$view->row->created      = date( 'Y-m-d H:i:s', time() );
			$view->row->created_by   = $this->juser->get('id');
			$view->row->modified     = '0000-00-00 00:00:00';
			$view->row->modified_by  = 0;
			$view->row->publish_up   = date( 'Y-m-d H:i:s', time() );
			$view->row->publish_down = 'Never';
			if ($view->pid) {
				$view->row->published  = 1;
				$view->row->standalone = 0;
			} else {
				$view->row->published  = 3; // default to "new" status
				$view->row->standalone = 1;
			}
		}

		// Editing existing
		$view->row->checkout( $this->juser->get('id') );

		if (trim( $view->row->publish_down ) == '0000-00-00 00:00:00') {
			$view->row->publish_down = JText::_('Never');
		}

		// Get name of resource creator
		$query = "SELECT name from #__users WHERE id=".$view->row->created_by;
		$this->database->setQuery( $query );
		$view->row->created_by_name = $this->database->loadResult();
		$view->row->created_by_name = ($view->row->created_by_name) ? $view->row->created_by_name : JText::_('Unknown');

		// Get name of last person to modify resource
		if ($view->row->modified_by) {
			$query = "SELECT name from #__users WHERE id=".$view->row->modified_by;
			$this->database->setQuery( $query );
			$view->row->modified_by_name = $this->database->loadResult();
			$view->row->modified_by_name = ($view->row->modified_by_name) ? $view->row->modified_by_name : JText::_('Unknown');
		} else {
			$view->row->modified_by_name = '';
		}

		// Get params definitions
		$view->params  = new JParameter( $view->row->params, JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'resources.xml' );
		$view->attribs = new JParameter( $view->row->attribs );

		// Build selects of various types
		$rt = new ResourcesType( $this->database );
		if ($view->row->standalone != 1) {
			$view->lists['type'] = ResourcesHtml::selectType( $rt->getTypes( 30 ), 'type', $view->row->type, '', '', '', '');
			$view->lists['logical_type'] = ResourcesHtml::selectType( $rt->getTypes( 28 ), 'logical_type', $view->row->logical_type, '[ none ]', '', '', '');
			$view->lists['sub_type'] = ResourcesHtml::selectType( $rt->getTypes( 30 ), 'logical_type', $view->row->logical_type, '[ none ]', '', '', '');
		} else {
			$view->lists['type'] = ResourcesHtml::selectType( $rt->getTypes( 27 ), 'type', $view->row->type, '', '', '', '');
			$view->lists['logical_type'] = ResourcesHtml::selectType( $rt->getTypes( 21 ), 'logical_type', $view->row->logical_type, '[ none ]', '', '', '');
		}

		// Build the <select> of admin users
		$view->lists['created_by'] = $this->userSelect( 'created_by', 0, 1 );

		// Build the <select> for the group access
		$view->lists['access'] = ResourcesHtml::selectAccess($view->rconfig->get('accesses'), $view->row->access);

		// Is this a standalone resource?
		if ($view->row->standalone == 1) {
			// Get groups
			ximport('Hubzero_Group');
			$filters = array();
			$filters['authorized'] = 'admin';
			$filters['fields'] = array('cn','description','published','gidNumber','type');
			$filters['type'] = array(1,3);
			$filters['sortby'] = 'description';
			$groups = Hubzero_Group::find($filters);

			// Build <select> of groups
			$view->lists['groups'] = ResourcesHtml::selectGroup($groups, $view->row->group_owner);

			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );

			// Get all contributors
			$mp = new MembersProfile( $this->database );
			$members = null; //$mp->getRecords( array('sortby'=>'surname DESC','limit'=>'all','search'=>'','show'=>''), true );

			// Get all contributors linked to this resource
			$ma = new MembersAssociation( $this->database );
			$sql = "SELECT n.uidNumber AS id, a.name, n.givenName, n.middleName, n.surname, a.role, a.organization  
					FROM $mp->_tbl AS n, $ma->_tbl AS a  
					WHERE a.subtable='resources'
					AND a.subid=".$view->row->id." 
					AND n.uidNumber=a.authorid
					ORDER BY a.ordering";
			$this->database->setQuery( $sql );
			$authnames = $this->database->loadObjectList();

			// Build <select> of contributors
			$view->lists['authors'] = ResourcesHtml::selectAuthors($members, $authnames, $view->attribs, $this->_option);

			// Get the tags on this item
			$rt = new ResourcesTags( $this->database );
			$view->lists['tags'] = $rt->get_tag_string($view->row->id, 0, 0, NULL, 0, 1);
		}

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initiate extended database class
		$row = new ResourcesResource( $this->database );
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
			$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
		} else {
			$old = new ResourcesResource( $this->database );
			$old->load( $row->id );

			$created_by_id = JRequest::getInt( 'created_by_id', 0 );

			// Updating entry
			$row->modified    = date( "Y-m-d H:i:s" );
			$row->modified_by = $this->juser->get('id');
			//$row->created     = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			if ($created_by_id) {
				$row->created_by = $row->created_by ? $row->created_by : $created_by_id;
			} else {
				$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
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
			$type = new ResourcesType( $this->database );
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
					$row->fulltext .= "\n".'<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>'."\n";
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
		$rt = new ResourcesTags($this->database);
		$rt->tag_object($this->juser->get('id'), $row->id, $tags, 1, 1);

		// Incoming authors
		$authorsOldstr = JRequest::getVar( 'old_authors', '', 'post' );
		$authorsNewstr = JRequest::getVar( 'new_authors', '', 'post' );
		if (!$authorsNewstr) {
			$authorsNewstr = $authorsOldstr;
		}
		//if ($authorsNewstr != $authorsOldstr) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'tables'.DS.'contributor.php');

			$authorsNew = split(',',$authorsNewstr);
			$authorsOld = split(',',$authorsOldstr);

			// We have either a new ordering or new authors or both
			if ($authorsNewstr) {
				for ($i=0, $n=count( $authorsNew ); $i < $n; $i++)
				{
					$rc = new ResourcesContributor( $this->database );
					$rc->subtable = 'resources';
					$rc->subid = $row->id;
					$rc->authorid = $authorsNew[$i];
					$rc->ordering = $i;
					$rc->role = trim(JRequest::getVar( $authorsNew[$i].'_role', '' ));
					$rc->name = trim(JRequest::getVar( $authorsNew[$i].'_name', '' ));
					$rc->organization = trim(JRequest::getVar( $authorsNew[$i].'_organization', '' ));
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
				$rc = new ResourcesContributor( $this->database );

				for ($i=0, $n=count( $authorsOld ); $i < $n; $i++)
				{
					if (!in_array($authorsOld[$i], $authorsNew)) {
						$rc->deleteAssociation( $authorsOld[$i], $row->id, 'resources' );
					}
				}
			}
		//}

		// If this is a child, add parent/child association
		$pid = JRequest::getInt( 'pid', 0, 'post' );
		if ($isNew && $pid) {
			$this->attachchild( $row->id, $pid );
		}

		// Is this a standalone resource and we need to email approved submissions?
		if ($row->standalone == 1 && $this->config->get('email_when_approved')) {
			// If the state went from pending to published
			if ($row->published == 1 && $old->published == 3) {
				$this->email_contributors($row, $this->database);
			}
		}

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
		$this->_message = JText::_('Item successfully saved');
	}

	/**
	 * Short description for 'email_contributors'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @param      unknown $database Parameter description (if any) ...
	 * @return     void
	 */
	private function email_contributors($row, $database)
	{
		include_once( JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'helpers'.DS.'helper.php' );
		$helper = new ResourcesHelper( $row->id, $database );
		$helper->getContributorIDs();

		$contributors = $helper->contributorIDs;

		if ($contributors && count($contributors) > 0) {
			// Email all the contributors
			$jconfig =& JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('SUBMISSIONS');

			// Message subject
			$subject = JText::_('EMAIL_SUBJECT');

			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option='.$this->_option.'&id='. $row->id);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}

			// Build message
			$message  = JText::sprintf('EMAIL_MESSAGE', $jconfig->getValue('config.sitename'))."\r\n";
			$message .= $jconfig->getValue('config.sitename').DS.'resources'.DS.$row->id;

			// Send message
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'resources_submission_approved', $subject, $message, $from, $contributors, $this->_option ))) {
				$this->setError( JText::_('Failed to message users.') );
			}
		}
	}

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
			$row = new ResourcesResource( $this->database );
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


	/**
	 * Short description for 'regroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function regroup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$assoc = new ResourcesAssoc( $this->database );
		$assoc->loadAssoc( $pid, $id );
		$assoc->grouping = JRequest::getInt( 'grouping'.$id, 0, 'post' );
		$assoc->store();

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	/**
	 * Short description for 'access'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function access()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			echo ResourcesHtml::alert( JText::_('No Resource ID found.') );
			exit;
		}

		// Choose access level
		switch ($this->_task)
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
			case 'accessprotected':  $access = 3; break;
			case 'accessprivate':    $access = 4; break;
			default: $access = 0; break;
		}

		// Load resource info
		$row = new ResourcesResource( $this->database );
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

	/**
	 * Short description for 'publish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function publish()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit( 'Invalid Token' );

		$publish = ($this->_task == 'publish') ? 1 : 0;

		// Incoming
		$pid = JRequest::getInt( 'pid', 0 );
		$ids = JRequest::getVar( 'id', array() );

		// Check for a resource
		if (count( $ids ) < 1) {
			echo ResourcesHtml::alert( JText::sprintf('Select a resource to %s',$this->_task) );
			exit();
		}

		// Loop through all the IDs
		foreach ($ids as $id)
		{
			// Load the resource
			$resource = new ResourcesResource( $this->database );
			$resource->load( $id );

			// Only allow changes if the resource isn't checked out or
			// is checked out by the user requesting changes
			if (!$resource->checked_out || $resource->checked_out == $this->juser->get('id')) {
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
						$this->email_contributors($resource, $this->database);
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

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );

		// Checkin the resource
		$row = new ResourcesResource($this->database);
		$row->bind( $_POST );
		$row->checkin();

		// Redirect
		$this->_redirect = $this->buildRedirectURL( $pid );
	}

	/**
	 * Short description for 'resethits'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function resethits()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			// Load the object, reset the hits, save, checkin
			$row = new ResourcesResource($this->database);
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

	/**
	 * Short description for 'resetrating'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function resetrating()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($this->database);
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

	/**
	 * Short description for 'resetranking'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function resetranking()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		if ($id) {
			// Load the object, reset the ratings, save, checkin
			$row = new ResourcesResource($this->database);
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

	/**
	 * Short description for 'checkin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function checkin()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );

		// Make sure we have at least one ID 
		if (count( $ids ) < 1) {
			echo ResourcesHtml::alert( JText::_('Select a resource to check in') );
			exit;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the resource and check it in
			$row = new ResourcesResource( $this->database );
			$row->load( $id );
			$row->checkin();
		}

		// Redirect
		$this->_redirect = $this->buildRedirectURL();
	}

	/**
	 * Short description for 'reorder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function reorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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
		$resource1 = new ResourcesAssoc( $this->database );
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


	/**
	 * Short description for 'viewtypes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function viewtypes()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'types') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.types.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.types.limitstart', 'limitstart', 0, 'int');
		$view->filters['sort']  = trim($app->getUserStateFromRequest($this->_option.'.types.sort', 'filter_order', 'category'));
		$view->filters['sort_Dir'] = trim($app->getUserStateFromRequest($this->_option.'.types.sortdir', 'filter_order_Dir', 'DESC'));
		$view->filters['category'] = $app->getUserStateFromRequest($this->_option.'.types.category', 'category', 0, 'int');

		// Instantiate an object
		$rt = new ResourcesType( $this->database );

		// Get a record count
		$view->total = $rt->getAllCount( $view->filters );

		// Get records
		$view->rows = $rt->getAllTypes( $view->filters );

		// initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Get the category names
		$view->cats = $rt->getTypes('0');

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'newtype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function newtype()
	{
		$this->edittype();
	}

	/**
	 * Short description for 'edittype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function edittype()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'type') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming (expecting an array)
		$id = JRequest::getVar( 'id', array(0) );
		if (is_array($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}

		// Load the object
		$view->row = new ResourcesType( $this->database );
		$view->row->load( $id );

		// Get the categories
		$view->categories = $view->row->getTypes( 0 );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'savetype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function savetype()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initiate extended database class
		$row = new ResourcesType( $this->database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		$row->contributable = ($row->contributable) ? $row->contributable : '0';
		$row->alias = ($row->alias) ? $row->alias : preg_replace("/[^a-zA-Z0-9\-_]/", "", strtolower($row->type));

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

	/**
	 * Short description for 'normalize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $txt Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function normalize($txt)
	{
		// Strip any non-alphanumeric characters
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $txt));
	}

	/**
	 * Short description for 'deletetype'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deletetype()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming (expecting an array)
		$ids = JRequest::getVar( 'id', array() );

		// Ensure we have an ID to work with
		if (empty($ids)) {
			$this->_message = JText::_('No type selected');
			$this->_redirect = 'index.php?option='.$this->_option.'&task=viewtypes';
			return;
		}

		$rt = new ResourcesType( $this->database );

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


	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function upload()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

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

	/**
	 * Short description for 'deletefolder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function deletefolder()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

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

	/**
	 * Short description for 'deletefile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function deletefile()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

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

	/**
	 * Short description for 'media'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function media()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'resource', 'layout'=>'media') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming directory (this should be a path built from a resource ID and its creation year/month)
		$view->listdir = JRequest::getVar( 'listdir', '' );
		if (!$view->listdir) {
			echo ResourcesHtml::error( JText::_('No list directory provided.') );
			return;
		}

		// Incoming sub-directory
		$view->subdir = JRequest::getVar( 'subdir', '' );
		if (!$view->subdir) {
			$view->subdir = JRequest::getVar( 'dirPath', '', 'post' );
		}

		// Build the path
		$view->path = $this->buildUploadPath( $view->listdir, $view->subdir );

		// Get list of directories
		$dirs = $this->recursive_listdir( $view->path );

		$folders   = array();
		$folders[] = JHTML::_('select.option', '/');
		if ($dirs) {
			foreach ($dirs as $dir)
			{
				$folders[] = JHTML::_('select.option', substr($dir,strlen($view->path)));
			}
		}
		if (is_array($folders)) {
			sort( $folders );
		}

		// Create folder <select> list
		$view->dirPath = JHTML::_('select.genericlist', $folders, 'dirPath', 'onchange="goUpDir()" ','value', 'text', $view->subdir );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'listfiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
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

		$images  = array();
		$folders = array();
		$docs    = array();

		if ($d) {
			// Loop through all files and separate them into arrays of images, folders, and other
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

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$view = new JView( array('name'=>'resource', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->listdir = $listdir;
		$view->subdir = $subdir;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'buildUploadPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listdir Parameter description (if any) ...
	 * @param      string $subdir Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'recursive_listdir'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $base Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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


	/**
	 * Short description for 'buildRedirectURL'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $pid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'userSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $active Parameter description (if any) ...
	 * @param      integer $nouser Parameter description (if any) ...
	 * @param      string $javascript Parameter description (if any) ...
	 * @param      string $order Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getauthor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function getauthor()
	{
		$u = JRequest::getInt('u', 0);

		// Get the member's info
		ximport('Hubzero_User_Profile');
		$profile = new Hubzero_User_Profile();
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


	/**
	 * Short description for 'edittags'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	protected function edittags()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'tags') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$view->id = JRequest::getInt( 'id', 0 );

		// Get resource title
		$view->row = new ResourcesResource( $this->database );
		$view->row->load($view->id);

		// Get all tags
		$query  = "SELECT id, tag, raw_tag, alias, admin FROM #__tags ORDER BY raw_tag ASC";
		$this->database->setQuery( $query );
		$view->tags = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) {
			echo $this->database->stderr();
			return false;
		}

		// Get tags for this resource
		$rt = new ResourcesTags( $this->database );
		//$tags_men = $this->get_tags($id, 0);
		$tags_men = $rt->getTags($view->id, 0, 0, 1);
		$mytagarray = array();
		$myrawtagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[]    = $tag_men->tag;
			$myrawtagarray[] = $tag_men->raw_tag;
		}
		$view->mytagarray = $mytagarray;
		$view->objtags->tag_men = implode( ', ', $myrawtagarray );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'savetags'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function savetags()
	{
	    // Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$t_tags = JRequest::getVar( 'tags', '' );
		$c_tags = JRequest::getVar( 'tgs', array(0) );

		// Process tags
		$tagging = new ResourcesTags( $this->database );
		$tagArray  = $tagging->_parse_tags($t_tags);
		$tagArray2 = $tagging->_parse_tags($t_tags,1);
		$diff_tags = array_diff($tagArray, $c_tags);
		foreach ($diff_tags as $diffed)
		{
			array_push($c_tags,$tagArray2[$diffed]);
		}
		$tags = implode( ',', $c_tags );
		$tagging->tag_object($this->juser->get('id'), $id, $tags, 0, 1);

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}

