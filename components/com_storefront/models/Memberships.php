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
		JFactory::getLanguage()->load('com_storefront');
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
		//echo $this->_db->_sql;
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
		$sql = "SELECT `crtmExpires`, IF(`crtmExpires` < NOW(), 0, 1) AS `crtmActive` FROM `#__cart_memberships` WHERE `pId` = " . $this->_db->quote($pId) . " AND `crtId` = " . $this->_db->quote($crtId);
		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$membershipInfo = $this->_db->loadAssoc();

		return $membershipInfo;
	}

	/**
	 * Set membership expiration
	 *
	 * @param  int			cart ID
	 * @param  int			membership product ID
	 * @param  int			new expiration time (UNIX format)
	 * @return array		membership info
	 */
	public function setMembershipExpiration($crtId, $pId, $expires)
	{
		$sql = "INSERT INTO `#__cart_memberships` SET
				`crtmExpires` = FROM_UNIXTIME(" . $this->_db->quote($expires) . "),
				`pId` = " . $this->_db->quote($pId) . ",
				`crtId` = " . $this->_db->quote($crtId) . "
				ON DUPLICATE KEY UPDATE
				`crtmExpires` = FROM_UNIXTIME(" . $this->_db->quote($expires) . ")";

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$this->_db->query();
	}

	/**
	 * Calculate new and return old expiration info for a product
	 *
	 * @param  string		TTL
	 * @return void
	 */
	public function getNewExpirationInfo($crtId, $item)
	{
		// Get current membership (if any)
		$membershipInfo = $this->getMembershipInfo($crtId, $item['info']->pId);

		// Calculate correct TTL for one SKU (sku tll * qty)
		$ttl = $this->_getTtl($item['meta']['ttl'], $item['cartInfo']->qty);

		// Calculate the new expiration date
		if ($membershipInfo && $membershipInfo['crtmActive'])
		{
			// New expiration date is an old not-expired date + TTL
			$membershipSIdInfo->newExpires = strtotime('+ ' . $ttl, strtotime($membershipInfo['crtmExpires']));
			$membershipSIdInfo->existingExpires = strtotime($membershipInfo['crtmExpires']);
			//echo date('l dS \o\f F Y h:i:s A', strtotime('+ 10 YEAR', strtotime($membershipInfo['crtmExpires']))); die;
		}
		else
		{
			// New expiration date is now + TTL
			$membershipSIdInfo->newExpires = strtotime('+ ' . $ttl);
		}

		return $membershipSIdInfo;
	}

	/**
	 * Check TTL format
	 *
	 * @param  string		TTL
	 * @return void
	 */
	public function checkTtl($ttl)
	{
		if (!preg_match("/^[1-9]+[0-9]* (year|month|day)+$/i", $ttl))
		{
			throw new Exception(JText::_('Bad TTL formatting. Please use something like 1 DAY, 2 MONTH or 3 YEAR'));
		}
	}

	/**
	 * Calculate correct TTL
	 *
	 * @param  string		single item TTL
	 * @param  int			number of items
	 * @return string		combined TTL
	 */
	private function _getTtl($ttl, $qty)
	{
		StorefrontModelMemberships::checkTtl($ttl);
		// Split ttl into parts
		$ttlParts = explode(' ', $ttl);
		$ttlParts[0] = $qty * $ttlParts[0];

		$ttl = implode(' ', $ttlParts);
		return $ttl;
	}
}