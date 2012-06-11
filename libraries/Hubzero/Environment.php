<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Helper class for getting a user's IP, etc.
 */
class Hubzero_Environment
{
	/**
	 * Checks the $_SERVER super-global for a given index
	 * 
	 * @param      string $index Parameter to check for
	 * @return     boolean True if found
	 */
	public function server($index = '')
	{
		if (!isset($_SERVER[$index])) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks if an IP address is valid or not
	 * 
	 * @param      string  $ip IP address to validate
	 * @return     boolean True if IP is valid
	 */
	public function validIp($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}

	/**
	 * Get a user's IP address
	 * 
	 * @return     string
	 */
	public function ipAddress()
	{
		if (Hubzero_Environment::server('REMOTE_ADDR') && Hubzero_Environment::server('HTTP_CLIENT_IP')) 
		{
			$ip_address = JRequest::getVar('HTTP_CLIENT_IP', '', 'server');
		} 
		elseif (Hubzero_Environment::server('REMOTE_ADDR')) 
		{
			$ip_address = JRequest::getVar('REMOTE_ADDR', '', 'server');
		} 
		elseif (Hubzero_Environment::server('HTTP_CLIENT_IP')) 
		{
			$ip_address = JRequest::getVar('HTTP_CLIENT_IP', '', 'server');
		} 
		elseif (Hubzero_Environment::server('HTTP_X_FORWARDED_FOR')) 
		{
			$ip_address = JRequest::getVar('HTTP_X_FORWARDED_FOR', '', 'server');
		}

		if ($ip_address === FALSE) 
		{
			$ip_address = '0.0.0.0';
			return $ip_address;
		}

		if (strstr($ip_address, ',')) 
		{
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}

		if (!Hubzero_Environment::validIp($ip_address)) 
		{
			$ip_address = '0.0.0.0';
		}

		return $ip_address;
	}
}
