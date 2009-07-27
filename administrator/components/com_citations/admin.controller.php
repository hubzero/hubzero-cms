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

class CitationsController extends JObject
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

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	public function browse()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Get filters
		$filter = array();
		$filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.search', 'search', ''));
		$filters['sort']   = $app->getUserStateFromRequest($this->_option.'.sort', 'sort', 'created DESC');

		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.limitstart', 'limitstart', 0, 'int');

		$obj = new CitationsCitation( $database );
		
		// Get a record count
		$total = $obj->getCount( $filters );

		// Get records
		$rows = $obj->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		CitationsHtml::browse( $rows, $pageNav, $this->_option, $filters, $this->_task );
	}

	//-----------
	
	private function add() 
	{
		$this->edit();
	}
	
	//-----------
	
	private function edit()
	{
		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar( 'id', array() );
		
		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		$database =& JFactory::getDBO();
		
		// Load the object
		$row = new CitationsCitation( $database );
		$row->load( $id );
		
		// Load the associations object
		$assoc = new CitationsAssociation( $database );
		
		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) {
			$juser =& JFactory::getUser();
			$row->uid = $juser->get('id');
			
			// It's new - no associations to get
			$assocs = array();
		} else {
			// Get the associations
			$assocs = $assoc->getRecords( array('cid'=>$id) );
		}
		
		// Output HTML
		CitationsHtml::edit( $row, $assocs, $this->_option );
	}
	
	//-----------
	
	private function stats() 
	{
		$database =& JFactory::getDBO();
		
		// Load the object
		$row = new CitationsCitation( $database );
		$stats = $row->getStats();
		
		// Output HTML
		CitationsHtml::stats( $stats, $this->_option );
	}
	
	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	private function save()
	{
		$database =& JFactory::getDBO();
	
		//$_POST = array_map('trim',$_POST);

		// Bind incoming data to object
		$row = new CitationsCitation( $database );
		if (!$row->bind( $_POST )) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}
	
		// New entry so set the created date
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
		}
		
		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = JRequest::getVar( 'uri', '', 'post' );
		
		// Check content for missing required data
		if (!$row->check()) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}
		
		// Incoming associations
		$arr = JRequest::getVar( 'assocs', array() );
		
		$ignored = array();
		
		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation( $database );
			
			if (!$this->_isempty($a, $ignored)) {
				$a['cid'] = $row->id;
			
				// bind the data
				if (!$assoc->bind( $a )) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
		
				// Check content
				if (!$assoc->check()) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}

				// Store new content
				if (!$assoc->store()) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
			} elseif ($this->_isempty($a, $ignored) && !empty($a['id'])) {
				// Delete the row
				if (!$assoc->delete( $a['id'] )) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'CITATION_SAVED' );
	}

	//-----------
	
	private function _isempty($b, $ignored=array())
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
	
	private function remove()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			$database =& JFactory::getDBO();
			
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation( $database );
			$assoc = new CitationsAssociation( $database );
			$author = new CitationsAuthor( $database );
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
			
			$msg = 'CITATION_REMOVED';
		} else {
			$msg = 'NO_SELECTION';
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( $msg );
	}
}
?>
