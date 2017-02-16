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

namespace Components\Cart\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Cart\Helpers\CartOrders;
use Components\Cart\Models\Cart;
use Components\Storefront\Models\Warehouse;
use Hubzero\User\Profile;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Orders.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Controller class for knowledge base categories
 */
class Orders extends AdminController
{
	/**
	 * Display a list of all orders
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
					'tLastUpdated'
			),
			'sort_Dir' => Request::getState(
					$this->_option . '.' . $this->_controller . '.sortdir',
					'filter_order_Dir',
					'DESC'
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
			),
			'report-notes' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-notes',
				'report-notes',
				0,
				'int'
			)
		);

		// Get record count
		$this->view->filters['count'] = true;
		$this->view->total = Cart::getAllTransactions($this->view->filters);

		// Get records
		$this->view->filters['count'] = false;
		$this->view->filters['userInfo'] = true;
		$this->view->rows = Cart::getAllTransactions($this->view->filters);

		if (!$this->view->rows)
		{
			$this->view->rows = array();
		}

		// update with total and items ordered
		foreach ($this->view->rows as $r)
		{
			$tInfo = Cart::getTransactionInfo($r->tId);
			$r->tiTotal = $tInfo->tiTotal;
			$tiItemsQty = sizeof(unserialize($tInfo->tiItems));
			$r->tiItemsQty = $tiItemsQty;
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * View the order
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		// Incoming
		$id = Request::getVar('id', array(0));

		// Get transaction info
		$tInfo = Cart::getTransactionInfo($id);

		$tItems = unserialize($tInfo->tiItems);

		foreach ($tItems as $item)
		{
			// Check if the product is still available
			$warehouse = new Warehouse();
			$skuInfo = $warehouse->getSkuInfo($item['info']->sId);
			if (!$skuInfo)
			{
				// product no longer available
				$item['info']->available = false;
			}
			else
			{
				$item['info']->available = true;
			}
		}

		$tInfo->tiItems = $tItems;

		// Get user info
		$userId = Cart::getCartUser($tInfo->crtId);
		$user = Profile::getInstance($userId);

		$this->view->user = $user;
		$this->view->tInfo = $tInfo;
		$this->view->tId = $id;

		$this->view
			->setLayout('view')
			->display();
	}

	/**
	 * Display a list of all orders
	 *
	 * @return  void
	 */
	public function itemsTask()
	{
		// Get filters
		$this->view->filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'tLastUpdated'
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

		// Get orders count
		$this->view->total = CartOrders::getItemsOrdered('count', $this->view->filters);

		// Get orders
		$orders = CartOrders::getItemsOrdered('list', $this->view->filters);

		foreach ($orders as $order)
		{
			$orderItems = unserialize(Cart::getTransactionInfo($order->tId)->tiItems);
			$order->itemInfo = $orderItems[$order->sId];
		}

		$this->view->rows = $orders;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Download CSV report (default)
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Get filters
		$filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'dDownloaded'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'report-notes' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-notes',
				'report-notes',
				0,
				'int'
			)
		);

		// request array to be returned
		$filters['returnFormat'] = 'array';
		$filters['userInfo'] = true;
		$rowsRaw = Cart::getAllTransactions($filters);
		$date = date('d-m-Y');

		$rows = array();

		foreach ($rowsRaw as $row)
		{
			$rows[] = array($row['tId'], $row['tLastUpdated'], $row['name'], $row['uidNumber']);
		}

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=cart-orders" . $date . ".csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies

		$output = fopen("php://output", "w");
		$row = array('Order ID', 'Order Placed', 'Purchased By', 'Purchased By (userId)');
		fputcsv($output, $row);
		foreach ($rows as $row) {
			fputcsv($output, $row);
		}
		fclose($output);
		die;
	}

	/**
	 * Download CSV report (default)
	 *
	 * @return  void
	 */
	public function downloadOrdersTask()
	{
		// Get filters
		$filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'dDownloaded'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get orders, request array to be returned
		$orders = CartOrders::getItemsOrdered('list', $filters);

		foreach ($orders as $order)
		{
			$orderItems = unserialize(Cart::getTransactionInfo($order->tId)->tiItems);
			$order->itemInfo = $orderItems[$order->sId];
		}
		$rowsRaw = $orders;
		$date = date('d-m-Y');

		$rows = array();

		foreach ($rowsRaw as $row)
		{
			$itemInfo = $row->itemInfo['info'];
			$rows[] = array($row->sId, $itemInfo->pName . ', ' . $itemInfo->sSku, $row->tiQty, $row->tiPrice, $row->tId, $row->tLastUpdated, $row->Name, $row->uidNumber);
		}

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=cart-items-ordered" . $date . ".csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies

		$output = fopen("php://output", "w");
		$row = array('SKU ID', 'Product', 'QTY', 'Price', 'Order ID', 'Order Placed', 'Purchased By', 'Purchased By (userId)');
		fputcsv($output, $row);
		foreach ($rows as $row)
		{
			fputcsv($output, $row);
		}
		fclose($output);
		die;
	}
}
