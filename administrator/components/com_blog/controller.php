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

ximport('Hubzero_Controller');

class BlogController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_task = strtolower(JRequest::getVar('task', ''));

		switch ($this->_task) 
		{
			case 'add':    $this->edit();   break;
			case 'edit':   $this->edit();   break;
			case 'save':   $this->save();   break;
			case 'delete': $this->delete(); break;
			case 'cancel': $this->cancel(); break;
			case 'publish': $this->setState(); break;
			case 'unpublish': $this->setState(); break;
			case 'disallow': $this->setComments(); break;
			case 'allow': $this->setComments(); break;

			default: $this->entries(); break;
		}
	}
	
	//----------------------------------------------------------
	// Our tasks
	//----------------------------------------------------------

	protected function entries()
	{
		// Get configuration
		$jconfig = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Instantiate a new view
		$view = new JView( array('name'=>'entries') );
		
		// Get paging variables
		$filters = array();
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $jconfig->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);

		// Instantiate our HelloEntry object
		$obj = new BlogEntry( $this->database );
		
		// Get record count
		$view->total = $obj->getEntriesCount( $filters );

		// Get records
		$view->rows = $obj->getEntries( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $filters['start'], $filters['limit'] );
		
		// Pass the view any data it may need
		$view->option = $this->_option;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function edit() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}

		// Load the article
		$view->row = new BlogEntry( $this->database );
		$view->row->load( $id );
		
		// Pass the view any data it may need
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function save() 
	{
		// Initiate extended database class
		$row = new BlogEntry( $this->database );
		if (!$row->bind( $_POST )) {
			echo BlogHtml::alert( $row->getError() );
			exit();
		}
		
		// Check content
		if (!$row->check()) {
			echo BlogHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo BlogHtml::alert( $row->getError() );
			exit();
		}

		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Entry saved!');
	}

	//-----------

	protected function delete() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array() );

		if (!empty($ids)) {
			// Create a category object
			$entry = new BlogEntry( $this->database );
			
			// Loop through all the IDs
			foreach ($ids as $id)
			{
				// Delete the entry
				$entry->delete( $id );
			}
		}
		
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Entries deleted!');
	}
	
	//-----------

	protected function setState() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );

		// Check for a resource
		if (count( $ids ) < 1) {
			echo BlogHtml::alert( JText::sprintf('Select an entry to %s',$this->_task) );
			exit();
		}

		// Loop through all the IDs
		foreach ($ids as $id) 
		{
			// Load the article
			$row = new BlogEntry( $this->database );
			$row->load( $id );
			
			switch ($this->_task) 
			{
				case 'publish': $row->state = 1; break;
				case 'unpublish': $row->state = 0; break;
			}

			// Store new content
			if (!$row->store()) {
				echo BlogHtml::alert( $row->getError() );
				exit();
			}
		}
		
		switch ($this->_task) 
		{
			case 'publish': 
				$this->_message = JText::sprintf('%s Item(s) successfully Published', count($ids));
			break;
			case 'unpublish':
				$this->_message = JText::sprintf('%s Item(s) successfully Unpublished', count($ids));
			break;
			case 'archive': 
				$this->_message = JText::sprintf('%s Item(s) successfully Archived', count($ids));
			break;
		}
		
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------

	protected function setComments() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );

		// Check for a resource
		if (count( $ids ) < 1) {
			echo BlogHtml::alert( JText::sprintf('Select an entry to %s comments',$this->_task) );
			exit();
		}

		// Loop through all the IDs
		foreach ($ids as $id) 
		{
			// Load the article
			$row = new BlogEntry( $this->database );
			$row->load( $id );
			
			switch ($this->_task) 
			{
				case 'allow': $row->allow_comments = 1; break;
				case 'disallow': $row->allow_comments = 0; break;
			}

			// Store new content
			if (!$row->store()) {
				echo BlogHtml::alert( $row->getError() );
				exit();
			}
		}
		
		switch ($this->_task) 
		{
			case 'allow': 
				$this->_message = JText::sprintf('%s Item(s) successfully turned on Comments', count($ids));
			break;
			case 'disallow':
				$this->_message = JText::sprintf('%s Item(s) successfully turned off Comments', count($ids));
			break;
		}
		
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//-----------

	protected function cancel()
	{
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}
