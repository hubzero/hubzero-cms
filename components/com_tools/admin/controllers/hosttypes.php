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

include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'hosttype.php');

/**
 * Tools controller for host types
 */
class ToolsControllerHosttypes extends \Hubzero\Component\AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display a list of host types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array(
			// Sorting
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'value'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
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
			)
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();

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
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$item = Request::getVar('item', '', 'get');

			$mwdb = ToolsHelperUtils::getMWDBO();

			$row = new MwHosttype($mwdb);
			$row->load($item);
		}

		$this->view->row = $row;

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
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->set('status', (isset($item) && $item != '') ? 'exists' : 'new')
			->setLayout('edit')
			->display();
	}

	/**
	 * Get a count of references
	 *
	 * @param   mixed    $value
	 * @return  integer
	 */
	private function _refs($value)
	{
		$refs = 0;

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();
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
	 * Save changes to a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();

		$fields = Request::getVar('fields', array(), 'post');

		$row = new MwHosttype($mwdb);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$insert = false;
		if ($fields['status'] == 'new')
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
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if ($fields['status'] == 'new')
		{
			$result = $row->store($insert);
		}
		else
		{
			$result = $row->update($fields['id']);
		}

		if (!$result)
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$this->setMessage(
			Lang::txt('COM_TOOLS_ITEM_SAVED'),
			'message'
		);

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete a hostname record
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array());

		$mwdb = ToolsHelperUtils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new MwHosttype($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$row->delete($id))
				{
					throw new Exception($row->getError(), 500);
				}
			}
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}
}
