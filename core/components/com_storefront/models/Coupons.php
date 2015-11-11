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

namespace Components\Storefront\Models;

defined('_HZEXEC_') or die();

/**
 *
 * Coupons lookup and management
 *
 */
class Coupons
{
	// Database instance
	var $db = NULL;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');

		// Load language file
		\App::get('language')->load('com_storefront', PATH_CORE . '/components/com_storefront/site/');

	}

	/**
	 * Get coupons info orderd by coupon type with item coupons first then ordered in the order of applied time
	 *
	 * @param  array		$cnIds coupon ids
	 * @return array		coupons info
	 */
	public function getCouponsInfo($cnIds)
	{
		$sqlCnIds = '0';
		if (is_array($cnIds) || is_object($cnIds))
		{
			foreach ($cnIds as $cnId)
			{
				$sqlCnIds .= ',' . $cnId;
			}
		}

		$sql = "SELECT `cnId`, `cnCode`, `cnDescription`, `cnObject`,
				IF(`cnExpires` < NOW(), 1, 0) AS `cnExpired`,
				IF(`cnObject` = 'sku' OR `cnObject` = 'product', 1, 0) AS `itemCoupon`
				FROM `#__storefront_coupons` cn WHERE `cnId` IN (" . $sqlCnIds . ") ORDER BY `itemCoupon` DESC";
		$this->_db->setQuery($sql);
		$couponsInfo = $this->_db->loadObjectList('cnId');

		// rearrange coupons in the order of applied within coupon types time keeping the item coupons on top (so that it is ordered by itemCoupon, dateAdded)

		// Initialize temp storage arrays
		$temp = new \stdClass();
		$temp->itemCoupons = array();
		$temp->genericCoupons = array();

		// $cnIds are orderd by time applied
		if (is_array($cnIds) || is_object($cnIds))
		{
			foreach ($cnIds as $cnId)
			{
				// Skip deleted or inactive coupons
				if (empty($couponsInfo[$cnId]))
				{
					continue;
				}

				if ($couponsInfo[$cnId]->itemCoupon)
				{
					$temp->itemCoupons[] = $couponsInfo[$cnId];
				}
				else
				{
					$temp->genericCoupons[] = $couponsInfo[$cnId];
				}
			}
		}

		$couponsInfo = array_merge($temp->itemCoupons, $temp->genericCoupons);
		unset($temp);

		return $couponsInfo;
	}

	/**
	 * Get complete info for a coupon
	 *
	 * @param 	int 		$cnId coupon ID
	 * @param 	bool 		$returnObjects flag wheter to query/return  coupon objects
	 * @param 	bool 		$returnConditions flag wheter to query/return  coupon conditions
	 * @param 	bool 		$returnAction flag wheter to query/return  coupon action
	 * @param 	bool 		$returnInfo flag wheter to query/return  coupon generic info
	 * @return 	object		coupon info
	 */
	public function getCouponInfo($cnId, $returnObjects = true, $returnConditions = true, $returnAction = true, $returnInfo = false)
	{
		$couponInfo = new \stdClass();

		// Get objects
		if ($returnObjects)
		{
			$sql = "SELECT * FROM `#__storefront_coupon_objects` WHERE cnId = " . $this->_db->quote($cnId);

			$this->_db->setQuery($sql);
			$this->_db->query();
			$objects = $this->_db->loadObjectList();
			$couponInfo->objects = $objects;
		}

		// Get conditions
		if ($returnConditions)
		{
			$sql = "SELECT * FROM `#__storefront_coupon_conditions` WHERE cnId = " . $this->_db->quote($cnId);

			$this->_db->setQuery($sql);
			$this->_db->query();
			$conditions = $this->_db->loadObjectList();
			$couponInfo->conditions = $conditions;
		}

		// Get action
		if ($returnAction)
		{
			$sql = "SELECT * FROM `#__storefront_coupon_actions` WHERE cnId = " . $this->_db->quote($cnId);

			$this->_db->setQuery($sql);
			$this->_db->query();
			$action = $this->_db->loadObject();
			$couponInfo->action = $action;
		}

		// Get generic coupon info
		if ($returnInfo)
		{
			$sql = "SELECT cn.*,
					IF(`cnObject` = 'sku' OR `cnObject` = 'product', 1, 0) AS `itemCoupon`
					FROM `#__storefront_coupons` cn WHERE cnId = " . $this->_db->quote($cnId);

			$this->_db->setQuery($sql);
			$this->_db->query();
			$info = $this->_db->loadObject();
			$couponInfo->info = $info;
		}

		return($couponInfo);
	}

	/**
	 * Check if coupon is valid
	 *
	 * @param 	string		$couponCode coupon code
	 * @return 	int			coupon id if the code is valid
	 */
	public function isValid($couponCode)
	{
		// Check if the code is valid
		$sql = 	"SELECT cn.`cnId`,
				IF(cn.`cnUseLimit` IS NULL, 'unlimited', cn.`cnUseLimit`) AS `cnUseLimit`,
				IF(cn.`cnExpires` IS NULL OR cn.`cnExpires` >= DATE(NOW()), 'valid', 'expired') AS `cnValid`"
				. ' FROM #__storefront_coupons cn '
				. ' WHERE cn.`cnCode` = ' . $this->_db->quote($couponCode);

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$this->_db->query();

		if (!$this->_db->getNumRows())
		{
			throw new \Exception(Lang::txt('COM_STOREFRONT_INVALID_COUPON_CODE'));
		}

		$row = $this->_db->loadObject();

		// check if expired
		if ($row->cnValid != 'valid')
		{
			throw new \Exception(Lang::txt('COM_STOREFRONT_EXPIRED_COUPON_CODE'));
		}

		if (!$row->cnUseLimit)
		{
			throw new \Exception(Lang::txt('COM_STOREFRONT_COUPON_ALREADY_USED'));
		}

		return $row->cnId;
	}

	/**
	 * Use up one coupon application
	 *
	 * @param 	int			$cnId coupon ID
	 * @return 	void
	 */
	public function apply($cnId)
	{
		$sql = "UPDATE `#__storefront_coupons` SET `cnUseLimit` = (IF(`cnUseLimit` IS NULL, NULL, `cnUseLimit` - 1)) WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	/**
	 * Return coupon back to the coupons pool to be available for future use
	 *
	 * @param 	int			$cnId coupon ID
	 * @return 	void
	 */
	public function recycle($cnId)
	{
		$sql = "UPDATE `#__storefront_coupons` SET `cnUseLimit` = (IF(`cnUseLimit` IS NULL, NULL, `cnUseLimit` + 1)) WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

}