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
 * Records database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Record extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order dir for fetch
	 *
	 * @var string
	 **/
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'time'    => 'positive|nonzero',
		'task_id' => 'notempty'
	);

	/**
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 * @author 
	 **/
	public function task()
	{
		return $this->belongsToOne('Task');
	}

	/**
	 * Defines a belongs to one relationship between record and hub user
	 *
	 * @return \Hubzero\Database\Relationship\BelongsToOne
	 * @since  1.3.2
	 **/
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User');
	}

	/**
	 * Defines a one to many relationship between record and proxies
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 * @since  1.3.2
	 **/
	public function proxies()
	{
		return $this->oneToMany('Proxy', 'user_id', 'user_id');
	}

	/**
	 * Compares the current user to the model user
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function helperIsMine()
	{
		return $this->isCreator('user_id');
	}

	/**
	 * Checks if the current user is a proxy for the record owner
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function helperICanProxy()
	{
		return in_array(User::get('id'), $this->proxies()->rows()->fieldsByKey('proxy_id'));
	}

	/**
	 * Pulls out hours from time field
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function transformHours()
	{
		if (strpos($this->time, '.') !== false)
		{
			$parts = explode('.', $this->time);
			return $parts[0];
		}
		else
		{
			return $this->time;
		}
	}

	/**
	 * Pulls out minutes from time field
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function transformMinutes()
	{
		if (strpos($this->time, '.') !== false)
		{
			$parts = explode('.', $this->time);
			return $parts[1];
		}
		else
		{
			return 0;
		}
	}
}