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

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Access\Group as Accessgroup;
use Hubzero\Access\Access;
use Request;
use Notify;
use Event;
use Route;
use Lang;
use App;

/**
 * Manage user Access Groups
 */
class Accessgroups extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.access', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * Display entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'sort' => 'lft',
			'sort_Dir' => 'ASC'
		);

		$entries = Accessgroup::all();

		/*$entries
			->including(['maps', function ($map){
				$map
					->select('*');
			}]);
			/*->select('a.*')
			->select('b.id', 'level', true)
			->from($entries->getTableName(), 'a')
			->joinRaw($entries->getTableName() . '` AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
			->group('a.id, a.title, a.lft, a.rgt');*/

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
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
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!$row)
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Accessgroup::oneOrNew($id);
		}

		$entries = Accessgroup::all();

		/*$entries
			->select('a.*')
			->select('b.id', 'level', true)
			->from($entries->getTableName(), 'a')
			->joinRaw($entries->getTableName() . '` AS b', 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
			->group('a.id, a.title, a.lft, a.rgt');*/

		if (!$row->isNew())
		{
			$entries->where('id', '!=', $row->get('id'));
		}

		$options = $entries
			->order('lft', 'asc')
			->rows();

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('options', $options)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming password blacklist edits
		$fields = Request::getVar('fields', array(), 'post');

		// Load the record
		$row = Accessgroup::oneOrNew($fields['id'])->set($fields);

		// Check the super admin permissions for group
		// We get the parent group permissions and then check the group permissions manually
		// We have to calculate the group permissions manually because we haven't saved the group yet
		$parentSuperAdmin = Access::checkGroup($fields['parent_id'], 'core.admin');

		// Get core.admin rules from the root asset
		$rules = Access::getAssetRules('root.1')->getData('core.admin');

		// Get the value for the current group (will be true (allowed), false (denied), or null (inherit)
		$groupSuperAdmin = $rules['core.admin']->allow($row->get('id'));

		// We only need to change the $groupSuperAdmin if the parent is true or false. Otherwise, the value set in the rule takes effect.
		if ($parentSuperAdmin === false)
		{
			// If parent is false (Denied), effective value will always be false
			$groupSuperAdmin = false;
		}
		elseif ($parentSuperAdmin === true)
		{
			// If parent is true (allowed), group is true unless explicitly set to false
			$groupSuperAdmin = ($groupSuperAdmin === false) ? false : true;
		}

		// Check for non-super admin trying to save with super admin group
		$iAmSuperAdmin = User::authorise('core.admin');

		if (!$iAmSuperAdmin && $groupSuperAdmin)
		{
			Notify::error(Lang::txt('JLIB_USER_ERROR_NOT_SUPERADMIN'));
			return $this->editTask($row);
		}

		// Check for super-admin changing self to be non-super-admin
		// First, are we a super admin>
		if ($iAmSuperAdmin)
		{
			// Next, are we a member of the current group?
			$myGroups = Access::getGroupsByUser(User::get('id'), false);

			if (in_array($fields['id'], $myGroups))
			{
				// Now, would we have super admin permissions without the current group?
				$otherGroups = array_diff($myGroups, array($fields['id']));
				$otherSuperAdmin = false;
				foreach ($otherGroups as $otherGroup)
				{
					$otherSuperAdmin = ($otherSuperAdmin) ? $otherSuperAdmin : Access::checkGroup($otherGroup, 'core.admin');
				}

				// If we would not otherwise have super admin permissions
				// and the current group does not have super admin permissions, throw an exception
				if (!$otherSuperAdmin && !$groupSuperAdmin)
				{
					Notify::error(Lang::txt('JLIB_USER_ERROR_CANNOT_DEMOTE_SELF'));
					return $this->editTask($row);
				}
			}
		}

		if ($this->getTask() == 'save2copy')
		{
			$row->set('id', null);
		}

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_SAVE_SUCCESS'));

		if ($this->getTask() == 'save2new')
		{
			$row = Accessgroup::blank();
		}

		// Fall through to edit form
		if (in_array($this->getTask(), array('apply', 'save2new', 'save2copy')))
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			$groups = Access::getGroupsByUser(User::get('id'));

			// Check if I am a Super Admin
			$iAmSuperAdmin = User::authorise('core.admin');

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				// do not allow to delete groups to which the current user belongs
				if (in_array($id, $groups))
				{
					Notify::warning(Lang::txt('COM_MEMBERS_DELETE_ERROR_INVALID_GROUP'));
					continue;
				}

				// Access checks.
				$allow = User::authorise('core.edit.state', 'com_users');

				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && Access::checkGroup($id, 'core.admin')) ? false : $allow;

				if (!$allow)
				{
					Notify::warning(Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
					continue;
				}

				$row = Accessgroup::oneOrFail($id);

				$data = $row->toArray();

				// Fire the onUserBeforeDeleteGroup event.
				Event::trigger('user.onUserBeforeDeleteGroup', array($data));

				// Remove the record
				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				// Trigger the onUserAfterDeleteGroup event.
				Event::trigger('user.onUserAfterDeleteGroup', array($data, true, $this->getError()));

				$i++;
			}
		}
		else // no rows were selected
		{
			Notify::warning(Lang::txt('COM_MEMBERS_ACCESSGROUP_DELETE_NO_ROW_SELECTED'));
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_ACCESSGROUP_DELETE_SUCCESS'));
		}

		$this->cancelTask();
	}

	/**
	 * Debug permissions
	 *
	 * @return  void
	 */
	public function debugTask()
	{
		include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'debug.php';

		// Get filters
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'lft'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'level_start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_level_start',
				'filter_level_start',
				0,
				'int'
			),
			'level_end' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_level_end',
				'filter_level_end',
				0,
				'int'
			),
			'component' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_component',
				'filter_component',
				''
			)
		);

		if ($filters['level_end'] > 0 && $filters['level_end'] < $filters['level_start'])
		{
			$filters['level_end'] = $filters['level_start'];
		}

		$id = Request::getInt('id', 0);

		// Load access group
		$accessgroup = Accessgroup::oneOrFail($id);

		// Select the required fields from the table.
		$entries = \Hubzero\Access\Asset::all();

		if ($filters['search'])
		{
			$entries->whereLike('name', $filters['search'], 1)
				->orWhereLike('title', $filters['search'], 1)
				->resetDepth();
		}

		if ($filters['level_start'] > 0)
		{
			$entries->where('level', '>=', $filters['level_start']);
		}
		if ($filters['level_end'] > 0)
		{
			$entries->where('level', '<=', $filters['level_end']);
		}

		// Filter the items over the component if set.
		if ($filters['component'])
		{
			$entries->whereEquals('name', $filters['component'], 1)
				->orWhereLike('name', $filters['component'], 1)
				->resetDepth();
		}

		$assets = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated()
			->rows();

		$actions = \Components\Members\Helpers\Debug::getActions($filters['component']);

		$data = $assets->raw();
		$assets->clear();

		foreach ($data as $key => $asset)
		{
			$checks = array();

			foreach ($actions as $action)
			{
				$name  = $action[0];
				$level = $action[1];

				// Check that we check this action for the level of the asset.
				if ($action[1] === null || $action[1] >= $asset->get('level'))
				{
					// We need to test this action.
					$checks[$name] = Access::checkGroup($id, $action[0], $asset->get('name'));
				}
				else
				{
					// We ignore this action.
					$checks[$name] = 'skip';
				}
			}

			$asset->set('checks', $checks);

			$assets->push($asset);
		}

		$levels     = \Components\Members\Helpers\Debug::getLevelsOptions();
		$components = \Components\Members\Helpers\Debug::getComponents();

		// Output the HTML
		$this->view
			->set('group', $accessgroup)
			->set('filters', $filters)
			->set('assets', $assets)
			->set('actions', $actions)
			->set('levels', $levels)
			->set('components', $components)
			->display();
	}
}
