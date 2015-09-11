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
 * @since     Class available since release 1.3.2
 */

namespace Components\Time\Models;

use Hubzero\Database\Relational;

/**
 * Time liaisons database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Liaison extends Relational
{
	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return void
	 * @since  1.3.2
	 **/
	public function setup()
	{
		$this->forwardTo('user');
	}

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Defines a belongs to one relationship between time proxies and hub user
	 *
	 * @return \Hubzero\Database\Relationship\BelongsToOne
	 * @since  1.3.2
	 **/
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User');
	}
}