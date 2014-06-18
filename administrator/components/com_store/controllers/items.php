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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for store items
 */
class StoreControllerItems extends \Hubzero\Component\AdminController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$upconfig = JComponentHelper::getParams('com_members');
		$this->banking = $upconfig->get('bankAccounts');

		parent::execute();
	}

	/**
	 * Displays a list of groups
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.items.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.items.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['filterby'] = trim($app->getUserStateFromRequest(
			$this->_option . '.items.filterby',
			'filterby',
			'all'
		));
		$this->view->filters['sortby']   = trim($app->getUserStateFromRequest(
			$this->_option . '.items.sortby',
			'sortby',
			'date'
		));

		$obj = new Store($this->database);

		$this->view->total = $obj->getItems('count', $this->view->filters, $this->config);

		$this->view->rows = $obj->getItems('retrieve', $this->view->filters, $this->config);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

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

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a store item
	 *
	 * @return void
	 */
	public function editTask()
	{
		//JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Instantiate a new view
		$this->view->store_enabled = $this->config->get('store_enabled');

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Load info from database
		$this->view->row = new Store($this->database);
		$this->view->row->load($id);

		if ($id)
		{
			// Get parameters
			$params = new JRegistry($this->view->row->params);
			$this->view->row->size  = $params->get('size', '');
			$this->view->row->color = $params->get('color', '');
		}
		else
		{
			// New item
			$this->view->row->available = 0;
			$this->view->row->created   = JFactory::getDate()->toSql();
			$this->view->row->published = 0;
			$this->view->row->featured  = 0;
			$this->view->row->special   = 0;
			$this->view->row->type      = 1;
			$this->view->row->category  = 'wear';
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Saves changes to a store item
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		$_POST = array_map('trim', $_POST);

		// initiate extended database class
		$row = new Store($this->database);
		if (!$row->bind($_POST))
		{
			JError::raiseError(500,$row->getError());
			return;
		}

		// code cleaner
		$row->description = \Hubzero\Utility\Sanitize::clean($row->description);
		if (!$id)
		{
			$row->created = $row->created ? $row->created : JFactory::getDate()->toSql();
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
			JError::raiseError(500, $row->getError());
			return;
		}

		// store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_STORE_MSG_SAVED')
		);
	}

	/**
	 * Calls stateTask to set entry to available
	 *
	 * @return     void
	 */
	public function availableTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to set entry to unavailable
	 *
	 * @return     void
	 */
	public function unavailableTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask();
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		$id = JRequest::getInt('id', 0, 'get');

		switch ($this->_task)
		{
			case 'publish':
			case 'unpublish':
				$publish = ($this->_task == 'publish') ? 1 : 0;

				// Check for an ID
				if (!$id)
				{
					$action = ($publish == 1) ? 'published' : 'unpublished';
					echo StoreHtml::alert(JText::_('COM_STORE_ALERT_SELECT_ITEM') . ' ' . $action);
					exit;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->published = $publish;

				if (!$obj->store())
				{
					JError::raiseError(500, $obj->getError());
					return;
				}

				// Set message
				if ($publish == '1')
				{
					$this->_message = JText::_('COM_STORE_MSG_ITEM_ADDED');
				}
				else if ($publish == '0')
				{
					$this->_message = JText::_('COM_STORE_MSG_ITEM_DELETED');
				}
			break;

			case 'available':
			case 'unavailable':
				$avail = ($this->_task == 'available') ? 1 : 0;

				// Check for an ID
				if (!$id)
				{
					$action = ($avail == 1) ? 'available' : 'unavailable';
					echo StoreHtml::alert(JText::_('COM_STORE_ALERT_SELECT_ITEM') . ' ' . $action);
					exit;
				}

				// Update record(s)
				$obj = new Store($this->database);
				$obj->load($id);
				$obj->available = $avail;

				if (!$obj->store())
				{
					JError::raiseError(500, $obj->getError());
					return;
				}

				// Set message
				if ($avail == '1')
				{
					$this->_message = JText::_('COM_STORE_MSG_ITEM_AVAIL');
				}
				else if ($avail == '0')
				{
					$this->_message = JText::_('COM_STORE_MSG_ITEM_UNAVAIL');
				}
			break;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

