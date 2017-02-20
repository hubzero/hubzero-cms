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

namespace Components\Time\Models;

use Hubzero\Database\Relational;

/**
 * Tasks database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Task extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order by for fetch
	 *
	 * @var  string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = [
		'name'   => 'notempty',
		'hub_id' => 'notempty'
	];

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setup()
	{
		$this->addRule('end_date', function($data)
		{
			return $data['end_date'] >= $data['start_date'] ? false : 'The task cannot end before it begins';
		});
	}

	/**
	 * Defines a one to many relationship with records
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 **/
	public function records()
	{
		return $this->oneToMany('Record');
	}

	/**
	 * Defines the inverse relationship between a task and a hub
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function hub()
	{
		return $this->belongsToOne('Hub');
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function liaison()
	{
		return $this->belongsToOne('Hubzero\User\User', 'liaison_id');
	}

	/**
	 * Defines a belongs to one relationship between task and assignee
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 * @since   2.0.0
	 **/
	public function assignee()
	{
		return $this->belongsToOne('Hubzero\User\User', 'assignee_id');
	}

	/**
	 * Returns only the active tasks
	 *
	 * @return  \Hubzero\Database\Row version row object
	 * @since   2.0.0
	 **/
	public function helperAreActive()
	{
		return $this->whereEquals('active', 1);
	}

	/**
	 * Display a text value for priority
	 *
	 * @return  string
	 **/
	public function transformPriority()
	{
		switch ($this->get('priority'))
		{
			case 5:
				$priority = 'Critical';
				break;
			case 4:
				$priority = 'Major';
				break;
			case 3:
				$priority = 'Normal';
				break;
			case 2:
				$priority = 'Minor';
				break;
			case 1:
				$priority = 'Trivial';
				break;
			case 0:
			default:
				$priority = 'Unknown';
		}
		return $priority;
	}

	/**
	 * Get total number of hours logged for this task
	 *
	 * @return  float
	 **/
	public function helperTotalHours()
	{
		$time = $this->records()->select('SUM(time)', 'time')->rows()->first()->time;
		$time = $time ?: 0;

		return $time; //number_format($time, 2);
	}
}
