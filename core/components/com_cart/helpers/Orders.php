<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
			$filters['sort_Dir'] = 'DESC';
		}
		if (strtolower($rtrn) == 'count')
		{
			// no limit for count
			unset($filters['limit']);
		}

		$db = \App::get('db');

		$sql = "SELECT ti.sId, ti.tiQty, ti.tiPrice, t.tId, t.crtId, t.`tLastUpdated`, crt.`uidNumber`, x.name
				FROM `#__cart_transaction_items` ti
				LEFT JOIN `#__cart_transactions` t ON (t.tId = ti.tId)
				LEFT JOIN `#__cart_carts` crt on (crt.crtId = t.crtId)
				LEFT JOIN `#__users` x ON (crt.`uidNumber` = x.id)";

		$sql .= " LEFT JOIN `#__storefront_skus` sku on (sku.sId = ti.sId)";
		$sql .= " LEFT JOIN `#__storefront_products` p on (sku.pId = p.pId)";

		// Filter by filters
		if ($filters['sort'] == 'title')
		{
			$filters['sort'] = 'uId';
		}
		elseif ($filters['sort'] == 'product')
		{
			$filters['sort'] = 'pName';
		}

		$where = array("t.tStatus = 'completed'");

		if (isset($filters['order']) && $filters['order'])
		{
			$where[] = "ti.`tId` = " . $db->quote($filters['order']);
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(
				x.`name` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR x.`username` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR sku.`sSku` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR p.`pName` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR t.`tId` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
			)";
		}

		if (isset($filters['report-from']) && strtotime($filters['report-from']))
		{
			$showFrom = date("Y-m-d", strtotime($filters['report-from']));
			$where[] = "t.`tLastUpdated` >= " . $db->quote($showFrom);
		}
		if (isset($filters['report-to']) && strtotime($filters['report-to']))
		{
			// Add one day to include all the records of the end day
			$showTo = strtotime($filters['report-to'] . ' +1 day');
			$showTo = date("Y-m-d 00:00:00", $showTo);
			$where[] = "t.`tLastUpdated` <= " . $db->quote($showTo);
		}

		if (!empty($filters['pId']) && $filters['pId'])
		{
			$where[] = " p.`pId` = " . intval($filters['pId']);
		}

		if (!empty($filters['sId']) && $filters['sId'])
		{
			$where[] = "sku.`sId` = " . intval($filters['sId']);
		}

		if (count($where))
		{
			$sql .= " WHERE " . implode(" AND ", $where) . " ";
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
			return $db->getNumRows();
		}
		elseif ($rtrn == 'array')
		{
			return $db->loadAssocList();
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

	/**
	 * Log order changes
	 *
	 * @param   int    	$tId
	 * @param   array   $orderChanges
	 * @return  mixed
	 */
	public static function logOrderChanges($tId, $orderChanges)
	{
		//print_r(json_encode($orderChanges)); die;

		$db = \App::get('db');

		$sql = "INSERT INTO `#__activity_logs` SET `created` = NOW(), `created_by` = " .  User::get('id') . ", `description` = 'Order updated', `action` = 'updated', `scope` = 'cart.order', `scope_id` = {$tId}, `details` = " . $db->quote(json_encode($orderChanges)). "";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Get order changes log
	 *
	 * @param   int    	$tId
	 * @return  mixed
	 */
	public static function getOrderChangesLog($tId)
	{
		//print_r(json_encode($orderChanges)); die;

		$db = \App::get('db');

		$sql = "SELECT * FROM `#__activity_logs` WHERE `action` = 'updated' AND `scope` = 'cart.order' AND `scope_id` = {$tId} ORDER BY `created` DESC";
		$db->setQuery($sql);
		$db->query();

		$res = $db->loadObjectList();
		return $res;
	}
}
