<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'restrictions.php';

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Sku;
use Components\Storefront\Admin\Helpers\RestrictionsHelper;
use Request;
use Route;
use User;
use App;

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
		$sId = Request::getInt('id');
		if (empty($sId))
		{
			$sId = Request::getArray('sId', array(0));
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

		// Get record count
		$this->view->filters['return'] = 'count';
		$this->view->total = RestrictionsHelper::getPermittedSkuUsers($this->view->filters, $sId);

		// Get records
		$this->view->filters['return'] = 'list';
		$this->view->rows = RestrictionsHelper::getPermittedSkuUsers($this->view->filters, $sId);

		// Output the HTML
		$this->view->display();
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
		$ids = Request::getArray('id', array());
		$sId = Request::getInt('sId');

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
		$sId = Request::getInt('id', '');
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
		$users = Request::getString('users', '');

		$users = explode(',', $users);
		$noHubUserMatch = array();
		$matched = 0;
		foreach ($users as $user)
		{
			$user = trim($user);

			$usr = User::getInstance($user);
			$uId = $usr->get('id', 0);
			if ($uId)
			{
				RestrictionsHelper::addPermittedSkuUser($uId, $sId);
				$matched++;
			}
			else
			{
				// Are we adding by username?
				if ($user && is_string($user))
				{
					RestrictionsHelper::addPermittedSkuUser($uId, $sId, $user);
					$matched++;
				}
				else
				{
					$noHubUserMatch[] = $user;
				}
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
		$csvFile = Request::getArray('csvFile', false, 'files');

		$sId = Request::getInt('sId', '');
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
						$key = trim($line[0]);

						if (!$key)
						{
							$ignored[] = $line[0];
							continue;
						}

						$usr = User::getInstance($key);
						$uId = $usr->get('id', 0);

						if ($uId)
						{
							$key = null;
						}

						$res = RestrictionsHelper::addSkuUser($uId, $sId, $key);
						if ($res)
						{
							$inserted++;
						}
						else
						{
							$skipped[] = $usr;
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
