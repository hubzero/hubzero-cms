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

defined('_HZEXEC_') or die();


class StorefrontHelperStorefront
{
	/**
	 * Find all SKUs that
	 *
	 * @param	int		Option ID
	 * @param	bool	Optional, flag whether to find only active SKUs. Default: true.
	 * @return	array	SKU IDs
	 */
	public static function getSkuIdsByOption($oId, $getOnlyActive = true)
	{
		$db = \App::get('db');

		$sql = 'SELECT o.`sId` FROM `#__storefront_sku_options` o
				LEFT JOIN `#__storefront_skus` s ON o.`sId` = s.`sId`
				WHERE o.`oId` = ' . $db->quote($oId);
		if ($getOnlyActive)
		{
			$sql .= ' AND s.`sActive` = 1';
		}
		$db->setQuery($sql);
		$skus = $db->loadColumn();
		return $skus;
	}
}