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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'middleware.php');

/**
 * Administrative tools controller for zone locations
 */
class ToolsControllerLocations extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of hosts
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['zone']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.zone',
			'zone',
			0,
			'int'
		));
		$this->view->filters['tmpl']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tmpl',
			'tmpl',
			''
		);
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'zone'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		if ($this->view->filters['tmpl'] == 'component')
		{
			$this->view->setLayout('component');
		}
		else
		{
			// Get paging variables
			$this->view->filters['limit']        = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			);
			$this->view->filters['start']        = $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			);

			// In case limit has been changed, adjust limitstart accordingly
			$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);
		}

		// Get the middleware database
		$this->view->zone = new MiddlewareModelZone($this->view->filters['zone']);

		$this->view->total = $this->view->zone->locations('count', $this->view->filters);

		$this->view->rows  = $this->view->zone->locations('list', $this->view->filters);

		if ($this->view->filters['tmpl'] != 'component')
		{
			// Initiate paging
			jimport('joomla.html.pagination');
			$this->view->pageNav = new JPagination(
				$this->view->total,
				$this->view->filters['start'],
				$this->view->filters['limit']
			);
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a record
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();

		$mw = new ToolsModelMiddleware($mwdb);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getInt('id', 0);

			$this->view->row = new MiddlewareModelLocation($id);
		}

		$this->view->zone = JRequest::getInt('zone', 0);
		if (!$this->view->row->exists())
		{
			$this->view->row->set('zone_id', $this->view->zone);
		}
		$this->view->tmpl = JRequest::getVar('tmpl', '');

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save changes to a record
	 *
	 * @param      boolean $redirect Redirect after save?
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$tmpl   = JRequest::getVar('tmpl', '');

		$row = new MiddlewareModelLocation($fields['id']);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($tmpl == 'component')
		{
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
			else
			{
				echo '<p class="message">' . JText::_('COM_TOOLS_ITEM_SAVED') . '</p>';
			}
			return;
		}

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Jtext::_('COM_TOOLS_ITEM_SAVED'),
				'message'
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Toggle a zone's state
	 *
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);
		$state = strtolower(JRequest::getWord('state', 'up'));

		if ($state != 'up' && $state != 'down')
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}


		$row = new MiddlewareModelLocation($id);
		if ($row->exists())
		{
			$row->set('state', $state);
			if (!$row->store())
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_TOOLS_ERROR_STATE_UPDATE_FAILED'),
					'error'
				);
				return;
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				$row = new MiddlewareModelLocation(intval($id));

				if (!$row->delete())
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
