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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Store\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Sanitize;
use Components\Store\Tables\Store;
use Components\Store\Tables\OrderItem;
use Exception;
use Component;
use Request;
use Route;
use Config;
use Lang;
use Date;

/**
 * Controller class for store items
 */
class Items extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->banking = Component::params('com_members')->get('bankAccounts');

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Displays a list of groups
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Get paging variables
		$this->view->filters = array(
			'limit' => Request::getState(
				$this->_option . '.items.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.items.limitstart',
				'limitstart',
				0,
				'int'
			),
			'filterby' => Request::getState(
				$this->_option . '.items.filterby',
				'filterby',
				'all'
			),
			'sortby' => Request::getState(
				$this->_option . '.items.sortby',
				'sortby',
				'date'
			)
		);

		$obj = new Store($this->database);

		$this->view->total = $obj->getItems('count', $this->view->filters, $this->config);

		$this->view->rows = $obj->getItems('retrieve', $this->view->filters, $this->config);

		// how many times ordered?
		if ($this->view->rows)
		{
			$oi = new OrderItem($this->database);
			foreach ($this->view->rows as $o)
			{
				// Active orders
				$o->activeorders = $oi->countActiveItemOrders($o->id);

				// All orders
				$o->allorders = $oi->countAllItemOrders($o->id);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a store item
	 *
	 * @return  void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Instantiate a new view
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Incoming
		$id = Request::getInt('id', 0);

		// Load info from database
		$this->view->row = new Store($this->database);
		$this->view->row->load($id);

		if ($id)
		{
			// Get parameters
			$params = new \Hubzero\Config\Registry($this->view->row->params);
			$this->view->row->size  = $params->get('size', '');
			$this->view->row->color = $params->get('color', '');
		}
		else
		{
			// New item
			$this->view->row->available = 0;
			$this->view->row->created   = Date::toSql();
			$this->view->row->published = 0;
			$this->view->row->featured  = 0;
			$this->view->row->special   = 0;
			$this->view->row->type      = 1;
			$this->view->row->category  = 'wear';
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves changes to a store item
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$id = Request::getInt('id', 0);

		$_POST = array_map('trim', $_POST);

		// initiate extended database class
		$row = new Store($this->database);
		if (!$row->bind($_POST))
		{
			throw new Exception($row->getError(), 500);
		}

		// code cleaner
		$row->description = Sanitize::clean($row->description);
		if (!$id)
		{
			$row->created = $row->created ? $row->created : Date::toSql();
		}
		$sizes = ($_POST['sizes']) ? $_POST['sizes'] : '';
		$sizes = str_replace(' ', '', $sizes);
		$sizes = preg_split('#,#', $sizes);
		$sizes_cl = '';
		foreach ($sizes as $s)
		{
			if (trim($s) != '')
			{
				$sizes_cl .= $s;
				$sizes_cl .= ($s == end($sizes)) ? '' : ', ';
			}
		}
		$row->title     = htmlspecialchars(stripslashes($row->title));
		$row->params    = $sizes_cl ? 'size=' . $sizes_cl : '';
		$row->published	= isset($_POST['published']) ? 1 : 0;
		$row->available	= isset($_POST['available']) ? 1 : 0;
		$row->featured  = isset($_POST['featured'])  ? 1 : 0;
		$row->type      = ($_POST['category'] == 'service') ? 2 : 1;

		// check content
		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}

		// store new content
		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_STORE_MSG_SAVED')
		);
	}

	/**
	 * Calls stateTask to set entry to available
	 *
	 * @return  void
	 */
	public function availableTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to set entry to unavailable
	 *
	 * @return  void
	 */
	public function unavailableTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or exit('Invalid Token');

		$id = Request::getInt('id', 0, 'get');

		switch ($this->_task)
		{
			case 'publish':
			case 'unpublish':
				$publish = ($this->_task == 'publish') ? 1 : 0;

				// Check for an ID
				if (!$id)
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						Lang::txt('COM_STORE_ALERT_SELECT_ITEM') . ' ' . ($publish == 1 ? 'published' : 'unpublished'),
						'error'
					);
					return;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->published = $publish;

				if (!$obj->store())
				{
					throw new Exception($obj->getError(), 500);
				}

				// Set message
				if ($publish == '1')
				{
					Notify::success(Lang::txt('COM_STORE_MSG_ITEM_ADDED'));
				}
				else if ($publish == '0')
				{
					Notify::success(Lang::txt('COM_STORE_MSG_ITEM_DELETED'));
				}
			break;

			case 'available':
			case 'unavailable':
				$avail = ($this->_task == 'available') ? 1 : 0;

				// Check for an ID
				if (!$id)
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						Lang::txt('COM_STORE_ALERT_SELECT_ITEM') . ' ' . ($avail == 1 ? 'available' : 'unavailable'),
						'error'
					);
					return;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->available = $avail;

				if (!$obj->store())
				{
					throw new Exception($obj->getError(), 500);
				}

				// Set message
				if ($avail == '1')
				{
					Notify::success(Lang::txt('COM_STORE_MSG_ITEM_AVAIL'));
				}
				else if ($avail == '0')
				{
					Notify::success(Lang::txt('COM_STORE_MSG_ITEM_UNAVAIL'));
				}
			break;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

