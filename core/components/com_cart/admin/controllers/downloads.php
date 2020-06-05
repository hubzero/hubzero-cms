<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Cart\Helpers\CartDownload;
use Components\Storefront\Models\Warehouse;
use Request;
use Config;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Download.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Warehouse.php';

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
		// Do some filter cleaning
		$setPId = Request::getInt('pId', 0);
		$setSId = Request::getInt('sId', 0);

		if ($setPId)
		{
			Request::setVar('sId', 0);
		}
		elseif ($setSId)
		{
			Request::setVar('pId', 0);
		}

		// Get filters
		$this->view->filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
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
			),
			'report-from' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-from',
				'report-from',
				date('m/d/Y', strtotime('-1 month'))
			),
			'report-to' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-to',
				'report-to',
				date('m/d/Y')
			),
			'uidNumber' => Request::getState(
				$this->_option . '.' . $this->_controller . '.uidNumber',
				'uidNumber',
				0,
				'int'
			),
			'pId' => Request::getState(
				$this->_option . '.' . $this->_controller . '.pId',
				'pId',
				0,
				'int'
			),
			'sId' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sId',
				'sId',
				0,
				'int'
			)
		);

		// Is a particular SKU requested?
		$skuRequested = Request::getInt('sku', 0);
		if (!$skuRequested && !empty($this->view->filters['skuRequested']))
		{
			$skuRequested = $this->view->filters['skuRequested'];
		}
		if ($skuRequested)
		{
			$warehouse = new Warehouse();
			$skuInfo = $warehouse->getSkuInfo($skuRequested);
			if ($skuInfo)
			{
				$skuName = $skuInfo['info']->pName . ', ' . $skuInfo['info']->sSku;
			}
			else
			{
				$skuName = 'Product no longer exists';
			}

			$this->view->filters['skuRequested'] = $skuRequested;
			$this->view->skuRequestedName = $skuName;
		}

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
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.search',
				'search',
				''
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.sort',
				'filter_order',
				'pName'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.limitstart',
				'limitstart',
				0,
				'int'
			),
			'report-from' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.report-from',
				'report-from',
				date('m/d/Y', strtotime('-1 month'))
			),
			'report-to' => Request::getState(
				$this->_option . '.' . $this->_controller . 'sku.report-to',
				'report-to',
				date('m/d/Y')
			)
		);

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
		$ids = Request::getArray('id', array());
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
		try
		{
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
			),
			'report-from' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-from',
				'report-from',
				date('m/d/Y', strtotime('-1 month'))
			),
			'report-to' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-to',
				'report-to',
				date('m/d/Y')
			)
		);

		$dateFrom = date('dMY', strtotime($filters['report-from']));
		$dateTo = date('dMY', strtotime($filters['report-to']));
		$date = date('d-m-Y');

		// If debugging is enabled it seems to want a lot more memory
		ini_set('memory_limit', '1G');
		// Disable output buffering
		if (ob_get_level())
		{
			ob_end_clean();
		}

		// Give the browser some headers so it knows what we're sending
		header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0'); // HTTP 1.1
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename=cart-downloads-' . $date . '(' . $dateFrom . '-' . $dateTo . ').csv');
		header('Expires: 0'); // Proxies
		header('Pragma: no-cache'); // HTTP 1.0

		$output = fopen("php://output", "w");

		$row = array('Downloaded', 'Product', 'SKU', 'User', 'Username', 'User ID', 'User Details', 'EULA', 'IP', 'Status');
		fputcsv($output, $row);

		// flush to get the headers to the browser and give the user the download dialog immediately
		flush();

		//get a count of records
		$rowsCount = CartDownload::getDownloads('count', $filters);

		// in chunks of 5000, grab results and start outputting to filestream
		$filters['limit'] = 5000;
		for ($i = 0; $i < $rowsCount; $i += 5000)
		{
			// get up to another 5000 records
			$filters['start'] = $i;
			$rowsRaw = CartDownload::getDownloads('array', $filters);

			// parse the metadata
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
					else
					{
						$metaEula = '';
					}
				}
				else
				{
					$metaUserInfoCsv = '';
					$metaEula = '';
				}

				// ouput to stream
				fputcsv($output, array($row->dDownloaded, $row->pName, $row->sSku, $row->dName, $row->username, $row->uId, $metaUserInfoCsv, $metaEula, $row->dIp, $status));
				// flush up to 5000 lines of output to browser
				flush();
			}
		}
		// close stream and die
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
			),
			'report-from' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-from',
				'report-from',
				date('m/d/Y', strtotime('-1 month'))
			),
			'report-to' => Request::getState(
				$this->_option . '.' . $this->_controller . '.report-to',
				'report-to',
				date('m/d/Y')
			)
		);
		$rowsRaw = CartDownload::getDownloadsSku('array', $filters);

		$rows = array();

		foreach ($rowsRaw as $row)
		{
			$rows[] = array($row['pName'], $row['sSku'], $row['downloaded']);
		}

		$dateFrom = date('dMY', strtotime($filters['report-from']));
		$dateTo = date('dMY', strtotime($filters['report-to']));
		$date = date('d-m-Y');

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=cart-downloads-sku-" . $date . "(" . $dateFrom . '-' . $dateTo . ").csv");
		// Disable caching
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies

		$output = fopen("php://output", "w");
		$row = array('Product', 'SKU', 'Downloaded (times)');
		fputcsv($output, $row);
		foreach ($rows as $row)
		{
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
