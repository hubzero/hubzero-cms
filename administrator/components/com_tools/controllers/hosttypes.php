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
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'hosttype.php');

/**
 * Tools controller for host types
 */
class ToolsControllerHosttypes extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of host types
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
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'value'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
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

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$model = new MwHosttype($mwdb);

		$this->view->total = $model->getCount($this->view->filters);

		$this->view->rows = $model->getRecords($this->view->filters);

		// Form the query and show the table.
		//$mwdb->setQuery("SELECT * FROM hosttype ORDER BY VALUE");
		//$this->view->rows = $mwdb->loadObjectList();
		if ($this->view->rows)
		{
			foreach ($this->view->rows as $key => $row)
			{
				$this->view->rows[$key]->refs = $this->_refs($row->value);
			}
		}

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
	public function editTask($row = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$item = JRequest::getVar('item', '', 'get');

			$mwdb = MwUtils::getMWDBO();

			$this->view->row = new MwHosttype($mwdb);
			$this->view->row->load($item);
		}

		if ($this->view->row->value > 0)
		{
			$this->view->bit = log($this->view->row->value)/log(2);
		}
		else
		{
			$this->view->bit = '';
		}

		$this->view->refs = $this->_refs($this->view->row->value);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Get a count of references
	 *
	 * @param      mixed $value
	 * @return     integer
	 */
	private function _refs($value)
	{
		$refs = 0;

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();
		$mwdb->setQuery("SELECT count(*) AS count FROM host WHERE provisions & " . $mwdb->Quote($value) . " != 0");
		$elts = $mwdb->loadObjectList();
		if ($elts)
		{
			$elt  = $elts[0];
			$refs = $elt->count;
		}

		return $refs;
	}

	/**
	 * Save changes to a record and return to edit form
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
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new MwHosttype($mwdb);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$insert = false;
		if (!$fields['id'])
		{
			$insert = true;
		}

		if (!$fields['value'])
		{
			$rows = $row->getRecords();

			$value = 1;
			for ($i=0; $i<count($rows); $i++)
			{
				if ($value == $rows[$i]->value)
				{
					$value = $value * 2;
				}
				// Double check that the hosttype doesn't already exist
				if ($row->name == $rows[$i]->name)
				{
					$insert = false;
				}
			}

			$row->value = $value;
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$fields['id'])
		{
			$result = $row->store($insert);
		}
		else
		{
			$result = $row->update($fields['id']);
		}

		if (!$result)
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
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
	 * Delete a hostname record
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$mwdb = MwUtils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new MwHosttype($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$row->delete($id))
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
