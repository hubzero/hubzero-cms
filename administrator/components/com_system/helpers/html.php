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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML helper for system
 */
class SystemHtml
{
	//public static $MY_SELF_WO_SORT = '';

	//public static $MYREQUEST = array();

	/**
	 * Sortable table header in "scripts for this host" view
	 *
	 * @param	string $key   Sort key
	 * @param	string $name  Link name
	 * @param	string $extra Extra data to append to URL
	 * @param	string
	 */
	public static function sortheader($MYREQUEST, $MY_SELF_WO_SORT, $key, $name, $extra='')
	{
		//$MYREQUEST = self::$MYREQUEST;
		//$MY_SELF_WO_SORT = self::$MY_SELF_WO_SORT;

		if ($MYREQUEST['SORT1'] == $key)
		{
			$MYREQUEST['SORT2'] = $MYREQUEST['SORT2']=='A' ? 'D' : 'A';
		}

		return "<a class=\"sortable\" href=\"$MY_SELF_WO_SORT$extra&amp;SORT1=$key&amp;SORT2=" . $MYREQUEST['SORT2'] . "\">$name</a>";
	}

	/**
	 * Pretty printer for byte values
	 *
	 * @param	integer $s Byte value
	 * @param	string
	 */
	public static function bsize($s)
	{
		foreach (array('', 'K', 'M', 'G') as $i => $k)
		{
			if ($s < 1024)
			{
				break;
			}
			$s/=1024;
		}
		return sprintf("%5.1f %sBytes", $s, $k);
	}
}