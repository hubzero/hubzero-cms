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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubzero_Factory Class
 */
class Hubzero_Factory
{
	/**
	 * Get the current user's profile
	 * 
	 * @return     object
	 */
	public static function &getProfile()
	{
		static $instances = null;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_User_Profile');
			$juser =& JFactory::getUser();
			$instances[0] =& Hubzero_User_Profile::getInstance($juser->get('id'));

			if (is_object($instances[0]))
			{
				return $instances[0];
			}
		}

		return $instances[0];
	}

	/**
	 * Get the debug logger, creating it if it doesn't exist
	 * 
	 * @return     object
	 */
	public static function &getLogger()
	{
		static $instances;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_Log');

			$instances[0] = new Hubzero_Log();
			$handler = new Hubzero_Log_FileHandler("/var/log/hubzero/cmsdebug.log");
			$instances[0]->attach(HUBZERO_LOG_DEBUG, $handler);
		}

		return $instances[0];
	}

	/**
	 * Get the auth logger, creating it if it doesn't exist
	 * 
	 * @return     object
	 */
	public static function &getAuthLogger()
	{
		static $instances;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_Log');

			$instances[] = new Hubzero_Log();
			$handler = new Hubzero_Log_FileHandler("/var/log/hubzero/cmsauth.log");
			$instances[0]->attach(HUBZERO_LOG_AUTH, $handler);
		}

		return $instances[0];
	}
}

