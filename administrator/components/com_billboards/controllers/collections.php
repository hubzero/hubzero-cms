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
		$this->view->rows = Collection::all()->paginated()->ordered();
		$this->view->display();
	}

	/**
	 * Create a new collection
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->view->task = 'edit';
		$this->editTask();
	}

	/**
	 * Edit a collection
	 *
	 * @param  object $collection
	 * @return void
	 */
	public function editTask($collection=null)
	{
		// Hide the menu, force users to save or cancel
		JRequest::setVar('hidemainmenu', 1);

		if (!isset($collection) || !is_object($collection))
		{
			// Incoming (expecting an array)
			$id = JRequest::getVar('id', array(0));
			if (!is_array($id))
			{
				$id = array($id);
			}
			$cid = $id[0];

			$collection = Collection::oneOrNew($cid);
		}

		// Display
		$this->view->row = $collection;
		$this->view->display();
	}

	/**
	 * Save a collection
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Create object
		$collection = Collection::oneOrNew(JRequest::getInt('id'))->set(array(
			'name' => JRequest::getVar('name')
		));

		if (!$collection->save())
		{
			// Something went wrong...return errors
			foreach ($collection->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($collection);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			JText::_('COM_BILLBOARDS_COLLECTION_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete a billboard collection
	 *
	 * @return void
	 */
	public function removeTask()
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
			$collection = Collection::oneOrFail($id);

			// Delete record
			$collection->destroy();
		}

		// Output messsage and redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
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
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}