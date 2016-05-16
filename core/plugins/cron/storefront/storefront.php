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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

/**
 * Cron plugin for storefront
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
				'name'   => 'notifyPublish',
				'label'  => Lang::txt('PLG_CRON_STOREFRONT_NOTIFY_PUBLISH'),
				'params' => 'notifyPublishParams'
			),
		);

		return $obj;
	}

	/**
	 * Email notifications if products' publish down dates are approaching.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function notifyPublish(\Components\Cron\Models\Job $job)
	{
		// Get the parameter
		$params = $job->get('params');
		$notifyDaysBefore = $params->get('storefront_notify_time_before_publish_down');

		// Get all products and SKUs which publish down date is approaching and due for notification

		// PRODUCTS
		$productsDigest = array();
		$sql = "SELECT p.pId, p.pName, p.publish_down, DATEDIFF(p.`publish_down`, NOW()) AS daysLeftUntilPublishDown,
				TIMEDIFF(p.`publish_down`, ADDDATE(NOW(), INTERVAL {$notifyDaysBefore} DAY)) AS timeToNotificationToBeSent
				FROM `#__storefront_products` p
				WHERE p.`pActive` = 1 AND p.`publish_down` > NOW()
				HAVING timeToNotificationToBeSent <= 0";

		$db = \App::get('db');
		$db->setQuery($sql);
		$db->execute();
		$products = $db->loadObjectList();

		foreach ($products as $product)
		{
			//print_r($product); die;
			// Check if any of the notifications have been sent out already
			$sql = "SELECT * FROM #__notifications WHERE scope = 'plgCronStorefrontProductPublishDown' AND `scope_id` = {$product->pId} AND `meta` = '{$product->publish_down}'";

			$db->setQuery($sql);
			$db->execute();
			if (!$db->getNumRows())
			{
				$productsDigest[] = $product; // Add the item for the notification

				// Log the notification
				$sql = "INSERT INTO `#__notifications` SET scope = 'plgCronStorefrontProductPublishDown', `scope_id` = {$product->pId}, `meta` = '{$product->publish_down}', notified = NOW()";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		//print_r($productsDigest); die;

		// SKUs
		$skusDigest = array();
		$sql = "SELECT s.sId, s.sSku, s.publish_down, DATEDIFF(s.`publish_down`, NOW()) AS daysLeftUntilPublishDown,
				TIMEDIFF(s.`publish_down`, ADDDATE(NOW(), INTERVAL {$notifyDaysBefore} DAY)) AS timeToNotificationToBeSent
				FROM `#__storefront_skus` s
				WHERE s.`sActive` = 1 AND s.`publish_down` > NOW()
				HAVING timeToNotificationToBeSent <= 0";

		$db->setQuery($sql);
		$db->execute();
		$skus = $db->loadObjectList();

		foreach ($skus as $sku)
		{
			// Check if any of the notifications have been sent out already
			$sql = "SELECT * FROM #__notifications WHERE scope = 'plgCronStorefrontSkuPublishDown' AND `scope_id` = {$sku->sId} AND `meta` = '{$sku->publish_down}'";

			$db->setQuery($sql);
			$db->execute();
			if (!$db->getNumRows())
			{
				$skusDigest[] = $sku; // Add the item for the notification

				// Log the notification
				$sql = "INSERT INTO `#__notifications` SET scope = 'plgCronStorefrontSkuPublishDown', `scope_id` = {$sku->sId}, `meta` = '{$sku->publish_down}', notified = NOW()";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		// Plain text email
		$eview = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'site',
			'name'      => 'emails',
			'layout'    => 'publish_down_notification'
		));
		$eview->products = $productsDigest;
		$eview->skus = $skusDigest;

		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		$message = new \Hubzero\Mail\Message();
		$message->setSubject('STOREFRONT PUBLISH DOWN NOTIFICATION');
		$message->addFrom(
			Config::get('mailfrom'),
			Config::get('sitename')
		);
		$message->addPart($plain, 'text/plain');

		$params = Component::params('com_cart');
		$notifyTo = $params->get('sendNotificationTo');

		if (!empty($notifyTo) && (!empty($productsDigest) || !empty($skusDigest)))
		{
			$notifyTo = explode(',', str_replace(' ', '', $notifyTo));
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

		return true;
	}
}