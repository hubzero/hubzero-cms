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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class LoggingLevel {
	const INFO = 2;
	const WARN = 1;
	const ERROR = 0;
}

/**
 * Logs cart activity and sends emails out as necessary
 *
 * Long description (if any) ...
 */
class CartMessenger
{
	private $logFile;
	private $caller;

	private $message;
	private $postback;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct($caller)
	{
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		$logPath = Config::get('log_path', PATH_APP . DS . 'logs');
		$this->logFile = $logPath  . DS . 'cart.log';
		$this->caller = $caller;
	}

	public function setPostback($postback)
	{
		$this->postback = $postback;
	}

	public function setMessage($msg = '')
	{
		$this->message = $msg;
	}

	public function log($loggingLevel = 2)
	{
		if (!file_exists($this->logFile))
		{
			try
			{
				Filesystem::write($this->logFile, '');
			}
			catch (\Exception $e)
			{
				$this->emailError($this->logFile, 'NO_LOG');
			}
		}

		if (is_writable($this->logFile))
		{
			$message = 'Log info: ';
			$message .= $this->message;
			if (!empty($this->postback)) {
				$message .= "\n" . "Postback info: " . serialize($this->postback) . "\n";
			}

			$message .= "\n";

			$hzl = new \Hubzero\Log\Writer(
				new \Monolog\Logger(Config::get('application_env')),
				\JDispatcher::getInstance()
			);
			$hzl->useFiles($this->logFile);

			if ($loggingLevel == 0) {
				$hzl->error($this->caller . ': ' . $message);
			} elseif ($loggingLevel == 1) {
				$log = $hzl->warning($this->caller . ': ' . $message);
				return $log;
			} elseif ($loggingLevel == 2) {
				$log = $hzl->info($this->caller . ': ' . $message);
				return $log;
			}

			// If error, needs to send email to admin
			$this->emailError($this->message, 'POSTBACK');
		}
		else {
			$this->emailError($this->logFile, 'LOG');
		}
	}

