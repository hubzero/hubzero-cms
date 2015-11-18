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

namespace Components\Cron\Models;

//use Components\Cron\Models\Expression;
use Hubzero\Database\Relational;
use Hubzero\User\Profile;
use Hubzero\Debug\Profiler;
use Hubzero\Config\Registry;
use Lang;
use Date;

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'Cron' . DS . 'CronExpression.php');

/**
 * Cron model for a job
 */
class Job extends Relational
{
	/**
	 * Cron expression
	 *
	 * @var  object
	 */
	protected $expression = NULL;

	/**
	 * Profiler
	 *
	 * @var  object
	 */
	protected $profiler = NULL;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'cron';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 */
	protected $rules = array(
		'title'      => 'notempty',
		'recurrence' => 'notempty',
		'event'      => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'event',
		'publish_up',
		'publish_down'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Split event into plugin name and event
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 **/
	public function automaticEvent($data)
	{
		if (strstr($data['event'], '::'))
		{
			$parts = explode('::', $data['event']);
			$this->set('plugin', trim($parts[0]));
			return trim($parts[1]);
		}
		return $data['event'];
	}

	/**
	 * Set publish up value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 **/
	public function automaticPublishUp($data)
	{
		if (!$data['publish_up'])
		{
			$data['publish_up'] = '0000-00-00 00:00:00';
		}
		return $data['publish_up'];
	}

	/**
	 * Set publish down value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!$data['publish_down'])
		{
			$data['publish_down'] = '0000-00-00 00:00:00';
		}
		return $data['publish_down'];
	}

	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('recurrence', function($data)
		{
			$data['recurrence'] = preg_replace('/[\s]{2,}/', ' ', $data['recurrence']);

			if (preg_match('/[^-,*\/ \\d]/', $data['recurrence']) !== 0)
			{
				return Lang::txt('Cron String contains invalid character.');
			}

			$bits = @explode(' ', $data['recurrence']);
			if (count($bits) != 5)
			{
				return Lang::txt('Cron string is invalid. Too many or too little sections.');
			}

			return false;
		});

		$this->set('params', new Registry($this->get('params')));

		$this->profiler = new Profiler('cron_job_' . $this->get('id'));
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 */
	public function save()
	{
		$params = $this->get('params');
		if (is_object($params))
		{
			$this->set('params', $params->toString());
		}

		$result = parent::save();

		$this->set('params', $params);

		return $result;
	}

	/**
	 * Defines a belongs to one relationship
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('date'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'relative':
				return Date::of($this->get('date'))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get('date'))->toLocal($as);
				}
				return $this->get('date');
			break;
		}
	}

	/**
	 * Get a cron expression
	 *
	 * @return  object
	 */
	public function expression()
	{
		if (!($this->expression instanceof \Cron\CronExpression))
		{
			$this->expression = \Cron\CronExpression::factory($this->get('recurrence'));
		}
		return $this->expression;
	}

	/**
	 * Is the entry published?
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		return ($this->get('state') == self::STATE_PUBLISHED);
	}

	/**
	 * Check if the job is available
	 *
	 * @return  boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->get('id') || !$this->isPublished())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended())
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the job started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		if (!$this->get('id') || !$this->isPublished())
		{
			return false;
		}

		$now = Date::of('now')->toLocal('Y-m-d H:i:s');

		if ($this->get('publish_up')
		 && $this->get('publish_up') != '0000-00-00 00:00:00'
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the job ended?
	 *
	 * @return  boolean
	 */
	public function ended()
	{
		if (!$this->get('id') || !$this->isPublished())
		{
			return true;
		}

		$now = Date::of('now')->toLocal('Y-m-d H:i:s');

		if ($this->get('publish_down')
		 && $this->get('publish_down') != '0000-00-00 00:00:00'
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the last run timestamp
	 *
	 * @return  void
	 */
	public function lastRun($format = 'Y-m-d H:i:s')
	{
		return $this->expression()->getPreviousRunDate()->format($format);
	}

	/**
	 * Get the next run timestamp
	 *
	 * @return  void
	 */
	public function nextRun($format = 'Y-m-d H:i:s')
	{
		return $this->expression()->getNextRunDate()->format($format);
	}

	/**
	 * Mark a time
	 *
	 * @param   string   $label
	 * @return  boolean
	 */
	public function mark($label)
	{
		return $this->profiler->mark($label);
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function profile()
	{
		return $this->profiler->marks();
	}

	/**
	 * Return data about this job, icluding profile info as an array
	 *
	 * @param   boolean  $vebose
	 * @return  array
	 */
	public function toArray($verbose = false)
	{
		$buffer = $this->profile();

		$start = $buffer[0];
		$end   = end($buffer);

		return array(
			'id'         => $this->get('id'),
			'title'      => $this->get('title'),
			'plugin'     => $this->get('plugin'),
			'event'      => $this->get('event'),
			'last_run'   => $this->get('last_run'),
			'next_run'   => $this->get('next_run'),
			'active'     => $this->get('active'),
			'start_time' => round($start->started(), 3),
			'start_mem'  => round($start->memory(), 3),
			'end_time'   => round($end->ended(), 3),
			'end_mem'    => round($end->memory(), 3),
			'delta_time' => round($end->ended() - $start->started(), 3),
			'delta_mem'  => round($end->memory() - $start->memory(), 3)
		);
	}
}

