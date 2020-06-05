<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Cron plugin for forum
 */
class plgCronStorefront extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'emailPublishDownNotifications',
				'label'  => Lang::txt('PLG_CRON_STOREFRONT_EMAIL_PUBLISH_DOWN_NOTIFICATIONS'),
				'params' => 'emailPublishDownNotifications'
			),
		);

		return $obj;
	}

	/**
	 * Email SKUs and Products publish down notifications
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function emailPublishDownNotifications(\Components\Cron\Models\Job $job)
	{
		// Get the notification days
		$params = $job->params;
		$days = array();
		if (is_object($params) &&
			($params->get('publish_down_notification1') || $params->get('publish_down_notification2')))
		{
			if ($params->get('publish_down_notification1') &&
				is_numeric($params->get('publish_down_notification1')) &&
				$params->get('publish_down_notification1') > 0)
			{
				$days[] = $params->get('publish_down_notification1');
			}
			if ($params->get('publish_down_notification2') &&
				is_numeric($params->get('publish_down_notification2')) &&
				$params->get('publish_down_notification2') > 0)
			{
				$days[] = $params->get('publish_down_notification2');
			}
		}
		if (!$days)
		{
			return;
		}

		// Database connection
		// We clone the db here so that we can use our prepared statement below without interruption
		$db = clone(App::get('db'));

		$now = Date::of('now')->toSql();
		$maxDays = max($days);

		// *** SKUS

		// Get all SKUs that potentially need a notification sent
		$query = "	SELECT s.sId, s.sSku, s.publish_down, DATEDIFF(`publish_down`, '{$now}') AS daysLeftUntilPublishDown
					FROM `#__storefront_skus` s
					WHERE `publish_down` > '{$now}'
					AND DATEDIFF(`publish_down`, '{$now}') <= {$maxDays}";
		$db->setQuery($query);
		$skus = $db->loadObjectList('sId');

		// Build an index of all SKU notifications that should potentially be sent
		$notificationsIndex = array();
		$affectedSkus = '0';
		foreach ($skus as $sku)
		{
			$skuAffected = false;
			// check each day
			foreach ($days as $day)
			{
				// need to notify for this setting
				if ($day >= $sku->daysLeftUntilPublishDown)
				{
					$notificationsIndex[$sku->sId]['notifyDays'][$day] = true;
					if (!$skuAffected)
					{
						$affectedSkus .= ',' . $sku->sId;
						$skuAffected = true;
					}

					$notificationsIndex[$sku->sId]['publish_down'] = $sku->publish_down;
				}
			}
		}

		// Get all existing notifications about potentially affected SKUs
		$query = "SELECT scope_id, meta FROM `#__notifications` WHERE scope = 'publishDownSku' AND scope_id IN ({$affectedSkus})";
		$db->setQuery($query);
		$notifications = $db->loadObjectList();

		// Clean the $notificationsIndex
		// Go through all notifications and remove the references from the $notificationsIndex for those already sent
		foreach ($notifications as $notification)
		{
			// Get meta
			$meta = unserialize($notification->meta);

			// Unset the index matching the current notification
			if ($meta['publish_down'] == $notificationsIndex[$notification->scope_id]['publish_down'])
			{
				unset($notificationsIndex[$notification->scope_id]['notifyDays'][$meta['days']]);
			}
		}

		// Go through all remaining $notificationsIndex and build the SKU information array
		$skusInfo = array();
		foreach ($notificationsIndex as $k => $notify)
		{
			$notify = $notify['notifyDays'];
			// each day value
			foreach ($notify as $day => $val)
			{
				if ($val)
				{
					$skusInfo[$k] = $skus[$k];
				}
			}
		}

		$skusNotificationsIndex = $notificationsIndex;

		// *** Products

		// Get all Products that potentially need a notification sent
		$query = "	SELECT pId, pName, publish_down, DATEDIFF(`publish_down`, '{$now}') AS daysLeftUntilPublishDown
					FROM `#__storefront_products`
					WHERE `publish_down` > '{$now}'
					AND DATEDIFF(`publish_down`, '{$now}') <= {$maxDays}";
		$db->setQuery($query);
		$products = $db->loadObjectList('pId');

		// Build an index of all Product notifications that should potentially be sent
		$notificationsIndex = array();
		$affectedProducts = '0';
		foreach ($products as $product)
		{
			$productAffected = false;
			// check each day
			foreach ($days as $day)
			{
				// need to notify for this setting
				if ($day >= $product->daysLeftUntilPublishDown)
				{
					$notificationsIndex[$product->pId]['notifyDays'][$day] = true;
					if (!$productAffected)
					{
						$affectedProducts .= ',' . $product->pId;
						$productAffected = true;
					}

					$notificationsIndex[$product->pId]['publish_down'] = $product->publish_down;
				}
			}
		}

		// Get all existing notifications about potentially affected Products
		$query = "SELECT scope_id, meta FROM `#__notifications` WHERE scope = 'publishDownProduct' AND scope_id IN ({$affectedProducts})";
		$db->setQuery($query);
		$notifications = $db->loadObjectList();

		// Clean the $notificationsIndex
		// Go through all notifications and remove the references from the $notificationsIndex for those already sent
		foreach ($notifications as $notification)
		{
			// Get meta
			$meta = unserialize($notification->meta);

			// Unset the index matching the current notification
			if ($meta['publish_down'] == $notificationsIndex[$notification->scope_id]['publish_down'])
			{
				unset($notificationsIndex[$notification->scope_id]['notifyDays'][$meta['days']]);
			}
		}

		// Go through all remaining $notificationsIndex and build the Product information array
		$productsInfo = array();
		foreach ($notificationsIndex as $k => $notify)
		{
			$notify = $notify['notifyDays'];
			// each day value
			foreach ($notify as $day => $val)
			{
				if ($val)
				{
					$productsInfo[$k] = $products[$k];
				}
			}
		}

		$productsNotificationsIndex = $notificationsIndex;

		if ($this->sendNotifications($skusInfo, $productsInfo))
		{
			$this->recordNotifications($skusNotificationsIndex, 'publishDownSku');
			$this->recordNotifications($productsNotificationsIndex, 'publishDownProduct');
		}

		return true;
	}

	/**
	 * Handles the actual sending of emails
	 *
	 * @param  boolean  $skusInfo     [description]
	 * @param  boolean  $productsInfo [description]
	 * @return boolean
	 **/
	private function sendNotifications($skusInfo, $productsInfo)
	{
		// Make sure there is something to send
		if (!$skusInfo && !$productsInfo)
		{
			return;
		}

		$eview = new \Hubzero\Component\View(array(
			'base_path' => Component::path('com_storefront') . DS . 'site',
			'name'      => 'emails',
			'layout'    => 'publish_down_notification'
		));
		$eview->option   = 'com_storefront';
		$eview->skus     = $skusInfo;
		$eview->products = $productsInfo;

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		$sendTo = Component::params('com_cart')->get('sendNotificationTo', false);
		$sendTo = explode(',', str_replace(' ', '', $sendTo));

		// Build message
		$message = App::get('mailer');
		$message->setSubject(Lang::txt('Storefront') . ': ' . Lang::txt('Publish down notifications'))
			->addFrom(Config::get('mailfrom'), Config::get('sitename'))
			->addHeader('X-Component', 'com_storefront')
			->addHeader('X-Component-Object', 'storefront_publish_down_notifications');

		foreach ($sendTo as $email)
		{
			if (\Hubzero\Utility\Validate::email($email))
			{
				$message->addTo($email);
			}
		}

		$message->addPart($plain, 'text/plain');

		// Send mail
		if (!$message->send())
		{
			$this->setError('Failed to mail publish down notifications');
			return false;
		}

		return true;
	}

	/**
	 * Summary
	 *
	 * @param   array   $notificationsIndex
	 * @param   string  $scope
	 * @return  bool
	 */
	private function recordNotifications($notificationsIndex, $scope)
	{
		$values = '';

		foreach ($notificationsIndex as $id => $info)
		{
			$days = $info['notifyDays'];
			foreach ($days as $day => $val)
			{
				if ($values != '')
				{
					$values .= ',';
				}

				$meta = serialize(array('publish_down' => $info['publish_down'], 'days' => $day));

				$values .= "('{$scope}', {$id}, NOW(), '{$meta}')";
			}
		}
		if (! $values)
		{
			return;
		}
		$db = App::get('db');
		$sql = "INSERT INTO `#__notifications` (scope, scope_id, notified, meta) VALUES" . $values;
		$db->setQuery($sql);
		$db->query();

		return true;
	}
}
