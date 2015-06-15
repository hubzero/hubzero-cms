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

class LoggingLevel
{
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

		$this->logFile = PATH_APP . DS . 'site' . DS . 'com_cart' . DS . 'cart.log';
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
		if (is_writable($this->logFile))
		{
			$message = 'Log info: ';
			if (!empty($this->postback))
			{
				$message = $this->message . "\n" . "Postback info: " . serialize($this->postback) . "\n";
			}

			$message .= "\n";

			$hzl = new \Hubzero\Log\Writer(
				new \Monolog\Logger(Config::get('application_env')),
				Event::getRoot()
			);
			$hzl->useFiles($this->logFile);

			if ($loggingLevel == 0)
			{
				$hzl->error($this->caller . ': ' . $message);
			}
			elseif ($loggingLevel == 1)
			{
				$log = $hzl->warning($this->caller . ': ' . $message);
				return $log;
			}
			elseif ($loggingLevel == 2)
			{
				$log = $hzl->info($this->caller . ': ' . $message);
				return $log;
			}

			// If error, needs to send email to admin
			$this->emailError($this->message, 'POSTBACK');
		}
		else
		{
			$this->emailError($this->logFile, 'LOG');
		}
	}

	public function emailOrderComplete($transactionInfo)
	{
		$params = Component::params(Request::getVar('option'));

		$items = unserialize($transactionInfo->tiItems);
		$meta = unserialize($transactionInfo->tiMeta);
		//print_r($items); die;

		// Build emails

		// Build order summary
		$summary = 'Order summary:';
		$summary .= "\n====================\n\n";

		$summary .= 'Order number: ' . $transactionInfo->tId . "\n\n";

		$summary .= 'Order subtotal: ' . money_format('%n', $transactionInfo->tiSubtotal) . "\n";
		if (!$transactionInfo->tiShipping)
		{
			$transactionInfo->tiShipping = 0;
		}
		if ($transactionInfo->tiShipping > 0)
		{
			$summary .= 'Shipping and handling: ' . money_format('%n', $transactionInfo->tiShipping) . "\n";
		}
		if (!$transactionInfo->tiTax)
		{
			$transactionInfo->tiTax = 0;
		}
		if ($transactionInfo->tiDiscounts > 0 || $transactionInfo->tiShippingDiscount > 0)
		{
			$summary .= 'Discounts: ' . money_format('%n', $transactionInfo->tiDiscounts + $transactionInfo->tiShippingDiscount) . "\n";
		}
		if ($transactionInfo->tiTax > 0)
		{
			$summary .= 'Tax: ' . money_format('%n', $transactionInfo->tiTax) . "\n";
		}
		$summary .= 'Order total: ' . money_format('%n', $transactionInfo->tiTotal) . "\n";

		if (!empty($transactionInfo->tiShippingToFirst))
		{
			$summary .= "\n\nShipping address:";
			$summary .= "\n--------------------\n";
			$summary .= $transactionInfo->tiShippingToFirst . ' ' . $transactionInfo->tiShippingToLast . "\n";
			$summary .= $transactionInfo->tiShippingAddress . "\n";
			$summary .= $transactionInfo->tiShippingCity . ', ' . $transactionInfo->tiShippingState . ' ' . $transactionInfo->tiShippingZip . "\n";
		}

		$summary .= "\n\nItems ordered:";
		$summary .= "\n--------------------\n";

		foreach ($items as $k => $item)
		{
			$itemInfo = $item['info'];
			$cartInfo = $item['cartInfo'];

			// If course, generate a link to the course
			$action = false;
			if ($itemInfo->ptId == 20)
			{
				$action = ' Go to the course page at: ' .
				$action .= Route::url('index.php?option=com_courses/' . $item['meta']['courseId'] . '/' . $item['meta']['offeringId'], true, -1);
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

			$summary .= ' @ ' . money_format('%n', $itemInfo->sPrice);

			if ($action)
			{
				$summary .= "\n\t";
				$summary .= $action;
			}

			$summary .= "\n";
		}

		//print_r($summary); die;

		// "from" info
		$from = array();
		$from['name']  = Config::get('sitename');
		$from['email'] = Config::get('mailfrom');

		// Email to admin
		$adminEmail = "There is a new online store order: \n\n";
		$adminEmail .= $summary;

		// Admin email
		$to = array($params->get('storeAdminId'));
		Event::trigger('xmessage.onSendMessage', array('store_notifications', 'New order at ' . $from['name'], $adminEmail, $from, $to, '', null, '', 0, true));

		// Email to client
		$clientEmail = 'Thank you for your order at ' .  Config::get('sitename') . "!\n\n";
		$clientEmail .= $summary;

		require_once(PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');
		$to = array(CartModelCart::getCartUser($transactionInfo->crtId));

		Event::trigger('xmessage.onSendMessage', array('store_notifications', 'Your order at ' . $from['name'], $clientEmail, $from, $to, '', null, '', 0, true));
	}

	private function emailError($error, $errorType = NULL)
	{
		$params = Component::params(Request::getVar('option'));

		// "from" info
		$from = array();

		$from['name']  = Config::get('sitename');
		$from['email'] = Config::get('mailfrom');

		// get admin id
		$adminId = array($params->get('storeAdminId'));

		$mailMessage = Date::toSql() . "\n";

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
		else
		{
			$mailSubject = 'Cart error';
		}

		$mailMessage .= $error;

		if ($errorType != 'LOG')
		{
			$mailMessage .= "\n\n" . 'Please see log for details';
		}

		// Send emails
		Event::trigger('xmessage.onSendMessage', array('store_notifications', $mailSubject, $mailMessage, $from, $adminId, '', null, '', 0, true));
	}
}