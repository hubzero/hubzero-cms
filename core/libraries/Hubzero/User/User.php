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
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\User;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Users database model
 *
 * @uses \Hubzero\Database\Relational
 */
class User extends \Hubzero\Database\Relational
{
	/**
	 * Defines a one to many relationship between users and reset tokens
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 * @since  1.3.2
	 **/
	public function tokens()
	{
		return $this->oneToMany('Token');
	}

	/**
	 * Checks to see if the current user has exceeded the site
	 * password reset request limit for a given time period
	 *
	 * @return bool
	 **/
	public function hasExceededResetLimit()
	{
		$params     = \Component::params('com_users');
		$resetCount = (int)$params->get('reset_count', 10);
		$resetHours = (int)$params->get('reset_time', 1);
		$result     = true;

		// Get the user's tokens
		$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$resetHours} hours ago"));
		$tokens    = $this->tokens()->where('created', '>=', $threshold)->rows();

		if ($tokens->count() < $resetCount)
		{
			$result = false;
		}

		return $result;
	}
}