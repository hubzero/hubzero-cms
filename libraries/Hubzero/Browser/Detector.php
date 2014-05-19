<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Browser;

use Hubzero\Base\Object;

/**
 * Browser class, provides capability information about the current web client.
 *
 * Browser identification is performed by examining the HTTP_USER_AGENT
 * environment variable provided by the web server.
 *
 * This class has many influences from the lib/Browser.php code in
 * version 3 of Horde by Chuck Hagenbuch and Jon Parise.
 */
class Detector extends Object
{
	/**
	 * @var    integer  Major version number
	 */
	protected $majorVersion = 0;

	/**
	 * @var    integer  Minor version number
	 */
	protected $minorVersion = 0;

	/**
	 * @var    string  Browser name.
	 */
	protected $browser = '';

	/**
	 * @var    string  Full user agent string.
	 */
	protected $agent = '';

	/**
	 * @var    string  Lower-case user agent string
	 */
	protected $lowerAgent = '';

	/**
	 * @var    string  HTTP_ACCEPT string.
	 */
	protected $accept = '';

	/**
	 * @var    array  Parsed HTTP_ACCEPT string
	 */
	protected $acceptParsed = array();

	/**
	 * @var    string  Platform the browser is running on
	 */
	protected $platform = '';

	/**
	 * @var    string  Platform version the browser is running on
	 */
	protected $platformVersion = '';

	/**
	 * @var    string  Device the browser is running on
	 */
	protected $device = '';

	/**
	 * @var    array  Known robots.
	 */
	protected $robots = array(
		/* The most common ones. */
		'Googlebot',
		'msnbot',
		'Slurp',
		'Yahoo',
		/* The rest alphabetically. */
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'Baiduspider',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
		'FAST-WebCrawler',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www.almaden.ibm.com/cs/crawler',
		'ZyBorg'
	);

	/**
	 * @var    boolean  Is this a mobile browser?
	 * @since  12.1
	 */
	protected $mobile = false;

	/**
	 * List of viewable image MIME subtypes.
	 * This list of viewable images works for IE and Netscape/Mozilla.
	 *
	 * @var    array
	 */
	protected $images = array(
		'jpeg', 
		'gif', 
		'png', 
		'pjpeg', 
		'x-png', 
		'bmp'
	);

	/**
	 * @var    array  Browser instances container.
	 */
	protected static $instances = array();

	/**
	 * Create a browser instance (constructor).
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 *
	 * @since   11.1
	 */
	public function __construct($userAgent = null, $accept = null)
	{
		$this->match($userAgent, $accept);
	}

	/**
	 * Returns the global Browser object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 * @return  object  The Browser object.
	 */
	public static function getInstance($userAgent = null, $accept = null)
	{
		$signature = serialize(array($userAgent, $accept));

		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new self($userAgent, $accept);
		}

