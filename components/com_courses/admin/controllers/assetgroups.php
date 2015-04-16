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

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Exception;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'unit.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php');

/**
 * Courses controller class for managing membership and course info
 */
class Assetgroups extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$task = Request::getVar('task', '');
		$plugin = Request::getVar('plugin', '');
		if ($plugin && $task && $task != 'manage') //!isset($this->_taskMap[$task]))
		{
			Request::setVar('action', $task);
			Request::setVar('task', 'manage');
		}

		$this->registerTask('add', 'edit');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');

		parent::execute();
	}

	/**
	 * Displays a list of courses
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'unit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.unit',
				'unit',
				0
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'-1'
			),
			// Filters for returning results
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

		$this->view->unit = \CoursesModelUnit::getInstance($this->view->filters['unit']);
		if (!$this->view->unit->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=courses', false)
			);
			return;
		}
		$this->view->offering = \CoursesModelOffering::getInstance($this->view->unit->get('offering_id'));
		$this->view->course = \CoursesModelCourse::getInstance($this->view->offering->get('course_id'));

		$rows = $this->view->unit->assetgroups(null, $this->view->filters);

		// establish the hierarchy of the menu
		$children = array(
			0 => array()
		);

		$levellimit = ($this->view->filters['limit'] == 0) ? 500 : $this->view->filters['limit'];

		// first pass - collect children
		foreach ($rows as $v)
		{
			$children[0][] = $v;
			$children[$v->get('id')] = $v->children();
		}

		// second pass - get an indent list of the items
		$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));

		$this->view->total = count($list);

		$this->view->rows = array_slice($list, $this->view->filters['start'], $this->view->filters['limit']);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	public function treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->get('id');

				if ($type)
				{
					$pre    = '<span class="treenode">&#8970;</span>&nbsp;';
					$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				else
				{
					$pre    = '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				if ($v->get('parent') == 0)
				{
					$txt = '';
				}
				else
				{
					$txt = $pre;
				}
				$pt = $v->get('parent');

				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);
				$list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}

	/**
	 * Displays an edit form
	 *
	 * @return  void
	 */
	public function editTask($model=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : '';
			}

			$model = new \CoursesModelAssetgroup($id);
		}

		$this->view->row = $model;

		if (!$this->view->row->get('unit_id'))
		{
			$this->view->row->set('unit_id', Request::getInt('unit', 0));
		}

		$this->view->unit = \CoursesModelUnit::getInstance($this->view->row->get('unit_id'));

		$this->view->offering = \CoursesModelOffering::getInstance($this->view->unit->get('offering_id'));

		$rows = $this->view->unit->assetgroups();

		// establish the hierarchy of the menu
		$children = array(
			0 => array()
		);

		$levellimit = 500;

		// first pass - collect children
		foreach ($rows as $v)
		{
			$children[0][] = $v;
			$children[$v->get('id')] = $v->children();
		}

		// second pass - get an indent list of the items
		$this->view->assetgroups = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));

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
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		// Instantiate a Course object
		$model = new \CoursesModelAssetgroup($fields['id']);

		if (!$model->bind($fields))
		{
			$this->setError('failed bind');
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		$p = new \JRegistry('');
		$p->loadArray(Request::getVar('params', array(), 'post'));

		$model->set('params', $p->toString());

		if (!$model->store(true))
		{
			$this->setError('failed store' . $model->getError());
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . $model->get('unit_id'), false),
			Lang::txt('COM_COURSES_ASSETGROUP_SAVED')
		);
	}

	/**
	 * Copy an entry and all associated data
	 *
	 * @return	void
	 */
	public function copyTask()
	{
		// Incoming
		$id = Request::getVar('id', array());

		// Get the single ID we're working with
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false),
				Lang::txt('COM_COURSES_ERROR_NO_ID'),
				'error'
			);
			return;
		}

		$assetgroup = new \CoursesModelAssetgroup($id);
		if (!$assetgroup->copy())
		{
			// Redirect back to the courses page
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . $assetgroup->get('unit_id'), false),
				Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $assetgroup->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . $assetgroup->get('unit_id'), false),
			Lang::txt('COM_COURSES_ITEM_COPIED')
		);
	}

	/**
	 * Removes a course and all associated information
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the course page
				$model = new \CoursesModelAssetgroup($id);

				// Ensure we found the course info
				if (!$model->exists())
				{
					continue;
				}

				// Delete course
				if (!$model->delete())
				{
					throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false),
			Lang::txt('COM_COURSES_ITEMS_REMOVED', $num)
		);
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
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false),
				($state == 1 ? Lang::txt('COM_COURSES_SELECT_PUBLISH') : Lang::txt('COM_COURSES_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating a category
			$row = new \CoursesModelAssetgroup($id);
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_COURSES_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_COURSES_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_COURSES_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false),
			$message
		);
	}

	/**
	 * Reorder a plugin
	 *
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		$id = Request::getVar('id', array(0), 'post', 'array');
		\JArrayHelper::toInteger($id, array(0));

		$uid = $id[0];
		$inc = ($this->_task == 'orderup' ? -1 : 1);

		$row = new Tables\Assetgroup($this->database);
		$row->load($uid);
		$row->move($inc, 'unit_id=' . $this->database->Quote($row->unit_id) . ' AND parent=' . $this->database->Quote($row->parent));
		$row->reorder('unit_id=' . $this->database->Quote($row->unit_id) . ' AND parent=' . $this->database->Quote($row->parent));

		//$unit = \CoursesModelUnit::getInstance(Request::getInt('unit', 0));
		//$ags = $unit->assetgroups(null, array('parent' => $row->parent));

		if (($ags = $row->find(array('w' => array('parent' => $row->parent, 'unit_id' => $row->unit_id)))))
		{
			foreach ($ags as $ag)
			{
				$a = new \CoursesModelAssetgroup($ag);
				$a->store();
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false)
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&unit=' . Request::getInt('unit', 0), false)
		);
	}
}
