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

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Arr;
use Components\Plugins\Models\Plugin;
use Request;
use Config;
use Route;
use Event;
use User;
use Html;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . '/helpers/permissions.php';
include_once \Component::path('com_plugins') . '/models/plugin.php';

/**
 * Manage resource types
 */
class Plugins extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$task   = Request::getCmd('task', '');
		$plugin = Request::getString('plugin', '');

		if ($plugin && $task && $task != 'manage')
		{
			Request::setVar('action', $task);
			Request::setVar('task', 'manage');
		}

		// States
		$this->registerTask('unpublish', 'publish');  // Value = 0
		$this->registerTask('archive', 'publish');  // Value = 2
		$this->registerTask('trash', 'publish');  // Value = -2
		$this->registerTask('report', 'publish'); // Value = -3

		// Reordering
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * List plugins
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'folder' => 'members',
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'filter_state',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				0,
				'int'
			),
			'enabled' => Request::getState(
				$this->_option . '.' . $this->_controller . '.enabled',
				'filter_state',
				'',
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'folder'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Plugin::all()
			->where('state', '>=', 0);

		$p = $query->getTableName();
		$u = '#__users';
		$a = '#__viewlevels';

		$query->select($p . '.*');

		// Join over the users for the checked out user.
		$query
			->select($u . '.name', 'editor')
			->join($u, $u . '.id', $p . '.checked_out', 'left');

		// Join over the access groups.
		$query
			->select($a . '.title', 'access_level')
			->join($a, $a . '.id', $p . '.access', 'left');

		// Filter by access level.
		if ($filters['access'])
		{
			$query->whereEquals($p . '.access', (int) $filters['access']);
		}

		// Filter by published state
		if (is_numeric($filters['state']))
		{
			$query->whereEquals($p . '.enabled', (int) $filters['state']);
		}
		elseif ($filters['state'] === '')
		{
			$query->whereIn($p . '.enabled', array(0, 1));
		}

		// Filter by folder.
		if ($filters['folder'])
		{
			$query->whereEquals($p . '.folder', $filters['folder']);
		}

		// Filter by search in id
		if (!empty($filters['search']) && stripos($filters['search'], 'id:') === 0)
		{
			$query->whereEquals($p . '.extension_id', (int) substr($filters['search'], 3));
		}

		if ($filters['sort'] == 'name')
		{
			$query->order('name', $filters['sort_Dir']);
			$query->order('ordering', 'asc');
		}
		else if ($filters['sort'] == 'ordering')
		{
			$query->order('folder', 'asc');
			$query->order('ordering', $filters['sort_Dir']);
			$query->order('name', 'asc');
		}
		else
		{
			$query->order($filters['sort'], $filters['sort_Dir']);
			$query->order('name', 'asc');
			$query->order('ordering', 'asc');
		}

		$items = $query
			->paginated('limitstart', 'limit')
			->rows();

		// Check if there are no matching items
		if (!count($items))
		{
			Notify::warning(Lang::txt('COM_PLUGINS_MSG_MANAGE_NO_PLUGINS'));
		}

		$manage = Event::trigger('members.onCanManage');

		$this->view
			->set('filters', $filters)
			->set('items', $items)
			->set('manage', $manage)
			->display();
	}

	/**
	 * Edit a type
	 *
	 * @return  void
	 */
	public function manageTask()
	{
		// Incoming (expecting an array)
		$plugin = Request::getString('plugin', '');

		if (!$plugin)
		{
			Notify::warning(Lang::txt('Please select a plugin to manage.'));

			return $this->cancelTask();
		}

		// Show related content
		$out = Event::trigger(
			'members.onManage',
			array(
				$this->_option,
				$this->_controller,
				Request::getString('action', 'default')
			)
		);

		$html = '';

		if (count($out) > 0)
		{
			foreach ($out as $o)
			{
				$html .= $o;
			}
		}

		// Output the HTML
		$this->view
			->set('html', $html)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Set the state of a plugin
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		// Get items to publish from the request.
		$cid   = Request::getArray('id', array(), '');
		$data  = array(
			'publish'   => 1,
			'unpublish' => 0,
			'archive'   => 2,
			'trash'     => -2,
			'report'    => -3
		);
		$task  = $this->getTask();
		$value = Arr::getValue($data, $task, 0, 'int');

		$success = 0;

		foreach ($cid as $id)
		{
			// Load the record
			$model = Plugin::oneOrFail(intval($id));

			// Set state
			$model->set('enabled', $value);

			if (!$model->save())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			if ($value == 1)
			{
				$ntext = 'COM_MEMBERS_N_ITEMS_PUBLISHED';
			}
			elseif ($value == 0)
			{
				$ntext = 'COM_MEMBERS_N_ITEMS_UNPUBLISHED';
			}
			elseif ($value == 2)
			{
				$ntext = 'COM_MEMBERS_N_ITEMS_ARCHIVED';
			}
			else
			{
				$ntext = 'COM_MEMBERS_N_ITEMS_TRASHED';
			}

			Notify::success(Lang::txts($ntext, $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Reorder a plugin
	 *
	 * @return  void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		// Initialise variables.
		$ids = Request::getArray('id', null, 'post');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record and reorder it
			$model = Plugin::oneOrFail(intval($id));

			if (!$model->move($inc))
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Save the ordering for an array of plugins
	 *
	 * @return  void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		$pks   = Request::getArray('id', array(0), 'post');
		$order = Request::getArray('order', array(0), 'post');

		// Sanitize the input
		Arr::toInteger($pks);
		Arr::toInteger($order);

		// Save the ordering
		$return = Plugin::saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED'));
		}
		else
		{
			// Clean the cache.
			$this->cleanCache();

			// Reorder succeeded.
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Clean cached data
	 *
	 * @return  void
	 */
	public function cleanCache()
	{
		Cache::clean('com_plugins');
	}
}
