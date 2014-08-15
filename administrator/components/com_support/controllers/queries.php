<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT . DS . 'tables' . DS . 'query.php');

/**
 * Support controller class for ticket queries
 */
class SupportControllerQueries extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get paging variables
		$this->view->filters = array();
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		$this->view->filters['iscore']   = array(4, 2, 1);

		$obj = new SupportQuery($this->database);

		// Record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Fetch results
		$this->view->rows = $obj->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

		$id = JRequest::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (!empty($id) ? $id[0] : 0);
		}

		$this->view->row = new SupportQuery($this->database);
		$this->view->row->load($id);
		if (!$this->view->row->sort)
		{
			$this->view->row->sort = 'created';
		}
		if (!$this->view->row->sort_dir)
		{
			$this->view->row->sort_dir = 'desc';
		}

		include_once(JPATH_COMPONENT . DS . 'models' . DS . 'conditions.php');
		$con = new SupportModelConditions();
		$this->view->conditions = $con->getConditions();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields  = JRequest::getVar('fields', array(), 'post');
		$no_html = JRequest::getInt('no_html', 0);
		$tmpl    = JRequest::getVar('component', '');

		$row = new SupportQuery($this->database);
		if (!$row->bind($fields))
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		// Check content
		if (!$row->check())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		// Store new content
		if (!$row->store())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_QUERY_SUCCESSFULLY_SAVED')
			);
		}
		else
		{
			$this->view->setLayout('list');

			$obj = new SupportTicket($this->database);

			$queries = $row->getCustom($this->juser->get('id'));
			if ($queries)
			{
				foreach ($queries as $k => $query)
				{
					if (!$query->query)
					{
						$query->query = $row->getQuery($query->conditions);
					}
					$queries[$k]->count = $obj->getCount($query->query);
				}
			}

			$this->view->queries = $queries;
			$this->view->show = 0;
			// Set any errors
			if ($this->getError())
			{
				foreach ($this->getError() as $error)
				{
					$this->view->setError($error);
				}
			}

			// Output the HTML
			$this->view->display();
		}
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$no_html = JRequest::getInt('no_html', 0);
		$tmpl    = JRequest::getVar('component', '');

		// Check for an ID
		if (count($ids) < 1)
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_SUPPORT_ERROR_SELECT_QUERY_TO_DELETE'),
					'error'
				);
			}
			return;
		}

		$row = new SupportQuery($this->database);
		foreach ($ids as $id)
		{
			// Delete message
			$row->delete(intval($id));
		}

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::sprintf('COM_SUPPORT_QUERY_SUCCESSFULLY_DELETED', count($ids))
			);
		}
		else
		{
			$this->view->setLayout('list');

			$obj = new SupportTicket($this->database);

			$queries = $row->getCustom($this->juser->get('id'));
			if ($queries)
			{
				foreach ($queries as $k => $query)
				{
					if (!$query->query)
					{
						$query->query = $row->getQuery($query->conditions);
					}
					$queries[$k]->count = $obj->getCount($query->query);
				}
			}

			$this->view->queries = $queries;
			$this->view->show = 0;
			// Set any errors
			if ($this->getError())
			{
				foreach ($this->getError() as $error)
				{
					$this->view->setError($error);
				}
			}

			// Output the HTML
			$this->view->display();
		}
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
