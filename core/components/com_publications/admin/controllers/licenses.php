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

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;

/**
 * Manage publication licenses
 */
class Licenses extends AdminController
{
	/**
	 * List resource types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = Request::getState(
			$this->_option . '.licenses.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start']    = Request::getState(
			$this->_option . '.licenses.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim(Request::getState(
			$this->_option . '.licenses.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim(Request::getState(
			$this->_option . '.licenses.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir'] = trim(Request::getState(
			$this->_option . '.licenses.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Instantiate an object
		$rt = new \Components\Publications\Tables\License($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new type
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}
			else
			{
				$id = 0;
			}

			// Load the object
			$this->view->row = new \Components\Publications\Tables\License($this->database);
			$this->view->row->loadLicense($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Push some styles to the template
		$this->css();

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit&id[]=' . $fields['id'], false);

		// Initiate extended database class
		$row = new \Components\Publications\Tables\License($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			App::redirect($url);
			return;
		}

		$row->customizable 	= Request::getInt('customizable', 0, 'post');
		$row->agreement 	= Request::getInt('agreement', 0, 'post');
		$row->apps_only 	= Request::getInt('apps_only', 0, 'post');
		$row->active 		= Request::getInt('active', 0, 'post');
		$row->icon			= $row->icon ? $row->icon : '/core/components/com_publications/site/assets/img/logos/license.gif';

		if (!$row->id)
		{
			$row->ordering = $row->getNextOrder();
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Redirect to edit view?
		if ($redirect)
		{
			App::redirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED')
			);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED')
			);
		}
		return;
	}

	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders licenses
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');

		// Load row
		$row = new \Components\Publications\Tables\License($this->database);
		$row->loadLicense( (int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Makes one license default
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function makedefaultTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$id = Request::getVar('id', array(0), '', 'array');

		if (count($id) > 1)
		{
			$this->addComponentMessage(Lang::txt('COM_PUBLICATIONS_LICENSE_SELECT_ONE'), 'error');
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Initialize
		$row = new \Components\Publications\Tables\License($this->database);

		$id = intval($id[0]);

		// Load row
		$row->loadLicense( $id );

		// Make default
		$row->main = 1;

		// Save
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Fix up all other licenses
		$row->undefault($id);

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_MADE_DEFAULT')
		);
	}

	/**
	 * Change license status
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function changestatusTask($dir = 0)
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new \Components\Publications\Tables\License($this->database);

		foreach ($ids as $id)
		{
			if (intval($id))
			{
				// Load row
				$row->loadLicense( $id );
				$row->active = $row->active == 1 ? 0 : 1;

				// Save
				if (!$row->store())
				{
					$this->addComponentMessage($row->getError(), 'error');
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
					);
					return;
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_SUCCESS_LICENSE_PUBLISHED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false));
	}
}
