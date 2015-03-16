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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Wishlist\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Wishlist\Tables\Wishlist;

/**
 * Cotnroller class for wish lists
 */
class Lists extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessregistered', 'access');
		$this->registerTask('accessspecial', 'access');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = \JFactory::getConfig();
		$app = \JFactory::getApplication();

		// Get filters
		$this->view->filters = array(
			'search' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'category' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.category',
				'category',
				''
			),
			// Get sorting variables
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$obj = new Wishlist($this->database);

		// Get record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		\JRequest::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = \JRequest::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = new Wishlist($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		/*
		$m = new Model\Adminlist();
		$this->view->form = $m->getForm();
		*/

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->juser = $this->juser;

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = \JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new Wishlist($this->database);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}
		$row->state  = (isset($fields['state']))  ? 1 : 0;
		$row->public = (isset($fields['public'])) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option='.$this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_WISHLIST_LIST_SAVED')
		);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = \JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Make sure we have an ID to work with
		if (!count($ids))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WISHLIST_NO_ID'),
				'error'
			);
			return;
		}

		// Create a Wishlist object
		$wishlist = new Wishlist($this->database);

		$i = 0;
		foreach ($ids as $id)
		{
			// Delete the list
			if (!$wishlist->delete($id))
			{
				$this->setError($wishlist->getError());
			}
			else
			{
				$i++;
			}
		}

		if ($i)
		{
			$this->setMessage(Lang::txt('COM_WISHLIST_ITEMS_REMOVED', $i));
		}

		// Set the redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Set the access level of an entry
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = \JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_WISHLIST_NO_ID'),
				'error'
			);
			return;
		}

		switch ($this->getTask())
		{
			case 'accesspublic':     $access = 0; break;
			case 'accessregistered': $access = 1; break;
			case 'accessspecial':    $access = 2; break;
		}

		// Load the article
		$row = new Wishlist($this->database);
		$row->load($id);
		$row->public = $access;

		// Check and store the changes
		if (!$row->check())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		if (!$row->store())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		$state = $this->getTask() == 'publish' ? 1 : 0;

		// Incoming
		$cid = \JRequest::getInt('cid', 0);
		$ids = \JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state == 1 ? Lang::txt('COM_WISHLIST_SELECT_PUBLISH') : Lang::txt('COM_WISHLIST_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Wishlist($this->database);
			$row->load($id);
			$row->state = $state;
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_WISHLIST_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_WISHLIST_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_WISHLIST_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''), false),
			$message
		);
	}
}

