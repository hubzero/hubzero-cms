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

namespace Components\Cart\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Cart\Helpers\CartDownload;

require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Download.php');

/**
 * Controller class for knowledge base categories
 */
class Downloads extends AdminController
{
	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			// Get sorting variables
				'sort' => Request::getState(
						$this->_option . '.' . $this->_controller . '.sort',
						'filter_order',
						'dDownloaded'
				),
				'sort_Dir' => Request::getState(
						$this->_option . '.' . $this->_controller . '.sortdir',
						'filter_order_Dir',
						'ASC'
				),
			// Get paging variables
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
				)
		);
		//print_r($this->view->filters);

		// Get record count
		$this->view->total = CartDownload::getDownloads('count', $this->view->filters);

		// Get records
		$this->view->rows = CartDownload::getDownloads('list', $this->view->filters);

		// Output the HTML
		//print_r($this->view); die;
		$this->view->display();
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function activeTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function inactiveTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state = 0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					($state == 1 ? Lang::txt('COM_CART_SELECT_ACTIVE') : Lang::txt('COM_CART_SELECT_INACTIVE')),
					'error'
			);
			return;
		}

		// Save downloads
		try {
			CartDownload::setStatus($ids, $state);
		}
		catch (\Exception $e)
		{
			\Notify::error($e->getMessage());
			return;
		}

		// Set message
		switch ($state)
		{
			case '1':
				$message = Lang::txt('COM_CART_ACTIVATED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_CART_DEACTIVATED', count($ids));
			break;
		}

		// Redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				$message
		);
	}
}