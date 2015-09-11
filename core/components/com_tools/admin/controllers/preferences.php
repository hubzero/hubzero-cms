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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Tables;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use Html;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'preferences.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclass.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'sessionclassgroup.php');

/**
 * Manage member quotas
 */
class Preferences extends AdminController
{
	/**
	 * Display member quotas
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'search_field' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search_field',
				'search_field',
				'name'
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'user_id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'class_alias' => Request::getState(
				$this->_option . '.' . $this->_controller . '.class_alias',
				'class_alias',
				''
			),
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

		$obj = new Tables\Preferences($this->database);

		// Get a record count
		$this->view->total = $obj->find('count', $this->view->filters);
		$this->view->rows  = $obj->find('list', $this->view->filters);

		$classes = new Tables\SessionClass($this->database);
		$this->view->classes = $classes->find('list');
		$this->view->config  = $this->config;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new quota class
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
	}

	/**
	 * Edit a member quota
	 *
	 * @param   integer  $id  ID of user to edit
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = new Tables\Preferences($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Build classes select option
		$quotaClass = new Tables\SessionClass($this->database);
		$classes    = $quotaClass->find('list');
		$selected   = '';
		$options[]  = Html::select('option', '0', Lang::txt('COM_TOOLS_USER_PREFS_CUSTOM'), 'value', 'text');

		foreach ($classes as $class)
		{
			$options[] = Html::select('option', $class->id, $class->alias, 'value', 'text');
			if ($class->id == $this->view->row->class_id)
			{
				$selected = $class->id;
			}
		}
		$this->view->classes = Html::select('genericlist', $options, 'fields[class_id]', '', 'value', 'text', $selected, 'class_id', false, false);

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
	 * Apply changes to a user quota
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		// Save without redirect
		$this->saveTask();
	}

	/**
	 * Save user quota
	 *
	 * @param   integer  $redirect  Whether or not to redirect after save
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming fields
		$fields = Request::getVar('fields', array(), 'post');

		// Load the profile
		$row = new Tables\Preferences($this->database);

		if ($fields['class_id'])
		{
			$class = new Tables\SessionClass($this->database);
			$class->load($fields['class_id']);

			if ($class->id)
			{
				$fields['jobs']  = $class->jobs;
			}
		}

		$user = \User::getInstance($fields['user_id']);

		if (!is_object($user) || !$user->get('id'))
		{
			Notify::error(Lang::txt('COM_TOOLS_USER_PREFS_USER_NOT_FOUND'));
			return $this->editTask($row);
		}

		$fields['user_id'] = $user->get('id');

		// Try to save
		if (!$row->save($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TOOLS_USER_PREFS_SAVE_SUCCESSFUL'));

		// Redirect
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
	 * Restore member to default quota class
	 *
	 * @return  void
	 */
	public function restoreDefaultTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and restore
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = new Tables\Preferences($this->database);
				$row->load($id);

				$class = new Tables\SessionClass($this->database);
				$class->load(array('alias' => 'default'));

				if (!$class->id)
				{
					// Output message and redirect
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						Lang::txt('COM_TOOLS_USER_PREFS_MISSING_DEFAULT_CLASS'),
						'error'
					);
					return;
				}

				$row->set('class_id', $class->id);
				$row->set('jobs', $class->jobs);
				$row->store();
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_TOOLS_USER_PREFS_DELETE_NO_ROWS'),
				'warning'
			);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_USER_PREFS_SET_TO_DEFAULT')
		);
	}

	/**
	 * Get class values
	 *
	 * @return  void
	 */
	public function getClassValuesTask()
	{
		$class_id = Request::getInt('class_id');

		$class = new Tables\SessionClass($this->database);
		$class->load($class_id);

		$return = array(
			'jobs'  => $class->jobs,
		);

		echo json_encode($return);
		exit();
	}
}