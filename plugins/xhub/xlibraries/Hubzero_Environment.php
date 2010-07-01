<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class Hubzero_Environment 
{
	public function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	//-----------
	
	public function validIp($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	public function ipAddress()
	{
		if (Hubzero_Environment::server('REMOTE_ADDR') AND Hubzero_Environment::server('HTTP_CLIENT_IP')) {
			$ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif (Hubzero_Environment::server('REMOTE_ADDR')) {
			$ip_address = JRequest::getVar('REMOTE_ADDR','','server');
		} elseif (Hubzero_Environment::server('HTTP_CLIENT_IP')) {
			$ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif (Hubzero_Environment::server('HTTP_X_FORWARDED_FOR')) {
			$ip_address = JRequest::getVar('HTTP_X_FORWARDED_FOR','','server');
		}
		
		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!Hubzero_Environment::validIp($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
}