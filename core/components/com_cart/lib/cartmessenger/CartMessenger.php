<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib;

include_once __DIR__ . '/LoggingLevel.php';

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * Logs cart activity and sends emails out as necessary
 */
class CartMessenger
{
	/**
	 * Log file
	 *
	 * @var  string
	 */
	private $logFile;

	/**
	 * Caller
	 *
	 * @var  string
	 */
	private $caller;

	/**
	 * Message
	 *
	 * @var  string
	 */
	private $message;

	/**
	 * Post information
	 *
	 * @var  array
	 */
	private $postback;

	/**
	 * Constructor
	 *
	 * @param   string  $caller
	 * @return  void
	 */
	public function __construct($caller)
	{
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		$logPath = Config::get('log_path', PATH_APP . DS . 'logs');

		$this->logFile = $logPath  . DS . 'cart.log';
		$this->caller  = $caller;
	}

	/**
	 * Set postback
	 *
	 * @param   array  $postback
	 * @return  void
	 */
	public function setPostback($postback)
	{
		$this->postback = $postback;
	}

	/**
	 * Set a message
	 *
	 * @param   string  $msg
	 * @return  void
	 */
	public function setMessage($msg = '')
	{
		$this->message = $msg;
	}

	/**
	 * Log information
	 *
	 * @param   integer  $loggingLevel
	 * @return  mixed
	 */
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
			if (!empty($this->postback))
			{
				$message .= "\n" . "Postback info: " . serialize($this->postback) . "\n";
			}

			$message .= "\n";

			$hzl = new \Hubzero\Log\Writer(
				new \Monolog\Logger(Config::get('application_env')),
				\App::get('dispatcher')
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

	/**
	 * Email completeed order
	 *
	 * @param   object  $transactionInfo
	 * @return  void
	 */
	public function emailOrderComplete($transaction)
	{
		$transactionInfo = $transaction->info;
		$transactionItems = $transaction->items;
		$params = Component::params(Request::getCmd('option'));

		$items = unserialize($transactionInfo->tiItems);

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


		// Check the notes, both SKU-specific and other
		$notes = array();
		foreach ($transactionItems as $item)
		{
			$meta = $item['transactionInfo']->tiMeta;
			if (isset($meta->checkoutNotes) && $meta->checkoutNotes)
			{
				$notes[] = array(
					'label' => $item['info']->pName . ', ' . $item['info']->sSku,
					'notes' => $meta->checkoutNotes
				);
			}
		}

		$genericNotesLabel = '';
		if (!empty($notes))
		{
			$genericNotesLabel = 'Other notes/comments';
		}

		if ($transactionInfo->tiNotes)
		{
			$notes[] = array(
				'label' => $genericNotesLabel,
				'notes' => $transactionInfo->tiNotes);
		}

		if (!empty($notes))
		{
			$summary .= "\n" . 'Notes/Comments: ' . "\n";
			foreach ($notes as $note)
			{
				$summary .= $note['label'];
				if ($note['label'])
				{
					$summary .= ': ';
				}
				$summary .= $note['notes'] . "\n";
			}
		};

		$summary .= "\n\nItems ordered:";
		$summary .= "\n--------------------\n";

		// Initialize low inventory notification
		$lowInventoryNotifySummary = '';

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();

		foreach ($items as $k => $item)
		{
			$itemInfo = (isset($item['info']) ? $item['info'] : array());
			$cartInfo = (isset($item['cartInfo']) ? $item['cartInfo'] : array());
			$itemMeta = (isset($item['meta']) ? $item['meta'] : array());

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
			if ($item['info']->sTrackInventory)
			{
				// get the latest SKU info
				$sInfo = $warehouse->getSkuInfo($item['info']->sId);

				// Set the inventoryNotificationThreshold to 0 is it is not set
				if (!array_key_exists('inventoryNotificationThreshold', $sInfo['meta']))
				{
					$sInfo['meta']['inventoryNotificationThreshold'] = 0;
				}

				if ($sInfo['info']->sTrackInventory &&
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

		// "from" info
		$from = array();
		$from['name']  = Config::get('sitename');
		$from['email'] = Config::get('mailfrom');

		// Email to client
		$clientEmail = 'Thank you for your order at ' . Config::get('sitename') . "!\n\n";
		$clientEmail .= $summary;

		// Plain text email
		$plain = $clientEmail;

		$message = new \Hubzero\Mail\Message();
		$message->setSubject('Your order at ' . $from['name']);
		// Find out where to send it from
		if ($params->get('sendOrderInfoFromEmail') && \Hubzero\Utility\Validate::email($params->get('sendOrderInfoFromEmail')))
		{
			$sendFromEmail = $params->get('sendOrderInfoFromEmail');
		}
		else
		{
			$sendFromEmail = Config::get('mailfrom');
		}
		$message->addFrom(
			$sendFromEmail,
			Config::get('sitename')
		);
		$message->setSender(Config::get('mailfrom'));
		$message->addPart($plain, 'text/plain');

		// Get user's email address
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';
		$uId = \Components\Cart\Models\Cart::getCartUser($transactionInfo->crtId);
		$usr = \Hubzero\User\Profile::getInstance($uId);
		$message->addTo($usr->get('email'));
		$message->setBody($plain);
		$message->send();

		// Email notifications
		$notifyTo = $params->get('sendNotificationTo');
		if (!empty($notifyTo))
		{
			$notifyTo = explode(',', str_replace(' ', '', $notifyTo));

			$notifyEmail = 'There is a new online store order at ' . Config::get('sitename') . "\n\n";
			$notifyEmail .= $summary;

			$plain = $notifyEmail;
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

				$plain = $lowInventoryNotifySummary;

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

	/**
	 * Email error to store admin
	 *
	 * @param   string  $error
	 * @param   string  $errorType
	 * @return  void
	 */
	private function emailError($error, $errorType = null)
	{
		$params = Component::params(Request::getCmd('option'));

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

		if ($errorType != 'LOG')
		{
			$mailMessage .= "\n\n" . 'Please see log for details';
		}

		// Send emails
		Event::trigger('xmessage.onSendMessage', array('store_notifications', $mailSubject, $mailMessage, $from, $adminId, '', null, '', 0, true));
	}
}
