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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Projects ToDo model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Todo extends Relational
{
	/**
	 * Completed state
	 *
	 * @var  integer
	 **/
	const STATE_COMPLETED = 1;

	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project';

	/**
	 * The table name
	 *
	 * @var  string
	 **/
	protected $table = '#__project_todo';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'priority';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'projectid' => 'positive|nonzero',
		'content'   => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'content'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'priority'
	);

	/**
	 * Generates automatic content field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticContent($data)
	{
		if (!isset($data['content']))
		{
			$data['content'] = '';
		}
		$data['content'] = rtrim($data['content']);
		$data['content'] = \Hubzero\Utility\Sanitize::stripAll($data['content']);

		if (strlen($data['content']) > 255)
		{
			$data['details'] = $data['content'];
		}
		$data['content'] = \Hubzero\Utility\String::truncate($data['content'], 255);

		return $data['content'];
	}

	/**
	 * Generates automatic priority field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPriority($data)
	{
		if (!isset($data['priority']))
		{
			$last = self::all()
				->select('priority')
				->whereEquals('projectid', (isset($data['projectid']) ? $data['projectid'] : 0))
				->order('priority', 'desc')
				->row();

			$data['priority'] = $last->priority + 1;
		}

		return $data['priority'];
	}

	/**
	 * Get parent project
	 *
	 * @return  object
	 */
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'projectid');
	}

	/**
	 * Get creator
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get user the item is assigned to
	 *
	 * @return  object
	 */
	public function owner()
	{
		return $this->belongsToOne('Hubzero\User\User', 'assigned_to');
	}

	/**
	 * Get user that closed the item
	 *
	 * @return  object
	 */
	public function closer()
	{
		return $this->belongsToOne('Hubzero\User\User', 'closed_by');
	}

	/**
	 * Get comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		$activity = \Hubzero\Activity\Log::all()
			->whereEquals('scope_id', $this->get('id'))
			->whereEquals('scope', 'project.todo')
			->row();

		return \Hubzero\Activity\Log::all()->whereEquals('parent', $activity->get('id')); //$this->oneToMany('Hubzero\Activity\Log', 'scope_id', 'id')->whereEquals('scope', 'project.todo.comment');
	}

	/**
	 * Is the entry due?
	 *
	 * @return  bool
	 */
	public function isOverdue()
	{
		if ($this->get('duedate') && $this->get('duedate') != '0000-00-00 00:00:00')
		{
			if ($this->get('duedate') < Date::toSql())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Is the entry complete?
	 *
	 * @return  bool
	 */
	public function isComplete()
	{
		return ($this->get('state') == self::STATE_COMPLETED);
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function due($as='')
	{
		return $this->_date('duedate', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function closed($as='')
	{
		return $this->_date('closed', $as);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $key  Field to return
	 * @param   string  $as   What data to return
	 * @return  string
	 */
	protected function _date($key, $as='')
	{
		$dt = $this->get($key);

		if (!$dt || $dt == '0000-00-00 00:00:00')
		{
			return '';
		}

		$as = strtolower($as);

		if ($as == 'date')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			$dt = Date::of($dt)->toLocal($as);
		}

		return $dt;
	}

	/**
	 * Get lists
	 *
	 * @param   integer  $projectid
	 * @return  object
	 */
	public static function listsByProject($projectid)
	{
		$lists = self::all()
			->select('DISTINCT(todolist)')
			->select('color')
			->where('todolist', '!=', '')
			->whereEquals('projectid', $projectid)
			->whereRaw('todolist IS NOT NULL')
			->rows();

		return $lists;
	}

	/**
	 * Get entries by project ID
	 *
	 * @param   integer  $projectid
	 * @return  object
	 */
	public static function allByProject($projectid)
	{
		return self::all()->whereEquals('projectid', $projectid);
	}

	/**
	 * Get list name by project ID and color
	 *
	 * @param   integer  $projectid
	 * @param   string   $color
	 * @return  string
	 */
	public static function listByProjectAndColor($projectid, $color)
	{
		$lst = self::all()
			->select('todolist')
			->whereEquals('color', $color)
			->whereEquals('projectid', $projectid)
			->row();

		return $lst->get('todolist');
	}
}
