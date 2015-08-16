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
class StorefrontControllerProducts extends \Hubzero\Component\AdminController
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

		//print_r($this->view->filters);

		$obj = new StorefrontModelArchive();

		// Get record count
		$this->view->total = $obj->products('count', $this->view->filters);

		// Get records
		$this->view->rows = $obj->products('list', $this->view->filters);

		// For all records here get SKUs
		$skus = new stdClass();
		$warehouse = new StorefrontModelWarehouse();
		foreach ($this->view->rows as $r)
		{
			$key = $r->pId;
			$allSkus = $warehouse->getProductSkus($r->pId, 'all', false);

			// Count how many active and how many inactive SKUs there are
			$skuCounter = new stdClass();
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

		//print_r($skus); die;
		$this->view->skus = $skus;

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
		JRequest::setVar('hidemainmenu', 1);

		$obj = new StorefrontModelArchive();
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
			$id = JRequest::getVar('id', array(0));

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load product
			$this->view->row = $obj->product($id);
		}

		// Get product active groups
		$this->view->productOptionGroups = $this->view->row->getOptionGroups();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->config = $this->config;

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

		$obj = new StorefrontModelArchive();

		// Save product
		try {
			$product = $obj->updateProduct($fields['pId'], $fields);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			// Get the product
			$product = $obj->product($fields['pId']);
			$this->editTask($product);
			return;
		}

		// when saving product need to check all active product SKUs and disable those that do not verify anymore
		$skus = $product->getSkus();
		$skusDisabled = false;
		foreach ($skus as $sku)
		{
			if ($sku->getActiveStatus())
			{
				try
				{
					$sku->verify();
				}
				catch (Exception $e)
				{
					$sku->unpublish();
					$skusDisabled = true;
				}
			}
		}

		$disabledSkuMessage = 'Some product SKUs were unpublished because of the recent product update. Check each SKU to fix the issues.';
		if ($skusDisabled && !$redirect)
		{
			JFactory::getApplication()->enqueueMessage($disabledSkuMessage, 'warning');
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option='.$this->_option . '&controller=' . $this->_controller,
				JText::_('COM_STOREFRONT_PRODUCT_SAVED')
			);

			if ($skusDisabled)
			{
				JFactory::getApplication()->enqueueMessage($disabledSkuMessage, 'warning');
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
		$step = JRequest::getInt('step', 1);
		$step = (!$step) ? 1 : $step;

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
				JRequest::checkToken() or jexit('Invalid Token');

				// Incoming
				$pIds = JRequest::getVar('pIds', 0);
				//print_r($sId); die;

				// Make sure we have IDs to work with
				if (empty($pIds))
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

					foreach ($pIds as $pId)
					{
						// Delete SKU
						try
						{
							$product = $obj->product($pId);
							$product->delete();
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

					$msg = "Product(s) deleted";
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
	 * Set the access level of an article to 'public'
	 *
	 * @return     void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the access level of an article to 'registered'
	 *
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the access level of an article to 'special'
	 *
	 * @return     void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access level of an article
	 *
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function accessTask($access=0)
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_STOREFRONT_NO_ID'),
				'error'
			);
			return;
		}

		// Load the article
		$row = new StorefrontModelCategory($id);
		$row->set('access', $access);

		// Check and store the changes
		if (!$row->store(true))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
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

		//print_r($ids); die;

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

		foreach ($ids as $pId)
		{
			// Save product
			try {
				$obj->updateProduct($pId, array('state' => $state));
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return;
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

		// Redirect
		$this->setRedirect(
			'index.php?option='.$this->_option . '&controller=' . $this->_controller,
			$message
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
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

