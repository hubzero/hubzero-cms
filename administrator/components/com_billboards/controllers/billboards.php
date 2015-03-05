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
class BillboardsControllerBillBoards extends \Hubzero\Component\AdminController
{
	/**
	 * Browse the list of billboards
	 *
	 * @return void
	 */
	public function displayTask()
	{
		$this->view->rows = Billboard::all()->paginated()->ordered()->rows();
		$this->view->display();
	}

	/**
	 * Create a billboard
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
	 * Edit a billboard
	 *
	 * @param  object $billboard
	 * @return void
	 */
	public function editTask($billboard=null)
	{
		// Hide the menu, force users to save or cancel
		JRequest::setVar('hidemainmenu', 1);

		if (!isset($billboard) || !is_object($billboard))
		{
			// Incoming - expecting an array
			$cid = JRequest::getVar('cid', array(0));
			if (!is_array($cid))
			{
				$cid = array($cid);
			}
			$uid = $cid[0];

			$billboard = Billboard::oneOrNew($uid);
		}

		// Fail if not checked out by current user
		if ($billboard->isCheckedOut())
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				JText::_('COM_BILLBOARDS_ERROR_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		// Are we editing an existing entry?
		if ($billboard->id)
		{
			// Yes, we should check it out first
			$billboard->checkout($this->juser->get('id'));
		}

		// Output the HTML
		$this->view->row = $billboard;
		$this->view->display();
	}

	/**
	 * Save a billboard
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming, make sure to allow HTML to pass through
		$data = JRequest::getVar('billboard', array(), 'post', 'array', JREQUEST_ALLOWHTML);

		// Create object
		$billboard = Billboard::oneOrNew($data['id'])->set($data);

		if (!$billboard->save())
		{
			// Something went wrong...return errors
			foreach ($billboard->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($billboard);
			return;
		}

		// Check in the billboard now that we've saved it
		$billboard->checkin();

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			JText::_('COM_BILLBOARDS_BILLBOARD_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Save the new order
	 *
	 * @return void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initialize variables
		$cid   = JRequest::getVar('cid', array(), 'post', 'array');
		$order = JRequest::getVar('order', array(), 'post', 'array');

		// Make sure we have something to work with
		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_('BILLBOARDS_ORDER_PLEASE_SELECT_ITEMS'));
			return;
		}

		// Update ordering values
		for ($i = 0; $i < count($cid); $i++)
		{
			$billboard = Billboard::oneOrFail($cid[$i]);
			if ($billboard->ordering != $order[$i])
			{
				$billboard->set('ordering', $order[$i]);
				if (!$billboard->save())
				{
					JError::raiseError(500, $billboard->getError());
					return;
				}
			}
		}

		// Clear the component's cache
		$cache = JFactory::getCache('com_billboards');
		$cache->clean();

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			JText::_('COM_BILLBOARDS_ORDER_SUCCESSFULLY_UPDATED')
		);
	}

	/**
	 * Delete a billboard
	 *
	 * @return void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('cid', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0)
		{
			// Loop through the array of ID's and delete
			foreach ($ids as $id)
			{
				$billboard = Billboard::oneOrFail($id);

				// Delete record
				if (!$billboard->destroy())
				{
					$this->setRedirect(
						JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						JText::_('COM_BILLBOARDS_ERROR_CANT_DELETE')
					);
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			JText::sprintf('COM_BILLBOARDS_BILLBOARD_SUCCESSFULLY_DELETED', count($ids))
		);
	}

	/**
	 * Publish billboards
	 *
	 * @return void
	 */
	public function publishTask()
	{
		$this->toggle(1);
	}

	/**
	 * Unpublish billboards
	 *
	 * @return void
	 */
	public function unpublishTask()
	{
		$this->toggle(0);
	}

	/**
	 * Cancels out of the billboard edit view, makes sure to check the billboard back in for other people to edit
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		// Incoming - we need an id so that we can check it back in
		$fields = JRequest::getVar('billboard', array(), 'post');

		// Check the billboard back in
		$billboard = Billboard::oneOrNew($fields['id']);
		$billboard->checkin();

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Toggle a billboard between published and unpublished.  We're looking for an array of ID's to publish/unpublish
	 *
	 * @param  $publish: 1 to publish and 0 for unpublish
	 * @return void
	 */
	protected function toggle($publish=1)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('cid', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the billboard
			$row = Billboard::oneOrFail($id);

			// Only alter items not checked out or checked out by 'me'
			if (!$row->isCheckedOut())
			{
				$row->set('published', $publish);
				if (!$row->save())
				{
					JError::raiseError(500, $row->getError());
					return;
				}
				// Check it back in
				$row->checkin();
			}
			else
			{
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					JText::_('COM_BILLBOARDS_ERROR_CHECKED_OUT'),
					'warning'
				);
				return;
			}
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}