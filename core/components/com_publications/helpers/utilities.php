<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Helpers;

/**
 * Utility methods
 */
class Utilities
{
	/**
	 * Returns mkAIP script path
	 *
	 * @return     string
	 */
	public static function getMkAipBase()
	{
		return PATH_CORE . DS . 'cli/mkaip/bin/mkaip';
	}

	/**
	 * Checks if mkAIP is used
	 *
	 * @return   boolean
	 */
	public static function archiveOn()
	{
		$mkaip = self::getMkAipBase();
		if (file_exists($mkaip))
		{
			return true;
		}

		return false;
	}

	/**
	 * Run mkAIP
	 *
	 * @param      object $row      Publication version object
	 * @return     void
	 */
	public static function mkAip($row)
	{
		$mkaip = self::getMkAipBase();

		// Create OAIS Archival Information Package
		if (file_exists($mkaip))
		{
			$mkaipOutput =
				'mkaip-'
				. str_replace(
					'/',
					'__',
					$row->doi
				)
				. '.out';

			// "fire and forget" mkaip --
			// must use proc_open / proc_close()
			// or we cannot run mkaip in the
			// background on:
			//     Debian GNU/Linux 6.0.7 (squeeze)
			// [ Mark Leighton Fisher, 2014-04-28 ]
			$handles = array();
			$pipes	 = array();
			proc_close(
				proc_open(
					'( /usr/bin/nohup '
					. '/usr/bin/php -q '
					. $mkaip . ' ' . $row->doi . ' '
					. '2>&1 > '
					. "/www/tmp/$mkaipOutput & ) &",
					$handles,
					$pipes
				)
			);
			return true;
		}

		return false;
	}
}
