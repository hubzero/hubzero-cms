<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class CitationsController extends Hubzero_Controller
{
	public function execute()
	{
		$default = 'browse';
		
		$task = strtolower(JRequest::getVar('task', $default, 'default'));
		
		$thisMethods = get_class_methods( get_class( $this ) );
		if (!in_array($task, $thisMethods)) {
			$task = $default;
			if (!in_array($task, $thisMethods)) {
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}

		$this->_task = $task;
		$this->$task();
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	public function browse()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Instantiate a new view
		$view = new JView( array('name'=>'citations') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get filters
		$view->filters = array();
		$view->filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		$view->filters['sort']   = $app->getUserStateFromRequest($this->_option.'.sort', 'sort', 'created DESC');

		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');

		$obj = new CitationsCitation( $this->database );
		
		// Get a record count
		$view->total = $obj->getCount( $view->filters );

		// Get records
		$view->rows = $obj->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------
	
	private function add() 
	{
		$this->edit();
	}
	
	//-----------
	
	private function edit()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'citation') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar( 'id', array() );
		
		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		// Load the object
		$view->row = new CitationsCitation( $this->database );
		$view->row->load( $id );
		
		// Load the associations object
		$assoc = new CitationsAssociation( $this->database );
		
		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) {
			$juser =& JFactory::getUser();
			$view->row->uid = $juser->get('id');
			
			// It's new - no associations to get
			$view->assocs = array();
		} else {
			// Get the associations
			$view->assocs = $assoc->getRecords( array('cid'=>$id) );
		}
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	private function stats() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'stats') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Load the object
		$row = new CitationsCitation( $this->database );
		$view->stats = $row->getStats();
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$citation = JRequest::getVar('citation', array(), 'post');
		$citation = array_map('trim', $citation);
		
		// Bind incoming data to object
		$row = new CitationsCitation( $this->database );
		if (!$row->bind( $citation )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
	
		// New entry so set the created date
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
		}
		
		// Check content for missing required data
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Incoming associations
		$arr = JRequest::getVar( 'assocs', array(), 'post' );
		
		$ignored = array();
		
		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation( $this->database );
			
			if (!$this->_isempty($a, $ignored)) {
				$a['cid'] = $row->id;
			
				// bind the data
				if (!$assoc->bind( $a )) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}
		
				// Check content
				if (!$assoc->check()) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}

				// Store new content
				if (!$assoc->store()) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}
			} elseif ($this->_isEmpty($a, $ignored) && !empty($a['id'])) {
				// Delete the row
				if (!$assoc->delete( $a['id'] )) {
					JError::raiseError( 500, $assoc->getError() );
					return;
				}
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'CITATION_SAVED' );
	}

	//-----------
	
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore,$b)) {
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b)) {
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v) 
		{
			if ($v) {
				$e = false;
			}
		}
		return $e;
	}

	//-----------
	
	protected function remove()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation( $this->database );
			$assoc = new CitationsAssociation( $this->database );
			$author = new CitationsAuthor( $this->database );
			foreach ($ids as $id) 
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords( array('cid'=>$id) );
				foreach ($assocs as $a) 
				{
					$assoc->delete( $a->id );
				}
				
				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords( array('cid'=>$id) );
				foreach ($authors as $a) 
				{
					$author->delete( $a->id );
				}
				
				// Delete the citation
				$citation->delete( $id );
			}
			
			$this->_message = JText::_('CITATION_REMOVED');
		} else {
			$this->_message = JText::_('NO_SELECTION');
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}

