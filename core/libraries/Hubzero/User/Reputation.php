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
 */
namespace Hubzero\User;

use Hubzero\Database\Relational;
use Session;

/**
 * Reputation database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Reputation extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var string
	 **/
	protected $table = '#__user_reputation';

	/**
	 * Increments user spam count, both globally and in current session
	 *
	 * @return bool
	 **/
	public function incrementSpamCount()
	{
		// Save global spam count
		$current = $this->get('spam_count', 0);
		$this->set('spam_count', ($current+1));
		$this->set('user_id', \User::get('id'));
		$this->save();

		// Also increment session spam count
		$current = Session::get('spam_count', 0);
		Session::set('spam_count', ($current+1));
	}

	/**
	 * Checks to see if user is jailed
	 *
	 * @return bool
	 **/
	public function isJailed()
	{
		if ($this->get('user_id', false))
		{
			$params        = Plugin::params('system', 'spamjail');
			$sessionCount  = $params->get('session_count', 5);
			$lifetimeCount = $params->get('user_count', 10);
			if (Session::get('spam_count', 0) > $sessionCount || $this->get('spam_count', 0) > $lifetimeCount)
			{
				return true;
			}
		}

		return false;
	}
}