<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Admin\Controllers;

use Hubzero\Component\AdminController;
use Exception;
use Request;
use Config;
use Event;
use Route;
use Lang;
use App;

/**
 * Manage resource types
 */
class Plugins extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$task = Request::getVar('task', '');
		$plugin = Request::getVar('plugin', '');
		if ($plugin && $task && $task != 'manage')
		{
			Request::setVar('action', $task);
			Request::setVar('task', 'manage');
		}

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');
		$this->registerTask('accesspublic', 'access');
		$this->registerTask('accessspecial', 'access');
		$this->registerTask('accessregistered', 'access');

		$this->_folder = 'resources';

		parent::execute();
	}

	/**
	 * List resource types
	 *
	 * @return  void
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
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'',
				'word'
			),
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
				$where[] = 'p.published = 1';
			}
			else if ($this->view->filters['state'] == 'U')
			{
				$where[] = 'p.published = 0';
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

		$query = 'SELECT p.extension_id AS id, p.enabled As published, p.*, u.name AS editor, g.title AS access_level'
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
			throw new Exception($this->database->stderr(), 500);
		}

		$lang = Lang::getRoot();
		if ($this->view->rows)
		{
			foreach ($this->view->rows as &$item)
			{
				$source = PATH_CORE . '/plugins/' . $item->folder . '/' . $item->element;
				$extension = 'plg_' . $item->folder . '_' . $item->element;
					$lang->load($extension . '.sys', PATH_APP . '/plugins/' . $item->folder . '/' . $item->element, null, false, false)
				||	$lang->load($extension . '.sys', $source, null, false, false)
				||	$lang->load($extension . '.sys', PATH_APP . '/plugins/' . $item->folder . '/' . $item->element, $lang->getDefault(), false, false)
				||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
				$item->name = Lang::txt($item->name);
			}
		}

		// Show related content
		$this->view->manage = Event::trigger('resources.onCanManage');

		$this->view->client = $this->client;
		$this->view->states = \Html::grid('state', $this->view->filters['state']);

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
	 * @return  void
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
				Lang::txt('COM_RESOURCES_ERROR_NO_PLUGIN_SELECTED')
			);
			return;
		}

		// Show related content
		$out = Event::trigger(
			'resources.onManage',
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
	 * Edit a plugin
	 *
	 * @param      object $row JPluginTable
	 * @return     void
	 */
	public function editTask($row = null)
	{
		$cid = Request::getVar('cid', array(0), '', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		App::redirect(
			Route::url('index.php?option=com_plugins&task=plugin.edit&extension_id=' . $cid[0] . '&component=resources', false)
		);
	}

	/**
	 * Save changes to a plugin
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$client = Request::getWord('filter_client', 'site');

		// Bind data
		$row = \JTable::getInstance('extension');
		if (!$row->bind(Request::get('post')))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$row->checkin();
		$row->reorder(
			'folder = ' . $this->database->Quote($row->folder) . '
			AND ordering > -10000
			AND ordering < 10000
			AND (' . ($client == 'admin' ? "client_id=1" : "client_id=0") . ')'
		);

		switch ($this->_task)
		{
			case 'apply':
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client . '&task=edit&cid=' . $row->id, false),
					Lang::txt('COM_RESOURCES_PLUGINS_ITEM_SAVED', $row->name)
				);
			break;

			case 'save':
			default:
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
					Lang::txt('COM_RESOURCES_PLUGINS_ITEM_SAVED', $row->name)
				);
			break;
		}
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

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');
		\Hubzero\Utility\Arr::toInteger($id, array(0));

		$client = Request::getWord('filter_client', 'site');

		if (count($id) < 1)
		{
			$action = $state ? Lang::txt('COM_RESOURCES_PUBLISH') : Lang::txt('COM_RESOURCES_UNPUBLISH');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
				Lang::txt('COM_RESOURCES_ERROR_SELECT_TO', $action),
				'error'
			);
			return;
		}

		$query = "UPDATE `#__extensions` SET enabled = ".(int) $state
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
	 * Reorder a plugin
	 *
	 * @param   integer  $access  Access level to set
	 * @return  void
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
	 * Set the access of a plugin
	 *
	 * @return  void
	 */
	public function accessTask()
	{
		// Check for request forgeries
		Request::checkToken(['post', 'get']);

		switch ($this->_task)
		{
			case 'accesspublic':     $access = 1; break;
			case 'accessregistered': $access = 2; break;
			case 'accessspecial':    $access = 3; break;
		}

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
	 * @return  void
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
			Lang::txt('COM_RESOURCES_ORDERING_SAVED')
		);
	}
}
