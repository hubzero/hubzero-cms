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

use Components\Storefront\Models\Sku;
use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Warehouse;
use Components\Cart\Helpers\CartDownload;

require_once PATH_CORE . DS. 'components' . DS . 'com_cart' . DS . 'helpers' . DS . 'Download.php';

/**
 * Controller class for knowledge base categories
 */
class Skus extends AdminController
{
	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get product ID
		$pId = Request::getVar('id');
		if (empty($pId))
		{
			$pId = Request::getVar('pId', array(0));
		}
		$this->view->pId = $pId;

		// Get product
		$product = new Product($pId);
		$this->view->product = $product;

		// Get filters
		$this->view->filters = array(
			// Get sorting variables
				'sort' => Request::getState(
						$this->_option . '.' . $this->_controller . '.sort',
						'filter_order',
						'title'
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

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->skus('count', $pId, $this->view->filters);

		// Get records
		$this->view->rows = $obj->skus('list', $pId, $this->view->filters);

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
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a SKU
	 *
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();

		if (is_object($row))
		{
			$id = $row->getId();
			// If this is a new SKU, set product ID
			if (!$id)
			{
				$pId = Request::getVar('pId');
				$row->setProductId($pId);
			}
			$this->view->row = $row;
			$this->view->task = 'edit';
		}
		else
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Get correct SKU instance
			$pId = Request::getVar('pId');

			if ($id)
			{
				$row = Sku::getInstance($id);
			}
			elseif ($pId)
			{
				// create new SKU
				$row = Sku::newInstance($pId);
			}
			else
			{
				throw new \Exception('SKU was not found');
			}
			$this->view->row = $row;
		}

		// Get product's info
		$pId = $row->getProductId();
		$warehouse = new Warehouse();
		$pInfo = $warehouse->getProductInfo($pId, true);
		$this->view->pInfo = $pInfo;
		//print_r($pInfo); die;

		// Get available product-defined option groups and options
		$this->view->allOptions = $obj->getProductOptions($pId);

		// Get current SKU options
		$this->view->options = $row->getOptions();

		// Get number of downloads
		$downloaded = CartDownload::countSkuDownloads($id);
		$this->view->downloaded = $downloaded;

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
	public function saveTask($redirect = true)
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up'] = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}
		//print_r($fields); die;

		$pId = Request::getVar('pId');
		// Get the proper SKU
		if ($fields['sId'])
		{
			$sku = Sku::getInstance($fields['sId']);
		}
		elseif ($pId)
		{
			// create new SKU
			$sku = Sku::newInstance($pId);
		}

		// Save SKU
		$obj = new Archive();
		try {
			$sku = $obj->updateSku($sku, $fields);
		}
		catch (\Exception $e)
		{
			Notify::error($e->getMessage());
			$this->editTask($sku);
			return;
		}

		if ($redirect)
		{
			// Redirect
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&id=' . Request::getInt('pId', 0), false),
					Lang::txt('COM_STOREFRONT_SKU_SAVED')
			);
			return;
		}

		$this->editTask($sku);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Incoming
		$step = Request::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		$pId = Request::getVar('pId');

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Incoming
				$id = Request::getVar('id', array(0));
				if (!is_array($id) && !empty($id))
				{
					$id = array($id);
				}
				$this->view->sId = $id;

				$this->view->pId = $pId;

				// Set any errors
				if ($this->getError())
				{
					$this->view->setError($this->getError());
				}

				// Output the HTML
				$this->view->display();
			break;

			case 2:
				// Check for request forgeries
				Request::checkToken() or jexit('Invalid Token');

				// Incoming
				$sIds = Request::getVar('sId', 0);
				//print_r($sId); die;

				// Make sure we have an ID to work with
				if (empty($sIds))
				{
					App::redirect(
							Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
							Lang::txt('COM_STOREFRONT_NO_ID'),
							'error'
					);
					return;
				}

				$delete = Request::getVar('delete', 0);

				$msg = "Delete canceled";
				$type = 'error';
				if ($delete)
				{
					// Do the delete
					$obj = new Archive();

					foreach ($sIds as $sId)
					{
						// Delete SKU
						try
						{
							$sku = Sku::getInstance($sId);
							$sku->delete();
						}
						catch (\Exception $e)
						{
							App::redirect(
									Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId, false),
									$e->getMessage(),
									$type
							);
							return;
						}
					}

					$msg = "SKU(s) deleted";
					$type = 'message';
				}

				// Set the redirect
				App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId, false),
						$msg,
						$type
				);
			break;
		}
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
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$pId = Request::getVar('pId', 0);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					($state == 1 ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH') : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH')),
					'error'
			);
			return;
		}

		// Update record(s)
		$obj = new Archive();

		foreach ($ids as $sId)
		{
			// Save SKU
			try
			{
				$sku = Sku::getInstance($sId);
				$obj->updateSku($sku, array('state' => $state));
			}
			catch (\Exception $e)
			{
				$error = true;
			}
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_STOREFRONT_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_STOREFRONT_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_STOREFRONT_UNPUBLISHED', count($ids));
			break;
		}

		$type = 'message';

		if (isset($error) && $error)
		{
			switch ($state)
			{
				case '1':
					$action = 'published';
					break;
				case '0':
					$action = 'unpublished';
					break;
			}

			$message = 'SKU could not be ' . $action;
			if (sizeof($ids) > 1)
			{
				$message = 'Some SKUs could not be ' . $action;
			}
			$type = 'error';
		}

		// Redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&id=' . $pId, false),
				$message,
				$type
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&id=' . Request::getInt('pId', 0), false)
		);
	}
}

