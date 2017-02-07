<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'restrictions.php';

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Sku;
use Components\Storefront\Admin\Helpers\RestrictionsHelper;

/**
 * Controller class
 */
class Restrictions extends AdminController
{
	/**
	 * Display a list of all users
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get SKU ID
		$sId = Request::getVar('id');
		if (empty($sId))
		{
			$sId = Request::getVar('sId', array(0));
		}
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
					'uId'
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

		RestrictionsHelper::getSkuUsers($this->view->filters, $sId);

		// Get record count
		$this->view->filters['return'] = 'count';
		$this->view->total = RestrictionsHelper::getSkuUsers($this->view->filters, $sId);

		// Get records
		$this->view->filters['return'] = 'list';
		$this->view->rows = RestrictionsHelper::getSkuUsers($this->view->filters, $sId);

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
			Route::url('index.php?option=' . $this->_option . '&controller=skus&task=display&id=' . Request::getVar('pId', 0), false)
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
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', 0);
		$sId = Request::getVar('sId');
		//print_r($ids); die;

		RestrictionsHelper::removeUsers($ids);

		$msg = "User(s) deleted";
		$type = 'message';

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=dispaly&id=' . $sId, false),
			$msg,
			$type
		);
	}

	/**
	 * Display a form for a new entry
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$sId = Request::getVar('id', '');
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
	 * Add users
	 *
	 * @return  void
	 */
	public function addusersTask()
	{
		$sId = Request::getInt('sId', '');
		$users = Request::getVar('users', '');

		$users = explode(',', $users);
		$noHubUserMatch = array();
		$matched = 0;
		foreach ($users as $user)
		{
			$user = trim($user);
			$usr = new \Hubzero\User\Profile($user);
			$uId = $usr->get('uidNumber');
			if ($uId)
			{
				RestrictionsHelper::addSkuUser($uId, $sId);
				$matched++;
			}
			else
			{
				$noHubUserMatch[] = $user;
			}
		}

		$this->view->matched = $matched;
		$this->view->noUserMatch = $noHubUserMatch;
		$this->view->display();
	}

	/**
	 * Display a form for uploading
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		$sId = Request::getInt('id', '');

		$this->view->sId = $sId;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Handle upload of a CSV
	 *
	 * @return  void
	 */
	public function uploadcsvTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// See if we have a file
		$csvFile = Request::getVar('csvFile', false, 'files', 'array');

		$sId = Request::getVar('sId', '');
		$inserted = 0;
		$skipped = array();
		$ignored = array();

		if (isset($csvFile['name']) && $csvFile['name'])
		{
			if (($handle = fopen($csvFile['tmp_name'], "r")) !== false)
			{
				while (($line = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!empty($line[0]))
					{
						$usr = new \Hubzero\User\Profile(trim($line[0]));
						$uId = $usr->get('uidNumber');
						if ($uId)
						{
							$res = RestrictionsHelper::addSkuUser($uId, $sId);
							if ($res)
							{
								$inserted++;
							}
							else
							{
								$skipped[] = $usr;
							}
						}
						else
						{
							$ignored[] = $line[0];
						}
					}
				}
				fclose($handle);


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
		$this->view->inserted = $inserted;
		$this->view->skipped = $skipped;
		$this->view->ignored = $ignored;
		$this->view->display();
	}
}
