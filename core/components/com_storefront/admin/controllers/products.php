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
use Components\Storefront\Models\Warehouse;
use Components\Storefront\Models\Product;
use Hubzero\Html\Builder\Access;
use Components\Cart\Helpers\CartDownload;

require_once PATH_CORE . DS. 'components' . DS . 'com_cart' . DS . 'helpers' . DS . 'Download.php';
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Warehouse.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Product.php');

/**
 * Controller class for knowledge base categories
 */
class Products extends AdminController
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
		$this->view->total = $obj->products('count', $this->view->filters);

		// Get records
		$this->view->rows = $obj->products('list', $this->view->filters);

		// For all records here get SKUs
		$skus = new \stdClass();
		$warehouse = new Warehouse();
		foreach ($this->view->rows as $r)
		{
			$key = $r->pId;
			$allSkus = $warehouse->getProductSkus($r->pId, 'all', false);

			// Count how many active and how many inactive SKUs there are
			$skuCounter = new \stdClass();
			$skuCounter->active = 0;
			$skuCounter->inactive = 0;
			foreach ($allSkus as $skuInfo)
			{
				if ($skuInfo->sActive)
				{
					$skuCounter->active++;
				}
				else
				{
					$skuCounter->inactive++;
				}
			}
			$skus->$key = $skuCounter;
		}

		$this->view->skus = $skus;

		// access groups
		$accessGroups = array();
		if ($this->config->get('productAccess'))
		{
			$ag = \Hubzero\Access\Group::all()->rows();
			$accessGroups[0] = 'None';
			foreach ($ag as $obj)
			{
				$accessGroups[$obj->get('id')] = $obj->get('title');
			}
		}
		else
		{
			$ag = Access::assetgroups();
			$accessGroups[0] = 'All';
			foreach ($ag as $obj)
			{
				$accessGroups[$obj->value] = $obj->text;
			}
		}
		$this->view->ag = $accessGroups;

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->display();
	}

	/**
	 * Create a new entry
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();
		// Get types
		$this->view->types = $obj->getProductTypes();

		// Get collections
		$this->view->collections = $obj->collections('list', array('sort' => 'cType'));

		// Get all option groups
		$this->view->optionGroups = $obj->optionGroups('list', array('sort' => 'ogName'));

		if (is_object($row))
		{
			$id = $row->getId();
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

			// Load product
			$this->view->row = $obj->product($id);
		}

		// Get product option groups
		$this->view->productOptionGroups = $this->view->row->getOptionGroups();
		$this->view->config = $this->config;

		// Check if meta is needed for this product
		$pType = null;
		if ($this->view->row->getTypeInfo())
		{
			$pType = $this->view->row->getTypeInfo()->name;
		}
		$this->view->metaNeeded = false;
		// Only software needs meta
		if ($pType == 'Software Download')
		{
			$this->view->metaNeeded = true;

			// Get number of downloads
			$downloaded = CartDownload::countProductDownloads($id);
			$this->view->downloaded = $downloaded;
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
	 * Save an entry and return to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
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

		$obj = new Archive();

		// Save product
		try
		{
			$product = new Product($fields['pId']);

			if (isset($fields['pName']))
			{
				$product->setName($fields['pName']);
			}
			if (isset($fields['pAlias']) && $fields['pAlias'])
			{
				$product->setAlias($fields['pAlias']);
			}
			if (isset($fields['pDescription'])) {
				$product->setDescription($fields['pDescription']);
			}
			if (isset($fields['pFeatures'])) {
				$product->setFeatures($fields['pFeatures']);
			}
			if (isset($fields['pTagline']) && $fields['pTagline'])
			{
				$product->setTagline($fields['pTagline']);
			}
			if (isset($fields['access']))
			{
				$product->setAccessLevel($fields['access']);
			}
			if (isset($fields['state']))
			{
				$product->setActiveStatus($fields['state']);
			}
			if (isset($fields['ptId']))
			{
				$product->setType($fields['ptId']);
			}
			if (isset($fields['pAllowMultiple']))
			{
				$product->setAllowMultiple($fields['pAllowMultiple']);
			}

			if (!isset($fields['collections'])) {
				$fields['collections'] = array();
			}
			$product->setCollections($fields['collections']);

			if (!isset($fields['optionGroups'])) {
				$fields['optionGroups'] = array();
			}
			$product->setOptionGroups($fields['optionGroups']);
			$product->setPublishTime($fields['publish_up'], $fields['publish_down']);
			$product->save();

			$accessgroups = Request::getVar('accessgroups', array(), 'post');
			$product->setAccessGroups($accessgroups);
		}
		catch (\Exception $e)
		{
			\Notify::error($e->getMessage());
			// Get the product
			//$product = $obj->product($fields['pId']);
			$this->editTask($product);
			return;
		}

		$warnings = $product->getMessages();

		if ($warnings && !$redirect)
		{
			foreach ($warnings as $warning)
			{
				\Notify::warning($warning);

			}
		}

		if ($redirect)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_STOREFRONT_PRODUCT_SAVED')
			);

			if ($warnings)
			{
				foreach ($warnings as $warning)
				{
					\Notify::warning($warning);
				}
			}
			return;
		}

		$this->editTask($product);
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
				$this->view->pIds = $id;

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
				$pIds = Request::getVar('pIds', 0);

				// Make sure we have IDs to work with
				if (empty($pIds))
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
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

					foreach ($pIds as $pId)
					{
						// Delete SKU
						try
						{
							$product = $obj->product($pId);
							$product->delete();
						}
						catch (\Exception $e)
						{
							App::redirect(
								Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId),
								$e->getMessage(),
								$type
							);
							return;
						}
					}

					$msg = "Product(s) deleted";
					$type = 'message';
				}

				// Set the redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId),
					$msg,
					$type
				);
				break;
		}
	}

	/**
	 * Set the access level of an article to 'public'
	 *
	 * @return  void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the access level of an article to 'registered'
	 *
	 * @return  void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the access level of an article to 'special'
	 *
	 * @return  void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access level of an article
	 *
	 * @param   integer  $access  Access level to set
	 * @return  void
	 */
	public function accessTask($access=0)
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_STOREFRONT_NO_ID'),
				'error'
			);
			return;
		}

		// Load the article
		$row = new Category($id);
		$row->set('access', $access);

		// Check and store the changes
		if (!$row->store(true))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				$row->getError(),
				'error'
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param   integer  $state  State to set
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				($state == 1 ? Lang::txt('COM_STOREFRONT_SELECT_PUBLISH') : Lang::txt('COM_STOREFRONT_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		$obj = new Archive();

		foreach ($ids as $pId)
		{
			// Save product
			try {
				$product = new Product($pId);
				$product->setActiveStatus($state);
				$product->save();
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

			$message = 'Product could not be ' . $action;
			if (sizeof($ids) > 1)
			{
				$message = 'Some products could not be ' . $action;
			}
			$type = 'error';
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
			$message,
			$type
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
		);
	}
}