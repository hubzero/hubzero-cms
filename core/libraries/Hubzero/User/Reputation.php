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
	 * @var  string
	 */
	protected $table = '#__user_reputation';

	/**
	 * Increments user spam count, both globally and in current session
	 *
	 * @return  bool
	 */
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
	 * @return  bool
	 */
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