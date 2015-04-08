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
 * @author    Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Memberships lookup and management
 *
 */
class StorefrontModelMemberships
{
	// Database instance
	var $_db = NULL;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();

		// Load language file
		Lang::load('com_storefront');
	}

	/**
	 * Get all product type IDs with membership model
	 *
	 * @param  void
	 * @return array		membership type IDs
	 */
	public function getMembershipTypes()
	{
		$sql = "SELECT `ptId` FROM `#__storefront_product_types` WHERE `ptModel` = 'membership'";
		$this->_db->setQuery($sql);
		$membershipTypes = $this->_db->loadResultArray();

		return $membershipTypes;
	}

	/**
	 * Lookup membership info
	 *
	 * @param  int			cart ID
	 * @param  int			membership product ID
	 * @return array		membership info
	 */
	public function getMembershipInfo($crtId, $pId)
	{
		$now = Date::toSql();
		$sql = "SELECT `crtmExpires`, IF(`crtmExpires` < '" . $now . "', 0, 1) AS `crtmActive` FROM `#__cart_memberships` WHERE `pId` = " . $this->_db->quote($pId) . " AND `crtId` = " . $this->_db->quote($crtId);
		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$membershipInfo = $this->_db->loadAssoc();

		return $membershipInfo;
	}

	/* ****************************** Static Functions *********************************/

	/**
	 * Find the proper Product Type Subscription Object and return it
	 * @param	String 	Product type
	 * @param 	Object  Product ID
	 * @return 	Subscription Object	If not found returns a generic subscription object
	 */
	public static function getSubscriptionObject($type, $pId, $uId)
	{
		// Find if there is a corresponding object
		$lookupPath  = JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'ProductTypes';
		$lookupPath .= DS . 'Subscriptions';

		$objectClass = str_replace(' ', '_', ucwords(strtolower($type))) . '_Subscription';
		if (file_exists($lookupPath . DS . $objectClass . '.php'))
		{
			// Include the class file
			require_once($lookupPath . DS . $objectClass . '.php');
			return new $objectClass($pId, $uId);
		}
		else
		{
			require_once($lookupPath . DS . 'BaseSubscription.php');
			return new BaseSubscription($pId, $uId);
		}
	}

	/**
	 * Calculate and return new expiration date for a SKU
	 *
	 * @param  	string	Current subscription expiration (MySQL format)
	 * @param	Array	SKU/Cart info
	 * @return 	string	Calculated new expiration (MySQL format)
	 */
	public static function calculateNewExpiration($currentExpiration, $item)
	{
		// Calculate correct TTL for the SKU (sku tll * qty)
		$ttl = self::getTtl($item['meta']['ttl'], $item['cartInfo']->qty);

		// Calculate the new expiration date
		if ($currentExpiration && $currentExpiration['crtmActive'])
		{
			// Set the date to the current expiration
			$date = JFactory::getDate($currentExpiration['crtmExpires']);
			// Add TTL to the current expiration
			$date->modify('+ ' . $ttl);
		}
		else
		{
			// Get current time
			$date = JFactory::getDate();
			// Add TTL to the current time
			$date->modify('+ ' . $ttl);
		}

		return $date->toSql();
	}

	/**
	 * Calculate TTL with respect to the quantity
	 *
	 * @param  string		single item TTL
	 * @param  int			number of items
	 * @return string		combined TTL
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
	 * @param  string	TTL
	 * @return void
	 */
	public static function checkTtl($ttl)
	{
		if (!preg_match("/^[1-9]+[0-9]* (year|month|day)+$/i", $ttl))
		{
			throw new Exception(Lang::txt('Bad TTL formatting. Please use something like 1 DAY, 2 MONTH or 3 YEAR'));
		}
	}

	/**
	 * Lookup membership info by user (almost identical as above)
	 *
	 * @param  int			user ID
	 * @param  int			membership product ID
	 * @return array		membership info
	 */
	public static function getMembershipInfoByUser($uId, $pId)
	{
		$db = JFactory::getDBO();

		$now = Date::toSql();
		$sql =  "SELECT `crtmExpires`, IF(`crtmExpires` < '" . $now . "', 0, 1) AS `crtmActive` FROM `#__cart_memberships` m";
		$sql .= " LEFT JOIN `#__cart_carts` c on c.`crtId` = m.`crtId`";
		$sql .= "WHERE m.`pId` = " . $db->quote($pId) . " AND c.`uidNumber` = " . $db->quote($uId);
		$db->setQuery($sql);
		$membershipInfo = $db->loadAssoc();

		return $membershipInfo;
	}

}