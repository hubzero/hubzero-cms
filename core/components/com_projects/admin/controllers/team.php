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

namespace Components\Projects\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Projects\Models\Project;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * Projects controller class for managing membership
 */
class Team extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!User::authorize('core.manage', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
		}

		parent::execute();
	}

	/**
	 * Displays a list of members
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'project' => Request::getState(
				$this->_option . '.' . $this->_controller . '.project',
				'project',
				''
			)
		);

		// Ensure we have a group ID
		if (!$filters['project'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error'
			);
		}

		// Load the group page
		$model = new Project($filters['project']);

		if (!$model->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_PROJECTS_NOTICE_ID_NOT_FOUND'),
				'error'
			);
		}

		// Sorting options
		$filters['sortby']         = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'name'
		));
		$filters['sortdir']     = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		// Filters for returning results
		$filters['limit']  = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$filters['start']  = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		if (!in_array($filters['sortdir'], array('ASC', 'DESC')))
		{
			$filters['sortdir'] = 'ASC';
		}
		if (!in_array($filters['sortby'], array('group', 'added', 'date', 'role', 'name', 'status')))
		{
			$filters['sortby'] = 'name';
		}

		$total = count($model->team());

		if ($filters['start'] > $total)
		{
			$filters['start'] = 0;
		}

		// In case limit has been changed, adjust limitstart accordingly
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		$team  = $model->team($filters, true);

		// Get managers count
		$managers_count = count($model->table('Owner')->getIds($model->get('id'), $role = 1));

		// Get count of project groups
		$groups = $model->table('Owner')->getProjectGroups($model->get('id'));
		$count_groups = $groups ? count($groups) : 0;

		// Output the HTML
		$this->view
			->set('total', $total)
			->set('filters', $filters)
			->set('project', $model)
			->set('rows', $team)
			->set('managers_count', $managers_count)
			->set('count_groups', $count_groups)
			->display();
	}

	/**
	 * Show a form to add new users
	 *
	 * @return	void
	 */
	public function newTask()
	{
		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Load the project
		$project_id = Request::getInt('project', 0);

		$model = new Project($project_id);

		// Output the HTML
		$this->view
			->set('model', $model)
			->display();
	}

	/**
	 * Add user(s) to a group members list (invitee, applicant, member, manager)
	 *
	 * @return  void
	 */
	public function addusersTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$project_id = Request::getInt('project', 0);

		// Load the group page
		$this->model = new Project($project_id);

		// Incoming
		$newm    = Request::getVar('newmember', '', 'post');
		if (is_string($newm))
		{
			$newm = trim(urldecode($newm));
			$newm = preg_split("/[,;]/", $newm);
			$newm = array_filter($newm);
		}
		$newm    = (array)$newm;
		$groups  = urldecode(trim(Request::getVar('newgroup', '')));
		$role    = Request::getInt('role', 0);

		// Result collectors
		$m_added = 0; // count of individual members added
		$g_added = 0; // count of members from new group
		$uids    = array(); // ids/emails of added people
		$invalid = array(); // collector for invalid names

		// Setup stage?
		$setup = $this->model->inSetup();

		// Get owner class
		$objO = $this->model->table('Owner');

		// Do we have new authors?
		if (!empty($newm))
		{
			for ($i=0, $n=count($newm); $i < $n; $i++)
			{
				$cid = strtolower(trim($newm[$i]));

				if (!$cid)
				{
					continue;
				}

				$validUser = User::getInstance($cid);
				$uid = $validUser->get('id');

				if (!$uid)
				{
					$invalid[] = $cid;
					continue;
				}

				// Save new author
				$native = ($this->model->access('owner')) ? 1 : 0;

				if ($objO->saveOwners($this->model->get('id'), User::get('id'), $uid, 0, $role, $status = 1, $native))
				{
					$uids[] = $uid;
				}
			}
		}

		if ($groups)
		{
			// Save new authors from group
			$g_added = $objO->saveOwners($this->model->get('id'), User::get('id'), 0, $groups, $role, $status = 1, $native = 0);

			if ($objO->getError())
			{
				Notify::error($objO->getError());
			}

			if ($g_added)
			{
				$uids = array_merge($uids, $g_added);
			}
		}

		if (count($uids) > 0)
		{
			// Sync with system group
			$objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));
		}

		if (!Request::getInt('no_html', 0))
		{
			$this->cancelTask();
		}
	}

	/**
	 * Add user(s) to a group members list (invitee, applicant, member, manager)
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$project_id = Request::getInt('project', 0);

		if (!$project_id)
		{
			return $this->cancelTask();
		}

		// Load the group page
		$model = new Project($project_id);

		//$ids   = Request::getVar('id', array());
		$roles = Request::getVar('role', array());

		// Instantiate project owner
		$objO = $model->table('Owner');

		foreach ($roles as $id => $role)
		{
			$checked = array($id);
			//$role = (isset($roles[$id]) ? $roles[$id] : 0);

			// Changing role(s)
			$left = $checked;

			if ($role == 0)
			{
				// Get all managers
				$all = $objO->getIds($model->get('id'), 1);

				$remaining = array_diff($all, $checked);

				if (!$remaining && count($all) > 0)
				{
					$left = array_diff($checked, array($all[0])); // leave one manager

					Notify::error(Lang::txt('PLG_PROJECTS_TEAM_OWNERS_REASSIGN_NOMANAGERS'));
				}
			}

			if ($objO->reassignRole($model->get('id'), $left, 0, $role))
			{
				Notify::success(Lang::txt('COM_PROJECTS_TEAM_OWNERS_ROLE_CHANGED'));

				// Sync with system group
				$objO->sysGroup($model->get('alias'), $this->config->get('group_prefix', 'pr-'));
			}
		}

		if (!Request::getInt('no_html', 0))
		{
			$this->cancelTask();
		}
	}

	/**
	 * Remove member(s) from a project
	 * Disallows removal of last manager (must have at least one)
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$checked = Request::getVar('id', array());
		//$groups  = Request::getVar('group', array());
		$project_id = Request::getInt('project', 0);

		// Load the group page
		$this->model = new Project($project_id);

		// Instantiate project owner
		$objO = $this->model->table('Owner');

		// Get all managers
		$all = $objO->getIds($this->model->get('id'), $role = 'all');
		$remaining = array_diff($all, $checked);

		$deleted = 0;

		// Cannot delete if no managers remain
		if (!empty($remaining))
		{
			// Perform delete
			$deleted = $objO->removeOwners($this->model->get('id'), $checked, 1);

			if ($deleted)
			{
				Notify::success(Lang::txt('COM_PROJECTS_TEAM_OWNERS_DELETED'));
			}
		}
		else
		{
			if (count($all) > 0)
			{
				$left = array_diff($checked, array($all[0])); // leave one manager
				$deleted = $objO->removeOwners($this->model->get('id'), $left, 1);
			}

			Notify::error(Lang::txt('COM_PROJECTS_TEAM_OWNERS_DELETE_NOMANAGERS'));
		}

		if ($deleted)
		{
			// Sync with system group
			$objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));
		}

		if (!Request::getInt('no_html', 0))
		{
			$this->cancelTask();
		}
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&project=' . Request::getInt('project', 0), false)
		);
	}
}
