<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Manage resource types
 */
class MembersControllerPlugins extends \Hubzero\Component\AdminController
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
		// Get configuration
		$app = JFactory::getApplication();

		// Incoming
		$this->view->filters = array(
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'state' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				'',
				'word'
			),
			'search' => urldecode($app->getUserStateFromRequest(
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

		jimport('joomla.html.pagination');
		$this->view->pagination = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		$query = 'SELECT p.extension_id AS id, p.enabled As published, p.*, u.name AS editor, g.title AS groupname'
			. ' FROM #__extensions AS p'
			. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
			. ' LEFT JOIN #__viewlevels AS g ON g.id = p.access'
			. $where
			. ' GROUP BY p.extension_id'
			. $orderby;

		$this->database->setQuery($query, $this->view->pagination->limitstart, $this->view->pagination->limit);
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
				$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
				$extension = 'plg_' . $item->folder . '_' . $item->element;
					$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
				||	$lang->load($extension . '.sys', $source, null, false, false)
				||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
				||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
				$item->name = Lang::txt($item->name);
			}
		}

		// Get related plugins
		JPluginHelper::importPlugin('members');
		$dispatcher = JDispatcher::getInstance();

		// Show related content
		$this->view->manage = $dispatcher->trigger('onCanManage');

		$this->view->client = $this->client;
		$this->view->states = JHTML::_('grid.state', $this->view->filters['state']);
		$this->view->user   = User::getRoot();

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
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('Please select a plugin to manage.')
			);
		}

		// Get related plugins
		JPluginHelper::importPlugin('members', $plugin);
		$dispatcher = JDispatcher::getInstance();

		// Show related content
		$out = $dispatcher->trigger(
			'onManage',
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
		Request::checkToken() or Request::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = Request::getVar('id', array(), '', 'array');
		$id = (!is_array($id) ? array($id) : $id);
		JArrayHelper::toInteger($id, array(0));

		$client = Request::getWord('filter_client', 'site');

		if (count($id) < 1)
		{
			$action = $state ? Lang::txt('publish') : Lang::txt('unpublish');
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
				Lang::txt('Select a plugin to ' . $action),
				'error'
			);
			return;
		}

		$query = "UPDATE #__extensions SET enabled = ".(int) $state
			. " WHERE extension_id IN (" . implode(',', $id) . ")"
			. " AND `type`='plugin' AND (checked_out = 0 OR (checked_out = ". (int) $this->juser->get('id') . "))";

		$this->database->setQuery($query);
		if (!$this->database->query())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, false),
				$this->database->getErrorMsg(),
				'error'
			);
			return;
		}

		if (count($id) == 1)
		{
			$row = JTable::getInstance('extension');
			$row->checkin($id[0]);
		}

		$this->setRedirect(
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
		Request::checkToken() or Request::checkToken('get') or jexit('Invalid Token');

		$cid    = Request::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

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

		$row = JTable::getInstance('extension');
		$row->load($uid);
		$row->move($inc, 'folder=' . $this->database->Quote($row->folder) . ' AND ordering > -10000 AND ordering < 10000 AND (' . $where . ')');

		$this->setRedirect(
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
		Request::checkToken() or Request::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$cid = Request::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		// Load the object
		$row = JTable::getInstance('extension');
		$row->load($cid[0]);

		// Set the access
		$row->access = $access;

		// Check data
		if (!$row->check())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Store data
		if (!$row->store())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
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
		Request::checkToken() or Request::checkToken('get') or jexit('Invalid Token');

		$cid = Request::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		$total = count($cid);
		$order = Request::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order, array(0));

		$row = JTable::getInstance('extension');

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
					$this->setRedirect(
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
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('New ordering saved')
		);
	}
}
