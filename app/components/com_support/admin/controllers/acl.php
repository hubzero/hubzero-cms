<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\ACL as TheACL;
use Components\Support\Models\Acl\Aro;
use Components\Support\Models\Acl\Aco;
use Components\Support\Models\Acl\Map;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Support controller class for defining permissions
 */
class Acl extends AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$acl = TheACL::getACL();

		// Fetch results
		$rows = Aro::all()->rows();

		// Output HTML
		$this->view
			->set('acl', $acl)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Update an existing record
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$id     = Request::getInt('id', 0);
		$action = Request::getString('action', '');
		$value  = Request::getInt('value', 0);

		$row = Map::oneOrFail($id);

		switch ($action)
		{
			case 'create':
				$row->set('action_create', $value);
				break;
			case 'read':
				$row->set('action_read', $value);
				break;
			case 'update':
				$row->set('action_update', $value);
				break;
			case 'delete':
				$row->set('action_delete', $value);
				break;
		}

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
		}
		else
		{
			Notify::success(Lang::txt('COM_SUPPORT_ACL_SAVED'));
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more records
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$ids = Request::getArray('id', array());

		$removed = 0;
		foreach ($ids as $id)
		{
			$row = Aro::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_SUPPORT_ACL_REMOVED'));
		}

		// Output messsage and redirect
		$this->cancelTask();
	}

	/**
	 * Save a new record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Trim and addslashes all posted items
		$aro = Request::getArray('aro', array(), 'post');
		$aro = array_map('trim', $aro);

		// Initiate class and bind posted items to database fields
		$row = Aro::blank()->set($aro);

		if ($fk = $row->get('foreign_key'))
		{
			switch ($row->get('model'))
			{
				case 'user':
					$user = User::getInstance($fk);
					if (!is_object($user))
					{
						App::abort(500, Lang::txt('COM_SUPPORT_ACL_ERROR_UNKNOWN_USER'));
					}
					$row->set('foreign_key', intval($user->get('id')));
					$row->set('alias', $user->get('username'));
				break;

				case 'group':
					$group = \Hubzero\User\Group::getInstance($fk);
					if (!is_object($group))
					{
						App::abort(500, Lang::txt('COM_SUPPORT_ACL_ERROR_UNKNOWN_GROUP') . ' ' . $fk);
					}
					$row->set('foreign_key', intval($group->get('gidNumber')));
					$row->set('alias', $group->get('cn'));
				break;
			}
		}

		// Store new content
		if (!$row->save())
		{
			App::abort(500, $row->getError());
		}

		// Trim and addslashes all posted items
		$map = Request::getArray('map', array(), 'post');

		foreach ($map as $k => $v)
		{
			// Initiate class and bind posted items to database fields
			$aroaco = Map::oneOrNew($v['id'])->set($v);

			$aroaco->set('aro_id', ($aroaco->get('aro_id') ? $aroaco->get('aro_id') : $row->get('id')));

			// Store new content
			if (!$aroaco->save())
			{
				App::abort(500, $aroaco->getError());
			}
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_SUPPORT_ACL_SAVED'));

		$this->cancelTask();
	}
}