	public function emailOrderComplete($transactionInfo)
	{
		$params =  Component::params(Request::getVar('option'));

		$items = unserialize($transactionInfo->tiItems);
		//print_r($items); die;

		// Build emails

		// Build order summary
		$summary = 'Order number: ' . $transactionInfo->tId . "\n\n";
		$summary .= "====================\n\n";

		$summary .= 'Subtotal: ' . '$' . number_format($transactionInfo->tiSubtotal, 2) . "\n";
		if (!$transactionInfo->tiShipping)
		{
			$transactionInfo->tiShipping = 0;
		}
		if ($transactionInfo->tiShipping > 0)
		{
			$summary .= 'Shipping and handling: ' . '$' . number_format($transactionInfo->tiShipping, 2) . "\n";
		}
		if (!$transactionInfo->tiTax)
		{
			$transactionInfo->tiTax = 0;
		}
		if ($transactionInfo->tiDiscounts > 0 || $transactionInfo->tiShippingDiscount > 0)
		{
			$summary .= 'Discounts: ' . '$' . number_format($transactionInfo->tiDiscounts + $transactionInfo->tiShippingDiscount, 2) . "\n";
		}
		if ($transactionInfo->tiTax > 0)
		{
			$summary .= 'Tax: ' . '$' . number_format($transactionInfo->tiTax, 2) . "\n";
		}
		$summary .= 'Total: ' . '$' . number_format($transactionInfo->tiTotal, 2) . "\n";

		if (!empty($transactionInfo->tiShippingToFirst))
		{
			$summary .= "\n\nShipping address:";
			$summary .= "\n--------------------\n";
			$summary .= $transactionInfo->tiShippingToFirst . ' ' . $transactionInfo->tiShippingToLast . "\n";
			$summary .= $transactionInfo->tiShippingAddress . "\n";
			$summary .= $transactionInfo->tiShippingCity . ', ' . $transactionInfo->tiShippingState . ' ' . $transactionInfo->tiShippingZip . "\n";
		}

		if ($transactionInfo->tiNotes)
		{
			$summary .= "\n" . 'Notes/Comments: ' . "\n" . $transactionInfo->tiNotes . "\n";
		}

		$summary .= "\n\nItems ordered:";
		$summary .= "\n--------------------\n";

		// Initialize low inventory notification
		$lowInventoryNotifySummary = '';

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();

		foreach ($items as $k => $item)
		{
			$itemInfo = $item['info'];
			$cartInfo = $item['cartInfo'];
			$itemMeta = $item['meta'];

			//print_r($item); die;

			$productType = $warehouse->getProductTypeInfo($itemInfo->ptId)['ptName'];

			// If course, generate a link to the course
			$action = false;
			if ($productType == 'Course')
			{
				$action = ' Go to the course page at: ';
				$action .= Route::url('index.php?option=com_courses', true, -1) . $itemMeta['courseId'] . '/' . $itemMeta['offeringId'];
			}
			elseif ($productType == 'Software Download')
			{
				$action = ' Download at: ';
				$action .= Route::url('index.php?option=com_cart', true, -1) . 'download/' . $transactionInfo->tId . '/' . $itemInfo->sId;

				if (isset($itemMeta['serialManagement']) && $itemMeta['serialManagement'] == 'multiple' && isset($itemMeta['serials']) && !empty($itemMeta['serials']))
				{
					$action .= "\n\t";
					$action .= " Serial number";
					if (count($itemMeta['serials']) > 1)
					{
						$action .= "s";
					}
					$action .= ': ';
					foreach ($itemMeta['serials'] as $serial)
					{
						if (count($itemMeta['serials']) > 1)
						{
							$action .= '\n\t';
						}
						$action .= $serial;
					}
				}
				elseif (isset($itemMeta['serial']) && !empty($itemMeta['serial']))
				{
					$action .= "\n\t";
					$action .= " Serial number: " . $itemMeta['serial'];
				}
			}

			$summary .= "$cartInfo->qty x ";
			$summary .= "$itemInfo->pName";

			if (!empty($item['options']))
			{
				$summary .= '(';
				$optionCount = 0;
				foreach ($item['options'] as $option)
				{
					if ($optionCount)
					{
						$summary .=	', ';
					}
					$summary .= $option;
					$optionCount++;
				}
				$summary .= ')';
			}

			$summary .= ' @ ' . '$' . number_format($itemInfo->sPrice, 2);

			if ($action)
			{
				$summary .= "\n\t";
				$summary .= $action;
			}

			$summary .= "\n";

			// Build low inventory level notifications if needed
			if ($item['info']->sTrackInventory && $item['meta']['inventoryNotificationThreshold'])
			{
				// get the latest SKU info
				$sInfo = $warehouse->getSkuInfo($item['info']->sId);

				if ($sInfo['info']->sTrackInventory &&
					$sInfo['meta']['inventoryNotificationThreshold'] &&
					$sInfo['info']->sInventory <= $sInfo['meta']['inventoryNotificationThreshold']
				)
				{
					$lowInventoryNotifySummary .= "\n\t" . $sInfo['info']->pName;
					if (!empty($sInfo['info']->oName))
					{
						$lowInventoryNotifySummary .= ', ' . $sInfo['info']->oName;
					}
					$lowInventoryNotifySummary .= ': inventory level is ' . $sInfo['info']->sInventory;
				}
			}
		}
		//print_r($summary); die;

		// Get message plugin
		JPluginHelper::importPlugin('xmessage');

		// "from" info
		$from = array();
		$from['name']  = Config::get('sitename');
		$from['email'] = Config::get('mailfrom');

		// Email to client
		$clientEmail = 'Thank you for your order at ' . Config::get('sitename') . "!\n\n";
		$clientEmail .= $summary;

		require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php');
		$to = array(\Components\Cart\Models\Cart::getCartUser($transactionInfo->crtId));
		Event::trigger('onSendMessage', array('store_notifications', 'Your order at ' . $from['name'], $clientEmail, $from, $to, '', null, '', 0, true));

		// Email notifications
		$notifyTo = $params->get('sendNotificationTo');
		if (!empty($notifyTo)) {

			$notifyTo = explode(',', str_replace(' ', '', $notifyTo));

			$notifyEmail = 'There is a new online store order at ' . Config::get('sitename') . "\n\n";
			$notifyEmail .= $summary;
			// Plain text email
			$eview = new \Hubzero\Component\View(array(
				'name' => 'emails',
				'layout' => 'order_notify'
			));
			//$eview->option     = $this->_option;
			//$eview->controller = $this->_controller;
			$eview->message = $notifyEmail;

			$plain = $eview->loadTemplate();
			$plain = str_replace("\n", "\r\n", $plain);

			$message = new \Hubzero\Mail\Message();
			$message->setSubject('ORDER NOTIFICATION: New order at ' . $from['name']);
			$message->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename')
			);
			$message->addPart($plain, 'text/plain');
			foreach ($notifyTo as $email)
			{
				if (\Hubzero\Utility\Validate::email($email))
				{
					$message->addTo($email);
				}
			}
			$message->setBody($plain);
			$message->send();

			// Send the low inventory notification if needed
			if ($lowInventoryNotifySummary)
			{
				$lowInventoryNotifyEmail = 'Low inventory level notification from ' . Config::get('sitename') . "\n\n";
				$lowInventoryNotifyEmail .= $lowInventoryNotifySummary;

				// Plain text email
				$eview = new \Hubzero\Component\View(array(
					'name' => 'emails',
					'layout' => 'order_notify'
				));
				$eview->message = $lowInventoryNotifySummary;

				$plain = $eview->loadTemplate();
				$plain = str_replace("\n", "\r\n", $plain);

				$message = new \Hubzero\Mail\Message();
				$message->setSubject('LOW INVENTORY NOTIFICATION: low inventory levels at ' . $from['name']);
				$message->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename')
				);
				$message->addPart($plain, 'text/plain');
				foreach ($notifyTo as $email)
				{
					if (\Hubzero\Utility\Validate::email($email))
					{
						$message->addTo($email);
					}
				}
				$message->setBody($plain);
				$message->send();
			}
		}
	}

	private function emailError($error, $errorType = NULL)
	{
		$params = Component::params(Request::getVar('option'));

		// Get message plugin
		JPluginHelper::importPlugin('xmessage');
		$dispatcher = JDispatcher::getInstance();

		// "from" info
		$from = array();

		$from['name']  = Config::get('sitename');
		$from['email'] = Config::get('mailfrom');

		// get admin id
		$adminId = array($params->get('storeAdminId'));

		$mailMessage = Date::getRoot() . "\n";

		if ($errorType == 'POSTBACK')
		{
			$mailSubject = ': Error processing postback payment.';
			$mailMessage = 'There was an error processing payment postback:' . "\n\n";
		}
		elseif ($errorType == 'LOG')
		{
			$mailSubject = ': Error logging payment postback information.';
			$mailMessage = 'Log file is not writable.' . "\n\n";
		}
		elseif ($errorType == 'NO_LOG')
		{
			$mailSubject = ': Error logging payment postback information.';
			$mailMessage = 'Log file does not exist' . "\n\n";
		}
		else
		{
			$mailSubject = 'Cart error';
		}

		$mailMessage .= $error;

		if ($errorType != 'LOG') {
			$mailMessage .= "\n\n" . 'Please see log for details';
		}

		// Send emails
		Event::trigger('onSendMessage', array('store_notifications', $mailSubject, $mailMessage, $from, $adminId, '', null, '', 0, true));
	}

}
