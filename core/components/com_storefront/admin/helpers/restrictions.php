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


class RestrictionsHelper
{
	/**
	 * Name of the component
	 *
	 * @var string
	 */
	public static $extension = 'com_storefront';

	/**
	 * Get a list or count of the users for the SKU.
	 *
	 * @param   array   Filters
	 * @param   int  SKU id
	 * @return  object
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

		$db = \App::get('db');
		$sql = "SELECT p.id, p.uId, u.name, u.username, u.email ";
		$sql .= "FROM #__storefront_permissions p
				LEFT JOIN `#__users` u ON (u.id = p.uId)";
		$sql .= " WHERE p.scope='sku' AND p.scope_id = " . $db->quote($sId);

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
		//print_r($db->toString()); die;
		$db->execute();
		if ($filters['return'] == 'count')
		{
			return($db->getNumRows());
		}
		$users = $db->loadObjectList();

		return $users;
	}

	/**
	 * Delete users
	 *
	 * @param   array	permissions IDs
	 * @return  void
	 */
	public static function removeUsers($ids)
	{
		$db = \App::get('db');
		$sql = "DELETE FROM #__storefront_permissions";
		$sql .= " WHERE id IN (0";
		foreach ($ids as $id)
		{
			$sql .= ',' . $id;
		}
		$sql .= ")";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();
	}

	/**
	 * Delete users
	 *
	 * @param   int	user ID
	 * @param   int	SKU ID
	 * @return  void
	 */
	public static function addSkuUser($uId, $sId)
	{
		return RestrictionsHelper::addUser('sku', $uId, $sId);
	}

	private static function addUser($scope, $uId, $scopeId)
	{
		$db = \App::get('db');
		$sql = "INSERT IGNORE INTO #__storefront_permissions";
		$sql .= " SET `scope` = '{$scope}', `uId` = {$uId}, `scope_id` = {$scopeId}";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return $db->getAffectedRows();
	}
}

