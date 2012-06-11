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

/**
 * Browser/OS detection class.
 *
 * @author		Shawn    Rice <zooley@purdue.edu>
 * @package		HUBzero CMS
 */
class Hubzero_Browser
{
	/**
	 * The raw user agent string provided by the user's browser
	 *
	 * @access private
	 * @var string
	 */
	private $_user_agent = null;

	/**
	 * OS determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_os = null;

	/**
	 * OS version determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_os_version = null;

	/**
	 * Browser determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_browser = null;

	/**
	 * Browser version determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_browser_version = null;

	/**
	 * Browser major version determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_browser_version_major = null;

	/**
	 * Browser minor version determined from user agent string
	 *
	 * @access private
	 * @var string
	 */
	private $_browser_version_minor = null;

	/**
	 * Determines the user's Browser, Browser version, OS, and OS version from 
	 * the browser user agent string.
	 *
	 * @access public
	 * @param	string	$sagent 	User's browser user agent string
	 */
	public function __construct($sagent=null)
	{
		if (!$sagent) 
		{
			$sagent = JRequest::getVar('HTTP_USER_AGENT','','server');
			$this->_user_agent = $sagent;
		}

		unset($os);
		unset($os_version);
		unset($browser);
		unset($browser_ver);
		unset($browser_ver_major);
		unset($browser_ver_minor);

		// Determine browser and version
		// Note: chrome must be before safari
		$browsers = array(
			'firefox', 'msie', 'opera', 'chrome', 'icab', 'safari',
			'mozilla', 'seamonkey', 'konqueror', 'netscape',
			'gecko', 'navigator', 'mosaic', 'lynx', 'amaya',
			'omniweb', 'avant', 'camino', 'flock', 'aol'
		);

		$browser = null;
		$browser_ver = null;
		$browser_ver_major = null;
		$browser_ver_minor = null;

		foreach ($browsers as $b)
		{
			if (preg_match("#($b)[/ ]?([0-9.]*)#", strtolower($sagent), $match)) 
			{
				$browser = $match[1];
				$browser_ver = $match[2];
				$browser_ver_major = strstr($match[2], '.', true);
				$browser_ver_minor = substr($match[2], strlen($browser_ver_major . '.'));
				if (preg_match("#(version)[/ ]?([0-9.]*)#", strtolower($sagent), $match)) 
				{
					$browser_ver_major = strstr($match[2], '.', true);
					$browser_ver_minor = substr($match[2], strlen($browser_ver_major . '.'));
				}
				break;
			}
		}

		// Determine platform
		/*
		packs the os array
		use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
		which would make the nt test register true
		*/
		$a_mac = array(
			'mac68k', 'macppc'
		); // this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix = array(
			'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun',
			'freebsd', 'openbsd', 'bsd' , 'irix5', 'irix6', 'irix', 'hpux9', 
			'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 
			'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 
			'unix'
		);
		// only sometimes will you get a linux distro to id itself...
		$a_linux = array(
			'kanotix', 'ubuntu', 'mepis', 'debian', 'suse', 'redhat', 'slackware', 
			'mandrake', 'gentoo', 'linux'
		);
		$a_linux_process = array(
			'i386', 'i586', 'i686'
		); // not use currently
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os = array(
			'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', 
			$a_unix, 
			$a_linux
		);

