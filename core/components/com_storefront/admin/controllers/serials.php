<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Serials.php';

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Sku;
use Components\Storefront\Helpers\Serials as SerialsHelper;
use Request;
use Config;
use Route;
use App;

ini_set("auto_detect_line_endings", true);

/**
 * Controller class for knowledge base categories
 */
class Serials extends AdminController
{
	/**
	 * Display a list of all users
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get SKU ID
		$sId = Request::getInt('sId');
		$this->view->sId = $sId;

		// Get SKU
		$sku = Sku::getInstance($sId);
		$this->view->sku = $sku;

		// Get filters
		$this->view->filters = array(
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'srId'
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
		//print_r($this->view->filters); die;

		// Get record count
		$this->view->filters['return'] = 'count';
		$this->view->total = SerialsHelper::getSkuSerials($this->view->filters, $sId);

		// Get records
		$this->view->filters['return'] = 'list';
		$this->view->rows = SerialsHelper::getSkuSerials($this->view->filters, $sId);

		// Output the HTML
		$this->view->display();
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
			Route::url('index.php?option=' . $this->_option . '&controller=skus&task=edit&id=' . Request::getInt('sId', 0), false)
		);
	}

	/**
	 * Remove an entry
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getInt('srId', 0);
		$sId = Request::getInt('sId');
		//print_r($ids); die;

		$deletedMessage = SerialsHelper::delete($ids);

		if ($deletedMessage)
		{
			$msg = $deletedMessage->message;
			$type = $deletedMessage->type;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&sId=' . $sId, false),
			$msg,
			$type
		);
	}

	/**
	 * Create an entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$sId = Request::getInt('sId', 0);
		$this->view->sId = $sId;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add serials
	 *
	 * @return  void
	 */
	public function addserialsTask()
	{
		$sId = Request::getInt('sId', '');
		$serials = Request::getString('serials', '');

		$serials = explode(',', $serials);
		foreach ($serials as $serial)
		{
			\Components\Storefront\Helpers\Serials::add($serial, $sId);
		}
	}

	/**
	 * Upload
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		$sId = Request::getInt('sId', 0);
		$this->view->sId = $sId;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Upload a CSV file
	 *
	 * @return  void
	 */
	public function uploadcsvTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// See if we have a file
		$csvFile = Request::getArray('csvFile', false, 'files');

		$sId = Request::getInt('sId', 0);

		if (isset($csvFile['name']) && $csvFile['name'] && $csvFile['type'] == 'text/csv')
		{
			if (($handle = fopen($csvFile['tmp_name'], "r")) !== false)
			{
				$inserted = 0;
				$skipped = array();
				$ignored = array();

				while (($line = fgetcsv($handle, 1000, ",")) !== false)
				{
					if (!empty($line[0]))
					{
						$serial = trim($line[0]);

						$res = SerialsHelper::add($serial, $sId);
						if ($res)
						{
							$inserted++;
						}
						else
						{
							$skipped[] = $serial;
						}

					}
				}
				fclose($handle);

				$this->view->inserted = $inserted;
				$this->view->skipped = $skipped;
				$this->view->ignored = $ignored;
			}
			else
			{
				$this->view->setError('Could not read the file.');
			}
		}
		else
		{
			$this->view->setError('No file or bad file was uploaded. Please make sure you upload the CSV formated file.');
		}

		// Output the HTML
		$this->view->sId = $sId;
		$this->view->display();
	}
}
