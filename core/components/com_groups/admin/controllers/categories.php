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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin\Controllers;

use Hubzero\User\Group;
use Hubzero\Component\AdminController;
use Components\Groups\Models\Orm\Page\Category;
use Components\Groups\Models\Log;
use Request;
use Notify;
use Route;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . '/models/orm/page/category.php');

/**
 * Groups controller class for page categories
 */
class Categories extends AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		// Incoming
		$gid = Request::getVar('gid', '');

		// Ensure we have a group ID
		if (!$gid)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=manage', false),
				Lang::txt('COM_GROUPS_MISSING_ID'),
				'error'
			);
		}

		$this->group = Group::getInstance($gid);

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display Page Categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get records
		$entries = Category::all()
			->whereEquals('gidNumber', $this->group->get('gidNumber'));

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('group', $this->group)
			->display();
	}

	/**
	 * Edit Page Category
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the entry
			$row = Category::oneOrNew($id);
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->set('category', $row)
			->set('group', $this->group)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save Page Category
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get request vars
		$fields = Request::getVar('category', array(), 'post');

		// Add group id to category
		$fields['gidNumber'] = $this->group->get('gidNumber');

		// Load category object
		$row = Category::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Log change
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => (isset($fields['id']) && $fields['id']) ? 'group_pagecategory_updated' : 'group_pagecategory_created',
			'comments'  => array(
				'id'    => $row->get('id'),
				'title' => $row->get('title'),
				'color' => $row->get('color')
			)
		));

		// Notify user
		Notify::success(Lang::txt('COM_GROUPS_PAGES_CATEGORY_SAVED'));

		// Fall back to the edit form?
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Delete Page Category
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get request vars
		$ids = Request::getVar('id', array());
		$deleted = array();

		// Delete each category
		foreach ($ids as $categoryid)
		{
			// Load category object
			$category = Category::oneOrFail($categoryid);

			// Make sure this is our groups cat
			if ($category->get('gidNumber') != $this->group->get('gidNumber'))
			{
				Notify::error(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETE_FAILED'));
				continue;
			}

			// Delete row
			if (!$category->destroy())
			{
				Notify::error($category->getError());
				continue;
			}

			$deleted[] = $category->get('id');
		}

		if (count($deleted))
		{
			Notify::success(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETE_SUCCESS'));

			// Log change
			Log::log(array(
				'gidNumber' => $this->group->get('gidNumber'),
				'action'    => 'group_pagecategory_deleted',
				'comments'  => $deleted
			));
		}

		$this->cancelTask();
	}

	/**
	 * Cancel a group page task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . Request::getVar('gid', ''), false)
		);
	}

	/**
	 * Manage group
	 *
	 * @return  void
	 */
	public function manageTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=manage&task=edit&id=' . Request::getVar('gid', ''), false)
		);
	}
}