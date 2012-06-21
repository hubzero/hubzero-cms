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
 * Controller class for polls
 */
class XPollControllerPolls extends Hubzero_Controller
{
	/**
	 * Display a list of entries
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		// Paging
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

		$p = new XPollPoll($this->database);

		// Get a record count
		$this->view->total = $p->getCount($this->view->filters);

		// Retrieve all the records
		$this->view->rows = $p->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create an entry
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an entry
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming (expecting an array)
		$cid = JRequest::getVar('cid', array(0));
		if (!is_array($cid)) {
			$cid = array(0);
		}
		$uid = $cid[0];

		// Load the poll
		$this->view->row = new XPollPoll($this->database);
		$this->view->row->load($uid);

		// Fail if not checked out by 'me'
		if ($this->view->row->checked_out 
		 && $this->view->row->checked_out != $this->juser->get('id')) 
		{
			$this->setRedirect(
				'index.php?option='. $this->_option,
				JText::_('XPOLL_ERROR_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		// Are we editing existing or creating new?
		if ($uid) 
		{
			// Editing existing
			// Check it out
			$this->view->row->checkout($this->juser->get('id'));

			// Load the poll's options
			$xpdata = new XPollData($this->database);
			$this->view->options = $xpdata->getPollOptions($uid, true);
		} 
		else 
		{
			// Creating new
			// Set the log time to the default
			$this->view->row->lag = 3600*24;
			$this->view->options = array();
		}

		// Get selected pages
		if ($uid) 
		{
			$xpmenu = new XPollMenu($this->database);
			$lookup = $xpmenu->getMenuIds($this->view->row->id);
		} 
		else 
		{
			$lookup = array(JHTML::_('select.option', 0, JText::_('ALL'), 'value', 'text'));
		}

		// Build the html select list
		$this->view->lists = array();

		$soptions = JHTML::_('menu.linkoptions', $lookup, NULL, 1);
		if (empty($lookup)) 
		{
			$lookup = array(JHTML::_('select.option',  -1));
		}
		$this->view->lists['select'] = JHTML::_('select.genericlist', $soptions, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections');

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getError() as $error)
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

		// Incoming
		$p = JRequest::getVar('poll', array(), 'post');
		$p = array_map('trim', $p);

		// Save the poll parent information
		$row = new XPollPoll($this->database);
		if (!$row->bind($p)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}
		$isNew = ($row->id == 0);
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}
		$row->checkin();

		// Incoming poll options
		$options = JRequest::getVar('polloption', array(), 'post');

		foreach ($options as $i => $text)
		{
			// 'slash' the options
			if (!get_magic_quotes_gpc()) 
			{
				$text = addslashes($text);
			}

			if (trim($text) != '') 
			{
				$xpdata = new XPollData($this->database);
				if (!$isNew) 
				{
					$xpdata->id = $i;
				}
				$xpdata->pollid = $row->id;
				$xpdata->text = trim($text);
				if (!$xpdata->check()) 
				{
					JError::raiseError(500, $xpdata->getError());
					return;
				}
				if (!$xpdata->store()) 
				{
					JError::raiseError(500, $xpdata->getError());
					return;
				}
			}
		}

		// Remove old menu entries for this poll
		$xpmenu = new XPollMenu($this->database);
		$xpmenu->deleteEntries($row->id);

		// Update the menu visibility
		$selections = JRequest::getVar('selections', array(), 'post');

		for ($i=0, $n=count($selections); $i < $n; $i++)
		{
			$xpmenu->insertEntry($row->id, $selections[$i]);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reset the vote data
	 * 
	 * @return     void
	 */
	public function resetTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array($ids) || count($ids) < 1) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('XPOLL_ERROR_NO_SELECTION_TO_RESET'),
				'error'
			);
			return;
		}

		// Loop through the IDs
		$xpdate = new XPollDate($this->database);
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll($this->database);
			$row->load($id);

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 
			 || $row->checked_out == $this->juser->get('id')) 
			{
				// Delete the Date entries
				$xpdate->deleteEntries($id);

				// Reset voters to 0 and save
				$row->voters = 0;
				if (!$row->check()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				if (!$row->store()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				$row->checkin($id);
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Remove one or more entries
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) 
		{
			$poll = new XPollPoll($this->database);

			// Loop through the array of IDs and delete
			foreach ($ids as $id)
			{
				if (!$poll->delete($id)) 
				{
					$this->addComponentMessage($poll->getError(), 'error');
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Publish one or more entries
	 * 
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Unpublish one or more entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state for one or more entries
	 * 
	 * @param      integer $state State to set (1=publish, 0=unpublish)
	 * @return     void
	 */
	public function stateTask($state=1)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array($ids) || count($ids) < 1) 
		{
			if ($state) 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('XPOLL_ERROR_NO_SELECTION_TO_PUBLISH'),
					'error'
				);
			} 
			else 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('XPOLL_ERROR_NO_SELECTION_TO_UNPUBLISH'),
					'error'
				);
			}
			return;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll($this->database);
			$row->load($id);

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 
			 || $row->checked_out == $this->juser->get('id')) 
			{
				// Reset voters to 0 and save
				$row->published = $state;
				if (!$row->check()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				if (!$row->store()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				$row->checkin($id);
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Set an entry to closed
	 * 
	 * @return     void
	 */
	public function closeTask()
	{
		$this->openTask(0);
	}

	/**
	 * Set an entry to open
	 * 
	 * @param      integer $open Active state (1=open, 0=closed)
	 * @return     void
	 */
	public function openTask($active=1)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('cid', array(0));
		if (!is_array($ids)) 
		{
			$ids = array(0);
		}

		// Make sure we have IDs to work with
		if (!is_array($ids) || count($ids) < 1) 
		{
			if ($active) 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('XPOLL_ERROR_NO_SELECTION_TO_OPEN'),
					'error'
				);
			} 
			else 
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('XPOLL_ERROR_NO_SELECTION_TO_CLOSE'),
					'error'
				);
			}
			return;
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the poll
			$row = new XPollPoll($this->database);
			$row->load($id);

			// Only alter items not checked out or checked out by 'me'
			if ($row->checked_out == 0 
			 || $row->checked_out == $this->juser->get('id')) 
			{
				// Reset voters to 0 and save
				$row->open = $active;
				if (!$row->check()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				if (!$row->store()) 
				{
					$this->addComponentMessage($row->getError(), 'error');
					continue;
				}
				$row->checkin($id);
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Cancel a task and redirect to main listing
	 * 
	 * @return     void
	 */
	public function cancelTask()
	{
		$p = JRequest::getVar('poll', array(), 'post');

		// Check the poll in
		$row = new XPollPoll($this->database);
		$row->bind($p);
		$row->checkin();

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

