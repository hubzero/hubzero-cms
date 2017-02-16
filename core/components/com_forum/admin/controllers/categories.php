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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
use Components\Forum\Admin\Models\AdminCategory;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for forum categories
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display all categories in a section
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Filters
		$filters = array(
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
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
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'scopeinfo' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scopeinfo',
				'scopeinfo',
				''
			)
		);
		if (strstr($filters['scopeinfo'], ':'))
		{
			$bits = explode(':', $filters['scopeinfo']);
			$filters['scope']    = $bits[0];
			$filters['scope_id'] = intval(end($bits));
		}
		else
		{
			$filters['scope']    = '';
			$filters['scope_id'] = -1;
		}

		$filters['admin'] = true;

		// Load the current section
		if (!$filters['section_id'] || $filters['section_id'] <= 0)
		{
			// No section? Load a default blank section
			$section = Section::blank();
		}
		else
		{
			$section = Section::oneOrFail($filters['section_id']);
			$filters['scope']    = $section->get('scope');
			$filters['scope_id'] = $section->get('scope_id');
			$filters['scopeinfo'] = $filters['scope'] . ':' . $filters['scope_id'];
		}

		$sections = array();

		if ($filters['scope_id'] >= 0)
		{
			$sections = Section::all()
				->whereEquals('scope' , $filters['scope'])
				->whereEquals('scope_id', $filters['scope_id'])
				->ordered('title', 'ASC')
				->rows();
		}

		$entries = Category::all()
			->including(['posts', function ($post){
				$post
					->select('id')
					->select('category_id');
			}]);

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['scope'])
		{
			$entries->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'] >= 0)
		{
			$entries->whereEquals('scope_id', (int)$filters['scope_id']);
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'] >= 0)
		{
			$entries->whereEquals('access', (int)$filters['access']);
		}

		if ($filters['section_id'] > 0)
		{
			$entries->whereEquals('section_id', (int)$filters['section_id']);
		}

		// Get records
		$rows = $entries
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$forum = new Manager($filters['scope'], $filters['scope_id']);

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('section', $section)
			->set('sections', $sections)
			->set('scopes', $forum->scopes())
			->display();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @param   object  $category
	 * @return  void
	 */
	public function editTask($category=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$section = Section::oneOrNew(Request::getInt('section_id', 0));

		if (!is_object($category))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			$category = Category::oneOrNew($id);
		}

		if ($category->isNew())
		{
			$category->set('created_by', User::get('id'));
			$category->set('section_id', $section->get('id'));
			$category->set('scope', $section->get('scope'));
			$category->set('scope_id', $section->get('scope_id'));
		}

		$data = Section::all()
			->ordered()
			->rows();

		$sections = array();

		foreach ($data as $s)
		{
			$ky = $s->scope . ' (' . $s->scope_id . ')';
			if ($s->scope == 'site')
			{
				$ky = '[ site ]';
			}
			if (!isset($sections[$ky]))
			{
				$sections[$ky] = array();
			}
			$sections[$ky][] = $s;
			asort($sections[$ky]);
		}

		User::setState('com_forum.edit.category.data', array(
			'id'       => $category->get('id'),
			'asset_id' => $category->get('asset_id')
		));
		$m = new AdminCategory();

		// Output the HTML
		$this->view
			->set('row', $category)
			->set('section', $section)
			->set('sections', $sections)
			->set('form', $m->getForm())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a category record and redirects to listing
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

		User::setState('com_forum.edit.category.data', null);

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$category = Category::oneOrNew($fields['id'])->set($fields);

		// Bind the rules.
		$data = Request::getVar('jform', array(), 'post');
		if (isset($data['rules']) && is_array($data['rules']))
		{
			$model = new AdminCategory();
			$form      = $model->getForm($data, false);
			$validData = $model->validate($form, $data);

			$category->assetRules = new \Hubzero\Access\Rules($validData['rules']);
		}

		if (!$category->get('scope'))
		{
			$section = Section::oneOrFail($fields['section_id']);

			$category->set('scope', $section->get('scope'));
			$category->set('scope_id', $section->get('scope_id'));
		}

		// Store new content
		if (!$category->save())
		{
			Notify::error($category->getError());
			return $this->editTask($category);
		}

		Notify::success(Lang::txt('COM_FORUM_CATEGORY_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($category);
		}

		// Redirect
		$this->cancelTask();
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$section = Request::getInt('section_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Loop through each ID
		foreach ($ids as $id)
		{
			// Remove this category
			$category = Category::oneOrFail(intval($id));

			if (!$category->destroy())
			{
				Notify::error($category->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_CATEGORIES_DELETED'));
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$section = Request::getInt('section_id', 0);

		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$state = ($this->getTask() == 'publish' ? Category::STATE_PUBLISHED : Category::STATE_UNPUBLISHED);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == Category::STATE_PUBLISHED) ? Lang::txt('COM_FORUM_PUBLISH') : Lang::txt('COM_FORUM_UNPUBLISH');

			Notify::warning(Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = Category::oneOrFail(intval($id));
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// set message
		if ($i)
		{
			if ($state == Category::STATE_PUBLISHED)
			{
				$message = Lang::txt('COM_FORUM_ITEMS_PUBLISHED', $i);
			}
			else
			{
				$message = Lang::txt('COM_FORUM_ITEMS_UNPUBLISHED', $i);
			}

			Notify::success($message);
		}

		$this->cancelTask();
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$state   = Request::getInt('access', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_FORUM_SELECT_ENTRY_TO_CHANGE_ACCESS'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = Category::oneOrFail(intval($id));
			$row->set('access', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// set message
		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', $i));
		}

		$this->cancelTask();
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$fields = Request::getVar('fields', array('section_id' => 0));
		if (!isset($fields['section_id']) || !$fields['section_id'])
		{
			$fields['section_id'] = Request::getInt('section_id', 0);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section_id=' . $fields['section_id'], false)
		);
	}
}
