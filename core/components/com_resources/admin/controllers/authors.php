<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Author;
use Components\Resources\Models\Author\Role;
use Hubzero\Component\AdminController;
use Request;
use Route;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'author.php');

/**
 * Manage resource authors
 */
class Authors extends AdminController
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
	 * List resource authors
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get records
		$model = Author::all();

		if ($filters['search'])
		{
			$model->whereLike('name', strtolower($filters['search']));
		}

		$rows = $model
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   array  $rows
	 * @return  void
	 */
	public function editTask($rows=null)
	{
		Request::setVar('hidemainmenu', 1);

		require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'author' . DS . 'role.php');

		$authorid = 0;

		if (!is_array($rows))
		{
			// Incoming
			$authorid = Request::getVar('id', array(0));
			if (is_array($authorid))
			{
				$authorid = (!empty($authorid) ? $authorid[0] : 0);
			}

			$rows = Author::all()
				->whereEquals('authorid', $authorid)
				->rows();
		}

		$roles = Role::all()
			->ordered()
			->rows();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('authorid', $authorid)
			->set('roles', $roles)
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

		// Incoming
		$fields   = Request::getVar('fields', array(), 'post');
		$authorid = Request::getVar('authorid', 0);
		$id       = Request::getVar('id', 0);

		if (!$authorid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		$rows = array();

		if (is_array($fields))
		{
			foreach ($fields as $fieldset)
			{
				$fieldset['authorid'] = $authorid;

				$row = Role::oneOrNew($fieldset['id'])->set($fieldset);

				if (!$row->save())
				{
					$this->setError($row->getError());
				}

				$rows[] = $row;
			}
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($rows);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}
