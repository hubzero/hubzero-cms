<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Helpers;

/**
 * Cart download helpers
 */
class CartDownload
{
	/**
	 * Cound product downloads
	 *
	 * @param   int  $pId
	 * @return  int
	 */
	public static function countProductDownloads($pId)
	{
		$db = \App::get('db');
		$sql = 'SELECT COUNT(*) FROM `#__cart_downloads` d
				LEFT JOIN `#__storefront_skus` s ON d.`sId` = s.`sId`
				WHERE d.dStatus > 0 AND s.pId = ' . $db->quote($pId);
		$db->setQuery($sql);
		$downloadsCount = $db->loadResult();
		return $downloadsCount;
	}

	/**
	 * Cound downloads for a SKU
	 *
	 * @param   int  $sId
	 * @param   int  $uId
	 * @return  int
	 */
	public static function countSkuDownloads($sId, $uId = false)
	{
		$db = \App::get('db');
		$sql = 'SELECT COUNT(*) FROM `#__cart_downloads`
				WHERE dStatus > 0 AND sId = ' . $db->quote($sId);
		if ($uId)
		{
			$sql .= 'AND `uId` = ' . $db->quote($uId);
		}
		$db->setQuery($sql);
		$downloadsCount = $db->loadResult();
		return $downloadsCount;
	}

	/**
	 * Cound downloads for a user and SKU
	 *
	 * @param   int  $sId
	 * @param   int  $uId
	 * @return  int
	 */
	public static function countUserSkuDownloads($sId, $uId)
	{
		return self::countSkuDownloads($sId, $uId);
	}

	/**
	 * Get a count or list of downloads
	 *
	 * @param   string  $rtrn     What data to return
	 * @param   array   $filters  Filters to apply to data retrieval
	 * @return  mixed
	 */
	public static function getDownloads($rtrn = 'list', $filters = array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		if (strtolower($rtrn) == 'count')
		{
			// no limit for count
			unset($filters['limit']);
		}

		$db = \App::get('db');

		$sql  = 'SELECT d.*, INET_NTOA(d.dIp) AS dIp, x.id AS uidNumber, x.name AS dName, x.username, s.sSku, p.pId, p.pName FROM `#__cart_downloads` d ';
		$sql .= ' LEFT JOIN `#__users` x ON (d.uId = x.id)';
		$sql .= ' LEFT JOIN `#__storefront_skus` s ON (s.sId = d.sId)';
		$sql .= ' LEFT JOIN `#__storefront_products` p ON (s.pId = p.pId)';
		$sql .= ' WHERE 1';

		// Filter by filters
		if (isset($filters['active']) && $filters['active'] == 1)
		{
			$sql .= " AND dActive = 1";
		}

		if (!empty($filters['skuRequested']))
		{
			$sql .= " AND d.sId = " . $db->quote($filters['skuRequested']);
		}

		if (isset($filters['report-from']) && strtotime($filters['report-from']))
		{
			$showFrom = date("Y-m-d", strtotime($filters['report-from']));
			$sql .= " AND d.`dDownloaded` >= '{$showFrom}'";
		}
		if (isset($filters['report-to']) && strtotime($filters['report-to']))
		{
			// Add one day to include all the records of the end day
			$showTo = strtotime($filters['report-to'] . ' +1 day');
			$showTo = date("Y-m-d 00:00:00", $showTo);
			$sql .= " AND d.`dDownloaded` <= '{$showTo}'";
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$where   = array();
			$where[] = "p.`pName` LIKE " . $db->quote('%' . $filters['search'] . '%');
			$where[] = "s.`sSku` LIKE " . $db->quote('%' . $filters['search'] . '%');
			$where[] = "x.`name` LIKE " . $db->quote('%' . $filters['search'] . '%');
			$where[] = "x.`username` LIKE " . $db->quote('%' . $filters['search'] . '%');

			$sql .= " AND (" . implode(" OR ", $where) . ")";
		}

		if (!empty($filters['uidNumber']) && $filters['uidNumber'])
		{
			$sql .= " AND d.`uId` = " . intval($filters['uidNumber']);
		}

		if (!empty($filters['pId']) && $filters['pId'])
		{
			$sql .= " AND p.`pId` = " . intval($filters['pId']);
		}

		if (!empty($filters['sId']) && $filters['sId'])
		{
			$sql .= " AND s.`sId` = " . intval($filters['sId']);
		}

		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'uId';
			}
			elseif ($filters['sort'] == 'product')
			{
				$filters['sort'] = 'pName';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}

