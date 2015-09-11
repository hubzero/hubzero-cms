<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Tables\Section;
use Components\Forum\Tables\Category;
use Components\Forum\Tables\Post;
use Components\Forum\Admin\Models\AdminCategory;
use Components\Forum\Models\Manager;
use Exception;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use App;

/**
 * Controller class for forum categories
 */
class Categories extends AdminController
{
	/**
	 * Display all categories in a section
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Filters
		$this->view->filters = array(
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
			),
			'group' => Request::getState(
				$this->_option . '.' . $this->_controller . '.group',
				'group',
				-1,
				'int'
			),
			'section_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.section_id',
				'section_id',
				-1,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'scopeinfo' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scopeinfo',
				'scopeinfo',
				''
			)
		);
		if (strstr($this->view->filters['scopeinfo'], ':'))
		{
			$bits = explode(':', $this->view->filters['scopeinfo']);
			$this->view->filters['scope'] = $bits[0];
			$this->view->filters['scope_id'] = intval(end($bits));
		}
		else
		{
			$this->view->filters['scope'] = '';
			$this->view->filters['scope_id'] = -1;
		}

		$this->view->filters['admin'] = true;

		// Load the current section
		$this->view->section = new Section($this->database);
		if (!$this->view->filters['section_id'] || $this->view->filters['section_id'] <= 0)
		{
			// No section? Load a default blank section
			$this->view->section->loadDefault();
		}
		else
		{
			$this->view->section->load($this->view->filters['section_id']);
		}

		$this->view->sections = array();

		if ($this->view->filters['scopeinfo'])
		{
			$this->view->sections = $this->view->section->getRecords(array(
				'scope'    => $this->view->filters['scope'],
				'scope_id' => $this->view->filters['scope_id'],
				'sort'     => 'title',
				'sort_Dir' => 'ASC'
			));
		}

		$model = new Category($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->results = $model->getRecords($this->view->filters);

		$this->view->forum = new Manager($this->view->filters['scope'], $this->view->filters['scope_id']);

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$section = Request::getInt('section_id', 0);

		$this->view->section = new Section($this->database);
		$this->view->section->load($section);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			$row = new Category($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->id)
		{
			$this->view->row->created_by = User::get('id');
			$this->view->row->section_id = $section;
			$this->view->row->scope      = $this->view->section->scope;
			$this->view->row->scope_id   = $this->view->section->scope_id;
		}

		$this->view->sections = array();

		$sections = $this->view->section->getRecords();
		if ($sections)
		{
			foreach ($sections as $s)
			{
				$ky = $s->scope . ' (' . $s->scope_id . ')';
				if ($s->scope == 'site')
				{
					$ky = '[ site ]';
				}
				if (!isset($this->view->sections[$ky]))
				{
					$this->view->sections[$ky] = array();
				}
				$this->view->sections[$ky][] = $s;
				asort($this->view->sections[$ky]);
			}
		}
		else
		{
			$default = new Section($this->database);
			$default->loadDefault($this->view->section->scope, $this->view->section->scope_id);

			$this->view->sections[] = $default;
		}
		asort($this->view->sections);

		\User::setState('com_forum.edit.category.data', array(
			'id'       => $this->view->row->get('id'),
			'asset_id' => $this->view->row->get('asset_id')
		));
		$m = new AdminCategory();
		$this->view->form = $m->getForm();

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a category record and redirects to listing
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		\User::setState('com_forum.edit.category.data', null);

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Bind the rules.
		$data = Request::getVar('jform', array(), 'post');
		if (isset($data['rules']) && is_array($data['rules']))
		{
			$model = new AdminCategory();
			$form = $model->getForm($data, false);
			$validData = $model->validate($form, $data);

			$fields['rules'] = $validData['rules'];
		}

		// Initiate extended database class
		$model = new Category($this->database);
		if (!$model->bind($fields))
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		if (!$model->scope)
		{
			$section = new Section($this->database);
			$section->load($fields['section_id']);
			$model->scope    = $section->scope;
			$model->scope_id = $section->scope_id;
		}

		// Check content
		if (!$model->check())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Store new content
		if (!$model->store())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $fields['section_id'], false),
			Lang::txt('COM_FORUM_CATEGORY_SAVED')
		);
	}

	/**
	 * Deletes one or more records and redirects to listing
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$section = Request::getInt('section_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			// Instantiate some objects
			$category = new Category($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				// Remove the posts in this category
				$tModel = new Post($this->database);
				if (!$tModel->deleteByCategory($id))
				{
					throw new Exception($tModel->getError(), 500);
				}

				// Remove this category
				if (!$category->delete($id))
				{
					throw new Exception($category->getError(), 500);
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section, false),
			Lang::txt('COM_FORUM_CATEGORIES_DELETED')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  $state  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$section = Request::getInt('section_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? Lang::txt('COM_FORUM_UNPUBLISH') : Lang::txt('COM_FORUM_PUBLISH');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Category($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		// set message
		if ($state == 1)
		{
			$message = Lang::txt('COM_FORUM_ITEMS_PUBLISHED', count($ids));
		}
		else
		{
			$message = Lang::txt('COM_FORUM_ITEMS_UNPUBLISHED', count($ids));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section, false),
			$message
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$section = Request::getInt('section_id', 0);
		$state   = Request::getInt('access', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO_CHANGE_ACCESS'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Category($this->database);
			$row->load(intval($id));
			$row->access = $state;
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		// set message
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $section, false),
			Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', count($ids))
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$fields = Request::getVar('fields', array());

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $fields['section_id'], false)
		);
	}
}

