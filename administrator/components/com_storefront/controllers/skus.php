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

/**
 * Controller class for knowledge base categories
 */
class StorefrontControllerSkus extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get product ID
		$pId = JRequest::getVar('id', array(0));
		$this->view->pId = $pId;

		// Get product
		$product = new StorefrontModelProduct($pId);
		$this->view->product = $product;

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
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$obj = new StorefrontModelArchive();

		// Get record count
		$this->view->total = $obj->skus('count', $pId, $this->view->filters);

		// Get records
		$this->view->rows = $obj->skus('list', $pId, $this->view->filters);

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
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$obj = new StorefrontModelArchive();

		if (is_object($row))
		{
			$id = $row->getId();
			// If this is a new SKU, set product ID
			if (!$id)
			{
				$pId = JRequest::getVar('pId');
				$row->setProductId($pId);
			}
			$this->view->row = $row;
			$this->view->task = 'edit';
		}
		else
		{
			// Incoming
			$id = JRequest::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Get corrent SKU instance
			$pId = JRequest::getVar('pId');
			$row = $this->instantiateSkuForProduct($id, $pId);
			$this->view->row = $row;
		}
		//print_r($row); die;

		// Get product's info
		$pId = $row->getProductId();
		$warehouse = new StorefrontModelWarehouse();
		$pInfo = $warehouse->getProductInfo($pId, true);
		$this->view->pInfo = $pInfo;
		//print_r($pInfo); die;

		// Get available product-defined option groups and options
		$this->view->allOptions = $obj->getProductOptions($pId);

		// Get current SKU options
		$this->view->options = $row->getOptions();

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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		//print_r($fields); die;

		// Get the proper SKU
		$pId = JRequest::getVar('pId');
		$sku = $this->instantiateSkuForProduct($fields['sId'], $pId);

		// Save SKU
		$obj = new StorefrontModelArchive();
		try {
			$sku = $obj->updateSku($sku, $fields);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			// Get the sku
			$this->editTask($sku);
			return;
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option='.$this->_option . '&controller=' . $this->_controller . '&task=display&id=' . JRequest::getInt('pId', 0),
				JText::_('COM_STOREFRONT_SKU_SAVED')
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
		$step = JRequest::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

		$pId = JRequest::getVar('pId');

		// What step are we on?
		switch ($step)
		{
			case 1:
				JRequest::setVar('hidemainmenu', 1);

				// Incoming
				$id = JRequest::getVar('id', array(0));
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
				JRequest::checkToken() or jexit('Invalid Token');

				// Incoming
				$sIds = JRequest::getVar('sId', 0);
				//print_r($sId); die;

				// Make sure we have an ID to work with
				if (empty($sIds))
				{
					$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
						JText::_('COM_STOREFRONT_NO_ID'),
						'error'
					);
					return;
				}

				$delete = JRequest::getVar('delete', 0);

				$msg = "Delete canceled";
				$type = 'error';
				if ($delete)
				{
					// Do the delete
					$obj = new StorefrontModelArchive();

					foreach ($sIds as $sId)
					{
						// Delete SKU
						try
						{
							$sku = $this->instantiateSkuForProduct($sId, $pId);
							$sku->delete();
						}
						catch (Exception $e)
						{
							$this->setRedirect(
								'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId,
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
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $pId,
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
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$pId = JRequest::getVar('pId', 0);

		// Check for an ID
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				($state == 1 ? JText::_('COM_STOREFRONT_SELECT_PUBLISH') : JText::_('COM_STOREFRONT_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		$obj = new StorefrontModelArchive();

		foreach ($ids as $sId)
		{
			// Save SKU
			try
			{
				$sku = $this->instantiateSkuForProduct($sId, $pId);
				$obj->updateSku($sku, array('state' => $state));
			}
			catch (Exception $e)
			{
				$error = true;
			}
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = JText::sprintf('COM_STOREFRONT_ARCHIVED', count($ids));
			break;
			case '1':
				$message = JText::sprintf('COM_STOREFRONT_PUBLISHED', count($ids));
			break;
			case '0':
				$message = JText::sprintf('COM_STOREFRONT_UNPUBLISHED', count($ids));
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
		$this->setRedirect(
			'index.php?option='.$this->_option . '&controller=' . $this->_controller . '&task=display&id=' . $pId,
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
		$this->setRedirect(
			'index.php?option='.$this->_option . '&controller=' . $this->_controller . '&task=display&id=' . JRequest::getInt('pId', 0)
		);
	}

	/**
	 * Instantiate the correct Sku for a given product
	 *
	 * @return     StorefrontModelProduct
	 */
	private function instantiateSkuForProduct($sId, $pId)
	{
		$warehouse = new StorefrontModelWarehouse();

		// If existing SKU, load the SKU, find the product, get the product type
		if ($sId)
		{
			$skuInfo = $warehouse->getSkuInfo($sId);
			$productType = $warehouse->getProductTypeInfo($skuInfo['info']->ptId)['ptName'];
		}
		// For the new SKU load the product the SKU is being created for, get the product type
		else {
			$product = new StorefrontModelProduct($pId);
			$productType = $warehouse->getProductTypeInfo($product->getType())['ptName'];
		}

		// Initialize the correct SKU based on the product type
		if (!empty($productType) && $productType == 'Software Download')
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'SoftwareSku.php');
			$sku = new StorefrontModelSoftwareSku($sId);
		}
		else
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');
			$sku = new StorefrontModelSku($sId);
		}

		// If this is a new SKU, set the product ID
		if (!$sId)
		{
			$sku->setProductId($pId);
		}

		return $sku;
	}
}

