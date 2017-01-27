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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Helpers;

/**
 * Cart orders helpers
 */
class CartOrders
{
	/**
	 * Get a count or list of items ordered
	 *
	 * @param   string  $rtrn     What data to return
	 * @param   array   $filters  Filters to apply to data retrieval
	 * @return  mixed
	 */
	public static function getItemsOrdered($rtrn = 'list', $filters = array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'tId';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'DESC';
		}
		if (strtolower($rtrn) == 'count')
		{
			// no limit for count
			unset($filters['limit']);
		}

		$db = \App::get('db');

		$sql  = 'SELECT ti.sId, ti.tiQty, ti.tiPrice, t.tId, t.crtId, t.`tLastUpdated`, x.id AS uidNumber, x.name ';
		$sql .= 'FROM #__cart_transaction_items ti ';
		$sql .= 'LEFT JOIN  #__cart_transactions t ON (t.tId = ti.tId) ';
		$sql .= 'LEFT JOIN  #__cart_carts crt on (crt.crtId = t.crtId) ';
		$sql .= 'LEFT JOIN `#__users` x ON (crt.`uidNumber` = x.id) ';
		$sql .= "WHERE t.tStatus = 'completed'";

		// Filter by filters
		if ($filters['sort'] == 'title')
		{
			$filters['sort'] = 'uId';
		}
		elseif ($filters['sort'] == 'product')
		{
			$filters['sort'] = 'pName';
		}

		$sql .= " ORDER BY " . $filters['sort'];

		$sql .= ' ' . $filters['sort_Dir'];

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$db->setQuery($sql);
		$db->execute();
		if ($rtrn == 'count')
		{
			return($db->getNumRows());
		}
		elseif ($rtrn == 'array')
		{
			return($db->loadAssocList());
		}

		$res = $db->loadObjectList();
		return $res;
	}

	/**
	 * Get items for an order
	 *
	 * @param   int    $tId
	 * @return  mixed
	 */
	public static function getOrderItems($tId)
	{
		$db = \App::get('db');

		$sql = "SELECT `sId`, `tiQty`, `tiPrice` FROM `#__cart_transaction_items` ti WHERE ti.`tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		if (!$db->getNumRows())
		{
			return false;
		}

		$allSkuInfo = $db->loadObjectList('sId');
		$skus = $db->loadColumn();

		$warehouse = new Warehouse();

		$skuInfo = $warehouse->getSkusInfo($skus);

		// Update skuInfo with transaction info
		foreach ($skuInfo as $sId => $sku)
		{
			$transactionInfo = new \stdClass();
			$transactionInfo->qty = $allSkuInfo[$sId]->tiQty;
			$transactionInfo->tiPrice = $allSkuInfo[$sId]->tiPrice;
			$skuInfo[$sId]['transactionInfo'] = $transactionInfo;
			unset($transactionInfo);
		}

		if (empty($skuInfo))
		{
			return false;
		}

		return $skuInfo;
	}
}
