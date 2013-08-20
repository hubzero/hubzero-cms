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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Events controller for pages
 */
class EventsControllerPages extends Hubzero_Controller
{
	/**
	 * Display a list of entries for an event
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$ids = JRequest::getVar('id', array(0));
		if (count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['event_id'] = $ids[0];
		$this->view->filters['search']   = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		));
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		$obj = new EventsPage($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows  = $obj->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->view->event = new EventsEvent($this->database);
		$this->view->event->load($ids[0]);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for adding an entry
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$ids = JRequest::getVar('id', array());
		if (is_array($ids)) 
		{
			$id = (!empty($ids)) ? $ids[0] : 0;
		} 
		else 
		{
			$id = 0;
		}
		JRequest::setVar('id', array());
		$this->editTask($id);
	}

	/**
	 * Show a form for editing an entry
	 * 
	 * @param      integer $eid Event ID
	 * @return     void
	 */
	public function editTask($eid=null)
	{
		$this->view->setLayout('edit');

		// Incoming
		$eid = ($eid) ? $eid : JRequest::getInt('event', 0);
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (is_array($ids)) 
		{
			$id = (!empty($ids)) ? $ids[0] : 0;
		} 
		else 
		{
			$id = 0;
		}

		// Initiate database class and load info
		$this->view->page = new EventsPage($this->database);
		$this->view->page->load($id);

		$this->view->event = new EventsEvent($this->database);
		$this->view->event->load($eid);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Bind incoming data to object
		$row = new EventsPage($this->database);
		if (!$row->bind($_POST)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if (!$row->alias) 
		{
			$row->alias = $row->title;
		}

		$row->event_id = JRequest::getInt('event', 0);
		$row->alias = preg_replace("/[^a-zA-Z0-9]/", '', $row->alias);
		$row->alias = strtolower($row->alias);
		
		//set created date and user
		if ($row->id == NULL || $row->id == '' || $row->id == 0)
		{
			$row->created = date('Y-m-d H:i:s');
			$row->created_by = JFactory::getUser()->get('id');
		}
		
		//set modified date and user
		$row->modified = date('Y-m-d H:i:s');
		$row->modified_by = JFactory::getUser()->get('id');

		// Check content for missing required data
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . $row->event_id,
			JText::_('COM_EVENTS_PAGE_SAVED')
		);
	}

	/**
	 * Remove one or more entries for an event
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$event = JRequest::getInt('event', 0);
		$ids   = JRequest::getVar('id', array(0));

		// Get the single ID we're working with
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) 
		{
			$page = new EventsPage($this->database);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Remove the profile
				$page->delete($id);
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . $event,
			JText::_('EVENTS_PAGES_REMOVED')
		);
	}

	/**
	 * Move an item up one in the ordering
	 * 
	 * @return     void
	 */
	public function orderupTask()
	{
		$this->reorderTask('up');
	}

	/**
	 * Move an item down one in the ordering
	 * 
	 * @return     void
	 */
	public function orderdownTask()
	{
		$this->reorderTask('down');
	}

	/**
	 * Move an item one down or own up int he ordering
	 * 
	 * @param      string $move Direction to move
	 * @return     void
	 */
	protected function reorderTask($move='down')
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array());
		$id = $id[0];
		$pid = JRequest::getInt('event', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No page ID found.'),
				'error'
			);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No event ID found.'),
				'error'
			);
			return;
		}

		// Get the element moving down - item 1
		$page1 = new EventsPage($this->database);
		$page1->load($id);

		// Get the element directly after it in ordering - item 2
		$page2 = clone($page1);
		$page2->getNeighbor($this->_task);

		switch ($move)
		{
			case 'up':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $page2->ordering;
				$orderdn = $page1->ordering;

				$page1->ordering = $orderup;
				$page2->ordering = $orderdn;
			break;

			case 'down':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $page1->ordering;
				$orderdn = $page2->ordering;

				$page1->ordering = $orderdn;
				$page2->ordering = $orderup;
			break;
		}

		// Save changes
		$page1->store();
		$page2->store();

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . $pid
		);
	}

	/**
	 * Cancel a task by redirecting to main page
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . JRequest::getInt('event', 0)
		);
	}
}

