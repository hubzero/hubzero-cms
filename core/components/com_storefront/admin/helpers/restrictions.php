<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

namespace Components\Storefront\Admin\Helpers;

use Hubzero\Base\Object;
use App;

class RestrictionsHelper
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_storefront';

	/**
	 * Get a list or count of the users for the SKU.
	 *
	 * @param   array  $filters  Filters
	 * @param   int    $sId      SKU id
	 * @return  mixed
	 */
	public static function getSkuUsers($filters = array(), $sId)
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'uId';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		if (!isset($filters['return']))
		{
			$filters['return'] = 'list';
		}

		$db = App::get('db');
		$sql = "SELECT p.id, p.uId, p.username AS uName, u.name, u.username, u.email
				FROM `#__storefront_permissions` p
				LEFT JOIN `#__users` u ON (u.id = p.uId)
				WHERE p.scope='sku' AND p.scope_id = " . $db->quote($sId);

		if (isset($filters['sort']))
		{
			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']) && $filters['return'] != 'count')
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$db->setQuery($sql);
		$db->execute();

		if ($filters['return'] == 'count')
		{
			return $db->getNumRows();
		}

		return $db->loadObjectList();
	}

	/**
	 * Delete users
	 *
	 * @param   array  permissions IDs
	 * @return  void
	 */
	public static function removeUsers($ids)
	{
		$ids = array_map('intval', $ids);

		$db = App::get('db');
		$sql = "DELETE FROM `#__storefront_permissions`
				WHERE id IN (" . implode(',', $ids) . ")";

		$db->setQuery($sql);
		$db->execute();
	}

	/**
	 * Add user/sku
	 *
	 * @param   int  $uId  user ID
	 * @param   int  $sId  SKU ID
	 * @return  int
	 */
	public static function addSkuUser($uId, $sId, $username = null)
	{
		return self::addUser('sku', $uId, $sId, $username);
	}

	/**
	 * Add user
	 *
	 * @param   string  $scope
	 * @param   int     $uId      user ID
	 * @param   int     $scopeId
	 * @return  int
	 */
	private static function addUser($scope, $uId, $scopeId, $username = null)
	{
		$db = App::get('db');

		$sql = "SELECT COUNT(p.id)
				FROM `#__storefront_permissions` p
				WHERE p.scope=" . $db->quote($scope) . " AND p.scope_id = " . $db->quote($scopeId);

		if ($uId)
		{
			$sql .= " AND p.uId = " . $db->quote($uId);
		}
		else if ($username)
		{
			$sql .= " AND p.username = " . $db->quote($username);
		}

		$db->setQuery($sql);
		if ($db->loadResult())
		{
			return 1;
		}

		$sql  = "INSERT IGNORE INTO `#__storefront_permissions`
				SET `scope` = " . $db->quote($scope) . ", `uId` = " . $db->quote((int)$uId) . ", `scope_id` = " . $db->quote((int)$scopeId);

		if (!$uId && $username)
		{
			$sql .= ", `username` = " . $db->quote($username);
		}

		$db->setQuery($sql);
		$db->execute();

		return $db->getAffectedRows();
	}

	/**
	 * Update entry
	 *
	 * @param   int  $uId  user ID
	 * @param   int  $sId  SKU ID
	 * @return  int
	 */
	public static function updateUser($uId, $username)
	{
		$db = App::get('db');

		$sql  = "UPDATE `#__storefront_permissions`
				SET `uId` = " . $db->quote((int)$uId) . ", `username` = NULL WHERE `username` = " . $db->quote($username);

		$db->setQuery($sql);
		$db->execute();
	}
}
