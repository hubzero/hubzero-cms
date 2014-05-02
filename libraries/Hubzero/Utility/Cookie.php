<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Utility;

/**
 * Cookie utility class
 *
 * Set and retrieve cookies in consistent manner
 */
class Cookie
{
	/**
	 * Drop a cookie
	 *
	 * @param  (string) $namespace - make sure the cookie name is unique
	 * @param  (time)   $lifetime  - how long the cookie should last
	 * @param  (array)  $data      - data to be saved in cookie
	 * @return void
	 **/
	public static function bake($namespace, $lifetime, $data=array())
	{
		$hash   = \JUtility::getHash(\JFactory::getApplication()->getName() . ':' . $namespace);
		$crypt  = new \JSimpleCrypt();
		$cookie = $crypt->encrypt(serialize($data));

		// Set the actual cookie
		setcookie($hash, $cookie, $lifetime, '/');
	}

	/**
	 * Retrieve a cookie
	 *
	 * @param  (string) $namespace - make sure the cookie name is unique
	 * @return (object) $cookie data
	 **/
	public static function eat($namespace)
	{
		$hash  = \JUtility::getHash(\JFactory::getApplication()->getName() . ':' . $namespace);
		$crypt = new \JSimpleCrypt();

		if ($str = \JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
		{
			$sstr   = $crypt->decrypt($str);
			$cookie = @unserialize($sstr);

			return (object)$cookie;
		}
		else
		{
			return false;
		}
	}
}