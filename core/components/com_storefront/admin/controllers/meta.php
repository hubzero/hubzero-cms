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

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;

/**
 * Controller class for knowledge base categories
 */
class Meta extends AdminController
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
			'access' => -1
		);
		$this->view->filters['sort'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		//print_r($this->view->filters);

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->products('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->products('list', $this->view->filters);

		//print_r($this->view->rows); die;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		//print_r($this->view); die;
		$this->view->display();
	}

	/**
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();

		if (is_object($row))
		{
			$this->view->row = $row;
			$this->view->task = 'edit';

			$id = $row->getId();
		}
		else
		{
			// Incoming
			$id = Request::getVar('id', 0);

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load product
			$this->view->row = $obj->product($id);
		}

		// Load meta
		$this->view->meta = $obj->getProductMeta($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// See what layout to load
		if ($this->view->row->getType() == 30)
		{
			// Software
			$layout = 'edit-software';
		}
		else {
			App::abort(404, Lang::txt('No meta for this product'));
		}

		// Output the HTML
		$this->view
			->setLayout($layout)
			->display();
	}

	/**
	 * Save a category and come back to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a product
	 *
	 * @param   boolean  $redirect  Redirect the page after saving
	 * @return  void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		$obj = new Archive();

		// Save product
		try {
			$obj->setProductMeta($fields['pId'], $fields);
		}
		catch (\Exception $e)
		{
			\Notify::error($e->getMessage());
			// Get the product
			$product = $obj->product($fields['pId']);
			$this->editTask($product);
			return;
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=products&task=edit&id=' . Request::getInt('id', 0),
				Lang::txt('COM_STOREFRONT_PRODUCT_SAVED')
			);
			return;
		}

		// Get the product
		$product = $obj->product($fields['pId']);

		$this->editTask($product);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=products&task=edit&id=' . Request::getInt('id', 0)
		);
	}
}