			if ($filters['sort'] == 'product')
			{
				$sql .= ', sSku';
			}
		}
		else
		{
			$sql .= " ORDER BY dDownloaded DESC";
		}

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

		// Get results keyed on download ID so we can add metadata later easily
		$res = $db->loadObjectList('dId');
		$dids = implode(',', array_keys($res));

		if (!empty($dids))
		{

			// Get the meta for all returned objects
			// Build a (potentially) long SQL statement to get metadata
			$sql = "SELECT `scope_id`, `mtKey`, `mtValue` FROM `#__cart_meta` WHERE `scope` = 'download' AND `scope_id` IN ({$dids})";
			$db->setQuery($sql);
			$db->execute();
			$metas = $db->loadAssocList();
			foreach ($metas as $meta)
			{
				$dId = $meta['scope_id'];

				// ditch scope_id to match old output
				unset($meta['scope_id']);

				// if the download record has no metadata yet, add it
				if (!isset($res[$dId]->meta))
				{
					$res[$dId]->meta = array();
				}
				$res[$dId]->meta[$meta['mtKey']] = $meta;
			}
		}


		return $res;
	}

	/**
	 * Get a count or list of downloads by SKU
	 *
	 * @param   string  $rtrn     What data to return
	 * @param   array   $filters  Filters to apply to data retrieval
	 * @return  mixed
	 */
	public static function getDownloadsSku($rtrn = 'list', $filters = array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'product';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		if (strtolower($rtrn) == 'count')
		{
			// no limit for count
			unset($filters['limit']);
		}

		$db = \App::get('db');

		$sql  = 'SELECT p.pId, p.pName, s.sId, s.sSku, d.sId, COUNT(d.sId) AS downloaded FROM `#__cart_downloads` d';
		$sql .= ' LEFT JOIN `#__storefront_skus` s ON (s.sId = d.sId)';
		$sql .= ' LEFT JOIN `#__storefront_products` p ON (s.pId = p.pId)';
		$sql .= ' WHERE 1';

		// Filter by filters
		if (isset($filters['report-from']) && strtotime($filters['report-from']))
		{
			$showFrom = date("Y-m-d", strtotime($filters['report-from']));
			$sql .= " AND d.`dDownloaded` >= '{$showFrom}'";
		}
		if (isset($filters['report-to']) && strtotime($filters['report-to']))
		{
			// Add one day to include all the records of the end day
			$showTo = strtotime($filters['report-to'] . ' +1 day');
			$showTo = date("Y-m-d 00:00:00", $showTo);
			$sql .= " AND d.`dDownloaded` <= '{$showTo}'";
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$where   = array();
			$where[] = "p.`pName` LIKE " . $db->quote('%' . $filters['search'] . '%');
			$where[] = "p.`pDescription` LIKE " . $db->quote('%' . $filters['search'] . '%');
			$where[] = "s.`sSku` LIKE " . $db->quote('%' . $filters['search'] . '%');

			$sql .= " AND (" . implode(" OR ", $where) . ")";
		}

		$sql .= ' GROUP BY d.sId';

		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'product')
			{
				$filters['sort'] = 'pName';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}

			if ($filters['sort'] == 'product')
			{
				$sql .= ', sSku';
			}
		}

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
	 * Set downloads status
	 *
	 * @param   array  $dIds    download ids
	 * @param   int    $status  0 - deactivate, 1 - activate
	 * @return  mixed
	 */
	public static function setStatus($dIds, $status = 1)
	{
		$ids = '0';
		foreach ($dIds as $dId)
		{
			$ids .= ',' . $dId;
		}

		$db = \App::get('db');
		$sql  = 'UPDATE `#__cart_downloads` SET `dStatus` = ' . $db->quote($status) . ' WHERE dId IN(';
		$sql .= $ids;
		$sql .= ')';
		$db->setQuery($sql);
		$db->execute();
	}
}
