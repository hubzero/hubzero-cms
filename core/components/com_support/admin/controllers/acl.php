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

namespace Components\Support\Admin\Controllers;

use Components\Support\Helpers\ACL as TheACL;
use Components\Support\Tables\Aro;
use Components\Support\Tables\Aco;
use Components\Support\Tables\AroAco;
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
		$aro = new Aro($this->database);
		$rows = $aro->getRecords();

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
		//Request::checkToken('get');

		$id     = Request::getInt('id', 0);
		$action = Request::getVar('action', '');
		$value  = Request::getInt('value', 0);

		$row = new AroAco($this->database);
		$row->load($id);

		switch ($action)
		{
			case 'create': $row->action_create = $value; break;
			case 'read':   $row->action_read   = $value; break;
			case 'update': $row->action_update = $value; break;
			case 'delete': $row->action_delete = $value; break;
		}

		// Check content
		if (!$row->check())
		{
			App::abort(500, $row->getError());
		}

		// Store new content
		if (!$row->store())
		{
			App::abort(500, $row->getError());
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_SUPPORT_ACL_SAVED'));

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

		$ids = Request::getVar('id', array());

		$i = 0;
		foreach ($ids as $id)
		{
			$row = new Aro($this->database);
			$row->load(intval($id));

			if ($row->id)
			{
				$aro_aco = new AroAco($this->database);

				if (!$aro_aco->deleteRecordsByAro($row->id))
				{
					Notify::error($aro_aco->getError());
					continue;
				}
			}

			if (!$row->delete())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
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
		$aro = Request::getVar('aro', array(), 'post');
		$aro = array_map('trim', $aro);

		// Initiate class and bind posted items to database fields
		$row = new Aro($this->database);
		if (!$row->bind($aro))
		{
			App::abort(500, $row->getError());
		}

		if ($row->foreign_key)
		{
			switch ($row->model)
			{
				case 'user':
					$user = User::getInstance($row->foreign_key);
					if (!is_object($user))
					{
						App::abort(500, Lang::txt('COM_SUPPORT_ACL_ERROR_UNKNOWN_USER'));
					}
					$row->foreign_key = intval($user->get('id'));
					$row->alias = $user->get('username');
				break;

				case 'group':
					$group = \Hubzero\User\Group::getInstance($row->foreign_key);
					if (!is_object($group))
					{
						App::abort(500, Lang::txt('COM_SUPPORT_ACL_ERROR_UNKNOWN_GROUP'));
					}
					$row->foreign_key = intval($group->gidNumber);
					$row->alias = $group->cn;
				break;
			}
		}

		// Check content
		if (!$row->check())
		{
			App::abort(500, $row->getError());
		}

		// Store new content
		if (!$row->store())
		{
			App::abort(500, $row->getError());
		}

		if (!$row->id)
		{
			$row->id = $this->database->insertid();
		}

		// Trim and addslashes all posted items
		$map = Request::getVar('map', array(), 'post');

		foreach ($map as $k => $v)
		{
			// Initiate class and bind posted items to database fields
			$aroaco = new AroAco($this->database);
			if (!$aroaco->bind($v))
			{
				App::abort(500, $aroaco->getError());
			}

			$aroaco->aro_id = (!$aroaco->aro_id) ? $row->id : $aroaco->aro_id;

			// Check content
			if (!$aroaco->check())
			{
				App::abort(500, $aroaco->getError());
			}

			// Store new content
			if (!$aroaco->store())
			{
				App::abort(500, $aroaco->getError());
			}
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_SUPPORT_ACL_SAVED'));

		$this->cancelTask();
	}
}
