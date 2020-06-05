<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

use Date;
use Lang;

/**
 * Memberships lookup and management
 */
class Memberships
{
	/**
	 * Database instance
	 *
	 * @var  object
	 */
	protected $_db = null;

	/**
	 * Contructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');

		// Load language file
		\App::get('language')->load('com_storefront');
	}

	/**
	 * Get all product type IDs with membership model
	 *
	 * @return  array  membership type IDs
	 */
	public function getMembershipTypes()
	{
		$sql = "SELECT `ptId` FROM `#__storefront_product_types` WHERE `ptModel` = 'membership'";
		$this->_db->setQuery($sql);
		$membershipTypes = $this->_db->loadColumn();

		return $membershipTypes;
	}

	/**
	 * Lookup membership info
	 *
	 * @param   int    $crtId  cart ID
	 * @param   int    $pId    membership product ID
	 * @return  array  membership info
	 */
	public function getMembershipInfo($crtId, $pId)
	{
		$now = Date::toSql();

		$sql = "SELECT `crtmExpires`, IF(`crtmExpires` < '" . $now . "', 0, 1) AS `crtmActive` FROM `#__cart_memberships` WHERE `pId` = " . $this->_db->quote($pId) . " AND `crtId` = " . $this->_db->quote($crtId);
		$this->_db->setQuery($sql);

		return $this->_db->loadAssoc();
	}

	/* ****************************** Static Functions *********************************/

	/**
	 * Find the proper Product Type Subscription Object and return it
	 *
	 * @param   string  $type  Product type
	 * @param   int     $pId   Product ID
	 * @param   int     $uId   User ID
	 * @return  object  Subscription Object. If not found returns a generic subscription object
	 */
	public static function getSubscriptionObject($type, $pId, $uId)
	{
		// Find if there is a corresponding object
		$lookupPath = dirname(dirname(__DIR__)) . DS . 'com_storefront' . DS . 'models' . DS . 'ProductTypes' . DS .'Subscriptions';

		$objectClass = str_replace(' ', '_', ucwords(strtolower($type))) . '_Subscription';

		if (!file_exists($lookupPath . DS . $objectClass . '.php'))
		{
			$objectClass = 'BaseSubscription';
		}

		require_once $lookupPath . DS . $objectClass . '.php';

		return new $objectClass($pId, $uId);
	}

	/**
	 * Calculate and return new expiration date for a SKU
	 *
	 * @param  	string  $currentExpiration  Current subscription expiration (MySQL format)
	 * @param   array   $item               SKU/Cart info
	 * @return  string  Calculated new expiration (MySQL format)
	 */
	public static function calculateNewExpiration($currentExpiration, $item)
	{
		// Calculate correct TTL for the SKU (sku tll * qty)
		$ttl = self::getTtl($item['meta']['ttl'], $item['cartInfo']->qty);

		// Calculate the new expiration date
		if ($currentExpiration && $currentExpiration['crtmActive'])
		{
			// Set the date to the current expiration
			$date = Date::of($currentExpiration['crtmExpires']);
			// Add TTL to the current expiration
			$date->modify('+ ' . $ttl);
		}
		else
		{
			// Get current time
			$date = Date::of('now');
			// Add TTL to the current time
			$date->modify('+ ' . $ttl);
		}

		return $date->toSql();
	}

	/**
	 * Calculate TTL with respect to the quantity
	 *
	 * @param   string  $ttl  single item TTL
	 * @param   int     $qty  number of items
	 * @return  string  combined TTL
	 */
	private static function getTtl($ttl, $qty)
	{
		self::checkTtl($ttl);

		// Split ttl into parts
		$ttlParts = explode(' ', $ttl);
		$ttlParts[0] = $qty * $ttlParts[0];

		$ttl = implode(' ', $ttlParts);
		return $ttl;
	}

	/**
	 * Check TTL format
	 *
	 * @param   string  $ttl  Time To Live
	 * @return  void
	 * @throws  Exception
	 */
	public static function checkTtl($ttl)
	{
		if (!preg_match("/^[1-9]+[0-9]* (year|month|day)+$/i", $ttl))
		{
			throw new \Exception(Lang::txt('Bad TTL formatting. Please use something like 1 DAY, 2 MONTH or 3 YEAR'));
		}
	}

	/**
	 * Lookup membership info by user (almost identical as above)
	 *
	 * @param   int    $uId  user ID
	 * @param   int    $pId  membership product ID
	 * @return  array  membership info
	 */
	public static function getMembershipInfoByUser($uId, $pId)
	{
		$db = \App::get('db');

		$now = Date::of('now')->toSql();
		$sql =  "SELECT `crtmExpires`, IF(`crtmExpires` < '" . $now . "', 0, 1) AS `crtmActive` FROM `#__cart_memberships` m";
		$sql .= " LEFT JOIN `#__cart_carts` c on c.`crtId` = m.`crtId`";
		$sql .= "WHERE m.`pId` = " . $db->quote($pId) . " AND c.`uidNumber` = " . $db->quote($uId);
		$db->setQuery($sql);
		$membershipInfo = $db->loadAssoc();

		return $membershipInfo;
	}
}
