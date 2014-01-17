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
class Hubzero_Browser extends \Hubzero\Browser\Detector
{
	/**
	 * Return the user's browser
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowser()
	{
		return $this->name();
	}

	/**
	 * Return the user's browser version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserVersion()
	{
		return $this->version();
	}

	/**
	 * Return the user's browser major version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserMajorVersion()
	{
		return $this->major();
	}

	/**
	 * Return the user's browser minor version
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserMinorVersion()
	{
		return $this->minor();
	}

	/**
	 * Return the user's OS
	 *
	 * @access public
	 * @return string
	 */
	public function getOs()
	{
		return $this->platform();
	}

	/**
	 * Return the user's OS version
	 *
	 * @access public
	 * @return string
	 */
	public function getOsVersion()
	{
		return $this->platformVersion();
	}

	/**
	 * Return the user's browser user agent string
	 *
	 * @access public
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->agent();
	}
}

