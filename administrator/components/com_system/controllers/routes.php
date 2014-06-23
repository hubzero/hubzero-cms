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

require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'entry.php');

/**
 * System controller class for custom routes
 */
class SystemControllerRoutes extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of entries
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get Joomla configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['catid']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.catid',
			'catid',
			0,
			'int'
		);
		$this->view->filters['ViewModeId']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.viewmode',
			'viewmode',
			0,
			'int'
		);
		$this->view->filters['SortById']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortby',
			'sortby',
			0,
			'int'
		);

		// Determine the mode
		$this->view->is404mode = false;
		if ($this->view->filters['ViewModeId'] == 1)
		{
			$this->view->is404mode = true;
		}

		$lists = array();

		// Make the select list for the filter
		$viewmode = array();
		$viewmode[] = JHTML::_('select.option', '0', JText::_('COM_SYSTEM_ROUTES_VIEW_MODE_SEF'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '1', JText::_('COM_SYSTEM_ROUTES_VIEW_MODE_404'), 'value', 'text');
		$viewmode[] = JHTML::_('select.option', '2', JText::_('COM_SYSTEM_ROUTES_VIEW_MODE_REDIRECTS'), 'value', 'text');

		$this->view->lists['viewmode'] = JHTML::_('select.genericlist', $viewmode, 'viewmode', '', 'value', 'text', $this->view->filters['ViewModeId'], false, false);

		// Make the select list for the filter
		$orderby = array();
		$orderby[] = JHTML::_('select.option', '0', JText::_('COM_SYSTEM_ROUTES_SORT_BY_SEF_ASC'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '1', JText::_('COM_SYSTEM_ROUTES_SORT_BY_SEF_DESC'), 'value', 'text');
		if ($this->view->is404mode != true)
		{
			$orderby[] = JHTML::_('select.option', '2', JText::_('COM_SYSTEM_ROUTES_SORT_BY_REAL_ASC'), 'value', 'text');
			$orderby[] = JHTML::_('select.option', '3', JText::_('COM_SYSTEM_ROUTES_SORT_BY_REAL_DESC'), 'value', 'text');
		}
		$orderby[] = JHTML::_('select.option', '4', JText::_('COM_SYSTEM_ROUTES_SORT_BY_HITS_ASC'), 'value', 'text');
		$orderby[] = JHTML::_('select.option', '5', JText::_('COM_SYSTEM_ROUTES_SORT_BY_HITS_DESC'), 'value', 'text');

		$this->view->lists['sortby'] = JHTML::_('select.genericlist', $orderby, 'sortby', '', 'value', 'text', $this->view->filters['SortById'], false, false);

		// Instantiate a new SefEntry
		$s = new SefEntry($this->database);

		// Record count
		$this->view->total = $s->getCount($this->view->filters);

		// Get records
		$this->view->rows = $s->getRecords($this->view->filters);

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
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param      object $row SefEntry
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Load a tag object if one doesn't already exist
		if (!is_object($row))
		{
			// Incoming
			$ids = JRequest::getVar('id', array());
			if (!is_array($ids))
			{
				$ids = array();
			}

			$id = (!empty($ids)) ? $ids[0] : 0;

			$this->view->row = new SefEntry($this->database);
			$this->view->row->load($id);

			if (!$id)
			{
				// do stuff for new records
				$this->view->row->dateadd = JFactory::getDate()->toSql();
			}
		}
		else
		{
			$this->view->row = $row;
		}

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
	 * Cancel a task and redirect
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
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

		// Load the tag object and bind the incoming data to it
		$row = new SefEntry($this->database);
		if (!$row->bind($_POST))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SYSTEM_ROUTES_ITEM_SAVED')
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

		$ids = JRequest::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Make sure we have an ID
		if (empty($ids))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Load some needed objects
		$sef = new SefEntry($this->database);

		foreach ($ids as $id)
		{
			// Remove the SEF
			$sef->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SYSTEM_ROUTES_ITEM_REMOVED')
		);
	}
}

