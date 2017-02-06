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

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'restrictions.php');

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

	public function uploadTask()
	{
		$sId = Request::getInt('id', '');

		$this->view->sId = $sId;

		// Output the HTML
		$this->view->display();
	}

	public function uploadcsvTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// See if we have a file
		$csvFile = Request::getVar('csvFile', false, 'files', 'array');

		$sId = Request::getVar('sId', '');

		if (isset($csvFile['name']) && $csvFile['name'] && $csvFile['type'] == 'text/csv')
		{
			if (($handle = fopen($csvFile['tmp_name'], "r")) !== FALSE)
			{
				$inserted = 0;
				$skipped = array();
				$ignored = array();
				while (($line = fgetcsv($handle, 1000, ",")) !== FALSE)
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
							else {
								$skipped[] = $usr;
							}
						}
						else {
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
		else {
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