		//os tester
		for ($i = 0; $i < count($a_os); $i++)
		{
			//unpacks os array, assigns to variable
			$s_os = $a_os[$i];

			//assign os to global os variable, os flag true on success
			//!stristr($browser_string, "linux") corrects a linux detection bug
			if (!is_array($s_os) && stristr($sagent, $s_os) && !stristr($sagent, "linux"))
			{
				$os = $s_os;

				switch ($os)
				{
					case 'win':
						$os = 'Windows';
						if (stristr($sagent, '95')) {
							$os_version = '95';
						}
						elseif ((stristr($sagent, '9x 4.9')) || (stristr($sagent, 'me')))
						{
							$os_version = 'me';
						}
						elseif (stristr($sagent, '98'))
						{
							$os_version = '98';
						}
						elseif (stristr($sagent, '2000')) // windows 2000, for opera ID
						{
							$os_version = 5.0;
							$os .= ' NT';
						}
						elseif (stristr($sagent, 'xp')) // windows 2000, for opera ID
						{
							$os_version = 5.1;
							$os .= ' NT';
						}
						elseif (stristr($sagent, '2003')) // windows server 2003, for opera ID
						{
							$os_version = 5.2;
							$os .= ' NT';
						}
						elseif (stristr($sagent, 'ce')) // windows CE
						{
							$os_version = 'ce';
						}
					break;

					case 'nt':
						$os = 'Windows NT';
						if (stristr($sagent, 'nt 5.2')) // windows server 2003
						{
							$os_version = 5.2;
						}
						elseif (stristr($sagent, 'nt 5.1') || stristr($sagent, 'xp')) // windows xp
						{
							//$os_version = 5.1;
							$os_version = 'XP';
							$os = 'Windows';
						}
						elseif (stristr($sagent, 'nt 5') || stristr($sagent, '2000')) // windows 2000
						{
							//$os_version = 5.0;
							$os_version = '2000';
							$os = 'Windows';
						}
						elseif (stristr($sagent, 'nt 4')) // nt 4
						{
							$os_version = 4;
						}
						elseif (stristr($sagent, 'nt 3')) // nt 4
						{
							$os_version = 3;
						} else {
							$os_version = '';
						}
					break;

					case 'mac':
						$os = 'Mac OS';
						if (stristr($sagent, 'os x'))
						{
							$os_version = 10;
						}
						// this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3 
						// are only made for os x
						elseif (($browser == 'safari') || ($browser == 'camino') || ($browser == 'shiira') ||
							(($browser == 'mozilla') && ($browser_ver >= 1.3)) ||
							(($browser == 'msie') && ($browser_ver >= 5.2)))
						{
							$os_version = 10;
						}
					break;

					default:
					break;
				}
				break;
			}
			// check that it's an array, check it's the second to last item 
			// in the main os array, the unix one that is
			elseif (is_array($s_os) && ($i == (count($a_os) - 2)))
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if (stristr($sagent, $s_os[$j]))
					{
						$os = 'Unix'; // if the os is in the unix array, it's unix, obviously...
						$os_version = ($s_os[$j] != 'unix') ? $s_os[$j] : ''; // assign sub unix version from the unix array
						break;
					}
				}
			}
			// check that it's an array, check it's the last item 
			// in the main os array, the linux one that is
			elseif (is_array($s_os) && ($i == (count($a_os) - 1))) 
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if (stristr($sagent, $s_os[$j])) 
					{
						$os = 'Linux';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$os_version = ($s_os[$j] != 'linux') ? $s_os[$j] : '';
						break;
					}
				}
			}
		}

		// pack the os data array for return to main function
		$this->_os = (!empty($os)) ? $os : 'unknown';
		$this->_os_version = (!empty($os_version)) ? $os_version : '';
		$this->_browser = ($browser) ? $browser : 'unknown';
		$this->_browser_version = ($browser_ver) ? $browser_ver : '';
		$this->_browser_version_major = ($browser_ver_major) ? $browser_ver_major : '';
		$this->_browser_version_minor = ($browser_ver_minor) ? $browser_ver_minor : '';
	}

	/**
	 * Return the user's browser
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowser()
	{
		return $this->_browser;
	}

	/**
	 * Return the user's browser version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserVersion()
	{
		return $this->_browser_version;
	}

	/**
	 * Return the user's browser major version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserMajorVersion()
	{
		return $this->_browser_version_major;
	}

	/**
	 * Return the user's browser minor version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserMinorVersion()
	{
		return $this->_browser_version_minor;
	}

	/**
	 * Return the user's OS
	 *
	 * @access public
	 * @return string
	 */
	public function getOs()
	{
		return $this->_os;
	}

	/**
	 * Return the user's OS version
	 *
	 * @access public
	 * @return string
	 */
	public function getOsVersion()
	{
		return $this->_os_version;
	}

	/**
	 * Return the user's browser user agent string
	 *
	 * @access public
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->_user_agent;
	}
}

