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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Primary controller for the Billboards component
 */
class BillboardsControllerCollections extends \Hubzero\Component\AdminController
{
	/**
	 * Browse billboards collections (collections are used to display multiple billboards via mod_billboards)
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.collections.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.collections.limitstart',
			'limitstart',
			0,
			'int'
		);

		// Get an object
		$collections = new BillboardsCollection($this->database);

		// Get a record count
		$this->view->total = $collections->getCount($this->view->filters);

		// Grab the results
		$this->view->rows = $collections->getRecords($this->view->filters);

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
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a billboards collection
	 *
	 * @return void
	 */
	public function editTask()
	{
		// Hide the menu, force users to save or cancel
		JRequest::setVar('hidemainmenu', 1);

		// Incoming (expecting an array)
		$id = JRequest::getVar('id', array(0));
		if (!is_array($id))
		{
			$id = array($id);
		}
		$cid = $id[0];

		// Initiate a class and load the info
		$this->view->row = new BillboardsCollection($this->database);
		$this->view->row->load($cid);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a billboard collection
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim all posted items (we don't need to allow HTML here)
		$collection = JRequest::getVar('collection', array(), 'post');
		$collection = array_map('trim', $collection);

		// Initiate class and bind posted items to database fields
		$row = new BillboardsCollection($this->database);
		if (!$row->bind($collection))
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_BILLBOARDS_COLLECTION_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete a billboard collection
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Loop through the selected collections to delete
		// @TODO: maybe we should warn people if trying to delete a collection with associated billboards?
		foreach ($ids as $id)
		{
			// Delete collection
			$collection = new BillboardsCollection($this->database);
			$collection->delete($id);
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::sprintf('COM_BILLBOARDS_COLLECTION_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Cancel out of editing a billboard collection (i.e. just redirect back to the collections view)
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		// Just redirect, no checkin necessary
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Build the select list for ordering of a specified Table
	 *
	 * @return $ordering
	 */
	protected function ordering(&$row, $id, $query, $neworder = 0)
	{
		$db = JFactory::getDBO();

		if ($id)
		{
			$order = JHTML::_('list.genericordering', $query);
			$ordering = JHTML::_('select.genericlist', $order, 'billboard[ordering]', 'class="inputbox" size="1"', 'value', 'text', intval($row->ordering));
		}
		else
		{
			if ($neworder)
			{
				$text = JText::_('descNewItemsFirst');
			}
			else
			{
				$text = JText::_('descNewItemsLast');
			}
			$ordering = '<input type="hidden" name="billboard[ordering]" value="' . $row->ordering . '" />' . $text;
		}

		return $ordering;
	}
}