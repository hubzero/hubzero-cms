<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\User;

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
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function tokens()
	{
		return $this->oneToMany('\Components\Members\Models\Token');
	}

	/**
	 * Defines a one to one relationship between a user and their reputation
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 * @since   2.0.0
	 */
	public function reputation()
	{
		return $this->oneToOne('Reputation');
	}

	/**
	 * Defines a relationship with a generic user logging class (not a relational model itself)
	 *
	 * @return  object  \Hubzero\User\Logger
	 * @since   2.0.0
	 */
	public function logger()
	{
		return new Logger($this);
	}

	/**
	 * Checks to see if the current user has exceeded the site
	 * password reset request limit for a given time period
	 *
	 * @return  bool
	 */
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

	/**
	 * Checks to see if the current user has exceeded the site
	 * login attempt limit for a given time period
	 *
	 * @return  bool
	 */
	public function hasExceededLoginLimit()
	{
		$params    = \Component::params('com_users');
		$limit     = (int)$params->get('login_attempts_limit', 10);
		$timeframe = (int)$params->get('login_attempts_timeframe', 1);
		$result    = true;

		// Get the user's tokens
		$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$timeframe} hours ago"));
		$auths     = new \Hubzero\User\Log\Auth;

		$auths->whereEquals('username', $this->username)
		      ->whereEquals('status', 'failure')
		      ->where('logged', '>=', $threshold);

		if ($auths->count() < $limit)
		{
			$result = false;
		}

		return $result;
	}
}