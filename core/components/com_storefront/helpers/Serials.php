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

namespace Components\Storefront\Helpers;

use Hubzero\Base\Object;


class Serials
{
	/**
	 * Gets a count of serial numbers available for the SKU
	 *
	 * @param   integer  $sID    SKU ID.
	 * @return  int				number of serials available
	 */
	public static function countAvailableSerials($sId)
	{
		$db = \App::get('db');
		$sql = "SELECT COUNT(srId) FROM `#__storefront_serials`";
		$sql .= " WHERE srStatus='available' AND srSId = " . $db->quote($sId);

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();
		$serialsCount = $db->loadResult();

		return $serialsCount;
	}

	/**
	 * Mark serials as reserved for the SKU
	 *
	 * @param   integer  $sID    SKU ID
	 * @param   integer  Number of serials to mark
	 * @return  true
	 */
	public static function reserveSerials($sId, $qty = 1)
	{
		$db = \App::get('db');
		$sql = "UPDATE `#__storefront_serials` SET srStatus = 'reserved'";
		$sql .= " WHERE srStatus='available' AND srSId = " . $db->quote($sId) . " LIMIT " . intval($qty);

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return true;
	}

	/**
	 * Mark reserved serials as available for the SKU
	 *
	 * @param   integer  $sID    SKU ID
	 * @param   integer  Number of serials to mark
	 * @return  array
	 */
	public static function releaseSerials($sId, $qty = 1)
	{
		$db = \App::get('db');
		$sql = "UPDATE `#__storefront_serials` SET srStatus = 'available'";
		$sql .= " WHERE srStatus='reserved' AND srSId = " . $db->quote($sId) . " LIMIT " . intval($qty);

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return true;
	}

	/**
	 * Get serials and mark the as used for the SKU
	 *
	 * @param   integer  $sID    SKU ID
	 * @param   integer  Number of serials to get
	 * @return  obj
	 */
	public static function issueSerials($sId, $qty = 1)
	{
		$db = \App::get('db');
		$sql = "SELECT srId, srNumber FROM `#__storefront_serials`";
		$sql .= " WHERE srStatus='reserved' AND srSId = " . $db->quote($sId) . " LIMIT " . intval($qty);

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();
		$serials = $db->loadObjectList();
		//print_r($serials); die;

		// Mark serials as used
		$serialIds = '0';
		$serialNumbers = array();
		foreach ($serials as $serial)
		{
			$serialNumbers[] = $serial->srNumber;
			$serialIds .= ',' . $serial->srId;
		}

		$sql = "UPDATE `#__storefront_serials` SET srStatus = 'used'";
		$sql .= " WHERE srId IN (" . $serialIds . ")";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return $serialNumbers;
	}

	// Admin functions

	/**
	 * Get a list or count of the serials for the SKU.
	 *
	 * @param   array   Filters
	 * @param   int  SKU id
	 * @return  object/int
	 */
	public static function getSkuSerials($filters = array(), $sId)
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
		$sql = "SELECT s.* ";
		$sql .= "FROM #__storefront_serials s";
		$sql .= " WHERE srSId = " . $db->quote($sId);

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

	public static function delete($ids)
	{
		$msg = new \stdClass();
		$msg->type = 'message';

		$sIds = '0';
		foreach ($ids as $id)
		{
			$sIds .= ',' . $id;
		}

		$db = \App::get('db');
		// delete only available
		$sql = "DELETE FROM `#__storefront_serials`";
		$sql .= " WHERE srStatus='available' AND srId IN (" . $sIds . ")";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();
		$deleted = $db->getNumRows();

		$message = $deleted . ' serial number';
		if ($deleted > 1)
		{
			$message .= 's';
		}
		$message .= ' deleted';
		$msg->message = $message;

		if (count($ids) > $deleted)
		{
			$msg->type = 'warning';
			$message = (count($ids) - $deleted) . ' serial number';
			if (count($ids) - $deleted > 1)
			{
				$message .= 's';
			}
			$message .= ' could not be deleted. Possible status change.';
			$msg->message = $message;
		}

		return $msg;
	}

	public static function add($serial, $sId)
	{
		$serial = trim($serial);

		$db = \App::get('db');
		$sql = "INSERT IGNORE INTO #__storefront_serials";
		$sql .= " SET `srStatus` = 'available', `srNumber` = '{$serial}', `srSId` = {$sId}";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return $db->getAffectedRows();
	}
}