		return self::$instances[$signature];
	}

	/**
	 * Parses the user agent string and inititializes the object with
	 * all the known features and quirks for the given browser.
	 *
	 * @param   string  $userAgent  The browser string to parse.
	 * @param   string  $accept     The HTTP_ACCEPT settings to use.
	 * @return  void
	 */
	public function match($userAgent = null, $accept = null)
	{
		// Set our agent string.
		if (is_null($userAgent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		}
		else
		{
			$this->agent = $userAgent;
		}

		$this->lowerAgent = strtolower($this->agent);

		// Set our accept string.
		if (is_null($accept))
		{
			if (isset($_SERVER['HTTP_ACCEPT']))
			{
				$this->accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
			}
		}
		else
		{
			$this->accept = strtolower($accept);
		}

		if (!empty($this->agent))
		{
			$this->_setPlatform();

			// Determine browser and version
			// Note: chrome must be before safari
			/*$browsers = array(
				'firefox', 'msie', 'opera', 'chrome', 'icab', 'safari',
				'mozilla', 'seamonkey', 'konqueror', 'netscape',
				'gecko', 'navigator', 'mosaic', 'lynx', 'amaya',
				'omniweb', 'avant', 'camino', 'flock', 'aol'
			);

			foreach ($browsers as $b)
			{
				if (preg_match("#($b)[/ ]?([0-9.]*)#", $this->lowerAgent, $match)) 
				{
					$this->setBrowser($match[1]);

					$this->majorVersion = strstr($match[2], '.', true);
					$this->minorVersion = substr($match[2], strlen($this->majorVersion . '.'));
					if (preg_match("#(version)[/ ]?([0-9.]*)#", $this->lowerAagent, $match)) 
					{
						$this->majorVersion = strstr($match[2], '.', true);
						$this->minorVersion = substr($match[2], strlen($this->majorVersion . '.'));
					}
					break;
				}
			}*/

			if (strpos($this->lowerAgent, 'mobileexplorer') !== false
				|| strpos($this->lowerAgent, 'openwave') !== false
				|| strpos($this->lowerAgent, 'opera mini') !== false
				|| strpos($this->lowerAgent, 'opera mobi') !== false
				|| strpos($this->lowerAgent, 'operamini') !== false)
			{
				$this->mobile = true;
			}
			elseif (preg_match('|Opera[/ ]([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('opera');
				list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);

				// Due to changes in Opera UA, we need to check Version/xx.yy,
				// but only if version is > 9.80. See: http://dev.opera.com/articles/view/opera-ua-string-changes/ */
				if ($this->majorVersion == 9 && $this->minorVersion >= 80)
				{
					$this->identifyBrowserVersion();
				}
			}
			elseif (preg_match('|Chrome[/ ]([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('chrome');
				//list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);

				// [!] Changed to handle UAS that do not include the minor version (StatusCake)
				$bits = explode('.', $version[1]);
				$this->majorVersion = $bits[0];
				$this->minorVersion = (isset($bits[1]) ? $bits[1] : 0);
			}
			elseif (preg_match('|CrMo[/ ]([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('chrome');
				//list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);

				// [!] Changed to handle UAS that do not include the minor version (StatusCake)
				$bits = explode('.', $version[1]);
				$this->majorVersion = $bits[0];
				$this->minorVersion = (isset($bits[1]) ? $bits[1] : 0);
			}
			elseif (preg_match('|CriOS[/ ]([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('chrome');
				//list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);
				// [!] Changed to handle UAS that do not include the minor version (StatusCake)
				$bits = explode('.', $version[1]);
				$this->majorVersion = $bits[0];
				$this->minorVersion = (isset($bits[1]) ? $bits[1] : 0);

				$this->mobile = true;
			}
			elseif (strpos($this->lowerAgent, 'elaine/') !== false
				|| strpos($this->lowerAgent, 'palmsource') !== false
				|| strpos($this->lowerAgent, 'digital paths') !== false)
			{
				$this->setBrowser('palm');
				$this->mobile = true;
			}
			elseif ((preg_match('|MSIE ([0-9.]+)|', $this->agent, $version)) || (preg_match('|Internet Explorer/([0-9.]+)|', $this->agent, $version)))
			{
				$this->setBrowser('msie');

				if (strpos($version[1], '.') !== false)
				{
					list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);
				}
				else
				{
					$this->majorVersion = $version[1];
					$this->minorVersion = 0;
				}

				// Some Handhelds have their screen resolution in the
				// user agent string, which we can use to look for
				// mobile agents.
				if (preg_match('/; (120x160|240x280|240x320|320x320)\)/', $this->agent))
				{
					$this->mobile = true;
				}
			}
			elseif (preg_match('|amaya/([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('amaya');
				$this->majorVersion = $version[1];

				if (isset($version[2]))
				{
					$this->minorVersion = $version[2];
				}
			}
			elseif (preg_match('|ANTFresco/([0-9]+)|', $this->agent, $version))
			{
				$this->setBrowser('fresco');
			}
			elseif (strpos($this->lowerAgent, 'avantgo') !== false)
			{
				$this->setBrowser('avantgo');
				$this->mobile = true;
			}
			elseif (preg_match('|Konqueror/([0-9]+)|', $this->agent, $version) || preg_match('|Safari/([0-9]+)\.?([0-9]+)?|', $this->agent, $version))
			{
				// Konqueror and Apple's Safari both use the KHTML
				// rendering engine.
				$this->setBrowser('konqueror');
				$this->majorVersion = $version[1];

				if (isset($version[2]))
				{
					$this->minorVersion = $version[2];
				}

				if (strpos($this->agent, 'Safari') !== false && $this->majorVersion >= 60)
				{
					// Safari.
					$this->setBrowser('safari');
					$this->identifyBrowserVersion();
				}
			}
			elseif (preg_match('|Mozilla/([0-9.]+)|', $this->agent, $version))
			{
				$this->setBrowser('mozilla');

				list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);
			}
			elseif (preg_match('|Lynx/([0-9]+)|', $this->agent, $version))
			{
				$this->setBrowser('lynx');
			}
			elseif (preg_match('|Links \(([0-9]+)|', $this->agent, $version))
			{
				$this->setBrowser('links');
			}
			elseif (preg_match('|HotJava/([0-9]+)|', $this->agent, $version))
			{
				$this->setBrowser('hotjava');
			}
			elseif (strpos($this->agent, 'UP/') !== false || strpos($this->agent, 'UP.B') !== false || strpos($this->agent, 'UP.L') !== false)
			{
				$this->setBrowser('up');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'Xiino/') !== false)
			{
				$this->setBrowser('xiino');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'Palmscape/') !== false)
			{
				$this->setBrowser('palmscape');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'Nokia') !== false)
			{
				$this->setBrowser('nokia');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'Ericsson') !== false)
			{
				$this->setBrowser('ericsson');
				$this->mobile = true;
			}
			elseif (strpos($this->lowerAgent, 'wap') !== false)
			{
				$this->setBrowser('wap');
				$this->mobile = true;
			}
			elseif (strpos($this->lowerAgent, 'docomo') !== false || strpos($this->lowerAgent, 'portalmmm') !== false)
			{
				$this->setBrowser('imode');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'BlackBerry') !== false)
			{
				$this->setBrowser('blackberry');
				$this->mobile = true;
			}
			elseif (strpos($this->agent, 'MOT-') !== false)
			{
				$this->setBrowser('motorola');
				$this->mobile = true;
			}
			elseif (strpos($this->lowerAgent, 'j-') !== false)
			{
				$this->setBrowser('mml');
				$this->mobile = true;
			}
		}
	}

	/**
	 * Match the platform of the browser.
	 *
	 * This is a pretty simplistic implementation, but it's intended
	 * to let us tell what line breaks to send, so it's good enough
	 * for its purpose.
	 *
	 * @return  void
	 */
	protected function _setPlatform()
	{
		// if we're on an iPad or iPhone
		/*if (preg_match('/ipad|iphone/i', $this->lowerAgent))
		{
			$this->platform = 'ios';
		}
		elseif (strpos($this->lowerAgent, 'wind') !== false)
		{
			$this->platform = 'win';
		}
		elseif (strpos($this->lowerAgent, 'mac') !== false)
		{
			$this->platform = 'mac';
		}
		else
		{
			$this->platform = 'unix';
		}*/
		$this->device = 'computer';
		
		// Determine platform
		//
		// packs the os array
		// use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
		// which would make the nt test register true
		$a_mobile = array(
			'ios', 'android', 'blackberry os', 'windows', 'symbian os', 'web os'
		);

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
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os = array(
			'beos', 'os2', 'amiga', 'webtv', 'iphone', 'ipad', 'mac', 'nt', 'win', 
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
			if (!is_array($s_os) && stristr($this->lowerAgent, $s_os) && !stristr($this->lowerAgent, 'linux'))
			{
				$this->platform = $s_os;

				switch ($this->platform)
				{
					case 'ipad':
					case 'iphone':
						$this->platform = 'iOS';
					break;

					case 'win':
						$this->platform = 'Windows';
						if (stristr($this->lowerAgent, '95')) 
						{
							$this->platformVersion = '95';
						}
						elseif ((stristr($this->lowerAgent, '9x 4.9')) || (stristr($this->lowerAgent, 'me')))
						{
							$this->platformVersion = 'me';
						}
						elseif (stristr($this->lowerAgent, '98'))
						{
							$this->platformVersion = '98';
						}
						elseif (stristr($this->lowerAgent, '2000')) // windows 2000, for opera ID
						{
							$this->platformVersion = 5.0;
							$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, 'xp')) // windows 2000, for opera ID
						{
							$this->platformVersion = 5.1;
							$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, '2003')) // windows server 2003, for opera ID
						{
							$this->platformVersion = 5.2;
							$this->platform .= ' NT';
						}
						elseif (stristr($this->lowerAgent, 'ce')) // windows CE
						{
							$this->platformVersion = 'ce';
						}
					break;

					case 'nt':
						$this->platform = 'Windows NT';
						if (stristr($this->lowerAgent, 'nt 5.2')) // windows server 2003
						{
							$this->platformVersion = 5.2;
						}
						elseif (stristr($this->lowerAgent, 'nt 5.1') || stristr($this->lowerAgent, 'xp')) // windows xp
						{
							//$this->platformVersion = 5.1;
							$this->platformVersion = 'XP';
							$this->platform = 'Windows';
						}
						elseif (stristr($this->lowerAgent, 'nt 5') || stristr($this->lowerAgent, '2000')) // windows 2000
						{
							//$this->platformVersion = 5.0;
							$this->platformVersion = '2000';
							$this->platform = 'Windows';
						}
						elseif (stristr($this->lowerAgent, 'nt 4')) // nt 4
						{
							$this->platformVersion = 4;
						}
						elseif (stristr($this->lowerAgent, 'nt 3')) // nt 4
						{
							$this->platformVersion = 3;
						} else {
							$this->platformVersion = '';
						}
					break;

					case 'mac':
						$this->platform = 'Mac OS';
						if (stristr($this->lowerAgent, 'os x'))
						{
							$this->platformVersion = 10;
						}
						// this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3 
						// are only made for os x
						/*elseif (($browser == 'safari') || ($browser == 'camino') || ($browser == 'shiira') ||
							(($browser == 'mozilla') && ($browser_ver >= 1.3)) ||
							(($browser == 'msie') && ($browser_ver >= 5.2)))
						{
							$this->platformVersion = 10;
						}*/
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
					if (stristr($this->lowerAgent, $s_os[$j]))
					{
						$this->platform = 'Unix'; // if the os is in the unix array, it's unix, obviously...
						$this->platformVersion = ($s_os[$j] != 'unix') ? $s_os[$j] : ''; // assign sub unix version from the unix array
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
					if (stristr($this->lowerAgent, $s_os[$j])) 
					{
						$this->platform = 'Linux';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$this->platformVersion = ($s_os[$j] != 'linux') ? $s_os[$j] : '';
						break;
					}
				}
			}
		}

		// if we're on iOS
		if (in_array(strtolower($this->platform), $a_mobile))
		{
			$this->mobile = true;
			$this->device = 'phone';

			if (preg_match('/iphone/i', strtolower($this->lowerAgent)))
			{
				$this->device = 'iPhone';
			}
			if (preg_match('/ipad/i', strtolower($this->lowerAgent)))
			{
				$this->device = 'iPad';
			}
		}

		if (strtolower($this->platform) == 'ios')
		{
			preg_match('/OS (\d\w\d)/i', $this->lowerAgent, $matches);
			$v = explode('_', $matches[1]);
			$this->platformVersion = $v[0] . '.' . $v[1];
		}

		return $this;
	}

	/**
	 * Return the currently matched device.
	 *
	 * @return  string  The user's device.
	 */
	public function device()
	{
		return $this->device;
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 */
	public function platform()
	{
		return $this->platform;
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 */
	public function platformVersion()
	{
		return $this->platformVersion;
	}

	/**
	 * Set browser version, not by engine version
	 * Fallback to use when no other method identify the engine version
	 *
	 * @return  void
	 */
	protected function identifyBrowserVersion()
	{
		if (preg_match('|Version[/ ]([0-9.]+)|', $this->agent, $version))
		{
			list ($this->majorVersion, $this->minorVersion) = explode('.', $version[1]);

			return;
		}

		// Can't identify browser version
		$this->majorVersion = 0;
		$this->minorVersion = 0;

		\JFactory::getLogger()->notice("Can't identify browser version. Agent: " . $this->agent);
	}

	/**
	 * Sets the current browser.
	 *
	 * @param   string  $browser  The browser to set as current.
	 * @return  void
	 */
	public function setBrowser($browser)
	{
		$this->browser = $browser;
	}

	/**
	 * Retrieve the current browser.
	 *
	 * @return  string  The current browser.
	 */
	public function name()
	{
		return $this->browser;
	}

	/**
	 * Retrieve the current browser's major version.
	 *
	 * @return  integer  The current browser's major version
	 */
	public function major()
	{
		return $this->majorVersion;
	}

	/**
	 * Retrieve the current browser's minor version.
	 *
	 * @return  integer  The current browser's minor version.
	 */
	public function minor()
	{
		return $this->minorVersion;
	}

	/**
	 * Retrieve the current browser's version.
	 *
	 * @return  string  The current browser's version.
	 */
	public function version($for='')
	{
		switch (strtolower($for))
		{
			case 'major':
				return $this->major();
			break;

			case 'minor':
				return $this->minor();
			break;

			case 'platform':
				return $this->platformVersion();
			break;

			default:
				return $this->majorVersion . '.' . $this->minorVersion;
			break;
		}
	}

	/**
	 * Return the full browser agent string.
	 *
	 * @return  string  The browser agent string
	 */
	public function agent()
	{
		return $this->agent;
	}

	/**
	 * Returns the server protocol in use on the current server.
	 *
	 * @return  string  The HTTP server protocol version.
	 */
	public function protocol()
	{
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (($pos = strrpos($_SERVER['SERVER_PROTOCOL'], '/')))
			{
				return substr($_SERVER['SERVER_PROTOCOL'], $pos + 1);
			}
		}

		return null;
	}

	/**
	 * Determines if a browser can display a given MIME type.
	 *
	 * Note that  image/jpeg and image/pjpeg *appear* to be the same
	 * entity, but Mozilla doesn't seem to want to accept the latter.
	 * For our purposes, we will treat them the same.
	 *
	 * @param   string  $mimetype  The MIME type to check.
	 * @return  boolean  True if the browser can display the MIME type.
	 */
	public function isViewable($mimetype)
	{
		$mimetype = strtolower($mimetype);
		list ($type, $subtype) = explode('/', $mimetype);

		if (!empty($this->accept))
		{
			$wildcard_match = false;

			if (strpos($this->accept, $mimetype) !== false)
			{
				return true;
			}

			if (strpos($this->accept, '*/*') !== false)
			{
				$wildcard_match = true;

				if ($type != 'image')
				{
					return true;
				}
			}

			// Deal with Mozilla pjpeg/jpeg issue
			if ($this->isBrowser('mozilla') && ($mimetype == 'image/pjpeg') && (strpos($this->accept, 'image/jpeg') !== false))
			{
				return true;
			}

			if (!$wildcard_match)
			{
				return false;
			}
		}

		if (!$this->hasFeature('images') || ($type != 'image'))
		{
			return false;
		}

		return (in_array($subtype, $this->images));
	}

	/**
	 * Determine if the given browser is the same as the current.
	 *
	 * @param   string  $browser  The browser to check.
	 * @return  boolean  Is the given browser the same as the current?
	 */
	public function isBrowser($browser)
	{
		return ($this->browser === $browser);
	}

	/**
	 * Determines if the browser is a robot or not.
	 *
	 * @return  boolean  True if browser is a known robot.
	 */
	public function isRobot()
	{
		foreach ($this->robots as $robot)
		{
			if (strpos($this->agent, $robot) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if the browser is mobile version or not.
	 *
	 * @return boolean  True if browser is a known mobile version.
	 */
	public function isMobile()
	{
		return $this->mobile;
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 */
	public function isSecure()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}
}
