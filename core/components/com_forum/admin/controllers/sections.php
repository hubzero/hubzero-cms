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
use Components\Forum\Admin\Models\AdminSection;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for forum sections
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display all sections
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
				'-1',
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				'-1',
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
			$filters['scope'] = '';
			$filters['scope_id'] = -1;
		}

		$entries = Section::all()
			->including(['categories', function ($category){
				$category
					->select('id')
					->select('section_id');
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
			->set('scopes', $forum->scopes())
			->display();
	}

	/**
	 * Displays a form for editing or creating entries
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			// load infor from database
			$row = Section::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('scope', 'site');
			$row->set('scope_id', 0);
		}

		User::setState('com_forum.edit.section.data', array(
			'id'       => $row->get('id'),
			'asset_id' => $row->get('asset_id')
		));

		$m = new AdminSection();
		$form = $m->getForm();

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('form', $form)
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves an entry
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

		User::setState('com_forum.edit.section.data', null);

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$section = Section::oneOrNew($fields['id'])->set($fields);

		// Bind the rules.
		$data = Request::getVar('jform', array(), 'post');
		if (isset($data['rules']) && is_array($data['rules']))
		{
			$model = new AdminSection();
			$form      = $model->getForm($data, false);
			$validData = $model->validate($form, $data);

			$section->assetRules = new \JAccessRules($validData['rules']);
		}

		if (!$section->save())
		{
			Notify::error($section->getError());
			return $this->editTask($section);
		}

		Notify::success(Lang::txt('COM_FORUM_SECTION_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($section);
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
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Loop through each ID
		foreach ($ids as $id)
		{
			$section = Section::oneOrFail(intval($id));

			// Remove this section
			if (!$section->destroy())
			{
				Notify::error($section->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_SECTIONS_DELETED'));
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
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$state = ($this->getTask() == 'publish' ? Section::STATE_PUBLISHED : Section::STATE_UNPUBLISHED);

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == Section::STATE_PUBLISHED) ? Lang::txt('COM_FORUM_PUBLISH') : Lang::txt('COM_FORUM_UNPUBLISH');

			Notify::warning(Lang::txt('COM_FORUM_SELECT_ENTRY_TO', $action));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = Section::oneOrFail(intval($id));
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
			if ($state == Section::STATE_PUBLISHED)
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
	 * Sets the access of one or more entries
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
		$state = Request::getInt('access', 0);
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
			$row = Section::oneOrFail(intval($id));
			$row->set('access', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_FORUM_ITEMS_ACCESS_CHANGED', $i));
		}

		$this->cancelTask();
	}
}
