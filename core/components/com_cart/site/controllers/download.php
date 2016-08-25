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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Site\Controllers;

use Request;
use Components\Cart\Models\Cart;
use Components\Storefront\Models\Warehouse;
use User;
//use Hubzero\User\Group;
use Hubzero\Access\Group as Accessgroup;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Product viewing controller class
 */
class Download extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{

		$this->warehouse = new Warehouse();

		$this->juser = User::getRoot();

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->login('Please login to continue');
			return;
		}

		parent::execute();
	}

	/**
	 * Serve the file
	 *
	 * @param		$pId
	 * @return     	void
	 */
	public function displayTask()
	{
		// Get the transaction ID
		$tId  = Request::getInt('task', '');

		// Get the SKU ID
		$sId = Request::getVar('p0');

		// Get the landing page flag
		$direct = Request::getVar('p1');

		// Check if the transaction is complete and belongs to the user and is active
		$transaction = Cart::getTransactionFacts($tId);
		$transaction = $transaction->info;

		$tStatus = $transaction->tStatus;
		$crtId = $transaction->crtId;

		// get cart user
		$cartUser = Cart::getCartUser($crtId);
		$currentUser = $this->juser->id;

		// Error if needed
		if ($tStatus !== 'completed')
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_TRANSACTION_NOT_COMPLETED'), 'error'));
			$this->messageTask($messages);
			return;
		}
		elseif ($cartUser != $currentUser)
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_NOT_AUTHORIZED'), 'error'));
			$this->messageTask($messages);
			return;

		}

		// Check if the product is valid and downloadable; find the file
		$warehouse = new Warehouse();
		$sku = $warehouse->getSkuInfo($sId);
		$productType = $warehouse->getProductTypeInfo($sku['info']->ptId);
		$downloadFile = $sku['meta']['downloadFile'];

		// Error if needed
		if ($productType['ptName'] != 'Software Download' || empty($downloadFile))
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_FILE_NOT_DOWNLOABLE'), 'error'));
			$this->messageTask($messages);
			return;
		}

		$db = \App::get('db');

		// Check if there is a limit on how many times the product can be downloaded

		// Get the number of downloads allowed
		$allowedDownloads = $sku;
		if (isset($sku['meta']['downloadLimit']) && $sku['meta']['downloadLimit'] && is_numeric($sku['meta']['downloadLimit']))
		{
			$sql = "SELECT COUNT(`dId`) FROM `#__cart_downloads` WHERE `uId` = {$currentUser} AND `sId` = {$sId}";
			$db->setQuery($sql);
			$downloadsCount = $db->loadResult();

			if ($downloadsCount >= $sku['meta']['downloadLimit'])
			{
				$messages = array(array('Download limit exceeded', 'error'));
				$this->messageTask($messages);
				return;
			}
		}

		// Path and file name
		$storefrontConfig = Component::params('com_storefront');
		$dir = $storefrontConfig->get('downloadFolder', '/site/protected/storefront/software');

		$file = PATH_APP . $dir . DS . $downloadFile;

		if (!file_exists($file))
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_FILE_NOT_FOUND'), 'error'));
			$this->messageTask($messages);
			return;
		}

		if (!$direct)
		{
			$this->landingTask($tId, $sId);
			return;
		}

		// Log the download
		$sql = "INSERT INTO `#__cart_downloads` SET
				`uId` = " . $currentUser . ",
				`sId` = " . $sId . ",
				`dIp` = INET_ATON(" . $db->quote(Request::getClientIp()) . "),
				`dDownloaded` = NOW()";
		$db->setQuery($sql);
		$db->query();
		$dId = $db->insertid();

		// Save the meta data
		$userGroups = User::getAuthorisedGroups();
		$meta = array();
		$ignoreGroups = array('public', 'registered');
		foreach ($userGroups as $groupId)
		{
			$group = Accessgroup::one($groupId);
			if (!in_array(strtolower($group->get('title')), $ignoreGroups))
			{
				$meta[$groupId] = $group->get('title');
			}
		}

		$sql = "INSERT INTO `#__cart_meta` SET
				`scope_id` = " . $dId . ",
				`scope` = 'download',
				`mtKey` = 'userInfo',
				`mtValue` = '" . serialize($meta) . "'";
		$db->setQuery($sql);
		$db->query();

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
		exit;
	}

	public function landingTask($tId, $sId)
	{
		$this->setView('download', 'landing');
		$this->view->sId = $sId;
		$this->view->tId = $tId;
		$this->view->display();
	}

	public function messageTask($notifications)
	{
		$this->setView('download', 'message');
		$this->view->notifications = $notifications;
		$this->view->display();
	}

	/**
	 * Redirect to login page
	 *
	 * @return void
	 */
	private function login($message = '')
	{
		$return = base64_encode($_SERVER['REQUEST_URI']);
		$this->setRedirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}
}

