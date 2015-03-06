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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Facades;

/**
 * Router facade
 */
class Route extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'router';
	}

	/**
	 * Translates an internal URL to a humanly readible URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             0: Leave URI in the same secure state as it was passed to the function.
	 *                            -1: Make URI unsecure using the global unsecure site URI.
	 * @return  The translated humanly readible URL.
	 */
	public static function url($url, $xhtml = true, $ssl = null)
	{
		return \JRoute::_($url, $xhtml, $ssl);
	}
}