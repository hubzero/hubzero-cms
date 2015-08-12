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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product viewing controller class
 */
class CartControllerDownload extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$this->warehouse = new StorefrontModelWarehouse();

		$this->juser = JFactory::getUser();

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
		$tId  = JRequest::getInt('task', '');

		// Get the SKU ID
		$sId = JRequest::getVar('p0');

		// Check if the transaction is complete and belongs to the user and is active
		include_once(JPATH_COMPONENT . DS . 'models' . DS . 'Cart.php');
		$transaction = CartModelCart::getTransactionFacts($tId);
		$transaction = $transaction->info;

		$tStatus = $transaction->tStatus;
		$crtId = $transaction->crtId;

		// get cart user
		$cartUser = CartModelCart::getCartUser($crtId);
		$currentUser = $this->juser->id;

		// Error if needed
		if ($tStatus !== 'completed')
		{
			JError::raiseError(401, JText::_('COM_CART_DOWNLOAD_TRANSACTION_NOT_COMPLETED'));
			return;
		}
		elseif ($cartUser != $currentUser)
		{
			JError::raiseError(401, JText::_('COM_CART_DOWNLOAD_NOT_AUTHORIZED'));
			return;
		}

		// Check if the product is valid and downloadable; find the file

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();
		$sku = $warehouse->getSkuInfo($sId);
		$productType = $sku['info']->ptId;
		$downloadFile = $sku['meta']['downloadFile'];

		// Error if needed
		if ($productType != 30 || empty($downloadFile))
		{
			JError::raiseError(400, JText::_('COM_CART_DOWNLOAD_FILE_NOT_DOWNLOABLE'));
			return;
		}

		$db = JFactory::getDBO();

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
		$dir = JPATH_ROOT . DS . 'media' . DS . 'software';
		$file = $dir . DS . $downloadFile;

		if (!file_exists($file))
		{
			JError::raiseError(404, JText::_('COM_CART_DOWNLOAD_FILE_NOT_FOUND'));
			return;
		}

		// Log the download
		$sql = "INSERT INTO `#__cart_downloads` SET
				`uId` = " . $currentUser . ",
				`sId` = " . $sId . ",
				`dDownloaded` = NOW()";
		$db->setQuery($sql);
		$db->query();

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
		exit;
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
			JRoute::_('index.php?option=com_users&view=login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}
}

