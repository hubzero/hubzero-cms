<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Base\Object;

/**
 * Logger model
 *
 * This is basically an aggregator class.  It helps us map our relational models
 * and namespaces in a similar fashion.
*/
class Logger extends Object
{
	/**
	 * User model
	 *
	 * @var  object  \Hubzero\User\User
	 */
	private $user = null;

	/**
	 * Constructs a new user logger class
	 *
	 * @return  void
	 * @since   2.0.0
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Defines a one to many relationship between users and auth log entries
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function auth()
	{
		return $this->user->oneToMany('Hubzero\User\Log\Auth');
	}
}