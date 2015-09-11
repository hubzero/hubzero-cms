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

namespace Hubzero\User\Log;

use Hubzero\Database\Relational;

/**
 * User authentication log database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Auth extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__users_log_auth';

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $initiate = [
		'logged',
		'ip'
	];

	/**
	 * Generates automatic owned logged date/time
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 **/
	public function automaticLogged($data)
	{
		return \Date::of()->toSql();
	}

	/**
	 * Generates automatic source ip
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 **/
	public function automaticIp($data)
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}
}