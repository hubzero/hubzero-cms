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

		$db = \App::get('db');
		$sql = "UPDATE `#__storefront_serials` SET srStatus = 'used'";
		$sql .= " WHERE srId IN (" . $serialIds . ")";

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->execute();

		return $serialNumbers;
	}
}

