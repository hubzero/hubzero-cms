<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Helpers;

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
	 * Get a list or count of the permitted users for the SKU.
	 *
	 * @param   array  $filters  Filters
	 * @param   int    $sId      SKU id
	 * @return  mixed
	 */
	public static function getPermittedSkuUsers($filters, $sId)
	{
		return self::getSkuUsers($filters = array(), $sId);
	}

	/**
	 * Get a list or count of the whitelisted users for the SKU.
	 *
	 * @param   array  $filters  Filters
	 * @param   int    $sId      SKU id
	 * @return  mixed
	 */
	public static function getWhitelistedSkuUsers($filters, $sId)
	{
		return self::getSkuUsers($filters = array(), $sId, array('usersType' => 'skuWhitelist'));
	}

	/**
	 * Check if the user is whitelisted for the SKU.
	 *
	 * @param   int    $uId      User ID
	 * @param   int    $sId      SKU ID
	 * @return  mixed
	 */
	public static function checkWhitelistedSkuUser($uId, $sId)
	{
		$db = App::get('db');
		$sql = "SELECT id
				FROM `#__storefront_permissions` p
				WHERE p.scope='skuWhitelist' AND uId = '{$uId}' AND p.scope_id = " . $db->quote($sId);
		$db->setQuery($sql);
		$db->execute();
		return $db->getNumRows();
	}

	/**
	 * Check if the user is whitelisted for the SKUs provided.
	 *
	 * @param   int    $uId      User ID
	 * @param   int    $sIds     SKUs IDs
	 * @return  mixed
	 */
	public static function checkWhitelistedSkusUser($uId, $sIds)
	{
		$db = App::get('db');

		$skus = '0';
		foreach ($sIds as $sId)
		{
			$skus .= ", {$sId}";
		}

		$sql = "SELECT scope_id AS sId
				FROM `#__storefront_permissions` p
				WHERE p.scope='skuWhitelist' AND uId = '{$uId}' AND p.scope_id IN (" . $skus . ')';
		$db->setQuery($sql);
		$db->execute();
		return $db->loadColumn();
	}

	/**
	 * Get a list or count of the permitted or whitelisted users for the SKU.
	 *
	 * @param   array  $filters  Filters
	 * @param   int    $sId      SKU id
	 * @param   array  $options  Options
	 * @return  mixed
	 */
	private static function getSkuUsers($filters, $sId, $options = array())
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
		if (!isset($options['usersType']))
		{
			$options['usersType'] = 'sku';
		}

		// sort and sort_Dir went into the statement verbatim. The two public
		// entry points above currently pass `$filters = array()` in the argument
		// list, which discards the caller's filters, so the request-controlled
		// values from the restrictions and whitelist controllers never arrive --
		// but the allowlist is what makes that a bug about sorting rather than a
		// bug about SQL, and both default here anyway, so nothing changes today.
		$sortable = array('id', 'uId', 'uName', 'name', 'username', 'email');

		if (!in_array($filters['sort'], $sortable, true))
		{
			$filters['sort'] = 'uId';
		}

		$filters['sort_Dir'] = (is_scalar($filters['sort_Dir'])
			&& strtoupper((string) $filters['sort_Dir']) == 'DESC') ? 'DESC' : 'ASC';

		$db = App::get('db');
		$sql = "SELECT p.id, p.uId, p.username AS uName, u.name, u.username, u.email
				FROM `#__storefront_permissions` p
				LEFT JOIN `#__users` u ON (u.id = p.uId)
				WHERE p.scope=" . $db->quote($options['usersType']) . " AND p.scope_id = " . $db->quote($sId);

		$sql .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];

		if (isset($filters['limit']) && is_numeric($filters['limit']) && $filters['return'] != 'count')
		{
			$sql .= ' LIMIT ' . (int) $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . (int) $filters['start'];
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
	 * Add permitted user to a SKU
	 *
	 * @param   int  $uId  user ID
	 * @param   int  $sId  SKU ID
	 * @return  int
	 */
	public static function addPermittedSkuUser($uId, $sId, $username = null)
	{
		return self::addUser('sku', $uId, $sId, $username);
	}

	/**
	 * Add whitelisted user to a SKU
	 *
	 * @param   int  $uId  user ID
	 * @param   int  $sId  SKU ID
	 * @return  int
	 */
	public static function addWhitelistedSkuUser($uId, $sId, $username = null)
	{
		return self::addUser('skuWhitelist', $uId, $sId, $username);
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
