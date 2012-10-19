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
defined('_JEXEC') or die('Restricted access');

class Hubzero_Device
{
	private $_user_agent;
	private $_device_family;
	private $_device_os;
	private $_device_os_version;
	
	public function __construct( $ua = null )
	{
		if(!$ua)
		{
			$ua = JRequest::getVar('HTTP_USER_AGENT', '', 'server');
			$this->_user_agent = $ua;
		}
		
		//unset all method vars
		unset($device_family);
		unset($device_os);
		unset($device_os_version);
		
		//list of Mobile OS's
		$os = array(
			'ios', 'android', 'blackberry os', 'windows', 'symbian os', 'web os'
		);
		
		//default all class vars
		$device_family = null;
		$device_os = null;
		$device_os_version = null;
		
		//if were an iPad
		if(preg_match('/ipad/i', strtolower($ua)))
		{
			$device_family = 'iPad';
			$device_os = 'iOS';
		}
		//if we are an iPhone
		elseif(preg_match('/iphone/i', strtolower($ua)))
		{
			$device_family = 'iPhone';
			$device_os = 'iOS';
		}
		
		//if were on iOS
		if($device_os == 'iOS')
		{
			preg_match('/OS (\d\w\d)/i', strtolower($ua), $matches);
			$v = explode("_", $matches[1]);
			$device_os_version = $v[0].".".$v[1];
		}
		
		
		//set all the class vars
		$this->_device_family 		= $device_family;
		$this->_device_os 			= $device_os;
		$this->_device_os_version 	= $device_os_version;
	}
	
	public function getUserAgent()
	{
		return $this->_user_agent;
	}
	
	public function getDeviceFamily()
	{
		return $this->_device_family;
	}
	
	public function getDeviceOs()
	{
		return $this->_device_os;
	}
	
	public function getDeviceOsVersion()
	{
		return $this->_device_os_version;
	}
}