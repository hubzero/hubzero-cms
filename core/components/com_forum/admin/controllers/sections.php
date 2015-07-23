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

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Tables;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Admin\Models\AdminSection;
use Exception;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use App;

/**
 * Controller class for forum sections
 */
class Sections extends AdminController
{
	/**
	 * Display all sections
	 *
	 * @return  void
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

		$model = new Manager($this->view->filters['scope'], $this->view->filters['scope_id']);

		// Get a record count
		$this->view->total = $model->sections('count', $this->view->filters);

		// Get records
		$this->view->results = $model->sections('list', $this->view->filters);

		$this->view->forum = $model;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			// load infor from database
			$row = new Section($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', User::get('id'));
		}

		\User::setState('com_forum.edit.section.data', array(
			'id'       => $this->view->row->get('id'),
			'asset_id' => $this->view->row->get('asset_id')
		));
		$m = new AdminSection();
		$this->view->form = $m->getForm();

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and show the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask();
	}

	/**
	 * Saves an entry and redirects to listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		\User::setState('com_forum.edit.section.data', null);

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Bind the rules.
		$data = Request::getVar('jform', array(), 'post');
		if (isset($data['rules']) && is_array($data['rules']))
		{
			$model = new AdminSection();
			$form = $model->getForm($data, false);
			$validData = $model->validate($form, $data);

			$fields['rules'] = $validData['rules'];
		}

		// Initiate extended database class
		$row = new Section($fields['id']);
		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_FORUM_SECTION_SAVED'));

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
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
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);

				$section = new Tables\Section($this->database);
				$section->load($id);

				// Get the categories in this section
				$cModel = new Tables\Category($this->database);
				$categories = $cModel->getRecords(array('section_id' => $section->id));

				// Loop through each category
				foreach ($categories as $category)
				{
					// Remove the posts in this category
					$tModel = new Tables\Post($this->database);
					if (!$tModel->deleteByCategory($category->id))
					{
						throw new Exception($tModel->getError(), 500);
					}
					// Remove this category
					if (!$cModel->delete($category->id))
					{
						throw new Exception($cModel->getError(), 500);
					}
				}

				// Remove this section
				if (!$section->delete())
				{
					throw new Exception($section->getError(), 500);
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . Request::getInt('section_id', 0), false),
			Lang::txt('COM_FORUM_SECTIONS_DELETED')
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
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? Lang::txt('COM_FORUM_UNPUBLISH') : Lang::txt('COM_FORUM_PUBLISH');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Section(intval($id));
			if (!$row->exists())
			{
				continue;
			}

			$row->set('state', $state);
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
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
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
		$state = Request::getInt('access', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_FORUM_SELECT_ENTRY_TO_CHANGE_ACCESS'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = new Section(intval($id));
			if (!$row->exists())
			{
				continue;
			}

			$row->set('access', $state);
			if (!$row->store())
			{
				throw new Exception($row->getError(), 500);
			}
		}

		// set message
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', count($ids))
		);
	}
}

