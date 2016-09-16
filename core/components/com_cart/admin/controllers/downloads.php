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
use Components\Cart\Helpers\CartDownload;
use Components\Storefront\Models\Warehouse;

require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Download.php');
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Controller class for knowledge base categories
 */
class Downloads extends AdminController
{
	/**
	 * Display a list of all downloads
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
					'dDownloaded'
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
			),
			'skuRequested' => Request::getState(
				$this->_option . '.' . $this->_controller . '.skuRequested',
				'skuRequested',
				0
			)
		);

		// Is a particular SKU requested?
		$skuRequested = Request::getInt('sku', 0);
		if (!$skuRequested && !empty($this->view->filters['skuRequested'])) {
			$skuRequested = $this->view->filters['skuRequested'];
		}
		if ($skuRequested) {
			$warehouse = new Warehouse();
			$skuInfo = $warehouse->getSkuInfo($skuRequested);
			if ($skuInfo)
			{
				$skuName = $skuInfo['info']->pName . ', ' . $skuInfo['info']->sSku;
			}
			else {
				$skuName = 'Product no longer exists';
			}

			$this->view->filters['skuRequested'] = $skuRequested;
			$this->view->skuRequestedName = $skuName;
		}

		//print_r($this->view->filters); die;

		// Clean filters -- reset to default if needed
		$allowedSorting = array('product', 'dName', 'dDownloaded', 'dStatus');
		if (!in_array($this->view->filters['sort'], $allowedSorting))
		{
			$this->view->filters['sort'] = 'dDownloaded';
		}

		// Get record count
		$this->view->total = CartDownload::getDownloads('count', $this->view->filters);

		// Get records
		$this->view->rows = CartDownload::getDownloads('list', $this->view->filters);

		//print_r($this->view->rows); die;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a list of SKUS with number of downloads
	 *
	 * @return  void
	 */
	public function skuTask()
	{
		// Get filters
		$this->view->filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'pName'
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
		//print_r($this->view->filters);

		// Get record count
		$this->view->total = CartDownload::getDownloadsSku('count', $this->view->filters);

		// Get records
		$this->view->rows = CartDownload::getDownloadsSku('list', $this->view->filters);

		// Output the HTML
		//print_r($this->view); die;
		$this->view->display();
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return     void
	 */
	public function activeTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function inactiveTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of an entry
	 *
	 * @param      integer $state State to set
	 * @return     void
	 */
	public function stateTask($state = 0)
	{
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					($state == 1 ? Lang::txt('COM_CART_SELECT_ACTIVE') : Lang::txt('COM_CART_SELECT_INACTIVE')),
					'error'
			);
			return;
		}

		// Save downloads
		try {
			CartDownload::setStatus($ids, $state);
		}
		catch (\Exception $e)
		{
			\Notify::error($e->getMessage());
			return;
		}

		// Set message
		switch ($state)
		{
			case '1':
				$message = Lang::txt('COM_CART_ACTIVATED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_CART_DEACTIVATED', count($ids));
			break;
		}

		// Redirect
		App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				$message
		);
	}

	/**
	 * Download CSV report (default)
	 *
	 * @return     void
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
			'skuRequested' => Request::getState(
				$this->_option . '.' . $this->_controller . '.skuRequested',
				'skuRequested',
				0
			)
		);
		$rowsRaw = CartDownload::getDownloads('array', $filters);
		$date = date('d-m-Y');

		$rows = array();

		foreach ($rowsRaw as $row)
		{
			$status = 'active';
			if (!$row->dStatus)
			{
				$status = 'inactive';
			}

			if ($row->meta)
			{
				// userInfo
				if (array_key_exists('userInfo', $row->meta) && $row->meta['userInfo'])
				{
					$metaUserInfo = unserialize($row->meta['userInfo']['mtValue']);
					$metaUserInfoCsv = array();
					foreach ($metaUserInfo as $mtK => $mtV)
					{
						if (is_array($mtV))
						{
							$mtV = implode('; ', $mtV);
						}
						$metaUserInfoCsv[] = $mtV;
					}
					$metaUserInfoCsv = implode(', ', $metaUserInfoCsv);
				}

				// eulaAccepted
				if (array_key_exists('eulaAccepted', $row->meta) && $row->meta['eulaAccepted'])
				{
					if ($row->meta['eulaAccepted']['mtValue'])
					{
						$metaEula = 'EULA accepted';
					}
				}
				else {
					$metaEula = '';
				}
			}
			else
			{
				$metaUserInfoCsv = '';
				$metaEula = '';
			}

			$rows[] = array($row->dDownloaded, $row->pName, $row->sSku, $row->dName, $row->username, $row->uId, $metaUserInfoCsv, $metaEula, $row->dIp, $status);
		}

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=cart-downloads-" . $date . ".csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies

		$output = fopen("php://output", "w");
		$row = array('Downloaded', 'Product', 'SKU', 'User', 'Username', 'User ID', 'User Details', 'EULA', 'IP', 'Status');
		fputcsv($output, $row);
		foreach ($rows as $row) {
			fputcsv($output, $row);
		}
		fclose($output);
		die;
	}

	/**
	 * Download CSV report (SKU)
	 *
	 * @return     void
	 */
	public function downloadSkuTask()
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
		$rowsRaw = CartDownload::getDownloadsSku('array', $filters);

		$rows = array();

		foreach ($rowsRaw as $row)
		{
			$rows[] = array($row['pName'], $row['sSku'], $row['downloaded']);
		}

		$date = date('d-m-Y');

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=cart-downloads-sku-" . $date . ".csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies

		$output = fopen("php://output", "w");
		$row = array('Product', 'SKU', 'Downloaded (times)');
		fputcsv($output, $row);
		foreach ($rows as $row) {
			// replace($row) empty vals with n/a
			foreach ($row as $k => $val)
			{
				if (empty($val))
				{
					$row[$k] = 'n/a';
				}
			}
			fputcsv($output, $row);
		}
		fclose($output);
		die;
	}
}