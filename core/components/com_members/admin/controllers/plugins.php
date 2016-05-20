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
use Request;
use Config;
use Route;
use Event;
use User;
use Html;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'permissions.php';

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
		$task = Request::getVar('task', '');
		$plugin = Request::getVar('plugin', '');
		if ($plugin && $task && $task != 'manage') //!isset($this->_taskMap[$task]))
		{
			Request::setVar('action', $task);
			Request::setVar('task', 'manage');
		}

		$this->_folder = 'members';

		parent::execute();
	}

	/**
	 * List resource types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
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
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'state' => strtoupper(Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'filter_state',
				'',
				'word'
			)),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				'',
				'word'
			))
		);

		$where = array();
		$this->client = Request::getWord('filter_client', 'site');

		if ($this->client == 'admin')
		{
			$where[] = 'p.client_id = 1';
			$client_id = 1;
		}
		else
		{
			$where[] = 'p.client_id = 0';
			$where[] = 'p.folder = ' . $this->database->Quote($this->_folder);
			$client_id = 0;
		}

		if ($this->view->filters['search'])
		{
			$where[] = 'LOWER(p.name) LIKE ' . $this->database->Quote('%' . $this->view->filters['search'] . '%');
		}
		if ($this->view->filters['state'])
		{
			if ($this->view->filters['state'] == 'P')
			{
				$where[] = 'p.enabled = 1';
			}
			else if ($this->view->filters['state'] == 'U')
			{
				$where[] = 'p.enabled = 0';
			}
		}
		$where[] = 'p.type = ' . $this->database->Quote('plugin');

		$where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$orderby = ' ORDER BY ' . $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'] . ', p.ordering ASC';

		// get the total number of records
		$query = 'SELECT COUNT(*)'
			. ' FROM #__extensions AS p'
			. $where;

		$this->database->setQuery($query);
		$this->view->total = $this->database->loadResult();

		$query = 'SELECT p.extension_id AS id, p.enabled As published, p.*, u.name AS editor, g.title AS groupname'
			. ' FROM #__extensions AS p'
			. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
			. ' LEFT JOIN #__viewlevels AS g ON g.id = p.access'
			. $where
			. ' GROUP BY p.extension_id'
			. $orderby;

		$this->database->setQuery($query, $this->view->filters['start'], $this->view->filters['limit']);
		$this->view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum())
		{
			App::abort(500, $this->database->stderr());
			return false;
		}

		$lang = Lang::getRoot();
		if ($this->view->rows)
		{
			foreach ($this->view->rows as &$item)
			{
				$source = '/plugins/' . $item->folder . '/' . $item->element;
				$extension = 'plg_' . $item->folder . '_' . $item->element;
					$lang->load($extension . '.sys', PATH_APP . $source, null, false, false)
				||	$lang->load($extension . '.sys', PATH_CORE . $source, null, false, false)
				||	$lang->load($extension . '.sys', PATH_APP . $source, $lang->getDefault(), false, false)
				||	$lang->load($extension . '.sys', PATH_CORE . $source, $lang->getDefault(), false, false);
				$item->name = Lang::txt($item->name);
			}
		}

		// Show related content
		$this->view->manage = Event::trigger('members.onCanManage');

		$this->view->client = $this->client;
		$this->view->states = Html::grid('states', $this->view->filters['state']);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function manageTask()
	{
		// Incoming (expecting an array)
		$plugin = Request::getVar('plugin', '');

		if (!$plugin)
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('Please select a plugin to manage.')
			);
		}

		// Show related content
		$out = Event::trigger(
			'members.onManage',
			array(
				$this->_option,
				$this->_controller,
				Request::getVar('action', 'default')
			)
		);

		$this->view->html = '';

		if (count($out) > 0)
		{
			foreach ($out as $o)
			{
				$this->view->html .= $o;
			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of a plugin
	 *
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		// Incoming
		$id = Request::getVar('id', array(), '', 'array');
		$id = (!is_array($id) ? array($id) : $id);
		\Hubzero\Utility\Arr::toInteger($id, array(0));

		$client = Request::getWord('filter_client', 'site');

		if (count($id) < 1)
		{
			$action = $state ? Lang::txt('publish') : Lang::txt('unpublish');
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
				Lang::txt('Select a plugin to ' . $action),
				'error'
			);
			return;
		}

		$query = "UPDATE #__extensions SET enabled = ".(int) $state
			. " WHERE extension_id IN (" . implode(',', $id) . ")"
			. " AND `type`='plugin' AND (checked_out = 0 OR (checked_out = ". (int) User::get('id') . "))";

		$this->database->setQuery($query);
		if (!$this->database->query())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
				$this->database->getErrorMsg(),
				'error'
			);
			return;
		}

		if (count($id) == 1)
		{
			$row = \JTable::getInstance('extension');
			$row->checkin($id[0]);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false)
		);
	}

	/**
	 * Reorder a plugin up
	 *
	 * @return     void
	 */
	public function orderupTask()
	{
		return $this->orderTask();
	}

	/**
	 * Reorder a plugin down
	 *
	 * @return     void
	 */
	public function orderdownTask()
	{
		return $this->orderTask();
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
		Request::checkToken(['post', 'get']);

		$cid    = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		$uid    = $cid[0];
		$inc    = ($this->_task == 'orderup' ? -1 : 1);
		$client = Request::getWord('filter_client', 'site');

		// Currently Unsupported
		if ($client == 'admin')
		{
			$where = "client_id = 1";
		}
		else
		{
			$where = "client_id = 0";
		}

		$row = \JTable::getInstance('extension');
		$row->load($uid);
		$row->move($inc, 'folder=' . $this->database->Quote($row->folder) . ' AND ordering > -10000 AND ordering < 10000 AND (' . $where . ')');

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Set the state of an article to 'public'
	 *
	 * @return     void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the state of an article to 'registered'
	 *
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the state of an article to 'special'
	 *
	 * @return     void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(3);
	}

	/**
	 * Set the access of a plugin
	 *
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function accessTask($access=0)
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		// Incoming
		$cid = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		// Load the object
		$row = \JTable::getInstance('extension');
		$row->load($cid[0]);

		// Set the access
		$row->access = $access;

		// Check data
		if (!$row->check())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Store data
		if (!$row->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Save the ordering for an array of plugins
	 *
	 * @return     void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		$cid = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		$total = count($cid);
		$order = Request::getVar('order', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($order, array(0));

		$row = \JTable::getInstance('extension');

		$conditions = array();

		// update ordering values
		for ($i=0; $i < $total; $i++)
		{
			$row->load((int) $cid[$i]);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						$this->database->getErrorMsg(),
						'error'
					);
					return;
				}
				// remember to updateOrder this group
				$condition = 'folder = ' . $this->database->Quote($row->folder) . ' AND ordering > -10000 AND ordering < 10000 AND client_id = ' . (int) $row->client_id;
				$found = false;
				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}
				if (!$found) $conditions[] = array($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('New ordering saved')
		);
	}
}
