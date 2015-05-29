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

namespace Components\Kb\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Kb\Models\Archive;
use Components\Kb\Models\Category;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for knowledge base categories
 */
class Categories extends AdminController
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
		$this->registerTask('unpublish', 'state');
		$this->registerTask('publish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'state' => -1,
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			),
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'empty' => 1,
			'section' => Request::getState(
				$this->_option . '.' . $this->_controller . '.id',
				'id',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'a.title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);
		if (!$this->view->filters['section'])
		{
			$this->view->filters['section'] = Request::getState(
				$this->_option . '.' . $this->_controller . '.cid',
				'cid',
				0,
				'int'
			);
		}

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->categories('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->categories('list', $this->view->filters);

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
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			$this->view->cid = Request::getInt('cid', 0);

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = new Category($id);
		}

		$this->view->row = $row;

		$archive = new Archive();

		// Get the sections
		$this->view->sections = $archive->categories('list', array('parent' => 0, 'empty' => 1));

		/*
		$m = new KbModelAdminCategory();
		$this->view->form = $m->getForm();
		*/

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a category
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$fields   = Request::getVar('fields', array(), 'post');
		$articles = null;

		// Initiate extended database class
		$row = new Category($fields['id']);

		// Did the parent category change?
		if ($row->exists())
		{
			if ($fields['section'] != $row->get('section'))
			{
				$articles = $row->articles('list', array('state' => -1));
			}
		}

		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			$this->editTask($row);
			return;
		}

		// Update articles if category parent has changed
		if ($articles)
		{
			$sec = $row->get('id');
			$cat = 0;
			if ($row->get('section'))
			{
				$sec = $row->get('section', 0);
				$cat = $row->get('id');
			}
			foreach ($articles as $article)
			{
				$article->set('section', $sec);
				$article->set('category', $cat);
				$article->store(false);
			}
		}

		if ($this->_task == 'apply')
		{
			Notify::success(Lang::txt('COM_KB_CATEGORY_SAVED'));
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option='.$this->_option . '&controller=' . $this->_controller . ($articles ? '&id=0' : ''), false),
			Lang::txt('COM_KB_CATEGORY_SAVED')
		);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming
		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Incoming
				$id = Request::getVar('id', array(0));
				if (is_array($id) && !empty($id))
				{
					$id = $id[0];
				}

				$this->view->id = $id;

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken() or exit('Invalid Token');

				// Incoming
				$id = Request::getInt('id', 0);

				// Make sure we have an ID to work with
				if (!$id)
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						Lang::txt('COM_KB_NO_ID'),
						'error'
					);
					return;
				}

				$msg = null;
				$typ = null;

				// Delete the category
				$category = new Category($id);

				// Check if we're deleting collection and all FAQs or just the collection page
				$category->set('delete_action', Request::getVar('action', 'removefaqs'));
				if (!$category->delete())
				{
					$msg = $category->getError();
					$typ = 'error';
				}

				// Set the redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					$msg,
					$typ
				);
			break;
		}
	}

	/**
	 * Set the state of an entry
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state == 1 ? Lang::txt('COM_KB_SELECT_PUBLISH') : Lang::txt('COM_KB_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new Category(intval($id));
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '1':
				$message = Lang::txt('COM_KB_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_KB_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&id=' . $cid : ''), false),
			$message
		);
	}
}

