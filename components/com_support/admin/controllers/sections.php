<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Support\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Support\Tables;

/**
 * Support controller class for managing ticket sections
 */
class Sections extends AdminController
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

		parent::execute();
	}

	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = \JFactory::getApplication();

		// Get paging variables
		$this->view->filters = array(
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.sections.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.sections.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$model = new Tables\Section($this->database);

		// Record count
		$this->view->total = $model->getCount($this->view->filters);

		// Fetch results
		$this->view->rows  = $model->getRecords($this->view->filters);

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
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($edit))
		{
			// Incoming
			$id = Request::getInt('id', 0);

			// Initiate database class and load info
			$row = new SupportSection($this->database);
			$row->load($id);

			// Set action
			if (!$row->id)
			{
				$row->section = '';
			}
		}

		$this->view->row = $row;

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Trim and addslashes all posted items
		$sec = Request::getVar('sec', array(), 'post');
		$sec = array_map('trim', $sec);

		// Initiate class and bind posted items to database fields
		$row = new Tables\Section($this->database);
		if (!$row->bind($sec))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->section = trim($row->section);

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('SECTION_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0));
		if (!is_array($ids))
		{
			$ids = array(0);
		}

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('SUPPORT_ERROR_SELECT_SECTION_TO_DELETE')
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Delete message
			$cat = new Tables\Section($this->database);
			$cat->delete(intval($id));
		}

		// Output messsage and redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('SECTION_SUCCESSFULLY_DELETED', count($ids))
		);
	}
}
