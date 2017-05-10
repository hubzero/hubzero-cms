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
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Hub extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'time';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'name'    => 'notempty',
		'liaison' => 'notempty'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 **/
	protected $parsed = array(
		'notes'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		'name_normalized',
		'asset_id'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param  array $data the data being saved
	 * @return int
	 * @since  1.3.2
	 **/
	public function automaticNameNormalized($data)
	{
		return strtolower(str_replace(" ", "", $data['name']));
	}

	/**
	 * Defines a one to many relationship with tasks
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function tasks()
	{
		return $this->oneToMany('Task');
	}

	/**
	 * Defines a one to many through relationship with records by way of tasks
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function records()
	{
		return $this->oneToManyThrough('Record', 'Task');
	}

	/**
	 * Defines a one to many relationship with hub contacts
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function contacts()
	{
		return $this->oneToMany('Contact');
	}

	/**
	 * Defines a one to many relationship with time allotments
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function allotments()
	{
		return $this->oneToMany('Allotment');
	}

	/**
	 * Returns sum of hours for the hub
	 *
	 * @return float
	 * @since  1.3.2
	 **/
	public function helperTotalHours()
	{
		//$time = $this->records()->select('SUM(time)', 'time')->rows()->first()->time;
		$records = Record::all();
		$task = Task::blank();

		$r = $records->getTableName();
		$t = $task->getTableName();

		$time = $records
			->select('SUM(' . $r . '.time)', 'time')
			->join($t, $t . '.id', $r . '.task_id')
			->whereEquals($t . '.hub_id', $this->get('id'))
			->rows()
			->first()
			->time;

		return $time ? $time : 0;
	}
}
