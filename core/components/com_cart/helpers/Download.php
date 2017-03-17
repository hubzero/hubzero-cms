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

		$sql  = 'SELECT d.*, INET_NTOA(d.dIp) AS dIp, x.name AS dName, x.username, s.sSku, p.pId, p.pName FROM `#__cart_downloads` d ';
		$sql .= ' LEFT JOIN `#__users` x ON (d.uId = x.id)';
		$sql .= ' LEFT JOIN `#__storefront_skus` s ON (s.sId = d.sId)';
		$sql .= ' LEFT JOIN `#__storefront_products` p ON (s.pId = p.pId)';
		//$sql .= ' LEFT JOIN `#__cart_meta` mt ON (d.dId = mt.scope_id AND mt.scope = \'download\')';
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
			return($db->getNumRows());
		}

		$res = $db->loadObjectList();

		// Get the meta for all returned objects
		foreach ($res as $download)
		{
			$dId = $download->dId;

			$sql = "SELECT `mtKey`, `mtValue` FROM `#__cart_meta` WHERE `scope` = 'download' AND `scope_id` = {$dId}";
			$db->setQuery($sql);
			$db->execute();

			$meta = $db->loadAssocList('mtKey');
			$download->meta = $meta;
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

		$sql  = 'SELECT p.pId, p.pName, s.sId, s.sSku, d.sId, COUNT(d.sId) AS downloaded FROM `#__cart_downloads` d';
		$sql .= ' LEFT JOIN `#__storefront_skus` s ON (s.sId = d.sId)';
		$sql .= ' LEFT JOIN `#__storefront_products` p ON (s.pId = p.pId)';
		$sql .= ' WHERE 1';
		$sql .= ' GROUP BY d.sId';

		// Filter by filters
		if (isset($filters['active']) && $filters['active'] == 1)
		{
			$sql .= " AND dActive = 1";
		}

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

		$db = \App::get('db');
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
