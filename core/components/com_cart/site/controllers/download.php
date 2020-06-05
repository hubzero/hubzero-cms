<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

use Request;
use Components\Cart\Models\Cart;
use Components\Storefront\Models\Warehouse;
use User;
//use Hubzero\User\Group;
use Hubzero\Access\Group as Accessgroup;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Warehouse.php';

/**
 * Product viewing controller class
 */
class Download extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->warehouse = new Warehouse();

		$this->user = User::getInstance();

		// Check if they're logged in
		if ($this->user->get('guest'))
		{
			$this->login('Please login to continue');
			return;
		}

		parent::execute();
	}

	/**
	 * Serve the file
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get the transaction ID
		$tId  = Request::getInt('task', '');

		// Get the SKU ID
		$sId = Request::getString('p0');

		// Get the landing page flag
		$direct = Request::getString('p1');

		// Check if the transaction is complete and belongs to the user and is active and the SKU requested is valid
		$transaction = Cart::getTransactionFacts($tId);
		$transactionExistingItems = $transaction->items;
		$transaction = $transaction->info;
		$transactionItems = unserialize($transaction->tiItems);
		$tStatus = $transaction->tStatus;
		$crtId = $transaction->crtId;

		// get cart user
		$cartUser = Cart::getCartUser($crtId);
		$currentUser = $this->user->id;

		// Error if needed
		if ($tStatus !== 'completed')
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_TRANSACTION_NOT_COMPLETED'), 'error'));
			$this->messageTask($messages);
			return;
		}
		// Transaction requested doesn't belong to the user
		elseif ($cartUser != $currentUser)
		{
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_NOT_AUTHORIZED'), 'error'));
			$this->messageTask($messages);
			return;
		}
		// The SKU requested is not in the transaction
		elseif (!array_key_exists($sId, $transactionItems))
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
			$messages = array(array(Lang::txt('COM_CART_DOWNLOAD_FILE_NOT_DOWNLOADABLE'), 'error'));
			$this->messageTask($messages);
			return;
		}

		$db = \App::get('db');

		// Check if there is a limit on how many times the product can be downloaded

		// Get the number of downloads allowed
		if (isset($sku['meta']['downloadLimit']) && $sku['meta']['downloadLimit'] && is_numeric($sku['meta']['downloadLimit']))
		{
			$sql = "SELECT COUNT(`dId`) FROM `#__cart_downloads` WHERE `uId` = {$currentUser} AND `sId` = {$sId} AND `dStatus` > 0";
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
				`dIp` = INET_ATON(" . $db->quote(Request::ip()) . "),
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

		if ($mta = User::getState('metadata'))
		{
			$meta = array_merge($meta, $mta);
		}

		$sql = "INSERT INTO `#__cart_meta` SET
				`scope_id` = " . $dId . ",
				`scope` = 'download',
				`mtKey` = 'userInfo',
				`mtValue` = '" . serialize($meta) . "'";
		$db->setQuery($sql);
		$db->query();

		// Figure out if the EULA was accepted
		$itemTransactionInfoMeta = $transactionExistingItems[$sId]['transactionInfo']->tiMeta;
		$eulaAccepted = ($itemTransactionInfoMeta && property_exists($itemTransactionInfoMeta, 'eulaAccepted') && $itemTransactionInfoMeta->eulaAccepted) ? true : false;

		if ($eulaAccepted)
		{
			$sql = "INSERT INTO `#__cart_meta` SET
					`scope_id` = " . $dId . ",
					`scope` = 'download',
					`mtKey` = 'eulaAccepted',
					`mtValue` = '" . $eulaAccepted . "'";
			$db->setQuery($sql);
			$db->query();
		}

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
		exit;
	}

	/**
	 * Display landing page
	 *
	 * @param   integer  $tId
	 * @param   integer  $sId
	 * @return  void
	 */
	public function landingTask($tId, $sId)
	{
		$this->setView('download', 'landing');
		$this->view->sId = $sId;
		$this->view->tId = $tId;
		$this->view->display();
	}

	/**
	 * Display a message
	 *
	 * @param   array  $notifications
	 * @return  void
	 */
	public function messageTask($notifications)
	{
		$this->setView('download', 'message');
		$this->view->notifications = $notifications;
		$this->view->display();
	}

	/**
	 * Redirect to login page
	 *
	 * @param   string  $message
	 * @return  void
	 */
	private function login($message = '')
	{
		$return = base64_encode($_SERVER['REQUEST_URI']);
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}
}
