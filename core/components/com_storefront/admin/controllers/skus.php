<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Components\Storefront\Models\Sku;
use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Warehouse;
use Components\Cart\Helpers\CartDownload;
use Request;
use Config;
use Route;
use Lang;
use App;

require_once \Component::path('com_cart') . DS . 'helpers' . DS . 'Download.php';

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
		$pId = Request::getInt('id');
		if (empty($pId))
		{
			$pId = Request::getArray('pId', array(0));
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
	 * @param   object  $row
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
				$pId = Request::getInt('pId');
				$row->setProductId($pId);
			}
			$this->view->row = $row;
			$this->view->task = 'edit';
		}
		else
		{
			// Incoming
			$id = Request::getArray('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Get correct SKU instance
			$pId = Request::getInt('pId');

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
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up'] = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}

		$pId = Request::getInt('pId');

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
		try
		{
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
		Notify::success(Lang::txt('COM_STOREFRONT_SKU_SAVED'));

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

		$pId = Request::getInt('pId');

		// What step are we on?
		switch ($step)
		{
			case 1:
				Request::setVar('hidemainmenu', 1);

				// Incoming
				$id = Request::getArray('id', array(0));
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
				$sIds = Request::getInt('sId', 0);

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

				$delete = Request::getInt('delete', 0);

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
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$pId = Request::getInt('pId', 0);

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
			if (count($ids) > 1)
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
	 * @return  void
	 */
	public function cancelTask()
	{
		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display&id=' . Request::getInt('pId', 0), false)
		);
	}
}
