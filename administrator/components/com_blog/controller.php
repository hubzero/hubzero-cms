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
 * Short description for 'BlogController'
 * 
 * Long description (if any) ...
 */
class BlogController extends Hubzero_Controller
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


	/**
	 * Short description for 'entries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'setState'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'setComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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

	/**
	 * Short description for 'cancel'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function cancel()
	{
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}
}

