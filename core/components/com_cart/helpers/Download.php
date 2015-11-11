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

class CartDownload
{
	public static function countProductDownloads($pId)
	{
		$db = \App::get('db');
		$sql = 'SELECT COUNT(*) FROM `#__cart_downloads` d
				LEFT JOIN `#__storefront_skus` s ON d.`sId` = s.`sId`
				WHERE s.pId = ' . $db->quote($pId);
		$db->setQuery($sql);
		$downloadsCount = $db->loadResult();
		return $downloadsCount;
	}

	public static function countSkuDownloads($sId, $uId = false)
	{
		$db = \App::get('db');
		$sql = 'SELECT COUNT(*) FROM `#__cart_downloads`
				WHERE sId = ' . $db->quote($sId);
		if ($uId)
		{
			$sql .= 'AND `uId` = ' . $db->quote($uId);
		}
		$db->setQuery($sql);
		$downloadsCount = $db->loadResult();
		return $downloadsCount;
	}

	public static function countUserSkuDownloads($sId, $uId)
	{
		return CartDownload::countSkuDownloads($sId, $uId);
	}
}