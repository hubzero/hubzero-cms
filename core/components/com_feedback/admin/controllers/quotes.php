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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Feedback\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Feedback\Tables\Quote;
use Hubzero\User\Profile;
use Filesystem;
use Request;
use Route;
use Lang;
use Date;
use App;

/**
 * Feedback controller class for quotes
 */
class Quotes extends AdminController
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
	 * Display a list of quotes
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (Request::getMethod() == 'POST')
		{
			// Check for request forgeries
			Request::checkToken();
		}

		// Incoming
		$this->view->filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.sortby',
				'filter_order',
				'date'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			// Get paging variables
			'start'  => Request::getState(
				$this->_option . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'limit'  => Request::getState(
				$this->_option . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			)
		);

		$obj = new Quote($this->database);

		// Get a record count
		$this->view->total = $obj->find('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->find('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (Request::getMethod() == 'POST')
		{
			// Check for request forgeries
			Request::checkToken();
		}

		if (!is_object($row))
		{
			// Incoming ID
			$id = Request::getVar('id', array(0));
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$row = new Quote($this->database);
			$row->load($id);
		}

		$this->view->row = $row;
		$this->view->id  = $row->id;

		$this->view->pictures = array();
		$this->view->path = $row->filespace() . DS;
		$path = $this->view->path . ($id ? $id . DS : '');
		if (is_dir($path))
		{
			$pictures = scandir($path);
			array_shift($pictures);
			array_shift($pictures);
			$this->view->pictures = $pictures;
		}

		$username = trim(Request::getVar('username', ''));
		if ($username)
		{
			$profile = new Profile();
			$profile->load($username);

			$this->view->row->fullname = $profile->get('name');
			$this->view->row->org      = $profile->get('organization');
			$this->view->row->user_id  = $profile->get('uidNumber');
		}

		if (!$this->view->row->id)
		{
			$this->view->row->date = Date::toSql();
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

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
		Request::checkToken();

		// Initiate class and bind posted items to database fields
		$row = new Quote($this->database);
		$row->notable_quote = Request::getInt('notable_quotes', 0);

		$path = $row->filespace() . DS . $row->id;

		$existingPictures = is_dir($path) ? scandir($path . DS) : array();
		array_shift($existingPictures);
		array_shift($existingPictures);

		foreach ($existingPictures as $existingPicture)
		{
			if (!isset($_POST['existingPictures']) or in_array($existingPicture, $_POST['existingPictures']) === false)
			{
				if (!Filesystem::delete($path . DS . $existingPicture))
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
					);
					return;
				}
			}

			if (count(scandir($path)) === 2)
			{
				rmdir($path);
			}
		}

		$files = $_FILES;

		if ($files['files']['name'][0] !== '')
		{
			if (!is_dir($path))
			{
				Filesystem::makeDirectory($path);
			}

			foreach ($files['files']['name'] as $fileIndex => $file)
			{
				Filesystem::upload($files['files']['tmp_name'][$fileIndex], $path . DS . $files['files']['name'][$fileIndex]);
			}
		}

		if (!$row->bind($_POST))
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Check new content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_FEEDBACK_QUOTE_SAVED', $row->fullname)
		);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (!count($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_FEEDBACK_SELECT_QUOTE_TO_DELETE'),
				'error'
			);
			return;
		}

		$row = new Quote($this->database);

		foreach ($ids as $id)
		{
			// Delete the quote
			$row->delete(intval($id));
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_FEEDBACK_REMOVED')
		);
	}
}

